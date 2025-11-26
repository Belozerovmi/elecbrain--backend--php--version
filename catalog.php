<?php
$pageTitle = "Каталог";
include 'includes/header.php';

$categoryFilter = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$products = getProducts($categoryFilter, null, $searchQuery);
$categories = getCategories();

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

<main class="catalog-main">
    <div class="catalog-header">
        <div class="catalog-header--inner inner">
            <h1 class="catalog-title">Каталог товаров</h1>
            <p class="catalog-subtitle">Умные устройства для современного дома</p>
        </div>
    </div>

    <div class="catalog-filters">
        <div class="catalog-filters--inner inner">
            <div class="filter-categories">
                <a href="catalog.php" class="filter-btn <?php echo $categoryFilter === 'all' ? 'active' : ''; ?>" data-category="all">
                    Все товары
                </a>
                <?php foreach ($categories as $category): ?>
                <a href="catalog.php?category=<?php echo $category['slug']; ?>" 
                   class="filter-btn <?php echo $categoryFilter === $category['slug'] ? 'active' : ''; ?>" 
                   data-category="<?php echo $category['slug']; ?>">
                    <?php echo $category['name']; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="catalog-products">
        <div class="catalog-products--inner inner">
            <?php if (!empty($searchQuery)): ?>
            <div class="search-results-info">
                <p>Найдено товаров: <?php echo count($products); ?> по запросу "<?php echo $searchQuery; ?>"</p>
            </div>
            <?php endif; ?>
            
            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <p style="margin-bottom: 40px;">Товары не найдены</p>
                        <a href="catalog.php" class="dark--btn">Вернуться в каталог</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): 
                        // ИСПРАВЛЕНО: Используем сессию для проверки состояния корзины
                        $isInCart = false;
                        $cartQuantity = 0;
                        $isInWishlist = false;
                        
                        if (isLoggedIn()) {
                            $isInWishlist = isProductInWishlist($product['id']);
                        }
                        
                        // Проверяем корзину в сессии (работает для всех пользователей)
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
                    <div class="product-card" data-category="<?php echo $product['category_slug']; ?>">
    <!-- Обертка-ссылка для всей карточки -->
    <a href="product.php?id=<?php echo $product['id']; ?>" class="product-card-link">
        <div class="product-image">
            <div class="image-placeholder">
                <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo $product['name']; ?>" />
                <?php if (isLoggedIn()): ?>
                <button class="wishlist-heart <?php echo $isInWishlist ? 'active' : ''; ?>" 
                        data-product-id="<?php echo $product['id']; ?>"
                        title="<?php echo $isInWishlist ? 'Удалить из избранного' : 'Добавить в избранное'; ?>">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="product-info">
            <h3 class="product-title"><?php echo $product['name']; ?></h3>
            <p class="product-description"><?php echo $product['description']; ?></p>
            <div class="product-price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
        </div>
    </a>
    
    <!-- Блок управления корзиной ВНЕ обертки-ссылки -->
    <div class="cart-controls">
        <?php if (isLoggedIn()): ?>
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
        <?php else: ?>
            <button class="add-to-cart dark--btn" onclick="showAuthModal()">
                В корзину
            </button>
        <?php endif; ?>
    </div>
</div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="authModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Требуется авторизация</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <p>Для добавления товаров в корзину необходимо войти в аккаунт.</p>
        </div>
        <div class="modal-footer">
            <button class="light--btn modal-cancel">Отмена</button>
            <a href="login.php" class="dark--btn">Войти</a>
        </div>
    </div>
</div>

<script>
function isUserLoggedIn() {
    return <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
}

function showAuthModal() {
    const modal = document.getElementById('authModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function hideAuthModal() {
    const modal = document.getElementById('authModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

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
            const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
            if (productCard) {
                productCard.style.transition = 'all 0.3s ease';
                productCard.style.opacity = '0';
                productCard.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    productCard.remove();
                    
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

function showWishlistNotification(message, isError = false) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${isError ? '#f44336' : '#4CAF50'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Обработчики для авторизованных пользователей
<?php if (isLoggedIn()): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для кнопки "В корзину"
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

    // Обработчик для кнопки "Добавлено"
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

    // Обработчик для кнопок счетчика
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

    // Обработчик для кнопки избранного
    document.querySelectorAll('.wishlist-heart').forEach(heart => {
        heart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            const wasActive = this.classList.contains('active');
            
            fetch('ajax/simple_wishlist.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.toggle('active');
                    this.title = data.added ? 'Удалить из избранного' : 'Добавить в избранное';
                    
                    // Показываем уведомление
                    showWishlistNotification(data.added ? 'Товар добавлен в избранное' : 'Товар удален из избранного');
                } else {
                    console.error('Wishlist error:', data.error);
                    showWishlistNotification('Ошибка: ' + data.error, true);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showWishlistNotification('Ошибка сети', true);
            });
        });
    });

    // Предотвращаем переход по ссылке при клике на кнопки внутри карточки
    document.querySelectorAll('.product-card .cart-controls, .product-card .wishlist-heart').forEach(element => {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});
<?php endif; ?>

// Обработчики для неавторизованных пользователей
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для кнопки "В корзину" для неавторизованных
    document.querySelectorAll(".add-to-cart:not([data-product-id])").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
        });
    });

    // Обработчик для кнопки избранного для неавторизованных
    document.querySelectorAll('.wishlist-heart:not([data-product-id])').forEach(heart => {
        heart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
        });
    });

    // Предотвращаем переход по ссылке при клике на кнопки внутри карточки для неавторизованных
    document.querySelectorAll('.product-card .cart-controls, .product-card .wishlist-heart').forEach(element => {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});

// Обработчики модального окна
document.addEventListener('DOMContentLoaded', function() {
    const modalClose = document.querySelector('.modal-close');
    const modalCancel = document.querySelector('.modal-cancel');
    const authModal = document.getElementById('authModal');
    
    if (modalClose) {
        modalClose.addEventListener('click', hideAuthModal);
    }
    
    if (modalCancel) {
        modalCancel.addEventListener('click', hideAuthModal);
    }
    
    if (authModal) {
        authModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideAuthModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAuthModal();
        }
    });
});

// Инициализация счетчика корзины при загрузке
document.addEventListener("DOMContentLoaded", function () {
    const initialCount = <?php echo $cartCount; ?>;
    updateCartCounter(initialCount);
});

// Улучшение UX: добавляем визуальную обратную связь для карточек
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.product-card-link').forEach(link => {
        link.addEventListener('mousedown', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        link.addEventListener('mouseup', function() {
            this.style.transform = 'scale(1)';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
<?php include 'includes/footer.php'; ?>