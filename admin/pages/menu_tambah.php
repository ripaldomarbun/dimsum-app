<div class="page-header">
    <h1>Tambah Menu</h1>
</div>

<?php
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $portion = $_POST['portion'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO menu (nama, kategori, deskripsi, harga, `portion`, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $nama, $kategori, $deskripsi, $harga, $portion, $status);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Menu berhasil ditambahkan';
        header('Location: ?page=menu');
    } else {
        $_SESSION['error'] = 'Gagal menambahkan menu';
    }
    $stmt->close();
}
?>

<div class="card">
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label for="nama">Nama Menu</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <select id="kategori" name="kategori" required>
                    <option value="mentai">Dimsum Mentai</option>
                    <option value="original">Dimsum Original</option>
                    <option value="paket">Paket & Combo</option>
                    <option value="minuman">Minuman</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" id="harga" name="harga" min="0" required>
            </div>
            <div class="form-group">
                <label for="portion">Porsi</label>
                <input type="text" id="portion" name="portion" placeholder="Contoh: 5 pcs">
            </div>
        </div>
        
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        
        <button type="submit" name="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
        <a href="?page=menu" class="btn btn-secondary">Batal</a>
    </form>
</div>
