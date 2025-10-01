<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Api Service - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(-45deg, #2563eb, #7c3aed, #059669, #0ea5e9);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 55px rgba(0, 0, 0, 0.2);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .logo-icon i {
            font-size: 35px;
            color: white;
        }

        .brand-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .brand-subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .premium-badge {
            display: inline-block;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(5, 150, 105, 0.3);
        }

        .premium-badge i {
            margin-right: 5px;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            padding: 18px 25px;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
            background: white;
        }

        .input-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border: none;
            border-radius: 15px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(37, 99, 235, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: none;
            font-weight: 500;
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message i {
            margin-right: 10px;
        }

        .features {
            margin-top: 30px;
            text-align: center;
        }

        .feature-item {
            display: inline-block;
            margin: 0 15px;
            color: #666;
            font-size: 13px;
        }

        .feature-item i {
            color: #2563eb;
            margin-right: 5px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #2563eb;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-card {
                margin: 10px;
                padding: 30px 25px;
            }

            .brand-title {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <div class="background-animation"></div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="brand-title">Api<span style="color:#2563eb">Service</span></h1>
                <p class="brand-subtitle">by instinticxd & farkediyo</p>
                <div class="premium-badge">
                    <i class="fas fa-crown"></i>
                    Best Service
                </div>
            </div>

            <div class="error-message" id="errorMsg">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="errorText"></span>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <input type="text" class="form-control" id="keyInput" placeholder="Premium Key Girin" autocomplete="off" required>
                    <i class="fas fa-key input-icon"></i>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Login
                </button>
            </form>

            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-lock"></i>
                    Ücretli
                </div>
                <div class="feature-item">
                    <i class="fas fa-tachometer-alt"></i>
                    Best
                </div>
                <div class="feature-item">
                    <i class="fas fa-shield-check"></i>
                    Hızlı
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            checkKey();
        });

        document.getElementById('keyInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkKey();
            }
        });

        function checkKey() {
            const key = document.getElementById('keyInput').value.trim();
            const errorMsg = document.getElementById('errorMsg');
            const errorText = document.getElementById('errorText');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const loginBtn = document.getElementById('loginBtn');

            if (!key) {
                showError('Lütfen premium keyinizi girin');
                return;
            }

            loadingOverlay.style.display = 'flex';
            loginBtn.disabled = true;

            fetch(`keydogrulama.php?key=${encodeURIComponent(key)}`)
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none';
                    loginBtn.disabled = false;

                    if (data.status === 'success') {
                        localStorage.setItem('kerexa_key', key);
                        localStorage.setItem('kerexa_user', data.owner);
                        localStorage.setItem('kerexa_ip', data.ip);

                        loginBtn.innerHTML = '<i class="fas fa-check me-2"></i>Başarılı!';
                        loginBtn.style.background = 'linear-gradient(135deg, #059669, #10b981)';

                        setTimeout(() => {
                            window.location.href = 'checker.php';
                        }, 1500);
                    } else {
                        if (data.message.includes('IP sınırı aşıldı') || data.message.includes('Device limit exceeded')) {
                            showError('🚫 Cihaz Limiti Aşıldı! Bu anahtar maksimum cihaz sayısına ulaştı.');
                        } else {
                            showError(data.message || 'Bilinmeyen bir hata oluştu');
                        }
                    }
                })
                .catch(error => {
                    loadingOverlay.style.display = 'none';
                    loginBtn.disabled = false;
                    showError('Sunucu bağlantı hatası. Lütfen tekrar deneyin.');
                    console.error('Error:', error);
                });
        }

        function showError(message) {
            const errorMsg = document.getElementById('errorMsg');
            const errorText = document.getElementById('errorText');

            errorText.textContent = message;
            errorMsg.style.display = 'block';

            setTimeout(() => {
                errorMsg.style.display = 'none';
            }, 5000);
        }

        document.getElementById('keyInput').addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });

        document.getElementById('keyInput').addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
        });
    </script>
</body>
</html>