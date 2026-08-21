<?php
/**
 * Panggil file ini di awal setiap halaman admin yang butuh login.
 * Jika belum login, admin akan diarahkan ke halaman login admin.
 */

require_once __DIR__ . '/functions.php';

if (empty($_SESSION['admin_id'])) {
    set_flash('error', 'Silakan login sebagai admin terlebih dahulu.');
    redirect('/motogorent-app/admin/login.php');
}
