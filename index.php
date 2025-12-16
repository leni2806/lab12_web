<?php
session_start();

include "config.php";

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/lab11_php_oop');
}

include "class/Database.php";
include "class/Form.php";

$request_uri = $_SERVER['REQUEST_URI'] ?? '/home/index';

$request_uri = strtok($request_uri, '?');

$base_path = parse_url(BASE_URL, PHP_URL_PATH) ?? '';
if ($base_path && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
} else {
    $path = $request_uri;
}

if (empty($path) || $path == '/') {
    $path = '/home/index';
}

$segments = explode('/', trim($path, '/'));
$mod = $segments[0] ?? 'home';
$page = $segments[1] ?? 'index';

$params = array_slice($segments, 2);

$public_pages = ['home', 'user'];

if (!in_array($mod, $public_pages)) {
    if (!isset($_SESSION['is_login'])) {
        header('Location: ' . BASE_URL . '/user/login');
        exit();
    }
}

$file = "module/{$mod}/{$page}.php";

if (file_exists($file)) {
    if ($mod == 'user' && $page == 'login') {
        include $file;
    } else {
        include "template/header.php";
        include $file;
        include "template/footer.php";
    }
} else {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error: Halaman tidak ditemukan</h4>";
    echo "<p>File <strong>{$file}</strong> tidak ditemukan.</p>";
    echo "<p>Path: {$path}</p>";
    echo "<p>Segments: " . implode(', ', $segments) . "</p>";
    echo "<p>Params: " . implode(', ', $params) . "</p>";
    echo "<p><a href='" . BASE_URL . "/home/index' class='btn btn-primary'>Kembali ke Home</a></p>";
    echo "</div>";
    echo "</div>";
}
?>