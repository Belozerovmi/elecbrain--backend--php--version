<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Требуется авторизация']);
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Неверный ID товара']);
    exit;
}

// ИСПРАВЛЕНО: Работаем с сессией для вишлиста
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

// Проверяем текущее состояние в сессии
$currentlyInWishlist = isset($_SESSION['wishlist'][$productId]);

$result = toggleWishlist($productId);

if ($result['success']) {
    // Обновляем сессию на основе ответа от БД
    if ($result['added']) {
        $_SESSION['wishlist'][$productId] = true;
    } else {
        unset($_SESSION['wishlist'][$productId]);
    }
    
    echo json_encode([
        'success' => true, 
        'added' => $result['added'],
        'message' => $result['message'],
        'previously_in_wishlist' => $currentlyInWishlist
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $result['error']]);
}
?>