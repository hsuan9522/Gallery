<?php
if ($_SESSION['ig_token'] == '') {
    echo '222';
    require '../auth/get_token_instagram.php';
}
echo $_SESSION['ig_token'] . '123';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/v1/tags/nofilter/media/recent/");
curl_setopt($ch, CURLOPT_POST, true); // 啟用POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(
    array(
        "access_token" => $_SESSION['ig_token'],
    )
));
$data = curl_exec($ch);

$data = str_replace('jsonFlickrApi(', '', $data);
$data = substr($data, 0, strlen($data) - 1); //strip out last paren

$data = json_decode($data);
echo json_encode($data);
curl_close($ch);
