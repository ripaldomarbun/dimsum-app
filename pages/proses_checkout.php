<?php
include '../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$orderNumber = 'DM' . date('YmdHis') . rand(100, 999);
$metode = $data['metode'];
$status = 'menunggu';

$stmt = $conn->prepare("INSERT INTO pesanan (nomor_pesanan, nama_pelanggan, no_wa, total_harga, metode_pembayaran, status) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiss", $orderNumber, $data['nama'], $data['no_wa'], $data['total'], $metode, $status);
$stmt->execute();
$pesananId = $stmt->insert_id;
$stmt->close();

foreach ($data['items'] as $item) {
    $stmt = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_ini, subtotal) VALUES (?, ?, ?, ?, ?)");
    $subtotal = $item['price'] * $item['qty'];
    $stmt->bind_param("iiiid", $pesananId, $item['menuId'], $item['qty'], $item['price'], $subtotal);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['success' => true, 'order_id' => $orderNumber]);
?>