<?php
header('Content-Type: application/json');

require_once 'db_config.php'; // подключаем скрипт

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$item_types = array(
    1 => "Мультяшный",
    "Сказочный",
    "Игровой",
    "Реалистичный",
    "Супергерой"
);

var_dump($item_types);

$sql_count = "SELECT cms3_objects.id, cms3_filter_index_52_pages_6.tip_personazha
FROM cms3_objects
LEFT JOIN cms3_hierarchy
ON cms3_objects.type_id = 131 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
JOIN cms3_filter_index_52_pages_6
ON cms3_objects.id = cms3_filter_index_52_pages_6.obj_id";
$rs_result_count = mysqli_query($link, $sql_count);
if ($rs_result_count) {
    $total_records = 1;
    $total_records_mult = 0;
    $total_records_fairy = 0;
    $total_records_game = 0;
    $total_records_real = 0;
    $total_records_hero = 0;
    while ($row2 = mysqli_fetch_row($rs_result_count)) {
        while ($row = mysqli_fetch_array($rs_result_count, MYSQLI_ASSOC)) {
            $total_records++;
            if (stripos($row["tip_personazha"], $item_types[1])) {
                $total_records_mult++;
            }
            if (stripos($row["tip_personazha"], $item_types[2])) {
                $total_records_fairy++;
            }
            if (stripos($row["tip_personazha"], $item_types[3])) {
                $total_records_game++;
            }
            if (stripos($row["tip_personazha"], $item_types[4])) {
                $total_records_real++;
            }
            if (stripos($row["tip_personazha"], $item_types[5])) {
                $total_records_hero++;
            }
        }
        echo $total_records . "\n";
        echo $item_types[1] . " " . $total_records_mult . "\n";
        echo $item_types[2] . " " . $total_records_fairy . "\n";
        echo $item_types[3] . " " . $total_records_game . "\n";
        echo $item_types[4] . " " . $total_records_real . "\n";
        echo $item_types[5] . " " . $total_records_hero . "\n";
        $filename = 'animators_all.txt';
        $filename_mult = 'animators_mult.txt';
        $filename_fairy = 'animators_fairy.txt';
        $filename_game = 'animators_game.txt';
        $filename_real = 'animators_real.txt';
        $filename_hero = 'animators_hero.txt';
        file_put_contents($filename, $total_records);
        file_put_contents($filename_mult, $total_records_mult);
        file_put_contents($filename_fairy, $total_records_fairy);
        file_put_contents($filename_game, $total_records_game);
        file_put_contents($filename_real, $total_records_real);
        file_put_contents($filename_hero, $total_records_hero);
    }
} else {
    $response["success"] = '0';
    $response["message"] = 'error';

    echo json_encode($response);
}

mysqli_close($link);