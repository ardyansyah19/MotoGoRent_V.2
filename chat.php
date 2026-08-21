<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/database.php';

$customer_id = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;

$stmt = $pdo->prepare('SELECT id, nama_lengkap, email FROM customers WHERE id = ?');
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    set_flash('error', 'Customer tidak ditemukan.');
    redirect('/motogorent-app/admin/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat <?= clean($customer['nama_lengkap']) ?> — Admin MotoGoRent</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/chat.css">
</head>
<body>

<header class="nav">
  <div class="nav__inner container">
    <a href="dashboard.php" class="nav__brand">
      <span class="nav__brand-mark">MGR</span>
      <span class="nav__brand-text">Moto<em>Go</em>Rent <small>Admin</small></span>
    </a>
    <nav class="nav__links">
      <a href="dashboard.php">&larr; Kembali ke Dashboard</a>
      <a href="logout.php" class="nav__cta">Keluar</a>
    </nav>
  </div>
</header>

<main class="container page page--chat">
  <div class="page__head">
    <span class="eyebrow">CHAT DENGAN CUSTOMER</span>
    <h1><?= clean($customer['nama_lengkap']) ?></h1>
    <p><?= clean($customer['email']) ?></p>
  </div>

  <div class="chat-box">
    <div class="chat-box__messages" id="chatMessages">
      <div class="chat-box__loading">Memuat percakapan…</div>
    </div>

    <form class="chat-box__form" id="chatForm">
      <input
        type="text"
        id="chatInput"
        placeholder="Balas pesan customer..."
        autocomplete="off"
        required
      >
      <button type="submit" class="btn btn--primary">Kirim</button>
    </form>
  </div>
</main>

<script>
  window.CHAT_ENDPOINT = 'chat_actions.php?customer_id=<?= (int) $customer['id'] ?>';
  window.CHAT_SEND_EXTRA = { customer_id: <?= (int) $customer['id'] ?> };
  window.CHAT_ROLE = 'admin';
</script>
<script src="../js/chat.js"></script>
</body>
</html>
