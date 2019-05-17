<?php
header('Content-Type: application/json');

require_once 'db_config.php';

$total_records = file_get_contents('test.txt');

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$limit = 10;
$total_pages = ceil($total_records / $limit);
if (isset($_GET["page"]) && $_GET["page"] > 0 && $_GET["page"] <= $total_pages) {
    $pn = $_GET["page"];
} else if (isset($_GET["page"]) && $_GET["page"] > $total_pages) {
    $pn = $total_pages;
} else {
    $pn = 1;
};
$start_from = ($pn - 1) * $limit;

$sql = "SELECT cms3_objects.type_id, cms3_objects.id, cms3_objects.name, cms3_object_images.src
FROM cms3_objects JOIN cms3_object_images
ON cms3_object_images.field_id = 300 && cms3_objects.id = cms3_object_images.obj_id
LIMIT $start_from, $limit";
$rs_result = mysqli_query($link, $sql);
if ($rs_result) {
    $response["products"] = array();
    while ($row = mysqli_fetch_array($rs_result)) {
        $product = array();
        $product["type_id"] = $row["type_id"];
        $product["id"] = $row["id"];
        $product["name"] = $row["name"];
        $product["img_url"] = str_replace('./', 'http://ct03381.tmweb.ru/', $row["src"]);
        array_push($response["products"], $product);
    }
    $response["page"] = $pn;
    $response["total_records"] = $total_records;
    $response["total_pages"] = $total_pages;
    if ($pn == $total_pages) {
        $response["has_more"] = false;
    } else {
        $response["has_more"] = true;
    }
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    $response["success"] = 0;
    $response["message"] = "No products found";
    echo json_encode($response);
}