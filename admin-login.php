<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: adminX9279N725E.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Güvenli olmayan direkt karşılaştırma
    $correct_username = 'admin';
    $correct_password = 'adminknksa';
    
    // Başarısızlığı kesin olarak görmek için:
    // echo "Girdiğiniz Kullanıcı Adı: " . htmlspecialchars($username) . " | Şifre: " . htmlspecialchars($password); 
    // echo " | Beklenen Şifre: " . $correct_password; 

    if ($username === $correct_username && $password === $correct_password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: adminX9279N725E.php');
        exit;
    } else {
        $error = 'Geçersiz kullanıcı adı veya şifre!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - Api Service</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #7c3aed;
            --dark-bg: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.95);
            --border-color: #334155;
            --text-color: #f1f5f9;
            --text-muted: #94a3b8;
            --box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }
        
        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            box-shadow: var(--box-shadow);
            backdrop-filter: blur(20px);
            padding: 2.5rem;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-title {
            color: var(--text-color);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .form-control {
            background: rgba(15, 23, 42, 0.8);
            border: 2px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 14px 18px;
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
            padding: 14px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.4);
        }
        
        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fca5a5;
            border-radius: 12px;
        }
        
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .logo-icon {
            font-size: 3.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 8px rgba(37, 99, 235, 0.3));
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="login-title">Api<span style="color: var(--primary);">Service</span></h1>
                <p class="login-subtitle">Yönetim paneline giriş yapın</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Kullanıcı Adı</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Kullanıcı adınızı girin">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Şifre</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Şifrenizi girin">
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                </button>
            </form>
        </div>
    </div>
</body>
</html>