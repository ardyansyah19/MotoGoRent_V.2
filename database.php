<?php
/**
 * Koneksi Database — MotoGoRent
 * Sesuaikan DB_HOST / DB_NAME / DB_USER / DB_PASS dengan konfigurasi XAMPP kamu.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'motogorent');
define('DB_USER', 'root');
define('DB_PASS', ''); // default XAMPP: password kosong

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
}
