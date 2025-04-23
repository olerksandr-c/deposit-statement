import sys
import json
import dbf
import datetime


def create_dbf(json_path, dbf_path):
    # Читаем JSON-файл
    with open(json_path, "r", encoding="utf-8") as file:
        data = json.load(file)

    # Проверяем, что data — это список
    if not isinstance(data, list):
        raise ValueError("Ошибка: JSON содержит не список, а другой тип данных.")

    # Проверяем, что каждая строка — это список, а не словарь
    for row in data:
        if not isinstance(row, list):
            raise ValueError(f"Ошибка: строка {row} не является списком.")

    # Фильтруем данные (работаем с индексами списка)
    filtered_data = [
        row
        for row in data
        if len(row) > 3
        and ("депозитних коштів" in row[3]
             or "Акцептування" in row[3]
             or "Коригуюча" in row[3])
        and "відсотк" not in row[3]
    ]

    if not filtered_data:
        raise ValueError("Ошибка: после фильтрации данные отсутствуют!")

    # Создаем DBF-таблицу с точной структурой полей
    table = dbf.Table(
        dbf_path,
        "n_doc C(35); mfo_db N(10,0); rr_db C(29); naim_d C(140); okpo_db C(14); "
        "mfo_cr N(10,0); rr_k C(29); naim_k C(140); okpo_cr C(14); sum N(20,9); "
        "sumeq N(20,9); prizn C(253); prizn_end C(167); dat D; dat_pr D; "
        "kod_val N(10,0); operat N(10,0); dat_gn D; dat_arc D; tim_pr C(8)",
        codepage="cp1251",
    )
    table.open(dbf.READ_WRITE)

    # Функция для конвертации строки даты в объект datetime.date
    def convert_date(date_str):
        try:
            parts = date_str.split(".")
            day = int(parts[0])
            month = int(parts[1])
            year = int(parts[2])
            if year < 100:  # Если год двузначный
                year = 2000 + year if year < 50 else 1900 + year
            return datetime.date(year, month, day)
        except Exception as e:
            print(f"Ошибка конвертации даты '{date_str}': {e}")
            return None

    # Счетчик для формирования номера документа
    doc_counter = 1

    # Записываем данные в DBF
    for row in filtered_data:
        try:
            # Получаем дату из JSON (индекс 1) и конвертируем её
            date_obj = convert_date(row[1]) if len(row) > 1 else None

            # Формируем номер документа: дата + порядковый номер
            if date_obj:
                n_doc = f"{date_obj.day:02d}{date_obj.month:02d}{date_obj.year}{doc_counter:03d}"
                doc_counter += 1
            else:
                n_doc = ""

            # Абсолютные значения для суммы
            sum_val = abs(float(row[4])) if len(row) > 4 else 0
            sumeq = abs(float(row[5])) if len(row) > 5 else 0

            # Заполняем поля значениями из JSON (работаем по индексам)
            mfo_db = 339500
            rr_db = "UA903395002610901655704000001"
            naim_d = 'АТ "ЧЕРНІГІВОБЛЕНЕРГО"'
            okpo_db = "22815333"
            mfo_cr = 339500
            rr_k = "UA443395002600201655704000001"
            naim_k = 'АТ "ЧЕРНІГІВОБЛЕНЕРГО"'
            okpo_cr = "22815333"
            prizn = row[6] if len(row) > 6 else ""
            prizn_end = "UA903395002610901655704000001"
            kod_val = 980

            # Определение операции по знаку суммы в исходных данных
            operat = 1 if float(row[4]) < 0 else 2

            # Извлекаем дату и время из row[7]
            datetime_str = row[7] if len(row) > 7 else ""
            date_from_row7 = None
            time_from_row7 = "12:00:00"

            if datetime_str:
                try:
                    date_part, time_part = datetime_str.split()
                    day, month, year = map(int, date_part.split('.'))
                    date_from_row7 = datetime.date(year, month, day)
                    time_from_row7 = time_part
                except Exception as e:
                    print(f"Ошибка при разборе даты/времени из строки '{datetime_str}': {e}")

            # Добавляем строку в DBF
            table.append(
                (
                    n_doc,
                    mfo_db,
                    rr_db,
                    naim_d,
                    okpo_db,
                    mfo_cr,
                    rr_k,
                    naim_k,
                    okpo_cr,
                    sum_val,
                    sumeq,
                    prizn,
                    prizn_end,
                    date_obj,
                    date_obj,
                    kod_val,
                    operat,
                    date_from_row7,
                    date_from_row7,
                    time_from_row7,
                )
            )
        except Exception as e:
            print(f"Ошибка при обработке строки {row}: {e}")

    table.close()
    print("DBF-файл успешно создан с отфильтрованными данными.")


if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Использование: python export_to_dbf.py <json_path> <dbf_path>")
        sys.exit(1)

    json_file = sys.argv[1]
    dbf_file = sys.argv[2]
    create_dbf(json_file, dbf_file)
