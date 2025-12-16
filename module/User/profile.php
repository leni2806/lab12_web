<?php
// Cek apakah user sudah login
if (!isset($_SESSION['is_login'])) {
    header('Location: ' . BASE_URL . '/user/login');
    exit;
}

$db = new Database();
$message = "";
$success = "";

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
$result = $db->query($sql);
$user = $result->fetch_assoc();

// Proses update password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verifikasi password lama
    if (password_verify($current_password, $user['password'])) {
        // Cek kesesuaian password baru
        if ($new_password == $confirm_password) {
            // Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password di database
            $update_sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            $db->query($update_sql);
            
            $success = "Password berhasil diubah!";
        } else {
            $message = "Password baru tidak cocok!";
        }
    } else {
        $message = "Password lama salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Profil Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <!-- Informasi User -->
                        <div class="mb-4">
                            <h5>Informasi Akun</h5>
                            <p><strong>Nama:</strong> <?php echo $user['nama']; ?></p>
                            <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                        </div>
                        
                        <!-- Form Ubah Password -->
                        <h5 class="mb-3">Ubah Password</h5>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Ubah Password</button>
                            </div>
                        </form>
                        
                        <div class="mt-3">
                            <a href="<?php echo BASE_URL; ?>/artikel/index" class="btn btn-secondary">Kembali ke Artikel</a>
                            <a href="<?php echo BASE_URL; ?>/home/index" class="btn btn-outline-secondary">Ke Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>