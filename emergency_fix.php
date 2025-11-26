<?php
session_start();

// Полностью очищаем корзину
unset($_SESSION['cart']);

echo "<h1>Корзина очищена!</h1>";
echo "<p>Теперь все страницы должны открываться нормально.</p>";

echo "<h3>Текущая сессия:</h3>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";
?>

<a href="catalog.php">Перейти в каталог</a> | 
<a href="cart.php">Перейти в корзину</a>