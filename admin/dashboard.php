<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Dimsum Mentai</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
        header('Location: login.php');
        exit;
    }
    include '../config/database.php';
    ?>
    
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🍜 Dimsum Mentai</h2>
                <p>Admin Panel</p>
            </div>
            <nav class="sidebar-nav">
                <a href="?page=dashboard" class="nav-item active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="?page=menu" class="nav-item">
                    <i class="fas fa-utensils"></i> Kelola Menu
                </a>
                <a href="?page=pesanan" class="nav-item">
                    <i class="fas fa-shopping-cart"></i> Pesanan
                </a>
                <a href="?page=kontak" class="nav-item">
                    <i class="fas fa-envelope"></i> Kontak
                </a>
                <a href="?page=laporan" class="nav-item">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
                <a href="?page=slider" class="nav-item">
                    <i class="fas fa-images"></i> Slider
                </a>
                <a href="?page=meja" class="nav-item">
                    <i class="fas fa-chair"></i> Meja
                </a>
                <a href="?page=promosi" class="nav-item">
                    <i class="fas fa-bullhorn"></i> Promosi
                </a>
                <a href="logout.php" class="nav-item logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

            switch($page):
                case 'dashboard':
                    include 'pages/dashboard.php';
                    break;
                case 'menu':
                    include 'pages/menu.php';
                    break;
                case 'menu_tambah':
                    include 'pages/menu_tambah.php';
                    break;
                case 'menu_edit':
                    include 'pages/menu_edit.php';
                    break;
                case 'pesanan':
                    include 'pages/pesanan.php';
                    break;
                case 'pesanan_detail':
                    include 'pages/pesanan_detail.php';
                    break;
                case 'kontak':
                    include 'pages/kontak.php';
                    break;
                case 'laporan':
                    include 'pages/laporan.php';
                    break;
                case 'promosi':
                    include 'pages/promosi.php';
                    break;
                case 'slider':
                    include 'pages/slider.php';
                    break;
                case 'meja':
                    include 'pages/meja.php';
                    break;
                default:
                    include 'pages/dashboard.php';
            endswitch;
            ?>
        </main>
    </div>

    <script>
        // Active menu
        const currentPage = new URLSearchParams(window.location.search).get('page') || 'dashboard';
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.getAttribute('href').includes('page=' + currentPage) || 
                (currentPage === 'dashboard' && item.getAttribute('href').includes('dashboard'))) {
                item.classList.add('active');
            }
        });
    </script>
</body>

</html>
