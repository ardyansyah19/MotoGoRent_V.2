<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

// Kalau sudah login, langsung ke beranda
if (!empty($_SESSION['customer_id'])) {
    redirect('/motogorent-app/customer/beranda.php');
}

$errors = [];
$old = ['nama_lengkap' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi form kedaluwarsa, silakan coba lagi.';
    } else {
        $nama_lengkap     = trim($_POST['nama_lengkap'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $password         = $_POST['password'] ?? '';
        $konfirmasi       = $_POST['konfirmasi_password'] ?? '';

        $old['nama_lengkap'] = $nama_lengkap;
        $old['email']        = $email;

        if ($nama_lengkap === '' || $email === '' || $password === '' || $konfirmasi === '') {
            $errors[] = 'Semua kolom wajib diisi.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }
        if ($password !== $konfirmasi) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM customers WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email ini sudah terdaftar. Silakan login.';
            }
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO customers (nama_lengkap, email, password) VALUES (?, ?, ?)'
            );
            $stmt->execute([$nama_lengkap, $email, $hash]);

            set_flash('success', 'Registrasi berhasil! Silakan login menggunakan email & password kamu.');
            redirect('/motogorent-app/auth/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Akun — MotoGoRent</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/auth.css">
</head>
<body class="auth-body">

<div class="auth-wrap">
  <div class="auth-side">
    <a href="../index.php" class="nav__brand">
      <span class="nav__brand-mark">MGR</span>
      <span class="nav__brand-text">Moto<em>Go</em>Rent</span>
    </a>
    <h1>Satu akun,<br>bebas jelajah Jogja.</h1>
    <p>Daftar sekali, langsung bisa lihat unit motor yang ready dan chat admin kapan pun kamu butuh.</p>
    <div class="auth-side__route">
      <span class="route__dot route__dot--start"></span>
      <div class="route__line"></div>
      <span class="route__dot route__dot--end"></span>
    </div>
  </div>

  <div class="auth-card">
    <div class="auth-card__eyebrow">FORM REGISTRASI</div>
    <h2>Buat Akun Baru</h2>
    <p class="auth-card__sub">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert--error">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= clean($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <label class="field">
        <span>Nama Lengkap</span>
        <input type="text" name="nama_lengkap" placeholder="Nama sesuai identitas" value="<?= clean($old['nama_lengkap']) ?>" required>
      </label>

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" placeholder="nama@email.com" value="<?= clean($old['email']) ?>" required>
      </label>

      <label class="field">
        <span>Password</span>
        <input type="password" name="password" placeholder="Minimal 6 karakter" required>
      </label>

      <label class="field">
        <span>Konfirmasi Password</span>
        <input type="password" name="konfirmasi_password" placeholder="Ulangi password" required>
      </label>

      <button type="submit" class="btn btn--primary btn--block">Daftar Sekarang</button>
    </form>
  </div>
</div>

</body>
</html>
