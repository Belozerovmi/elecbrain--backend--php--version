        <footer>
            <div class="footer--inner">
                <div class="footer--main">
                    <div class="footer--brand">
                        <div class="footer--logo">
                            <a href="index.php">
                                <h1>ElecBrain</h1>
                            </a>
                            <p class="footer--tagline">Больше, чем просто комфорт</p>
                            <p class="footer--description">
                                Мы создаем умные решения для вашего дома, объединяя передовые
                                технологии с безупречным дизайном. Превратите ваше жилое
                                пространство в цифровой оазис.
                            </p>
                        </div>
                        <div class="footer--newsletter">
                            <h3>Будьте в курсе новинок</h3>
                            <form class="newsletter--form" method="POST">
                                <input type="email" name="newsletter_email" placeholder="Ваш email" class="newsletter-input" required />
                                <button type="submit" class="newsletter-btn">Подписаться</button>
                            </form>
                        </div>
                    </div>

                    <div class="footer--content">
                        <div class="footer--nav--sections">
                            <div class="footer--section">
                                <h4>Навигация</h4>
                                <ul>
                                    <li><a href="index.php">Главная</a></li>
                                    <li><a href="catalog.php">Каталог</a></li>
                                    <li><a href="about.php">О нас</a></li>
                                    <li><a href="faqs.php">FAQs</a></li>
                                    <li><a href="support.php">Поддержка</a></li>
                                    <li><a href="account.php">Личный кабинет</a></li>
                                </ul>
                            </div>

                            <div class="footer--section">
                                <h4>Категории</h4>
                                <ul>
                                    <?php
                                    $categories = getCategories();
                                    foreach ($categories as $category): 
                                    ?>
                                    <li><a href="catalog.php?category=<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div class="footer--section">
                                <h4>Поддержка</h4>
                                <ul>
                                    <li><a href="support.php">Техническая поддержка</a></li>
                                    <li><a href="warranty.php">Гарантия и сервис</a></li>
                                    <li><a href="delivery.php">Доставка и оплата</a></li>
                                    <li><a href="returns.php">Возврат товара</a></li>
                                    <li><a href="privacy.php">Политика конфиденциальности</a></li>
                                    <li><a href="terms.php">Условия использования</a></li>
                                </ul>
                            </div>

                            <div class="footer--section">
                                <h4>Контакты</h4>
                                <div class="contact-info">
                                    <div class="contact-item">
                                        <span><a href="mailto:info@elecbrain.ru">info@elecbrain.ru</a></span>
                                    </div>
                                    <div class="contact-item">
                                        <span><a href="tel:74951234567">+7 (495) 123-45-67</a></span>
                                    </div>
                                    <div class="contact-item">
                                        <span>Москва, улица Льва Толстого, 16</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="footer--bottom">
                    <div class="social--media--links">
                        <h4>Мы в соцсетях:</h4>
                        <ul class="social--media">
                            <li>
                                <a href="https://m.vk.com/" target="_blank" aria-label="ВКонтакте" class="vk">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 2A10 10 0 0 0 2 12a10 10 0 0 0 10 10a10 10 0 0 0 10-10A10 10 0 0 0 12 2M5.5 8.5H7c.36 0 .5.16.64.57c.72 2.1 1.93 3.93 2.43 3.93c.19 0 .28-.08.28-.55v-2.17c-.06-1-.59-1.09-.59-1.44c0-.17.14-.34.38-.34h2.31c.32 0 .42.17.42.54v2.92c0 .31.13.42.23.42c.19 0 .35-.11.69-.45c1.06-1.19 1.81-3.01 1.81-3.01c.1-.22.27-.42.64-.42h1.47c.45 0 .55.23.45.54c-.19.86-1.98 3.39-1.98 3.39c-.18.25-.22.37 0 .66c.15.21.67.65 1.01 1.06c.64.71 1.11 1.31 1.25 1.72c.12.42-.09.63-.51.63h-1.48c-.56 0-.72-.45-1.72-1.45c-.88-.84-1.23-.95-1.47-.95c-.3 0-.39.08-.39.51v1.32c0 .36-.11.57-1.05.57c-1.56 0-3.28-.95-4.49-2.7C5.5 11.24 5 9.31 5 8.92c0-.22.08-.42.5-.42z"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://web.telegram.org/" target="_blank" aria-label="Telegram" class="telegram">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <g fill="none">
                                            <g clip-path="url(#SVGXv8lpc2Y)">
                                                <path fill-rule="evenodd" d="M24 12c0 6.627-5.373 12-12 12S0 18.627 0 12S5.373 0 12 0s12 5.373 12 12M12.43 8.859q-1.75.728-6.998 3.014q-.852.339-.893.663c-.046.366.412.51 1.034.705l.263.084c.613.199 1.437.432 1.865.441q.583.012 1.302-.48q4.902-3.31 5.061-3.346c.075-.017.179-.039.249.024c.07.062.063.18.056.212c-.046.193-1.84 1.862-2.77 2.726c-.29.269-.495.46-.537.504q-.143.145-.282.279c-.57.548-.996.96.024 1.632c.49.323.882.59 1.273.856c.427.291.853.581 1.405.943q.21.14.405.28c.497.355.944.673 1.496.623c.32-.03.652-.331.82-1.23c.397-2.126 1.179-6.73 1.36-8.628a2 2 0 0 0-.02-.472a.5.5 0 0 0-.172-.325c-.143-.117-.365-.142-.465-.140c-.451.008-1.143.249-4.476 1.635" clip-rule="evenodd"/>
                                            </g>
                                            <defs>
                                                <clipPath id="SVGXv8lpc2Y">
                                                    <path fill="#fff" d="M0 0h24v24H0z"/>
                                                </clipPath>
                                            </defs>
                                        </g>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.youtube.com/" target="_blank" aria-label="YouTube" class="youtube">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path d="M11.603 9.833L9.357 8.785C9.161 8.694 9 8.796 9 9.013v1.974c0 .217.161.319.357.228l2.245-1.048c.197-.092.197-.242.001-.334M10 .4C4.698.4.4 4.698.4 10s4.298 9.6 9.6 9.6s9.6-4.298 9.6-9.6S15.302.4 10 .4m0 13.5c-4.914 0-5-.443-5-3.9s.086-3.9 5-3.9s5 .443 5 3.9s-.086 3.9-5 3.9"/>
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.tiktok.com/ru-RU/" target="_blank" aria-label="TikTok" class="tiktok">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12 2a10 10 0 1 0 10 10A10.01 10.01 0 0 0 12 2m5.939 7.713v.646a.37.37 0 0 1-.38.37a5.36 5.36 0 0 1-2.903-1.108v4.728a3.94 3.94 0 0 1-1.18 2.81a4 4 0 0 1-2.87 1.17a4.1 4.1 0 0 1-2.862-1.17a3.98 3.98 0 0 1-1.026-3.805c.159-.642.48-1.232.933-1.713a3.58 3.58 0 0 1 2.79-1.313h.82v1.703a.348.348 0 0 1-.39.348a1.918 1.918 0 0 0-1.23 3.631c.27.155.572.246.882.267c.24.01.48-.02.708-.092a1.93 1.93 0 0 0 1.313-1.816V5.754a.36.36 0 0 1 .359-.36h1.415a.36.36 0 0 1 .359.34a3.3 3.3 0 0 0 1.282 2.245a3.25 3.25 0 0 0 1.641.636a.37.37 0 0 1 .338.35z"/>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="copyright">
                        <p>&copy; 2025 ElecBrain. Все права защищены.</p>
                        <div class="footer--links">
                            <a href="privacy.php">Политика конфиденциальности</a>
                            <a href="terms.php">Условия использования</a>
                            <a href="sitemap.php">Карта сайта</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Скрипт переключения темы
        const themeToggle = document.querySelector(".theme-toggle");
        const body = document.body;

        const savedTheme = localStorage.getItem("theme");
        if (savedTheme) {
            body.setAttribute("data-theme", savedTheme);
        }

        themeToggle.addEventListener("click", () => {
            const currentTheme = body.getAttribute("data-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";

            body.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        });

        // Бургер меню
        const burgerMenu = document.querySelector(".burger-menu");
        const mobileNav = document.querySelector(".mobile-nav");
        const overlay = document.querySelector(".overlay");

        burgerMenu.addEventListener("click", () => {
            burgerMenu.classList.toggle("active");
            mobileNav.classList.toggle("active");
            overlay.classList.toggle("active");
            document.body.style.overflow = burgerMenu.classList.contains("active") ? "hidden" : "";
        });

        overlay.addEventListener("click", () => {
            burgerMenu.classList.remove("active");
            mobileNav.classList.remove("active");
            overlay.classList.remove("active");
            document.body.style.overflow = "";
        });

        // ИСПРАВЛЕННЫЙ СКРИПТ ДЛЯ СЧЕТЧИКА КОРЗИНЫ
        function updateCartCounter(count) {
            const cartBtn = document.querySelector(".cart-icon-btn");
            if (!cartBtn) return;
            
            // Находим существующий счетчик
            let cartCount = cartBtn.querySelector(".cart-count");
            
            // Если счетчик есть - удаляем его
            if (cartCount) {
                cartCount.remove();
            }
            
            // Если количество больше 0 - создаем новый счетчик
            if (count > 0) {
                cartCount = document.createElement("span");
                cartCount.className = "cart-count";
                cartCount.textContent = count;
                cartBtn.appendChild(cartCount);
            }
        }

        // Функция для получения текущего количества товаров в корзине
        function getCurrentCartCount() {
            // Используем данные из PHP переменной, которая передается из header.php
            return <?php echo isset($cartCount) ? $cartCount : 0; ?>;
        }

        // Функция для обновления счетчика после AJAX операций
        function handleCartUpdate(data) {
            if (data && data.cart_count !== undefined) {
                updateCartCounter(data.cart_count);
            }
        }

        // Инициализация счетчика при загрузке страницы
        document.addEventListener("DOMContentLoaded", function() {
            // Устанавливаем начальное значение счетчика
            const initialCount = getCurrentCartCount();
            updateCartCounter(initialCount);
            
            // Перехватываем все AJAX запросы для обновления счетчика
            const originalFetch = window.fetch;
            window.fetch = function(...args) {
                return originalFetch.apply(this, args).then(response => {
                    // Клонируем response чтобы можно было прочитать его несколько раз
                    const clone = response.clone();
                    
                    // Проверяем если это запрос к корзине
                    if (args[0] && (args[0].includes('add_to_cart.php') || 
                                     args[0].includes('remove_from_cart.php') || 
                                     args[0].includes('update_cart.php'))) {
                        clone.json().then(data => {
                            handleCartUpdate(data);
                        }).catch(() => {
                            // Игнорируем ошибки парсинга
                        });
                    }
                    
                    return response;
                });
            };

            // Также обновляем счетчик при клике на кнопки корзины (на всякий случай)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-to-cart') || 
                    e.target.closest('.added-btn') || 
                    e.target.closest('.counter-btn') ||
                    e.target.closest('.remove-btn')) {
                    
                    // Ждем немного чтобы AJAX запрос успел выполниться
                    setTimeout(() => {
                        // Можно сделать дополнительный запрос для проверки актуального состояния
                        // Но обычно это не нужно, т.к. перехват fetch уже обрабатывает обновления
                    }, 100);
                }
            });
        });

        // Добавление в корзину (для совместимости со старым кодом)
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-to-cart')) {
                e.preventDefault();
                const productId = e.target.dataset.productId;
                
                fetch('ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Счетчик обновится автоматически через перехват fetch
                        
                        // Показываем уведомление
                        showNotification('Товар добавлен в корзину');
                    }
                });
            }
        });

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #4CAF50;
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

        // Дополнительная защита: периодическая проверка состояния корзины
        setInterval(() => {
            // Только если пользователь авторизован
            if (<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
                fetch('ajax/get_cart_count.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const currentCounter = document.querySelector('.cart-count');
                            const currentCount = currentCounter ? parseInt(currentCounter.textContent) : 0;
                            
                            // Обновляем только если количество изменилось
                            if (data.count !== currentCount) {
                                updateCartCounter(data.count);
                            }
                        }
                    })
                    .catch(() => {
                        // Игнорируем ошибки
                    });
            }
        }, 30000); // Проверяем каждые 30 секунд
    </script>
</body>
</html>