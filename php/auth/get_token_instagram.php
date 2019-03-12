<?php
session_start();

$client_id = '9796b00132f44e2aa1ebf77101d05cd5';
$client_secret = 'a78939b999934c53a70eda95d6ec5a34';
$redirect_uri = 'http://localhost:8080/php/auth/get_token_instagram.php';

if (!isset($_GET['code']) || $_SESSION['code'] == 400) {
    $scope = 'basic+likes+comments+relationships+public_content';
    $code_url = 'https://api.instagram.com/oauth/authorize/?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&scope=' . $scope . '&response_type=code';
    header('Location:' . $code_url);

    echo 'Login With Instagram';
}

$_SESSION['ig_code'] = $_GET['code'];

$url = "https://api.instagram.com/oauth/access_token";

$access_token_parameters = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirect_uri,
    'code' => $_SESSION['ig_code'],
);
$curl = curl_init($url); // we init curl by passing the url
curl_setopt($curl, CURLOPT_POST, true); // to send a POST request
curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_parameters); // indicate the data to send
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // to stop cURL from verifying the peer's certificate.
$result = curl_exec($curl); // to perform the curl session
$result = json_decode($result);
curl_close($curl); // to close the curl session

$_SESSION['ig_token'] = $result->access_token;

echo json_encode($result);
