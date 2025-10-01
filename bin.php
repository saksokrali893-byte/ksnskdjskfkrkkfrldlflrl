<?php
// BIN sorgulama isteğini HandyAPI'ye ileten sunucu tarafı proxy

// Çıktının JSON olduğunu belirt
header('Content-Type: application/json');

if (!isset($_GET['bin'])) {
    http_response_code(400);
    echo json_encode(['Status' => 'ERROR', 'Message' => 'BIN numarası eksik.']);
    exit;
}

$bin = filter_var($_GET['bin'], FILTER_SANITIZE_STRING);

if (!preg_match('/^\d{3,11}$/', $bin)) {
    http_response_code(400);
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Geçersiz BIN formatı.']);
    exit;
}

$api_url = "https://data.handyapi.com/bin/" . $bin;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['Status' => 'ERROR', 'Message' => 'Sunucu Hatası: cURL başarısız oldu.']);
    exit;
}
curl_close($ch);

// HandyAPI'den gelen JSON yanıtını doğrudan döndür
http_response_code($http_code);
echo $response;
?>