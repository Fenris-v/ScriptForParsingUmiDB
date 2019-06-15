<?php
header('Content-Type: application/json');
require_once 'db_config.php';
require_once 'api_key_db.php';
require_once 'count_autoGen.php';

$item_types = array(
    1 => "Мультяшный",
    "Сказочный",
    "Игровой",
    "Реалистичный",
    "Супергерой"
);

$link = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
if (isset($_GET["api_key"]) && $_GET["api_key"] == ANDROID_API_KEY) {
    if ($_GET["item_type"] >= 0 && $_GET["item_type"] <= 5) {
        switch ($_GET["item_type"]) {
            case 0:
                $total_records = total_records;
                break;
            case 1:
                $item_type = $item_types[1];
                $total_records = total_records_mult;
                break;
            case 2:
                $item_type = $item_types[2];
                $total_records = total_records_fairy;
                break;
            case 3:
                $item_type = $item_types[3];
                $total_records = total_records_game;
                break;
            case 4:
                $item_type = $item_types[4];
                $total_records = total_records_real;
                break;
            case 5:
                $item_type = $item_types[5];
                $total_records = total_records_hero;
                break;
        }
    } else {
        $total_records = total_records;
    }
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

    if ($_GET["item_type"] > 0 && $_GET["item_type"] <= 5) {
        $sql = "SELECT cms3_objects.type_id, cms3_objects.id, cms3_objects.name, cms3_hierarchy.is_active, cms3_filter_index_52_pages_6.tip_personazha, cms3_hierarchy.alt_name
FROM cms3_objects 
JOIN cms3_hierarchy
ON cms3_objects.type_id = 131 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
JOIN cms3_filter_index_52_pages_6
ON cms3_objects.id = cms3_filter_index_52_pages_6.obj_id && cms3_filter_index_52_pages_6.tip_personazha LIKE '%$item_type%'
LIMIT $start_from, $limit";
    } else {
        $sql = "SELECT cms3_objects.type_id, cms3_objects.id, cms3_objects.name, cms3_hierarchy.is_active, cms3_filter_index_52_pages_6.tip_personazha, cms3_hierarchy.alt_name
FROM cms3_objects
JOIN cms3_hierarchy
ON cms3_objects.type_id = 131 && cms3_hierarchy.is_active = 1 && cms3_objects.id = cms3_hierarchy.obj_id && cms3_hierarchy.is_deleted = 0
JOIN cms3_filter_index_52_pages_6
ON cms3_objects.id = cms3_filter_index_52_pages_6.obj_id
LIMIT $start_from, $limit";
    }
    $rs_result = mysqli_query($link, $sql);
    if ($rs_result) {
        $response["results"] = array();
        while ($row = mysqli_fetch_array($rs_result)) {
            $product = array();
            $product["type_id"] = $row["type_id"];
            $product["id"] = $row["id"];
            $product["name"] = $row["name"];
            $product["item_url"] = 'https://prazdnik-raduga.ru/animators/' . $row["alt_name"] . '/';
            $product["item_type"] = str_replace(";", " ", substr($row["tip_personazha"], 1, -1));
            $obj_id = $product["id"];
            $sql_img = "SELECT cms3_object_images.src, cms3_object_images.obj_id 
FROM cms3_object_images
WHERE cms3_object_images.obj_id = $obj_id && cms3_object_images.field_id = 497 || cms3_object_images.obj_id = $obj_id && cms3_object_images.field_id = 8";
            $img_result = mysqli_query($link, $sql_img);
            if ($img_result) {
                $i = 0;
                $images = array();
                while ($row = mysqli_fetch_array($img_result)) {
                    $img_url = array();
                    $img_url[$i] = str_replace('./', 'https://prazdnik-raduga.ru/', $row["src"]);
                    $i++;
                    array_push($images, $img_url);
                }
            }
            for ($i = 0; $i < count($images); $i++) {
                foreach ($images as $value) {
                    $product["img_url" . $i] = implode($images[$i]);
                }
            }

            $sql_content = "SELECT cms3_object_content.text_val, cms3_object_content.obj_id 
FROM cms3_object_content
WHERE cms3_object_content.obj_id = $obj_id && cms3_object_content.field_id = 496 || cms3_object_content.obj_id = $obj_id && cms3_object_content.field_id = 502";
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
