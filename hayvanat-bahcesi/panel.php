<?php
require_once 'config.php';

// Kullanıcı giriş kontrolü
if (!isset($_SESSION['kullanici_id'])) {
    header('Location: giris.php');
    exit();
}

// Hayvanları veritabanından çek
try {
    $sorgu = $pdo->prepare("SELECT * FROM animals ORDER BY created_at DESC");
    $sorgu->execute();
    $hayvanlar = $sorgu->fetchAll();
} catch (PDOException $e) {
    $hayvanlar = [];
    $hata = 'Hayvanlar yüklenirken bir hata oluştu.';
}

// Silme işlemi
if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    try {
        $sorgu = $pdo->prepare("DELETE FROM animals WHERE id = ?");
        $sorgu->execute([$_GET['sil']]);
        header('Location: panel.php?basari=silindi');
        exit();
    } catch (PDOException $e) {
        $hata = 'Hayvan silinirken bir hata oluştu.';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Paneli - Hayvanat Bahçesi Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .hayvan-kart {
            transition: transform 0.2s;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .hayvan-kart:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .saglik-durumu {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8em;
        }
        .durum-saglikli { background: #d4edda; color: #155724; }
        .durum-hasta { background: #f8d7da; color: #721c24; }
        .durum-iyilesiyor { background: #ffeaa7; color: #6c5ce7; }
        .durum-tedavi { background: #fd79a8; color: #2d3436; }
        .istatistik-kart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
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
                    Hoş geldin, <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?>
                </span>
                <a class="nav-link" href="cikis.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Başarı/Hata Mesajları -->
        <?php if (isset($_GET['basari'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                switch($_GET['basari']) {
                    case 'eklendi': echo 'Hayvan başarıyla eklendi!'; break;
                    case 'guncellendi': echo 'Hayvan bilgileri güncellendi!'; break;
                    case 'silindi': echo 'Hayvan kaydı silindi!'; break;
                    default: echo 'İşlem başarılı!';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($hata)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($hata); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Başlık ve Yeni Hayvan Ekleme Butonu -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="fw-bold">
                    <i class="fas fa-hippo text-success me-2"></i>
                    Hayvan Yönetimi
                </h1>
                <p class="text-muted">Hayvanat bahçenizdeki tüm hayvanları buradan yönetebilirsiniz</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="hayvan_ekle.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Yeni Hayvan Ekle
                </a>
            </div>
        </div>

        <!-- İstatistikler -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card istatistik-kart">
                    <div class="card-body text-center">
                        <i class="fas fa-paw fa-2x mb-2"></i>
                        <h3 class="fw-bold"><?php echo count($hayvanlar); ?></h3>
                        <p class="mb-0">Toplam Hayvan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-heart fa-2x mb-2"></i>
                        <h3 class="fw-bold">
                            <?php echo count(array_filter($hayvanlar, function($h) { return $h['health_status'] === 'Sağlıklı'; })); ?>
                        </h3>
                        <p class="mb-0">Sağlıklı</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h3 class="fw-bold">
                            <?php echo count(array_filter($hayvanlar, function($h) { return in_array($h['health_status'], ['Hasta', 'Tedavi Altında']); })); ?>
                        </h3>
                        <p class="mb-0">Dikkat Gereken</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-band-aid fa-2x mb-2"></i>
                        <h3 class="fw-bold">
                            <?php echo count(array_filter($hayvanlar, function($h) { return $h['health_status'] === 'İyileşiyor'; })); ?>
                        </h3>
                        <p class="mb-0">İyileşiyor</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hayvan Listesi -->
        <?php if (empty($hayvanlar)): ?>
            <div class="text-center py-5">
                <i class="fas fa-hippo text-muted" style="font-size: 5rem;"></i>
                <h3 class="text-muted mt-3">Henüz hayvan eklenmemiş</h3>
                <p class="text-muted">İlk hayvanınızı eklemek için butona tıklayın</p>
                <a href="hayvan_ekle.php" class="btn btn-success btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    İlk Hayvanı Ekle
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($hayvanlar as $hayvan): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card hayvan-kart h-100">
                            <div class="card-header bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0 fw-bold"><?php echo htmlspecialchars($hayvan['name']); ?></h5>
                                    <span class="saglik-durumu <?php 
                                        switch($hayvan['health_status']) {
                                            case 'Sağlıklı': echo 'durum-saglikli'; break;
                                            case 'Hasta': echo 'durum-hasta'; break;
                                            case 'İyileşiyor': echo 'durum-iyilesiyor'; break;
                                            case 'Tedavi Altında': echo 'durum-tedavi'; break;
                                        }
                                    ?>">
                                        <?php echo htmlspecialchars($hayvan['health_status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-2">
                                    <i class="fas fa-dna me-2"></i>
                                    <strong>Tür:</strong> <?php echo htmlspecialchars($hayvan['species']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-birthday-cake me-2"></i>
                                    <strong>Yaş:</strong> <?php echo $hayvan['age'] ? $hayvan['age'] . ' yaş' : 'Belirtilmemiş'; ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-weight me-2"></i>
                                    <strong>Kilo:</strong> <?php echo $hayvan['weight'] ? $hayvan['weight'] . ' kg' : 'Belirtilmemiş'; ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-home me-2"></i>
                                    <strong>Habitat:</strong> <?php echo htmlspecialchars($hayvan['habitat']) ?: 'Belirtilmemiş'; ?>
                                </p>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-utensils me-2"></i>
                                    <strong>Beslenme:</strong> <?php echo $hayvan['feeding_schedule'] ? htmlspecialchars(substr($hayvan['feeding_schedule'], 0, 50)) . '...' : 'Belirtilmemiş'; ?>
                                </p>
                                <?php if ($hayvan['notes']): ?>
                                    <p class="text-muted small">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        <?php echo htmlspecialchars(substr($hayvan['notes'], 0, 100)) . (strlen($hayvan['notes']) > 100 ? '...' : ''); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo date('d.m.Y', strtotime($hayvan['created_at'])); ?>
                                    </small>
                                    <div>
                                        <a href="hayvan_duzenle.php?id=<?php echo $hayvan['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary me-1" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="panel.php?sil=<?php echo $hayvan['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Sil"
                                           onclick="return confirm('Bu hayvanı silmek istediğinizden emin misiniz?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 