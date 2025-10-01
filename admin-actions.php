<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$keysFile = 'users/bbbba.json';

function loadKeysData($keysFile) {
    $keysData = ['keys' => []];
    if (file_exists($keysFile)) {
        $content = file_get_contents($keysFile);
        if ($content !== false) {
            $decoded = json_decode($content, true);
            if ($decoded !== null) {
                $keysData = $decoded;
            }
        }
    }
    if (!isset($keysData['keys'])) {
        $keysData['keys'] = [];
    }
    return $keysData;
}

function saveKeysData($keysFile, $keysData) {
    $dir = dirname($keysFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    return file_put_contents($keysFile, json_encode($keysData, JSON_PRETTY_PRINT)) !== false;
}

function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add_key':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyValue = trim($_POST['keyValue'] ?? '');
            $expiresAt = $_POST['expiresAt'] ?? '';
            $maxUsage = intval($_POST['maxUsage'] ?? 1);
            $maxIPs = intval($_POST['maxIPs'] ?? 3);
            $description = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['isActive']);
            
            if (empty($keyValue)) {
                setMessage('Key değeri boş olamaz!', 'danger');
                break;
            }
            
            if (empty($expiresAt)) {
                setMessage('Bitiş tarihi boş olamaz!', 'danger');
                break;
            }
            
            if ($maxUsage < 1) {
                setMessage('Maksimum kullanım 1\'den küçük olamaz!', 'danger');
                break;
            }
            
            $keysData = loadKeysData($keysFile);
            
            foreach ($keysData['keys'] as $key) {
                if (isset($key['key']) && $key['key'] === $keyValue) {
                    setMessage('Bu key değeri zaten mevcut!', 'danger');
                    break 2;
                }
            }
            
            $newKey = [
                'key' => $keyValue,
                'expiresAt' => $expiresAt,
                'maxUsage' => $maxUsage,
                'maxIPs' => $maxIPs,
                'allowedIPs' => [],
                'currentUsage' => 0,
                'description' => $description,
                'owner' => 'Admin',
                'isActive' => $isActive,
                'createdAt' => date('Y-m-d H:i:s')
            ];
            
            $keysData['keys'][] = $newKey;
            
            if (saveKeysData($keysFile, $keysData)) {
                setMessage('Key başarıyla eklendi!', 'success');
            } else {
                setMessage('Key eklenirken bir hata oluştu!', 'danger');
            }
        }
        break;
    
    case 'edit_key':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyIndex = intval($_POST['key_index'] ?? -1);
            $keyValue = trim($_POST['keyValue'] ?? '');
            $expiresAt = $_POST['expiresAt'] ?? '';
            $maxUsage = intval($_POST['maxUsage'] ?? 1);
            $currentUsage = intval($_POST['currentUsage'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['isActive']);
            
            if (empty($keyValue)) {
                setMessage('Key değeri boş olamaz!', 'danger');
                break;
            }
            
            if (empty($expiresAt)) {
                setMessage('Bitiş tarihi boş olamaz!', 'danger');
                break;
            }
            
            if ($maxUsage < 1) {
                setMessage('Maksimum kullanım 1\'den küçük olamaz!', 'danger');
                break;
            }
            
            if ($currentUsage < 0) {
                setMessage('Mevcut kullanım 0\'dan küçük olamaz!', 'danger');
                break;
            }
            
            $keysData = loadKeysData($keysFile);
            
            if ($keyIndex < 0 || $keyIndex >= count($keysData['keys'])) {
                setMessage('Geçersiz key indeksi!', 'danger');
                break;
            }
            
            foreach ($keysData['keys'] as $index => $key) {
                if ($index !== $keyIndex && isset($key['key']) && $key['key'] === $keyValue) {
                    setMessage('Bu key değeri zaten mevcut!', 'danger');
                    break 2;
                }
            }
            
            $keysData['keys'][$keyIndex]['key'] = $keyValue;
            $keysData['keys'][$keyIndex]['expiresAt'] = $expiresAt;
            $keysData['keys'][$keyIndex]['maxUsage'] = $maxUsage;
            $keysData['keys'][$keyIndex]['currentUsage'] = $currentUsage;
            $keysData['keys'][$keyIndex]['description'] = $description;
            $keysData['keys'][$keyIndex]['isActive'] = $isActive;
            
            if (saveKeysData($keysFile, $keysData)) {
                setMessage('Key başarıyla güncellendi!', 'success');
            } else {
                setMessage('Key güncellenirken bir hata oluştu!', 'danger');
            }
        }
        break;
    
    case 'delete_key':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyIndex = intval($_POST['key_index'] ?? -1);
            $keysData = loadKeysData($keysFile);
            
            if ($keyIndex < 0 || $keyIndex >= count($keysData['keys'])) {
                setMessage('Geçersiz key indeksi!', 'danger');
                break;
            }
            
            array_splice($keysData['keys'], $keyIndex, 1);
            
            if (saveKeysData($keysFile, $keysData)) {
                setMessage('Key başarıyla silindi!', 'success');
            } else {
                setMessage('Key silinirken bir hata oluştu!', 'danger');
            }
        }
        break;
    
    case 'toggle_key':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keyIndex = intval($_POST['key_index'] ?? -1);
            $keysData = loadKeysData($keysFile);
            
            if ($keyIndex < 0 || $keyIndex >= count($keysData['keys'])) {
                setMessage('Geçersiz key indeksi!', 'danger');
                break;
            }
            
            $currentStatus = $keysData['keys'][$keyIndex]['isActive'] ?? false;
            $keysData['keys'][$keyIndex]['isActive'] = !$currentStatus;
            
            if (saveKeysData($keysFile, $keysData)) {
                $newStatus = $keysData['keys'][$keyIndex]['isActive'] ? 'aktif' : 'pasif';
                setMessage("Key başarıyla {$newStatus} yapıldı!", 'success');
            } else {
                setMessage('Key durumu değiştirilirken bir hata oluştu!', 'danger');
            }
        }
        break;
    
    case 'logout':
        session_destroy();
        header('Location: admin-login.php');
        exit;
    
    default:
        setMessage('Geçersiz işlem!', 'danger');
        break;
}

header('Location: adminX9279N725E.php');
exit;
?>