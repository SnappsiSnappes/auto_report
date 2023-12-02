<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

#! убрать ошибки
error_reporting(E_ERROR | E_PARSE);


#! убрать  "//" с этих двух строк ниже, чтобы начать выгрузку данных из битрикса 
require_once (__DIR__.'/crest_all_deals.php');
require_once (__DIR__.'/crest_users.php');




# чтение файлов 
 if (($handle = fopen(__DIR__ . "/deals.csv", "r")) !== FALSE) {
    $deals = array_map(function($line) { return str_getcsv($line, ";"); }, file(__DIR__ . "/deals.csv"));
    fclose($handle);
} else {
    echo "Не удалось открыть файл deals.csv";
}

if (($handle = fopen(__DIR__ . "/users_crest.csv", "r")) !== FALSE) {
    $users = array_map(function($line) { return str_getcsv($line, ";"); }, file(__DIR__ . "/users_crest.csv"));
    fclose($handle);
} else {
    echo "Не удалось открыть файл users_crest.csv";
}



 $main_array = [];
 foreach($users as $user){
    $main_array[$user[0]] = [
    'A1' => 0, # просто сегодня
    'A2' => 0, # 
    'A3' => 0,
    'A4' => 0,
    'manager_id' =>$user[0], # id
    'A5' => 0,
    'A6' => 0,
    ];
 }
 $today = date('Y-m-d'); # сегодня
/* 
это данные из crest_all_deals.php расположены в SELECT на 59 строчке
0 - 'ID',                 
1 - 'CATEGORY_ID',      
2 - 'STAGE_ID',
3 - 'ASSIGNED_BY_ID' 
4 - 'DATE_CREATE'
 */
 foreach($deals as $deal){
    /* основной цикл заполнениями данных */
    $element_draft_day = $deal[4]; # $deal[4] = дата создания вот в таком виде 2020-01-01T09:57:53+03:00; находится в deals.csv
    $element_day = date('Y-m-d', strtotime($element_draft_day)); # привод даты битрикса в нормальный вид 2000-01-01

    $stage_element = $deal[5]; # кастомное поле , $deal[5] не существует в данной конфигурации, вам нужно добавить что то в crest_all_deals.php , чтобы начать считать свое поле из сделок 
    $stage_day = date('Y-m-d', strtotime($stage_element)); # кастомное сравнение. Например у вас есть несколько воронок , сделка  переходит в первую стадию какой то конкретой воронки и вы хотели бы посчитать ее здесь. Тогда создайте поле в сделке с датой и пусть робот битрикса заполнит это поле при попадании в стадию, а здесь вы можете вытащить это поле в crest_all_deals.php и посчитать его , например если $stage_day == $today { $main_array[$deal[3]['A5']]++ } 



# основной блок условий , после чего уже данные попадают в базу данных а потом выводятся на основной странице index.php
    if($element_day == $today){  
        $main_array[$deal[3]]['A1']++; # если сделка сегодня, то попадает в основной массив, под id конкретного человека
        # $deal[3] = поле ASSIGNED_BY_ID = id пользователя в битриксе
     }

     if($deal[1] == '0' && $deal['NEW'] != 'C12:6'){ # $deal[1] = категория сделки
        # deal[2] = стадия сделки, таким образом, условие наполняет массив менеджера, если сделка в конкретной категории но не в конкретной стадии
        $main_array[$deal[3]]['A2']++;
        # $deal[3] = поле ASSIGNED_BY_ID = id пользователя в битриксе

        if ($deal[2] == 'UC_EJJ2UK'){# если сделка в этой стадии то будет добавление в бд
        # deal[2] = стадия сделки, таким образом, условие наполняет массив менеджера, если сделка в конкретной категории и  в конкретной стадии 
            $main_array[$deal[3]]['A3']++;
        # $deal[3] = поле ASSIGNED_BY_ID = id пользователя в битриксе

        }
     }



     
 }
 



/* это запись когда последний раз нажимали на кнопку обновить */
/* #! last update */
$filename = 'last_time_update.csv'; # название файла так указано потому что текущий файл импортируется


// Получить текущую дату и время
$datetime = date('Y-m-d H:i:s');

// Открыть файл CSV для записи
$file = fopen($filename, 'w');

// Записать текущую дату и время в первую ячейку первой колонки
fputcsv($file, [$datetime], ';');

// Закрыть файл
fclose($file);


?>
