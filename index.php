<?php
session_start();
// Подключение к базе данных
require_once 'database.php';
require_once 'head.php';



/* #! прочитать last update */
$filename = 'last_time_update.csv';
// Открыть файл CSV для чтения
$file = fopen($filename, 'r');
// Прочитать первую строку из файла CSVf
$data = fgetcsv($file, 1000, ";");
// Занести данные в переменную
$last_time_update = $data[0];
// Закрыть файл
fclose($file);


if (!!$_POST["start-date"] and !!$_POST['end-date']) {
    
    $start = new DateTime($_POST["start-date"]);
    $end = new DateTime($_POST['end-date']);

    $interval = $start->diff($end);

    $report = $getter->getter_by_period($_POST["start-date"], $_POST['end-date']);

    $amount_of_days = $interval->days;
    $amount_of_days = $amount_of_days + 1;

    $current_day = $start->format('j'); // начальный день вашего периода
    $year = $start->format('Y'); // год начальной даты
    $month = $start->format('m'); // месяц начальной даты
    $days_in_month = date('t', strtotime("{$year}-{$month}-01")); // количество дней в месяце

} else {
    $start = new DateTime(date('Y-m-01')); // первый день текущего месяца
    $end = new DateTime(date('Y-m-d')); // текущий день

    $_POST['start-date'] = date('Y-m-01'); // текущий день
    $_POST['end-date'] = date('Y-m-d');

    $report = $getter->getter_by_period($_POST["start-date"], $_POST['end-date']);

    $interval = $start->diff($end); // разница
    $amount_of_days = $interval->days; // 29
    $amount_of_days = $amount_of_days + 1; // 30


    # просто выбирает текущий месяц от 1 до 31
}





/* #!! нормализация usera*/
if (($handle = fopen(__DIR__ . "/php_modules/crest/users_crest.csv", "r")) !== FALSE) {
    $users = array_map(function ($line) {
        return str_getcsv($line, ";");
    }, file(__DIR__ . "/php_modules/crest/users_crest.csv"));
    fclose($handle);
} else {
    echo "Не удалось открыть файл users_crest.csv";
}
foreach ($users as $user) {
    $normal_user[$user[0]] = $user[1];
}
/* #!! end */

?>

<style>
.form-control:focus {
  border-color: #DDEEEE;
  box-shadow: inset 0 1px 1px #00fff9, 0 0 8px #00fff9;
}

    /* remove gap */
    div.vertical-align:before,
    div.vertical-align:after {
        display: none
    }

    div.row:before,
    div.row:after {
        display: none
    }

    ::-webkit-scrollbar {
        width: 20px;
        /* Измените это значение, чтобы установить толщину полосы прокрутки */
        height: 32px;
    }

    ::-webkit-scrollbar-track {
        background: #636363;
        /* Цвет фона дорожки полосы прокрутки */
    }

    ::-webkit-scrollbar-thumb {
        background: #919191;
        /* Цвет фона ползунка полосы прокрутки */
    }





    body {
        background-color: rgba(212, 226, 235, 1);
    }

    .sammy-nowrap-1 {

        background-color: #DDEFEF;
        /* ячейки хеад */
        max-width: 70%;
        white-space: nowrap;
        border: solid 1px #000000;


    }

    .zui-table {
        border: solid 1px #DDEEEE;
        border-collapse: collapse;
        border-spacing: 0;

    }

    .zui-table thead th {
        background-color: #DDEFEF;
        /* ячейка хеад */
        border: solid 1px #000000;
        /* цвет текста */
        padding: 10px;
        text-align: center;

    }

    .zui-table tbody td {
        border: solid 1px #DDEEEE;
        /* #!!  очень важно   */
        padding: 0px;
        text-align: center;

    }

    .zui-table tbody td {
        border: solid 1px #DDEEEE;
        /* #!!  очень важно   */
        padding: 0px;
        text-align: center;

    }

    /* td style */
    .zui-table-highlight-all {
        overflow: hidden;
        z-index: 1;

    }

    .zui-table-highlight-all tbody td,
    .zui-table-highlight-all thead th {
        position: relative;
    }

    .zui-table-highlight-all tbody td:hover::before {
        content: '\00a0';

        background-color: #DDEFEF;
        /* выделение */

        height: 100%;
        left: -5000px;
        position: absolute;
        top: 0;
        width: 10000px;
        z-index: -1;
        /*         border: solid 1px #000000;  */

    }

    .zui-table-highlight-all tbody td:hover::after {
        background-color: #DDEFEF;
        /* выделение */
        content: '\00a0';
        height: 10000px;
        left: 0;
        position: absolute;
        top: -5000px;
        width: 100%;
        z-index: -1;

    }



    /* th style */
    .zui-table-highlight-all tbody th,
    .zui-table-highlight-all thead th {
        position: relative;
    }

    .zui-table-highlight-all tbody th:hover::before {
        content: '\00a0';

        background-color: #DDEFEF;
        /* выделение */

        height: 100%;
        left: -5000px;
        position: absolute;
        top: 0;
        width: 10000px;
        z-index: -1;
        /*         border: solid 1px #000000;  */

    }

    .zui-table-highlight-all tbody th:hover::after {
        background-color: #DDEFEF;
        /* выделение */
        content: '\00a0';
        height: 10000px;
        left: 0;
        position: absolute;
        top: -5000px;
        width: 100%;
        z-index: -1;

    }
</style>


<div class="d-inline-flex p-4    gap-0  ">
    <div class=" gap-0  d-inline-flex   boreder p-1" style='
    background-color: #DDEFEF; 
    border: solid 1px #000000;
    
    '>
        <form action="" class='gap-4 m-2 rounded-0  ' method='post'>
            <label class='m-2 fs-5 ' for="start">Начальная дата:</label><br>
            <input class='m-2' type="date" id="start" name="start-date" value=<?= $_POST['start-date'] ?>><br>
            <label class='m-2 fs-5 ' for="end">Конечная дата:</label><br>
            <input class='m-2' type="date" id="end" name="end-date" value=<?= $_POST['end-date'] ?>><br>
            <input class='rounded-0 m-2 btn btn-dark' type="submit" value="Выбрать">
        </form>


        <!-- #! update btn -->
        <table class=' d-flex align-items-end  text-center' >
            <tr>
                <th colspan="2" style="font-size: large;">
                    Количество дней между <br> начальной и конечной датой: <?= $amount_of_days ?> <br>
                </th>
            </tr>
            <tr>
                <td>
                    <form action="everyday_sender.php" method="post" id='update' class=' rounded-0 mt-auto '>
                        <div class="d-inline-flex">
                            <input type="submit" class=' rounded-0  btn btn-dark ' name="btn" value="Обновить данные">
                </td>
                <td>
                    <p class='m-2 text-nowrap   '> Последнее обновление: <br> <?= $last_time_update ?> </p>
                </td>

    </div>
    </form>
    </tr>
    </table>
</div>
</div>
<br>


<?
#! fix for prriod choices 

$current_day = $start->format('j'); // начальный день вашего периода
$days_in_month = date('t', strtotime("{$year}-{$month}-01")); // количество дней в месяце

?>

</div>
<div class='mb-5 gap-0   d-inline-flex'>

    <!-- #!! table -->
    <table class=" ps-5 ms-4 zui-table zui-table-highlight-all  ">
        <thead>
            <tr class=" ">
                <th class='mx-4 px-5 sammy-nowrap-1 text-start'>Фио менеджера</th>
                <?php
                $current_day = $start->format('j'); // начальный день вашего периода
                $year = $start->format('Y'); // год начальной даты
                $month = $start->format('m'); // месяц начальной даты
                $days_in_month = date('t', strtotime("{$year}-{$month}-01")); // количество дней в месяце

                for ($i = 1; $i <= $amount_of_days; $i++) : ?>
                    <th colspan="5"><?php echo str_pad($current_day, 2, '0', STR_PAD_LEFT) . '.' . $month; ?></th>
                    <?php
                    // Увеличиваем текущий день на 1
                    $current_day++;

                    // Если текущий день превышает количество дней в месяце,
                    // сбрасываем его обратно на 1
                    if ($current_day > $days_in_month) {
                        $current_day = 1;
                        // также увеличиваем месяц на 1
                        $month++;
                        // если месяц превышает 12, сбрасываем его обратно на 1 и увеличиваем год на 1
                        if ($month > 12) {
                            $month = 1;
                            $year++;
                        }
                        // обновляем количество дней в новом месяце
                        $days_in_month = date('t', strtotime("{$year}-{$month}-01"));
                    }
                    ?>
                <?php endfor; ?>
            </tr>
            <tr>
                <th></th>
                <?php for ($i = 1; $i <= $amount_of_days; $i++) : ?>
                    <th>
                        <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content=" ваше описание ">A1</a>
                </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A2</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A3</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание ">A4</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание ">A5</a>    
                    </th>

                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?
            // bing гений
            // Создаем новый массив для хранения данных по менеджерам
            $managers = [];

            // Перебираем отчет и заполняем массив менеджеров
            foreach ($report as $row) {
                $day = $row['day']; // используем полную дату вместо дня месяца
                if (!isset($managers[$row['manager_id']])) {
                    $managers[$row['manager_id']] = [];
                }
                $managers[$row['manager_id']][$day] = $row;
            }
            $current_day = clone $start; // начальный день вашего периода

            /* #!! alphabetical sorting */
            $temp = $managers;
            $managers = [];
            foreach ($temp as $sub => $content) {
                // fix for empty 
                if(!!$normal_user[$sub]){
                $managers[$normal_user[$sub]] =  $content;
            }
        };

            ksort($managers);
            $statistic = $managers;
            /* #!! end alphabetical sorting */
            
            foreach ($managers as $sub => $content) {
                echo "<tr>";
                echo "<td class='mx-4 px-5 sammy-nowrap-1 text-start' style='
                border: solid 1px #000000;
                
                '> "   . $sub . "</td>";

                $current_day = clone $start; // начальный день вашего периода

                for ($i = 1; $i <= $amount_of_days; $i++) {
                    $day_str = $current_day->format('Y-m-d');
                    if (isset($content[$day_str])) {
                        echo "<td style='border-left: solid 1px; ' >" . $content[$day_str]['A1'] . "</td>";
                        echo "<td>" . $content[$day_str]['A2'] . "</td>";
                        echo "<td >"  . $content[$day_str]['A3'] . "</td>";
                        echo "<td >"  . $content[$day_str]['A4'] . "</td>";
                        echo "<td style='border-right: solid 1px; ' >"  . $content[$day_str]['A5'] . "</td>";

                    } else {

                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";

                    }

                    // Увеличиваем текущий день на 1
                    $current_day->modify('+1 day');
                }
                echo "</tr>";
            }



            ?>


        </tbody>
    </table>
    <?
    /*#! dynamic  */
    $results = $getter->getter_by_period_diff($_POST["start-date"], $_POST['end-date']);

    $managers = [];
    $first_entries = [];
    foreach ($results as $result) {
        $manager_id = $result['manager_id'];
        if (!isset($first_entries[$manager_id])) {
            $first_entries[$manager_id] = $result;
        }
        $managers[$manager_id] = $result;
    }

    #<!-- #!!!differences v1 -->
/* здесь формируется таблица 'Динамика' , вычисляется разница между первым выбранным днём и последним по каждому менеджеру
Если в первый день в колонке A1 у менеджера было 100 сделок
а в последний день в колонке A1 у менеджера 200 сделок 
тогда в динамике будет показано +100
*/
    $differences = [];
    foreach ($managers as $manager_id => $manager) {
        $differences[$manager_id] = [
            'A3' => ($manager['A3'] - $first_entries[$manager_id]['A3']) >= 0 ? '+' . abs($manager['A3'] - $first_entries[$manager_id]['A3']) : '-' . abs($manager['A3'] - $first_entries[$manager_id]['A3']),
            'A2' => ($manager['A2'] - $first_entries[$manager_id]['A2']) >= 0 ? '+' . abs($manager['A2'] - $first_entries[$manager_id]['A2']) : '-' . abs($manager['A2'] - $first_entries[$manager_id]['A2']),
            'A1' => ($manager['A1'] - $first_entries[$manager_id]['A1']) >= 0 ? '+' . abs($manager['A1'] - $first_entries[$manager_id]['A1']) : '-' . abs($manager['A1'] - $first_entries[$manager_id]['A1']),
        ];
    }

    ?>


    <div class=' gap-0  pe-5'>

        <table class=' gap-0  ps-5 zui-table zui-table-highlight-all  text-center'>
            <thead style="border: unset;">

                <th colspan="9" class=''>Динамика</th>

                </tr>

                <tr>
                    <th colspan="3">ФИО</th>
                    <th>
                        <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A1</a>
                </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A2</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A3</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A4</a>    
                    </th>
                    <th>
                    <a tabindex="0" data-bs-placement="top"  data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="ваше описание">A5</a>    
                    </th>

                </tr>


            </thead>

            <?
            /* #!! sort dynamic */
            $temp2 = $managers;
            $managers = [];
            foreach ($temp2 as $sub => $content) {
                if (!!$normal_user[$sub]){
                $managers[$normal_user[$sub]] =  $content;
            }};
            ksort($managers);
            /* #!! end  sort dynamic */
            ?>
            <?php foreach ($managers as $sub => $content) : ?>
                <tr class=''>

                    <td colspan="3" class='sammy-nowrap-1' style="border: 1px solid black; ">
                        <?= $sub ?>
                    </td>

<!-- здесь происходит формирование динамики . Вам следует ознокомиться с массивами $managers и $differences для понимания , сделайте просто 
echo '<pre>; print_r($managers) ; die() или differences . вам станет более понятнее структура массивов. Что коасется этого формирования , то его можно спокойно сделать используя SQL запрос в файле database.php и здесь просто вывести в цикле эту информацию.  -->
                    <td style="border: 1px solid black;" class='px-2'><?= $differences[$content['manager_id']]['A1'] ?></td>
                    <td style="border: 1px solid black;" class='px-2'><?= $differences[$content['manager_id']]['A2'] ?></td>
                    <td style="border: 1px solid black;" class='px-2'><?= $differences[$content['manager_id']]['A3'] ?></td>
                    <td style="border: 1px solid black;" class='px-2'><?= 'место для ваших вычислений' ?></td>
                    <td style="border: 1px solid black;" class='px-2'><?= 'место для ваших вычислений' ?></td>

                </tr>

            <?php endforeach ?>
        </table>

    </div>
</div>
</div>
<!-- update gif -->
<script>
    // #! update gif
    $(document).ready(function() {
        $("#update").on("submit", function(event) {
            event.preventDefault();

            // Показать индикатор загрузки
            $("body").append('<div id="loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; text-align: center;"><img src="loading.gif" style="position: relative; top: 50%;" /></div>');

            $.ajax({
                url: "everyday_sender.php",
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    // Удалить индикатор загрузки
                    $("#loading").remove();

                    // Обновить страницу
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Удалить индикатор загрузки в случае ошибки
                    $("#loading").remove();
                    console.log(textStatus, errorThrown);
                }
            });
        });
    });
</script>

<?
/* #!!! differences ver 2  */
$temp3 = $differences;
$differences = [];
foreach($temp3 as $sub => $content){
    $differences[$normal_user[$sub]] = $content;
};
ksort($differences);

// #!! алгорит сортировки !! 
uasort($differences, function($a, $b) {
    $sumA = array_sum($a);
    $sumB = array_sum($b);
    return $sumB <=> $sumA;
});
// #!! конец алгорит сортировки

// #!! фикс пустого поста в графике 1
$_POST['chosen'] = $_POST['chosen'] == null ? array_key_first($differences) : $_POST['chosen'];

$chosen_one = $statistic[$_POST['chosen']];


function convertDate($curdate)
{
    $date = DateTime::createFromFormat('Y-m-d', $curdate);
    $date->modify('-1 day');
    return "new Date('" . $date->format('M d, Y') . "')";
}

?>

<br>
<!-- #! выбор менеджера -->
<div class=" d-inline-flex p-5 " >

    <form action="#chosen" method="post" id='chosen' class='d-inline-flex  mx-2 border border-dark-subtle p-5' 
    style='background-color:#DDEEEE;'>
    <label for="chosen" class="d-inline-flex mx-2">Выберите менеджера:</label>

        <select name='chosen' id='chosen' class='form-control bs-warning-border-subtle' >


            <option value=''>Выберите...</option>
            <option selected="selected"><?=$_POST['chosen']?></option>

            <? foreach ($managers as $sub => $content) : ?>
                <option value='<?= $sub ?>'><?= $sub ?></option>
            <? endforeach ?>
        </select>
        <input  type="hidden" id="start" name="start-date" value=<?=$_POST['start-date'] ?>>
        <input  type="hidden" id="end" name="end-date" value=<?=$_POST['end-date'] ?>>
        <input class='rounded-0 m-2 btn btn-dark 'id="btn" type="submit" value="Выбрать">

    </form>
</div>
<!-- #! конец -->
<br>

<div class="d-inline-flex pb-5">
<!-- #!! график 1 -->
<div class="d-flex flex-column">
<div id="line_top_x" class="ps-5 mt-5" style="width: 1100px; height: 500px;"> </div>
<div id="A2" class="ps-5 mt-5" style="width: 1100px; height: 500px;"> </div>
<div id="line_top_x_month" class="ps-5 mt-5" style="width: 1100px; height: 500px;"> </div>

</div>
<script type="text/javascript">
    /* #! график первый */
    google.charts.load('current', {
        'packages': ['line']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'День');
        data.addColumn('number', 'A1');
        data.addRows([
            <?
            foreach ($chosen_one as $sub => $content) {
                $date = convertDate($sub);
                echo "[{$date}, {$content['A1']}],";
            }
            ?>
        ]);
        var options = {
            hAxis: { format: 'dd MM' },

            curveType: 'function',
            legend: 'none',
            pointSize: 20,
            chart: {
                title: 'График активности <?=$_POST['chosen']?> A1',
            },
            pointSize: 30,

            curveType: 'function',
            titleTextStyle: {
                color: '#000000'
            },
            chartArea: {
                backgroundColor: {
                    fill: '#DDEFEF',

                },
            },
            colors: ['#ff218c'],
            backgroundColor: '#d4e2eb',
            width: 1000,
            height: 500,
            axes: {
                x: {
                    0: {
                        side: 'top'
                    }
                }
            }
        };
        var chart = new google.charts.Line(document.getElementById('line_top_x'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }

    
</script>

<script type="text/javascript">
    /* #! график A1 */
    google.charts.load('current', {
        'packages': ['line']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'День');
        data.addColumn('number', 'A2');
        data.addRows([
            <?
            foreach ($chosen_one as $sub => $content) {
                $date = convertDate($sub);
                echo "[{$date}, {$content['A2']}],";
            }
            ?>
        ]);
        var options = {
            hAxis: { format: 'dd MM' },

            curveType: 'function',
            legend: 'none',
            pointSize: 20,
            chart: {
                title: 'График активности <?=$_POST['chosen']?> A2',
            },
            pointSize: 30,

            curveType: 'function',
            titleTextStyle: {
                color: '#000000'
            },
            chartArea: {
                backgroundColor: {
                    fill: '#DDEFEF',

                },
            },
            colors: ['#131316'],
            backgroundColor: '#d4e2eb',
            width: 1000,
            height: 500,
            axes: {
                x: {
                    0: {
                        side: 'top'
                    }
                }
            }
        };
        var chart = new google.charts.Line(document.getElementById('A2'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }

    
</script>
<script type="text/javascript">
    /* #! график A2 */
    google.charts.load('current', {
        'packages': ['line']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'День');
        data.addColumn('number', 'A3');
        data.addRows([
            <?
            foreach ($chosen_one as $sub => $content) {
                $date = convertDate($sub);
                echo "[{$date}, {$content['A3']}],";
            }
            ?>
        ]);
        var options = {
            hAxis: { format: 'dd MM' },

            curveType: 'function',
            legend: 'none',
            pointSize: 20,
            chart: {
                title: 'График активности <?=$_POST['chosen']?> A3',
            },
            pointSize: 30,

            curveType: 'function',
            titleTextStyle: {
                color: '#000000'
            },
            chartArea: {
                backgroundColor: {
                    fill: '#DDEFEF',

                },
            },
            colors: ['#21b1ff'],
            backgroundColor: '#d4e2eb',
            width: 1000,
            height: 500,
            axes: {
                x: {
                    0: {
                        side: 'top'
                    }
                }
            }
        };
        var chart = new google.charts.Line(document.getElementById('line_top_x_month'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }

    
</script>

<?


function generateChartData($sortedArray) {
    $chartData = "";

    foreach($sortedArray as $sub => $content){
        
        $sum = $content['A3'] + $content['A2'] + $content['A1'];
        $sub = $sub . " ({$sum})";
        $chartData .= "['{$sub}', {$content['A1']},{$content['A2']},{$content['A3']} ],";
    }
    return $chartData;
}

// print_r(generateChartData($differences));

?>
<!-- #!!! график 2  -->
<div id="columnchart_material" style="width: 4000px; height: 500px; overflow-x: auto;"  class='me-5 pe-5'></div>

</div>
<script type="text/javascript">
    google.charts.load('current', {
        'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['Менеджер', 'A1', 'A2', 'A3'],
            <?=generateChartData($differences)?>

        ]);

        var options = {
            chart: {
                title: 'Динамика',
            },

            backgroundColor: '#d4e2eb',
            titleTextStyle: {
                color: '#000000'
            },
            colors: ['#ff218c',  '#131316','#21b1ff',],


            axes: {
                x: {
                    0: {
                        side: 'top'
                    }
                }
            },
            chartArea: {
                backgroundColor: {
                    fill: '#DDEFEF',

                }}
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, google.charts.Bar.convertOptions(options));
    }
</script>

<!-- #! enable poopovers -->
<script>
const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))

</script>

<? require_once 'bottom.php' ?>