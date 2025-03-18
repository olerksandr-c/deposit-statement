<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BankStatementExport;

class BankStatement extends Component
{
    use WithFileUploads;


    public $parsedData = []; // Данные таблицы
    public $editedRowIndex = null; // Индекс редактируемой строки
    public $editedRowData = []; // Данные редактируемой строки
    public $dbfExportSuccessMessage = null; // Сообщение об успешном экспорте в DBF



    public $pdfFile;
    public $errorMessage = '';

    protected $rules = [
        'pdfFile' => 'required|mimes:pdf|max:2048', // Файл обязателен, только PDF, макс. 2МБ
    ];



    public function mount()
    {
        $this->parsedData = []; // Явно встановлюємо порожній масив
    }

    public function render()
    {
        info('Rendering component with parsedData: ' . json_encode([
            'empty' => empty($this->parsedData),
            'count' => count($this->parsedData),
            'is_array' => is_array($this->parsedData),
        ]));

        return view('livewire.bank-statement');
    }

    public function updatedPdfFile()
    {
        $this->validateOnly('pdfFile'); // Валидация при выборе файла
    }

    public function uploadPdf()
    {
        $this->validate();


        // Сохраняем загруженный файл в папку '/pdfs'
        // $pdfPath = $this->pdfFile->store();
        $pdfPath = $this->pdfFile->storeAs('pdfs', 'bank-statement.pdf');

        info('pdfPath ' . $pdfPath);

        // Полный путь к файлу
        $fullPdfPath = storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . $pdfPath);
        info('fullPdfPath ' . $fullPdfPath);

        // Извлечение таблиц с помощью Tabula
        try {
            $this->parsedData = $this->extractTablesFromPdf($fullPdfPath);
            $this->dispatchBrowserEvent('parsed-data-updated'); // Додайте цю подію
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }

        // Логируем результат
        info('Parsed Data: ' . json_encode($this->parsedData));
    }

    public function extractTablesFromPdf($pdfPath)
    {
        // Путь к Python-скрипту
        $pythonScript = base_path('scripts' . DIRECTORY_SEPARATOR . 'extract_tables.py');
        info('Python script path: ' . $pythonScript);

        // Путь для сохранения JSON
        $outputJson = storage_path('app' . DIRECTORY_SEPARATOR . 'tables.json');
        info('Output JSON path: ' . $outputJson);

        // Проверка существования Python-скрипта
        if (!file_exists($pythonScript)) {
            throw new \Exception("Python-скрипт не найден: {$pythonScript}");
        }

        // Проверка существования PDF-файла
        if (!file_exists($pdfPath)) {
            throw new \Exception("PDF-файл не найден: {$pdfPath}");
        }

        // Команда для вызова Python-скрипта
        $command = "python {$pythonScript} {$pdfPath} {$outputJson}";
        info('Command: ' . $command);

        // Выполнение команды
        exec($command, $output, $returnVar);

        // Преобразование вывода в UTF-8 (если необходимо)
        $output = array_map(function ($line) {
            return mb_convert_encoding($line, 'UTF-8', 'UTF-8');
        }, $output);

        info('Command output: ' . implode("\n", $output));
        info('Command return code: ' . $returnVar);

        // Проверка результата выполнения команды
        if ($returnVar !== 0) {
            throw new \Exception("Ошибка при выполнении команды: " . implode("\n", $output));
        }

        // Проверка существования JSON-файла
        if (!file_exists($outputJson)) {
            throw new \Exception("JSON-файл не создан: {$outputJson}");
        }

        // Чтение JSON с таблицами
        $jsonContent = file_get_contents($outputJson);
        if (empty($jsonContent)) {
            throw new \Exception("JSON-файл пуст: {$outputJson}");
        }
        //dd($jsonContent);   
        // Декодирование JSON
        $tables = json_decode($jsonContent, true);

        //dd($tables);
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
        info('Tables extracted: ' . json_encode($cleanedTables));

        // dd($cleanedTables);
        return $cleanedTables;
    }

    public function exportToExcel()
    {
        if (empty($this->parsedData)) {
            session()->flash('error', 'Нет данных для экспорта.');
            return;
        }

        // Логируем данные перед экспортом
        info('Data for export: ' . json_encode($this->parsedData));

        return Excel::download(new BankStatementExport($this->parsedData), 'bank_statement.xlsx');
    }

    // Начало редактирования строки
    public function editRow($index)
    {


        $this->editedRowIndex = $index;
        $this->editedRowData = $this->parsedData[$index];
    }

    // Сохранение изменений
    public function saveRow()
    {
        $this->parsedData[$this->editedRowIndex] = $this->editedRowData;
        $this->cancelEdit();
    }

    // Отмена редактирования
    public function cancelEdit()
    {
        $this->editedRowIndex = null;
        $this->editedRowData = [];
    }

    /**
     * Экспорт данных в DBF формат
     */
    public function exportToDbf()
    {
        $this->dbfExportSuccessMessage = null; // Очистка перед началом экспорта

        if (empty($this->parsedData)) {
            session()->flash('error', 'Нет данных для экспорта в DBF.');
            return;
        }
        //dd($this->parsedData);
        try {
            // Генерируем временный JSON файл с данными
            $tempJsonPath = storage_path('app' . DIRECTORY_SEPARATOR . 'temp_dbf_export_' . time() . '.json');
            file_put_contents($tempJsonPath, json_encode($this->parsedData));

            // Проверяем, что JSON-файл создан успешно
            if (!file_exists($tempJsonPath)) {
                throw new \Exception("Не удалось создать временный JSON файл");
            }

            // Путь для сохранения DBF файла
            // $outputDbfPath = storage_path('app' . DIRECTORY_SEPARATOR . 'exports' . DIRECTORY_SEPARATOR . 'bank_statement_' . time() . '.dbf');
            $outputDbfPath = storage_path('app/exports/bank_statement_' . now()->format('Y-m-d') . '.dbf');

            // Создаем директорию для экспорта, если она не существует
            if (!file_exists(dirname($outputDbfPath))) {
                mkdir(dirname($outputDbfPath), 0755, true);
            }

            // Путь к Python-скрипту для экспорта в DBF
            $pythonScript = base_path('scripts' . DIRECTORY_SEPARATOR . 'export_to_dbf.py');

            // Проверка существования Python-скрипта
            if (!file_exists($pythonScript)) {
                throw new \Exception("Python-скрипт не найден: {$pythonScript}");
            }

            // Команда для вызова Python-скрипта
            $command = "python {$pythonScript} {$tempJsonPath} {$outputDbfPath}";
            info('Export to DBF command: ' . $command);

            // Выполнение команды
            exec($command, $output, $returnVar);

            // Логируем вывод команды
            info('Export to DBF output: ' . implode("\n", $output));
            info('Export to DBF return code: ' . $returnVar);

            // Проверка результата выполнения команды
            if ($returnVar !== 0) {
                throw new \Exception("Ошибка при экспорте в DBF: " . implode("\n", $output));
            }

            // Проверяем, что DBF файл создан успешно
            if (!file_exists($outputDbfPath)) {
                throw new \Exception("DBF файл не был создан");
            }


            // Удаляем временный JSON файл
            unlink($tempJsonPath);
            //$this->dbfExportSuccessMessage = "Експорт в DBF успішно виконано.";
            //$this->dispatch('dbf-export-success', ['message' => "Експорт в DBF успішно виконано."]);

            session()->flash('success', "Експорт в DBF успішно виконано.");
            //$this->js("alert('Post saved!')"); 

            // После этого отдаем файл для скачивания
            return response()->download($outputDbfPath);
        } catch (\Exception $e) {
            session()->flash('error', 'Ошибка при экспорте в DBF: ' . $e->getMessage());
            info('DBF export error: ' . $e->getMessage());

            // Используем dispatch вместо dispatchBrowserEvent (для Livewire 3)
            $this->dispatch('dbf-export-failed', [
                'error' => $e->getMessage()
            ]);

            return;
        }
    }
}
