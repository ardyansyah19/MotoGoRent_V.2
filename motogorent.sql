-- =========================================================
-- MotoGoRent — Database Schema
-- Import file ini di phpMyAdmin / mysql client
-- =========================================================

CREATE DATABASE IF NOT EXISTS motogorent CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE motogorent;

-- ---------------------------------------------------------
-- Tabel customers (pelanggan yang registrasi)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel admins (pengelola / operator MotoGoRent)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel motors (armada motor yang disewakan)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS motors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga_per_hari INT UNSIGNED NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    deskripsi VARCHAR(255) DEFAULT NULL,
    icon VARCHAR(10) DEFAULT '🛵',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel chats (chat 1-ke-1 antara customer & admin)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS chats (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    motor_id INT UNSIGNED DEFAULT NULL,
    sender ENUM('customer','admin') NOT NULL,
    pesan TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (motor_id) REFERENCES motors(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Seed data: akun admin default
-- Email   : admin@motogorent.com
-- Password: admin123
-- ---------------------------------------------------------
INSERT INTO admins (nama, email, password) VALUES
('Admin MotoGoRent', 'admin@motogorent.com', '$2b$10$bBWY5kaOQF7FDL2OvI/hius5N0RRXXV6L8CFSMn/HX5tYIy88PEzq');
-- hash di atas = password "admin123" (bcrypt, dibuat dengan password_hash() PHP)

-- ---------------------------------------------------------
-- Seed data: daftar motor
-- ---------------------------------------------------------
INSERT INTO motors (nama, kategori, harga_per_hari, stok, deskripsi, icon) VALUES
('Honda Beat FI',     'Hemat Harian',      45000, 8, 'Ringan, irit BBM, pas buat muter kota tanpa ribet.', '🛵'),
('Honda Beat Street', 'Hemat Harian',      55000, 6, 'Tampilan sporty, tetap gesit buat harian.', '🛵'),
('Honda Scoopy',      'Hemat Harian',      60000, 5, 'Desain klasik-modern, nyaman buat jalan santai.', '🛵'),
('Yamaha Fazzio',     'Hemat Harian',      60000, 4, 'Fitur keyless, cocok buat eksplor Malioboro seharian.', '🛵'),
('Honda Vario 125',   'Nyaman Jarak Jauh', 65000, 7, 'Tenaga lebih mantap buat jalur naik-turun kota.', '🏍'),
('Yamaha Lexi',       'Nyaman Jarak Jauh', 70000, 5, 'Jok lega, posisi duduk nyaman buat perjalanan jauh.', '🏍'),
('Honda Vario 160',   'Nyaman Jarak Jauh', 75000, 3, 'Performa lebih responsif untuk rute wisata berbukit.', '🏍'),
('Honda Stylo',       'Nyaman Jarak Jauh', 80000, 2, 'Desain elegan dengan kenyamanan berkendara ekstra.', '🏍'),
('Honda PCX',         'Premium Touring',   95000, 3, 'Stabil di kecepatan tinggi, ideal buat jarak jauh.', '🏁'),
('Yamaha NMax',       'Premium Touring',   95000, 2, 'Bagasi luas dan nyaman untuk turing seharian penuh.', '🏁'),
('Honda ADV',         'Premium Touring',  100000, 1, 'Ground clearance tinggi, siap untuk medan wisata menanjak.', '🏁');
