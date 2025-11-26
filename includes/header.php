<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';
include 'auth.php';
include 'functions.php';

if (isset($_GET['logout'])) {
    logout();
}

$user = getUser();

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
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <?php
    // Подключаем дополнительные стили только если файл существует
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    $cssFile = "assets/css/{$currentPage}.css";
    if (file_exists($cssFile) && $currentPage != 'index') {
        echo '<link rel="stylesheet" href="' . $cssFile . '" />';
    }
    ?>
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ElecBrain' : 'ElecBrain'; ?></title>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="header--inner inner">
                <div class="logo">
                    <a href="index.php">
                        <p>ElecBrain</p>
                    </a>
                </div>
                <div class="search--header">
                    <form action="search.php" method="GET" class="search-wrapper">
                        <input class="search-input" type="text" name="q" placeholder="Поиск" value="<?php echo isset($_GET['q']) ? sanitize($_GET['q']) : ''; ?>" />
                        <button class="search-button" aria-label="search" type="submit">
                            <svg viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" />
                                <line x1="16.5" y1="16.5" x2="20" y2="20" />
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="nav">
                    <ul>
                        <li><a href="index.php"><p>Главная</p></a></li>
                        <li><a href="catalog.php"><p>Каталог</p></a></li>
                        <li><a href="about.php"><p>О нас</p></a></li>
                        <li><a href="faqs.php"><p>FAQs</p></a></li>
                    </ul>
                </div>
                <div class="header--buttons">
                    <button class="theme-toggle" aria-label="Переключить тему">
                        <svg class="moon-icon" viewBox="0 0 24 24">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                        </svg>
                        <svg class="sun-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="5" />
                            <line x1="12" y1="3" x2="12" y2="1" />
                            <line x1="12" y1="23" x2="12" y2="21" />
                            <line x1="20.66" y1="4.66" x2="22.22" y2="3.11" />
                            <line x1="1.78" y1="20.89" x2="3.34" y2="19.34" />
                            <line x1="20.66" y1="19.34" x2="22.22" y2="20.89" />
                            <line x1="1.78" y1="3.11" x2="3.34" y2="4.66" />
                            <line x1="4.66" y1="20.66" x2="3.11" y2="22.22" />
                            <line x1="20.89" y1="1.78" x2="19.34" y2="3.34" />
                            <line x1="20.89" y1="22.22" x2="19.34" y2="20.66" />
                            <line x1="4.66" y1="3.34" x2="3.11" y2="1.78" />
                        </svg>
                    </button>
                    
                    <?php if (isLoggedIn()): ?>
                        <a href="cart.php" class="cart-btn dark--btn cart-icon-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <?php if ($cartCount > 0): ?>
                                <span class="cart-count"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="account.php" class="dark--btn profile--btn">Профиль</a>
                    <?php else: ?>
                        <?php if (basename($_SERVER['PHP_SELF']) == 'login.php'): ?>
                            <a href="register.php" class="dark--btn register-btn">Регистрация</a>
                        <?php elseif (basename($_SERVER['PHP_SELF']) == 'register.php'): ?>
                            <a href="login.php" class="dark--btn login-btn">Войти</a>
                        <?php else: ?>
                            <a href="login.php" class="dark--btn login-btn">Войти</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="burger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Мобильное меню -->
        <div class="mobile-nav">
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="catalog.php">Каталог</a></li>
                <li><a href="about.php">О нас</a></li>
                <li><a href="faqs.php">FAQs</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="burger--profile"><a href="account.php">Личный кабинет</a></li>
                    <li class="burger--cart"><a href="cart.php">Корзина</a></li>
                    <li><a href="?logout=1">Выйти</a></li>
                <?php else: ?>
                    <?php if (basename($_SERVER['PHP_SELF']) == 'login.php'): ?>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Войти</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="overlay"></div>