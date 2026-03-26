<div class="page-header">
    <h1>Dashboard</h1>
</div>

<?php
// Get stats
$menu_count = $conn->query("SELECT COUNT(*) as total FROM menu")->fetch_assoc()['total'];
$pesanan_count = $conn->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];
$pesanan_pending = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE status = 'menunggu'")->fetch_assoc()['total'];
$kontak_baru = $conn->query("SELECT COUNT(*) as total FROM kontak WHERE status = 'baru'")->fetch_assoc()['total'];
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon menu">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="stat-info">
            <h4>Total Menu</h4>
            <h2><?php echo $menu_count; ?></h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pesanan">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h4>Total Pesanan</h4>
            <h2><?php echo $pesanan_count; ?></h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h4>Pesanan Pending</h4>
            <h2><?php echo $pesanan_pending; ?></h2>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon kontak">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h4>Kontak Baru</h4>
            <h2><?php echo $kontak_baru; ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Pesanan Terbaru</h3>
        <a href="?page=pesanan" class="btn btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_orders = $conn->query("SELECT * FROM pesanan ORDER BY tanggal_pesanan DESC LIMIT 5");
                if ($recent_orders->num_rows > 0):
                    while($order = $recent_orders->fetch_assoc()):
                ?>
                <tr>
                    <td>#<?php echo $order['nomor_pesanan']; ?></td>
                    <td><?php echo $order['nama_pelanggan']; ?></td>
                    <td>Rp <?php echo number_format($order['total_harga']); ?></td>
                    <td><span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #888;">Belum ada pesanan</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
