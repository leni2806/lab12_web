<?php
// module/artikel/index.php (Alternatif Tabel)
$db = new Database();
$sql = "SELECT * FROM artikel ORDER BY id DESC";
$result = $db->query($sql);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-list-alt"></i> Daftar Artikel</h2>
        <a href="<?php echo BASE_URL; ?>/artikel/tambah" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Artikel
        </a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">ID</th>
                        <th width="25%">Judul Artikel</th>
                        <th width="45%">Isi</th>
                        <th width="15%">Kategori</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary"><?php echo $row['id']; ?></span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($row['judul']); ?></strong>
                                <?php if (!empty($row['tanggal'])): ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="far fa-calendar"></i> 
                                        <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="article-preview">
                                    <?php 
                                    $content = htmlspecialchars($row['isi']);
                                    echo substr($content, 0, 150);
                                    if (strlen($content) > 150) echo '...';
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($row['kategori'])): ?>
                                    <span class="badge bg-info"><?php echo $row['kategori']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Umum</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo BASE_URL; ?>/artikel/edit/<?php echo $row['id']; ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/artikel/hapus/<?php echo $row['id']; ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Hapus artikel ini?')"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/artikel/detail/<?php echo $row['id']; ?>" 
                                       class="btn btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination (jika banyak data) -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
        
        <div class="alert alert-secondary">
            <i class="fas fa-database"></i> 
            Menampilkan <?php echo $result->num_rows; ?> artikel dari database.
        </div>
        
    <?php else: ?>
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                <h4>Belum ada artikel</h4>
                <p class="text-muted">Mulai dengan menambahkan artikel pertama Anda.</p>
                <a href="<?php echo BASE_URL; ?>/artikel/tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Artikel Pertama
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Tambahkan CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .table th {
        font-weight: 600;
        background-color: #2c3e50;
        color: white;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .article-preview {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        line-height: 1.4;
        border-left: 3px solid #3498db;
    }
    
    .empty-state {
        max-width: 400px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    
    .badge {
        font-size: 0.8em;
        padding: 4px 8px;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>