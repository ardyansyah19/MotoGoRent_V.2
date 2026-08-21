<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/database.php';

// --- Update stok motor (form sederhana di dashboard) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stok'])) {
    if (csrf_verify($_POST['csrf_token'] ?? null)) {
        $motor_id = (int) $_POST['motor_id'];
        $stok_baru = max(0, (int) $_POST['stok']);

        $stmt = $pdo->prepare('UPDATE motors SET stok = ? WHERE id = ?');
        $stmt->execute([$stok_baru, $motor_id]);

        set_flash('success', 'Stok motor berhasil diperbarui.');
    }
    redirect('/motogorent-app/admin/dashboard.php');
}

// --- Daftar customer yang pernah chat, diurutkan dari pesan terbaru ---
$sql = "
    SELECT
        c.id AS customer_id,
        c.nama_lengkap,
        c.email,
        (SELECT pesan FROM chats WHERE customer_id = c.id ORDER BY created_at DESC, id DESC LIMIT 1) AS pesan_terakhir,
        (SELECT created_at FROM chats WHERE customer_id = c.id ORDER BY created_at DESC, id DESC LIMIT 1) AS waktu_terakhir,
        (SELECT COUNT(*) FROM chats WHERE customer_id = c.id AND sender = 'customer' AND is_read = 0) AS belum_dibaca
    FROM customers c
    INNER JOIN chats ch ON ch.customer_id = c.id
    GROUP BY c.id, c.nama_lengkap, c.email
    ORDER BY waktu_terakhir DESC
";
$percakapan = $pdo->query($sql)->fetchAll();

// --- Daftar motor untuk kelola stok ---
$motors = $pdo->query('SELECT * FROM motors ORDER BY kategori, nama')->fetchAll();

$flash = get_flash();
$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin — MotoGoRent</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<header class="nav">
  <div class="nav__inner container">
    <a href="dashboard.php" class="nav__brand">
      <span class="nav__brand-mark">MGR</span>
      <span class="nav__brand-text">Moto<em>Go</em>Rent <small>Admin</small></span>
    </a>
    <nav class="nav__links">
      <span class="nav__greet">Halo, <?= clean($_SESSION['admin_nama']) ?> 👋</span>
      <a href="logout.php" class="nav__cta">Keluar</a>
    </nav>
  </div>
</header>

<main class="container page">

  <?php if ($flash): ?>
    <div class="alert alert--<?= clean($flash['type']) ?>"><?= clean($flash['message']) ?></div>
  <?php endif; ?>

  <div class="admin-grid">

    <!-- ===== Percakapan ===== -->
    <section class="admin-panel">
      <div class="page__head page__head--tight">
        <span class="eyebrow">CHAT MASUK</span>
        <h2>Percakapan Customer</h2>
      </div>

      <?php if (empty($percakapan)): ?>
        <p class="empty-note">Belum ada percakapan dari customer.</p>
      <?php else: ?>
        <div class="convo-list">
          <?php foreach ($percakapan as $p): ?>
            <a href="chat.php?customer_id=<?= (int) $p['customer_id'] ?>" class="convo-item">
              <div class="convo-item__top">
                <span class="convo-item__name"><?= clean($p['nama_lengkap']) ?></span>
                <?php if ($p['belum_dibaca'] > 0): ?>
                  <span class="badge"><?= (int) $p['belum_dibaca'] ?></span>
                <?php endif; ?>
              </div>
              <div class="convo-item__preview"><?= clean(mb_strimwidth($p['pesan_terakhir'] ?? '', 0, 70, '…')) ?></div>
              <div class="convo-item__email"><?= clean($p['email']) ?></div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <!-- ===== Kelola Stok ===== -->
    <section class="admin-panel">
      <div class="page__head page__head--tight">
        <span class="eyebrow">ARMADA</span>
        <h2>Kelola Sisa Unit</h2>
      </div>

      <div class="stok-list">
        <?php foreach ($motors as $m): ?>
          <form method="post" action="dashboard.php" class="stok-item">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            <input type="hidden" name="motor_id" value="<?= (int) $m['id'] ?>">
            <input type="hidden" name="update_stok" value="1">

            <div class="stok-item__info">
              <span class="stok-item__icon"><?= clean($m['icon']) ?></span>
              <div>
                <div class="stok-item__nama"><?= clean($m['nama']) ?></div>
                <div class="stok-item__kategori"><?= clean($m['kategori']) ?></div>
              </div>
            </div>

            <input type="number" name="stok" min="0" value="<?= (int) $m['stok'] ?>" class="stok-item__input">
            <button type="submit" class="btn btn--ghost btn--sm">Simpan</button>
          </form>
        <?php endforeach; ?>
      </div>
    </section>

  </div>
</main>

</body>
</html>
