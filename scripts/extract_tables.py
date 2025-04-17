import tabula
import pandas as pd
import re
import numpy as np
import json
import sys
import os
import io
import datetime

#только для локальной разаработки
os.environ['JAVA_HOME'] = r'C:\Program Files\Java\jdk-23'

# Устанавливаем кодировку вывода в UTF-8
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Аргументы: путь к PDF и путь для сохранения JSON
pdf_path = sys.argv[1]
output_path = sys.argv[2]

def extract_table_from_pdf(pdf_path):
    """
    Извлекает таблицу из PDF-документа банковской выписки.
    """
    try:
        print(f"Извлечение таблицы из {pdf_path}...", file=sys.stdout)

        # Используем lattice=True для распознавания линий таблицы
        tables = tabula.read_pdf(
            pdf_path,
            pages='all',
            lattice=True,
            multiple_tables=True,
            guess=True,
            pandas_options={'header': None},
            silent=True
        )

        # Если таблицы не найдены, пробуем другие параметры
        if not tables or all(table.empty for table in tables):
            tables = tabula.read_pdf(
                pdf_path,
                pages='all',
                area=(150, 30, 550, 750),
                lattice=True,
                guess=True,
                multiple_tables=True,
                pandas_options={'header': None}
            )

        # Если все ещё нет данных, пробуем с stream=True
        if not tables or all(table.empty for table in tables):
            tables = tabula.read_pdf(
                pdf_path,
                pages='all',
                stream=True,
                guess=True,
                multiple_tables=True,
                pandas_options={'header': None}
            )

        # Обработка и объединение таблиц
        if tables and any(not table.empty for table in tables):
            # Находим таблицу с наибольшим количеством строк
            main_table = max(tables, key=lambda t: len(t) if not t.empty else 0)

            if not main_table.empty:
                # Определяем ожидаемые названия столбцов
                expected_columns = ['N п/п', 'Дата операцiї', '% ставка', 'Операцiя', 'Сума', 'Сума в грн.', 'Призначення платежу']

                # Проверяем, есть ли строка с заголовками
                header_row = -1
                for i in range(min(5, len(main_table))):
                    row_str = ' '.join([str(x) for x in main_table.iloc[i].tolist() if pd.notna(x)])
                    if 'N п/п' in row_str or 'Дата операцiї' in row_str or 'Призначення платежу' in row_str:
                        header_row = i
                        break

                # Если нашли строку с заголовками, используем ее
                if header_row >= 0:
                    headers = []
                    for col in main_table.iloc[header_row]:
                        if pd.isna(col):
                            headers.append('')
                        else:
                            headers.append(str(col).strip())

                    data_table = main_table.iloc[header_row+1:].reset_index(drop=True)

                    # Корректируем количество заголовков
                    if len(headers) < len(data_table.columns):
                        headers.extend([''] * (len(data_table.columns) - len(headers)))
                    elif len(headers) > len(data_table.columns):
                        headers = headers[:len(data_table.columns)]

                    data_table.columns = headers
                else:
                    # Если заголовки не найдены, используем стандартные
                    data_table = main_table
                    column_names = expected_columns[:len(data_table.columns)]

                    # Если столбцов больше, чем ожидалось, добавляем дополнительные
                    if len(data_table.columns) > len(expected_columns):
                        extra_cols = len(data_table.columns) - len(expected_columns)
                        column_names.extend([f'Extra_{i+1}' for i in range(extra_cols)])

                    data_table.columns = column_names

                # Очистка данных
                data_table = data_table.replace(np.nan, '', regex=True)

                # Ищем строки с реальными данными операций
                data_rows = []
                for idx, row in data_table.iterrows():
                    row_values = [str(x).strip() for x in row.tolist()]

                    # Проверяем, есть ли дата или сумма в строке
                    has_date = any(re.search(r'\d{2}\.\d{2}\.\d{4}', val) for val in row_values)
                    has_numbers = any(re.search(r'[\d\s]+[,\.]\d{2}', val) for val in row_values)

                    if has_date or has_numbers:
                        cleaned_row = [clean_text(val) for val in row_values]
                        data_rows.append(cleaned_row)

                # Создаем итоговый DataFrame
                if data_rows:
                    result_df = pd.DataFrame(data_rows)

                    # Восстанавливаем названия столбцов
                    if len(result_df.columns) <= len(data_table.columns):
                        result_df.columns = data_table.columns[:len(result_df.columns)]

                    # Удаляем пустые строки
                    result_df = result_df[result_df.astype(str).apply(lambda x: x.str.strip().str.len() > 0).any(axis=1)]

                    return result_df

        # Если таблица не найдена, возвращаем пустой DataFrame
        return pd.DataFrame(columns=['N п/п', 'Дата операцiї', '% ставка', 'Операцiя', 'Сума', 'Сума в грн.', 'Призначення платежу'])

    except Exception as e:
        print(f"Ошибка при извлечении данных: {str(e)}", file=sys.stderr)
        return pd.DataFrame()

def clean_text(text):
    """Очищает текст от лишних символов"""
    if not isinstance(text, str):
        return text

    # Удаление лишних пробелов и переносов строк
    text = text.strip()
    text = re.sub(r'\s+', ' ', text)

    # Корректировка символа №
    text = text.replace("No", "№")
    text = re.sub(r'N(\s*[ОВ\/\d])', r'№\1', text)

    return text

def convert_to_number(value):
    """Преобразует строковое представление суммы в число"""
    if not isinstance(value, str):
        return value

    # Удаляем все пробелы
    value = value.replace(' ', '')

    # Заменяем запятую на точку для десятичного разделителя
    value = value.replace(',', '.')
    value = value.replace('–', '-')

    # Проверяем, является ли строка числом
    try:
        # Если строка содержит минус, это отрицательное число
        if '-' in value:
            value = value.replace('–', '-').replace('—', '-')  # Разные типы тире
            num_value = float(value)
        else:
            num_value = float(value)
        return num_value
    except ValueError:
        return value

def extract_statement_datetime(pdf_path):
    """
    Извлекает дату и время формирования выписки напрямую из PDF-файла.

    Args:
        pdf_path (str): Путь к PDF-файлу

    Returns:
        str: Полная строка даты и времени формирования выписки
    """
    try:
        # Читаем PDF как текст с помощью PyPDF2
        import PyPDF2

        with open(pdf_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            full_text = ""

            # Извлекаем текст со всех страниц
            for page in pdf_reader.pages:
                full_text += page.extract_text()

        # Ищем строку
        # с "Виписка сформована:"
        match = re.search(r'Виписка сформована:\s*(\d{2}\.\d{2}\.\d{4}\s+\d{2}:\d{2}:\d{2})', full_text)

        if match:
            return match.group(1)

    except ImportError:
        print("Библиотека PyPDF2 не установлена. Устновите с помощью: pip install PyPDF2", file=sys.stderr)
    except Exception as e:
        print(f"Ошибка при извлечении даты и времени: {str(e)}", file=sys.stderr)

    return ""
def dataframe_to_json(df):
    """
    Преобразует DataFrame в список словарей для JSON с числовыми индексами
    и конвертацией сумм в числа.
    """
    if df.empty:
        return []

    # Определяем столбцы с суммами для конвертации
    amount_columns = []
    for col in df.columns:
        if isinstance(col, str) and ('сума' in col.lower() or 'грн' in col.lower()):
            amount_columns.append(col)

    # Создаем список словарей для каждой строки
    rows = []
    for _, row in df.iterrows():
        row_dict = {}
        for i, (col, value) in enumerate(row.items(), 1):  # Нумерация с 1
            # Проверяем, нужно ли конвертировать значение в число
            if col in amount_columns or ('сума' in str(col).lower() or 'грн' in str(col).lower()):
                row_dict[i] = convert_to_number(str(value))
            else:
                if isinstance(value, str):
                    row_dict[i] = value.strip()
                elif pd.isna(value):
                    row_dict[i] = ""
                else:
                    row_dict[i] = value
        rows.append(row_dict)

    return rows

def process_pdf_to_json(pdf_path, output_path):
    """
    Обрабатывает PDF-файл и сохраняет данные таблицы в JSON.

    Args:
        pdf_path (str): Путь к PDF-файлу
        output_path (str): Путь для сохранения JSON-файла
    """
    # Извлекаем таблицу из PDF
    table_df = extract_table_from_pdf(pdf_path)

    # Извлекаем дату и время формирования выписки
    statement_datetime = extract_statement_datetime(pdf_path)

    # Преобразуем DataFrame в список словарей с числовыми индексами
    tables_data = dataframe_to_json(table_df)

    # Добавляем дату и время формирования выписки в каждую запись
    for row in tables_data:
        row[8] = statement_datetime

    # Сохраняем в JSON-файл с поддержкой кириллицы
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(tables_data, f, indent=4, ensure_ascii=False)

    print(f"Таблицы извлечены и сохранены в {output_path}", file=sys.stdout)

# Пример использования
if __name__ == "__main__":
    # Обработка PDF и сохранение в JSON
    process_pdf_to_json(pdf_path, output_path)

    # Выводим содержимое JSON для проверки
    # print("\nСодержимое извлеченных данных:")
    with open(output_path, "r", encoding="utf-8") as f:
        data = json.load(f)
        # print(json.dumps(data, indent=2, ensure_ascii=False))
