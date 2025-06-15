<?php
require_once 'config.php';

// Eğer kullanıcı giriş yapmışsa panel'e yönlendir
if (isset($_SESSION['kullanici_id'])) {
    header('Location: panel.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hayvanat Bahçesi Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-card {
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-paw me-2"></i>
                Hayvanat Bahçesi Yönetim Sistemi
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="giris.php">
                    <i class="fas fa-sign-in-alt me-1"></i>Giriş Yap
                </a>
                <a class="nav-link" href="kayit.php">
                    <i class="fas fa-user-plus me-1"></i>Kayıt Ol
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-hippo me-3"></i>
                Hayvanat Bahçesi Yönetim Sistemi
            </h1>
            <p class="lead mb-5">Hayvanlarınızın sağlığını, beslenmesini ve yaşam alanlarını kolayca takip edin</p>
            <a href="giris.php" class="btn btn-light btn-lg me-3">
                <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
            </a>
            <a href="kayit.php" class="btn btn-outline-light btn-lg">
                <i class="fas fa-user-plus me-2"></i>Kayıt Ol
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Sistem Özellikleri</h2>
                <p class="text-muted">Hayvanat bahçenizi profesyonelce yönetin</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-heart text-danger fa-3x mb-3"></i>
                            <h5 class="card-title">Sağlık Takibi</h5>
                            <p class="card-text">Hayvanlarınızın sağlık durumlarını detaylı bir şekilde takip edin</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils text-warning fa-3x mb-3"></i>
                            <h5 class="card-title">Beslenme Planları</h5>
                            <p class="card-text">Her hayvan için özel beslenme programları oluşturun ve takip edin</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow">
                        <div class="card-body text-center">
                            <i class="fas fa-home text-success fa-3x mb-3"></i>
                            <h5 class="card-title">Yaşam Alanları</h5>
                            <p class="card-text">Hayvanların yaşam alanlarını ve habitat bilgilerini yönetin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 Hayvanat Bahçesi Yönetim Sistemi. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 