<?php
include 'includes/config.php';

echo "<h2>Исправление имен изображений в базе данных</h2>";

// Карта соответствия старых имен новым
$name_mapping = [
    'smart-speaker-pro.jpg' => 'smart--speaker--pro.jpg',
    'security-camera-hd.jpg' => 'smart--camera--4k.png',
    'smart-lamp-rgb.jpg' => 'smart--LED--RGB.png',
    'smart-thermostat.jpg' => 'smart--termostat.png',
    'portable-speaker.jpg' => 'portable--termostat.png',
    'media-center.jpg' => 'three--speakers.png', // временно, пока нет реального файла
    'smart-plug.jpg' => 'open--doors--detector.png', // временно
    'motion-sensor.jpg' => 'smart--lamp.png' // временно
];

$updated = 0;
foreach ($name_mapping as $old_name => $new_name) {
    // Проверяем существование нового файла
    $file_path = 'assets/images/catalog--photos/' . $new_name;
    $file_exists = file_exists($file_path);
    
    echo "<p>Замена: <strong>$old_name</strong> → <strong>$new_name</strong> - ";
    echo $file_exists ? "✅ Файл существует" : "⚠️ Файл отсутствует";
    echo "</p>";
    
    if ($file_exists) {
        // Обновляем запись в базе данных
        $stmt = $pdo->prepare("UPDATE products SET image = ? WHERE image = ?");
        $stmt->execute([$new_name, $old_name]);
        
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ Обновлено в базе: $old_name → $new_name</p>";
            $updated++;
        } else {
            echo "<p style='color: orange;'>⚠️ Не найдено в базе: $old_name</p>";
        }
    }
}

echo "<h3 style='color: green;'>✅ Готово! Обновлено записей: $updated</h3>";
echo "<p><a href='index.php'>Перейти на главную</a></p>";
?>