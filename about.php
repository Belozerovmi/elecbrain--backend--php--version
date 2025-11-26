<?php
$pageTitle = "О нас";
include 'includes/header.php';
?>

<main class="about-main">
    <section class="about-hero">
        <div class="about-hero--inner inner">
            <h1 class="about-title">О компании ElecBrain</h1>
            <p class="about-subtitle">Мы создаем будущее умных технологий для вашего дома</p>
        </div>
    </section>

    <section class="about-story">
        <div class="about-story--inner inner">
            <div class="story-content">
                <div class="story-text">
                    <h2>Наша история</h2>
                    <p>ElecBrain была основана в 2018 году с целью сделать умные технологии доступными для каждого. Начиная с небольшой команды энтузиастов, сегодня мы стали лидерами в области домашней автоматизации.</p>
                    <p>Наша миссия — создавать интуитивно понятные и надежные решения, которые делают жизнь комфортнее, безопаснее и эффективнее.</p>
                </div>
                <div class="story-image">
                    <div class="image-placeholder about-image-1">
                        <img class="about-image-1" src="assets/images/team-photo.webp" alt="Наша команда" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-values">
        <div class="about-values--inner inner">
            <div class="values-header">
                <h2>Наша философия</h2>
                <p class="values-intro">Мы создаем технологии, которые работают на вас — незаметно, надежно и эффективно</p>
            </div>

            <div class="values-grid">
                <div class="value-column">
                    <div class="value-item">
                        <div class="value-line"></div>
                        <div class="value-content">
                            <h3>Интуитивная интеграция</h3>
                            <p>Все устройства бесшовно работают вместе, создавая единую экосистему вашего умного дома</p>
                        </div>
                    </div>

                    <div class="value-item">
                        <div class="value-line"></div>
                        <div class="value-content">
                            <h3>Технологии без сложностей</h3>
                            <p>Сложные решения становятся простыми в использовании благодаря продуманному дизайну</p>
                        </div>
                    </div>
                </div>

                <div class="value-column">
                    <div class="value-item">
                        <div class="value-line"></div>
                        <div class="value-content">
                            <h3>Надежность как стандарт</h3>
                            <p>Каждое устройство проходит многоуровневое тестирование и соответствует высшим стандартам качества</p>
                        </div>
                    </div>

                    <div class="value-item">
                        <div class="value-line"></div>
                        <div class="value-content">
                            <h3>Постоянное развитие</h3>
                            <p>Мы постоянно совершенствуем наши продукты, следуя за развитием технологий и потребностями пользователей</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="values-stats">
                <div class="stat">
                    <div class="stat-number">99.8%</div>
                    <div class="stat-label">Надежность работы</div>
                </div>
                <div class="stat">
                    <div class="stat-number">50K+</div>
                    <div class="stat-label">Установленных систем</div>
                </div>
                <div class="stat">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Поддержка клиентов</div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-team">
        <div class="about-team--inner inner">
            <h2>Наша команда</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo about-image-2"></div>
                    <h3>Алексей Петров</h3>
                    <p class="member-role">CEO & Основатель</p>
                    <p class="member-bio">Более 10 лет в сфере IoT и умных технологий</p>
                </div>
                <div class="team-member">
                    <div class="member-photo about-image-3"></div>
                    <h3>Мария Иванова</h3>
                    <p class="member-role">Технический директор</p>
                    <p class="member-bio">Эксперт в области домашней автоматизации и AI</p>
                </div>
                <div class="team-member">
                    <div class="member-photo about-image-4"></div>
                    <h3>Дмитрий Сидоров</h3>
                    <p class="member-role">Главный разработчик</p>
                    <p class="member-bio">Специалист по интеграции умных устройств</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-achievements">
        <div class="about-achievements--inner inner">
            <h2>Наши достижения</h2>
            <div class="achievements-grid">
                <div class="achievement-item">
                    <div class="achievement-number">50,000+</div>
                    <div class="achievement-text">Довольных клиентов</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">100+</div>
                    <div class="achievement-text">Умных устройств</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">5</div>
                    <div class="achievement-text">Лет на рынке</div>
                </div>
                <div class="achievement-item">
                    <div class="achievement-number">24/7</div>
                    <div class="achievement-text">Техподдержка</div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>