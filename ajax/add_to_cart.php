<?php
session_start();
include '../includes/config.php';
include '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// ИСПРАВЛЕНО: Работаем с сессией
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Добавляем товар в сессионную корзину
if (isset($_SESSION['cart'][$productId])) {
    if (is_array($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity']++;
    } else {
        $_SESSION['cart'][$productId] = ['quantity' => 2];
    }
} else {
    $_SESSION['cart'][$productId] = ['quantity' => 1];
}

// Сохраняем в БД если пользователь авторизован
if (isLoggedIn()) {
    addToCartDB($productId);
}

// Считаем общее количество товаров
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    if (is_array($item) && isset($item['quantity'])) {
        $cartCount += $item['quantity'];
    } elseif (is_numeric($item)) {
        $cartCount += $item;
    }
}

echo json_encode([
    'success' => true, 
    'cart_count' => $cartCount
]);
?>