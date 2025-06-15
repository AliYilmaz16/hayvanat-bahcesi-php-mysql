<?php
require_once 'config.php';

// Hata ve başarı mesajları
$hata = '';
$basari = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $sifre = $_POST['sifre'];
    
    if (empty($kullanici_adi) || empty($sifre)) {
        $hata = 'Lütfen tüm alanları doldurun.';
    } else {
        try {
            // Kullanıcıyı veritabanından bul
            $sorgu = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
            $sorgu->execute([$kullanici_adi, $kullanici_adi]);
            $kullanici = $sorgu->fetch();
            
            if ($kullanici && password_verify($sifre, $kullanici['password'])) {
                // Giriş başarılı, session oluştur
                $_SESSION['kullanici_id'] = $kullanici['id'];
                $_SESSION['kullanici_adi'] = $kullanici['username'];
                $_SESSION['eposta'] = $kullanici['email'];
                
                header('Location: panel.php');
                exit();
            } else {
                $hata = 'Kullanıcı adı/e-posta veya şifre hatalı.';
            }
        } catch (PDOException $e) {
            $hata = 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    }
}

// Eğer kullanıcı zaten giriş yapmışsa panel'e yönlendir
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
    <title>Giriş Yap - Hayvanat Bahçesi Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .giris-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
        }
        .giris-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <div class="giris-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="giris-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-paw text-success fa-3x mb-3"></i>
                            <h2 class="fw-bold">Giriş Yap</h2>
                            <p class="text-muted">Hayvanat Bahçesi Yönetim Sistemi</p>
                        </div>

                        <?php if ($hata): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($hata); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($basari): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($basari); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="kullanici_adi" class="form-label">Kullanıcı Adı veya E-posta</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" 
                                           value="<?php echo isset($_POST['kullanici_adi']) ? htmlspecialchars($_POST['kullanici_adi']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="sifre" class="form-label">Şifre</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="sifre" name="sifre" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="sifreGoster()">
                                        <i class="fas fa-eye" id="sifreIkon"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 btn-lg mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Giriş Yap
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Hesabınız yok mu? 
                                <a href="kayit.php" class="text-success text-decoration-none fw-bold">
                                    Kayıt olun
                                </a>
                            </p>
                            <a href="index.php" class="btn btn-outline-secondary mt-3">
                                <i class="fas fa-arrow-left me-2"></i>
                                Ana Sayfaya Dön
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function sifreGoster() {
            const sifreGirisi = document.getElementById('sifre');
            const sifreIkonu = document.getElementById('sifreIkon');
            
            if (sifreGirisi.type === 'password') {
                sifreGirisi.type = 'text';
                sifreIkonu.classList.remove('fa-eye');
                sifreIkonu.classList.add('fa-eye-slash');
            } else {
                sifreGirisi.type = 'password';
                sifreIkonu.classList.remove('fa-eye-slash');
                sifreIkonu.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html> 