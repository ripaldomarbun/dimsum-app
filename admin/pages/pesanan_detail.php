<div class="page-header">
    <h1>Detail Pesanan</h1>
    <a href="?page=pesanan" class="btn btn-primary">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<?php
$id = $_GET['id'];
$pesanan = $conn->query("SELECT * FROM pesanan WHERE id = $id")->fetch_assoc();

if (!$pesanan) {
    header('Location: ?page=pesanan');
    exit;
}

$detail = $conn->query("SELECT dp.*, m.nama FROM detail_pesanan dp 
                        JOIN menu m ON dp.menu_id = m.id 
                        WHERE dp.pesanan_id = $id");
?>

<div class="card">
    <div class="card-header">
        <h3>Pesanan #<?php echo $pesanan['nomor_pesanan']; ?></h3>
        <span class="status <?php echo $pesanan['status']; ?>"><?php echo ucfirst($pesanan['status']); ?></span>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div>
            <h4 style="color: #888; margin-bottom: 5px;">Nama Pelanggan</h4>
            <p><?php echo $pesanan['nama_pelanggan']; ?></p>
        </div>
        <div>
            <h4 style="color: #888; margin-bottom: 5px;">No. WhatsApp</h4>
            <p><?php echo $pesanan['no_wa']; ?></p>
        </div>
        <div>
            <h4 style="color: #888; margin-bottom: 5px;">Metode Pembayaran</h4>
            <p><?php 
                $metode_label = ['cash' => '💵 Cash', 'transfer' => '🏦 Transfer', 'qris' => '📱 QRIS'];
                echo isset($metode_label[$pesanan['metode_pembayaran']]) ? $metode_label[$pesanan['metode_pembayaran']] : $pesanan['metode_pembayaran'];
            ?></p>
        </div>
        <div>
            <h4 style="color: #888; margin-bottom: 5px;">Tanggal Pesanan</h4>
            <p><?php echo date('d/m/Y H:i', strtotime($pesanan['tanggal_pesanan'])); ?></p>
        </div>
    </div>
    
    <h4 style="margin-bottom: 15px;">Detail Pesanan</h4>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $detail->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $item['nama']; ?></td>
                    <td>Rp <?php echo number_format($item['harga_saat_ini']); ?></td>
                    <td><?php echo $item['jumlah']; ?></td>
                    <td>Rp <?php echo number_format($item['subtotal']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                    <td style="font-weight: bold;">Rp <?php echo number_format($pesanan['total_harga']); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
