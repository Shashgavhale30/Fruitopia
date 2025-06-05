<?php
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (!isset($_SESSION['login_user'])) {
    header("Location: index.php");
    exit();
}

if (!is_logged_in() || $_SESSION['role'] !== 'buyer') {
    redirect('login.php');
}

$buyer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fruit_name = $_POST['fruit_name'];
    $price = $_POST['price'];
    $season = $_POST['season'];
    $seller_id = $_POST['seller_id'];
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, seller_id, fruit_name, quantity, price, season) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$buyer_id, $seller_id, $fruit_name, $quantity, $price, $season]);

    header('Location: my_orders.php');
    exit;
}
?>
