<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dimsum Mentai - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <?php include 'config/database.php'; ?>

    <section class="hero-slider" id="home">
        <div class="slider-container">
            <?php
            $slider_items = $conn->query("SELECT * FROM slider WHERE status = 'aktif' ORDER BY urutan ASC, id DESC LIMIT 10");
            $slides = [];
            while ($item = $slider_items->fetch_assoc()) {
                $slides[] = $item;
            }
            if (count($slides) == 0) {
                $slides = [
                    ['gambar' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=1200&h=600&fit=crop', 'nama' => 'Dimsum Mentai'],
                    ['gambar' => 'https://images.unsplash.com/photo-1541336032412-2048a678540d?w=1200&h=600&fit=crop', 'nama' => 'Dimsum Mozza'],
                    ['gambar' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?w=1200&h=600&fit=crop', 'nama' => 'Dimsum Crispy']
                ];
            }
            foreach($slides as $i => $slide):
            ?>
            <div class="slide <?php echo $i === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo $slide['gambar']; ?>')">
                <div class="slide-content">
                    <h2><?php echo $slide['nama'] ?? 'Dimsum Mentai Premium'; ?></h2>
                    <a href="pages/menu.php" class="btn btn-primary">Lihat Menu</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="slider-nav">
            <?php foreach($slides as $i => $slide): ?>
            <span class="slider-dot <?php echo $i === 0 ? 'active' : ''; ?>" data-slide="<?php echo $i; ?>"></span>
            <?php endforeach; ?>
        </div>
        <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
    </section>

    <style>
    .hero-slider {
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    .slider-container {
        position: relative;
        height: 100%;
    }
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slide::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5));
    }
    .slide.active {
        opacity: 1;
    }
    .slide-content {
        position: relative;
        z-index: 1;
        text-align: center;
        color: #fff;
    }
    .slide-content h2 {
        font-size: 3rem;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    .slider-nav {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }
    .slider-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
    }
    .slider-dot.active {
        background: #fff;
        transform: scale(1.2);
    }
    .slider-prev, .slider-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.3);
        border: none;
        color: #fff;
        font-size: 1.5rem;
        padding: 15px;
        cursor: pointer;
        z-index: 10;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s;
    }
    .slider-prev:hover, .slider-next:hover {
        background: rgba(255,255,255,0.6);
    }
    .slider-prev { left: 20px; }
    .slider-next { right: 20px; }
    @media (max-width: 768px) {
        .hero-slider { height: 350px; }
        .slide-content h2 { font-size: 2rem; }
        .slider-prev, .slider-next { padding: 10px; width: 40px; height: 40px; }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.querySelector('.slider-prev');
        const nextBtn = document.querySelector('.slider-next');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            currentSlide = index;
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % slides.length);
        }

        function prevSlide() {
            showSlide((currentSlide - 1 + slides.length) % slides.length);
        }

        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 4000);
        }

        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        if (nextBtn) nextBtn.addEventListener('click', function() { stopAutoSlide(); nextSlide(); startAutoSlide(); });
        if (prevBtn) prevBtn.addEventListener('click', function() { stopAutoSlide(); prevSlide(); startAutoSlide(); });
        
        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                stopAutoSlide();
                showSlide(parseInt(this.dataset.slide));
                startAutoSlide();
            });
        });

        if (slides.length > 1) startAutoSlide();
    });
    </script>

    <main>
        <section class="menu" id="menu">
            <div class="container">
                <h2 class="section-title">Menu Favorit</h2>
                <p style="text-align: center; color: #666; margin-bottom: 30px;">Paling sering dipesan oleh customer kami</p>
                <div class="menu-grid">
                    <?php
                    $favorit = $conn->query("
                        SELECT m.*, COUNT(dp.id) as total_terjual, SUM(dp.jumlah) as total_qty
                        FROM menu m
                        LEFT JOIN detail_pesanan dp ON m.id = dp.menu_id
                        LEFT JOIN pesanan p ON dp.pesanan_id = p.id AND p.status = 'selesai'
                        WHERE m.status = 'aktif'
                        GROUP BY m.id
                        ORDER BY total_qty DESC, total_terjual DESC
                        LIMIT 6
                    ");
                    while ($item = $favorit->fetch_assoc()):
                        $qty = $item['total_qty'] ?? 0;
                    ?>
                    <div class="menu-card">
                        <img src="<?php echo $item['gambar']; ?>" alt="<?php echo $item['nama']; ?>" class="menu-image">
                        <h3><?php echo $item['nama']; ?></h3>
                        <p><?php echo $item['deskripsi']; ?></p>
                        <span class="price">Rp <?php echo number_format($item['harga']); ?></span>
                        <?php if ($qty > 0): ?>
                        <span style="display: block; margin-top: 8px; font-size: 0.8rem; color: #e63946;">
                            <i class="fas fa-fire"></i> <?php echo $qty; ?>x dipesan
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="pages/menu.php" class="btn btn-primary">Lihat Semua Menu</a>
                </div>
            </div>
        </section>

        <section class="about" id="about">
            <div class="container">
                <h2 class="section-title">Tentang Kami</h2>
                <p>Kami menyajikan dimsum mentai premium dengan resep rahasia yang diwariskan turun-temurun. Setiap bite
                    adalah pengalaman kuliner yang tak terlupakan.</p>
            </div>
        </section>

        <section class="contact" id="contact">
            <div class="container">
                <h2 class="section-title">Hubungi Kami</h2>
                <p>WhatsApp: 0812-3456-7890</p>
                <p>Instagram: @dimsumentai</p>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>
