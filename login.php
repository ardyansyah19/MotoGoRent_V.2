<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('/motogorent-app/admin/dashboard.php');
}

$errors = [];
$old = ['email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Sesi form kedaluwarsa, silakan coba lagi.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $old['email'] = $email;

        if ($email === '' || $password === '') {
            $errors[] = 'Email dan password wajib diisi.';
        } else {
            $stmt = $pdo->prepare('SELECT id, nama, password FROM admins WHERE email = ?');
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin || !password_verify($password, $admin['password'])) {
                $errors[] = 'Email atau password salah.';
            } else {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_nama'] = $admin['nama'];
                redirect('/motogorent-app/admin/dashboard.php');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin — MotoGoRent</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/auth.css">
</head>
<body class="auth-body">

<div class="auth-wrap auth-wrap--admin">
  <div class="auth-side auth-side--admin">
    <a href="../index.php" class="nav__brand">
      <span class="nav__brand-mark">MGR</span>
      <span class="nav__brand-text">Moto<em>Go</em>Rent</span>
    </a>
    <h1>Panel Admin</h1>
    <p>Kelola stok motor dan balas chat pelanggan dari sini.</p>
  </div>

  <div class="auth-card">
    <div class="auth-card__eyebrow">KHUSUS ADMIN</div>
    <h2>Masuk Admin</h2>

    <?php if (!empty($errors)): ?>
      <div class="alert alert--error">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= clean($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" placeholder="admin@motogorent.com" value="<?= clean($old['email']) ?>" required>
      </label>

      <label class="field">
        <span>Password</span>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </label>

      <button type="submit" class="btn btn--primary btn--block">Masuk Admin</button>
    </form>

    <p class="auth-card__admin">Bukan admin? <a href="../auth/login.php">Login customer</a></p>
  </div>
</div>

</body>
</html>
