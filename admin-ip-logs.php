<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$logsFile = 'users/ip_logs.json';
$logsData = [];
if (file_exists($logsFile)) {
    $content = file_get_contents($logsFile);
    if ($content !== false) {
        $logsData = json_decode($content, true);
    }
}

if (!isset($logsData['logs'])) {
    $logsData['logs'] = [];
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 50;
$totalLogs = count($logsData['logs']);
$totalPages = ceil($totalLogs / $perPage);
$offset = ($page - 1) * $perPage;

$logs = array_reverse(array_slice($logsData['logs'], $offset, $perPage));
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Logları - Api Service Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #059669;
            --danger: #dc2626;
            --warning: #d97706;
            --dark: #0f172a;
            --darker: #020617;
            --light: #f8fafc;
            --border: #334155;
            --surface: #1e293b;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
        }
        
        body { 
            background: linear-gradient(135deg, var(--dark) 0%, var(--darker) 100%);
            color: var(--text); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
        }
        
        .navbar { 
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        
        .card { 
            background: var(--surface); 
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .table-dark { 
            background: var(--surface);
            border-radius: 12px;
        }
        
        .status-success { 
            background: var(--success);
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        
        .status-error { 
            background: var(--danger);
            color: white;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 0.85rem;
        }
        
        .pagination .page-link { 
            background: var(--surface); 
            border-color: var(--border); 
            color: var(--text);
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .pagination .page-link:hover { 
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .pagination .active .page-link { 
            background: var(--primary); 
            border-color: var(--primary);
        }
        
        .card-header {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(29, 78, 216, 0.1));
            border-bottom: 1px solid var(--border);
            border-radius: 16px 16px 0 0;
            padding: 1.5rem;
        }
        
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="adminX9279N725E.php">
                <i class="fas fa-shield-alt me-2"></i>Api Service Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="adminX9279N725E.php">
                    <i class="fas fa-arrow-left me-2"></i>Geri Dön
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>IP Erişim Logları
                            <span class="badge bg-primary ms-2"><?php echo $totalLogs; ?> Toplam</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>Tarih/Saat</th>
                                        <th>Key</th>
                                        <th>IP Adresi</th>
                                        <th>Durum</th>
                                        <th>User Agent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-info-circle me-2"></i>Henüz log kaydı bulunmamaktadır.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?php echo date('d.m.Y H:i:s', strtotime($log['timestamp'])); ?></td>
                                                <td class="text-break" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($log['key'], 0, 20) . '...'); ?>
                                                </td>
                                                <td>
                                                    <code><?php echo htmlspecialchars($log['ip']); ?></code>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = $log['status'] === 'success' ? 'status-success' : 'status-error';
                                                    $statusText = $log['status'] === 'success' ? 'Başarılı' : 'Hata';
                                                    ?>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td class="text-break" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars(substr($log['user_agent'], 0, 50) . '...'); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Sayfa navigasyonu">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">Önceki</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sonraki</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>