<?php
$wa_admin = '6283167707858';
$wa_links = [];
$wa_customers = [];
$upload_dir = '../../assets/images/promo/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_promosi'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $pesan = $conn->real_escape_string($_POST['pesan']);
    $gambar_url = '';
    
    if (!empty($_FILES['gambar']['name'])) {
        $file = $_FILES['gambar'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $file['error'] === 0) {
            $filename = 'promo_' . time() . '.' . $ext;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $gambar_url = 'http://' . $_SERVER['HTTP_HOST'] . '/dimsum/assets/images/promo/' . $filename;
            }
        }
    } elseif (!empty($_POST['gambar_url'])) {
        $gambar_url = filter_var($_POST['gambar_url'], FILTER_SANITIZE_URL);
        if (!filter_var($gambar_url, FILTER_VALIDATE_URL)) {
            $gambar_url = '';
        }
    }
    
    $customers = $conn->query("SELECT DISTINCT nama_pelanggan, no_wa FROM pesanan WHERE no_wa != '' AND no_wa IS NOT NULL");
    $jumlah = $customers->num_rows;
    
    if ($jumlah > 0) {
        $conn->query("INSERT INTO promosi (judul, jenis, pesan, gambar_url, jumlah_pelanggan, created_at) VALUES ('$judul', '$jenis', '$pesan', '$gambar_url', $jumlah, NOW())");
        
        while ($c = $customers->fetch_assoc()) {
            $no_wa = preg_replace('/[^0-9]/', '', $c['no_wa']);
            if (substr($no_wa, 0, 1) === '0') {
                $no_wa = '62' . substr($no_wa, 1);
            }
            
            $wa_pesan = $pesan;
            if ($gambar_url) {
                $wa_pesan .= "\n\n📷 Lihat Poster Promo:\n$gambar_url";
            }
            $wa_pesan .= "\n\n_Disampaikan oleh Dimsum Mentai_";
            
            $wa_link = "https://wa.me/$no_wa?text=" . urlencode($wa_pesan);
            $wa_links[] = $wa_link;
            $wa_customers[] = $c['nama_pelanggan'];
        }
    }
}

$promo_history = $conn->query("SELECT * FROM promosi ORDER BY created_at DESC LIMIT 10");
$conn->query("CREATE TABLE IF NOT EXISTS promosi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    jenis ENUM('promo', 'event', 'penawaran') NOT NULL,
    pesan TEXT NOT NULL,
    gambar_url VARCHAR(500) DEFAULT '',
    jumlah_pelanggan INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$customers_list = $conn->query("SELECT DISTINCT nama_pelanggan, no_wa, COUNT(*) as total_pesanan, SUM(total_harga) as total_belanja 
    FROM pesanan WHERE no_wa != '' AND no_wa IS NOT NULL GROUP BY no_wa ORDER BY total_belanja DESC");
?>
<style>
.preview-box { background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 12px; padding: 20px; margin-top: 20px; }
.preview-box h4 { color: #495057; margin-bottom: 10px; font-size: 0.9rem; }
.preview-box .preview-content { background: #fff; border-radius: 10px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); max-height: 300px; overflow-y: auto; white-space: pre-wrap; font-family: inherit; }
.telegram-btn { background: #0088cc; color: #fff; }
.telegram-btn:hover { background: #006fa3; }
.gambar-preview { max-width: 300px; max-height: 200px; border-radius: 8px; margin-top: 10px; }
.upload-box { border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.3s; background: #fafafa; }
.upload-box:hover { border-color: #e63946; background: #fff5f5; }
.upload-box input { display: none; }
.upload-icon { font-size: 2rem; color: #ccc; margin-bottom: 10px; }
.or-divider { display: flex; align-items: center; margin: 15px 0; color: #888; }
.or-divider::before, .or-divider::after { content: ''; flex: 1; height: 1px; background: #ddd; }
.or-divider span { padding: 0 15px; }
</style>

<div class="page-header">
    <h1>Promosi</h1>
</div>

<?php if (count($wa_links) > 0): ?>
<div class="alert alert-success" style="margin-bottom: 20px;">
    <i class="fas fa-check-circle"></i> Promo berhasil disimpan! Klik link di bawah untuk mengirim ke pelanggan.
</div>

<div class="card" style="background: #e8f5e9; border: 2px solid #28a745;">
    <div class="card-header" style="background: #28a745; color: #fff; margin: -25px -25px 20px -25px; padding: 15px 25px; border-radius: 12px 12px 0 0;">
        <h3 style="color: #fff;"><i class="fab fa-whatsapp"></i> Kirim Pesan ke <?php echo count($wa_links); ?> Pelanggan</h3>
    </div>
    <p style="margin-bottom: 15px; color: #555;">Klik tombol di bawah untuk membuka WhatsApp. Kirim satu per satu:</p>
    <div style="max-height: 400px; overflow-y: auto;">
        <?php for ($i = 0; $i < count($wa_links); $i++): ?>
        <a href="<?php echo $wa_links[$i]; ?>" target="_blank" class="btn btn-success" style="margin: 5px; display: inline-block;">
            <i class="fab fa-whatsapp"></i> <?php echo htmlspecialchars($wa_customers[$i]); ?>
        </a>
        <?php endfor; ?>
    </div>
    <p style="margin-top: 15px; font-size: 0.85rem; color: #888;">
        <i class="fas fa-info-circle"></i> Tip: Klik tombol di atas secara berurutan. Setiap klik akan membuka WhatsApp dengan pesan + link gambar promo.
    </p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Buat Promo / Event / Penawaran</h3>
    </div>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Jenis</label>
                <select name="jenis" required>
                    <option value="promo">Promo Spesial</option>
                    <option value="event">Event</option>
                    <option value="penawaran">Penawaran</option>
                </select>
            </div>
            <div class="form-group">
                <label>Judul Promo</label>
                <input type="text" name="judul" placeholder="Contoh: Diskon 20%" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Upload Banner</label>
            <div class="upload-box" onclick="document.getElementById('gambar').click()">
                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                <p>Klik untuk upload gambar poster promo</p>
                <small style="color: #888;">Format: JPG, PNG, GIF, WebP (Max 5MB)</small>
                <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)">
                <img id="preview-img" class="gambar-preview" style="display:none;">
            </div>
        </div>
        
        <div class="or-divider"><span>ATAU</span></div>
        
        <div class="form-group">
            <label>Link Gambar (dari IG/website lain)</label>
            <input type="url" name="gambar_url" placeholder="https://example.com/gambar-promo.jpg">
            <small style="color: #888;">Tempel URL gambar dari Instagram, Drive, atau website lainnya</small>
        </div>
        
        <div class="form-group">
            <label>Pesan WhatsApp</label>
            <textarea name="pesan" id="pesan" placeholder="Ketik pesan promo di sini..." required></textarea>
            <small style="color: #888; margin-top: 5px; display: block;">* Pesan akan dikirim bersama link gambar ke semua pelanggan</small>
        </div>
        
        <div class="preview-box">
            <h4><i class="fas fa-eye"></i> Preview Pesan</h4>
            <div class="preview-content" id="previewContent">
                <p style="color: #666; font-style: italic;">Ketik pesan di atas untuk melihat preview...</p>
            </div>
        </div>
        
        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="submit" name="kirim_promosi" class="btn btn-success" onclick="return confirm('Simpan dan kirim promosi ke semua pelanggan?');">
                <i class="fab fa-whatsapp"></i> Kirim ke Semua Pelanggan
            </button>
            <a href="https://web.whatsapp.com" target="_blank" class="btn telegram-btn">
                <i class="fas fa-external-link-alt"></i> Buka WhatsApp Web
            </a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Riwayat Promosi</h3>
    </div>
    <?php if ($promo_history && $promo_history->num_rows > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Jenis</th>
                    <th>Gambar</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($promo = $promo_history->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($promo['judul']); ?></strong></td>
                    <td><span class="status <?php echo $promo['jenis']; ?>"><?php echo ucfirst($promo['jenis']); ?></span></td>
                    <td>
                        <?php if ($promo['gambar_url']): ?>
                        <a href="<?php echo htmlspecialchars($promo['gambar_url']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($promo['gambar_url']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                        </a>
                        <?php else: ?>
                        <span style="color: #888;">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $promo['jumlah_pelanggan']; ?> orang</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($promo['created_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-bullhorn"></i>
        <p>Belum ada promosi yang dibuat</p>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pelanggan</h3>
    </div>
    <?php if ($customers_list && $customers_list->num_rows > 0): ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>WhatsApp</th>
                    <th>Total Pesanan</th>
                    <th>Total Belanja</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $customers_list->fetch_assoc()): 
                    $no_wa = preg_replace('/[^0-9]/', '', $c['no_wa']);
                    if (substr($no_wa, 0, 1) === '0') {
                        $no_wa = '62' . substr($no_wa, 1);
                    }
                    $wa_link = "https://wa.me/$no_wa";
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['nama_pelanggan']); ?></td>
                    <td><?php echo htmlspecialchars($c['no_wa']); ?></td>
                    <td><?php echo $c['total_pesanan']; ?> kali</td>
                    <td>Rp <?php echo number_format($c['total_belanja']); ?></td>
                    <td>
                        <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-sm btn-success">
                            <i class="fab fa-whatsapp"></i> Hubungi
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <i class="fas fa-users"></i>
        <p>Belum ada pelanggan</p>
    </div>
    <?php endif; ?>
</div>

<script>
const textarea = document.getElementById('pesan');
const preview = document.getElementById('previewContent');

if (textarea && preview) {
    textarea.addEventListener('input', function() {
        preview.innerHTML = this.value.replace(/\n/g, '<br>') || '<p style="color: #666; font-style: italic;">Ketik pesan di atas untuk melihat preview...</p>';
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('preview-img');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
