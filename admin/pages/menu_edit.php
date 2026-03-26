<div class="page-header">
    <h1>Edit Menu</h1>
</div>

<?php
$id = $_GET['id'];
$menu = $conn->query("SELECT * FROM menu WHERE id = $id")->fetch_assoc();

if (!$menu) {
    header('Location: ?page=menu');
    exit;
}

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $portion = $_POST['portion'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE menu SET nama=?, kategori=?, deskripsi=?, harga=?, `portion`=?, status=? WHERE id=?");
    $stmt->bind_param("sssissi", $nama, $kategori, $deskripsi, $harga, $portion, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Menu berhasil diupdate';
        header('Location: ?page=menu');
    } else {
        $_SESSION['error'] = 'Gagal mengupdate menu';
    }
    $stmt->close();
}
?>

<div class="card">
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama Menu</label>
                <input type="text" id="nama" name="nama" value="<?php echo $menu['nama']; ?>" required>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="mentai" <?php echo $menu['kategori'] == 'mentai' ? 'selected' : ''; ?>>Dimsum Mentai</option>
                    <option value="original" <?php echo $menu['kategori'] == 'original' ? 'selected' : ''; ?>>Dimsum Original</option>
                    <option value="paket" <?php echo $menu['kategori'] == 'paket' ? 'selected' : ''; ?>>Paket & Combo</option>
                    <option value="minuman" <?php echo $menu['kategori'] == 'minuman' ? 'selected' : ''; ?>>Minuman</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required><?php echo $menu['deskripsi']; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" min="0" value="<?php echo $menu['harga']; ?>" required>
            </div>
            <div class="form-group">
                <label for="portion">Porsi</label>
                <input type="text" id="portion" name="portion" value="<?php echo $menu['portion']; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="aktif" <?php echo $menu['status'] == 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                <option value="nonaktif" <?php echo $menu['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
            </select>
        </div>
        
        <button type="submit" name="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Update
        </button>
        <a href="?page=menu" class="btn btn-secondary">Batal</a>
    </form>
</div>
