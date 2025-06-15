<?php
require_once 'config.php';

// Hata ve başarı mesajları
$hata = '';
$basari = '';

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = trim($_POST['kullanici_adi']);
    $eposta = trim($_POST['eposta']);
    $sifre = $_POST['sifre'];
    $sifre_tekrar = $_POST['sifre_tekrar'];
    
    // Validasyon
    if (empty($kullanici_adi) || empty($eposta) || empty($sifre) || empty($sifre_tekrar)) {
        $hata = 'Lütfen tüm alanları doldurun.';
    } elseif (strlen($kullanici_adi) < 3) {
        $hata = 'Kullanıcı adı en az 3 karakter olmalıdır.';
    } elseif (!filter_var($eposta, FILTER_VALIDATE_EMAIL)) {
        $hata = 'Geçerli bir e-posta adresi girin.';
    } elseif (strlen($sifre) < 6) {
        $hata = 'Şifre en az 6 karakter olmalıdır.';
    } elseif ($sifre !== $sifre_tekrar) {
        $hata = 'Şifreler eşleşmiyor.';
    } else {
        try {
            // Kullanıcı adı veya e-posta zaten var mı kontrol et
            $sorgu = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $sorgu->execute([$kullanici_adi, $eposta]);
            
            if ($sorgu->fetch()) {
                $hata = 'Bu kullanıcı adı veya e-posta zaten kullanılıyor.';
            } else {
                // Şifreyi hash'le
                $sifreli_sifre = password_hash($sifre, PASSWORD_DEFAULT);
                
                // Kullanıcıyı veritabanına ekle
                $sorgu = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $sorgu->execute([$kullanici_adi, $eposta, $sifreli_sifre]);
                
                $basari = 'Kayıt başarılı! Şimdi giriş yapabilirsiniz.';
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
    <title>Kayıt Ol - Hayvanat Bahçesi Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .kayit-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c5530 0%, #4a7c59 100%);
            padding: 50px 0;
        }
        .kayit-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .sifre-guc {
            height: 5px;
            background: #e0e0e0;
            border-radius: 3px;
            margin-top: 5px;
        }
        .guc-zayif { background: #ff4444; }
        .guc-orta { background: #ffaa00; }
        .guc-guclu { background: #00dd00; }
    </style>
</head>
<body>
    <div class="kayit-container d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="kayit-card p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus text-success fa-3x mb-3"></i>
                            <h2 class="fw-bold">Kayıt Ol</h2>
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
                                <div class="mt-2">
                                    <a href="giris.php" class="btn btn-success btn-sm">
                                        Giriş Yapmak İçin Tıklayın
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="kayitForm">
                            <div class="mb-3">
                                <label for="kullanici_adi" class="form-label">Kullanıcı Adı</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control" id="kullanici_adi" name="kullanici_adi" 
                                           value="<?php echo isset($_POST['kullanici_adi']) ? htmlspecialchars($_POST['kullanici_adi']) : ''; ?>" 
                                           minlength="3" required>
                                </div>
                                <small class="text-muted">En az 3 karakter olmalıdır</small>
                            </div>

                            <div class="mb-3">
                                <label for="eposta" class="form-label">E-posta</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" id="eposta" name="eposta" 
                                           value="<?php echo isset($_POST['eposta']) ? htmlspecialchars($_POST['eposta']) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="sifre" class="form-label">Şifre</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="sifre" name="sifre" 
                                           minlength="6" required onkeyup="sifreGucKontrol()">
                                    <button class="btn btn-outline-secondary" type="button" onclick="sifreGoster('sifre', 'sifreIkon1')">
                                        <i class="fas fa-eye" id="sifreIkon1"></i>
                                    </button>
                                </div>
                                <div class="sifre-guc" id="sifreGuc"></div>
                                <small class="text-muted">En az 6 karakter olmalıdır</small>
                            </div>

                            <div class="mb-4">
                                <label for="sifre_tekrar" class="form-label">Şifre Tekrar</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" id="sifre_tekrar" name="sifre_tekrar" 
                                           required onkeyup="sifreUyumKontrol()">
                                    <button class="btn btn-outline-secondary" type="button" onclick="sifreGoster('sifre_tekrar', 'sifreIkon2')">
                                        <i class="fas fa-eye" id="sifreIkon2"></i>
                                    </button>
                                </div>
                                <small id="sifreUyum" class="text-muted"></small>
                            </div>

                            <button type="submit" class="btn btn-success w-100 btn-lg mb-3">
                                <i class="fas fa-user-plus me-2"></i>
                                Kayıt Ol
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="mb-0">Zaten hesabınız var mı? 
                                <a href="giris.php" class="text-success text-decoration-none fw-bold">
                                    Giriş yapın
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
        function sifreGoster(inputId, iconId) {
            const sifreGirisi = document.getElementById(inputId);
            const sifreIkonu = document.getElementById(iconId);
            
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

        function sifreGucKontrol() {
            const sifre = document.getElementById('sifre').value;
            const gucBar = document.getElementById('sifreGuc');
            
            let guc = 0;
            if (sifre.length >= 6) guc++;
            if (sifre.match(/[a-z]/) && sifre.match(/[A-Z]/)) guc++;
            if (sifre.match(/[0-9]/)) guc++;
            if (sifre.match(/[^a-zA-Z0-9]/)) guc++;
            
            gucBar.className = 'sifre-guc';
            if (guc >= 1 && guc <= 2) {
                gucBar.classList.add('guc-zayif');
            } else if (guc === 3) {
                gucBar.classList.add('guc-orta');
            } else if (guc >= 4) {
                gucBar.classList.add('guc-guclu');
            }
        }

        function sifreUyumKontrol() {
            const sifre = document.getElementById('sifre').value;
            const sifreTekrar = document.getElementById('sifre_tekrar').value;
            const uyumMetni = document.getElementById('sifreUyum');
            
            if (sifreTekrar.length > 0) {
                if (sifre === sifreTekrar) {
                    uyumMetni.textContent = '✓ Şifreler eşleşiyor';
                    uyumMetni.className = 'text-success';
                } else {
                    uyumMetni.textContent = '✗ Şifreler eşleşmiyor';
                    uyumMetni.className = 'text-danger';
                }
            } else {
                uyumMetni.textContent = '';
            }
        }
    </script>
</body>
</html> 