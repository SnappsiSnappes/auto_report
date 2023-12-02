<?php
$host = 'db';
$db   = 'auto_report';
$user = 'root';
$pass = 'root';
$port = 3306;

// $dsn = "mysql:host=".$host.';dbname='.$db;
$dsn = 'mysql:host='.$host.";port={$port}".';dbname='.$db;

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass,$opt);

$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


class Getter
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function getter_by_period_diff($period_start=null, $period_end=null)
    {
        if (is_null($period_start)) {
            $period_start = date('Y-m-01'); // первый день текущего месяца
            $period_end = date('Y-m-t'); // последний день текущего месяца
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM day_report WHERE day BETWEEN :start AND :end ORDER BY manager_id, day");
        $stmt->bindParam(':start', $period_start);
        $stmt->bindParam(':end', $period_end);
        $stmt->execute();
        return  $stmt->fetchAll();
    
    }
        
    public function getter_by_period($period_start=null, $period_end=null)
    {
        if (is_null($period_start)) {
            $period_start = date('Y-m-01'); // первый день текущего месяца
            $period_end = date('Y-m-t'); // последний день текущего месяца
        }
    
        $stmt = $this->pdo->prepare("SELECT * FROM day_report WHERE day BETWEEN :start AND :end ORDER BY manager_id, day");
        $stmt->bindParam(':start', $period_start);
        $stmt->bindParam(':end', $period_end);
        $stmt->execute();
        return $stmt->fetchAll();
    

    }
    
    public function getter_by_manager_id($manager_id)
    {



        $currentDate = date('Y-m-d', strtotime('-1 day'));

        $stmt = $this->pdo->prepare("SELECT * FROM day_report WHERE manager_id = :manager_id and  day =:day ");
        $stmt->bindParam(':manager_id', $manager_id);
        $stmt->bindParam(':day', $currentDate);
        $stmt->execute();
        return $stmt->fetchAll();

    }

    public function send($obj)
    {


        $currentDate = date('Y-m-d', strtotime('-1 day'));
        
        
        // $nextDay = date('Y-m-d', strtotime($currentDate . ' +1 day')); // следующий день

        foreach ($obj as $sub_array => $content) {

        #! duble check
        // надежная проверка, т.к. в этом методе он проверяет на текущий день в SQL запросе
        $x = $this->getter_by_manager_id($content['manager_id']);
        if (!!$x) {
            // delete from day_report where day = '2023-11-15';
            $stmt = $this->pdo->prepare("UPDATE day_report 
            SET 
            manager_id = ?, 
            A1 = ?, 
            A2 = ?, 
            A3 = ?,
            A4 = ?,
            A5 = ?
            WHERE day = ? AND id=?");
            $stmt->execute([

                $content['manager_id'],
                $content['A1'],
                $content['A2'],
                $content['A3'],
                $content['A4'],
                $content['A5'],
                $currentDate,
                $x[0]['id'],
            ]);
            continue;

        }else{
            $stmt = $this->pdo->prepare("INSERT INTO day_report (
                manager_id,
                A1,
                A2,
                A3,
                A4,
                A5,
                day

            ) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([
                $content['manager_id'],
                $content['A1'],
                $content['A2'],
                $content['A3'],
                $content['A4'],
                $content['A5'],
                $currentDate,
            ]);
        }}
    }


    public function getter_by_manager_id_p($period_start, $period_end)
    {




        $stmt = $this->pdo->prepare("SELECT manager_id ,sum(A1) FROM day_report WHERE day BETWEEN :start AND :end  group BY manager_id ");
        $stmt->bindParam(':start', $period_start);
        $stmt->bindParam(':end', $period_end);
        $stmt->execute();
        return  $stmt->fetchAll();

    }




}

$getter = new Getter($pdo);

