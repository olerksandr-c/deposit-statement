<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BankStatementExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class BankStatement extends Component
{
    use WithFileUploads;

    public $parsedData = []; // Данные таблицы
    public $editedRowIndex = null; // Индекс редактируемой строки
    public $editedRowData = []; // Данные редактируемой строки
    public $dbfExportSuccessMessage = null; // Сообщение об успешном экспорте в DBF

    public $pdfFile;
    public $errorMessage = '';
    public string|null $toastMessage = null;

    // Константы для путей и файлов
    private const PDF_STORAGE_PATH = 'pdfs';
    private const PDF_FILENAME = 'bank-statement.pdf';
    private const SCRIPTS_DIR = 'scripts';
    private const EXPORTS_DIR = 'app/exports';
    private const TABLES_JSON = 'app/tables.json';

    public $title = 'Депозитна виписка'; // Значение по умолчанию



    protected $rules = [
        'pdfFile' => 'required|mimes:pdf|max:2048', // Файл обязателен, только PDF, макс. 2МБ
    ];

    protected $messages = [
        'editedRowData.*.required' => 'Не може бути порожнім.',
        'editedRowData.*.numeric' => 'Значення має бути числом.',
        'editedRowData.*.date' => 'Значення не є датою.',
        // Добавьте другие кастомные сообщения, если нужно
    ];

    public function mount()
    {
        $this->parsedData = []; // Явно встановлюємо порожній масив
        $this->title = 'Депозитна виписка';
    }



    public function render()
    {
        // info('Rendering component with parsedData: ' . json_encode([
        //     'empty' => empty($this->parsedData),
        //     'count' => count($this->parsedData),
        //     'is_array' => is_array($this->parsedData),
        // ]));

        return view('livewire.bank-statement');
    }

    public function updatedPdfFile()
    {
        $this->validateOnly('pdfFile'); // Валидация при выборе файла
    }

    /**
     * Загрузка и обработка PDF файла
     */
    public function uploadPdf()
    {

        $this->validate();

        try {
            // Сохраняем загруженный файл
            $pdfPath = $this->storePdfFile();

            // Извлечение таблиц из PDF
            $this->parsedData = $this->extractTablesFromPdf($pdfPath);
            $this->dispatch('parsed-data-updated');

            // Логируем результат
            // info('Parsed Data: ' . json_encode($this->parsedData));


        } catch (\Exception $e) {
            $this->handleException($e, 'Error processing PDF');
            return;
        }
    }

    /**
     * Сохранение PDF файла
     *
     * @return string Полный путь к сохраненному файлу
     */
    protected function storePdfFile()
    {
        // Сохраняем загруженный файл в папку '/pdfs'
        $relativePath = $this->pdfFile->storeAs(self::PDF_STORAGE_PATH, self::PDF_FILENAME);
        // info('PDF stored at: ' . $relativePath);

        // Полный путь к файлу
        $fullPdfPath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $relativePath);
        // info('Full path: ' . $fullPdfPath);

        return $fullPdfPath;
    }

    /**
     * Запуск Python-скрипта с учетом ОС
     *
     * @param string $scriptPath Путь к Python-скрипту
     * @param array $arguments Массив аргументов для скрипта
     * @param string $description Описание операции для логов
     * @return array Массив с результатом выполнения [output, returnVar]
     * @throws \Exception При ошибке выполнения скрипта
     */
    protected function executePythonScript($scriptPath, $arguments, $description = 'Выполнение Python-скрипта')
    {
        // Определяем путь к Python в зависимости от ОС
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Для Windows
            $pythonPath = 'python';  // Или полный путь, например: 'C:\path\to\venv\Scripts\python.exe'

            // Альтернативный вариант поиска Python на Windows:
            if (!file_exists($pythonPath)) {
                $pythonPath = 'py';  // Пробуем стандартный псевдоним Python в Windows
            }
        } else {
            // Для Linux
            $pythonPath = base_path('venv/bin/python');

            // Проверяем, существует ли venv, если нет - используем системный python3
            if (!file_exists($pythonPath)) {
                $pythonPath = 'python3';
            }
        }

        // Проверка существования скрипта
        if (!file_exists($scriptPath)) {
            throw new \Exception("Python скрипт не найден: {$scriptPath}");
        }

        // Формирование команды
        $command = [escapeshellcmd($pythonPath), escapeshellarg($scriptPath)];

        // Добавление аргументов
        foreach ($arguments as $arg) {
            $command[] = escapeshellarg($arg);
        }

        // Для Windows и Linux разный формат команды
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $commandString = implode(' ', $command);
        } else {
            // Для Linux добавляем перенаправление ошибок
            $commandString = implode(' ', $command) . ' 2>&1';
        }

        // Логирование для отладки
        // info("Python path: {$pythonPath}");
        // info("{$description} - Command: {$commandString}");

        // Выполнение команды
        $output = [];
        $returnVar = 0;
        exec($commandString, $output, $returnVar);

        // Обработка результатов
        if ($returnVar !== 0) {
            $errorMessage = "{$description} - Ошибка выполнения скрипта (код {$returnVar}): " . implode("\n", $output);
            info($errorMessage);
            throw new \Exception($errorMessage);
        }

        // Успешное выполнение
        // info("{$description} - Скрипт выполнен успешно. Результат: " . implode("\n", $output));

        return [$output, $returnVar];
    }

    /**
     * Извлечение таблиц из PDF файла
     *
     * @param string $pdfPath Путь к PDF файлу
     * @return array Массив извлеченных таблиц
     * @throws \Exception При ошибке извлечения
     */
    public function extractTablesFromPdf($pdfPath)
    {
        // Пути к файлам
        $pythonScript = base_path(self::SCRIPTS_DIR . '/extract_tables.py');
        $outputJson = storage_path(self::TABLES_JSON);

        // Проверка существования PDF файла
        if (!file_exists($pdfPath)) {
            throw new \Exception("PDF файл не найден: {$pdfPath}");
        }

        // Вызов Python-скрипта через общий метод
        try {
            $this->executePythonScript(
                $pythonScript,
                [$pdfPath, $outputJson],
                'Извлечение таблиц из PDF'
            );
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при извлечении таблиц: " . $e->getMessage());
        }

        return $this->parseJsonTablesData($outputJson);
    }

    /**
     * Обработка JSON-данных с таблицами
     *
     * @param string $jsonPath Путь к JSON файлу
     * @return array Обработанные данные таблиц
     * @throws \Exception При ошибке обработки JSON
     */
    protected function parseJsonTablesData($jsonPath)
    {
        // Проверка существования JSON-файла
        if (!file_exists($jsonPath)) {
            throw new \Exception("JSON-файл не создан: {$jsonPath}");
        }

        // Чтение JSON с таблицами
        $jsonContent = file_get_contents($jsonPath);
        if (empty($jsonContent)) {
            throw new \Exception("JSON-файл пуст: {$jsonPath}");
        }

        // Декодирование JSON
        $tables = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Ошибка при декодировании JSON: " . json_last_error_msg());
        }

        // Проверяем, является ли $tables массивом массивов
        if (isset($tables[0]) && is_array($tables[0])) {
            // Преобразуем в индексированные массивы
            $cleanedTables = array_map('array_values', $tables);
        } else {
            throw new \Exception("Ошибка: данные не в ожидаемом формате");
        }

        // info('Tables extracted: ' . json_encode($cleanedTables));
        return $cleanedTables;
    }

    /**
     * Экспорт данных в Excel
     */
    public function exportToExcel()
    {
        if ($this->checkEmptyData('экспорта в Excel')) {
            // Сообщение об ошибке уже отправляется из checkEmptyData через handleException/notify
            return null;
        }
        // info('Data for Excel export count: ' . count($this->parsedData));
        // *** ДОБАВИТЬ (опционально, для уведомления о начале) ***
        $this->dispatch('notify', message: 'Починається експорт в Excel...', type: 'info');

        // Уведомление об успехе не сработает тут из-за return download
        return Excel::download(new BankStatementExport(array_map('array_values', $this->parsedData)), 'bank_statement.xlsx');
    }

    /**
     * Начало редактирования строки
     *
     * @param int $index Индекс редактируемой строки
     */
    public function editRow($index)
    {
        $this->editedRowIndex = $index;
        $this->editedRowData = $this->parsedData[$index];
    }

    /**
     * Сохранение изменений в строке
     */
    public function saveRow()
    {


        // --- НАЧАЛО ИЗМЕНЕНИЙ ---

        // Определите правила валидации ЗДЕСЬ, для конкретных индексов editedRowData
        // Замените ИНДЕКС_КОЛОНКИ_СУММЫ_1 и ИНДЕКС_КОЛОНКИ_СУММЫ_2
        // на реальные числовые индексы ваших колонок с суммами (начиная с 0).
        // Например, если суммы в 3-й и 4-й колонках, используйте '2' и '3'.
        $validationRules = [
            'editedRowData.4' => 'required|numeric', //  обязательная, числовая
            'editedRowData.5' => 'required|numeric', // - обязательная, числовая
            'editedRowData.1' => 'required|date',

            // 'editedRowData.0' => 'required|string|max:100', // Пример для текстового поля
        ];

        try {
            // Валидируем ТОЛЬКО данные редактируемой строки
            $this->validate($validationRules);

            // Если валидация прошла успешно, сохраняем данные
            $this->parsedData[$this->editedRowIndex] = $this->editedRowData;
            $this->dispatch('notify', message: 'Рядок успішно збережено.', type: 'success');

            $this->cancelEdit(); // Сбрасываем состояние редактирования

        } catch (ValidationException $e) {
            // Валидация не пройдена. Livewire автоматически обработает ошибки
            // и сделает их доступными в $errors в Blade.
            // Вы можете добавить дополнительное логирование или действия здесь, если нужно.
            info('Validation failed for edited row: ' . json_encode($e->errors()));
            // Не нужно вызывать $this->cancelEdit(), чтобы пользователь мог исправить ошибки
            // и поля ввода остались видимыми.
            // Просто позвольте исключению прервать выполнение метода.
            throw $e; // Перебрасываем исключение, чтобы Livewire его обработал
        }
    }

    /**
     * Отмена редактирования строки
     */
    public function cancelEdit()
    {
        $this->editedRowIndex = null;
        $this->editedRowData = [];
    }

    /**
     * Экспорт данных в DBF формат
     *
     * @return mixed Ответ для скачивания файла или null при ошибке
     */
    public function exportToDbf()
    {
        $this->dbfExportSuccessMessage = null; // Очистка перед началом экспорта
        // info('Начинаем экспорт в DBF');

        if ($this->checkEmptyData('экспорта в DBF')) {
            return null;
        }

        try {
            // Создаем временный JSON файл с данными
            $tempJsonPath = $this->createTempJsonFile();

            // Подготавливаем путь для сохранения DBF файла
            $outputDbfPath = $this->prepareDbfExportPath();

            // Вызываем Python-скрипт для экспорта
            $this->executeDbfExport($tempJsonPath, $outputDbfPath);

            // Удаляем временный JSON файл
            $this->cleanupTempFiles($tempJsonPath);

            // Уведомляем пользователя об успехе
            $this->dispatch('notify', message: 'Експорт в DBF успішно виконано. Файл завантажується...', type: 'success');

            $user = Auth::user();

            Log::create([
                'user_id' => $user?->id,  // ID пользователя
                'log_type' => 'info',  // Тип лога
                'message' => 'Виконано експорт dbf',  // Сообщение
                'is_archived' => false,  // Флаг архивирования
            ]);


            // Отдаем файл для скачивания
            return response()->download($outputDbfPath);
        } catch (\Exception $e) {
            $this->handleException($e, 'DBF export error'); // handleException отправит уведомление
            return null;
        }
    }

    /**
     * Создание временного JSON файла с данными
     *
     * @return string Путь к созданному JSON файлу
     * @throws \Exception При ошибке создания файла
     */
    protected function createTempJsonFile()
    {
        $tempJsonPath = storage_path('app' . DIRECTORY_SEPARATOR . 'temp_dbf_export_' . time() . '.json');
        file_put_contents($tempJsonPath, json_encode($this->parsedData));
        // info('Временный JSON файл создан: ' . $tempJsonPath);

        if (!file_exists($tempJsonPath)) {
            throw new \Exception("Не удалось создать временный JSON файл");
        }

        return $tempJsonPath;
    }

    /**
     * Подготовка пути для экспорта DBF файла
     *
     * @return string Путь для сохранения DBF файла
     * @throws \Exception При ошибке создания директории
     */
    protected function prepareDbfExportPath()
    {
        $outputDbfPath = storage_path(self::EXPORTS_DIR . '/bank_statement_' . now()->format('Y-m-d') . '.dbf');

        // Создаем директорию для экспорта с правильными правами
        $exportDir = dirname($outputDbfPath);
        if (!file_exists($exportDir)) {
            if (!mkdir($exportDir, 0755, true)) {
                throw new \Exception("Не удалось создать директорию: {$exportDir}");
            }
            // Устанавливаем владельца для веб-сервера (если нужно)
            @chown($exportDir, 'www-data');
        }

        return $outputDbfPath;
    }

    /**
     * Выполнение экспорта в DBF через Python-скрипт
     *
     * @param string $jsonPath Путь к JSON файлу с данными
     * @param string $dbfPath Путь для сохранения DBF файла
     * @throws \Exception При ошибке экспорта
     */
    protected function executeDbfExport($jsonPath, $dbfPath)
    {
        $pythonScript = base_path(self::SCRIPTS_DIR . '/export_to_dbf.py');

        try {
            $this->executePythonScript(
                $pythonScript,
                [$jsonPath, $dbfPath],
                'Экспорт в DBF'
            );
        } catch (\Exception $e) {
            throw new \Exception("Ошибка при экспорте в DBF: " . $e->getMessage());
        }

        // Проверяем, что DBF файл создан успешно
        if (!file_exists($dbfPath)) {
            throw new \Exception("DBF файл не был создан");
        }
    }

    /**
     * Очистка временных файлов
     *
     * @param string $tempJsonPath Путь к временному JSON файлу
     */
    protected function cleanupTempFiles($tempJsonPath)
    {
        if (file_exists($tempJsonPath)) {
            unlink($tempJsonPath);
            // info('Временный JSON файл удален: ' . $tempJsonPath);
        }
    }

    // Проверка наличия данных для экспорта

    protected function checkEmptyData($operation): bool
    {
        if (empty($this->parsedData) || !is_array($this->parsedData)) {
            $message = "Нет данных для {$operation}. Завантажте та обробіть PDF файл.";

            $this->dispatch('notify', message: $message, type: 'error');

            info("CheckEmptyData failed for '{$operation}'. Data is empty or not an array.");
            return true;
        }
        return false;
    }

    /**
     * Обработка исключений
     *
     * @param \Exception $exception Объект исключения
     * @param string $logPrefix Префикс для сообщения в логе
     */
    protected function handleException(\Exception $exception, $logPrefix = 'Error')
    {
        $detailedMessage = $exception->getMessage() . " in " . $exception->getFile() . ":" . $exception->getLine();
        info("{$logPrefix}: {$detailedMessage}");

        // Показываем пользователю сообщение через событие
        $userMessage = "Виникла помилка ({$logPrefix}): " . $exception->getMessage();



        $this->dispatch('notify', message: $userMessage, type: 'error');
    }

    public function resetFile()
    {
        $this->reset('pdfFile'); // Сбрасываем только свойство с файлом
        // Или если нужно сбросить все:
        // $this->reset();
    }
}
