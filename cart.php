<?php
$pageTitle = "Корзина";
include 'includes/header.php';
if (isset($_SESSION['checkout_error'])) {
    // echo '<div class="alert error" style="margin: 20px auto; max-width: 1200px;">' . $_SESSION['checkout_error'] . '</div>';
    unset($_SESSION['checkout_error']);
}

// ИСПРАВЛЕНО: Используем сессию для корзины
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
                'description' => $product['description'],
                'price' => $product['price'],
                'image' => $product['image'],
                'category_id' => $product['category_id'],
                'category_name' => $product['category_name'],
                'quantity' => $quantity,
                'total_price' => $product['price'] * $quantity
            ];
            
            $cartTotal += $product['price'] * $quantity;
            $itemsCount++;
        }
    }
}

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

<main class="cart-main">
    <div class="inner">
        <div class="cart-header">
            <h1>Корзина</h1>
            <div class="cart-stats">
                <span class="items-count"><?php echo $itemsCount . ' ' . getNumEnding($itemsCount, ['товар', 'товара', 'товаров']); ?></span>
            </div>
        </div>
        
        <div id="cart-content">
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Ваша корзина пуста</h2>
                    <p>Добавьте товары из каталога, чтобы сделать заказ</p>
                    <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-main-section">
                        <div class="cart-section-header">
                            <label class="select-all">
                                <input type="checkbox" id="select-all-checkbox" checked>
                                <span class="checkmark"></span>
                                Выбрать все
                            </label>
                            <button class="delete-selected" id="delete-selected-btn">Удалить выбранные</button>
                        </div>

                        <div class="cart-items">
                            <?php foreach ($cartItems as $item): 
                                $isInWishlist = false;
                                if (isLoggedIn()) {
                                    $isInWishlist = isProductInWishlist($item['id']);
                                }
                            ?>
                            <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                                <div class="item-select">
                                    <input type="checkbox" class="item-checkbox" id="item-<?php echo $item['id']; ?>" checked>
                                    <label for="item-<?php echo $item['id']; ?>" class="item-checkmark"></label>
                                </div>
                                <div class="item-content">
                                    <div class="item-image">
                                        <img src="<?php echo getProductImage($item['image']); ?>" alt="<?php echo $item['name']; ?>" />
                                    </div>
                                    <div class="item-details">
                                        <div class="item-header">
                                            <div class="item-brand">Defender > Marasин</div>
                                            <h3 class="item-title"><?php echo $item['name']; ?></h3>
                                        </div>
                                        <p class="item-description"><?php echo $item['description']; ?></p>
                                        <div class="item-delivery">
                                            <span class="delivery-date">Послезавтра</span>
                                            <span class="delivery-type">· курьер · ПВЗ</span>
                                        </div>
                                    </div>
                                    <div class="item-prices">
                                        <div class="price-main">
                                            <span class="current-price"><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽</span>
                                        </div>
                                    </div>
                                    <div class="item-controls">
                                        <div class="cart-controls">
                                            <div class="cart-counter">
                                                <button type="button" class="counter-btn minus">-</button>
                                                <span class="count"><?php echo $item['quantity']; ?></span>
                                                <button type="button" class="counter-btn plus">+</button>
                                            </div>
                                        </div>
                                        <div class="item-actions">
                                            <?php if (isLoggedIn()): ?>
                                            <button type="button" class="wishlist-btn <?php echo $isInWishlist ? 'active' : ''; ?>" 
                                                    data-product-id="<?php echo $item['id']; ?>" 
                                                    title="<?php echo $isInWishlist ? 'Удалить из избранного' : 'Добавить в избранное'; ?>">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                                </svg>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="remove-btn" data-product-id="<?php echo $item['id']; ?>" title="Удалить">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="cart-sidebar">
                        <div class="order-summary">
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Товары (<span id="sidebar-items-count"><?php echo $itemsCount; ?></span>)</span>
                                    <span id="cart-total"><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                                </div>
                                <div class="summary-row">
                                    <span>Доставка</span>
                                    <span class="free-delivery">Бесплатно</span>
                                </div>
                                <div class="summary-row discount">
                                    <span>Скидка</span>
                                    <span class="discount-amount">-0 ₽</span>
                                </div>
                            </div>
                            
                            <div class="final-total">
                                <span>К оплате</span>
                                <span class="final-price" id="final-total"><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                            </div>
                            
                            <div class="payment-options">
                                <div class="payment-option">
                                    <span>С картой</span>
                                    <span class="payment-price"><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                                </div>
                                <div class="payment-option">
                                    <span>Без карты</span>
                                    <span class="payment-price"><?php echo number_format($cartTotal, 0, ',', ' '); ?> ₽</span>
                                </div>
                            </div>
                            <div class="split-payment">
                                <div class="split-option">
                                    <span>4 × <span id="split-price"><?php echo number_format(ceil($cartTotal / 4), 0, ',', ' '); ?></span> ₽</span>
                                    <span class="split-label">В рассрочку без переплаты</span>
                                </div>
                            </div>
                           <?php if (!empty($cartItems)): ?>
    <a href="checkout.php" class="checkout-btn dark--btn">
        Перейти к оформлению
    </a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Подтверждение удаления</h3>
            </div>
            <div class="modal-body">
                <p>Вы точно хотите удалить выбранные товары?</p>
                <p class="modal-warning">Отменить данное действие будет невозможно.</p>
            </div>
            <div class="modal-actions">
                <button class="modal-btn modal-cancel">Отмена</button>
                <button class="modal-btn modal-confirm">Удалить</button>
            </div>
        </div>
    </div>
</main>

<script>
let pendingDeleteItems = [];

function sendCartRequest(url, data) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    return fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json());
}

function updateCartQuantity(productId, quantity) {
    return sendCartRequest('ajax/update_cart.php', {
        'product_id': productId,
        'quantity': quantity
    })
    .then(data => {
        if (data.success) {
            updateCartUI(data);
            return true;
        } else {
            console.error('Update failed:', data.error);
            return false;
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        return false;
    });
}

function removeCartItem(productId) {
    return sendCartRequest('ajax/remove_from_cart.php', {
        'product_id': productId
    })
    .then(data => {
        if (data.success) {
            const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
            if (cartItem) {
                cartItem.style.transition = 'all 0.3s ease';
                cartItem.style.opacity = '0';
                cartItem.style.height = cartItem.offsetHeight + 'px';
                
                setTimeout(() => {
                    cartItem.remove();
                    updateCartUI(data);
                    
                    if (data.items_count === 0) {
                        showEmptyCart();
                    }
                }, 300);
            }
            return true;
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

// ДОБАВЛЕНО: Функция для удаления нескольких товаров
function removeMultipleCartItems(productIds) {
    // Создаем промисы для каждого удаления
    const removePromises = productIds.map(productId => 
        sendCartRequest('ajax/remove_from_cart.php', {
            'product_id': productId
        })
    );
    
    // Ждем завершения всех промисов
    return Promise.all(removePromises)
        .then(results => {
            // Проверяем все ли удаления прошли успешно
            const allSuccess = results.every(result => result.success);
            
            if (allSuccess) {
                // Обновляем UI на основе последнего результата
                const lastResult = results[results.length - 1];
                updateCartUI(lastResult);
                
                // Удаляем элементы из DOM
                productIds.forEach(productId => {
                    const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                    if (cartItem) {
                        cartItem.style.transition = 'all 0.3s ease';
                        cartItem.style.opacity = '0';
                        cartItem.style.height = cartItem.offsetHeight + 'px';
                        
                        setTimeout(() => {
                            cartItem.remove();
                        }, 300);
                    }
                });
                
                // Проверяем пустая ли корзина
                setTimeout(() => {
                    const remainingItems = document.querySelectorAll('.cart-item');
                    if (remainingItems.length === 0) {
                        showEmptyCart();
                    }
                }, 500);
                
                return { success: true };
            } else {
                console.error('Some removals failed');
                return { success: false, error: 'Не удалось удалить некоторые товары' };
            }
        })
        .catch(error => {
            console.error('Network error:', error);
            return { success: false, error: 'Ошибка сети' };
        });
}

function addToWishlist(productId) {
    return fetch('ajax/simple_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const wishlistBtn = document.querySelector(`.wishlist-btn[data-product-id="${productId}"]`);
            if (wishlistBtn) {
                wishlistBtn.classList.toggle('active', data.added);
                wishlistBtn.title = data.added ? 'Удалить из избранного' : 'Добавить в избранное';
                
                const catalogHearts = document.querySelectorAll(`.wishlist-heart[data-product-id="${productId}"]`);
                catalogHearts.forEach(heart => {
                    heart.classList.toggle('active', data.added);
                });
            }
            return true;
        } else {
            console.error('Wishlist error:', data.error);
            return false;
        }
    })
    .catch(error => {
        console.error('Network error:', error);
        return false;
    });
}

function updateCartUI(data) {
    updateCartCounter(data.cart_count);
    
    const cartTotalElement = document.getElementById('cart-total');
    const finalTotalElement = document.getElementById('final-total');
    const itemsCountElement = document.querySelector('.items-count');
    const sidebarItemsCount = document.getElementById('sidebar-items-count');
    const splitPrice = document.getElementById('split-price');
    
    if (cartTotalElement && data.cart_total !== undefined) {
        cartTotalElement.textContent = formatPrice(data.cart_total) + ' ₽';
    }
    if (finalTotalElement && data.cart_total !== undefined) {
        finalTotalElement.textContent = formatPrice(data.cart_total) + ' ₽';
    }
    if (itemsCountElement && data.items_count !== undefined) {
        itemsCountElement.textContent = data.items_count + ' ' + getNumEnding(data.items_count, ['товар', 'товара', 'товаров']);
    }
    if (sidebarItemsCount && data.items_count !== undefined) {
        sidebarItemsCount.textContent = data.items_count;
    }
    if (splitPrice && data.cart_total !== undefined) {
        splitPrice.textContent = formatPrice(Math.ceil(data.cart_total / 4));
    }
}

function showEmptyCart() {
    const cartContent = document.getElementById('cart-content');
    cartContent.innerHTML = `
        <div class="empty-cart">
            <h2>Ваша корзина пуста</h2>
            <p>Добавьте товары из каталога, чтобы сделать заказ</p>
            <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
        </div>
    `;
}

function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function getNumEnding(number, endings) {
    number = number % 100;
    if (number >= 11 && number <= 19) {
        return endings[2];
    }
    let n = number % 10;
    if (n === 1) {
        return endings[0];
    }
    if (n >= 2 && n <= 4) {
        return endings[1];
    }
    return endings[2];
}

function updateCartCounter(count) {
    const cartBtn = document.querySelector(".cart-icon-btn");
    let cartCount = document.querySelector(".cart-count");

    if (cartBtn) {
        if (count > 0) {
            if (cartCount) {
                cartCount.textContent = count;
            } else {
                cartCount = document.createElement("span");
                cartCount.className = "cart-count";
                cartCount.textContent = count;
                cartBtn.appendChild(cartCount);
            }
        } else {
            if (cartCount) {
                cartCount.remove();
            }
        }
    }
}

// ДОБАВЛЕНО: Функции для работы с выбранными товарами
function getSelectedProductIds() {
    const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
    const productIds = [];
    
    selectedCheckboxes.forEach(checkbox => {
        const cartItem = checkbox.closest('.cart-item');
        if (cartItem) {
            productIds.push(cartItem.dataset.productId);
        }
    });
    
    return productIds;
}

function updateSelectAllCheckbox() {
    const allCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    
    if (allCheckboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.disabled = true;
        deleteSelectedBtn.disabled = true;
        return;
    }
    
    const allChecked = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
    const anyChecked = Array.from(allCheckboxes).some(checkbox => checkbox.checked);
    
    selectAllCheckbox.checked = allChecked;
    deleteSelectedBtn.disabled = !anyChecked;
}

function initCartHandlers() {
    // Обработчики для чекбоксов
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllCheckbox();
        });
    }
    
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllCheckbox);
    });
    
    // Обработчик для кнопки "Удалить выбранные"
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedProductIds = getSelectedProductIds();
            
            if (selectedProductIds.length === 0) {
                alert('Выберите товары для удаления');
                return;
            }
            
            // Показываем модальное окно подтверждения
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
            
            // Обработчик подтверждения удаления
            const confirmBtn = modal.querySelector('.modal-confirm');
            const cancelBtn = modal.querySelector('.modal-cancel');
            
            const confirmHandler = () => {
                removeMultipleCartItems(selectedProductIds)
                    .then(result => {
                        if (result.success) {
                            modal.style.display = 'none';
                        } else {
                            alert('Ошибка при удалении товаров: ' + result.error);
                        }
                    });
                
                // Удаляем обработчики после использования
                confirmBtn.removeEventListener('click', confirmHandler);
                cancelBtn.removeEventListener('click', cancelHandler);
            };
            
            const cancelHandler = () => {
                modal.style.display = 'none';
                // Удаляем обработчики после использования
                confirmBtn.removeEventListener('click', confirmHandler);
                cancelBtn.removeEventListener('click', cancelHandler);
            };
            
            confirmBtn.addEventListener('click', confirmHandler);
            cancelBtn.addEventListener('click', cancelHandler);
        });
    }
    
    // Обработчики для счетчиков
    document.querySelectorAll('.cart-counter').forEach(counter => {
        const minusBtn = counter.querySelector('.counter-btn.minus');
        const plusBtn = counter.querySelector('.counter-btn.plus');
        const countElement = counter.querySelector('.count');
        const cartItem = counter.closest('.cart-item');
        const productId = cartItem ? cartItem.dataset.productId : null;
        
        if (!productId) return;
        
        if (minusBtn) {
            minusBtn.onclick = function() {
                let currentQuantity = parseInt(countElement.textContent);
                if (currentQuantity > 1) {
                    const newQuantity = currentQuantity - 1;
                    countElement.textContent = newQuantity;
                    updateCartQuantity(productId, newQuantity);
                } else {
                    if (confirm('Удалить товар из корзины?')) {
                        removeCartItem(productId);
                    }
                }
            };
        }
        
        if (plusBtn) {
            plusBtn.onclick = function() {
                let currentQuantity = parseInt(countElement.textContent);
                const newQuantity = currentQuantity + 1;
                countElement.textContent = newQuantity;
                updateCartQuantity(productId, newQuantity);
            };
        }
    });
    
    // Обработчики для кнопок удаления отдельных товаров
    document.querySelectorAll('.remove-btn').forEach(button => {
        const productId = button.dataset.productId;
        if (productId) {
            button.onclick = function() {
                if (confirm('Удалить товар из корзины?')) {
                    removeCartItem(productId);
                }
            };
        }
    });
    
    // Обработчики для кнопок избранного
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        const productId = button.dataset.productId;
        if (productId) {
            button.onclick = function() {
                addToWishlist(productId);
            };
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    try {
        initCartHandlers();
        updateSelectAllCheckbox(); // Инициализируем состояние чекбоксов
        const initialCount = <?php echo $cartCount; ?>;
        updateCartCounter(initialCount);
    } catch (error) {
        console.error('Initialization error:', error);
    }
});

// Функция для обновления кнопки оформления заказа
function updateCheckoutButton() {
    const checkoutBtn = document.querySelector('.checkout-btn');
    const cartItems = document.querySelectorAll('.cart-item');
    
    if (cartItems.length === 0) {
        // Если корзина пуста, скрываем кнопку
        if (checkoutBtn && checkoutBtn.style) {
            checkoutBtn.style.display = 'none';
        }
    } else {
        // Если в корзине есть товары, показываем кнопку как ссылку
        if (checkoutBtn && checkoutBtn.tagName === 'BUTTON') {
            const link = document.createElement('a');
            link.href = 'checkout.php';
            link.className = 'checkout-btn dark--btn';
            link.textContent = 'Перейти к оформлению';
            checkoutBtn.parentNode.replaceChild(link, checkoutBtn);
        } else if (checkoutBtn && checkoutBtn.tagName === 'A') {
            checkoutBtn.style.display = 'flex';
        }
    }
}

// Обновляем UI корзины
function updateCartUI(data) {
    updateCartCounter(data.cart_count);
    
    const cartTotalElement = document.getElementById('cart-total');
    const finalTotalElement = document.getElementById('final-total');
    const itemsCountElement = document.querySelector('.items-count');
    const sidebarItemsCount = document.getElementById('sidebar-items-count');
    const splitPrice = document.getElementById('split-price');
    
    if (cartTotalElement && data.cart_total !== undefined) {
        cartTotalElement.textContent = formatPrice(data.cart_total) + ' ₽';
    }
    if (finalTotalElement && data.cart_total !== undefined) {
        finalTotalElement.textContent = formatPrice(data.cart_total) + ' ₽';
    }
    if (itemsCountElement && data.items_count !== undefined) {
        itemsCountElement.textContent = data.items_count + ' ' + getNumEnding(data.items_count, ['товар', 'товара', 'товаров']);
    }
    if (sidebarItemsCount && data.items_count !== undefined) {
        sidebarItemsCount.textContent = data.items_count;
    }
    if (splitPrice && data.cart_total !== undefined) {
        splitPrice.textContent = formatPrice(Math.ceil(data.cart_total / 4));
    }
    
    // Обновляем состояние кнопки оформления заказа
    updateCheckoutButton();
}

// Инициализация при загрузке
document.addEventListener("DOMContentLoaded", function () {
    try {
        initCartHandlers();
        updateSelectAllCheckbox();
        const initialCount = <?php echo $cartCount; ?>;
        updateCartCounter(initialCount);
        updateCheckoutButton(); // Инициализируем состояние кнопки
    } catch (error) {
        console.error('Initialization error:', error);
    }
});
</script>

<?php include 'includes/footer.php'; ?>