<?php
session_start();
echo '<pre>';

// Подключение к базе данных
require_once 'database.php';
require_once 'head.php';
require_once 'php_modules/crest/csv_worker.php';


echo '<pre>';

// print_r($main_array);
//  die();

try{
$temp = $getter->send($main_array);}
catch(Exception $e){print_r($e);}

// print_r($temp);
// var_dump($temp);
?>

hello world


<?require_once 'bottom.php'?>