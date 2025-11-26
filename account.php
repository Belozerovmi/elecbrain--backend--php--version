<?php
$pageTitle = "Личный кабинет";
include 'includes/header.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUser();
$activeTab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'orders';

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$user['id']]);
$orders = $ordersStmt->fetchAll();

// ИСПРАВЛЕНО: Используем сессию для счетчика корзины
$cartCount = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (is_array($item) && isset($item['quantity'])) {
            $cartCount += $item['quantity'];
        } elseif (is_numeric($item)) {
            $cartCount += $item;
        }
    }
}
?>
<main class="account-main">
    <section class="account-hero">
        <div class="account-hero--inner inner">
            <h1 class="account-title">Личный кабинет</h1>
            <p class="account-subtitle">Управляйте своими заказами, данными и предпочтениями</p>
        </div>
    </section>

    <section class="account-content">
        <div class="account-content--inner inner">
            <!-- Боковое меню -->
            <aside class="account-sidebar">
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="<?php echo !empty($user['avatar']) ? 'uploads/' . $user['avatar'] : 'assets/images/user--avatar.jpg'; ?>" alt="Аватар пользователя" />
                    </div>
                    <div class="user-info">
                        <h3 class="user-name"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
                        <p class="user-email"><?php echo $user['email']; ?></p>
                    </div>
                </div>

                <nav class="account-nav">
                    <ul>
                        <li>
                            <a href="?tab=orders" class="nav-link <?php echo $activeTab === 'orders' ? 'active' : ''; ?>">
                                <svg viewBox="0 0 24 24">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
                                </svg>
                                История заказов
                            </a>
                        </li>
                        <li>
                            <a href="?tab=tracking" class="nav-link <?php echo $activeTab === 'tracking' ? 'active' : ''; ?>">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2m0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                Отслеживание заказов
                            </a>
                        </li>
                        <li>
                            <a href="?tab=profile" class="nav-link <?php echo $activeTab === 'profile' ? 'active' : ''; ?>">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Персональные данные
                            </a>
                        </li>
                        <li>
                            <a href="?tab=wishlist" class="nav-link <?php echo $activeTab === 'wishlist' ? 'active' : ''; ?>">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78"/>
                                </svg>
                                Список желаний
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link logout-link" onclick="showLogoutModal(event)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M19.285 12h-8.012m5.237 3.636L20 12l-3.49-3.636M13.455 7V4H4v16h9.455v-3"/>
                                </svg>
                                Выход из аккаунта
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Основной контент -->
            <div class="account-main-content">
                <!-- История заказов -->
                <?php if ($activeTab === 'orders'): ?>
                <div id="orders" class="tab-content active">
                    <div class="tab-header">
                        <h2>История заказов</h2>
                        <p>Все ваши предыдущие заказы</p>
                    </div>

                    <div class="orders-list">
                        <?php if (empty($orders)): ?>
                            <div class="no-orders">
                                <p style="margin-bottom: 40px;">У вас пока нет заказов</p>
                                <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): 
                            // Получаем товары заказа
                            $orderItemsStmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi 
                                                            JOIN products p ON oi.product_id = p.id 
                                                            WHERE oi.order_id = ?");
                            $orderItemsStmt->execute([$order['id']]);
                            $orderItems = $orderItemsStmt->fetchAll();
                            ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h3>Заказ #<?php echo $order['order_number']; ?></h3>
                                        <span class="order-date"><?php echo date('d.m.Y', strtotime($order['created_at'])); ?></span>
                                    </div>
                                    <div class="order-status <?php echo $order['status']; ?>">
                                        <span>
                                            <?php echo getStatusText($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-items">
                                    <?php foreach ($orderItems as $item): ?>
                                    <div class="order-item">
                                        <img src="<?php echo getProductImage($item['image']); ?>" alt="<?php echo $item['name']; ?>" />
                                        <div class="item-info">
                                            <h4><?php echo $item['name']; ?></h4>
                                            <p>Количество: <?php echo $item['quantity']; ?></p>
                                            <span class="item-price"><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽</span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-footer">
                                    <div class="order-total">
                                        <strong>Итого: <?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</strong>
                                    </div>
                                    <button class="dark--btn order-details-btn">Детали заказа</button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Отслеживание заказов -->
                <?php if ($activeTab === 'tracking'): ?>
                <div id="tracking" class="tab-content active">
                    <div class="tab-header">
                        <h2>Отслеживание заказов</h2>
                        <p>Текущие статусы ваших заказов</p>
                    </div>

                    <div class="tracking-list">
                        <?php if (empty($orders)): ?>
                            <div class="no-orders">
                                <p style="margin-bottom: 40px;">У вас пока нет заказов для отслеживания</p>
                                <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($orders as $order): 
                                $timeline = getOrderTrackingInfo($order);
                            ?>
                            <div class="tracking-card">
                                <div class="tracking-header">
                                    <h3>Заказ #<?php echo $order['order_number']; ?></h3>
                                    <?php if (!empty($order['tracking_number'])): ?>
                                        <span class="tracking-number">Трек-номер: <?php echo $order['tracking_number']; ?></span>
                                    <?php else: ?>
                                        <span class="tracking-number">Трек-номер: генерируется</span>
                                    <?php endif; ?>
                                </div>
                                <div class="tracking-status <?php echo getStatusClass($order['status']); ?>">
                                    <span><?php echo getStatusText($order['status']); ?></span>
                                </div>
                                <div class="tracking-timeline">
                                    <?php foreach ($timeline as $step): ?>
                                    <div class="timeline-step <?php echo $step['completed'] ? 'completed' : ''; ?> <?php echo $step['active'] ? 'active' : ''; ?>">
                                        <div class="step-dot"></div>
                                        <div class="step-info">
                                            <span><?php echo $step['title']; ?></span>
                                            <small><?php echo $step['date']; ?></small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Дополнительная информация о заказе -->
                                <div class="order-summary">
                                    <div class="summary-item">
                                        <span>Дата заказа:</span>
                                        <strong><?php echo date('d.m.Y', strtotime($order['created_at'])); ?></strong>
                                    </div>
                                    <div class="summary-item">
                                        <span>Сумма заказа:</span>
                                        <strong><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</strong>
                                    </div>
                                    <?php if ($order['status'] === 'shipped' && !empty($order['tracking_number'])): ?>
                                    <div class="summary-item">
                                        <span>Отследить:</span>
                                        <a href="https://www.pochta.ru/tracking#<?php echo $order['tracking_number']; ?>" 
                                           target="_blank" class="track-link">Почта России</a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Персональные данные -->
<?php if ($activeTab === 'profile'): ?>
<div id="profile" class="tab-content active">
    <div class="tab-header">
        <h2>Персональные данные</h2>
        <p>Управление вашей контактной информацией</p>
    </div>

    <?php
    // Обработка формы обновления профиля
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
        $userData = [
            'first_name' => sanitize($_POST['first_name']),
            'last_name' => sanitize($_POST['last_name']),
            'email' => sanitize($_POST['email']),
            'phone' => sanitize($_POST['phone']),
            'address' => sanitize($_POST['address']),
            'city' => sanitize($_POST['city']),
            'postal_code' => sanitize($_POST['postal_code']),
            'remove_avatar' => isset($_POST['remove_avatar'])
        ];
        
        // Обрабатываем загрузку аватара
        $avatarFile = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarFile = $_FILES['avatar'];
        }
        
        $result = updateProfileWithAvatar($user['id'], $userData, $avatarFile);
        if ($result['success']) {
            echo '<div class="alert success">Данные успешно обновлены!</div>';
            $user = getUser(); // Обновляем данные пользователя
        } else {
            echo '<div class="alert error">' . $result['error'] . '</div>';
        }
    }
    ?>

    <form class="profile-form" method="POST" enctype="multipart/form-data">
        <div class="form-section">
            <h3>Аватар профиля</h3>
            <div class="avatar-upload">
                <div class="avatar-preview">
                    <div class="avatar-container">
                        <img src="<?php echo !empty($user['avatar']) ? 'uploads/avatars/' . $user['avatar'] : 'assets/images/user--avatar.jpg'; ?>" 
                             alt="Аватар пользователя" 
                             id="avatarPreview" />
                        <div class="avatar-overlay">
                            <button type="button" class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <?php if (!empty($user['avatar'])): ?>
                            <button type="button" class="avatar-remove-btn" onclick="removeAvatar()">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;" onchange="previewAvatar(this)" />
                    <input type="hidden" name="remove_avatar" id="removeAvatar" value="0" />
                </div>
                <div class="avatar-info">
                    <p>Нажмите на карандаш для загрузки нового аватара</p>
                    <small>Разрешены JPG, PNG, GIF, WebP до 5MB</small>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Основная информация</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">Имя *</label>
                    <input type="text" id="firstName" name="first_name" value="<?php echo $user['first_name']; ?>" required />
                </div>
                <div class="form-group">
                    <label for="lastName">Фамилия *</label>
                    <input type="text" id="lastName" name="last_name" value="<?php echo $user['last_name']; ?>" required />
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required />
            </div>
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" />
            </div>
        </div>

        <div class="form-section">
            <h3>Адрес доставки</h3>
            <div class="form-group">
                <label for="address">Адрес</label>
                <input type="text" id="address" name="address" value="<?php echo $user['address']; ?>" />
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="city">Город</label>
                    <input type="text" id="city" name="city" value="<?php echo $user['city']; ?>" />
                </div>
                <div class="form-group">
                    <label for="postalCode">Индекс</label>
                    <input type="text" id="postalCode" name="postal_code" value="<?php echo $user['postal_code']; ?>" />
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="update_profile" class="dark--btn save-btn">Сохранить изменения</button>
        </div>
    </form>
</div>
<?php endif; ?>

                <!-- Список желаний -->
                <?php if ($activeTab === 'wishlist'): ?>
                <div id="wishlist" class="tab-content active">
                    <div class="tab-header">
                        <h2>Список желаний</h2>
                        <p>Ваши избранные товары</p>
                    </div>

                    <?php
                    $wishlistProducts = getWishlistProducts();
                    ?>
                    
                    <div class="wishlist-products">
                        <?php if (empty($wishlistProducts)): ?>
                            <div class="no-wishlist">
                                <p style="margin-bottom: 40px;">В вашем списке желаний пока нет товаров</p>
                                <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                            </div>
                        <?php else: ?>
                            <div class="products-grid">
                                <?php foreach ($wishlistProducts as $product): 
                                    // ИСПРАВЛЕНО: Используем сессию для проверки состояния корзины
                                    $isInCart = false;
                                    $cartQuantity = 0;
                                    
                                    if (isset($_SESSION['cart'][$product['id']])) {
                                        $isInCart = true;
                                        $cartItem = $_SESSION['cart'][$product['id']];
                                        if (is_array($cartItem) && isset($cartItem['quantity'])) {
                                            $cartQuantity = $cartItem['quantity'];
                                        } elseif (is_numeric($cartItem)) {
                                            $cartQuantity = $cartItem;
                                        }
                                    }
                                ?>
                                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                                    <div class="product-image">
                                        <div class="image-placeholder">
                                            <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo $product['name']; ?>" />
                                            <button class="wishlist-heart active" 
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    title="Удалить из избранного">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                                        <p class="product-description"><?php echo $product['description']; ?></p>
                                        <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
                                        <div class="cart-controls">
                                            <button class="add-to-cart dark--btn <?php echo $isInCart ? 'hidden' : ''; ?>" 
                                                    data-product-id="<?php echo $product['id']; ?>">
                                                В корзину
                                            </button>
                                            <div class="added-state <?php echo $isInCart ? '' : 'hidden'; ?>">
                                                <button class="added-btn" data-product-id="<?php echo $product['id']; ?>">Добавлено</button>
                                                <div class="cart-counter">
                                                    <button class="counter-btn minus">-</button>
                                                    <span class="count"><?php echo $cartQuantity > 0 ? $cartQuantity : 1; ?></span>
                                                    <button class="counter-btn plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- Модальное окно подтверждения выхода -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Подтверждение выхода</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Вы уверены, что хотите выйти из аккаунта?</p>
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-cancel">Отмена</button>
            <a href="?logout" class="modal-btn modal-logout">Выйти</a>
        </div>
    </div>
</div>

<script>
// Функция для обновления счетчика корзины
function updateCartCounter(count) {
    const cartBtn = document.querySelector(".cart-icon-btn");
    if (!cartBtn) return;
    
    let cartCount = document.querySelector(".cart-count");
    
    if (cartCount) cartCount.remove();
    
    if (count > 0) {
        cartCount = document.createElement("span");
        cartCount.className = "cart-count";
        cartCount.textContent = count;
        cartBtn.appendChild(cartCount);
    }
}

// Функции для модального окна выхода
function showLogoutModal(e) {
    e.preventDefault();
    const modal = document.getElementById('logoutModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideLogoutModal() {
    const modal = document.getElementById('logoutModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Обработчики модального окна выхода
document.querySelector('#logoutModal .modal-close').addEventListener('click', hideLogoutModal);
document.querySelector('#logoutModal .modal-cancel').addEventListener('click', hideLogoutModal);

document.getElementById('logoutModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideLogoutModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideLogoutModal();
    }
});

function removeFromCart(productId) {
    return fetch('ajax/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCounter(data.cart_count);
            // Обновляем состояние карточки товара
            const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
            if (productCard) {
                const cartControls = productCard.querySelector('.cart-controls');
                const addToCartBtn = cartControls.querySelector('.add-to-cart');
                const addedState = cartControls.querySelector('.added-state');
                
                addToCartBtn.classList.remove('hidden');
                addedState.classList.add('hidden');
            }
            return data;
        } else {
            console.error('Remove failed:', data.error);
            return false;
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        return false;
    });
}

function addToCart(productId) {
    return fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.cart_count !== undefined) {
            updateCartCounter(data.cart_count);
            return data;
        }
        return data;
    })
    .catch(error => {
        console.error('Ошибка:', error);
        return false;
    });
}

function updateCartQuantity(productId, quantity) {
    return fetch('ajax/update_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId + '&quantity=' + quantity
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.cart_count !== undefined) {
            updateCartCounter(data.cart_count);
        }
        return data;
    })
    .catch(error => {
        console.error('Ошибка:', error);
        return false;
    });
}

function removeFromWishlist(productId) {
    return fetch('ajax/simple_wishlist.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку из DOM
            const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
            if (productCard) {
                productCard.style.transition = 'all 0.3s ease';
                productCard.style.opacity = '0';
                productCard.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    productCard.remove();
                    
                    // Если больше нет товаров, показываем сообщение
                    const remainingProducts = document.querySelectorAll('.product-card');
                    if (remainingProducts.length === 0) {
                        const wishlistContainer = document.querySelector('.wishlist-products');
                        wishlistContainer.innerHTML = `
                            <div class="no-wishlist">
                                <p style="margin-bottom: 40px;">В вашем списке желаний пока нет товаров</p>
                                <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                            </div>
                        `;
                    }
                }, 300);
            }
            return true;
        } else {
            console.error('Wishlist remove failed:', data.error);
            return false;
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        return false;
    });
}

// Обработчики при загрузке страницы
document.addEventListener("DOMContentLoaded", function () {
    const initialCount = <?php echo $cartCount; ?>;
    updateCartCounter(initialCount);
    
    // Обработчики для сердечек вишлиста
    document.querySelectorAll('.wishlist-heart').forEach(heart => {
        heart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            removeFromWishlist(productId);
        });
    });
    
    // Обработчики корзины - добавление товара
    document.querySelectorAll(".add-to-cart").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const cartControls = this.closest(".cart-controls");
            
            this.classList.add('hidden');
            cartControls.querySelector('.added-state').classList.remove('hidden');
            
            addToCart(productId)
                .then(result => {
                    if (!result || !result.success) {
                        this.classList.remove('hidden');
                        cartControls.querySelector('.added-state').classList.add('hidden');
                    }
                });
        });
    });
    
    // Обработчики для кнопки "Добавлено" - удаление из корзины
    document.querySelectorAll('.added-btn').forEach((button) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const cartControls = this.closest(".cart-controls");
            const addToCartBtn = cartControls.querySelector('.add-to-cart');
            const counter = cartControls.querySelector('.cart-counter');
            const countElement = counter.querySelector('.count');
            
            countElement.textContent = '1';
            
            this.closest('.added-state').classList.add('hidden');
            addToCartBtn.classList.remove('hidden');
            
            removeFromCart(productId)
                .then(result => {
                    if (!result || !result.success) {
                        this.closest('.added-state').classList.remove('hidden');
                        addToCartBtn.classList.add('hidden');
                    }
                });
        });
    });
    
    // Обработчики счетчиков
    document.querySelectorAll(".counter-btn").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            const cartCounter = this.closest(".cart-counter");
            const countElement = cartCounter.querySelector(".count");
            const addedState = cartCounter.closest(".added-state");
            const addToCartBtn = addedState.previousElementSibling;
            let count = parseInt(countElement.textContent);
            const productId = addToCartBtn.dataset.productId;

            if (this.classList.contains("plus")) {
                count++;
                countElement.textContent = count;
                
                updateCartQuantity(productId, count);

            } else if (this.classList.contains("minus")) {
                count--;
                
                if (count <= 0) {
                    addedState.classList.add('hidden');
                    addToCartBtn.classList.remove('hidden');
                    countElement.textContent = '1';
                    
                    removeFromCart(productId);
                } else {
                    countElement.textContent = count;
                    updateCartQuantity(productId, count);
                }
            }
        });
    });
});


// Функции для работы с аватаром
function previewAvatar(input) {
    const preview = document.getElementById('avatarPreview');
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            // Сбрасываем флаг удаления если загружаем новый аватар
            document.getElementById('removeAvatar').value = '0';
        }
        
        reader.readAsDataURL(file);
    }
}

function removeAvatar() {
    if (confirm('Удалить аватар?')) {
        const preview = document.getElementById('avatarPreview');
        preview.src = 'assets/images/user--avatar.jpg';
        document.getElementById('avatarInput').value = '';
        document.getElementById('removeAvatar').value = '1';
    }
}

// Обработчик для показа overlay при фокусе
document.addEventListener('DOMContentLoaded', function() {
    const avatarContainer = document.querySelector('.avatar-container');
    
    if (avatarContainer) {
        // Для доступности - показываем overlay при фокусе
        avatarContainer.addEventListener('focus', function() {
            this.querySelector('.avatar-overlay').style.opacity = '1';
        });
        
        avatarContainer.addEventListener('blur', function() {
            this.querySelector('.avatar-overlay').style.opacity = '0';
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>