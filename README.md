# Dimsum Mentai - Restaurant POS System

Sistem aplikasi manajemen restoran untuk pemesanan Dimsum dengan fitur lengkap.

## Tech Stack

- **Backend**: PHP native
- **Database**: MySQL (XAMPP)
- **Frontend**: HTML, CSS (Bootstrap), JavaScript
- **Payment**: Midtrans, QRIS, Transfer Bank

## Fitur

### Halaman Pelanggan
- Pemesanan via meja
- Keranjang belanja
- Checkout dengan multiple payment (QRIS, Transfer, Cash)
- Menu kategori lengkap

### Halaman Admin
- Dashboard dengan statistik
- Kelola menu (CRUD)
- Kelola pesanan
- Kelola meja
- Kelola slider promo
- Kirim promosi via WhatsApp
- Laporan penjualan

## Instalasi

1. Clone project ke htdocs:
   ```
   /Applications/XAMPP/htdocs/dimsum
   ```

2. Import database:
   - Buka phpMyAdmin
   - Import file `config/tables.sql`

3. Konfigurasi:
   - Edit `config/database.php` untuk koneksi MySQL
   - Edit `config/database.php` untuk koneksi MySQL

4. Akses aplikasi:
   - Pelanggan: `http://localhost/dimsum`
   - Admin: `http://localhost/dimsum/admin`

## Default Admin
- Username: admin
- Password: (cek di database)

## Struktur Folder

```
dimsum/
├── assets/
│   ├── css/        # Style files
│   ├── images/     # Gambar & upload
│   └── js/         # JavaScript files
├── admin/          # Halaman admin
├── config/         # Konfigurasi database
├── pages/          # Halaman customer
├── index.php       # Landing page
├── header.php      # Komponen header
└── footer.php      # Komponen footer
```

## License

MIT