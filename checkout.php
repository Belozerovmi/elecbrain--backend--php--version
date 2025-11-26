<?php
$pageTitle = "Оформление заказа";
include 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Получаем корзину из сессии
$cartItems = [];
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
            
            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'total_price' => $product['price'] * $quantity
            ];
            
            $cartTotal += $product['price'] * $quantity;
            $itemsCount++;
        }
    }
}

// Если корзина пуста, редиректим обратно с сообщением
if (empty($cartItems)) {
    $_SESSION['checkout_error'] = 'Корзина пуста. Добавьте товары для оформления заказа.';
    redirect('cart.php');
}

$user = getUser();

// Обработка формы заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $orderData = [
        'first_name' => sanitize($_POST['first_name']),
        'last_name' => sanitize($_POST['last_name']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'address' => sanitize($_POST['address']),
        'payment_method' => sanitize($_POST['payment_method']),
        'notes' => sanitize($_POST['notes']),
        'total_amount' => $cartTotal
    ];
    
    // Создаем заказ
    $orderResult = createOrder($user['id'], $orderData, $cartItems);
    
    if ($orderResult['success']) {
        // Очищаем корзину
        $_SESSION['cart'] = [];
        $_SESSION['cart_count'] = 0;
        
        // Сохраняем данные заказа для страницы успеха
        $_SESSION['order_success'] = [
            'order_number' => $orderResult['order_number'],
            'tracking_number' => $orderResult['tracking_number'],
            'total_amount' => $cartTotal
        ];
        
        redirect('order_success.php');
    } else {
        $error = $orderResult['error'];
    }
}
?>

<main class="checkout-main">
    <div class="inner">
        <div class="checkout-header">
            <h1>Оформление заказа</h1>
            <div class="checkout-steps">
                <div class="step active">1. Данные получателя</div>
                <div class="step">2. Доставка</div>
                <div class="step">3. Оплата</div>
                <div class="step">4. Подтверждение</div>
            </div>
        </div>

        <div class="checkout-layout">
            <!-- Левая колонка с формой -->
            <div class="checkout-content">
                <?php if (isset($error)): ?>
                <div class="alert error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <!-- ОДНА общая форма для всех данных -->
                <form class="checkout-form" method="POST" id="checkoutForm">
                    <div class="form-section">
                        <h3>Контактная информация</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">Имя *</label>
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?php echo $user['first_name']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Фамилия *</label>
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?php echo $user['last_name']; ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Телефон *</label>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo $user['phone']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Адрес доставки</h3>
                        <div class="form-group">
                            <label for="address">Адрес *</label>
                            <input type="text" id="address" name="address" 
                                   value="<?php echo $user['address']; ?>" 
                                   placeholder="Город, улица, дом, квартира" required>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Способ оплаты</h3>
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="card" checked>
                                <span class="checkmark"></span>
                                <div class="payment-info">
                                    <span class="payment-name">Банковской картой</span>
                                    <span class="payment-desc">Оплата онлайн</span>
                                </div>
                            </label>
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cash">
                                <span class="checkmark"></span>
                                <div class="payment-info">
                                    <span class="payment-name">Наличными</span>
                                    <span class="payment-desc">При получении</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3>Комментарий к заказу</h3>
                        <div class="form-group">
                            <textarea id="notes" name="notes" placeholder="Дополнительные пожелания к заказу..."></textarea>
                        </div>
                    </div>

                    <!-- Скрытая кнопка для десктопа -->
                    <button type="submit" name="place_order" class="checkout-btn dark--btn desktop-checkout-btn" style="display: none;">
                        Подтвердить заказ
                    </button>
                </form>
            </div>

            <!-- Правая колонка с сайдбаром -->
            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3>Ваш заказ</h3>
                    <div class="order-items">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="item-info">
                                <span class="item-name"><?php echo $item['name']; ?></span>
                                <span class="item-quantity"> <?php echo $item['quantity']; ?> шт.</span>
                            </div>
                            <span class="item-price"><?php echo number_format($item['total_price'], 0, ',', ' '); ?> ₽</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-totals">
                        <div class="total-row">
                            <span>Товары (<?php echo $itemsCount; ?>)</span>
                            <span><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                        </div>
                        <div class="total-row">
                            <span>Доставка</span>
                            <span class="free-delivery">Бесплатно</span>
                        </div>
                        <div class="total-row final-total">
                            <span>Итого</span>
                            <span><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                        </div>
                    </div>
                </div>
                
                <div class="delivery-info">
                    <h4>Информация о доставке</h4>
                    <p>Бесплатная доставка по Москве</p>
                    <p>Срок доставки: 1-2 дня</p>
                    <p>Самовывоз: доступен из пунктов выдачи</p>
                </div>
                
                <!-- Кнопка в сайдбаре - отправляет основную форму -->
                <button type="submit" form="checkoutForm" name="place_order" class="checkout-btn dark--btn sidebar-checkout-btn">
                    Подтвердить заказ
                </button>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>