<?php
if (!ob_get_level()) {
    ob_start();
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'elecbrain_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_PORT', '3307');
define('BASE_URL', 'http://localhost/elecbrain');
define('UPLOADS_DIR', 'uploads/');
define('IMAGES_DIR', 'assets/images/');
define('CATALOG_PHOTOS_DIR', 'assets/images/catalog--photos/');
define('LOGOS_DIR', 'assets/images/logos/');
define('PLACEHOLDER_IMAGE', 'assets/images/placeholder.jpg');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo "<script>window.location.href = '" . $url . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=" . $url . "'></noscript>";
        exit;
    }
}

function uploadImage($file, $target_dir = UPLOADS_DIR) {
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . uniqid() . '_' . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["success" => false, "error" => "Файл не является изображением"];
    }
    
    if ($file["size"] > 5000000) {
        return ["success" => false, "error" => "Изображение слишком большое"];
    }
    
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        return ["success" => false, "error" => "Разрешены только JPG, JPEG, PNG и GIF файлы"];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "file_path" => $target_file];
    } else {
        return ["success" => false, "error" => "Ошибка загрузки файла"];
    }
}

function getProductImage($image_name) {
    if (empty($image_name) || $image_name === 'placeholder.jpg') {
        return 'assets/images/placeholder.jpg';
    }
    
    // Исправляем двойные дефисы и цифры в названиях файлов
    $correctedName = str_replace('--', '-', $image_name);
    $correctedName = preg_replace('/-\d+(\.(jpg|png|jpeg))$/i', '$1', $correctedName);
    
    // Проверяем разные возможные пути
    $paths = [
        'assets/images/catalog--photos/' . $correctedName,
        'assets/images/catalog--photos/' . $image_name,
        'assets/images/catalog-photos/' . $correctedName,
        'assets/images/catalog-photos/' . $image_name,
        'assets/images/' . $correctedName,
        'assets/images/' . $image_name,
        $correctedName,
        $image_name
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    return 'assets/images/placeholder.jpg';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
?>