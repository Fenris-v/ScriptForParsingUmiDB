<?php
header('Content-Type: application/json');

require_once 'db_config.php';
require_once 'api_key_db.php';

$total_records = file_get_contents('test.txt');

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

if (isset($_GET["api_key"]) && $_GET["api_key"] == ANDROID_API_KEY) {

    if (isset($_GET["per_page"]) && $_GET["per_page"] > 0 && $_GET["per_page"] < $total_records) {
        $limit = $_GET["per_page"];
    } else {
        $limit = $total_records;
    }

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

        $response["results"] = array();
        while ($row = mysqli_fetch_array($rs_result)) {
            $product = array();
            $product["type_id"] = $row["type_id"];
            $product["id"] = $row["id"];
            $product["name"] = $row["name"];

            $obj_id = $product["id"];
            $sql_img = "SELECT cms3_object_images.src, cms3_object_images.obj_id 
FROM cms3_object_images
WHERE cms3_object_images.obj_id = $obj_id";

            $img_result = mysqli_query($link, $sql_img);
            if ($img_result){
                $i = 0;
                $images = array();
                while ($row = mysqli_fetch_array($img_result)) {
                    $img_url = array();
                    $img_url[$i] = str_replace('./', 'http://ct03381.tmweb.ru/', $row["src"]);
                    $i++;
                    array_push($images, $img_url);
                }
            }

            for ($i = 0; $i < count($images); $i++) {
                foreach ($images as $value) {
                    $product["img_url" . $i] = implode($images[$i]);
                }
            }

            array_push($response["results"], $product);
        }
        $response["page"] = $pn;
        $response["total_records"] = $total_records;
        $response["total_pages"] = $total_pages;
        $response["key"] = true;
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
} else {
    $response["key"] = false;
    echo json_encode($response);
}