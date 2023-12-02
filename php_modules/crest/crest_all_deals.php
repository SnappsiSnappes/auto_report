<?php
require_once (__DIR__.'/remove_file_deals_csv.php');
require_once (__DIR__.'/crest.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function save(array $data){
    $fp = fopen(__DIR__ . '/deals.csv', 'ab');
    foreach ($data as $fields)
    {
        fputcsv($fp,$fields,';');
    }
    fclose($fp);
}

function downloadLead(){
    $prevID=0;
    while(true)
    {
        $batch=getBatch($prevID);
        $result=CRest::callBatch($batch,1);
        if (!empty($result['result']['result'])){
            foreach($result['result']['result'] as $list)
            {
                $last =end($list);
                if($last['ID']> $prevID){
                    $prevID = $last['ID'];
                    save($list);
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

function getBatch($prevID){
    $batch=[];
    for($i=0; $i<50; $i++){
        $batch['step_'.$i]=[
            'method'=>'crm.deal.list',
            'params'=> [
               'order'=>[
                    'ID'=>'ASC'
                ], 
                'filter'=>[
                    '>ID'=>$prevID,
                    'CLOSED'=>'N',
                    
                ],
                'select'=>[
                    'ID',
                    'CATEGORY_ID',
                    'STAGE_ID',
                    'ASSIGNED_BY_ID',
                    'DATE_CREATE',
                    // 'UF_CRM_....',  */    

                ],
                'start'=>-1,
            ],
        ];
        $prevID = '$result[step_'.$i.'][49][ID]';
    }
    return $batch;
}



downloadLead();


?>