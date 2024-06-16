<?php
header("Access-Control-Allow-Origin: *");

if (!isset($_GET['url'])) {
    die("No URL provided");
}

$url = $_GET['url'];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo curl_error($ch);
} else {
    header("Content-Type: " . curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
    echo $response;
}

curl_close($ch);
?>
