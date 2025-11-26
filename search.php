<?php
$pageTitle = "Поиск";
include 'includes/header.php';

$searchQuery = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$categoryFilter = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

// Получаем товары с фильтрацией на сервере
if (!empty($searchQuery)) {
    $products = getProducts($categoryFilter, null, $searchQuery);
} else {
    $products = [];
}

$categories = getCategories();
$cartCount = getCartCount();

// Получаем количество товаров в каждой категории для поиска
$categoryCounts = [];
foreach ($categories as $category) {
    $categoryProducts = getProducts($category['slug'], null, $searchQuery);
    $categoryCounts[$category['slug']] = count($categoryProducts);
}

// Общее количество товаров по поисковому запросу
$allProducts = getProducts('all', null, $searchQuery);
$totalCount = count($allProducts);
?>

<main class="search-main">
    <div class="search-header">
        <div class="inner">
            <h1>Поиск товаров</h1>
            
            <!-- Форма поиска -->
           

            <?php if (!empty($searchQuery)): ?>
            <div class="search-filters">
                <div class="filter-categories">
                    <a href="search.php?q=<?php echo urlencode($searchQuery); ?>" 
                       class="filter-btn <?php echo $categoryFilter === 'all' ? 'active' : ''; ?>">
                        Все товары
                        <span class="filter-count">(<?php echo $totalCount; ?>)</span>
                    </a>
                    <?php foreach ($categories as $category): ?>
                    <a href="search.php?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $category['slug']; ?>" 
                       class="filter-btn <?php echo $categoryFilter === $category['slug'] ? 'active' : ''; ?>">
                        <?php echo $category['name']; ?>
                        <span class="filter-count">(<?php echo $categoryCounts[$category['slug']]; ?>)</span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="search-results">
        <div class="inner">
            <?php if (empty($searchQuery)): ?>
                <div class="no-results">
                    <h2>Начните поиск</h2>
                    <p>Введите название товара, бренд или категорию в поле поиска выше</p>
                    <div class="search-suggestions">
                        <p>Популярные запросы:</p>
                        <div class="suggestion-tags">
                            <a href="search.php?q=умные+часы" class="suggestion-tag">Умные часы</a>
                            <a href="search.php?q=беспроводные+наушники" class="suggestion-tag">Беспроводные наушники</a>
                            <a href="search.php?q=smart+home" class="suggestion-tag">Smart Home</a>
                            <a href="search.php?q=игровые+аксессуары" class="suggestion-tag">Игровые аксессуары</a>
                        </div>
                    </div>
                </div>
            <?php elseif (empty($products)): ?>
                <div class="no-results">
                    <h2>Ничего не найдено</h2>
                    <p>По запросу <strong>"<?php echo $searchQuery; ?>"</strong> 
                    <?php if ($categoryFilter !== 'all'): ?>
                        в категории <strong>
                            <?php 
                            $categoryName = '';
                            foreach ($categories as $cat) {
                                if ($cat['slug'] === $categoryFilter) {
                                    $categoryName = $cat['name'];
                                    break;
                                }
                            }
                            echo $categoryName;
                            ?>
                        </strong>
                    <?php endif; ?>
                    товаров не найдено</p>
                    
                    <!-- <div class="search-suggestions">
                        <p>Попробуйте:</p>
                        <ul>
                            <li>Проверить правильность написания</li>
                            <li>Использовать другие ключевые слова</li>
                            <li>Искать в <a href="search.php?q=<?php echo urlencode($searchQuery); ?>">других категориях</a></li>
                        </ul>
                    </div> -->
                    <a href="catalog.php" class="dark--btn">Перейти в каталог</a>
                </div>
            <?php else: ?>
                <div class="results-info">
                    <p>Найдено <strong><?php echo count($products); ?> товаров</strong> по запросу <strong>"<?php echo $searchQuery; ?>"</strong></p>
                    <?php if ($categoryFilter !== 'all'): ?>
                        <?php 
                        $categoryName = '';
                        foreach ($categories as $cat) {
                            if ($cat['slug'] === $categoryFilter) {
                                $categoryName = $cat['name'];
                                break;
                            }
                        }
                        ?>
                        <p class="current-filter">Категория: <strong><?php echo $categoryName; ?></strong></p>
                    <?php endif; ?>
                </div>
                
                <div class="products-grid">
                    <?php foreach ($products as $product): 
                        $isInCart = isProductInCart($product['id']);
                        $cartQuantity = getProductCartQuantity($product['id']);
                    ?>
                    <div class="product-card">
                        <div class="product-image">
                            <div class="image-placeholder">
                                <img src="<?php echo getProductImage($product['image']); ?>" alt="<?php echo $product['name']; ?>" />
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
                                    <button class="added-btn">Добавлено</button>
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
</main>

<!-- Модальное окно авторизации -->
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
// Функция для проверки авторизации
function isUserLoggedIn() {
    return <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
}

// Функции для работы с модальным окном
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

// ОДИН обработчик для кнопок "В корзину"
let addToCartClickCount = 0;
document.querySelectorAll(".add-to-cart").forEach((button) => {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        
        addToCartClickCount++;
        console.log(`🖱️ КЛИК №${addToCartClickCount} на "В корзину"`);
        
        if (!isUserLoggedIn()) {
            console.log('🔐 Показываем модалку авторизации');
            showAuthModal();
            return;
        }
        
        const productId = this.dataset.productId;
        const cartControls = this.closest(".cart-controls");
        const addToCartBtn = cartControls.querySelector(".add-to-cart");
        const addedState = cartControls.querySelector(".added-state");
        const countElement = addedState.querySelector(".count");

        console.log(`🛒 Добавляем товар ID: ${productId}`);

        // БЛОКИРУЕМ кнопку
        this.disabled = true;
        this.style.opacity = '0.6';

        // ОДИН AJAX запрос
        console.time('AJAX-Request');
        fetch('ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => {
            console.timeEnd('AJAX-Request');
            return response.json();
        })
        .then(data => {
            console.log('✅ Ответ сервера:', data);
            
            if (data.success) {
                // Обновляем интерфейс
                countElement.textContent = data.product_quantity || 1;
                addToCartBtn.classList.add('hidden');
                addedState.classList.remove('hidden');
                
                // ОБНОВЛЯЕМ СЧЕТЧИК КОРЗИНЫ
                if (data.cart_count !== undefined) {
                    updateCartCounter(data.cart_count);
                    console.log(`🔄 Обновлен счетчик корзины: ${data.cart_count}`);
                } else {
                    console.log('❌ cart_count не получен от сервера');
                    updateCartCounter(getCurrentCartCount() + 1);
                }
                
                console.log(`🎉 Товар добавлен! В корзине: ${data.cart_count || (getCurrentCartCount() + 1)} товаров`);
            }
            
            // РАЗБЛОКИРУЕМ кнопку
            this.disabled = false;
            this.style.opacity = '1';
        })
        .catch(error => {
            console.error('❌ Ошибка:', error);
            this.disabled = false;
            this.style.opacity = '1';
        });
    });
});

// Упрощенная версия для кнопки "Добавлено"
document.querySelectorAll(".added-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        
        const cartControls = this.closest(".cart-controls");
        const addToCartBtn = cartControls.querySelector(".add-to-cart");
        const addedState = cartControls.querySelector(".added-state");
        const productId = addToCartBtn.dataset.productId;

        console.log('🗑️ Удаление товара:', productId);

        // Переключение видимости
        addedState.classList.add('hidden');
        addToCartBtn.classList.remove('hidden');

        // AJAX запрос
        fetch('ajax/remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Удалено:', data);
            if (data.success) {
                updateCartCounter(data.cart_count);
            }
        })
        .catch(error => {
            console.error('❌ Ошибка удаления:', error);
            updateCartCounter(getCurrentCartCount() - 1);
        });
    });
});

// Обработчики для счетчиков
document.querySelectorAll(".counter-btn").forEach((button) => {
    button.addEventListener("click", function (e) {
        e.preventDefault();
        
        const cartCounter = this.closest(".cart-counter");
        const countElement = cartCounter.querySelector(".count");
        const addedState = cartCounter.closest(".added-state");
        const addToCartBtn = addedState.previousElementSibling;
        let count = parseInt(countElement.textContent);
        const productId = addToCartBtn.dataset.productId;

        console.log('🔢 Действие:', this.classList.contains('plus') ? '+' : '-', 'Текущее:', count);

        if (this.classList.contains("plus")) {
            count++;
            countElement.textContent = count;
            
            fetch('ajax/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'product_id=' + productId + '&quantity=' + count
            })
            .then(response => response.json())
            .then(data => {
                console.log('✅ Обновлено:', data);
                if (data.success) {
                    updateCartCounter(data.cart_count);
                }
            })
            .catch(error => {
                console.error('❌ Ошибка:', error);
                updateCartCounter(getCurrentCartCount() + 1);
            });

        } else if (this.classList.contains("minus")) {
            count--;
            
            if (count <= 0) {
                // Удаляем товар
                addedState.classList.add('hidden');
                addToCartBtn.classList.remove('hidden');
                countElement.textContent = '1';
                
                fetch('ajax/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Удалено:', data);
                    if (data.success) {
                        updateCartCounter(data.cart_count);
                    }
                })
                .catch(error => {
                    console.error('❌ Ошибка удаления:', error);
                    updateCartCounter(getCurrentCartCount() - 1);
                });
            } else {
                countElement.textContent = count;
                
                fetch('ajax/update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&quantity=' + count
                })
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Обновлено:', data);
                    if (data.success) {
                        updateCartCounter(data.cart_count);
                    }
                })
                .catch(error => {
                    console.error('❌ Ошибка:', error);
                    updateCartCounter(getCurrentCartCount() - 1);
                });
            }
        }
    });
});

// Функция для обновления счетчика корзины
function updateCartCounter(count) {
    console.log(`🔄 updateCartCounter вызван с: ${count}`);
    
    const cartBtn = document.querySelector(".cart-icon-btn");
    if (!cartBtn) {
        console.log('❌ Не найдена кнопка корзины .cart-icon-btn');
        return;
    }
    
    let cartCount = document.querySelector(".cart-count");

    if (count > 0) {
        if (cartCount) {
            cartCount.textContent = count;
            console.log(`✅ Обновлен существующий счетчик: ${count}`);
        } else {
            cartCount = document.createElement("span");
            cartCount.className = "cart-count";
            cartCount.textContent = count;
            cartBtn.appendChild(cartCount);
            console.log(`✅ Создан новый счетчик: ${count}`);
        }
    } else {
        if (cartCount) {
            cartCount.remove();
            console.log('✅ Счетчик удален (корзина пуста)');
        }
    }
}

// Функция для получения текущего количества товаров в корзине
function getCurrentCartCount() {
    const cartCount = document.querySelector(".cart-count");
    const count = cartCount ? parseInt(cartCount.textContent) : 0;
    console.log(`📊 Текущий счетчик: ${count}`);
    return count;
}

// Обработчики для модального окна
document.querySelector('.modal-close').addEventListener('click', hideAuthModal);
document.querySelector('.modal-cancel').addEventListener('click', hideAuthModal);

document.getElementById('authModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideAuthModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAuthModal();
    }
});

// При загрузке страницы
document.addEventListener("DOMContentLoaded", function () {
    const initialCount = <?php echo getCartCount(); ?>;
    updateCartCounter(initialCount);
    console.log('🚀 Поиск загружен. Товаров в корзине:', initialCount);
});
</script>

<?php include 'includes/footer.php'; ?>