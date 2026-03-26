<?php 
$isInPages = isset($isInPages) ? $isInPages : (strpos($_SERVER['PHP_SELF'], '/pages/') !== false);
$base = $isInPages ? '../' : '';
?>
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Dimsum Mentai</h3>
                <p>Dimsum premium dengan saus mentai signature. Fresh setiap hari, dibuat dari bahan pilihan.</p>
            </div>
            <div class="footer-section">
                <h4>Menu</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base; ?>pages/menu.php">Dimsum Mentai</a></li>
                    <li><a href="<?php echo $base; ?>pages/menu.php">Dimsum Original</a></li>
                    <li><a href="<?php echo $base; ?>pages/menu.php">Paket & Combo</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Navigasi</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base; ?>pages/menu.php">Menu</a></li>
                    <li><a href="<?php echo $base; ?>pages/about.php">Tentang</a></li>
                    <li><a href="<?php echo $base; ?>index.php#contact">Kontak</a></li>
                    <li><a href="<?php echo $base; ?>admin/login.php">Login Admin</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Hubungi Kami</h4>
                <div class="contact-info">
                    <p>📍 Jakarta, Indonesia</p>
                    <p>📱 0812-3456-7890</p>
                    <p>✉️ order@dimsumentai.com</p>
                </div>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Instagram">📸</a>
                    <a href="#" class="social-link" aria-label="WhatsApp">💬</a>
                    <a href="#" class="social-link" aria-label="Tokopedia">🛒</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Dimsum Mentai. All rights reserved.</p>
        </div>
    </div>
</footer>
