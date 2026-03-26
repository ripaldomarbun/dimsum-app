<?php 
$currentPage = basename($_SERVER['PHP_SELF']);
$isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
$base = $isInPages ? '../' : '';
?>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="<?php echo $base; ?>index.php" style="text-decoration: none;">
                    <h1>🍜 Dimsum Mentai</h1>
                </a>
            </div>
            <nav class="nav" id="mainNav">
                <ul class="nav-list">
                    <li><a href="<?php echo $base; ?>index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo $base; ?>pages/menu.php" class="nav-link <?php echo ($currentPage == 'menu.php') ? 'active' : ''; ?>">Menu</a></li>
                    <li><a href="<?php echo $base; ?>pages/about.php" class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">Tentang</a></li>
                    <li><a href="<?php echo $base; ?>index.php#contact" class="nav-link">Kontak</a></li>
                </ul>
            </nav>
            <div class="header-cta">
                <a href="<?php echo $base; ?>pages/menu.php" class="btn-order">Pesan Sekarang</a>
            </div>
            <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>
<style>
@media (max-width: 768px) {
    #mainNav {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    #mainNav.active {
        display: block;
    }
    #mainNav .nav-list {
        flex-direction: column;
        gap: 0;
        list-style: none;
        padding: 15px;
        margin: 0;
    }
    #mainNav .nav-link {
        display: block;
        padding: 12px 0;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #eee;
    }
    .header-cta {
        display: none;
    }
    .hamburger {
        display: flex !important;
    }
}
</style>
<script>
document.getElementById('hamburgerBtn').addEventListener('click', function() {
    document.getElementById('mainNav').classList.toggle('active');
    this.classList.toggle('active');
});
</script>
