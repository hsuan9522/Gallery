<?php
$request_body = file_get_contents('php://input');
$json = json_decode($request_body, true);

$location = $json['location'];

$location_folder_items = scandir('../../images/carousel/' . $location);

$image_extension = array("jpg", "png", "jpeg");
$index = 0;
// echo $location;
foreach ($location_folder_items as $item) {
    $file_extension = pathinfo($item, PATHINFO_EXTENSION);
    if (in_array(strtolower($file_extension), $image_extension)) {
        $pic[$index]['url'] = '/images/carousel/' . $location . '/' . $item;
        $index++;
    }
}
echo json_encode($pic);
