
<?php
// module/artikel/edit.php
// HAPUS session_start() karena sudah dipanggil di index.php
// session_start(); // ← HAPUS BARIS INI

// Include config dan database
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../class/Database.php';

// Cek session login - GUNAKAN isset($_SESSION) bukan session_start()
if (!isset($_SESSION['is_login'])) {
    header('Location: ' . BASE_URL . '/user/login');
    exit;
}

$db = new Database();
$message = '';
$error = '';

// ========= AMBIL ID DARI MANA SAJA =========
$id = 0;

// Cara 1: Dari GET parameter (?id=...)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
}
// Cara 2: Dari PATH_INFO (/edit/2)
elseif (isset($_SERVER['PATH_INFO'])) {
    $path = $_SERVER['PATH_INFO'];
    $segments = explode('/', trim($path, '/'));
    if (isset($segments[2]) && is_numeric($segments[2])) {
        $id = intval($segments[2]);
    }
}
// Cara 3: Dari REQUEST_URI parsing
elseif (isset($_SERVER['REQUEST_URI'])) {
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/artikel\/edit\/(\d+)/', $uri, $matches)) {
        $id = intval($matches[1]);
    }
    // Juga coba pattern dengan query string
    elseif (preg_match('/id=(\d+)/', $uri, $matches)) {
        $id = intval($matches[1]);
    }
}

// Debug mode
$debug_mode = isset($_GET['debug']) && $_GET['debug'] == 1;
if ($debug_mode) {
    echo "<div class='debug-info alert alert-warning'>";
    echo "<h5>Debug Information:</h5>";
    echo "ID: $id<br>";
    echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NULL') . "<br>";
    echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NULL') . "<br>";
    echo "GET: ";
    print_r($_GET);
    echo "<br>";
    echo "Session is_login: " . (isset($_SESSION['is_login']) ? 'YES' : 'NO') . "<br>";
    echo "</div>";
}

if ($id == 0) {
    ?>
    <div class="container mt-5">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> ID Artikel Tidak Valid</h4>
            </div>
            <div class="card-body">
                <p>ID artikel tidak dapat ditemukan atau tidak valid.</p>
                <p><strong>URL yang diakses:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'N/A'; ?></p>
                
                <div class="alert alert-info">
                    <h6>Solusi:</h6>
                    <ol>
                        <li>Pastikan Anda mengakses dari link "Edit" di halaman daftar artikel</li>
                        <li>Coba akses langsung: <a href="<?php echo BASE_URL; ?>/artikel/edit?id=1"><?php echo BASE_URL; ?>/artikel/edit?id=1</a></li>
                        <li>Atau dengan format lain: <a href="<?php echo BASE_URL; ?>/artikel/edit/1"><?php echo BASE_URL; ?>/artikel/edit/1</a></li>
                    </ol>
                </div>
                
                <div class="mt-3">
                    <a href="<?php echo BASE_URL; ?>/artikel/index" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Artikel
                    </a>
                    <a href="<?php echo BASE_URL; ?>/artikel/edit?debug=1&id=1" class="btn btn-warning">
                        <i class="fas fa-bug"></i> Debug Mode
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
    exit;
}

// ========= AMBIL DATA DARI DATABASE =========
$sql = "SELECT * FROM artikel WHERE id = " . $id;
$result = $db->query($sql);

if (!$result) {
    echo "<div class='alert alert-danger'>Error query: " . $db->getConnection()->error . "</div>";
    exit;
}

if ($result->num_rows == 0) {
    ?>
    <div class="container mt-5">
        <div class="alert alert-warning">
            <h4><i class="fas fa-search"></i> Artikel Tidak Ditemukan</h4>
            <p>Artikel dengan ID <strong><?php echo $id; ?></strong> tidak ditemukan di database.</p>
            <p>Mungkin artikel telah dihapus atau ID tidak sesuai.</p>
            <a href="<?php echo BASE_URL; ?>/artikel/index" class="btn btn-primary">Lihat Daftar Artikel yang Ada</a>
        </div>
    </div>
    <?php
    exit;
}

$artikel = $result->fetch_assoc();

// ========= PROSES UPDATE =========
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    
    // Validasi
    if (empty($judul)) {
        $error = "Judul artikel tidak boleh kosong!";
    } elseif (empty($isi)) {
        $error = "Isi artikel tidak boleh kosong!";
    } else {
        // Escape input untuk keamanan
        $judul_escaped = $db->escape($judul);
        $isi_escaped = $db->escape($isi);
        $kategori_escaped = $db->escape($kategori);
        
        // Update data
        $update_sql = "UPDATE artikel SET 
                      judul = '$judul_escaped',
                      isi = '$isi_escaped',
                      kategori = '$kategori_escaped',
                      updated_at = NOW()
                      WHERE id = $id";
        
        if ($db->query($update_sql)) {
            $message = "Artikel berhasil diperbarui!";
            
            // Refresh data artikel
            $result = $db->query("SELECT * FROM artikel WHERE id = $id");
            $artikel = $result->fetch_assoc();
            
            // Auto redirect setelah 3 detik
            echo '<script>
                setTimeout(function() {
                    window.location.href = "' . BASE_URL . '/artikel/index?success=Artikel+berhasil+diperbarui";
                }, 3000);
            </script>';
        } else {
            $error = "Gagal memperbarui artikel: " . $db->getConnection()->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel #<?php echo $id; ?> - Praktikum 12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }
        .form-label {
            font-weight: 600;
        }
        textarea {
            min-height: 300px;
            resize: vertical;
        }
        .debug-info {
            font-family: monospace;
            font-size: 0.9rem;
        }
        .success-message {
            animation: fadeOut 3s forwards;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/home/index"><i class="fas fa-home"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/artikel/index">Artikel</a></li>
                <li class="breadcrumb-item active">Edit Artikel #<?php echo $id; ?></li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="fas fa-edit text-primary"></i> Edit Artikel</h1>
            <a href="<?php echo BASE_URL; ?>/artikel/index" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success success-message">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Sukses!</h5>
                    <p class="mb-0"><?php echo $message; ?></p>
                    <small class="text-muted">Mengalihkan ke daftar artikel...</small>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Error!</h5>
                    <p class="mb-0"><?php echo $error; ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form Edit -->
        <div class="card border-0 shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Form Edit Artikel</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="" id="editForm">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <label for="judul" class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="judul" 
                                       name="judul" 
                                       value="<?php echo htmlspecialchars($artikel['judul']); ?>" 
                                       required
                                       maxlength="200"
                                       placeholder="Masukkan judul artikel">
                                <div class="form-text">Maksimal 200 karakter</div>
                            </div>

                            <div class="mb-4">
                                <label for="isi" class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                                <textarea class="form-control" 
                                          id="isi" 
                                          name="isi" 
                                          rows="12"
                                          required
                                          placeholder="Tulis isi artikel Anda di sini..."><?php echo htmlspecialchars($artikel['isi']); ?></textarea>
                                <div class="form-text">Gunakan bahasa yang jelas dan terstruktur</div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informasi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="kategori" class="form-label">Kategori</label>
                                        <select class="form-select" id="kategori" name="kategori">
                                            <option value="">-- Pilih Kategori --</option>
                                            <option value="PHP" <?php echo ($artikel['kategori'] == 'PHP') ? 'selected' : ''; ?>>PHP</option>
                                            <option value="Web Development" <?php echo ($artikel['kategori'] == 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                                            <option value="Database" <?php echo ($artikel['kategori'] == 'Database') ? 'selected' : ''; ?>>Database</option>
                                            <option value="JavaScript" <?php echo ($artikel['kategori'] == 'JavaScript') ? 'selected' : ''; ?>>JavaScript</option>
                                            <option value="HTML/CSS" <?php echo ($artikel['kategori'] == 'HTML/CSS') ? 'selected' : ''; ?>>HTML/CSS</option>
                                            <option value="Tutorial" <?php echo ($artikel['kategori'] == 'Tutorial') ? 'selected' : ''; ?>>Tutorial</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">ID Artikel</label>
                                        <input type="text" class="form-control bg-light" value="#<?php echo $id; ?>" readonly>
                                    </div>

                                    <?php if (!empty($artikel['created_at'])): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Dibuat Pada</label>
                                        <input type="text" class="form-control bg-light" 
                                               value="<?php echo date('d F Y H:i', strtotime($artikel['created_at'])); ?>" readonly>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary">
                                            <i class="fas fa-redo"></i> Reset Form
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-eye"></i> Preview</h5>
            </div>
            <div class="card-body">
                <h4 id="previewTitle"><?php echo htmlspecialchars($artikel['judul']); ?></h4>
                <div class="text-muted mb-3">
                    <small>
                        <i class="far fa-calendar"></i> 
                        <?php echo date('d F Y'); ?> | 
                        <span id="previewCategory"><?php echo isset($artikel['kategori']) && !empty($artikel['kategori']) ? $artikel['kategori'] : 'Umum'; ?></span>
                    </small>
                </div>
                <hr>
                <div id="previewContent" style="white-space: pre-line; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($artikel['isi'])); ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview
        document.getElementById('judul').addEventListener('input', function() {
            document.getElementById('previewTitle').textContent = this.value || 'Judul Artikel';
        });

        document.getElementById('isi').addEventListener('input', function() {
            document.getElementById('previewContent').innerHTML = 
                this.value.replace(/\n/g, '<br>') || '<em class="text-muted">Isi artikel akan muncul di sini...</em>';
        });

        document.getElementById('kategori').addEventListener('change', function() {
            document.getElementById('previewCategory').textContent = 
                this.value ? this.value : 'Umum';
        });

        // Validasi form
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value.trim();
            const isi = document.getElementById('isi').value.trim();
            
            if (!judul) {
                e.preventDefault();
                alert('Judul artikel harus diisi!');
                document.getElementById('judul').focus();
                return false;
            }
            
            if (!isi) {
                e.preventDefault();
                alert('Isi artikel harus diisi!');
                document.getElementById('isi').focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>
