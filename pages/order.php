<?php
include '../config/database.php';

$nomor_meja = isset($_GET['meja']) ? $_GET['meja'] : '';

$meja = $conn->query("SELECT * FROM meja WHERE nomor_meja = '$nomor_meja'")->fetch_assoc();

if (!$meja) {
    echo "<script>alert('Meja tidak ditemukan!'); window.location.href='../index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $no_wa = $conn->real_escape_string($_POST['no_wa']);
    $metode = $conn->real_escape_string($_POST['metode']);
    $catatan = $conn->real_escape_string($_POST['catatan']);
    $items_json = isset($_POST['items']) ? $_POST['items'] : '[]';
    $items = json_decode($items_json, true);
    
    if (!$items || count($items) == 0) {
        echo "<script>alert('Pilih menu terlebih dahulu!'); history.back();</script>";
        exit;
    }
    
    $total = 0;
    foreach ($items as $item) {
        $total += $item['harga'] * $item['jumlah'];
    }
    
    $nomor_pesanan = 'DM' . time();
    
    $conn->query("INSERT INTO pesanan (nomor_pesanan, nama_pelanggan, no_wa, nomor_meja, total_harga, metode_pembayaran, status, catatan) 
                   VALUES ('$nomor_pesanan', '$nama', '$no_wa', '$nomor_meja', $total, '$metode', 'menunggu', '$catatan')");
    
    $pesanan_id = $conn->insert_id;
    
    foreach ($items as $item) {
        $menu_id = (int)$item['id'];
        $jumlah = (int)$item['jumlah'];
        $harga = (int)$item['harga'];
        $nama = $conn->real_escape_string($item['nama']);
        $subtotal = $harga * $jumlah;
        $conn->query("INSERT INTO detail_pesanan (pesanan_id, menu_id, jumlah, harga_saat_ini, subtotal) VALUES ($pesanan_id, $menu_id, $jumlah, $harga, $subtotal)");
    }
    
    $conn->query("UPDATE meja SET status = 'terpakai' WHERE nomor_meja = '$nomor_meja'");
    
    $wa_admin = '6283167707858';
    $detail_items = "";
    foreach ($items as $item) {
        $nama_item = isset($item['nama']) ? $item['nama'] : 'Menu';
        $jumlah_item = isset($item['jumlah']) ? $item['jumlah'] : 0;
        $harga_item = isset($item['harga']) ? $item['harga'] : 0;
        $detail_items .= "- {$nama_item} x{$jumlah_item} = Rp " . number_format($harga_item * $jumlah_item) . "\n";
    }
    
    $wa_pesan = "Pesanan Baru #{$nomor_pesanan}\n";
    $wa_pesan .= "Meja: {$nomor_meja}\n";
    $wa_pesan .= "Nama: {$nama}\n";
    $wa_pesan .= "HP: {$no_wa}\n\n";
    $wa_pesan .= $detail_items;
    $wa_pesan .= "\nTotal: Rp " . number_format($total);
    $wa_pesan .= "\nMetode: " . strtoupper($metode);
    
    $wa_link = "https://wa.me/{$wa_admin}?text=" . urlencode($wa_pesan);
    
    echo "<script>
        alert('Pesanan #$nomor_pesanan berhasil! Mohon tunggu konfirmasi via WhatsApp.');
        window.location.href = '$wa_link';
    </script>";
    exit;
}

$menus = $conn->query("SELECT * FROM menu WHERE status = 'aktif'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order - Meja <?php echo $nomor_meja; ?> | Dimsum Mentai</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-header {
            background: #e63946;
            color: #fff;
            padding: 15px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .order-header h1 { font-size: 1.5rem; margin: 0; }
        .order-header p { margin: 5px 0 0; opacity: 0.9; font-size: 0.9rem; }
        .cart-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #e63946;
            color: #fff;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4);
            z-index: 100;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #28a745;
            color: #fff;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 200;
        }
        .cart-modal.show { display: flex; }
        .cart-content {
            background: #fff;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            margin: auto;
            border-radius: 20px 20px 0 0;
            padding: 20px;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-item-info { flex: 1; margin-left: 15px; }
        .cart-item-info h4 { margin: 0; font-size: 1rem; }
        .cart-item-info p { margin: 5px 0 0; color: #e63946; font-weight: 600; }
        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }
        .qty-btn:hover {
            background: #e63946;
            color: #fff;
            border-color: #e63946;
        }
        .form-order {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .form-order input, .form-order select, .form-order textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .form-order input:focus, .form-order select:focus, .form-order textarea:focus {
            border-color: #e63946;
            outline: none;
        }
        .btn-checkout {
            width: 100%;
            padding: 15px;
            background: #e63946;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .close-cart {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="order-header">
        <h1>🍜 Dimsum Mentai</h1>
        <p>Meja <?php echo $nomor_meja; ?></p>
    </div>

    <main class="menu" id="menu" style="padding-top: 20px;">
        <div class="container">
            <h2 class="section-title">Menu</h2>
            <div class="menu-grid" id="menuGrid">
                <?php while ($item = $menus->fetch_assoc()): ?>
                <div class="menu-card" id="menu-card-<?php echo $item['id']; ?>">
                    <img src="<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama']; ?>" class="menu-image">
                    <h3><?php echo $item['nama']; ?></h3>
                    <p><?php echo $item['deskripsi']; ?></p>
                    <span class="price">Rp <?php echo number_format($item['harga']); ?></span>
                    <div class="menu-qty-control" id="menu-qty-<?php echo $item['id']; ?>" style="display: none; margin-top: 10px;">
                        <button class="qty-btn" onclick="quickChangeQty(<?php echo $item['id']; ?>, -1)">-</button>
                        <span id="menu-qty-text-<?php echo $item['id']; ?>" style="min-width: 30px; text-align: center; font-weight: bold;">0</span>
                        <button class="qty-btn" onclick="quickChangeQty(<?php echo $item['id']; ?>, 1)">+</button>
                    </div>
                    <button class="btn btn-primary add-btn" id="add-btn-<?php echo $item['id']; ?>" style="margin-top: 10px; width: 100%;" onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['nama']); ?>', <?php echo $item['harga']; ?>, '<?php echo addslashes($item['gambar']); ?>')">
                        + Tambah
                    </button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>

    <div class="cart-float" onclick="openCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <div class="cart-modal" id="cartModal">
        <div class="cart-content">
            <span class="close-cart" onclick="closeCart()">&times;</span>
            <h2 style="margin-bottom: 20px;">Pesanan Meja <?php echo $nomor_meja; ?></h2>
            <div id="cartItems"></div>
            
            <div class="form-order" id="checkoutForm" style="display: none;">
                <button type="button" class="btn-checkout" onclick="closeCart()" style="background: #888; margin-bottom: 15px;">
                    + Tambah Pesanan Lainnya
                </button>
                <h3>Data Pemesan</h3>
                <form method="POST" action="" id="orderForm" onsubmit="return submitOrder()">
                    <input type="hidden" name="items" id="itemsData" value="">
                    <input type="text" name="nama" placeholder="Nama Pemesan" required>
                    <input type="tel" name="no_wa" placeholder="No. WhatsApp" required>
                    <select name="metode" id="metodeBayar" onchange="showPaymentInfo()" required>
                        <option value="">Pilih Pembayaran</option>
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                    
                    <div id="paymentInfo" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div id="qrisInfo" style="display: none; text-align: center;">
                            <img src="../assets/images/qris.svg" alt="QRIS" style="width: 150px; margin-bottom: 10px;">
                            <p style="font-size: 0.9rem;">Scan QRIS untuk pembayaran</p>
                        </div>
                        <div id="transferInfo" style="display: none;">
                            <p><strong>Transfer ke:</strong></p>
                            <p>Bank BCA - 1234567890<br>a.n. Dimsum Mentai</p>
                        </div>
                    </div>
                    
                    <textarea name="catatan" placeholder="Catatan (opsional)" rows="2"></textarea>
                    <button type="submit" name="checkout" class="btn-checkout" id="checkoutBtn" style="background: #28a745;">
                        Pesan Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function updateMenuCard(itemId) {
            const cartItem = cart.find(c => c.id === itemId);
            const qtyControl = document.getElementById('menu-qty-' + itemId);
            const addBtn = document.getElementById('add-btn-' + itemId);
            const qtyText = document.getElementById('menu-qty-text-' + itemId);
            
            if (cartItem && cartItem.jumlah > 0) {
                qtyControl.style.display = 'flex';
                qtyControl.style.justifyContent = 'center';
                qtyControl.style.gap = '15px';
                qtyControl.style.alignItems = 'center';
                addBtn.style.display = 'none';
                qtyText.textContent = cartItem.jumlah;
            } else {
                qtyControl.style.display = 'none';
                addBtn.style.display = 'block';
            }
        }

        function quickChangeQty(id, delta) {
            const existing = cart.find(c => c.id === id);
            if (existing) {
                existing.jumlah += delta;
                if (existing.jumlah <= 0) {
                    cart = cart.filter(c => c.id !== id);
                }
            }
            updateCart();
            updateMenuCard(id);
        }

        function addToCart(id, nama, harga, gambar) {
            const item = { id: id, nama: nama, harga: harga, gambar: gambar };
            const existing = cart.find(c => c.id === id);
            if (existing) {
                existing.jumlah++;
            } else {
                cart.push({...item, jumlah: 1});
            }
            updateCart();
            updateMenuCard(id);
            openCart();
        }

        function updateCart() {
            const count = cart.reduce((sum, item) => sum + item.jumlah, 0);
            document.getElementById('cartCount').textContent = count;
            
            let html = '';
            let total = 0;
            
            if (cart.length === 0) {
                html = '<p style="text-align: center; color: #888; padding: 30px;">Keranjang kosong</p>';
                document.getElementById('checkoutForm').style.display = 'none';
            } else {
                cart.forEach((item, index) => {
                    const subtotal = item.harga * item.jumlah;
                    total += subtotal;
                    html += `
                        <div class="cart-item">
                            <img src="${item.gambar}" alt="${item.nama}">
                            <div class="cart-item-info">
                                <h4>${item.nama}</h4>
                                <p>Rp ${subtotal.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="qty-control">
                                <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                                <span>${item.jumlah}</span>
                                <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                            </div>
                        </div>
                    `;
                });
                html += `<h3 style="text-align: right; color: #e63946;">Total: Rp ${total.toLocaleString('id-ID')}</h3>`;
                document.getElementById('checkoutForm').style.display = 'block';
            }
            
            document.getElementById('cartItems').innerHTML = html;
        }

        function changeQty(index, delta) {
            const itemId = cart[index].id;
            cart[index].jumlah += delta;
            if (cart[index].jumlah <= 0) {
                cart.splice(index, 1);
            }
            updateCart();
            updateMenuCard(itemId);
        }

        function openCart() {
            document.getElementById('cartModal').classList.add('show');
            document.getElementById('itemsData').value = JSON.stringify(cart);
        }

        function closeCart() {
            document.getElementById('cartModal').classList.remove('show');
            document.getElementById('itemsData').value = JSON.stringify(cart);
        }

        function showPaymentInfo() {
            const metode = document.getElementById('metodeBayar').value;
            const paymentInfo = document.getElementById('paymentInfo');
            const qrisInfo = document.getElementById('qrisInfo');
            const transferInfo = document.getElementById('transferInfo');
            
            if (metode === 'qris') {
                paymentInfo.style.display = 'block';
                qrisInfo.style.display = 'block';
                transferInfo.style.display = 'none';
            } else if (metode === 'transfer') {
                paymentInfo.style.display = 'block';
                qrisInfo.style.display = 'none';
                transferInfo.style.display = 'block';
            } else {
                paymentInfo.style.display = 'none';
            }
        }

        function submitOrder() {
            if (cart.length === 0) {
                alert('Pilih menu terlebih dahulu!');
                return false;
            }
            
            document.getElementById('itemsData').value = JSON.stringify(cart);
            
            const nama = document.querySelector('input[name="nama"]').value;
            const no_wa = document.querySelector('input[name="no_wa"]').value;
            const metode = document.getElementById('metodeBayar').value;
            
            if (!nama || !no_wa || !metode) {
                alert('Lengkapi data pemesan!');
                return false;
            }
            
            return true;
        }

        document.getElementById('cartModal').addEventListener('click', function(e) {
            if (e.target === this) closeCart();
        });
    </script>
</body>
</html>
