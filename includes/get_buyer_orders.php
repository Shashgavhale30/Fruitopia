<?php
require_once 'config.php';
require_once 'auth.php';

session_start();

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status IN ('pending', 'processing')");
$stmt->execute([$_SESSION['user_id']]);
$count = $stmt->fetchColumn();

header('Content-Type: application/json');
echo json_encode(['count' => $count]);
?>