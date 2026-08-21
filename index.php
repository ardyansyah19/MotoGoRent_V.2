<?php
require_once __DIR__ . '/includes/functions.php';

if (!empty($_SESSION['customer_id'])) {
    redirect('/motogorent-app/customer/beranda.php');
}

if (!empty($_SESSION['admin_id'])) {
    redirect('/motogorent-app/admin/dashboard.php');
}

redirect('/motogorent-app/auth/login.php');
