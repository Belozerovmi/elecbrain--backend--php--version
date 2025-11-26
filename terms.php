<?php
$pageTitle = "Условия использования";
include 'includes/header.php';
?>

<main class="terms-main">
        <section class="terms-hero">
          <div class="terms-hero--inner inner">
            <h1 class="terms-title">Условия пользования</h1>
            <p class="terms-subtitle">
              Ознакомьтесь с правилами использования наших продуктов и услуг
            </p>
          </div>
        </section>

        <section class="terms-content">
          <div class="terms-content--inner inner">
            <div class="terms-info">
              <p class="last-updated">
                Последнее обновление: 1 января 2025 года
              </p>
            </div>

            <div class="terms-sections">
              <div class="terms-section">
                <h2>1. Общие положения</h2>
                <p>
                  Настоящие Условия пользования регулируют отношения между
                  ElecBrain (далее — «Компания») и пользователями (далее —
                  «Пользователь») в отношении использования продуктов, услуг и
                  веб-сайта Компании.
                </p>
                <p>
                  Регистрируясь на сайте или используя наши продукты, вы
                  соглашаетесь с настоящими Условиями пользования.
                </p>
              </div>

              <div class="terms-section">
                <h2>2. Регистрация и учетная запись</h2>
                <p>
                  Для доступа к некоторым функциям наших продуктов требуется
                  создание учетной записи. При регистрации вы обязуетесь:
                </p>
                <ul>
                  <li>Предоставлять точную и полную информацию о себе</li>
                  <li>Немедленно обновлять информацию при ее изменении</li>
                  <li>Обеспечивать конфиденциальность своих учетных данных</li>
                  <li>
                    Нести ответственность за все действия, совершенные под вашей
                    учетной записью
                  </li>
                </ul>
              </div>

              <div class="terms-section">
                <h2>3. Использование продуктов</h2>
                <p>
                  Вы соглашаетесь использовать наши продукты исключительно в
                  законных целях и в соответствии с настоящими Условиями.
                  Запрещается:
                </p>
                <ul>
                  <li>Использовать продукты для незаконной деятельности</li>
                  <li>Нарушать права интеллектуальной собственности</li>
                  <li>Распространять вредоносное программное обеспечение</li>
                  <li>
                    Предпринимать попытки несанкционированного доступа к
                    системам
                  </li>
                  <li>Создавать помехи для работы продуктов или сетей</li>
                </ul>
              </div>

              <div class="terms-section">
                <h2>4. Интеллектуальная собственность</h2>
                <p>
                  Все права на программное обеспечение, дизайн, контент и другие
                  материалы, связанные с нашими продуктами, принадлежат Компании
                  или ее лицензиарам. Вы получаете ограниченную, непередаваемую
                  лицензию на использование наших продуктов в личных целях.
                </p>
              </div>

              <div class="terms-section">
                <h2>5. Ограничение ответственности</h2>
                <p>
                  Продукты предоставляются «как есть». Компания не гарантирует
                  бесперебойную работу продуктов или их соответствие конкретным
                  требованиям Пользователя.
                </p>
                <p>
                  Компания не несет ответственности за косвенные убытки,
                  упущенную выгоду или ущерб, возникший в результате
                  использования или невозможности использования продуктов.
                </p>
              </div>

              <div class="terms-section">
                <h2>6. Изменения условий</h2>
                <p>
                  Компания оставляет за собой право изменять настоящие Условия в
                  любое время. Изменения вступают в силу с момента их публикации
                  на сайте. Продолжение использования продуктов после внесения
                  изменений означает ваше согласие с новыми Условиями.
                </p>
              </div>

              <div class="terms-section">
                <h2>7. Прекращение действия</h2>
                <p>
                  Компания может приостановить или прекратить доступ к продуктам
                  в случае нарушения Пользователем настоящих Условий. Вы также
                  можете прекратить использование продуктов в любое время.
                </p>
              </div>

              <div class="terms-section">
                <h2>8. Применимое право</h2>
                <p>
                  Настоящие Условия регулируются законодательством Российской
                  Федерации. Все споры подлежат разрешению в судах по месту
                  нахождения Компании.
                </p>
              </div>

              <div class="terms-section">
                <h2>9. Контактная информация</h2>
                <p>
                  По вопросам, связанным с настоящими Условиями, обращайтесь:
                </p>
                <p>
                  Email: legal@elecbrain.ru<br />
                  Телефон: +7 (495) 123-45-67<br />
                  Адрес: Москва, улица Льва Толстого, 16
                </p>
              </div>
            </div>

            <div class="terms-acceptance">
              <div class="acceptance-box">
                <h3>Принятие условий</h3>
                <p>
                  Регистрируясь на сайте или используя наши продукты, вы
                  подтверждаете, что прочитали, поняли и согласны с настоящими
                  Условиями пользования.
                </p>
                <button class="dark--btn back-to-registration">
                  Вернуться к регистрации
                </button>
              </div>
            </div>
          </div>
        </section>
      </main>
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

      // Кнопка возврата к регистрации
      document
        .querySelector(".back-to-registration")
        .addEventListener("click", () => {
          window.history.back();
        });
    </script>
<?php include 'includes/footer.php'; ?>