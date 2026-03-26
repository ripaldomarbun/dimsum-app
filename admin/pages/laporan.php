<?php
// Get statistics
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$month_start = date('Y-m-01');
$year_start = date('Y-01-01');

// Today's sales (only completed orders)
$today_sales = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE DATE(tanggal_pesanan) = '$today' AND status = 'selesai'")->fetch_assoc();

// This week's sales (only completed orders)
$week_sales = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE DATE(tanggal_pesanan) >= '$week_start' AND status = 'selesai'")->fetch_assoc();

// This month's sales (only completed orders)
$month_sales = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE DATE(tanggal_pesanan) >= '$month_start' AND status = 'selesai'")->fetch_assoc();

// This year's sales (only completed orders)
$year_sales = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE DATE(tanggal_pesanan) >= '$year_start' AND status = 'selesai'")->fetch_assoc();

// All time sales (only completed orders)
$all_sales = $conn->query("SELECT COUNT(*) as total, COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE status = 'selesai'")->fetch_assoc();

// Sales by payment method (only completed orders)
$payment_stats = $conn->query("SELECT metode_pembayaran, COUNT(*) as total, SUM(total_harga) as amount FROM pesanan WHERE status = 'selesai' GROUP BY metode_pembayaran")->fetch_all(MYSQLI_ASSOC);

// Sales by status
$status_stats = $conn->query("SELECT status, COUNT(*) as total FROM pesanan GROUP BY status")->fetch_all(MYSQLI_ASSOC);

// Last 7 days sales data (only completed orders)
$last_7_days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days"));
    $day_sales = $conn->query("SELECT COALESCE(SUM(total_harga), 0) as amount FROM pesanan WHERE DATE(tanggal_pesanan) = '$date' AND status = 'selesai'")->fetch_assoc();
    $last_7_days[] = [
        'day' => $day_name,
        'date' => $date,
        'amount' => $day_sales['amount']
    ];
}

// Last 7 days orders count (only completed orders)
$last_7_orders = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_orders = $conn->query("SELECT COUNT(*) as total FROM pesanan WHERE DATE(tanggal_pesanan) = '$date' AND status = 'selesai'")->fetch_assoc();
    $last_7_orders[] = $day_orders['total'];
}

// Top selling items (only completed orders)
$top_orders = $conn->query("SELECT p.*, COUNT(dp.id) as item_count 
    FROM pesanan p 
    LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id 
    WHERE p.status = 'selesai' 
    GROUP BY p.id 
    ORDER BY p.total_harga DESC 
    LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Recent transactions (only completed orders)
$recent_transactions = $conn->query("SELECT * FROM pesanan WHERE status = 'selesai' ORDER BY tanggal_pesanan DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
?>

<div class="page-header">
    <h1>Laporan Penjualan</h1>
</div>

<!-- Summary Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:#e3f2fd; color:#1976d2;">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <h4>Hari Ini</h4>
            <h2>Rp <?php echo number_format($today_sales['amount']); ?></h2>
            <p style="color:#888; font-size:0.85rem;"><?php echo $today_sales['total']; ?> pesanan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fff3e0; color:#f57c00;">
            <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-info">
            <h4>Minggu Ini</h4>
            <h2>Rp <?php echo number_format($week_sales['amount']); ?></h2>
            <p style="color:#888; font-size:0.85rem;"><?php echo $week_sales['total']; ?> pesanan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#f3e5f5; color:#7b1fa2;">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="stat-info">
            <h4>Bulan Ini</h4>
            <h2>Rp <?php echo number_format($month_sales['amount']); ?></h2>
            <p style="color:#888; font-size:0.85rem;"><?php echo $month_sales['total']; ?> pesanan</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#e8f5e9; color:#388e3c;">
            <i class="fas fa-calendar"></i>
        </div>
        <div class="stat-info">
            <h4>Tahun Ini</h4>
            <h2>Rp <?php echo number_format($year_sales['amount']); ?></h2>
            <p style="color:#888; font-size:0.85rem;"><?php echo $year_sales['total']; ?> pesanan</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>📊 Grafik Penjualan 7 Hari Terakhir</h3>
    </div>
    <div style="padding: 20px;">
        <canvas id="salesChart" height="100"></canvas>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
    <div class="card">
        <div class="card-header">
            <h3>💳 Penjualan per Metode Pembayaran</h3>
        </div>
        <div style="padding: 20px;">
            <canvas id="paymentChart" height="200"></canvas>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>📈 Status Pesanan</h3>
        </div>
        <div style="padding: 20px;">
            <canvas id="statusChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h3>📋 Transaksi Terakhir</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Pembayaran</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_transactions as $trx): 
                    $metode = ['cash' => 'Cash', 'transfer' => 'Transfer', 'qris' => 'QRIS'];
                ?>
                <tr>
                    <td>#<?php echo $trx['nomor_pesanan']; ?></td>
                    <td><?php echo $trx['nama_pelanggan']; ?></td>
                    <td><?php echo isset($metode[$trx['metode_pembayaran']]) ? $metode[$trx['metode_pembayaran']] : '-'; ?></td>
                    <td>Rp <?php echo number_format($trx['total_harga']); ?></td>
                    <td><span class="status <?php echo $trx['status']; ?>"><?php echo ucfirst($trx['status']); ?></span></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($trx['tanggal_pesanan'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card" style="margin-top: 20px; text-align: center; padding: 40px;">
    <h3>💰 Total Penjualan Semua Waktu</h3>
    <h1 style="color: #28a745; font-size: 3rem; margin: 20px 0;">Rp <?php echo number_format($all_sales['amount']); ?></h1>
    <p style="color: #888;"><?php echo $all_sales['total']; ?> transaksi berhasil</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sales Chart - Line
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($last_7_days, 'day')); ?>,
        datasets: [{
            label: 'Penjualan (Rp)',
            data: <?php echo json_encode(array_column($last_7_days, 'amount')); ?>,
            borderColor: '#e63946',
            backgroundColor: 'rgba(230, 57, 70, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Payment Method Chart - Doughnut
const paymentLabels = <?php echo json_encode(array_column($payment_stats, 'metode_pembayaran')); ?>;
const paymentData = <?php echo json_encode(array_column($payment_stats, 'amount')); ?>;
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: paymentLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
        datasets: [{
            data: paymentData,
            backgroundColor: ['#28a745', '#1976d2', '#f57c00']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Status Chart - Bar
const statusLabels = <?php echo json_encode(array_column($status_stats, 'status')); ?>;
const statusData = <?php echo json_encode(array_column($status_stats, 'total')); ?>;
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'bar',
    data: {
        labels: statusLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
        datasets: [{
            data: statusData,
            backgroundColor: ['#fff3cd', '#cce5ff', '#d1ecf1', '#d4edda', '#f8d7da']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
