<?php
$pageTitle = "Главная";
include 'includes/header.php';

// Получаем популярные товары
// Убедитесь, что передается число
$featuredProducts = getProducts('all', 8);


// Для отладки - что возвращает функция
echo "<!-- Debug: Products count: " . count($featuredProducts) . " -->";
?>


<div class="main--section">
    <div class="main--section--inner inner">
        <div class="container">
            <h1 class="title">ElecBrain</h1>
            <div class="photo-wrapper">
                <div class="photo-with-gradient"></div>
            </div>
        </div>
    </div>
</div>

<div class="why--us--section">
    <div class="why--us--section--inner">
        <div class="accordion-wrapper">
            <h2 class="heading">Почему выбирают нас</h2>

            <div class="accordion-item">
                <button class="accordion-button" aria-expanded="true" aria-controls="panel-1" id="accordion-1">
                    Умные колонки и голосовые ассистенты
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="panel-1" role="region" aria-labelledby="accordion-1" class="accordion-content expanded">
                    Наши умные колонки и голосовые ассистенты интегрируются во все экосистемы вашего дома. Они позволяют управлять техникой, освещением и климатом с помощью простых голосовых команд, создавая по-настоящему интерактивную среду
                </div>
            </div>
            
            <div class="accordion-item">
                <button class="accordion-button" aria-expanded="false" aria-controls="panel-2" id="accordion-2">
                    Системы безопасности и автоматизации
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="panel-2" role="region" aria-labelledby="accordion-2" class="accordion-content">
                    Современные системы безопасности обеспечивают круглосуточный контроль за вашим домом. Умные датчики, камеры видеонаблюдения и автоматизированные протоколы защиты создают многоуровневую систему безопасности, которая оперативно реагирует на любые нештатные ситуации
                </div>
            </div>
            
            <div class="accordion-item">
                <button class="accordion-button" aria-expanded="false" aria-controls="panel-3" id="accordion-3">
                    Умное освещение и климат-контроль
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="panel-3" role="region" aria-labelledby="accordion-3" class="accordion-content">
                    Интеллектуальные системы освещения и климата адаптируются под ваш распорядок дня и природные условия. Автоматическая регулировка температуры, влажности и освещённости создаёт идеальный микроклимат при оптимальном энергопотреблении
                </div>
            </div>
            
            <div class="accordion-item">
                <button class="accordion-button" aria-expanded="false" aria-controls="panel-4" id="accordion-4">
                    Проектирование и установка
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div id="panel-4" role="region" aria-labelledby="accordion-4" class="accordion-content">
                    Наши специалисты разрабатывают индивидуальные решения под особенности вашего пространства. Полный цикл работ — от проектирования до пусконаладки — гарантирует бесшовную интеграцию всех элементов умного дома в единую экосистему
                </div>
            </div>
        </div>
        <div class="photo-grid">
            <div class="photo-top-left">
                <img src="assets/images/first--img--card.png" alt="Умная колонка" />
            </div>
            <div class="photo-bottom-left">
                <img src="assets/images/second--img--card.png" alt="Система безопасности" />
            </div>
            <div class="photo-large">
                <img src="assets/images/third--img--card.png" alt="Умный дом" />
            </div>
        </div>
    </div>
</div>

<div class="photo--of--production">
    <h2>Инновации<br />Комфорт<br />Безопасность</h2>
    <img class="left--speakers--html--banner--photo" src="assets/images/КОЛОНКИ СЛЕВА.png" alt="Колонки слева" />
    <img class="camera--html--banner--photo" src="assets/images/ВИДЕОКАМЕРА.png" alt="Видеокамера" />
    <img class="right--speaker--html--banner--photo" src="assets/images/КОЛОНКА СПРАВА.png" alt="Колонка справа" />
</div>

<div class="make--another--choice">
    <div class="make--another--choice--inner">
        <div class="title--make--another--choice">
            <h3>Сделайте выбор<br />в сторону удобства</h3>
        </div>
        <div class="items-row">
            <?php 
            // Используем первые 4 товара из featuredProducts
            $displayProducts = array_slice($featuredProducts, 0, 4);
            $cardImages = [
                'assets/images/first--img--card.png',
                'assets/images/second--img--card.png', 
                'assets/images/third--img--card.png',
                'assets/images/fourth--img--card.png'
            ];
            
            foreach ($displayProducts as $index => $product): 
                $cardImage = isset($cardImages[$index]) ? $cardImages[$index] : 'assets/images/placeholder.jpg';
            ?>
            <div class="img-placeholder">
                <a href="product.php?id=<?php echo $product['id']; ?>">
                    <img src="<?php echo $cardImage; ?>" alt="<?php echo $product['name']; ?>" />
                    
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="under--cards">
            <div class="description">
                Портативные гаджеты сочетают мобильность и мощный функционал для управления умным домом. Автономная работа и простой запуск позволяют начать использование сразу после распаковки
            </div>
            <a href="catalog.php" class="btn-learn dark--btn">Подробнее</a>
        </div>
    </div>
</div>

<div class="UPGRADE--YOUR--LIFE">
    <div class="UPGRADE--YOUR--LIFE--inner">
        <div class="left--side--upgrade--your--life">
            <h1>Новый уровень комфорта</h1>
            <p>Голосовые ассистенты, автоматизированные системы и умные девайсы — всё для того, чтобы ваш дом стал современным, безопасным и комфортным</p>
            <a href="catalog.php" class="btn-learn light--btn">Подробнее</a>
        </div>
        <div class="right--side--upgrade--your--life">
            <img src="assets/images/image.png" alt="" />
        </div>
    </div>
</div>

<div class="description--of--gadgets">
    <hr />
    <div class="first--description--of--gadget--central">
        <div class="first--description--of--gadget gadget--item--disply--flex">
            <div class="left--side--description--of--gadget">
                <h1>Умные устройства для дома</h1>
            </div>
            <div class="right--side--description--of--gadget">
                <p>Умный дом — это система, обеспечивающая круглосуточную безопасность, комфорт и удобство в вашем доме</p>
            </div>
        </div>
    </div>
    <hr />
    <div class="first--description--of--gadget--central">
        <div class="first--description--of--gadget gadget--item--disply--flex">
            <div class="left--side--description--of--gadget">
                <h1>Портативные гаджеты</h1>
            </div>
            <div class="right--side--description--of--gadget">
                <p>Это компактные и мобильные умные устройства, которые можно легко переносить из комнаты в комнату или брать с собой</p>
            </div>
        </div>
    </div>
    <hr />
    <div class="first--description--of--gadget--central">
        <div class="first--description--of--gadget gadget--item--disply--flex">
            <div class="left--side--description--of--gadget">
                <h1>Домашние развлечения</h1>
            </div>
            <div class="right--side--description--of--gadget">
                <p>Эта категория включает в себя устройства, предназначенные для аудио- и видеоконтента, игр и создания мультимедийной среды в доме</p>
            </div>
        </div>
    </div>
    <hr />
    <div class="first--description--of--gadget--central">
        <div class="first--description--of--gadget gadget--item--disply--flex">
            <div class="left--side--description--of--gadget">
                <h1>Аксессуары для устройств</h1>
            </div>
            <div class="right--side--description--of--gadget">
                <p>Это дополнительные устройства и гаджеты, которые дополняют и расширяют возможности основной техники, делают использование более удобным или решают конкретные задачи</p>
            </div>
        </div>
    </div>
    <hr />
</div>

<script>
    // Скрипт аккордеона
    document.querySelectorAll(".accordion-button").forEach((button) => {
        button.addEventListener("click", () => {
            const expanded = button.getAttribute("aria-expanded") === "true";
            document.querySelectorAll(".accordion-button").forEach((btn) => {
                btn.setAttribute("aria-expanded", "false");
            });
            document.querySelectorAll(".accordion-content").forEach((c) => {
                c.classList.remove("expanded");
            });
            if (!expanded) {
                button.setAttribute("aria-expanded", "true");
                const content = document.getElementById(button.getAttribute("aria-controls"));
                content.classList.add("expanded");
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>