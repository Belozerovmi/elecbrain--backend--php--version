<?php
include 'includes/config.php';

echo "<h2>Добавление тестовых данных</h2>";

try {
    // Добавляем категории
    $categories = [
        ['name' => 'Умные колонки', 'slug' => 'smart-speakers', 'description' => 'Голосовые ассистенты и умные колонки'],
        ['name' => 'Системы безопасности', 'slug' => 'security-systems', 'description' => 'Камеры наблюдения и системы безопасности'],
        ['name' => 'Умное освещение', 'slug' => 'smart-lighting', 'description' => 'Умные лампы и системы освещения'],
        ['name' => 'Климат-контроль', 'slug' => 'climate-control', 'description' => 'Умные термостаты и климатические системы'],
        ['name' => 'Портативные гаджеты', 'slug' => 'portable-gadgets', 'description' => 'Мобильные умные устройства'],
        ['name' => 'Домашние развлечения', 'slug' => 'home-entertainment', 'description' => 'Аудио и видео системы'],
        ['name' => 'Аксессуары', 'slug' => 'accessories', 'description' => 'Дополнительные устройства и аксессуары']
    ];

    foreach ($categories as $category) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$category['name'], $category['slug'], $category['description']]);
    }
    echo "✅ Категории добавлены<br>";

    // Добавляем тестовые товары
    $products = [
        [
            'name' => 'Умная колонка ElecBrain Pro',
            'description' => 'Мощная умная колонка с голосовым помощником и премиальным звуком. Управление умным домом через голосовые команды.',
            'price' => 14999.00,
            'image' => 'smart-speaker-pro.jpg',
            'category_slug' => 'smart-speakers',
            'specifications' => '{"color": "black", "connectivity": "Wi-Fi, Bluetooth", "voice_assistant": "Алиса, Google Assistant"}'
        ],
        [
            'name' => 'Камера безопасности HD Vision',
            'description' => 'Камера с ночным видением 1080p, детекцией движения и двусторонней аудиосвязью. Запись в облако.',
            'price' => 7990.00,
            'image' => 'security-camera-hd.jpg',
            'category_slug' => 'security-systems',
            'specifications' => '{"resolution": "1080p", "night_vision": "10m", "motion_detection": "да"}'
        ],
        [
            'name' => 'Умная лампа RGB Smart Light',
            'description' => 'LED лампа с изменением цвета 16 млн оттенков. Управление через приложение и голосовые команды.',
            'price' => 2490.00,
            'image' => 'smart-lamp-rgb.jpg',
            'category_slug' => 'smart-lighting',
            'specifications' => '{"power": "9W", "color_temp": "2700K-6500K", "compatibility": "Wi-Fi"}'
        ],
        [
            'name' => 'Умный термостат Climate Pro',
            'description' => 'Автоматическое регулирование температуры, управление через смартфон, экономия энергии до 30%.',
            'price' => 12990.00,
            'image' => 'smart-thermostat.jpg',
            'category_slug' => 'climate-control',
            'specifications' => '{"display": "LCD", "connectivity": "Wi-Fi", "compatibility": "iOS, Android"}'
        ],
        [
            'name' => 'Портативная колонка Mini Sound',
            'description' => 'Компактная Bluetooth колонка с защитой от воды IPX7. 12 часов автономной работы.',
            'price' => 4590.00,
            'image' => 'portable-speaker.jpg',
            'category_slug' => 'portable-gadgets',
            'specifications' => '{"battery": "12h", "waterproof": "IPX7", "weight": "450g"}'
        ],
        [
            'name' => 'Медиацентр Home Theater',
            'description' => '4K медиаплеер с поддержкой HDR и Dolby Atmos. Голосовое управление и стриминг.',
            'price' => 18990.00,
            'image' => 'media-center.jpg',
            'category_slug' => 'home-entertainment',
            'specifications' => '{"resolution": "4K", "hdr": "HDR10", "audio": "Dolby Atmos"}'
        ],
        [
            'name' => 'Умная розетка Smart Plug',
            'description' => 'Дистанционное управление электроприборами через приложение. Мониторинг энергопотребления.',
            'price' => 1590.00,
            'image' => 'smart-plug.jpg',
            'category_slug' => 'accessories',
            'specifications' => '{"power": "16A", "wifi": "2.4GHz", "remote_control": "да"}'
        ],
        [
            'name' => 'Датчик движения Motion Sensor',
            'description' => 'Беспроводной датчик движения для системы умного дома. Автоматизация освещения и безопасности.',
            'price' => 2190.00,
            'image' => 'motion-sensor.jpg',
            'category_slug' => 'security-systems',
            'specifications' => '{"battery": "2 года", "range": "8m", "angle": "120°"}'
        ]
    ];

    $added_count = 0;
    foreach ($products as $product) {
        // Получаем ID категории по slug
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$product['category_slug']]);
        $category = $stmt->fetch();
        
        if ($category) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO products (name, description, price, image, category_id, specifications, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $product['name'],
                $product['description'],
                $product['price'],
                $product['image'],
                $category['id'],
                $product['specifications']
            ]);
            $added_count++;
        }
    }
    
    echo "✅ Добавлено товаров: $added_count<br>";
    echo "<h3 style='color: green;'>Тестовые данные успешно добавлены!</h3>";
    echo "<p><a href='index.php'>Перейти на главную</a></p>";

} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
?>