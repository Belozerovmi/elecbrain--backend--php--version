<?php
$pageTitle = "Частые вопросы";
include 'includes/header.php';
?>

<div class="wrapper">


     <main class="faqs-main">
        <section class="faqs-hero">
          <div class="faqs-hero--inner inner">
            <h1 class="faqs-title">Часто задаваемые вопросы</h1>
            <p class="faqs-subtitle">
              Найдите ответы на самые популярные вопросы об умных устройствах и
              их использовании
            </p>
          </div>
        </section>

        <section class="faqs-content">
          <div class="faqs-content--inner inner">
            <div class="faqs-categories">
              <button class="faq-category-btn active" data-category="all">
                Все вопросы
              </button>
              <button class="faq-category-btn" data-category="installation">
                Установка
              </button>
              <button class="faq-category-btn" data-category="usage">
                Использование
              </button>
              <button class="faq-category-btn" data-category="troubleshooting">
                Решение проблем
              </button>
              <button class="faq-category-btn" data-category="warranty">
                Гарантия
              </button>
            </div>

            <div class="faqs-list">
              <!-- Установка -->
              <div class="faq-item" data-category="installation">
                <button class="faq-question">
                  <span>Как установить умную колонку?</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>Установка умной колонки занимает всего несколько минут:</p>
                  <ol>
                    <li>Подключите колонку к розетке</li>
                    <li>
                      Скачайте приложение ElecBrain из App Store или Google Play
                    </li>
                    <li>
                      Следуйте инструкциям в приложении для подключения к Wi-Fi
                    </li>
                    <li>
                      Настройте голосового ассистента и основные параметры
                    </li>
                  </ol>
                </div>
              </div>

              <div class="faq-item" data-category="installation">
                <button class="faq-question">
                  <span>Какие требования к Wi-Fi для работы устройств?</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>Для стабильной работы наших устройств рекомендуется:</p>
                  <ul>
                    <li>Скорость интернета не менее 10 Мбит/с</li>
                    <li>Частота Wi-Fi 2.4 ГГц (обязательно) или 5 ГГц</li>
                    <li>Стабильное соединение без частых разрывов</li>
                    <li>Открытые порты для корректной работы всех функций</li>
                  </ul>
                </div>
              </div>

              <!-- Использование -->
              <div class="faq-item" data-category="usage">
                <button class="faq-question">
                  <span>Как управлять устройствами через приложение?</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>
                    Управление через приложение ElecBrain интуитивно понятно:
                  </p>
                  <ul>
                    <li>Создайте сцены для автоматизации повседневных задач</li>
                    <li>Настройте расписание работы устройств</li>
                    <li>
                      Используйте голосовые команды через интеграцию с
                      ассистентами
                    </li>
                    <li>
                      Создавайте зоны для группового управления устройствами
                    </li>
                  </ul>
                </div>
              </div>

              <div class="faq-item" data-category="usage">
                <button class="faq-question">
                  <span
                    >Можно ли интегрировать устройства с другими
                    системами?</span
                  >
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>
                    Да, наши устройства поддерживают интеграцию с популярными
                    системами:
                  </p>
                  <ul>
                    <li>Яндекс Алиса</li>
                    <li>Салют (СБЕР)</li>
                    <li>Google Assistant и Google Home</li>
                    <li>Apple HomeKit (через мост)</li>
                    <li>IFTTT для создания сложных сценариев</li>
                    <li>Zigbee и Z-Wave совместимые устройства</li>
                  </ul>
                </div>
              </div>

              <!-- Решение проблем -->
              <div class="faq-item" data-category="troubleshooting">
                <button class="faq-question">
                  <span>Устройство не подключается к Wi-Fi</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>
                    Если устройство не подключается к Wi-Fi, выполните следующие
                    шаги:
                  </p>
                  <ol>
                    <li>Перезагрузите роутер и устройство</li>
                    <li>Убедитесь, что Wi-Fi работает на других устройствах</li>
                    <li>Проверьте правильность ввода пароля</li>
                    <li>
                      Убедитесь, что устройство находится в зоне действия Wi-Fi
                    </li>
                    <li>Попробуйте подключиться к сети 2.4 ГГц вместо 5 ГГц</li>
                    <li>
                      Если проблема не исчезла, выполните сброс к заводским
                      настройкам
                    </li>
                  </ol>
                </div>
              </div>

              <div class="faq-item" data-category="troubleshooting">
                <button class="faq-question">
                  <span>Голосовой ассистент не отвечает на команды</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>Если ассистент не реагирует на команды:</p>
                  <ul>
                    <li>Проверьте подключение к интернету</li>
                    <li>Убедитесь, что микрофон не заблокирован</li>
                    <li>Произносите команды четко и вблизи устройства</li>
                    <li>Проверьте настройки конфиденциальности в приложении</li>
                    <li>Обновите прошивку устройства до последней версии</li>
                  </ul>
                </div>
              </div>

              <!-- Гарантия -->
              <div class="faq-item" data-category="warranty">
                <button class="faq-question">
                  <span>Какая гарантия предоставляется на устройства?</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>
                    Мы предоставляем расширенную гарантию на все наши
                    устройства:
                  </p>
                  <ul>
                    <li>Стандартная гарантия - 24 месяца</li>
                    <li>
                      Расширенная гарантия доступна при регистрации устройства
                    </li>
                    <li>
                      Бесплатная замена в течение первых 14 дней при обнаружении
                      дефектов
                    </li>
                    <li>
                      Бесплатный ремонт в авторизованных сервисных центрах
                    </li>
                    <li>Техническая поддержка 24/7</li>
                  </ul>
                </div>
              </div>

              <div class="faq-item" data-category="warranty">
                <button class="faq-question">
                  <span>Как воспользоваться гарантийным обслуживанием?</span>
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>
                <div class="faq-answer">
                  <p>Для получения гарантийного обслуживания:</p>
                  <ol>
                    <li>Сохраните оригинальную упаковку и документы</li>
                    <li>Обратитесь в техподдержку через приложение или сайт</li>
                    <li>Предоставьте серийный номер устройства</li>
                    <li>Опишите возникшую проблему</li>
                    <li>Следуйте инструкциям специалиста поддержки</li>
                  </ol>
                </div>
              </div>
            </div>

            <div class="faqs-contact">
                <div class="faqs--contact--inner">
              <h3>Не нашли ответ на свой вопрос?</h3>
              <p>Наша служба поддержки всегда готова помочь вам</p>
              <button class="dark--btn contact-btn">
                Связаться с поддержкой
              </button>
              </div>
            </div>
          </div>
        </section>
      </main>
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
        document.body.style.overflow = burgerMenu.classList.contains("active")
          ? "hidden"
          : "";
      });

      overlay.addEventListener("click", () => {
        burgerMenu.classList.remove("active");
        mobileNav.classList.remove("active");
        overlay.classList.remove("active");
        document.body.style.overflow = "";
      });

      // FAQ аккордеон
      document.querySelectorAll(".faq-question").forEach((button) => {
        button.addEventListener("click", () => {
          const faqItem = button.parentElement;
          const isExpanded = button.getAttribute("aria-expanded") === "true";

          // Закрываем все вопросы
          document.querySelectorAll(".faq-item").forEach((item) => {
            item.classList.remove("active");
            item
              .querySelector(".faq-question")
              .setAttribute("aria-expanded", "false");
          });

          // Открываем текущий вопрос, если он был закрыт
          if (!isExpanded) {
            faqItem.classList.add("active");
            button.setAttribute("aria-expanded", "true");
          }
        });
      });

      // Фильтрация FAQ по категориям
      document.querySelectorAll(".faq-category-btn").forEach((button) => {
        button.addEventListener("click", () => {
          // Убираем активный класс у всех кнопок
          document.querySelectorAll(".faq-category-btn").forEach((btn) => {
            btn.classList.remove("active");
          });

          // Добавляем активный класс текущей кнопке
          button.classList.add("active");

          const category = button.getAttribute("data-category");
          const faqItems = document.querySelectorAll(".faq-item");

          faqItems.forEach((item) => {
            if (
              category === "all" ||
              item.getAttribute("data-category") === category
            ) {
              item.style.display = "block";
            } else {
              item.style.display = "none";
            }
          });
        });
      });

      // Кнопка связи с поддержкой
      document.querySelector(".contact-btn").addEventListener("click", () => {
        window.location.href = "mailto:support@elecbrain.ru";
      });
    </script>

<?php include 'includes/footer.php'; ?>