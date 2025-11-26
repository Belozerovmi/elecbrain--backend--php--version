<?php
function login($email, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        // ВОССТАНАВЛИВАЕМ КОРЗИНУ ИЗ БАЗЫ ДАННЫХ В СЕССИЮ
        restoreCartFromDB($user['id']);
        
        // ВОССТАНАВЛИВАЕМ ВИШЛИСТ ИЗ БАЗЫ ДАННЫХ В СЕССИЮ
        restoreWishlistFromDB($user['id']);
        
        return true;
    }
    return false;
}

function register($userData) {
    global $pdo;
    
    // Проверка существования email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$userData['email']]);
    
    if ($stmt->fetch()) {
        return ["success" => false, "error" => "Пользователь с таким email уже существует"];
    }
    
    // Хеширование пароля
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, phone, created_at) 
                          VALUES (?, ?, ?, ?, ?, NOW())");
    
    try {
        $stmt->execute([
            $userData['email'],
            $hashedPassword,
            $userData['first_name'],
            $userData['last_name'],
            $userData['phone']
        ]);
        
        $userId = $pdo->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_name'] = $userData['first_name'] . ' ' . $userData['last_name'];
        
        // ПЕРЕНОСИМ КОРЗИНУ ИЗ СЕССИИ В БАЗУ ДАННЫХ
        migrateSessionCartToDB($userId);
        
        return ["success" => true, "user_id" => $userId];
    } catch (PDOException $e) {
        return ["success" => false, "error" => "Ошибка регистрации: " . $e->getMessage()];
    }
}

function logout() {
    // СОХРАНЯЕМ КОРЗИНУ В БАЗУ ДАННЫХ ПЕРЕД ВЫХОДОМ
    if (isset($_SESSION['user_id']) && isset($_SESSION['cart'])) {
        saveCartToDB($_SESSION['user_id']);
    }
    
    // Очищаем все данные сессии
    $_SESSION = array();
    
    // Уничтожаем сессию
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    // Редирект на главную
    redirect('index.php');
}
?>