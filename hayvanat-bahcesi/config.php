<?php
// GitHub için Güvenli Config Dosyası
// ⚠️ Bu dosyayı GitHub'a yüklemeden önce gerçek bilgileri silin!

// XAMPP için (Yerel test)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'zoo_management');

// Hosting için (Canlı sunucu) 
// Hosting bilgilerinizi buraya ekleyin
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'hosting_kullanici_adi');
define('DB_PASS', 'hosting_sifresi');
define('DB_NAME', 'hosting_veritabani_adi');
*/

// Veritabanı bağlantısı
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage() . "<br><br>
    <strong>Çözüm:</strong><br>
    1. Hosting panelinden veritabanı kullanıcı adı ve şifresini kontrol edin<br>
    2. config.php dosyasındaki DB_USER ve DB_PASS değerlerini güncelleyin<br>
    3. Veritabanı adının doğru olduğundan emin olun: " . DB_NAME);
}

// Session başlat
session_start();
?> 