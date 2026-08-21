<?php
require_once __DIR__ . '/../includes/auth_customer.php';
require_once __DIR__ . '/../config/database.php';

$stmt = $pdo->query('SELECT * FROM motors ORDER BY harga_per_hari ASC');
$motors = $stmt->fetchAll();

// Hitung pesan admin yang belum dibaca customer ini, untuk badge di nav chat
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM chats WHERE customer_id = ? AND sender = 'admin' AND is_read = 0");
$stmt->execute([$_SESSION['customer_id']]);
$unread = (int) $stmt->fetch()['total'];

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beranda — MotoGoRent</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>

<header class="nav">
  <div class="nav__inner container">
    <a href="beranda.php" class="nav__brand">
      <span class="nav__brand-mark">MGR</span>
      <span class="nav__brand-text">Moto<em>Go</em>Rent</span>
    </a>
    <nav class="nav__links">
      <span class="nav__greet">Halo, <?= clean($_SESSION['customer_nama']) ?> 👋</span>
      <a href="chat.php" class="nav__chat-link">
        Chat Admin
        <?php if ($unread > 0): ?><span class="badge"><?= $unread ?></span><?php endif; ?>
      </a>
      <a href="../auth/logout.php" class="nav__cta">Keluar</a>
    </nav>
  </div>
</header>

<main class="container page">

  <?php if ($flash): ?>
    <div class="alert alert--<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <div class="page__head">
    <span class="eyebrow">ARMADA TERSEDIA</span>
    <h1>Pilih motor, tinggal hubungi admin.</h1>
    <p>Sisa unit ditampilkan langsung — begitu unit habis, kartu motor otomatis menandai "Habis".</p>
  </div>

  <div class="fleet-grid">
    <?php foreach ($motors as $m): ?>
      <?php
        $habis = $m['stok'] <= 0;
        $low   = !$habis && $m['stok'] <= 2;
      ?>
      <article class="moto-card <?= $habis ? 'is-empty' : '' ?>">
        <div class="moto-card__top">
          <div class="moto-card__icon"><?= clean($m['icon']) ?></div>
          <span class="moto-card__tag"><?= clean($m['kategori']) ?></span>
        </div>
        <h3><?= clean($m['nama']) ?></h3>
        <p><?= clean($m['deskripsi']) ?></p>

        <div class="moto-card__stock <?= $habis ? 'is-empty' : ($low ? 'is-low' : '') ?>">
          <?php if ($habis): ?>
            Unit habis — coba lagi nanti
          <?php else: ?>
            Sisa unit ready: <strong><?= (int) $m['stok'] ?></strong>
          <?php endif; ?>
        </div>

        <div class="moto-card__price"><?= format_rupiah($m['harga_per_hari']) ?> <small>/ hari</small></div>

        <a class="btn btn--primary btn--block <?= $habis ? 'is-disabled' : '' ?>"
           href="chat.php?motor_id=<?= (int) $m['id'] ?>">
          <?= $habis ? 'Unit Tidak Tersedia' : 'Hubungi Admin' ?>
        </a>
      </article>
    <?php endforeach; ?>
  </div>

</main>

<a href="chat.php" class="wa-float" aria-label="Chat Admin">
  💬
  <?php if ($unread > 0): ?><span class="wa-float__badge"><?= $unread ?></span><?php endif; ?>
</a>

</body>
</html>
