<?php
$db = new Database();
$form = new Form("", "Simpan");

// Proses penyimpanan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['judul'])) {
    $data = [
        'judul' => $_POST['judul'],
        'isi' => $_POST['isi']
    ];
    
    if ($db->insert('artikel', $data)) {
        // Redirect dengan status success
        header("Location: /lab11_php_oop/artikel/index?status=success");
        exit();
    } else {
        echo '<div class="alert alert-danger">Gagal menyimpan artikel.</div>';
    }
}
?>

<h2>Tambah Artikel</h2>

<form method="POST" action="">
    <table width="100%" cellpadding="10">
        <tr>
            <td width="20%"><strong>Judul</strong></td>
            <td width="80%">
                <input type="text" name="judul" placeholder="Masukkan judul artikel" required style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td valign="top"><strong>Isi</strong></td>
            <td>
                <textarea name="isi" rows="6" placeholder="Masukkan isi artikel" required style="width: 100%;"></textarea>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <input type="submit" value="Simpan">
                <a href="/lab11_php_oop/artikel/index" style="margin-left: 10px; color: #666;">Kembali</a>
            </td>
        </tr>
    </table>
</form>