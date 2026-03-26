<?php
$message = '';
$message_type = 'success';

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM slider WHERE id = $id");
    $message = "Slider berhasil dihapus!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_slider'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $gambar_url = $conn->real_escape_string($_POST['gambar_url']);
    $urutan = (int)$_POST['urutan'];
    $status = $conn->real_escape_string($_POST['status']);
    
    if (!empty($_FILES['gambar']['name'])) {
        $file = $_FILES['gambar'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($ext, $allowed) && $file['error'] === 0) {
            $filename = 'slider_' . time() . '.' . $ext;
            $destination = '../../assets/images/slider/' . $filename;
            
            if (!is_dir('../../assets/images/slider/')) {
                mkdir('../../assets/images/slider/', 0755, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $gambar_url = 'http://' . $_SERVER['HTTP_HOST'] . '/dimsum/assets/images/slider/' . $filename;
            }
        }
    }
    
    if (!empty($gambar_url)) {
        $conn->query("INSERT INTO slider (nama, gambar, urutan, status) VALUES ('$nama', '$gambar_url', $urutan, '$status')");
        $message = "Slider berhasil ditambahkan!";
    }
}

$sliders = $conn->query("SELECT * FROM slider ORDER BY urutan ASC, id DESC");

$conn->query("CREATE TABLE IF NOT EXISTS slider (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) DEFAULT '',
    gambar VARCHAR(500) NOT NULL,
    urutan INT DEFAULT 0,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
?>
<style>
.upload-preview { max-width: 200px; max-height: 120px; margin-top: 10px; border-radius: 8px; }
</style>

<div class="page-header">
    <h1>Manajemen Slider</h1>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $message_type; ?>">
    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Tambah Slider Baru</h3>
    </div>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Nama Slider</label>
                <input type="text" name="nama" placeholder="Contoh: Promo Akhir Bulan" required>
            </div>
            <div class="form-group">
                <label>Urutan Tampilan</label>
                <input type="number" name="urutan" value="1" min="1">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Upload Gambar</label>
                <input type="file" name="gambar" accept="image/*" onchange="previewSlider(this)">
                <small style="color: #888;">Format: JPG, PNG, GIF, WebP. Rekomendasi: 1200x600px</small>
                <img id="slider-preview" class="upload-preview" style="display:none;">
            </div>
            <div class="form-group">
                <label>Atau Masukkan URL Gambar</label>
                <input type="url" name="gambar_url" placeholder="https://example.com/gambar.jpg">
                <small style="color: #888;">Dari Instagram, Drive, atau website lain</small>
            </div>
        </div>
        
        <button type="submit" name="simpan_slider" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Slider
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Slider</h3>
    </div>
    <?php if ($sliders->num_rows > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $sliders->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($s['gambar']); ?>" style="width: 150px; height: 80px; object-fit: cover; border-radius: 5px;">
                    </td>
                    <td><?php echo htmlspecialchars($s['nama'] ?: '-'); ?></td>
                    <td><?php echo $s['urutan']; ?></td>
                    <td>
                        <span class="status <?php echo $s['status']; ?>">
                            <?php echo $s['status'] === 'aktif' ? 'Aktif' : 'Nonaktif'; ?>
                        </span>
                    </td>
                    <td>
                        <a href="?page=slider&hapus=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus slider ini?');">
                            <i class="fas fa-trash"></i> Hapus
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-images"></i>
        <p>Belum ada slider. Tambahkan slider baru di atas.</p>
    </div>
    <?php endif; ?>
</div>

<script>
function previewSlider(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('slider-preview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
