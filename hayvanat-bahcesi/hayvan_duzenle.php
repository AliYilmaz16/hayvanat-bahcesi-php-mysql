<?php
require_once 'config.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header('Location: giris.php');
    exit();
}

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: panel.php');
    exit();
}

$hayvan_id = (int)$_GET['id'];
$hata = '';

// Hayvan bilgilerini getir
try {
    $sorgu = $pdo->prepare("SELECT * FROM animals WHERE id = ?");
    $sorgu->execute([$hayvan_id]);
    $hayvan = $sorgu->fetch();
    
    if (!$hayvan) {
        header('Location: panel.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: panel.php');
    exit();
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad = trim($_POST['ad']);
    $tur = trim($_POST['tur']);
    $yas = !empty($_POST['yas']) ? (int)$_POST['yas'] : null;
    $kilo = !empty($_POST['kilo']) ? (float)$_POST['kilo'] : null;
    $saglik_durumu = $_POST['saglik_durumu'];
    $beslenme_programi = trim($_POST['beslenme_programi']);
    $habitat = trim($_POST['habitat']);
    $notlar = trim($_POST['notlar']);
    
    if (empty($ad)) {
        $hata = 'Hayvan adı zorunludur.';
    } elseif (empty($tur)) {
        $hata = 'Hayvan türü zorunludur.';
    } elseif (!in_array($saglik_durumu, ['Sağlıklı', 'Hasta', 'İyileşiyor', 'Tedavi Altında'])) {
        $hata = 'Geçerli bir sağlık durumu seçin.';
    } else {
        try {
            $sorgu = $pdo->prepare("UPDATE animals SET name = ?, species = ?, age = ?, weight = ?, health_status = ?, feeding_schedule = ?, habitat = ?, notes = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $sorgu->execute([$ad, $tur, $yas, $kilo, $saglik_durumu, $beslenme_programi, $habitat, $notlar, $hayvan_id]);
            
            header('Location: panel.php?basari=guncellendi');
            exit();
        } catch (PDOException $e) {
            $hata = 'Güncelleme sırasında hata oluştu.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hayvan Düzenle - Hayvanat Bahçesi Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: bold;
        }
        .hayvan-bilgi {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="panel.php">
                <i class="fas fa-paw me-2"></i>
                Hayvanat Bahçesi Yönetim Sistemi
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="fas fa-user me-1"></i>
                    <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>
                </span>
                <a class="nav-link" href="cikis.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-edit text-primary fa-3x mb-3"></i>
                        <h2 class="fw-bold">Hayvan Düzenle</h2>
                        <p class="text-muted">Hayvan bilgilerini güncelleyin</p>
                    </div>

                    <!-- Mevcut Bilgiler -->
                    <div class="hayvan-bilgi">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Kayıt Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($hayvan['created_at'])); ?></p>
                                <p class="mb-0"><strong>Son Güncelleme:</strong> <?php echo date('d.m.Y H:i', strtotime($hayvan['updated_at'])); ?></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="mb-1"><strong>Hayvan ID:</strong> #<?php echo $hayvan['id']; ?></p>
                                <p class="mb-0"><strong>Mevcut Durum:</strong> <?php echo htmlspecialchars($hayvan['health_status']); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($hata): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($hata); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ad" class="form-label">Hayvan Adı *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <input type="text" class="form-control" id="ad" name="ad" 
                                               value="<?php echo isset($_POST['ad']) ? htmlspecialchars($_POST['ad']) : htmlspecialchars($hayvan['name']); ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tur" class="form-label">Tür/Cins *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-dna"></i>
                                        </span>
                                        <input type="text" class="form-control" id="tur" name="tur" 
                                               value="<?php echo isset($_POST['tur']) ? htmlspecialchars($_POST['tur']) : htmlspecialchars($hayvan['species']); ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="yas" class="form-label">Yaş</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-birthday-cake"></i>
                                        </span>
                                        <input type="number" class="form-control" id="yas" name="yas" min="0" max="100"
                                               value="<?php echo isset($_POST['yas']) ? htmlspecialchars($_POST['yas']) : htmlspecialchars($hayvan['age']); ?>">
                                        <span class="input-group-text">yaş</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kilo" class="form-label">Kilo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-weight"></i>
                                        </span>
                                        <input type="number" class="form-control" id="kilo" name="kilo" min="0" step="0.01"
                                               value="<?php echo isset($_POST['kilo']) ? htmlspecialchars($_POST['kilo']) : htmlspecialchars($hayvan['weight']); ?>">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="saglik_durumu" class="form-label">Sağlık Durumu *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-heartbeat"></i>
                                        </span>
                                        <select class="form-select" id="saglik_durumu" name="saglik_durumu" required>
                                            <option value="">Seçiniz</option>
                                            <?php 
                                            $durumlar = ['Sağlıklı', 'Hasta', 'İyileşiyor', 'Tedavi Altında'];
                                            $mevcut_durum = isset($_POST['saglik_durumu']) ? $_POST['saglik_durumu'] : $hayvan['health_status'];
                                            foreach ($durumlar as $durum): 
                                            ?>
                                                <option value="<?php echo $durum; ?>" <?php echo ($mevcut_durum === $durum) ? 'selected' : ''; ?>>
                                                    <?php echo $durum; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="habitat" class="form-label">Yaşam Alanı/Habitat</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-home"></i>
                                </span>
                                <input type="text" class="form-control" id="habitat" name="habitat" 
                                       value="<?php echo isset($_POST['habitat']) ? htmlspecialchars($_POST['habitat']) : htmlspecialchars($hayvan['habitat']); ?>"
                                       placeholder="Örn: Afrika Savanası, Asya Ormanları, Kutup Bölgesi">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="beslenme_programi" class="form-label">Beslenme Programı</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-utensils"></i>
                                </span>
                                <textarea class="form-control" id="beslenme_programi" name="beslenme_programi" rows="3"
                                          placeholder="Beslenme saatleri, yiyecek türleri ve miktarları..."><?php echo isset($_POST['beslenme_programi']) ? htmlspecialchars($_POST['beslenme_programi']) : htmlspecialchars($hayvan['feeding_schedule']); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="notlar" class="form-label">Notlar</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-sticky-note"></i>
                                </span>
                                <textarea class="form-control" id="notlar" name="notlar" rows="4"
                                          placeholder="Hayvan hakkında özel notlar, davranış özellikleri, sağlık geçmişi..."><?php echo isset($_POST['notlar']) ? htmlspecialchars($_POST['notlar']) : htmlspecialchars($hayvan['notes']); ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="panel.php" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>
                                Geri Dön
                            </a>
                            <div>
                                <a href="panel.php?sil=<?php echo $hayvan['id']; ?>" 
                                   class="btn btn-outline-danger btn-lg me-2"
                                   onclick="return confirm('Bu hayvanı silmek istediğinizden emin misiniz?')">
                                    <i class="fas fa-trash me-2"></i>
                                    Sil
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>
                                    Değişiklikleri Kaydet
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validasyonu
        document.getElementById('ad').addEventListener('input', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });

        document.getElementById('tur').addEventListener('input', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });

        document.getElementById('habitat').addEventListener('input', function() {
            this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
        });
    </script>
</body>
</html> 