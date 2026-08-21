<?php
require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['admin_id'], $_SESSION['admin_nama']);
session_regenerate_id(true);

set_flash('success', 'Admin berhasil keluar.');
redirect('/motogorent-app/admin/login.php');
