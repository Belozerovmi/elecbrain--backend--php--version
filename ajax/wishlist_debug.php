<?php
// ajax/wishlist_debug.php - ДЛЯ ОТЛАДКИ

// Простой тестовый ответ
header('Content-Type: application/json; charset=utf-8');

// Имитируем успешный ответ
echo json_encode([
    'success' => true, 
    'added' => true, 
    'message' => 'Товар добавлен в избранное (тестовый ответ)'
], JSON_UNESCAPED_UNICODE);
?>