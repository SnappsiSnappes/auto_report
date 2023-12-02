# Документация к проекту

## Запуск

docker-compose up в терминале 
http://localhost:8091
phpmyadmin http://localhost:8055/ 
сервер db_service_auto_report
лог пароль - root root

## Описание

Веб-приложение “Авто отчёт” для Bitrix - это мощный инструмент, разработанный мной, который предлагает следующие преимущества:

1. Отчётность по сделкам: Приложение позволяет вести отчёт о сделках по менеджерам, предоставляя важные данные для анализа и улучшения производительности.

2. Гибкость периода: Вы можете выбрать любой период для сравнения данных, начиная с определенного дня и заканчивая конкретной датой.

3. Использование Docker: Docker был тщательно настроен и отлажен для обеспечения надежности и удобства использования.

4. Чёткая документация: Вся документация понятна и содержит комментарии в коде для лучшего понимания работы приложения.

5. Использование phpMyAdmin: Приложение использует phpMyAdmin для эффективного управления базой данных.

6. Гибкость настройки: Это простой и гибко настраиваемый бизнес-инструмент, который можно адаптировать под любые потребности.

7. Графики: В приложении представлены четыре графика: три из них по конкретному менеджеру, а один - общий. Это делает его мощным инструментом для отслеживания и анализа данных.

8. Эффективный алгоритм: Используется лучший алгоритм вытаскивания данных из Bitrix




## Введение

Этот проект был изначально разработан мной как рабочее задание, но теперь он переделан под Open Source и является частью моего портфолио на hh.ru. Это веб-приложение совместимо с любым аккаунтом Bitrix и относительно легко настраивается.

## Начало работы

Для начала работы с веб-приложением, вам потребуется прописать webhook в файле `/php_modules/crest/settings.php`.

## Как работает веб-приложение

1. **Скачивание данных из Bitrix**: За это отвечают два скрипта - `/php_modules/crest/crest_all_deals.php` и `/php_modules/crest/crest_users.php`. Эти скрипты используют библиотеку crest для эффективного выкачивания данных. По окончанию работы эти два скрипта создают два .csv файла - `deals.csv` и `users_crest.csv`. Вы можете настроить, какие поля сделок вы хотите выкачивать из Bitrix для дальнейшей работы.

2. **Парсинг .csv файлов и заполнение main_array**: За это отвечает файл `/php_modules/crest/csv_worker.php`. Этот файл следует изучить более детально, так как в нем необходимо разобраться, какие данные будут записаны в базу данных и в дальнейшем отображены в отчете. Чтобы включить выгрузку данных из Bitrix, вам нужно раскомментировать 12 и 13 строки. Далее происходит наполнение `$main_array` данными, сначала заполняется `'manager_id' => $user[0]`. Самое главное это блок условий, исходя и существующих .csv файлов, вы должны сформировать условия, какие сделки будут считаться а какие нет, обратите пристальное внимание как это сделано и записано в файле.

3. **Обновление списка в базе данных**: Файл `everyday_sender.php` отвечает за обновление списка в базе данных на текущий день. Здесь вы можете посмотреть ошибки, так как на этой стадии происходит отправка уже в базу данных.

4. **База данных**: Файл `database.php` отвечает за работу с базой данных. Очень важно посмотреть на метод класса send. Вам придется сюда вернуться, чтобы написать свой метод класса для формирования графиков.

5. **Основной файл**: Файл `index.php` написан в процедурном стиле. Здесь много циклов foreach, сортировка и т.д. Внимательно проследите за списком differences, у него есть две версии. Первый раз мы видем как формируется differences на 436 строчке, вам следует ознокомиться с комментариями в коде. Формирование таблицы Динамика происходит на 501 строчке, ознокомьтесь с комментарием в коде. На 549 строчке differences ver 2. Этот массив переделывается затем чтобы заполнить данные для графиков ниже.

В общем, это веб-приложение по своей сути гибкий инструмент ведения отчетности. Все, что вам нужно, это настроить CRON, чтобы он отправлял GET-запрос на `everyday_sender.php`, разобраться с принципом подсчета сделок у менеджера в `csv_worker.php` и в базе данных `database.php`. Пожалуйста, прочтите документацию и ознакомьтесь со скриншотами. Лучше всего вы поймете на простом примере, посмотрите основные файлы и увидите, что в них полно комментариев для понимания.