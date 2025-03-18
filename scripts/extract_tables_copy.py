import sys
import io
import tabula
import json
import os
import pandas as pd
import numpy as np

# Устанавливаем кодировку вывода в UTF-8
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

# Аргументы: путь к PDF и путь для сохранения JSON
pdf_path = sys.argv[1]
output_path = sys.argv[2]

# Функция для преобразования строки в число
def convert_to_number(value):
    if isinstance(value, str):
        value = value.replace(" ", "").replace("–", "-").replace(",", ".")
        try:
            return float(value)
        except ValueError:
            return value
    return value

# Проверка существования файла
if not os.path.exists(pdf_path):
    print(f"Файл не найден: {pdf_path}", file=sys.stderr)
    sys.exit(1)

try:
    # Извлечение таблиц из PDF
    tables = tabula.read_pdf(
        pdf_path,
        pages="all",
        multiple_tables=True,
        stream=True,
        lattice=False,
        pandas_options={"header": 0}
    )

    # Преобразование таблиц в список словарей
    tables_data = []
    for table in tables:
        if len(table.columns) > 1:
            table = table.iloc[:, :-1]
        table = table.dropna(how="all")
        if "Unnamed: 0" in table.columns and "N п/п" in table.columns:
            mask = table["N п/п"].isna()
            table.loc[mask, "N п/п"] = table.loc[mask, "Unnamed: 0"]
            table = table.drop(columns=["Unnamed: 0"])
        elif "Unnamed: 0" in table.columns and "N п/п" not in table.columns:
            table = table.rename(columns={"Unnamed: 0": "N п/п"})
        if "Сума" in table.columns:
            table["Сума"] = table["Сума"].apply(convert_to_number)
        if "Сума в грн." in table.columns:
            table["Сума в грн."] = table["Сума в грн."].apply(convert_to_number)
        tables_data.append(table.replace({np.nan: None}).to_dict(orient="records"))

    # Преобразуем ключи в числовые индексы
    tables_data_indexed = []
    for table in tables_data:
        indexed_table = []
        for row in table:
            indexed_row = {i + 1: value for i, value in enumerate(row.values())}
            indexed_table.append(indexed_row)
        tables_data_indexed.append(indexed_table)

    # Сохранение таблиц в JSON
    with open(output_path, "w", encoding="utf-8") as f:
        json.dump(tables_data_indexed, f, indent=4, ensure_ascii=False)

    print(f"Таблицы извлечены и сохранены в {output_path}", file=sys.stdout)
except Exception as e:
    print(f"Ошибка: {e}", file=sys.stderr)
    sys.exit(1)