<?php
// Cek apakah konstanta sudah didefinisikan sebelumnya
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}

if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}

if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'lathhan_oop');
}

if (!defined('BASE_URL')) {
    // Base URL - sesuaikan dengan folder Anda
    define('BASE_URL', 'http://localhost/lab11_php_oop');
}

// Array konfigurasi untuk compatibility dengan class Database lama
$config = [
    'host' => DB_HOST,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'db' => DB_NAME
];
?>