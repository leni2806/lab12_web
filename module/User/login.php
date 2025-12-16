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