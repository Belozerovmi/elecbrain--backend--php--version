<?php
include '../includes/config.php';
include '../includes/functions.php';



header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// ВРЕМЕННО: устанавливаем user_id если нет
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

$result = toggleWishlist($productId);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>