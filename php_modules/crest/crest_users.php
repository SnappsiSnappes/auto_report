<?php
error_reporting(0);

require_once (__DIR__.'/remove_file_users_crest_csv.php');
require_once (__DIR__.'/crest.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function save3(array $data){
    $fp = fopen(__DIR__ . '/users_crest.csv', 'ab');
    foreach ($data as $fields)
    {
        fputcsv($fp,$fields,';'); // delimeter separator разделитель ;
    }
    fclose($fp);
};



function downloadLead3(){
    $prevID=0;
    while(true)
    {
        $batch=getBatch3($prevID);
        $result=CRest::callBatch($batch,1);
        if (!empty($result['result']['result'])){
            foreach($result['result']['result'] as $list)
            {
                $last =end($list);
                if($last['ID']> $prevID){
                    $prevID = $last['ID'];
                    save3($list);
                }
                else{
                    break 2;
                }
            }
        }
        else{
            break;
        }
    }


}

function getBatch3($prevID){
    $batch=[];
    for($i=0; $i<50; $i++){
        $batch['step_'.$i]=[
            'method'=>'user.search',
            'params'=> [

                'FILTER'=> [
                    '>ID'=> $prevID,
                    // 'UF_DEPARTMENT' => ['1','2','3','4','5','6'], # только менеджеры в конкретном отделе
                    'ACTIVE' => '1', // не уволенные
                ],
               'order'=>[
                    'ID'=>'ASC'],

                
                'select'=>[
                    'ID',
                    'FIRST_NAME',
                    'LAST_NAME'
                ],
                'start'=>-1,
            ]
        ];
        $prevID = '$result[step_'.$i.'][49][ID]';
    }
    return $batch;
}






downloadLead3();

// Открываем файл CSV
if (($handle = fopen(__DIR__ . "/users_crest.csv", "r")) !== FALSE) {
    $new_array = [];
    // Читаем файл CSV
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Удаляем все столбцы, кроме 0, 3 и 4
        $full_name = "{$data[3]} {$data[4]}";
        $new_data = [$data[0],$full_name ];
        // Добавляем новые данные в новый массив
        $new_array[] = $new_data;
    }
    fclose($handle);
}

// Записываем новый массив в новый файл CSV
$fp = fopen(__DIR__.'/users_crest.csv', 'w');
foreach ($new_array as $fields) {
    fputcsv($fp, $fields,';');
}
fclose($fp);


?>