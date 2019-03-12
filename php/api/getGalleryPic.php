<?php
header('Content-Type: application/json');

$request_body = file_get_contents('php://input');
$json = json_decode($request_body, true);

$foldername = strtolower($json['foldername']);
$page = strtolower($json['page']);
$country_name = $json['country_name'];

$once_render = 12; //一頁幾個
$all_pic = array();
// echo $page;
if ($foldername != 'photography') {
    $folder_items = preg_grep('/^([^.])/', scandir('../../images/gallery/' . $foldername));

    $image_extension = array("jpg", "png", "jpeg");
    $index = 0;
    foreach ($folder_items as $item) {
        $file_extension = pathinfo($item, PATHINFO_EXTENSION);
        if (in_array(strtolower($file_extension), $image_extension)) {
            $all_pic[$index]['small_url'] = '/images/gallery/' . $foldername . '/' . $item;
            $index++;
        }
    }
    $j = 0;
    for ($i = ($page - 1) * $once_render; $i < $once_render * $page; $i++) {
        if ($all_pic[$i]) {
            $pic['data'][$j++] = $all_pic[$i];
        }
    }
    $pic['arg']['now_page'] = $page;
    $pic['arg']['total_page'] = ceil($index / $once_render);
    //  print_r($pic);
    // print_r($all_pic);
    echo json_encode($pic);
} else {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.flickr.com/services/rest/");
    curl_setopt($ch, CURLOPT_POST, true); // 啟用POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
        array(
            "method" => 'flickr.photosets.getPhotos',
            "api_key" => '3dcf8f7380edf00f74172e39cea64e61',
            "user_id" => '142545419@N03',
            "photoset_id" => '72157673469306148',
            "per_page" => $once_render,
            "page" => $page,
            "extras" => 'url_o',
            "format" => 'json',
        )
    ));
    $data = curl_exec($ch);

    $data = str_replace('jsonFlickrApi(', '', $data);
    $data = substr($data, 0, strlen($data) - 1); //strip out last paren

    $data = json_decode($data);
    $country_tmp = '';
    foreach ($data->photoset->photo as $key => &$value) {
        preg_match('/(?<url>.*?)_(?<sercet>.*?)_(?<size>.*?)\.(?<extension>.*)/', $value->url_o, $match);
        $value->small_url = $match['url'] . '_' . $value->secret . '_z.jpg';
        $value->large_url = $match['url'] . '_' . $value->secret . '_b.jpg';
        $value->country = preg_replace('/-.*/', '', $value->title);
        if ($country_name != $value->country && $country_tmp != $value->country) {
            $value->country_first = true;
            $country_tmp = $value->country;
        } else {
            if ($country_tmp == $value->country || $country_name == $value->country) {
                $value->country_first = false;
            } else {
                $value->country_first = true;
                $country_tmp = $value->country;
            }
        }
    }
    echo json_encode($data);
    curl_close($ch);
}
