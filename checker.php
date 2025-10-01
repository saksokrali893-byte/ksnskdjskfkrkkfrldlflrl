<?php if(!isset($_SERVER['HTTP_REFERER'])){header('Location: index.php');exit;} ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Api Service Team">
    <title>Api Service | Best Checker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes dots {
            0%, 20% { color: rgba(0,0,0,0); text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            40% { color: black; text-shadow: .25em 0 0 rgba(0,0,0,0), .5em 0 0 rgba(0,0,0,0); }
            60% { text-shadow: .25em 0 0 black, .5em 0 0 rgba(0,0,0,0); }
            80%, 100% { text-shadow: .25em 0 0 black, .5em 0 0 black; }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0ea5e9 0%, #10b981 100%);
            background-size: 200% 200%;
            animation: gradientMove 8s ease infinite;
            min-height: 100vh;
            color: #2d3748;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        #sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding-top: 1rem;
            overflow-y: auto;
        }

        #sidebar.active {
            left: 0;
        }

        .content {
            flex-grow: 1;
            padding: 1.5rem 0;
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .sidebar-header {
            padding: 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .sidebar-header .brand {
            font-size: 1.8rem;
        }

        .sidebar-list a {
            padding: 10px 15px;
            color: #4a5568;
            font-weight: 500;
            display: block;
            transition: all 0.2s ease;
            text-decoration: none;
            border-left: 5px solid transparent;
        }

        .sidebar-list a:hover {
            color: #0ea5e9;
            background: rgba(14, 165, 233, 0.05);
        }

        .sidebar-list a.active {
            color: #0ea5e9;
            background: rgba(14, 165, 233, 0.1);
            border-left: 5px solid #0ea5e9;
        }

        #sidebar-toggle {
            position: fixed;
            top: 1.5rem;
            left: 1.5rem;
            z-index: 1030;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #sidebar-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }

        .page-content {
            display: none;
            padding: 0 15px;
        }

        .page-content.active {
            display: block;
        }

        .main-container .header {
            margin-left: 0 !important;
        }

        .main-container {
            min-height: 100vh;
            padding: 1.5rem 0;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand {
            font-size: 1.6rem;
            font-weight: 700;
            background: linear-gradient(45deg, #0ea5e9, #10b981);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .user-info {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 50px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .card-header-modern {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(16, 185, 129, 0.1));
            border-bottom: 1px solid rgba(14, 165, 233, 0.1);
            padding: 1rem;
            font-weight: 600;
            color: #0ea5e9;
        }

        .gateway-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .gateway-btn {
            background: rgba(255, 255, 255, 0.8);
            border: 2px solid rgba(14, 165, 233, 0.2);
            border-radius: 12px;
            padding: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .gateway-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
            border-color: #0ea5e9;
        }

        .gateway-btn.active {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            border-color: transparent;
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }

        .gateway-btn i {
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .form-control-modern {
            border: 2px solid rgba(14, 165, 233, 0.2);
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
        }

        .btn-modern {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            font-weight: 500;
            opacity: 0.8;
        }
        
        .card-modern .p-3:not(.card-carousel-container) {
            padding: 1rem !important;
        }
        
        .card-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .form-control-modern:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            outline: none;
            background: rgba(255, 255, 255, 0.95);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: white;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        .btn-danger-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 101, 101, 0.4);
        }

        .results-container {
            max-height: 500px;
            overflow-y: auto;
            background: rgba(247, 250, 252, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1rem;
        }

        .results-container::-webkit-scrollbar {
            width: 8px;
        }

        .results-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .results-container::-webkit-scrollbar-thumb {
            background: #0ea5e9;
            border-radius: 4px;
        }

        .result-item {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .result-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .result-approved {
            border-left: 4px solid #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
        }

        .result-declined {
            border-left: 4px solid #f56565;
            background: linear-gradient(135deg, rgba(245, 101, 101, 0.05), rgba(245, 101, 101, 0.02));
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
            animation: pulse 2s infinite;
        }

        .status-live { 
            background: #10b981; 
        }
        
        .status-processing { 
            background: #f6ad55; 
        }
        
        .status-checking {
            background: linear-gradient(45deg, #0ea5e9, #10b981);
            animation: spin 1s linear infinite;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(5, end) infinite;
        }

        .floating-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #0d9488);
            color: white;
            border: none;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.4);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(16, 185, 129, 0.6);
        }
        
        .floating-status {
            position: fixed;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 1000;
            min-width: 250px;
            transition: all 0.3s ease;
        }

        .floating-status:hover {
            transform: translateY(-50%) scale(1.05);
        }

        .status-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .status-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .status-info {
            flex: 1;
        }

        .status-title {
            font-weight: 600;
            color: #0ea5e9;
            font-size: 0.9rem;
        }

        .status-detail {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }

        .progress-ring {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: conic-gradient(#0ea5e9 0deg, #e2e8f0 0deg);
            position: relative;
            transition: all 0.3s ease;
        }

        .progress-ring::before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            background: white;
            border-radius: 50%;
        }

        .toast-container {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1050;
        }

        .toast-modern {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
        }

        .bin-info-card {
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        @media (min-width: 992px) {
            #sidebar {
                left: 0;
            }
            .content {
                margin-left: 250px;
            }
            .main-container {
                padding: 1.5rem;
            }
            #sidebar-toggle {
                display: none;
            }
            .header {
                border-top-left-radius: 0;
                border-bottom-left-radius: 0;
            }
        }

        @media (max-width: 991px) {
            .content.sidebar-open {
                margin-left: 250px;
            }
            #sidebar-toggle {
                left: 1rem;
                top: 1rem;
            }
            .header {
                margin-top: 3.5rem;
            }
            .floating-status {
                right: 1rem;
                left: 1rem;
                top: auto;
                bottom: 6rem;
                transform: none;
            }
            .floating-status:hover {
                transform: none;
            }
        }

        @media (min-width: 1200px) {
            .floating-btn {
                width: 60px;
                height: 60px;
            }

            .floating-status {
                right: 3rem;
                min-width: 300px;
            }
            
            .gateway-grid {
                grid-template-columns: repeat(5, 1fr);
            }

            .row.g-4 {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-12.col-lg-6 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }
        }

        @media (max-width: 768px) {
            .gateway-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stat-number {
                font-size: 2rem;
            }

            .floating-btn {
                width: 50px;
                height: 50px;
                bottom: 1rem;
                right: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <i class="fas fa-rocket me-2"></i>Api<span style="color: #10b981;">Panel</span>
                </div>
            </div>

            <ul class="list-unstyled sidebar-list">
                <li>
                    <a href="#" data-page="checker" class="active"><i class="fas fa-bolt me-2"></i> Checker</a>
                </li>
                <li>
                    <a href="#" data-page="bin"><i class="fas fa-info-circle me-2"></i> Bin Sorgu</a>
                </li>
                <li>
                    <a href="#" data-page="gen"><i class="fas fa-seedling me-2"></i> Kart Üretici (Gen)</a>
                </li>
            </ul>
        </nav>

        <div class="content" id="main-content">
            <button id="sidebar-toggle" class="btn"><i class="fas fa-bars"></i></button>

            <div class="container main-container">
                <div class="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="brand">
                            <i class="fas fa-rocket me-2"></i>Api<span style="color: #10b981;">Service</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="user-info">
                                <i class="fas fa-crown me-2"></i>
                                <span id="username-display">Kullanıcı</span>
                            </div>
                            <button class="btn btn-outline-danger btn-sm" id="logout-btn">
                                <i class="fas fa-power-off me-1"></i>Çıkış
                            </button>
                        </div>
                    </div>
                </div>

                <div id="checker-page" class="page-content active">
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="card-modern mb-4">
                                <div class="card-header-modern">
                                    <i class="fas fa-network-wired me-2"></i>Gateways
                                </div>
                                <div class="p-3"> 
                                    <div class="gateway-grid">
                                        <div class="gateway-btn active" data-gateway="paypal">
                                            <i class="fab fa-paypal"></i>
                                            <span>PayPal</span>
                                        </div>
                                        <div class="gateway-btn" data-gateway="troy">
                                            <i class="fas fa-credit-card"></i>
                                            <span>Troy</span>
                                        </div>
                                        <div class="gateway-btn" data-gateway="tumkart2">
                                            <i class="fas fa-credit-card"></i>
                                            <span>MaxiPuan</span>
                                        </div>
                                        <div class="gateway-btn" data-gateway="vbv">
                                            <i class="fas fa-shield-alt"></i>
                                            <span>B3 VBV</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <i class="fas fa-bolt me-2"></i>Checker
                                </div>
                                <div class="p-3"> 
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-list-alt me-2"></i>Kart Listesi
                                        </label>
                                        <textarea class="form-control-modern" id="card-list" rows="8" 
                                            placeholder="4532123412341234|12|2025|123&#10;5555444433332222|01|2026|456&#10;&#10;Her satıra bir kart (kart|ay|yıl|cvv)"></textarea>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary-modern btn-modern" id="start-btn">
                                            <i class="fas fa-magic me-2"></i>Start 
                                        </button>
                                        <button class="btn btn-danger-modern btn-modern" id="stop-btn" disabled>
                                            <i class="fas fa-hand me-2"></i>Stop 
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="floating-status" id="floating-status" style="display: none;">
                                <div class="status-content">
                                    <div class="status-icon">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div class="status-info">
                                        <div class="status-title" id="floating-status-title">İşlem Durumu</div>
                                        <div class="status-detail" id="floating-status-detail">Hazır</div>
                                    </div>
                                    <div class="status-progress">
                                        <div class="progress-ring">
                                            <div class="progress-fill" id="progress-fill"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card-modern mb-4">
                                <div class="card-header-modern d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-terminal me-2"></i>Sonuçlar
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-sm filter-btn active" data-filter="all">
                                            <i class="fas fa-globe me-1"></i>Tümü
                                        </button>
                                        <button class="btn btn-outline-success btn-sm filter-btn" data-filter="approved">
                                            <i class="fas fa-heart me-1"></i>Live
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm filter-btn" data-filter="declined">
                                            <i class="fas fa-skull me-1"></i>Dead
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm" id="clear-btn">
                                            <i class="fas fa-broom"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-0">
                                    <div class="results-container" id="results">
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-search mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5>Henüz sonuç yok</h5>
                                            <p class="mb-0">Kartları kontrol etmeye başlayın</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <i class="fas fa-fire me-2"></i>İstatistikler
                                </div>
                                <div class="p-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <div class="stat-number" id="total-stat">0</div>
                                                <div class="stat-label">Toplam</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <div class="stat-number text-success" id="live-stat">0</div>
                                                <div class="stat-label">Live</div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <div class="stat-number text-danger" id="dead-stat">0</div>
                                                <div class="stat-label">Dead</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="bin-page" class="page-content">
                    <div class="row g-4 justify-content-center">
                        <div class="col-12 col-lg-8">
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <i class="fas fa-search-dollar me-2"></i>BIN Sorgulama
                                </div>
                                <div class="p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-hashtag me-2"></i>BIN Numarası (3-11 hane)
                                        </label>
                                        <input type="number" class="form-control-modern" id="bin-input" placeholder="BIN numarasını girin (örn: 453212)">
                                    </div>
                                    <div class="d-grid gap-2 mb-4">
                                        <button class="btn btn-primary-modern btn-modern" id="bin-check-btn">
                                            <i class="fas fa-search me-2"></i>Sorgula
                                        </button>
                                    </div>
                                    <div class="card-modern p-3 bin-info-card" id="bin-info-display">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-credit-card mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                            <h5>Sorgulama yapın</h5>
                                            <p class="mb-0">BIN bilgileri burada görüntülenecektir.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="gen-page" class="page-content">
                    <div class="row g-4">
                        <div class="col-12 col-lg-6">
                            <div class="card-modern">
                                <div class="card-header-modern">
                                    <i class="fas fa-magic me-2"></i>Kart Üretici (Gen)
                                </div>
                                <div class="p-3">
                                    <div class="row mb-3">
                                        <div class="col-12 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-credit-card me-2"></i>BIN Maskesi (Örn: 453212xxxxxxxxxx)
                                            </label>
                                            <input type="text" class="form-control-modern" id="gen-bin" placeholder="BIN girin (3-16 hane, x ile joker)">
                                            <small class="text-muted">Boş bırakılırsa rastgele üretilir.</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-calendar-alt me-2"></i>Ay (MM)
                                            </label>
                                            <input type="number" min="1" max="12" class="form-control-modern" id="gen-month" placeholder="Ay (örn: 12)">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-calendar-check me-2"></i>Yıl (YYYY)
                                            </label>
                                            <input type="number" min="2025" max="2040" class="form-control-modern" id="gen-year" placeholder="Yıl (örn: 2026)">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-lock me-2"></i>CVV
                                            </label>
                                            <input type="number" min="100" max="999" class="form-control-modern" id="gen-cvv" placeholder="CVV (örn: 123)">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-sort-numeric-up me-2"></i>Adet
                                            </label>
                                            <input type="number" min="1" max="100" value="10" class="form-control-modern" id="gen-count" placeholder="Adet">
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary-modern btn-modern" id="generate-btn">
                                            <i class="fas fa-wand-magic-sparkles me-2"></i>Kartları Üret
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="card-modern">
                                <div class="card-header-modern d-flex justify-content-between align-items-center">
                                    <div><i class="fas fa-credit-card me-2"></i>Üretilen Kartlar</div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-success btn-sm" id="copy-gen-btn" disabled>
                                            <i class="fas fa-copy me-1"></i>Kopyala
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" id="download-gen-btn" disabled>
                                            <i class="fas fa-download me-1"></i>.txt İndir
                                        </button>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <textarea class="form-control-modern" id="gen-output" rows="10" readonly 
                                        placeholder="Üretilen kartlar burada görünecektir. (kart|ay|yıl|cvv)"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="floating-btn" id="copy-live-btn" title="Tüm Live Kartları Kopyala">
        <i class="fas fa-copy"></i>
    </button>

    <div class="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        class ModernChecker {
            constructor() {
                this.currentGateway = 'paypal';
                this.isRunning = false;
                this.queue = [];
                this.processedCards = [];
                this.allCards = [];
                this.stats = { total: 0, live: 0, dead: 0 };
                this.currentFilter = 'all';
                this.checkProgress = 0;
                this.keyCheckInterval = null;
                this.init();
            }

            init() {
                this.loadUserData();
                this.bindEvents();
                this.keyCheckInterval = setInterval(this.checkKeyStatus.bind(this), 15 * 1000); 
                this.setupAntiTampering();
                this.showPage('checker'); 
                if (window.innerWidth < 992) {
                    document.getElementById('sidebar').classList.remove('active');
                }
            }

            loadUserData() {
                const userKey = localStorage.getItem('kerexa_key');
                const userName = localStorage.getItem('kerexa_user');

                if (!userKey) {
                    window.location.href = 'index.php';
                    return;
                }

                document.getElementById('username-display').textContent = userName || 'Kullanıcı';
            }
            
            async checkKeyStatus() {
                const userKey = localStorage.getItem('kerexa_key');
                if (!userKey) {
                    this.showToast('Anahtar bulunamadı. Lütfen tekrar giriş yapın.', 'danger');
                    this.logout();
                    return;
                }

                try {
                    const response = await fetch(`keydogrulama.php?key=${encodeURIComponent(userKey)}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.status !== 'success') {
                        this.showToast(data.message || 'API anahtarınız geçersiz veya süresi dolmuş. Lütfen tekrar giriş yapın.', 'danger');
                        this.logout();
                    }
                } catch (error) {
                    console.error('API anahtarı durumu kontrol edilirken hata oluştu:', error);
                }
            }

            setupAntiTampering() {
                document.addEventListener('contextmenu', (e) => {
                    e.preventDefault();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'F12') {
                        e.preventDefault();
                    }
                    if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i')) {
                        e.preventDefault();
                    }
                    if (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j')) {
                        e.preventDefault();
                    }
                    if (e.ctrlKey && (e.key === 'U' || e.key === 'u')) {
                        e.preventDefault();
                    }
                });
            }

            bindEvents() {
                document.getElementById('sidebar-toggle').addEventListener('click', () => {
                    document.getElementById('sidebar').classList.toggle('active');
                    if (window.innerWidth < 992) {
                        document.getElementById('main-content').classList.toggle('sidebar-open');
                    }
                });

                document.querySelectorAll('.sidebar-list a').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const page = e.currentTarget.dataset.page;
                        this.showPage(page);
                        if (window.innerWidth < 992) {
                            document.getElementById('sidebar').classList.remove('active');
                            document.getElementById('main-content').classList.remove('sidebar-open');
                        }
                    });
                });

                document.querySelectorAll('.gateway-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        document.querySelectorAll('.gateway-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.currentGateway = btn.dataset.gateway;
                    });
                });

                document.getElementById('start-btn').addEventListener('click', () => this.startChecking());
                document.getElementById('stop-btn').addEventListener('click', () => this.stopChecking());
                document.getElementById('clear-btn').addEventListener('click', () => {
                    this.clearResults();
                });
                document.getElementById('copy-live-btn').addEventListener('click', () => {
                    this.copyLiveCards();
                });
                document.getElementById('logout-btn').addEventListener('click', () => this.logout());

                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        this.currentFilter = btn.dataset.filter;
                        this.applyFilter();
                    });
                });
                
                document.getElementById('bin-check-btn').addEventListener('click', () => this.checkBin());
                document.getElementById('bin-input').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.checkBin();
                });

                document.getElementById('generate-btn').addEventListener('click', () => this.generateCards());
                document.getElementById('copy-gen-btn').addEventListener('click', () => this.copyToClipboard(document.getElementById('gen-output').value, 'Üretilen kartlar kopyalandı!'));
                document.getElementById('download-gen-btn').addEventListener('click', () => this.downloadText('gen-output', 'generated_cards.txt'));

                document.addEventListener('keydown', (e) => {
                    if (this.currentPage === 'checker') {
                        if (e.ctrlKey && e.key === 'Enter') {
                            e.preventDefault();
                            if (!this.isRunning) this.startChecking();
                        }
    
                        if (e.key === 'Escape') {
                            e.preventDefault();
                            if (this.isRunning) this.stopChecking();
                        }
    
                        if (e.ctrlKey && e.key === 'k') {
                            e.preventDefault();
                            this.clearResults();
                        }
                    }
                });
            }
            
            showPage(page) {
                this.currentPage = page;
                document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
                document.getElementById(`${page}-page`).classList.add('active');

                document.querySelectorAll('.sidebar-list a').forEach(link => link.classList.remove('active'));
                document.querySelector(`[data-page="${page}"]`).classList.add('active');

                document.getElementById('copy-live-btn').style.display = (page === 'checker' ? 'flex' : 'none');
                document.getElementById('floating-status').style.display = (page === 'checker' && this.isRunning ? 'block' : 'none');
            }

            async startChecking() {
                const userKey = localStorage.getItem('kerexa_key');
                if (!userKey) {
                    this.showToast('API anahtarı bulunamadı! Lütfen tekrar giriş yapın.', 'danger');
                    this.logout();
                    return;
                }

                const cardList = document.getElementById('card-list').value.trim();
                if (!cardList) {
                    this.showToast('Lütfen kart listesi girin!', 'warning');
                    return;
                }

                const uniqueCards = [...new Set(cardList.split('\n'))]
                                    .filter(line => line.trim() && line.includes('|'))
                                    .map(card => card.trim());

                if (uniqueCards.length === 0) {
                    this.showToast('Geçerli kart formatı bulunamadı!', 'danger');
                    return;
                }

                this.allCards = uniqueCards;
                this.queue = [...this.allCards];
                this.processedCards = [];
                this.currentCardIndex = 0;
                this.isRunning = true;
                this.stats = { total: 0, live: 0, dead: 0 };
                this.updateStats();

                document.getElementById('start-btn').disabled = true;
                document.getElementById('stop-btn').disabled = false;

                this.showFloatingStatus();
                this.updateFloatingStatus('İşlem başlatılıyor...', 'processing', 0);
                this.clearResults(false);
                this.processQueue();
            }

            async processQueue() {
                if (!this.isRunning || this.queue.length === 0) {
                    this.stopChecking();
                    return;
                }

                const card = this.queue.shift();

                this.checkProgress = ((this.allCards.length - this.queue.length) / this.allCards.length) * 100;
                this.updateFloatingStatus(
                    'Kart Kontrol Ediliyor',
                    `${this.allCards.length - this.queue.length} / ${this.allCards.length} tamamlandı`,
                    this.checkProgress
                );

                await this.checkCard(card);
                this.processedCards.push(card);

                if (this.isRunning) {
                    setTimeout(() => {
                        this.processQueue();
                    }, 1000);
                }
            }

            async checkCard(card) {
                try {
                    const userKey = localStorage.getItem('kerexa_key');
                    if (!userKey) {
                        this.showToast('API anahtarı bulunamadı!', 'danger');
                        this.stopChecking();
                        return;
                    }

                    const response = await fetch(`utils/${this.currentGateway}.php?key=${encodeURIComponent(userKey)}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `card=${encodeURIComponent(card)}&key=${encodeURIComponent(userKey)}`
                    });

                    const result = await response.text();
                    const isLive = result.includes('✅') || result.includes('Approved') || result.includes('Live') || result.includes('succeeded');

                    this.addResult(card, result, isLive);
                    this.updateStats(isLive);

                } catch (error) {
                    console.error("checkCard error:", error);
                    const errorMsg = `❌ Declined | ${card} - Bağlantı hatası`;
                    this.addResult(card, errorMsg, false);
                    this.updateStats(false);
                }
            }

            addResult(card, result, isLive) {
                const resultsContainer = document.getElementById('results');

                const emptyMessage = resultsContainer.querySelector('.text-center.text-muted');
                if (emptyMessage && emptyMessage.textContent.includes('Henüz sonuç yok')) {
                    resultsContainer.innerHTML = '';
                }

                const resultDiv = document.createElement('div');
                resultDiv.className = `result-item ${isLive ? 'result-approved' : 'result-declined'}`;
                resultDiv.dataset.status = isLive ? 'approved' : 'declined';

                const resultText = result.length > 100 ? result.substring(0, 100) + '...' : result;

                resultDiv.innerHTML = `
                    <div class="d-flex align-items-center flex-grow-1">
                        <i class="fas ${isLive ? 'fa-heart text-success' : 'fa-skull text-danger'} me-2"></i>
                        <code class="flex-grow-1" title="${result}">${resultText}</code>
                        <small class="text-muted ms-2">${new Date().toLocaleTimeString('tr-TR')}</small>
                    </div>
                    <button class="btn btn-outline-${isLive ? 'success' : 'danger'} btn-sm ms-2 copy-card-btn" data-card-full="${encodeURIComponent(result)}">
                        <i class="fas fa-clipboard"></i>
                    </button>
                `;

                resultsContainer.insertBefore(resultDiv, resultsContainer.firstChild);

                resultDiv.querySelector('.copy-card-btn').addEventListener('click', (e) => {
                    const button = e.target.closest('button');
                    const contentToCopy = decodeURIComponent(button.dataset.cardFull);
                    this.copyToClipboard(contentToCopy, 'Sonuç kopyalandı!');
                });

                this.applyFilter();
                resultsContainer.scrollTop = 0;
            }

            updateStats(isLive) {
                if (isLive !== undefined) {
                    this.stats.total++;
                    if (isLive) this.stats.live++;
                    else this.stats.dead++;
                }

                document.getElementById('total-stat').textContent = this.stats.total;
                document.getElementById('live-stat').textContent = this.stats.live;
                document.getElementById('dead-stat').textContent = this.stats.dead;
            }

            stopChecking() {
                this.isRunning = false;
                this.queue = [];
                document.getElementById('start-btn').disabled = false;
                document.getElementById('stop-btn').disabled = true;

                this.updateFloatingStatus('İşlem Tamamlandı', `Toplam: ${this.stats.total}, Live: ${this.stats.live}`, 100);

                setTimeout(() => {
                    this.hideFloatingStatus();
                }, 3000);
            }

            showFloatingStatus() {
                document.getElementById('floating-status').style.display = 'block';
            }

            hideFloatingStatus() {
                document.getElementById('floating-status').style.display = 'none';
            }

            updateFloatingStatus(title, detail, progress = 0) {
                document.getElementById('floating-status-title').textContent = title;
                document.getElementById('floating-status-detail').textContent = detail;

                const progressFill = document.getElementById('progress-fill');
                const progressRing = progressFill.parentElement;

                const degrees = (progress / 100) * 360;
                progressRing.style.background = `conic-gradient(#0ea5e9 ${degrees}deg, #e2e8f0 ${degrees}deg)`;
            }

            clearResults(resetStats = true) {
                document.getElementById('results').innerHTML = `
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-search mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h5>Henüz sonuç yok</h5>
                        <p class="mb-0">Kartları kontrol etmeye başlayın</p>
                    </div>
                `;
                if (resetStats) {
                    this.stats = { total: 0, live: 0, dead: 0 };
                    this.updateStats();
                }
            }

            applyFilter() {
                const results = document.querySelectorAll('.result-item');
                results.forEach(result => {
                    const status = result.dataset.status;
                    const show = this.currentFilter === 'all' || this.currentFilter === status;
                    result.style.display = show ? 'flex' : 'none';
                });
            }

            copyLiveCards() {
                const liveCards = [];
                document.querySelectorAll('.result-item[data-status="approved"] .copy-card-btn').forEach(btn => {
                    const fullResult = decodeURIComponent(btn.dataset.cardFull);
                    const cardMatch = fullResult.match(/(\d{13,19}\|\d{2}\|\d{4}\|\d{3})/);
                    if (cardMatch) {
                        liveCards.push(cardMatch[0]);
                    }
                });
                
                if (liveCards.length === 0) {
                     document.querySelectorAll('.result-item[data-status="approved"] .copy-card-btn').forEach(btn => {
                        liveCards.push(decodeURIComponent(btn.dataset.cardFull));
                     });
                }
                
                if (liveCards.length === 0) {
                    this.showToast('Live kart bulunamadı!', 'warning');
                    return;
                }

                this.copyToClipboard(liveCards.join('\n'), `${liveCards.length} live kart kopyalandı!`);
            }

            async copyToClipboard(text, successMessage = 'Kopyalandı!') {
                if (navigator.clipboard && window.isSecureContext) {
                    try {
                        await navigator.clipboard.writeText(text);
                        this.showToast(successMessage, 'success');
                        return;
                    } catch (err) {
                        console.error('Kopyalama API hatası:', err);
                    }
                }

                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.top = 0;
                textarea.style.left = 0;
                textarea.style.opacity = 0;

                document.body.appendChild(textarea);
                textarea.select();
                
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        this.showToast(successMessage, 'success');
                    } else {
                        throw new Error('execCommand başarısız');
                    }
                } catch (err) {
                    console.error('Yedek kopyalama hatası:', err);
                    this.showToast('Kopyalama başarısız oldu.', 'danger');
                } finally {
                    document.body.removeChild(textarea);
                }
            }

            showToast(message, type = 'info') {
                const toastContainer = document.querySelector('.toast-container');
                const toast = document.createElement('div');
                toast.className = `toast toast-modern show mb-2`;
                toast.innerHTML = `
                    <div class="toast-body d-flex align-items-center">
                        <i class="fas fa-${this.getToastIcon(type)} text-${type} me-2"></i>
                        ${message}
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;

                toastContainer.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentNode === toastContainer) {
                        toast.remove();
                    }
                }, 5000);
            }

            getToastIcon(type) {
                const icons = {
                    success: 'thumbs-up',
                    danger: 'exclamation-triangle',
                    warning: 'bell',
                    info: 'lightbulb'
                };
                return icons[type] || 'lightbulb';
            }

            logout() {
                localStorage.removeItem('kerexa_key');
                localStorage.removeItem('kerexa_user');
                localStorage.removeItem('kerexa_ip');
                if (this.keyCheckInterval) {
                    clearInterval(this.keyCheckInterval);
                }
                window.location.href = 'index.php';
            }
            
            downloadText(textareaId, filename) {
                const text = document.getElementById(textareaId).value;
                if (!text.trim()) {
                    this.showToast('İndirilecek içerik boş.', 'warning');
                    return;
                }

                const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                this.showToast(`${filename} dosyası indirildi.`, 'success');
            }
            
            async checkBin() {
                const bin = document.getElementById('bin-input').value.trim();
                const display = document.getElementById('bin-info-display');

                if (!bin || bin.length < 3 || bin.length > 11 || !/^\d+$/.test(bin)) {
                    this.showToast('Lütfen geçerli bir BIN (3-11 hane) girin.', 'warning');
                    return;
                }

                display.innerHTML = `
                    <div class="text-center text-primary py-5">
                        <i class="fas fa-spinner fa-spin mb-3" style="font-size: 3rem;"></i>
                        <h5>Sorgulanıyor...</h5>
                    </div>
                `;

                try {
                    const proxyUrl = `bin.php?bin=${bin}`;
                    
                    const response = await fetch(proxyUrl, {
                        method: 'GET',
                    });

                    const data = await response.json();

                    if (data.Status === 'SUCCESS') {
                        let html = `
                            <div class="p-3 text-start w-100">
                                <h5 class="text-center mb-3"><span class="badge bg-success">BIN: ${data.Bin || bin}</span></h5>
                                <ul class="list-unstyled mt-3">
                                    <li><strong>Şema:</strong> ${data.Scheme || 'Bilinmiyor'}</li>
                                    <li><strong>Tip:</strong> ${data.Type || 'Bilinmiyor'}</li>
                                    <li><strong>Veren Kuruluş:</strong> ${data.Issuer || 'Bilinmiyor'}</li>
                                    <li><strong>Kart Katmanı:</strong> ${data.CardTier || 'Bilinmiyor'}</li>
                                    <li><strong>Ülke:</strong> ${data.Country?.Name || 'Bilinmiyor'} (${data.Country?.A2 || '-'})</li>
                                    <li><strong>Luhn:</strong> ${data.Luhn ? 'Geçerli ✅' : 'Geçersiz ❌'}</li>
                                </ul>
                            </div>
                        `;
                        display.innerHTML = html;
                        this.showToast(`BIN ${bin} bilgisi bulundu.`, 'success');
                    } else {
                         display.innerHTML = `
                            <div class="text-center text-danger py-5">
                                <i class="fas fa-exclamation-triangle mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                <h5>Bilgi Bulunamadı</h5>
                                <p class="mb-0">${data.Message || 'BIN bilgisi bulunamadı veya bir hata oluştu.'}</p>
                            </div>
                        `;
                        this.showToast('BIN bilgisi bulunamadı.', 'danger');
                    }

                } catch (error) {
                    console.error("BIN Sorgulama Hatası:", error);
                     display.innerHTML = `
                        <div class="text-center text-danger py-5">
                            <i class="fas fa-server mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                            <h5>Bağlantı Hatası</h5>
                            <p class="mb-0">API'ye bağlanırken bir sorun oluştu. Konsolu kontrol edin.</p>
                        </div>
                    `;
                    this.showToast('BIN sorgulama sırasında bir ağ hatası oluştu.', 'danger');
                }
            }

            generateCards() {
                const binMask = document.getElementById('gen-bin').value.trim();
                const count = parseInt(document.getElementById('gen-count').value) || 10;
                let month = document.getElementById('gen-month').value.padStart(2, '0');
                let year = document.getElementById('gen-year').value;
                let cvv = document.getElementById('gen-cvv').value.padStart(3, '0');
                
                const generatedCards = [];
                
                if (count > 9999999999) {
                    this.showToast('En fazla 9999999999999 kart üretebilirsiniz.', 'warning');
                    return;
                }

                for (let i = 0; i < count; i++) {
                    const cardData = this._generateSingleCard(binMask, month, year, cvv);
                    generatedCards.push(cardData);
                }

                const outputTextarea = document.getElementById('gen-output');
                outputTextarea.value = generatedCards.join('\n');

                if (generatedCards.length > 0) {
                    this.showToast(`${generatedCards.length} adet kart üretildi.`, 'success');
                    document.getElementById('copy-gen-btn').disabled = false;
                    document.getElementById('download-gen-btn').disabled = false;
                } else {
                    this.showToast('Kart üretilemedi. Lütfen BIN formatını kontrol edin.', 'danger');
                    document.getElementById('copy-gen-btn').disabled = true;
                    document.getElementById('download-gen-btn').disabled = false;
                }
            }

            _generateSingleCard(binMask, month, year, cvv) {
                const rand = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
                
                let finalMonth = month.length > 0 && parseInt(month) >= 1 && parseInt(month) <= 12 ? month : rand(1, 12).toString().padStart(2, '0');
                let finalYear = year.length === 4 && parseInt(year) >= 2025 && parseInt(year) <= 2040 ? year : rand(2025, 2030).toString();
                let finalCvv = cvv.length === 3 ? cvv : rand(100, 999).toString().padStart(3, '0');

                if (!binMask) {
                    let cardNum = rand(4, 5).toString();
                    for (let i = 1; i < 15; i++) {
                        cardNum += rand(0, 9).toString();
                    }
                    let baseCard = cardNum + rand(0, 9).toString();

                    return `${baseCard}|${finalMonth}|${finalYear}|${finalCvv}`;
                }

                let cardNumber = '';
                const maskLength = binMask.length;
                let fullCardLength = 16;

                for (let i = 0; i < maskLength; i++) {
                    const char = binMask[i];
                    if (char === 'x') {
                        cardNumber += rand(0, 9).toString();
                    } else if (/\d/.test(char)) {
                        cardNumber += char;
                    }
                }
                
                while (cardNumber.length < fullCardLength) {
                    cardNumber += rand(0, 9).toString();
                }

                let checkDigit = 0;
                let isDouble = true;
                for (let i = cardNumber.length - 2; i >= 0; i--) {
                    let digit = parseInt(cardNumber[i]);
                    if (isDouble) {
                        digit *= 2;
                        if (digit > 9) digit -= 9;
                    }
                    checkDigit += digit;
                    isDouble = !isDouble;
                }
                
                const finalCheckDigit = (10 - (checkDigit % 10)) % 10;
                cardNumber = cardNumber.substring(0, cardNumber.length - 1) + finalCheckDigit.toString();

                return `${cardNumber}|${finalMonth}|${finalYear}|${finalCvv}`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new ModernChecker();
        });
    </script>
</body>
</html>