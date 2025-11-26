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
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// ИСПРАВЛЕНО: Работаем с сессией
if (isset($_SESSION['cart'][$productId])) {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = ['quantity' => $quantity];
    }
}

// Обновляем в БД если пользователь авторизован
if (isLoggedIn()) {
    updateCartQuantityDB($productId, $quantity);
}

// Считаем общее количество товаров и сумму
$cartCount = 0;
$cartTotal = 0;
$itemsCount = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $cartItem) {
        $product = getProductById($productId);
        if ($product) {
            $quantity = 1;
            if (is_array($cartItem) && isset($cartItem['quantity'])) {
                $quantity = $cartItem['quantity'];
            } elseif (is_numeric($cartItem)) {
                $quantity = $cartItem;
            }
            
            $cartCount += $quantity;
            $cartTotal += $product['price'] * $quantity;
            $itemsCount++;
        }
    }
}

echo json_encode([
    'success' => true, 
    'cart_count' => $cartCount,
    'cart_total' => $cartTotal,
    'items_count' => $itemsCount
]);
?>