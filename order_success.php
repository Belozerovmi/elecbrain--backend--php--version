<?php
$pageTitle = "Заказ оформлен";
include 'includes/header.php';

if (!isLoggedIn() || !isset($_SESSION['order_success'])) {
    redirect('index.php');
}

// Получаем данные заказа из сессии
$orderData = $_SESSION['order_success'];
$orderNumber = $orderData['order_number'];

// ПРИНУДИТЕЛЬНО ОЧИЩАЕМ КОРЗИНУ ПОСЛЕ УСПЕШНОГО ЗАКАЗА
$_SESSION['cart'] = [];
$_SESSION['cart_count'] = 0;

// Очищаем сессионные переменные
unset($_SESSION['order_success']);
?>

<main class="order-success-main">
    <div class="inner">
        <div class="success-container">
            <!-- <div class="success-icon">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
            </div> -->
            
            <h1>Заказ успешно оформлен!</h1>
            <p class="success-message">Спасибо за ваш заказ. Мы свяжемся с вами в ближайшее время для подтверждения.</p>
            
            <div class="order-details">
                <div class="order-number">
                    <strong>Номер заказа:</strong>
                    <span>#<?php echo $orderNumber; ?></span>
                </div>
                <p>На вашу почту отправлено письмо с деталями заказа.</p>
            </div>
            
            <div class="success-actions">
                <a href="account.php?tab=orders" class="success-btn continue-btn">Мои заказы</a>
                <a href="catalog.php" class="success-btn continue-btn">Продолжить покупки</a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>