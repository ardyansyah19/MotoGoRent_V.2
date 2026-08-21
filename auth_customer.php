<?php
/**
 * Panggil file ini di awal setiap halaman customer yang butuh login.
 * Jika belum login, customer akan diarahkan ke halaman login.
 */

require_once __DIR__ . '/functions.php';

if (empty($_SESSION['customer_id'])) {
    set_flash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
    redirect('/motogorent-app/auth/login.php');
}
