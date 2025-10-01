<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

function getUserIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function logIPAccess($key, $ip, $status) {
    $logFile = base64_decode('dXNlcnMvaXBfbG9ncy5qc29u');
    $logData = [];
    
    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        if ($content !== false) {
            $logData = json_decode($content, true) ?: [];
        }
    }
    
    if (!isset($logData['logs'])) {
        $logData['logs'] = [];
    }
    
    $logData['logs'][] = [
        'key' => $key,
        'ip' => $ip,
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if (count($logData['logs']) > 1000) {
        $logData['logs'] = array_slice($logData['logs'], -1000);
    }
    
    file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));
}

function sendTelegramNotification($message) {
    $botToken = '8066679823:AAGuUDujD3BKx_MXCUrwP6eBMZcTrYhB9MM';
    $chatId = '-1002754106820';
    
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    @file_get_contents($url, false, $context);
}

if (!isset($_GET['key']) || empty($_GET['key'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anahtar parametresi sağlanmadı']);
    exit;
}

$key = $_GET['key'];
$userIP = getUserIP();
$keysFile = base64_decode('dXNlcnMvYmJiYmEuanNvbg==');

if (!file_exists($keysFile)) {
    logIPAccess($key, $userIP, 'error_file_not_found');
    echo json_encode(['status' => 'error', 'message' => 'Anahtar dosyası bulunamadı']);
    exit;
}

$data = json_decode(file_get_contents($keysFile), true);

if (!$data || !isset($data['keys'])) {
    logIPAccess($key, $userIP, 'error_invalid_data');
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz anahtar verisi']);
    exit;
}

$found = false;
$keyData = null;
$keyIndex = -1;

foreach ($data['keys'] as $index => $entry) {
    if ($entry['key'] === $key) {
        $found = true;
        $keyData = $entry;
        $keyIndex = $index;
        break;
    }
}

if (!$found) {
    logIPAccess($key, $userIP, 'error_invalid_key');
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz anahtar']);
    exit;
}

if (!$keyData['isActive']) {
    logIPAccess($key, $userIP, 'error_inactive');
    echo json_encode(['status' => 'error', 'message' => 'Anahtar aktif değil']);
    exit;
}

$expirationDate = new DateTime($keyData['expiresAt']);
$currentDate = new DateTime();

if ($currentDate > $expirationDate) {
    logIPAccess($key, $userIP, 'error_expired');
    echo json_encode(['status' => 'error', 'message' => 'Anahtar süresi dolmuş']);
    exit;
}

$allowedIPs = isset($keyData['allowedIPs']) ? $keyData['allowedIPs'] : [];
$isExistingIP = in_array($userIP, $allowedIPs);

if (!$isExistingIP) {
    $maxUsage = isset($keyData['maxUsage']) ? (int)$keyData['maxUsage'] : 1;
    $currentUsage = isset($keyData['currentUsage']) ? (int)$keyData['currentUsage'] : 0;

    if ($currentUsage >= $maxUsage) {
        logIPAccess($key, $userIP, 'error_usage_limit_exceeded');
        echo json_encode(['status' => 'error', 'message' => 'Kullanım (cihaz) sınırı aşıldı. Bu anahtar maksimum ' . $maxUsage . ' farklı cihazda kullanılabilir.']);
        exit;
    }

    $data['keys'][$keyIndex]['allowedIPs'][] = $userIP;
    $data['keys'][$keyIndex]['currentUsage'] = $currentUsage + 1;
    
    file_put_contents($keysFile, json_encode($data, JSON_PRETTY_PRINT));
    
    $keyData = $data['keys'][$keyIndex];
    
    $ownerName = $keyData['owner'] ?? 'Unknown';
    $telegramMessage = "🔐 <b>Yeni IP Girişi</b>\n\n" .
                      "👤 <b>Kullanıcı:</b> {$ownerName}\n" .
                      "🔑 <b>Key:</b> <code>{$key}</code>\n" .
                      "🌐 <b>IP Adresi:</b> <code>{$userIP}</code>\n" .
                      "📊 <b>Kullanım:</b> {$keyData['currentUsage']}/{$maxUsage}\n" .
                      "🕐 <b>Tarih:</b> " . date('Y-m-d H:i:s');
    
    sendTelegramNotification($telegramMessage);
}

logIPAccess($key, $userIP, 'success');

echo json_encode([
    'status' => 'success',
    'message' => 'Geçerli anahtar',
    'owner' => $keyData['owner'] ?? 'Unknown',
    'ip' => $userIP,
    'remaining_usage' => ($keyData['maxUsage'] ?? 1) - ($keyData['currentUsage'] ?? 0)
]);
?>