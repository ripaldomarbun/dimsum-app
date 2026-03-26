-- Database: dimsum_mentai

-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: dimsum2026)
INSERT INTO admin (username, password, nama) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Table: menu
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori ENUM('mentai', 'original', 'paket', 'minuman') NOT NULL,
    deskripsi TEXT,
    harga INT NOT NULL,
    `portion` VARCHAR(50),
    gambar VARCHAR(255),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample menu
INSERT INTO menu (nama, kategori, deskripsi, harga, `portion`) VALUES 
('Dimsum Mentai Original', 'mentai', 'Dimsum ayam dengan saus mentai signature yang gurih dan kaya rasa. Daging ayam pilihan yang juicy dengan saus mentai spesial.', 25000, '5 pcs'),
('Dimsum Mentai Mozza', 'mentai', 'Dimsum dengan isian keju mozzarella yang melt saat digigit, disiram saus mentai yang creamy dan pedas gurih.', 30000, '5 pcs'),
('Dimsum Mentai Crispy', 'mentai', 'Dimsum dengan lapisan tepung crispy yang renyah, disiram saus mentai. Tekstur crunchy di luar, lembut di dalam.', 28000, '5 pcs'),
('Dimsum Mentai Spicy', 'mentai', 'Untuk pecinta pedas! Saus mentai dengan level pedas yang dapat disesuaikan. Sensasi pedas yang bikin ketagihan.', 27000, '5 pcs'),
('Siomay', 'original', 'Siomay khas dengan tekstur kenyal dan rasa yang gurih. Cocok dimakan dengan saus kacang spesial.', 20000, '5 pcs'),
('Hakau Udang', 'original', 'Dimsum dengan kulit transparan berisi udang utuh yang fresh dan kenyal.', 25000, '5 pcs'),
('Kulit Tahu Isi', 'original', 'Kulit tahu tipis diisi dengan sayuran dan daging ayam cincang, lembut dan lezat.', 18000, '5 pcs'),
('Lumpia Udang', 'original', 'Lumpia crispy dengan isian udang dan sayuran segar, cocok untuk cemilan.', 22000, '5 pcs'),
('Paket Hemat', 'paket', '3 Dimsum Mentai Original + Nasi + Minuman. Cocok untuk makan siang.', 35000, '1 porsi'),
('Paket Keluarga', 'paket', '6 Dimsum Mentai (mixed) + Nasi + 2 Minuman. Untuk 2-3 orang.', 75000, '3-4 porsi'),
('Paket Party', 'paket', '12 Dimsum Mixed + Nasi + 4 Minuman. Sempurna untuk acara kecil.', 150000, '5-6 porsi'),
('Es Teh Manis', 'minuman', 'Teh manis dingin segar.', 8000, '1 gelas'),
('Es Jeruk', 'minuman', 'Jeruk peras dingin segar.', 10000, '1 gelas'),
('Es Milo', 'minuman', 'Milo dingin dengan susu.', 12000, '1 gelas'),
('Air Mineral', 'minuman', 'Air mineral kemasan.', 5000, '1 botol');

-- Table: pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_pesanan VARCHAR(50) NOT NULL UNIQUE,
    nama_pelanggan VARCHAR(100) NOT NULL,
    no_wa VARCHAR(20),
    total_harga INT NOT NULL,
    metode_pembayaran VARCHAR(50) DEFAULT 'cash',
    status ENUM('menunggu', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'menunggu',
    tanggal_pesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: detail_pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    menu_id INT NOT NULL,
    jumlah INT NOT NULL,
    harga_saat_ini INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id) REFERENCES menu(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: kontak
CREATE TABLE IF NOT EXISTS kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    no_wa VARCHAR(20),
    pesan TEXT NOT NULL,
    status ENUM('baru', 'dibaca', 'dibalas') DEFAULT 'baru',
    tanggal_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
