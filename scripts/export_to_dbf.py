import sys
import json
import dbf
import datetime
import os
import shutil

# --- Определяем директорию, где находится сам скрипт ---
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

# --- Формируем АБСОЛЮТНЫЙ путь к шаблону ---
# Важно: Считаем, что template_deposit_statement.dbf лежит
# В ТОЙ ЖЕ ПАПКЕ, что и export_to_dbf.py (т.е. внутри папки scripts)
TEMPLATE_DBF_PATH_ABS = os.path.join(SCRIPT_DIR, "template_deposit_statement.dbf")


# --- Немного изменим функцию create_dbf, чтобы она принимала абсолютный путь ---
def create_dbf(json_path, dbf_path, template_path_absolute):
    """
    Заполняет DBF-таблицу данными из JSON-файла, используя структуру
    существующего DBF-файла как шаблон.

    Args:
        json_path (str): Абсолютный путь к исходному JSON-файлу.
        dbf_path (str): Абсолютный путь для сохранения итогового DBF-файла.
        template_path_absolute (str): Абсолютный путь к файлу-шаблону DBF.
    """
    # --- 1. Проверка существования шаблона по АБСОЛЮТНОМУ пути ---
    if not os.path.exists(template_path_absolute):
        # Попытка вывести ошибку в UTF-8 для лучшего логгирования в PHP
        error_message = f"Ошибка Python: Файл-шаблон не найден по абсолютному пути: {template_path_absolute}"
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise FileNotFoundError(error_message)  # Вызываем ошибку с кодом 2
    print(f"Используется шаблон: {template_path_absolute}")

    # --- 2. Копирование шаблона (используем абсолютный путь) ---
    try:
        output_dir = os.path.dirname(dbf_path)
        if output_dir and not os.path.exists(output_dir):
            os.makedirs(output_dir)
            print(f"Создана директория: {output_dir}")
        shutil.copyfile(template_path_absolute, dbf_path)  # Используем абсолютный путь
        print(f"Шаблон скопирован из {template_path_absolute} в: {dbf_path}")
    except Exception as e:
        error_message = f"Ошибка Python копирования файла шаблона из '{template_path_absolute}' в '{dbf_path}': {e}"
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise IOError(error_message)

    # --- 3. Чтение и фильтрация JSON-данных ---
    try:
        with open(json_path, "r", encoding="utf-8") as file:
            data = json.load(file)
    except FileNotFoundError:
        error_message = f"Ошибка Python: JSON-файл не найден: {json_path}"
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise FileNotFoundError(error_message)  # Код 2
    except json.JSONDecodeError as e:
        error_message = (
            f"Ошибка Python: не удалось декодировать JSON: {json_path}. Ошибка: {e}"
        )
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise ValueError(error_message)  # Код 3 (или другой)
    except UnicodeDecodeError as e:
        error_message = f"Ошибка Python: файл '{json_path}' содержит некорректные UTF-8 символы. Ошибка: {e}"
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise ValueError(error_message)  # Код 3
    except Exception as e:
        error_message = f"Ошибка Python при чтении JSON '{json_path}': {e}"
        print(
            error_message.encode("utf-8", errors="replace").decode("utf-8"),
            file=sys.stderr,
        )
        raise IOError(error_message)  # Код 3

    if not isinstance(data, list):
        raise ValueError("Ошибка: JSON содержит не список, а другой тип данных.")

    for i, row in enumerate(data):
        if not isinstance(row, list):
            # Предоставляем больше контекста в ошибке
            raise ValueError(
                f"Ошибка: элемент JSON с индексом {i} не является списком: {row}"
            )

    filtered_data = [
        row
        for row in data
        if len(row) > 3
        and isinstance(row[3], str)  # Добавлена проверка типа перед in
        and (
            "депозитних коштів" in row[3]
            or "Акцептування" in row[3]
            or "Коригуюча" in row[3]
        )
        and "відсотк" not in row[3]
    ]

    if not filtered_data:
        print(
            "Внимание: После фильтрации данные для записи в DBF отсутствуют. Файл будет содержать только структуру шаблона."
        )
        # Не прерываем выполнение, создаем пустой файл со структурой
        # raise ValueError("Ошибка: после фильтрации данные отсутствуют!") # Закомментировано

    # --- 4. Открытие СКОПИРОВАННОЙ DBF-таблицы для записи ---
    # Структура уже определена файлом-шаблоном.
    # Указываем кодировку, которая должна совпадать с кодировкой шаблона
    try:
        table = dbf.Table(
            dbf_path,
            codepage="cp1251",  # Убедитесь, что эта кодировка соответствует шаблону
        )
        table.open(dbf.READ_WRITE)
    except dbf.DbfError as e:
        raise dbf.DbfError(
            f"Ошибка открытия DBF-файла '{dbf_path}' (возможно, проблема с кодировкой или структурой): {e}"
        )
    except Exception as e:
        raise IOError(f"Неожиданная ошибка при открытии DBF-файла '{dbf_path}': {e}")

    # --- 5. Подготовка данных и запись в DBF (логика осталась прежней) ---

    # Функция для конвертации строки даты в объект datetime.date
    def convert_date(date_str):
        if not date_str or not isinstance(date_str, str):
            print(
                f"Предупреждение: Пустое или некорректное значение для конвертации даты: {date_str}"
            )
            return None
        try:
            parts = date_str.split(".")
            if len(parts) != 3:
                print(
                    f"Предупреждение: Некорректный формат даты '{date_str}'. Ожидается DD.MM.YYYY или DD.MM.YY."
                )
                return None
            day = int(parts[0])
            month = int(parts[1])
            year_part = parts[2]
            # Обработка как 'YYYY', так и 'YY'
            if len(year_part) == 4:
                year = int(year_part)
            elif len(year_part) == 2:
                year_short = int(year_part)
                year = (
                    2000 + year_short if year_short < 50 else 1900 + year_short
                )  # Логика для YY
            else:
                print(f"Предупреждение: Некорректная длина года в дате '{date_str}'.")
                return None

            return datetime.date(year, month, day)
        except ValueError:  # Ошибка конвертации в int
            print(
                f"Предупреждение: Ошибка конвертации компонентов даты '{date_str}' в числа."
            )
            return None
        except Exception as e:
            print(f"Предупреждение: Ошибка конвертации даты '{date_str}': {e}")
            return None

    # Счетчик для формирования номера документа
    doc_counter = 1

    # Записываем данные в DBF
    for i, row in enumerate(filtered_data):
        try:
            # Проверяем наличие достаточного количества элементов
            if len(row) < 8:
                print(
                    f"Предупреждение: Пропуск строки {i+1} из-за недостаточного количества элементов ({len(row)} < 8): {row}"
                )
                continue

            # Получаем дату из JSON (индекс 1) и конвертируем её
            date_obj = convert_date(row[1])

            # Формируем номер документа: дата + порядковый номер
            if date_obj:
                n_doc = f"{date_obj.day:02d}{date_obj.month:02d}{date_obj.year}{doc_counter:03d}"
                doc_counter += 1
            else:
                # Если дата некорректна, используем запасной вариант или пропускаем
                print(
                    f"Предупреждение: Не удалось сформировать n_doc для строки {i+1}, т.к. дата некорректна: {row[1]}"
                )
                n_doc = f"NO_DATE_{doc_counter:03d}"  # Запасной вариант
                doc_counter += 1
                # или можно пропустить строку: continue

            # Сумма и проверка типа
            try:
                # Сначала получаем исходное значение (со знаком)
                sum_raw = float(row[4]) if row[4] is not None else 0.0
            except (ValueError, TypeError):
                print(
                    f"Предупреждение: Некорректное значение суммы (индекс 4) в строке {i+1}: {row[4]}. Используется 0."
                )
                sum_raw = 0.0
            # Берем абсолютное значение и округляем до 6 знаков после запятой
            sum_val = round(abs(sum_raw), 6)  # <--- ОКРУГЛЕНИЕ ДО 6 ЗНАКОВ

            # Сумма эквивалента и проверка типа
            try:
                # Сначала получаем исходное значение (со знаком)
                sumeq_raw = float(row[5]) if row[5] is not None else 0.0
            except (ValueError, TypeError):
                print(
                    f"Предупреждение: Некорректное значение суммы экв. (индекс 5) в строке {i+1}: {row[5]}. Используется 0."
                )
                sumeq_raw = 0.0
            # Берем абсолютное значение и округляем до 6 знаков после запятой
            sumeq = round(abs(sumeq_raw), 6)  # <--- ОКРУГЛЕНИЕ ДО 6 ЗНАКОВ

            # Заполняем поля значениями из JSON и константами
            # Важно: Порядок и типы данных должны СТРОГО СООТВЕТСТВОВАТЬ
            # структуре файла-шаблона template_deposit_statement.dbf
            mfo_db = 339500
            rr_db = "UA903395002610901655704000001"
            naim_d = 'АТ "ЧЕРНІГІВОБЛЕНЕРГО"'
            okpo_db = "22815333"
            mfo_cr = 339500
            rr_k = "UA443395002600201655704000001"
            naim_k = 'АТ "ЧЕРНІГІВОБЛЕНЕРГО"'
            okpo_cr = "22815333"
            prizn = (
                str(row[6]) if row[6] is not None else ""
            )  # Приведение к строке на всякий случай
            prizn_end = "UA903395002610901655704000001"  # Используем rr_db, как и раньше? Уточнить при необходимости
            kod_val = 980

            # Определение операции по знаку *исходной* суммы
            operat = 1 if sum_raw < 0 else 2  # Используем sum_raw до взятия abs()

            # Извлекаем дату и время из row[7]
            datetime_str = row[7]
            date_from_row7 = None
            time_from_row7_str = "12:00:00"  # Значение по умолчанию

            if datetime_str and isinstance(datetime_str, str):
                try:
                    parts = datetime_str.split()
                    if len(parts) >= 1:
                        date_part = parts[0]
                        # Попытка конвертировать дату из row[7]
                        date_from_row7_candidate = convert_date(date_part)
                        if date_from_row7_candidate:
                            date_from_row7 = date_from_row7_candidate
                        else:
                            print(
                                f"Предупреждение: Не удалось конвертировать дату из row[7] ('{date_part}') в строке {i+1}. Поля дат gn/arc будут пустыми."
                            )
                            # date_from_row7 останется None

                        # Если есть часть со временем
                        if len(parts) >= 2:
                            time_part = parts[1]
                            # Проверка формата времени (HH:MM:SS)
                            time_parts = time_part.split(":")
                            if len(time_parts) == 3 and all(
                                p.isdigit() for p in time_parts
                            ):
                                hour, minute, second = map(int, time_parts)
                                if (
                                    0 <= hour <= 23
                                    and 0 <= minute <= 59
                                    and 0 <= second <= 59
                                ):
                                    time_from_row7_str = (
                                        f"{hour:02d}:{minute:02d}:{second:02d}"
                                    )
                                else:
                                    print(
                                        f"Предупреждение: Некорректные значения времени '{time_part}' в строке {i+1}. Используется '{time_from_row7_str}'."
                                    )
                            else:
                                print(
                                    f"Предупреждение: Некорректный формат времени '{time_part}' в строке {i+1}. Используется '{time_from_row7_str}'."
                                )
                    else:
                        print(
                            f"Предупреждение: Не удалось разделить дату/время в строке {i+1}: '{datetime_str}'. Используются значения по умолчанию."
                        )

                except Exception as e:
                    print(
                        f"Предупреждение: Ошибка при разборе даты/времени из строки {i+1} ('{datetime_str}'): {e}. Используются значения по умолчанию."
                    )
            else:
                print(
                    f"Предупреждение: Отсутствует или некорректное значение даты/времени (индекс 7) в строке {i+1}: {datetime_str}. Используются значения по умолчанию."
                )

            # --- !!! ВАЖНО !!! ---
            # Создаем кортеж данных В ТОМ ЖЕ ПОРЯДКЕ, что и поля в ШАБЛОНЕ DBF
            record_data = (
                n_doc,  # n_doc C(35)
                mfo_db,  # mfo_db N(10,0)
                rr_db,  # rr_db C(29)
                naim_d,  # naim_d C(140)
                okpo_db,  # okpo_db C(14)
                mfo_cr,  # mfo_cr N(10,0)
                rr_k,  # rr_k C(29)
                naim_k,  # naim_k C(140)
                okpo_cr,  # okpo_cr C(14)
                sum_val,  # sum N(38,6) - ОКРУГЛЕНО до 6 знаков
                sumeq,  # sumeq N(38,6) - ОКРУГЛЕНО до 6 знаков
                prizn,  # prizn C(253)
                prizn_end,  # prizn_end C(167)
                date_obj,  # dat D
                date_obj,  # dat_pr D
                kod_val,  # kod_val N(10,0)
                operat,  # operat N(10,0)
                date_from_row7,  # dat_gn D
                date_from_row7,  # dat_arc D
                time_from_row7_str,  # tim_pr C(8)
            )

            # Добавляем подготовленную строку данных в DBF
            table.append(record_data)

        except dbf.DbfError as e:
            print(
                f"Ошибка DBF при добавлении строки {i+1} ({row}): {e}. Проверьте соответствие типов и длин данных структуре шаблона."
            )
            # Можно добавить `continue`, чтобы пропустить ошибочную строку
        except IndexError as e:
            print(
                f"Ошибка индекса при обработке строки {i+1} ({row}): {e}. Убедитесь, что в строке достаточно элементов."
            )
            # Можно добавить `continue`
        except Exception as e:
            # Ловим другие возможные ошибки при обработке строки
            print(f"Неожиданная ошибка при обработке строки {i+1} ({row}): {e}")
            # Можно добавить `continue`

    # --- 6. Закрытие DBF-файла ---
    table.close()
    print("-" * 30)
    if filtered_data:
        print(f"DBF-файл '{dbf_path}' успешно заполнен {len(filtered_data)} записями.")
    else:
        print(
            f"DBF-файл '{dbf_path}' создан со структурой шаблона, но без записей (данные отфильтрованы)."
        )


if __name__ == "__main__":
    if len(sys.argv) != 3:
        # Выводим в stderr, чтобы PHP мог это поймать при ошибке
        print(
            "Использование Python: python export_to_dbf.py <json_path> <dbf_path>",
            file=sys.stderr,
        )
        sys.exit(1)  # Код 1 для ошибки аргументов

    json_file_arg = sys.argv[1]
    dbf_file_arg = sys.argv[2]

    # Используем АБСОЛЮТНЫЙ путь к шаблону, определенный выше
    template_to_use = TEMPLATE_DBF_PATH_ABS

    try:
        # Передаем абсолютный путь в функцию
        create_dbf(json_file_arg, dbf_file_arg, template_to_use)
    except FileNotFoundError:
        # Сообщение уже должно было быть выведено функцией create_dbf
        sys.exit(2)  # Явно выходим с кодом 2
    except (ValueError, IOError, dbf.DbfError) as e:
        # Сообщение об ошибке уже должно было быть выведено
        # print(f"Ошибка выполнения Python: {e}".encode('utf-8', errors='replace').decode('utf-8'), file=sys.stderr) # Можно раскомментировать для доп. лога
        sys.exit(3)  # Код 3 для других ошибок обработки
    except Exception as e:
        # Ловим все остальные непредвиденные ошибки
        print(
            f"Непредвиденная ошибка Python: {e}".encode(
                "utf-8", errors="replace"
            ).decode("utf-8"),
            file=sys.stderr,
        )
        import traceback

        traceback.print_exc(file=sys.stderr)  # Печатаем traceback в stderr
        sys.exit(4)  # Код 4 для непредвиденных ошибок
