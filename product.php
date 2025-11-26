<?php
$pageTitle = "Страница товара";
include 'includes/header.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catalog.php");
    exit;
}



$productId = (int)$_GET['id'];
$product = getProductById($productId);

if (!$product) {
    header("Location: catalog.php");
    exit;
}

// Увеличиваем счетчик просмотров
incrementProductViews($productId);

// Получаем галерею изображений
$galleryImages = getProductGallery($productId);
// ВРЕМЕННАЯ ОТЛАДКА - удалить после исправления
// if ($productId == 2) { // ID товара
//     echo "<div style='background: #ffebee; padding: 10px; margin: 10px; border: 2px solid red;'>";
//     echo "<h3>CAMERA DEBUG:</h3>";
//     echo "Product Image from DB: " . $product['image'] . "<br>";
//     echo "Gallery images:<br>";
//     foreach ($galleryImages as $index => $img) {
//         echo "[$index] " . $img['image_path'] . "<br>";
        
//         // Проверим разные варианты
//         $testNames = [
//             $img['image_path'],
//             preg_replace('/-\d+(\.(jpg|png|jpeg))$/i', '$1', $img['image_path']),
//             str_replace('--', '-', $img['image_path']),
//             preg_replace('/-\d+(\.(jpg|png|jpeg))$/i', '$1', str_replace('--', '-', $img['image_path']))
//         ];
        
//         foreach ($testNames as $test) {
//             $path = 'assets/images/catalog--photos/' . $test;
//             echo "&nbsp;&nbsp;→ $test: " . (file_exists($path) ? '✅ EXISTS' : '❌ NOT FOUND') . "<br>";
//         }
//     }
//     echo "</div>";
// }
$hasMultipleImages = count($galleryImages) > 1;
$hasMultipleImages = count($galleryImages) > 1;
$mainImage = $galleryImages[0]['image_path'] ?? $product['image'];


// Получаем отзывы и статистику
$reviews = getProductReviewsWithPurchaseInfo($productId);
$ratingStats = getProductRatingStats($productId);

// Проверяем возможность оставить отзыв
$canReview = false;
$hasUserReviewed = false;
$hasPurchasedProduct = false;

if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $hasPurchasedProduct = hasUserPurchasedProduct($userId, $productId);
    $hasUserReviewed = hasUserReviewedProduct($productId, $userId);
    $canReview = $hasPurchasedProduct && !$hasUserReviewed;
    
    $isInWishlist = isProductInWishlist($productId);
}

// Проверяем корзину
$isInCart = false;
$cartQuantity = 0;

if (isset($_SESSION['cart'][$productId])) {
    $isInCart = true;
    $cartItem = $_SESSION['cart'][$productId];
    if (is_array($cartItem) && isset($cartItem['quantity'])) {
        $cartQuantity = $cartItem['quantity'];
    } elseif (is_numeric($cartItem)) {
        $cartQuantity = $cartItem;
    }
}

// Получаем характеристики
$specifications = [];
if ($product['specifications']) {
    $specifications = json_decode($product['specifications'], true);
}
$specsCount = count($specifications);

// Получаем детальное описание
$detailedDescription = $product['detailed_description'] ?? $product['description'];

// Обработка формы отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $comment = sanitize($_POST['comment']);
    
    // Проверяем, что пользователь действительно покупал товар
    if (!$hasPurchasedProduct) {
        $errorMessage = 'Вы можете оставить отзыв только на купленный товар';
    } elseif ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $result = addReview($productId, $_SESSION['user_id'], $rating, $comment);
        if ($result['success']) {
            $successMessage = $result['message'];
            // Обновляем данные
            $reviews = getProductReviewsWithPurchaseInfo($productId);
            $ratingStats = getProductRatingStats($productId);
            $hasUserReviewed = true;
            $canReview = false;
        } else {
            $errorMessage = $result['error'];
        }
    } else {
        $errorMessage = 'Пожалуйста, заполните все поля правильно';
    }
}


?>

<main class="product-main">
    <div class="inner">
        <!-- Хлебные крошки -->
        <nav class="breadcrumbs">
            <a href="catalog.php">Каталог</a>
            <span>/</span>
            <a href="catalog.php?category=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a>
            <span>/</span>
            <span class="current"><?php echo $product['name']; ?></span>
        </nav>

        <div class="product-container">
            <!-- Левая часть - изображение -->
<div class="product-image-section">
    <div class="main-image" id="mainImageContainer">
        <?php
        // Получаем корректный путь к основному изображению
        $mainImagePath = getProductImage($mainImage);
        ?>
        <img src="<?php echo $mainImagePath; ?>" alt="<?php echo $product['name']; ?>" id="mainProductImage" />
        
        <!-- Кнопка избранного -->
        <?php if (isLoggedIn()): ?>
        <button class="wishlist-heart <?php echo $isInWishlist ? 'active' : ''; ?>" 
                data-product-id="<?php echo $product['id']; ?>"
                title="<?php echo $isInWishlist ? 'Удалить из избранного' : 'Добавить в избранное'; ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        <?php else: ?>
        <button class="wishlist-heart" onclick="showAuthModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
            </svg>
        </button>
        <?php endif; ?>
    </div>
    
    <!-- Миниатюры показываем ТОЛЬКО если есть несколько РАЗНЫХ изображений -->
    <?php 
    // Фильтруем дубликаты и проверяем что есть минимум 2 разных изображения
    $uniqueImages = [];
    foreach ($galleryImages as $image) {
        $imgPath = getProductImage($image['image_path']);
        if (!in_array($imgPath, $uniqueImages)) {
            $uniqueImages[] = $imgPath;
        }
    }
    $realMultipleImages = count($uniqueImages) > 1;
    ?>
    
    <?php if ($realMultipleImages): ?>
    <div class="image-thumbnails">
        <?php foreach ($galleryImages as $index => $image): 
            $thumbnailPath = getProductImage($image['image_path']);
            // Пропускаем дубликаты
            if (in_array($thumbnailPath, array_slice($uniqueImages, 0, $index))) continue;
        ?>
        <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
             data-image="<?php echo $thumbnailPath; ?>">
            <img src="<?php echo $thumbnailPath; ?>" alt="<?php echo $product['name']; ?>" />
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

            <!-- Правая часть - информация -->
            <div class="product-info-section">
                <div class="product-header">
                    <h1 class="product-title"><?php echo $product['name']; ?></h1>
                    <div class="product-category"><?php echo $product['category_name']; ?></div>
                </div>

                <div class="product-description">
                    <p><?php echo $product['description']; ?></p>
                </div>

                <div class="product-price-section">
                    <div class="price"><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</div>
                    <div class="price-note">Цена с картой</div>
                </div>

                <div class="delivery-info">
                    <div class="delivery-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>Доставка: послезавтра</span>
                    </div>
                    <div class="delivery-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4M4 6v6c0 1.1.9 2 2 2h14v-6"></path>
                        </svg>
                        <span>Самовывоз: бесплатно</span>
                    </div>
                </div>

                <!-- Основные кнопки действий -->
                <div class="product-actions-main">
                    <!-- Кнопка "Купить сейчас" -->
                    <?php if (isLoggedIn()): ?>
                        <button class="add-to-cart buy-now-btn dark--btn" data-product-id="<?php echo $product['id']; ?>">
                            Купить сейчас
                        </button>
                    <?php else: ?>
                        <button class="add-to-cart buy-now-btn dark--btn" onclick="showAuthModal()">
                            Купить сейчас
                        </button>
                    <?php endif; ?>

                    <!-- Блок управления корзиной -->
                    <div class="cart-wishlist-row">
                        <div class="cart-controls">
                            <?php if (isLoggedIn()): ?>
                                <?php if ($isInCart): ?>
                                    <div class="added-state">
                                        <button class="added-btn" data-product-id="<?php echo $product['id']; ?>">Добавлено</button>
                                        <div class="cart-counter">
                                            <button class="counter-btn minus">-</button>
                                            <span class="count"><?php echo $cartQuantity > 0 ? $cartQuantity : 1; ?></span>
                                            <button class="counter-btn plus">+</button>
                                        </div>
                                    </div>
                                    <button class="add-to-cart dark--btn hidden" data-product-id="<?php echo $product['id']; ?>">
                                        В корзину
                                    </button>
                                <?php else: ?>
                                    <button class="add-to-cart dark--btn" data-product-id="<?php echo $product['id']; ?>">
                                        В корзину
                                    </button>
                                    <div class="added-state hidden">
                                        <button class="added-btn" data-product-id="<?php echo $product['id']; ?>">Добавлено</button>
                                        <div class="cart-counter">
                                            <button class="counter-btn minus">-</button>
                                            <span class="count">1</span>
                                            <button class="counter-btn plus">+</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="add-to-cart dark--btn" onclick="showAuthModal()">
                                    В корзину
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="product-features">
                    <div class="feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <span>Гарантия 1 год</span>
                    </div>
                    <div class="feature">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Официальная гарантия</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Детальная информация -->
        <div class="product-details">
            <!-- Описание с аккордеоном -->
            <div class="details-section">
                <h2>Описание</h2>
                <div class="description-content" id="descriptionContent">
                    <div class="description-text">
                        <?php echo nl2br(htmlspecialchars($detailedDescription)); ?>
                    </div>
                    <div class="description-fade"></div>
                </div>
                <button class="expand-description-btn hidden" id="expandDescriptionBtn">
                    <span>Показать полностью</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>

            <!-- Характеристики через grid -->
            <div class="details-section">
                <div class="section-header">
                    <h2>Характеристики</h2>
                </div>
                
                <?php if (!empty($specifications)): ?>
                <div class="specs-grid-container">
                    <div class="specs-grid" id="specsGrid">
                        <?php foreach ($specifications as $key => $value): ?>
                        <div class="spec-row">
                            <div class="spec-name"><?php echo htmlspecialchars($key); ?></div>
                            <div class="spec-value"><?php echo htmlspecialchars($value); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="expand-specs-btn hidden" id="expandSpecsBtn">
                        <span>Показать все характеристики</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </div>
                <?php else: ?>
                <div class="no-specs">
                    <p>Характеристики временно отсутствуют</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Блок отзывов -->
            <div class="details-section reviews-section">
                <div class="reviews-header">
                    <h2>Отзывы о товаре</h2>
                    <?php if ($canReview): ?>
                    <button class="add-review-btn" id="showReviewFormBtn">Написать отзыв</button>
                    <?php elseif (isLoggedIn() && !$hasPurchasedProduct): ?>
                    <div class="review-notice">
                        <small>Отзывы могут оставлять только покупатели товара</small>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($ratingStats && $ratingStats['total_reviews'] > 0): ?>
                <div class="reviews-stats">
                    <div class="average-rating">
                        <div class="rating-big"><?php echo number_format($ratingStats['average_rating'], 1); ?></div>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star"><?php echo $i <= round($ratingStats['average_rating']) ? '★' : '☆'; ?></span>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-count"><?php echo $ratingStats['total_reviews']; ?> отзывов</div>
                    </div>
                    
                    <div class="rating-bars">
                        <?php for ($rating = 5; $rating >= 1; $rating--): 
                            $count = $ratingStats["rating_{$rating}"] ?? 0;
                            $percentage = $ratingStats['total_reviews'] > 0 ? ($count / $ratingStats['total_reviews']) * 100 : 0;
                        ?>
                        <div class="rating-bar">
                            <span><?php echo $rating; ?></span>
                            <span class="star">★</span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span><?php echo $count; ?></span>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Форма добавления отзыва -->
                <?php if ($canReview): ?>
                <form class="review-form hidden" id="reviewForm" method="POST">
                    <h3>Оставить отзыв</h3>
                    
                    <?php if (isset($successMessage)): ?>
                    <div class="success-message"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($errorMessage)): ?>
                    <div class="error-message"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    
                    <div class="rating-input">
                        <label>Ваша оценка:</label>
                        <div class="stars-input">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="comment">Ваш отзыв:</label>
                        <textarea name="comment" id="comment" placeholder="Поделитесь вашим мнением о товаре..." required></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="cancel-review-btn" id="cancelReviewBtn">Отмена</button>
                        <button type="submit" name="submit_review" class="submit-review-btn">Опубликовать отзыв</button>
                    </div>
                </form>
                <?php endif; ?>

                <!-- Список отзывов -->
                <div class="reviews-list">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-avatar">
                                        <?php echo strtoupper(substr($review['first_name'] ?? 'U', 0, 1) . substr($review['last_name'] ?? 'S', 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="reviewer-name">
                                            <?php echo htmlspecialchars(($review['first_name'] ?? 'Пользователь') . ' ' . ($review['last_name'] ?? '')); ?>
                                        </div>
                                        <div class="review-meta">
                                            <span class="review-date">
                                                <?php echo date('d.m.Y', strtotime($review['created_at'])); ?>
                                            </span>
                                            <?php if ($review['has_purchased']): ?>
                                            <span class="purchased-badge">Покупатель</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star"><?php echo $i <= $review['rating'] ? '★' : '☆'; ?></span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="review-content">
                                <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-reviews">
                            <p>Пока нет отзывов о этом товаре</p>
                            <?php if (isLoggedIn() && $hasPurchasedProduct && !$hasUserReviewed): ?>
                            <p>Будьте первым, кто оставит отзыв!</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- Модальное окно для просмотра изображений -->
<div class="image-modal" id="imageModal">
    <button class="image-modal-close" id="modalClose">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
    
    <button class="image-modal-nav image-modal-prev" id="modalPrev">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>
    
    <button class="image-modal-nav image-modal-next" id="modalNext">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>
    
    <div class="image-modal-content">
        <img src="" alt="" class="image-modal-img" id="modalImage">
    </div>
    
    <div class="image-modal-counter" id="modalCounter">1 / 1</div>
</div>

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
// Функции для работы с корзиной и избранным остаются без изменений
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
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCounter(data.cart_count);
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

function buyNow(productId) {
    return fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCounter(data.cart_count);
            window.location.href = 'cart.php';
            return data;
        }
        return data;
    })
    .catch(error => {
        console.error('Ошибка:', error);
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

// Функция для проверки переполнения контента
function checkContentOverflow() {
    const descriptionContent = document.getElementById('descriptionContent');
    const specsGrid = document.getElementById('specsGrid');
    const expandDescriptionBtn = document.getElementById('expandDescriptionBtn');
    const expandSpecsBtn = document.getElementById('expandSpecsBtn');
    
    // Проверяем описание
    if (descriptionContent && expandDescriptionBtn) {
        const descriptionHeight = descriptionContent.scrollHeight;
        const maxHeight = 150; // Максимальная высота до обрезки
        
        if (descriptionHeight > maxHeight) {
            descriptionContent.classList.add('collapsed');
            expandDescriptionBtn.classList.remove('hidden');
        } else {
            descriptionContent.classList.remove('collapsed');
            expandDescriptionBtn.classList.add('hidden');
        }
    }
    
    // Проверяем характеристики
    if (specsGrid && expandSpecsBtn) {
        const specsHeight = specsGrid.scrollHeight;
        const maxSpecsHeight = 400; // Максимальная высота для характеристик
        
        if (specsHeight > maxSpecsHeight) {
            specsGrid.classList.add('collapsed');
            expandSpecsBtn.classList.remove('hidden');
        } else {
            specsGrid.classList.remove('collapsed');
            expandSpecsBtn.classList.add('hidden');
        }
    }
}

// Переключение изображений
document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', function() {
        const mainImage = document.querySelector('.main-image img');
        const newImageSrc = this.getAttribute('data-image');
        
        mainImage.src = newImageSrc;
        
        document.querySelectorAll('.thumbnail').forEach(item => {
            item.classList.remove('active');
        });
        this.classList.add('active');
    });
});

// Плавный аккордеон для описания
document.getElementById('expandDescriptionBtn')?.addEventListener('click', function() {
    const descriptionContent = document.getElementById('descriptionContent');
    const fadeElement = descriptionContent.querySelector('.description-fade');
    
    descriptionContent.classList.remove('collapsed');
    if (fadeElement) {
        fadeElement.style.display = 'none';
    }
    this.style.display = 'none';
});

// Плавное раскрытие характеристик
document.getElementById('expandSpecsBtn')?.addEventListener('click', function() {
    const specsGrid = document.getElementById('specsGrid');
    specsGrid.classList.remove('collapsed');
    this.style.display = 'none';
});

// Управление формой отзыва
document.getElementById('showReviewFormBtn')?.addEventListener('click', function() {
    const reviewForm = document.getElementById('reviewForm');
    reviewForm.classList.remove('hidden');
    this.style.display = 'none';
});

document.getElementById('cancelReviewBtn')?.addEventListener('click', function() {
    const reviewForm = document.getElementById('reviewForm');
    const showBtn = document.getElementById('showReviewFormBtn');
    reviewForm.classList.add('hidden');
    if (showBtn) showBtn.style.display = 'block';
});

// Обработчики для авторизованных пользователей
<?php if (isLoggedIn()): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик для кнопки "Купить сейчас"
    document.querySelectorAll(".buy-now-btn").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            
            this.innerHTML = 'Добавляем...';
            this.disabled = true;
            
            buyNow(productId)
                .then(result => {
                    if (!result || !result.success) {
                        this.innerHTML = 'Купить сейчас';
                        this.disabled = false;
                        console.error('Buy now failed:', result?.error);
                    }
                });
        });
    });

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
});
<?php endif; ?>

// Обработчики для неавторизованных пользователей
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll(".buy-now-btn:not([data-product-id])").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
        });
    });

    document.querySelectorAll(".add-to-cart:not([data-product-id])").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
        });
    });

    document.querySelectorAll('.wishlist-heart:not([data-product-id])').forEach(heart => {
        heart.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            showAuthModal();
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

// Инициализация при загрузке страницы
document.addEventListener("DOMContentLoaded", function () {
    // Проверяем переполнение контента
    setTimeout(checkContentOverflow, 100);
    
    // Инициализация счетчика корзины
    const initialCount = <?php echo isset($_SESSION['cart']) ? array_reduce($_SESSION['cart'], function($carry, $item) {
        if (is_array($item) && isset($item['quantity'])) return $carry + $item['quantity'];
        elseif (is_numeric($item)) return $carry + $item;
        return $carry;
    }, 0) : 0; ?>;
    updateCartCounter(initialCount);
});

// Проверяем переполнение при изменении размера окна
window.addEventListener('resize', checkContentOverflow);



// Модальное окно для просмотра изображений
function initImageModal() {
    const mainImage = document.getElementById('mainProductImage');
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalClose = document.getElementById('modalClose');
    const modalPrev = document.getElementById('modalPrev');
    const modalNext = document.getElementById('modalNext');
    const modalCounter = document.getElementById('modalCounter');
    
    if (!mainImage || !modal) return;
    
    // Получаем все уникальные изображения
    const thumbnails = document.querySelectorAll('.thumbnail');
    let images = [];
    let currentImageIndex = 0;
    
    // Собираем все изображения (основное + миниатюры)
    function collectImages() {
        images = [];
        
        // Добавляем основное изображение
        images.push(mainImage.src);
        
        // Добавляем уникальные миниатюры
        thumbnails.forEach(thumb => {
            const imgSrc = thumb.getAttribute('data-image');
            if (!images.includes(imgSrc)) {
                images.push(imgSrc);
            }
        });
        
        // Обновляем счетчик
        updateCounter();
    }
    
    // Обновляем счетчик
    function updateCounter() {
        if (modalCounter) {
            modalCounter.textContent = `${currentImageIndex + 1} / ${images.length}`;
        }
    }
    
    // Открываем модальное окно
    function openModal(imageIndex = 0) {
        currentImageIndex = imageIndex;
        modalImage.src = images[currentImageIndex];
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        updateCounter();
        
        // Показываем/скрываем кнопки навигации
        if (images.length <= 1) {
            modalPrev.style.display = 'none';
            modalNext.style.display = 'none';
        } else {
            modalPrev.style.display = 'flex';
            modalNext.style.display = 'flex';
        }
    }
    
    // Закрываем модальное окно
    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        
        // Небольшая задержка перед сбросом трансформации
        setTimeout(() => {
            modalImage.style.transform = 'scale(0.8)';
        }, 300);
    }
    
    // Переход к следующему изображению
    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        modalImage.src = images[currentImageIndex];
        modalImage.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            modalImage.style.transform = 'scale(1)';
        }, 50);
        
        updateCounter();
    }
    
    // Переход к предыдущему изображению
    function prevImage() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        modalImage.src = images[currentImageIndex];
        modalImage.style.transform = 'scale(0.8)';
        
        setTimeout(() => {
            modalImage.style.transform = 'scale(1)';
        }, 50);
        
        updateCounter();
    }
    
    // Обработчики событий для основного изображения
    mainImage.addEventListener('click', function() {
        collectImages();
        openModal(0);
    });
    
    // Обработчики для миниатюр
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            collectImages();
            const clickedImageSrc = this.getAttribute('data-image');
            const imageIndex = images.indexOf(clickedImageSrc);
            openModal(imageIndex);
        });
    });
    
    // Обработчики для модального окна
    modalClose.addEventListener('click', closeModal);
    modalNext.addEventListener('click', nextImage);
    modalPrev.addEventListener('click', prevImage);
    
    // Закрытие по клику на фон
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
    
    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
        
        // Навигация стрелками
        if (modal.classList.contains('active')) {
            if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'ArrowLeft') {
                prevImage();
            }
        }
    });
    
    // Предзагрузка изображений для плавной навигации
    function preloadImages() {
        images.forEach(src => {
            const img = new Image();
            img.src = src;
        });
    }
    
    // Инициализация
    collectImages();
    preloadImages();
}

// Инициализируем модальное окно при загрузке
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initImageModal, 100);
});
</script>




<?php include 'includes/footer.php'; ?>