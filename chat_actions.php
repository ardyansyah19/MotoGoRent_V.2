<?php
require_once __DIR__ . '/../includes/auth_admin.php';
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$customer_id = isset($_GET['customer_id']) ? (int) $_GET['customer_id']
             : (isset($_POST['customer_id']) ? (int) $_POST['customer_id'] : 0);

if ($customer_id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'customer_id wajib diisi.']);
    exit;
}

if ($action === 'fetch') {
    // Tandai pesan dari customer sebagai sudah dibaca admin
    $stmt = $pdo->prepare("UPDATE chats SET is_read = 1 WHERE customer_id = ? AND sender = 'customer' AND is_read = 0");
    $stmt->execute([$customer_id]);

    $stmt = $pdo->prepare('SELECT sender, pesan, created_at FROM chats WHERE customer_id = ? ORDER BY created_at ASC, id ASC');
    $stmt->execute([$customer_id]);
    $messages = $stmt->fetchAll();

    echo json_encode(['ok' => true, 'messages' => $messages]);
    exit;
}

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pesan = trim($_POST['pesan'] ?? '');

    if ($pesan === '') {
        http_response_code(422);
        echo json_encode(['ok' => false, 'error' => 'Pesan tidak boleh kosong.']);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO chats (customer_id, sender, pesan) VALUES (?, 'admin', ?)"
    );
    $stmt->execute([$customer_id, $pesan]);

    echo json_encode(['ok' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'error' => 'Aksi tidak dikenali.']);
