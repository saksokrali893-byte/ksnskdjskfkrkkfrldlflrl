<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$keysFile = 'users/bbbba.json';
$keysData = [];
if (file_exists($keysFile)) {
    $content = file_get_contents($keysFile);
    if ($content !== false) {
        $keysData = json_decode($content, true);
    }
}

if (!isset($keysData['keys'])) {
    $keysData['keys'] = [];
}

$totalKeys = count($keysData['keys']);
$activeKeys = 0;
$expiredKeys = 0;

foreach ($keysData['keys'] as $key) {
    if (isset($key['isActive']) && $key['isActive']) {
        $activeKeys++;
    }
    
    if (isset($key['expiresAt'])) {
        $expiryDate = strtotime($key['expiresAt']);
        if ($expiryDate < time()) {
            $expiredKeys++;
        }
    }
}

$message = '';
$messageType = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Api Service</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #059669;
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.95);
            --card-header-bg: rgba(51, 65, 85, 0.95);
            --border-color: #334155;
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --box-shadow: 0 20px 35px rgba(0, 0, 0, 0.3);
            --hover-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
        }
        
        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 4px 25px rgba(37, 99, 235, 0.3);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-color) !important;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, var(--success), #10b981);
            color: white !important;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            white-space: nowrap;
        }
        
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--box-shadow);
            backdrop-filter: blur(20px);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: var(--card-header-bg);
            border-bottom: 1px solid var(--border-color);
            border-radius: 20px 20px 0 0 !important;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: var(--text-color);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 2px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
            color: var(--text-color);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success), #10b981);
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #10b981, var(--success));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626, var(--danger-color));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
            border: none;
            border-radius: 12px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #f59e0b, var(--warning-color));
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        }
        
        .table-dark {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-dark th,
        .table-dark td {
            border-color: var(--border-color);
            padding: 12px;
            vertical-align: middle;
        }
        
        .table-dark thead th {
            background: var(--card-header-bg);
            font-weight: 600;
        }
        
        .badge {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .status-active {
            background: var(--success-color);
            color: white;
        }
        
        .status-inactive {
            background: var(--danger-color);
            color: white;
        }
        
        .status-expired {
            background: var(--warning-color);
            color: white;
        }
        
        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: var(--box-shadow);
        }
        
        .modal-header {
            background: var(--card-header-bg);
            border-bottom: 1px solid var(--border-color);
            border-radius: 20px 20px 0 0;
        }
        
        .modal-title {
            color: var(--text-color);
            font-weight: 600;
        }
        
        .btn-close {
            background: transparent;
            border: none;
            color: var(--text-color);
            font-size: 1.5rem;
            opacity: 0.7;
            filter: invert(1);
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success-color);
            border-radius: 12px;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: var(--danger-color);
            border-radius: 12px;
        }
        
        .stats-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: var(--box-shadow);
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        .form-control-sm {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .text-break {
            word-break: break-all;
        }
        
        .logout-btn {
            color: var(--danger-color) !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            color: white !important;
            background: var(--danger-color);
            border-radius: 6px;
            padding: 6px 12px;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-icon {
                font-size: 2rem;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-shield me-2"></i>Api<span style="color:#10b981">Service</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-key me-1"></i>Key Yönetimi
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="admin-badge me-3">
                            <i class="fas fa-crown me-1"></i>Admin
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link logout-btn" href="admin-actions.php?action=logout">
                            <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($messageType); ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-primary">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="stats-number"><?php echo $totalKeys; ?></div>
                    <div class="stats-label">Toplam Key</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stats-number"><?php echo $activeKeys; ?></div>
                    <div class="stats-label">Aktif Key</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stats-number"><?php echo $totalKeys - $activeKeys; ?></div>
                    <div class="stats-label">Pasif Key</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card">
                    <div class="stats-icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stats-number"><?php echo $expiredKeys; ?></div>
                    <div class="stats-label">Süresi Dolmuş</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Yeni Key Ekle
                </h5>
            </div>
            <div class="card-body">
                <form action="admin-actions.php" method="POST">
                    <input type="hidden" name="action" value="add_key">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="keyValue" class="form-label">Key Değeri</label>
                            <input type="text" class="form-control" id="keyValue" name="keyValue" required placeholder="Key değerini girin">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expiresAt" class="form-label">Bitiş Tarihi</label>
                            <input type="datetime-local" class="form-control" id="expiresAt" name="expiresAt" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="maxUsage" class="form-label">Maksimum Kullanım</label>
                            <input type="number" class="form-control" id="maxUsage" name="maxUsage" min="1" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Key açıklaması (isteğe bağlı)">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="isActive" name="isActive" checked>
                                <label class="form-check-label" for="isActive">
                                    Aktif olarak başlat
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Key Ekle
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Key Listesi
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Durum</th>
                                <th>Kullanım</th>
                                <th>Bitiş Tarihi</th>
                                <th>Açıklama</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($keysData['keys'])): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-info-circle me-2"></i>Henüz key bulunmamaktadır.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($keysData['keys'] as $index => $key): ?>
                                    <?php
                                    $isExpired = isset($key['expiresAt']) && strtotime($key['expiresAt']) < time();
                                    $isActive = isset($key['isActive']) && $key['isActive'] && !$isExpired;
                                    $currentUsage = isset($key['currentUsage']) ? $key['currentUsage'] : 0;
                                    $maxUsage = isset($key['maxUsage']) ? $key['maxUsage'] : 1;
                                    $isUsageExceeded = $currentUsage >= $maxUsage;
                                    ?>
                                    <tr>
                                        <td class="text-break"><?php echo htmlspecialchars($key['key'] ?? ''); ?></td>
                                        <td>
                                            <?php if ($isExpired): ?>
                                                <span class="badge status-expired">Süresi Dolmuş</span>
                                            <?php elseif ($isUsageExceeded): ?>
                                                <span class="badge status-inactive">Kullanım Dolmuş</span>
                                            <?php elseif ($isActive): ?>
                                                <span class="badge status-active">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge status-inactive">Pasif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $currentUsage; ?> / <?php echo $maxUsage; ?></td>
                                        <td><?php echo isset($key['expiresAt']) ? date('d.m.Y H:i', strtotime($key['expiresAt'])) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($key['description'] ?? '-'); ?></td>
                                        <td><?php echo isset($key['createdAt']) ? date('d.m.Y H:i', strtotime($key['createdAt'])) : '-'; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <form action="admin-actions.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="toggle_key">
                                                    <input type="hidden" name="key_index" value="<?php echo $index; ?>">
                                                    <button type="submit" class="btn <?php echo $isActive ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo $isActive ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                        <i class="fas fa-<?php echo $isActive ? 'pause' : 'play'; ?>"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="editKey(<?php echo $index; ?>)" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="admin-actions.php" method="POST" style="display: inline;" onsubmit="return confirm('Bu key\'i silmek istediğinizden emin misiniz?');">
                                                    <input type="hidden" name="action" value="delete_key">
                                                    <input type="hidden" name="key_index" value="<?php echo $index; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editKeyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Key Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="admin-actions.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_key">
                        <input type="hidden" name="key_index" id="editKeyIndex">
                        
                        <div class="mb-3">
                            <label for="editKeyValue" class="form-label">Key Değeri</label>
                            <input type="text" class="form-control" id="editKeyValue" name="keyValue" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editExpiresAt" class="form-label">Bitiş Tarihi</label>
                            <input type="datetime-local" class="form-control" id="editExpiresAt" name="expiresAt" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editMaxUsage" class="form-label">Maksimum Kullanım</label>
                                <input type="number" class="form-control" id="editMaxUsage" name="maxUsage" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editCurrentUsage" class="form-label">Mevcut Kullanım</label>
                                <input type="number" class="form-control" id="editCurrentUsage" name="currentUsage" min="0" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" id="editDescription" name="description">
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="editIsActive" name="isActive">
                            <label class="form-check-label" for="editIsActive">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editKey(index) {
            const keysData = <?php echo json_encode($keysData['keys']); ?>;
            const key = keysData[index];
            
            document.getElementById('editKeyIndex').value = index;
            document.getElementById('editKeyValue').value = key.key || '';
            document.getElementById('editExpiresAt').value = key.expiresAt ? new Date(key.expiresAt).toISOString().slice(0, 16) : '';
            document.getElementById('editMaxUsage').value = key.maxUsage || 1;
            document.getElementById('editCurrentUsage').value = key.currentUsage || 0;
            document.getElementById('editDescription').value = key.description || '';
            document.getElementById('editIsActive').checked = key.isActive || false;
            
            const modal = new bootstrap.Modal(document.getElementById('editKeyModal'));
            modal.show();
        }
        
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('show')) {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }
            });
        }, 5000);
    </script>
</body>
</html>