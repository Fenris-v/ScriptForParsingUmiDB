<?php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'api_key_db.php';
require_once 'count_autoGen.php';

$total_records = total_records_additional;
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

    $sql = "SELECT cms3_objects.type_id, cms3_objects.id, cms3_objects.name, cms3_hierarchy.is_active, cms3_hierarchy.alt_name, cms3_object_images.src
FROM cms3_objects
JOIN cms3_hierarchy
ON cms3_objects.type_id = 150 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0 && cms3_hierarchy.rel = 14
JOIN cms3_object_images
ON cms3_object_images.obj_id = cms3_objects.id && cms3_object_images.field_id = 10
LIMIT $start_from, $limit";

    $rs_result = mysqli_query($link, $sql);
    if ($rs_result) {
        $response["results"] = array();
        while ($row = mysqli_fetch_array($rs_result)) {
            $product = array();
            $product["type_id"] = $row["type_id"];
            $product["id"] = $row["id"];
            $product["name"] = $row["name"];
            $product["item_url"] = 'https://prazdnik-raduga.ru/dopolnitelnye-uslugi/' . $row["alt_name"] . '/';
            $product["img_url"] = str_replace('./', 'https://prazdnik-raduga.ru/', $row["src"]);
            $obj_id = $product["id"];

            $sql_content = "SELECT cms3_object_content.text_val, cms3_object_content.obj_id 
FROM cms3_object_content
WHERE cms3_object_content.obj_id = $obj_id && field_id = 496 || cms3_object_content.obj_id = $obj_id && field_id = 506";
            $content_result = mysqli_query($link, $sql_content);
            if ($content_result) {
                $i = 0;
                $contents = array();
                while ($row = mysqli_fetch_array($content_result)) {
                    $content = array();
                    $content[$i] = $row["text_val"];
                    $i++;
                    array_push($contents, $content);
                }
            }
            for ($i = 0; $i < count($contents); $i++) {
                foreach ($contents as $value) {
                    $product["content" . $i] = implode($contents[$i]);
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
mysqli_close($link);
