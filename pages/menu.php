<?php 
$base = '../';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Dimsum Mentai</title>
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
</head>
<body>
<?php include '../header.php'; ?>
<?php include '../config/database.php'; ?>

<section class="page-header">
    <div class="container">
        <h1>Menu Kami</h1>
        <p>Temukan berbagai pilihan dimsum mentai premium</p>
    </div>
</section>

<main class="menu-page">
    <div class="container">
        <div class="search-filter-section">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari menu...">
                <span class="search-icon">🔍</span>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="mentai">Dimsum Mentai</button>
                <button class="filter-btn" data-filter="original">Dimsum Original</button>
                <button class="filter-btn" data-filter="paket">Paket & Combo</button>
                <button class="filter-btn" data-filter="minuman">Minuman</button>
            </div>
        </div>

        <div id="noResults" class="no-results" style="display: none;">
            <p>Menu tidak ditemukan</p>
        </div>

        <?php
        $kategori = ['mentai' => '🍜 Dimsum Mentai', 'original' => '🥟 Dimsum Original', 'paket' => '🍚 Paket & Combo', 'minuman' => '🥤 Minuman'];
        
        foreach ($kategori as $kat => $judul):
            $menu_list = $conn->query("SELECT * FROM menu WHERE kategori = '$kat' AND status = 'aktif' ORDER BY nama");
            if ($menu_list && $menu_list->num_rows > 0):
        ?>
        
        <section class="menu-category" data-category="<?php echo $kat; ?>">
            <h2 class="category-title"><?php echo $judul; ?></h2>
            <div class="menu-grid-full">
                <?php while($menu = $menu_list->fetch_assoc()): 
                    $gambar = !empty($menu['gambar']) ? $menu['gambar'] : 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=300&h=200&fit=crop';
                ?>
                
                <?php if ($kat == 'minuman'): ?>
                <div class="menu-card-small" data-name="<?php echo strtolower($menu['nama']); ?>" data-price="<?php echo $menu['harga']; ?>">
                    <img src="<?php echo $gambar; ?>" alt="<?php echo $menu['nama']; ?>">
                    <h4><?php echo $menu['nama']; ?></h4>
                    <span class="price">Rp <?php echo number_format($menu['harga']); ?></span>
                    <button class="btn-add" onclick="addToCart('<?php echo addslashes($menu['nama']); ?>', <?php echo $menu['harga']; ?>, <?php echo $menu['id']; ?>)">Pesan</button>
                </div>
                <?php else: ?>
                <div class="menu-card-large" data-name="<?php echo strtolower($menu['nama']); ?>" data-price="<?php echo $menu['harga']; ?>">
                    <div class="menu-img">
                        <img src="<?php echo $gambar; ?>" alt="<?php echo $menu['nama']; ?>">
                    </div>
                    <div class="menu-info">
                        <h3><?php echo $menu['nama']; ?></h3>
                        <p><?php echo $menu['deskripsi']; ?></p>
                        <div class="menu-meta">
                            <span class="price">Rp <?php echo number_format($menu['harga']); ?></span>
                            <span class="portion"><?php echo $menu['portion']; ?></span>
                        </div>
                        <button class="btn-add" onclick="addToCart('<?php echo addslashes($menu['nama']); ?>', <?php echo $menu['harga']; ?>, <?php echo $menu['id']; ?>)">Pesan</button>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php endwhile; ?>
            </div>
        </section>
        
        <?php 
            endif;
        endforeach;
        ?>
    </div>
</main>

<button id="cartBtn" class="cart-btn" onclick="openCart()">
    <span class="cart-icon">🛒</span>
    <span id="cartCount" class="cart-count">0</span>
</button>

<div id="cartModal" class="cart-modal">
    <div class="cart-content">
        <div class="cart-header">
            <h2>🛒 Keranjang Pesanan</h2>
            <button class="close-cart" onclick="closeCart()">&times;</button>
        </div>
        <div id="cartItems" class="cart-items">
            <p class="empty-cart">Keranjang masih kosong</p>
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">Rp 0</span>
            </div>
            <button class="btn-checkout" onclick="openCheckoutModal()">
                <i class="fas fa-shopping-bag"></i> Lanjut ke Pembayaran
            </button>
        </div>
    </div>
</div>

<div id="checkoutModal" class="cart-modal">
    <div class="cart-content">
        <div class="cart-header">
            <h2>💳 Pembayaran</h2>
            <button class="close-cart" onclick="closeCheckoutModal()">&times;</button>
        </div>
        <div class="cart-items">
            <form id="checkoutForm">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="form-group">
                    <label for="no_wa">Nomor WhatsApp</label>
                    <input type="tel" id="no_wa" name="no_wa" required placeholder="08xxxxxxxxxx">
                </div>
                
                <h4 style="margin: 20px 0 10px; color: #333;">Ringkasan Pesanan:</h4>
                <div id="orderSummary"></div>
                
                <div class="cart-total" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <span>Total Bayar:</span>
                    <span id="checkoutTotal" style="color: #e63946; font-size: 1.3rem;">Rp 0</span>
                </div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label for="metode">Metode Pembayaran</label>
                    <select id="metode" name="metode" required onchange="togglePaymentInfo()">
                        <option value="">Pilih metode pembayaran</option>
                        <option value="cash">Bayar di Kasir (Cash)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                
                <div id="paymentInfo" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 15px;"></div>
                
                <button type="button" id="btnPesan" class="btn-checkout" onclick="submitOrder()">
                    <i class="fab fa-whatsapp"></i> Pesan via WhatsApp
                </button>
            </form>
        </div>
    </div>
</div>

<div id="successModal" class="cart-modal">
    <div class="cart-content" style="text-align: center; padding: 40px;">
        <div style="font-size: 4rem; margin-bottom: 20px;">✅</div>
        <h2 style="color: #28a745; margin-bottom: 15px;">Pesanan Berhasil!</h2>
        <p style="color: #666; margin-bottom: 20px;">Pesanan Anda telah terkirim. Mohon tunggu konfirmasi dari admin.</p>
        <div id="orderDetails" style="background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: left; margin-bottom: 20px;"></div>
        <button class="btn-primary" onclick="sendToWhatsApp()" style="padding: 15px 30px; border: none; border-radius: 10px; background: #25D366; color: #fff; cursor: pointer; font-size: 1rem;">
            <i class="fab fa-whatsapp"></i> Kirim via WhatsApp
        </button>
        <br><br>
        <a href="menu.php" style="color: #888; text-decoration: none;">← Kembali ke Menu</a>
    </div>
</div>

<?php include '../footer.php'; ?>

<style>
.cart-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: #e63946;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 5px 20px rgba(230, 57, 70, 0.4);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    transition: transform 0.3s;
}
.cart-btn:hover { transform: scale(1.1); }
.cart-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #333;
    color: #fff;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cart-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1001;
    justify-content: flex-end;
}
.cart-modal.active { display: flex; }
.cart-content {
    width: 100%;
    max-width: 400px;
    background: #fff;
    height: 100%;
    overflow-y: auto;
    animation: slideIn 0.3s ease;
}
@keyframes slideIn {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #e63946;
    color: #fff;
}
.cart-header h2 { margin: 0; font-size: 1.3rem; }
.close-cart {
    background: none;
    border: none;
    color: #fff;
    font-size: 2rem;
    cursor: pointer;
}
.cart-items { padding: 20px; max-height: 60vh; overflow-y: auto; }
.empty-cart { text-align: center; color: #888; padding: 40px 0; }
.cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}
.cart-item-info h4 { margin: 0 0 5px; color: #333; }
.cart-item-info p { margin: 0; color: #e63946; font-weight: 600; }
.cart-item-actions { display: flex; align-items: center; gap: 10px; }
.qty-btn {
    width: 30px;
    height: 30px;
    border: 1px solid #ddd;
    background: #fff;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}
.qty-btn:hover { background: #f0f0f0; }
.remove-btn { background: none; border: none; color: #e63946; cursor: pointer; font-size: 1.2rem; }
.cart-footer { padding: 20px; background: #f9f9f9; border-top: 1px solid #eee; }
.cart-total { display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700; margin-bottom: 20px; color: #333; }
.btn-checkout {
    width: 100%;
    padding: 15px;
    background: #25D366;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
}
.btn-checkout:hover { background: #128C7E; }
.btn-primary {
    background: #e63946;
    color: #fff;
    padding: 15px 30px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 1rem;
}
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #333; }
.form-group input, .form-group select {
    width: 100%;
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
}
.form-group input:focus, .form-group select:focus { outline: none; border-color: #e63946; }
@media (max-width: 768px) {
    .cart-btn { bottom: 15px; right: 15px; width: 55px; height: 55px; }
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesomeesome/6.4.0/css/all.min.css">

<script>
let cart = [];
let orderData = {};

function addToCart(name, price, menuId) {
    const existingItem = cart.find(item => item.name === name);
    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({ name, price, qty: 1, menuId });
    }
    updateCartUI();
    showNotification(`${name} ditambahkan ke keranjang!`);
    openCart();
}

function updateCartUI() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartTotal = document.getElementById('cartTotal');

    cartCount.textContent = cart.reduce((sum, item) => sum + item.qty, 0);

    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="empty-cart">Keranjang masih kosong</p>';
        cartTotal.textContent = 'Rp 0';
        return;
    }

    cartItems.innerHTML = cart.map((item, index) => `
        <div class="cart-item">
            <div class="cart-item-info">
                <h4>${item.name}</h4>
                <p>Rp ${item.price.toLocaleString('id-ID')} x ${item.qty}</p>
            </div>
            <div class="cart-item-actions">
                <button class="qty-btn" onclick="changeQty(${index}, -1)">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${index}, 1)">+</button>
                <button class="remove-btn" onclick="removeFromCart(${index})">🗑️</button>
            </div>
        </div>
    `).join('');

    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
}

function changeQty(index, delta) {
    cart[index].qty += delta;
    if (cart[index].qty <= 0) cart.splice(index, 1);
    updateCartUI();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCartUI();
}

function openCart() {
    document.getElementById('cartModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCart() {
    document.getElementById('cartModal').classList.remove('active');
    document.body.style.overflow = '';
}

function openCheckoutModal() {
    if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    closeCart();
    
    let summary = '';
    let total = 0;
    cart.forEach(item => {
        const subtotal = item.price * item.qty;
        total += subtotal;
        summary += `<div style="display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #eee;">
            <span>${item.name} x${item.qty}</span>
            <span>Rp ${subtotal.toLocaleString('id-ID')}</span>
        </div>`;
    });
    
    document.getElementById('orderSummary').innerHTML = summary;
    document.getElementById('checkoutTotal').textContent = `Rp ${total.toLocaleString('id-ID')}`;
    document.getElementById('checkoutModal').classList.add('active');
    document.body.style.overflow = 'hidden';
    document.getElementById('paymentInfo').style.display = 'none';
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.remove('active');
    document.body.style.overflow = '';
}

function togglePaymentInfo() {
    const metode = document.getElementById('metode').value;
    const infoDiv = document.getElementById('paymentInfo');
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    
    if (metode === 'cash') {
        infoDiv.style.display = 'block';
        infoDiv.innerHTML = '<p><strong>Bayar di Kasir (Cash)</strong></p><p>Pesanan akan disimpan. Silakan bayar langsung di kasir saat mengambil pesanan.</p>';
    } else if (metode === 'transfer') {
        infoDiv.style.display = 'block';
        infoDiv.innerHTML = `<p><strong>Transfer Bank</strong></p>
        <p>Transfer ke salah satu rekening:</p>
        <p><strong>BCA: 1234567890 a.n. Dimsum Mentai</strong></p>
        <p><strong>BRI: 1234567890 a.n. Dimsum Mentai</strong></p>
        <p>Wajib kirim bukti transfer via WhatsApp</p>`;
    } else if (metode === 'qris') {
        infoDiv.style.display = 'block';
        infoDiv.innerHTML = `<p><strong>QRIS</strong></p>
        <p style="text-align:center;">Scan QRIS di bawah ini:</p>
        <div style="text-align:center; margin:15px 0;">
            <img src="../assets/images/qris.svg" alt="QRIS" style="max-width:200px; border:1px solid #ddd; border-radius:10px;">
        </div>
        <p style="font-weight:bold; text-align:center;">Total: Rp ${total.toLocaleString('id-ID')}</p>
        <p style="color:#e63946; text-align:center; font-size:0.9rem;">*Mohon tunjukkan bukti pembayaran saat pengambilan</p>`;
    } else {
        infoDiv.style.display = 'none';
    }
}

function submitOrder() {
    const nama = document.getElementById('nama').value;
    const no_wa = document.getElementById('no_wa').value;
    const metode = document.getElementById('metode').value;
    
    if (!nama || !no_wa || !metode) {
        alert('Mohon lengkapi semua data!');
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const orderNumber = 'DM' + Date.now();
    
    orderData = {
        order_id: orderNumber,
        nama: nama,
        no_wa: no_wa,
        metode: metode,
        total: total,
        items: cart
    };
    
    // Save to server
    fetch('proses_pesanan.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCheckoutModal();
            showSuccessModal();
            cart = [];
            updateCartUI();
        } else {
            alert('Gagal menyimpan pesanan');
        }
    });
}

function showSuccessModal() {
    let details = `
        <p><strong>No. Pesanan:</strong> ${orderData.order_id}</p>
        <p><strong>Nama:</strong> ${orderData.nama}</p>
        <p><strong>WA:</strong> ${orderData.no_wa}</p>
        <p><strong>Pembayaran:</strong> ${orderData.metode.toUpperCase()}</p>
        <hr style="margin: 15px 0;">
        <p><strong>Pesanan:</strong></p>
    `;
    orderData.items.forEach(item => {
        details += `<p style="margin:5px 0;">• ${item.name} x${item.qty} - Rp ${(item.price * item.qty).toLocaleString('id-ID')}</p>`;
    });
    details += `<hr style="margin: 15px 0;"><p><strong>Total: Rp ${orderData.total.toLocaleString('id-ID')}</strong></p>`;
    
    document.getElementById('orderDetails').innerHTML = details;
    document.getElementById('successModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function sendToWhatsApp() {
    let message = `Halo Dimsum Mentai! Saya ingin memesan:\n\n`;
    message += `*No. Pesanan:* ${orderData.order_id}\n`;
    message += `*Nama:* ${orderData.nama}\n`;
    message += `*WA:* ${orderData.no_wa}\n`;
    message += `*Pembayaran:* ${orderData.metode.toUpperCase()}\n\n`;
    message += `*Pesanan:*\n`;
    orderData.items.forEach(item => {
        message += `• ${item.name} x${item.qty} = Rp ${(item.price * item.qty).toLocaleString('id-ID')}\n`;
    });
    message += `\n*Total: Rp ${orderData.total.toLocaleString('id-ID')}*\n\n`;
    message += `Mohon konfirmasi pesanan saya. Terima kasih!`;
    
    window.open(`https://wa.me/6283167707858?text=${encodeURIComponent(message)}`, '_blank');
}

function showNotification(message) {
    const notif = document.createElement('div');
    notif.style.cssText = 'position:fixed;top:80px;right:20px;background:#25D366;color:#fff;padding:15px 25px;border-radius:10px;box-shadow:0 5px 20px rgba(0,0,0,0.2);z-index:1002';
    notif.innerHTML = '✓ ' + message;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2500);
}

// Search & Filter
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const menuCategories = document.querySelectorAll('.menu-category');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterMenu(this.dataset.filter);
        });
    });

    searchInput.addEventListener('input', function () {
        filterMenu(document.querySelector('.filter-btn.active').dataset.filter, this.value.toLowerCase().trim());
    });

    function filterMenu(category = 'all', search = '') {
        menuCategories.forEach(cat => {
            const catMatch = category === 'all' || cat.dataset.category === category;
            const cards = cat.querySelectorAll('.menu-card-large, .menu-card-small');
            let hasVisible = false;

            cards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const show = catMatch && (search === '' || name.includes(search));
                card.style.display = show ? '' : 'none';
                if (show) hasVisible = true;
            });

            cat.style.display = hasVisible ? '' : 'none';
        });
    }
});

document.getElementById('checkoutModal').addEventListener('click', function(e) {
    if (e.target === this) closeCheckoutModal();
});
</script>
</body>
</html>
