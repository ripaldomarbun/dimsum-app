-- Run this SQL in phpMyAdmin to add metode_pembayaran column to existing pesanan table
ALTER TABLE pesanan ADD COLUMN metode_pembayaran VARCHAR(50) DEFAULT 'cash' AFTER total_harga;
