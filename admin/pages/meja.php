<?php
$message = '';

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM meja WHERE id = $id");
    $message = "Meja berhasil dihapus!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_meja'])) {
    $nomor = $conn->real_escape_string($_POST['nomor_meja']);
    $conn->query("INSERT INTO meja (nomor_meja) VALUES ('$nomor')");
    $message = "Meja berhasil ditambahkan!";
}

if (isset($_GET['reset'])) {
    $conn->query("UPDATE meja SET status = 'tersedia'");
    $message = "Semua meja di-reset ke tersedia!";
}

$mejas = $conn->query("SELECT * FROM meja ORDER BY CAST(SUBSTRING(nomor_meja, 2) AS UNSIGNED) ASC");
$total_meja = $mejas->num_rows;
$meja_tersedia = $conn->query("SELECT COUNT(*) as cnt FROM meja WHERE status = 'tersedia'")->fetch_assoc()['cnt'];
$meja_terpakai = $conn->query("SELECT COUNT(*) as cnt FROM meja WHERE status = 'terpakai'")->fetch_assoc()['cnt'];
?>
<style>
.meja-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 20px;
}
.meja-card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.meja-card.tersedia {
    border: 2px solid #28a745;
}
.meja-card.terpakai {
    border: 2px solid #dc3545;
    background: #fff5f5;
}
.meja-nomor {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}
.meja-status {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
}
.meja-status.tersedia {
    background: #d4edda;
    color: #155724;
}
.meja-status.terpakai {
    background: #f8d7da;
    color: #721c24;
}
.qr-btn {
    margin-top: 10px;
    padding: 8px 15px;
    background: #e63946;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 0.8rem;
}
.qr-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}
.qr-modal.show {
    display: flex;
}
.qr-modal-content {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
    max-width: 350px;
}
.qr-modal-content img {
    width: 250px;
    height: 250px;
    margin: 20px 0;
}
.close-modal {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #fff;
    font-size: 2rem;
    cursor: pointer;
}
.stats-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}
.stat-box {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    flex: 1;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.stat-box h3 { color: #888; font-size: 0.9rem; }
.stat-box span { font-size: 2rem; font-weight: bold; color: #333; }
</style>

<div class="page-header">
    <h1>Manajemen Meja</h1>
    <div>
        <a href="?page=meja&reset=1" class="btn btn-warning" onclick="return confirm('Reset semua meja ke tersedia?')">
            <i class="fas fa-sync"></i> Reset Semua
        </a>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="stats-row">
    <div class="stat-box">
        <h3>Total Meja</h3>
        <span><?php echo $total_meja; ?></span>
    </div>
    <div class="stat-box">
        <h3>Tersedia</h3>
        <span style="color: #28a745;"><?php echo $meja_tersedia; ?></span>
    </div>
    <div class="stat-box">
        <h3>Terpakai</h3>
        <span style="color: #dc3545;"><?php echo $meja_terpakai; ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Meja</h3>
    </div>
    
    <div class="meja-grid">
        <?php while ($meja = $mejas->fetch_assoc()): 
            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode("http://" . $_SERVER['HTTP_HOST'] . "/dimsum/pages/order.php?meja=" . $meja['nomor_meja']);
        ?>
        <div class="meja-card <?php echo $meja['status']; ?>">
            <div class="meja-nomor"><?php echo $meja['nomor_meja']; ?></div>
            <span class="meja-status <?php echo $meja['status']; ?>">
                <?php echo $meja['status'] === 'tersedia' ? 'Tersedia' : 'Terpakai'; ?>
            </span>
            <br>
            <button class="qr-btn" onclick="showQR('<?php echo $meja['nomor_meja']; ?>', '<?php echo $qr_url; ?>')">
                <i class="fas fa-qrcode"></i> QR Code
            </button>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Tambah Meja Baru</h3>
    </div>
    <form method="POST" action="" style="display: flex; gap: 10px; align-items: flex-end;">
        <div class="form-group" style="margin: 0; flex: 1;">
            <label>Nomor Meja</label>
            <input type="text" name="nomor_meja" placeholder="M15" required>
        </div>
        <button type="submit" name="tambah_meja" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </form>
</div>

<div class="qr-modal" id="qrModal">
    <span class="close-modal" onclick="closeQR()">&times;</span>
    <div class="qr-modal-content">
        <h3>QR Code Meja <span id="modalMeja"></span></h3>
        <img id="qrImage" src="" alt="QR Code">
        <p style="color: #666; font-size: 0.9rem;">Scan untuk memesan</p>
        <a id="downloadQR" href="" download class="btn btn-primary" style="margin-top: 15px;">
            <i class="fas fa-download"></i> Download QR
        </a>
    </div>
</div>

<script>
function showQR(meja, qrUrl) {
    document.getElementById('modalMeja').textContent = meja;
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('downloadQR').href = qrUrl;
    document.getElementById('qrModal').classList.add('show');
}

function closeQR() {
    document.getElementById('qrModal').classList.remove('show');
}

document.getElementById('qrModal').addEventListener('click', function(e) {
    if (e.target === this) closeQR();
});
</script>
