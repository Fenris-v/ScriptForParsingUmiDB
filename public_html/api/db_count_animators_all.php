<?php
header('Content-Type: application/json');

require_once 'db_config.php'; // подключаем скрипт

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$sql_count = "SELECT cms3_objects.id
FROM cms3_objects
JOIN cms3_hierarchy
WHERE cms3_objects.type_id = 131 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0";
$rs_result_count = mysqli_query($link, $sql_count);
if ($rs_result_count) {
    $total_records = 1;
    while ($row2 = mysqli_fetch_row($rs_result_count)) {
        while ($row = mysqli_fetch_array($rs_result_count, MYSQLI_ASSOC)) {
            $total_records++;
        }
        echo $total_records;
        $filename = 'animators_all.txt';
        file_put_contents($filename, $total_records);
    }
} else {
    $response["success"] = '0';
    $response["message"] = 'error';

    echo json_encode($response);
}