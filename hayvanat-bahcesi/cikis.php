<?php
require_once 'config.php';

// Oturumu temizle
session_unset();
session_destroy();

// Ana sayfaya yönlendir
header('Location: index.php?cikis=basarili');
exit();
?> 