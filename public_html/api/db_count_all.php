<?php
header('Content-Type: application/json');

require_once 'db_config.php';

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$item_types = array(
    "Все аниматоры",
    "Мультяшный",
    "Сказочный",
    "Игровой",
    "Реалистичный",
    "Супергерой"
);

$total_records_animator = 0;
$total_records_mult = 0;
$total_records_fairy = 0;
$total_records_game = 0;
$total_records_real = 0;
$total_records_hero = 0;
$total_records_show = 0;
$total_records_master = 0;
$total_records_additional = 0;
$total_records_thematic = 0;
$total_records_season = 0;

$sql_count = "SELECT cms3_objects.id, cms3_filter_index_52_pages_6.tip_personazha
FROM cms3_objects
JOIN cms3_hierarchy
ON cms3_objects.type_id = 131 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
JOIN cms3_filter_index_52_pages_6
ON cms3_objects.id = cms3_filter_index_52_pages_6.obj_id";
$rs_result_count = mysqli_query($link, $sql_count);
if ($rs_result_count) {
    while ($row = mysqli_fetch_array($rs_result_count, MYSQLI_ASSOC)) {
        $total_records_animator++;
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

    $sql_count = "SELECT cms3_objects.id, cms3_objects.type_id, cms3_hierarchy.rel, cms3_hierarchy.rel
FROM cms3_objects
JOIN cms3_hierarchy
ON cms3_objects.type_id = 125 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
|| cms3_objects.type_id = 145 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
|| cms3_objects.type_id = 150 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0 && cms3_hierarchy.rel = 14
|| cms3_objects.type_id = 150 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0 && cms3_hierarchy.rel = 178
|| cms3_objects.type_id = 150 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0 && cms3_hierarchy.rel = 192";
    $rs_result_count = mysqli_query($link, $sql_count);
    if ($rs_result_count) {
        while ($row = mysqli_fetch_array($rs_result_count, MYSQLI_ASSOC)) {
            if ($row["type_id"] == 125) {
                $total_records_show++;
            }
            if ($row["type_id"] == 145) {
                $total_records_master++;
            }
            if ($row["type_id"] == 150 && $row["rel"] == 14) {
                $total_records_additional++;
            }
            if ($row["type_id"] == 150 && $row["rel"] == 178) {
                $total_records_thematic++;
            }
            if ($row["type_id"] == 150 && $row["rel"] == 192) {
                $total_records_season++;
            }
        }
    }

    echo $item_types[0] . " " . $total_records_animator . "\n";
    echo $item_types[1] . " " . $total_records_mult . "\n";
    echo $item_types[2] . " " . $total_records_fairy . "\n";
    echo $item_types[3] . " " . $total_records_game . "\n";
    echo $item_types[4] . " " . $total_records_real . "\n";
    echo $item_types[5] . " " . $total_records_hero . "\n";
    echo "Шоу " . $total_records_show . "\n";
    echo "Мастер-классы " . $total_records_master . "\n";
    echo "Доп услуги " . $total_records_additional . "\n";
    echo "Тематические вечеринки " . $total_records_thematic . "\n";
    echo "Сезонные " . $total_records_season . "\n";

    $filename = 'count_autoGen.php';
    $put = "<?php
    define(\"total_records\", $total_records_animator); 
    define(\"total_records_mult\", $total_records_mult);
    define(\"total_records_fairy\", $total_records_fairy);
    define(\"total_records_game\", $total_records_game);
    define(\"total_records_real\", $total_records_real);
    define(\"total_records_hero\", $total_records_hero);
    define(\"total_records_show\", $total_records_show);
    define(\"total_records_master\", $total_records_master);
    define(\"total_records_additional\", $total_records_additional);
    define(\"total_records_thematic\", $total_records_thematic);
    define(\"total_records_season\", $total_records_season);";

    file_put_contents($filename, $put);
} else {
    $response["success"] = '0';
    $response["message"] = 'error';

    echo json_encode($response);
}

mysqli_close($link);
