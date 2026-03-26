<div class="page-header">
    <h1>Pesanan</h1>
</div>

<?php
if (isset($_POST['update_status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];
    
    // Get order info before update
    $order_result = $conn->query("SELECT * FROM pesanan WHERE id = $id");
    if ($order_result && $order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        $nomor_pesanan = $order['nomor_pesanan'];
        $nama_pelanggan = $order['nama_pelanggan'];
        $no_wa = $order['no_wa'];
        $total = $order['total_harga'];
        $metode = strtoupper($order['metode_pembayaran']);
        
        // Update status
        $conn->query("UPDATE pesanan SET status='$status' WHERE id=$id");
        
        // If order completed or cancelled, free up the table
        if (($status === 'selesai' || $status === 'dibatalkan') && !empty($order['nomor_meja'])) {
            $conn->query("UPDATE meja SET status = 'tersedia' WHERE nomor_meja = '" . $order['nomor_meja'] . "'");
        }
        
        // Status message
        $status_messages = [
            'menunggu' => 'Pesanan Anda sedang menunggu konfirmasi dari kami.',
            'dikonfirmasi' => 'Yeay! Pesanan Anda telah dikonfirmasi. Kami sedang mempersiapkan pesanan Anda.',
            'diproses' => 'Pesanan Anda sedang diproses. Harap tunggu sebentar ya!',
            'selesai' => 'Pesanan Anda sudah selesai! Silakan diambil di tempat kami. Terima kasih!',
            'dibatalkan' => 'Mohon maaf, pesanan Anda telah dibatalkan. Jika ada pertanyaan, silakan hubungi kami.'
        ];
        
        $status_titles = [
            'menunggu' => 'Pesanan Menunggu',
            'dikonfirmasi' => 'Pesanan Dikonfirmasi',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Pesanan Selesai!',
            'dibatalkan' => 'Pesanan Dibatalkan'
        ];
        
        // Create WhatsApp message
        $wa_message = "Halo " . $nama_pelanggan . "!\n\n";
        $wa_message .= "Update Pesanan #" . $nomor_pesanan . "\n\n";
        $wa_message .= $status_titles[$status] . "\n\n";
        $wa_message .= $status_messages[$status] . "\n\n";
        $wa_message .= "------------------------------\n";
        $wa_message .= "Total: Rp " . number_format($total) . "\n";
        $wa_message .= "Pembayaran: " . $metode . "\n";
        $wa_message .= "------------------------------\n\n";
        $wa_message .= "Terima kasih sudah memesan di Dimsum Mentai!";
        
        // Format WhatsApp URL
        $wa_number = preg_replace('/^0/', '62', $no_wa);
        $wa_url = "https://wa.me/" . $wa_number . "?text=" . urlencode($wa_message);
        
        // Auto redirect to WhatsApp
        echo '<script>window.location.href = "' . $wa_url . '";</script>';
        echo '<div class="alert alert-success">';
        echo 'Status diupdate! Mengirim notifikasi WA...<br>';
        echo '<a href="' . $wa_url . '" target="_blank">Klik di sini jika tidak dialihkan</a>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-error">Pesanan tidak ditemukan</div>';
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3>Daftar Pesanan</h3>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Pesanan</th>
                    <th>Meja</th>
                    <th>Pelanggan</th>
                    <th>No. WA</th>
                    <th>Pembayaran</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $orders = $conn->query("SELECT * FROM pesanan ORDER BY tanggal_pesanan DESC");
                if ($orders && $orders->num_rows > 0):
                    while($order = $orders->fetch_assoc()):
                    
                    $metode_label = [
                        'cash' => 'Cash',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS'
                    ];
                    $metode = isset($metode_label[$order['metode_pembayaran']]) ? $metode_label[$order['metode_pembayaran']] : $order['metode_pembayaran'];
                ?>
                <tr>
                    <td>#<?php echo $order['nomor_pesanan']; ?></td>
                    <td>
                        <?php if ($order['nomor_meja']): ?>
                        <span style="background: #e63946; color: #fff; padding: 3px 8px; border-radius: 5px; font-weight: bold;">
                            <?php echo $order['nomor_meja']; ?>
                        </span>
                        <?php else: ?>
                        <span style="color: #888;">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $order['nama_pelanggan']; ?></td>
                    <td>
                        <?php echo $order['no_wa']; ?>
                        <br>
                        <a href="https://wa.me/<?php echo preg_replace('/^0/', '62', $order['no_wa']); ?>" target="_blank" style="color:#25D366; font-size:0.8rem;">Hubungi</a>
                    </td>
                    <td><?php echo $metode; ?></td>
                    <td>Rp <?php echo number_format($order['total_harga']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                            <select name="status" onchange="this.form.submit()" style="padding:5px; border-radius:5px; border:1px solid #ddd;">
                                <option value="menunggu" <?php echo $order['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                <option value="dikonfirmasi" <?php echo $order['status'] == 'dikonfirmasi' ? 'selected' : ''; ?>>Dikonfirmasi</option>
                                <option value="diproses" <?php echo $order['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                <option value="selesai" <?php echo $order['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="dibatalkan" <?php echo $order['status'] == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                    <td>
                        <a href="?page=pesanan_detail&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">Detail</a>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #888;">Belum ada pesanan</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
