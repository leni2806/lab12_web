# Praktikum 12: Autentikasi dan Session 

**Nama**: LENI  
**NIM**: 312410442  
**Kelas**: TI.24.A5  
**Program Studi**: Teknik Informatika  
**Mata Kuliah**: Pemrograman Web  

## Tujuan Praktikum 
1. Mahasiswa mampu memahami konsep dasar Autnetikasi. 
2. Mahasiswa mampu memahami konsep dasar Session. 
3. Mahasaswa mampu mengimplementasikan Autentikasi sederhana. 

## Struktur Project
```
   lab11_php_oop/
├── index.php              # Main router dan controller
├── config.php             # Konfigurasi database dan base URL
├── .htaccess              # URL rewriting (opsional)
├── class/
│   ├── Database.php       # Class untuk koneksi database
│   └── Form.php           # Class untuk form handling
├── module/
│   ├── home/
│   │   └── index.php      # Halaman home
│   ├── user/
│   │   ├── login.php      # Halaman login
│   │   ├── logout.php     # Proses logout
│   │   └── profile.php    # Halaman profil user
│   └── artikel/
│       ├── index.php      # Daftar artikel (READ)
│       ├── tambah.php     # Form tambah artikel (CREATE)
│       ├── edit.php       # Form edit artikel (UPDATE)
│       └── hapus.php      # Konfirmasi hapus (DELETE)
├── template/
│   ├── header.php         # Header template dengan navigation
│   └── footer.php         # Footer template
└── README.md              # Dokumentasi ini     
```

## Implementasi Kode

### 1. Sistem Routing (`index.php`)
```php
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
```
### Fungsi:
- Menangani routing URL dan proteksi halaman

### 2. Autentikasi (`modeule/user/login.php`)
```php
<?php
// Cek jika sudah login, langsung ke home
if (isset($_SESSION['is_login'])) {
    header('Location: ' . BASE_URL . '/home/index');
    exit;
}

$message = "";
// Logika Proses Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    
    // Ambil input dan sanitasi
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Debug: Tampilkan input (hapus setelah testing)
    // echo "Username: $username<br>";
    // echo "Password: $password<br>";
    
    // Escape untuk keamanan
    $escaped_username = $db->escape($username);
    
    // Query cari user berdasarkan username
    $sql = "SELECT * FROM users WHERE username = '$escaped_username' LIMIT 1";
    // echo "SQL: $sql<br>"; // Debug
    
    $result = $db->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // Debug: Tampilkan data user
        // echo "Data user: ";
        // print_r($data);
        // echo "<br>Password hash di DB: " . $data['password'] . "<br>";
        
        // Verifikasi password
        if (password_verify($password, $data['password'])) {
            // Login Sukses: Set Session
            $_SESSION['is_login'] = true;
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['user_id'] = $data['id'];
            
            // Debug
            // echo "Password verified!<br>";
            
            // Redirect ke halaman admin/artikel
            header('Location: ' . BASE_URL . '/artikel/index');
            exit;
        } else {
            $message = "Password salah!";
            // Debug
            // echo "Password verification failed!<br>";
        }
    } else {
        $message = "Username tidak ditemukan!";
        // Debug
        // echo "User not found!<br>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container { 
            max-width: 400px; 
            margin: 100px auto; 
            padding: 20px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            border-radius: 8px; 
        }
        .debug-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3 class="text-center mb-4">Login User</h3>
            
            <?php 
            // Debug: Tampilkan info
            if (isset($_GET['debug'])) {
                echo '<div class="debug-info">';
                echo 'BASE_URL: ' . BASE_URL . '<br>';
                echo 'Session status: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive');
                echo '</div>';
            }
            ?>
            
            <?php if ($message): ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> <?php echo $message; ?>
                    <?php if (isset($_GET['debug'])): ?>
                        <br><small>Cek database dan hash password</small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            
            <div class="mt-3 text-center">
                <a href="<?php echo BASE_URL; ?>/home/index">Kembali ke Home</a> |
                <a href="<?php echo BASE_URL; ?>/user/login?debug=1">Debug Mode</a> |
                <a href="<?php echo BASE_URL; ?>/reset_password.php">Reset Password</a>
            </div>
        </div>
    </div>
</body>
</html>
```
### Fungsi:
- validasi user dan membuat session

### 3. Database Class (`class/database.php`)
```php
<?php
class Database {
    private $conn;
    
    public function __construct() {
        // Gunakan konstanta yang sudah didefinisikan di config.php
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $dbname = defined('DB_NAME') ? DB_NAME : 'latihan_oop';
        
        // Buat koneksi
        $this->conn = new mysqli($host, $user, $pass, $dbname);
        
        // Cek koneksi
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
```
### CRUD Operations (module/artikel/)
1. index.php - Menampilkan daftar artikel
2. tambah.php - Form untuk menambah artikel baru
3. edit.php - Form untuk mengedit artikel
4. hapus.php - Konfirmasi dan proses hapus artikel

### session Management
- Login: Membuat session variables
- Logout: Menghapus semua session dengan session_destroy()
- Proteksi: Mengecek $_SESSION['is_login'] sebelum akses halaman terproteksi

## Screenshot Implementasi
1. Halaman Login
<img src="/login.png" width="450">
From login dengan validasi username dan password

2. Daftar Artikel
<img src="/artikel.png" width="450">
Tampilan tabel artikel dengan aksi edit dan hapus 

3. Form Tambah Artikel
<img src="/tambah.png" width="450">
Form untuk menambahkan artikel baru

4. Form Edit Artikel
<img src="/edit.png" width="450">
Form untuk mengedit artikel yang sudah ada

5. Halaman Profil
<img src="/profil.png" width="450">
Halaman profil dengan fitur ganti password

## Akses:
http://localhost/lab11_php_oop/user/login


## Kesimpulan
Praktikum ini berhasil mengimplementasikan:

1. Sistem autentikasi dengan session management
2. Proteksi halaman berdasarkan status login
3. CRUD operations untuk manajemen artikel
4. Password security dengan hashing
5. Modular architecture dengan routing dinamis
6. Responsive UI dengan Bootstrap 5
7. Error handling dan user feedback
8. Security best practices
