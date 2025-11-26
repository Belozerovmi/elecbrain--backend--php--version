<?php
function getProducts($category = null, $limit = null, $search = null) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.in_stock = TRUE";
    
    $params = [];
    
    if ($category && $category !== 'all') {
        $sql .= " AND c.slug = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductById($id) {
    global $pdo;
    
    try {
        // Проверяем существование колонки views
        $stmtCheck = $pdo->prepare("SHOW COLUMNS FROM products LIKE 'views'");
        $stmtCheck->execute();
        $hasViews = $stmtCheck->fetch();
        
        if ($hasViews) {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  WHERE p.id = ?");
        } else {
            $stmt = $pdo->prepare("SELECT p.*, 0 as views, c.name as category_name, c.slug as category_slug FROM products p 
                                  LEFT JOIN categories c ON p.category_id = c.id 
                                  WHERE p.id = ?");
        }
        
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error getting product by id: " . $e->getMessage());
        return false;
    }
}

function getCategories() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategoryBySlug($slug) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

// ФУНКЦИИ ДЛЯ РАБОТЫ С КОРЗИНОЙ В БАЗЕ ДАННЫХ
function addToCartDB($productId, $quantity = 1) {
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $checkStmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$userId, $productId]);
    $existingItem = $checkStmt->fetch();
    
    if ($existingItem) {
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $result = $stmt->execute([$newQuantity, $userId, $productId]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $result = $stmt->execute([$userId, $productId, $quantity]);
    }
    
    if ($result) {
        return ['success' => true, 'cart_count' => getCartCountDB()];
    } else {
        return ['success' => false, 'error' => 'Ошибка добавления в корзину'];
    }
}

function removeFromCartDB($productId) {
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $result = $stmt->execute([$userId, $productId]);
    
    if ($result) {
        return ['success' => true, 'cart_count' => getCartCountDB(), 'items_count' => getCartItemsCountDB()];
    } else {
        return ['success' => false, 'error' => 'Ошибка удаления из корзины'];
    }
}

function updateCartQuantityDB($productId, $quantity) {
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    if ($quantity <= 0) {
        return removeFromCartDB($productId);
    }
    
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $result = $stmt->execute([$quantity, $userId, $productId]);
    
    if ($result) {
        return ['success' => true, 'cart_count' => getCartCountDB()];
    } else {
        return ['success' => false, 'error' => 'Ошибка обновления количества'];
    }
}

function getCartCountDB() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    return $result['total'] ? (int)$result['total'] : 0;
}

function getCartItemsCountDB() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    return $result['count'] ? (int)$result['count'] : 0;
}

function getCartItemsDB() {
    if (!isLoggedIn()) {
        return [];
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, ci.quantity 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE ci.user_id = ? 
        ORDER BY ci.created_at DESC
    ");
    $stmt->execute([$userId]);
    
    $cartItems = [];
    while ($item = $stmt->fetch()) {
        $cartItems[] = [
            'id' => $item['id'],
            'name' => $item['name'],
            'description' => $item['description'],
            'price' => $item['price'],
            'image' => $item['image'],
            'category_id' => $item['category_id'],
            'category_name' => $item['category_name'],
            'quantity' => $item['quantity'],
            'total_price' => $item['price'] * $item['quantity']
        ];
    }
    
    return $cartItems;
}

function getCartTotalDB() {
    $cartItems = getCartItemsDB();
    $total = 0;
    
    foreach ($cartItems as $item) {
        $total += $item['total_price'];
    }
    
    return $total;
}

function clearCartDB() {
    if (!isLoggedIn()) {
        return false;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

function isProductInCartDB($productId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT id FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return $stmt->fetch() !== false;
}

function getProductCartQuantityDB($productId) {
    if (!isLoggedIn()) {
        return 0;
    }
    
    global $pdo;
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $result = $stmt->fetch();
    
    return $result ? (int)$result['quantity'] : 0;
}

// ФУНКЦИИ-АЛИАСЫ ДЛЯ СОВМЕСТИМОСТИ
function addToCart($productId, $quantity = 1) {
    return addToCartDB($productId, $quantity);
}

function removeFromCart($productId) {
    return removeFromCartDB($productId);
}

function updateCartQuantity($productId, $quantity) {
    return updateCartQuantityDB($productId, $quantity);
}

function getCartCount() {
    return getCartCountDB();
}

function getCartItems() {
    return getCartItemsDB();
}

function getCartTotal() {
    return getCartTotalDB();
}

function clearCart() {
    return clearCartDB();
}

function isProductInCart($productId) {
    return isProductInCartDB($productId);
}

function getProductCartQuantity($productId) {
    return getProductCartQuantityDB($productId);
}

// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
function getNumEnding($number, $endings) {
    $number = $number % 100;
    if ($number >= 11 && $number <= 19) {
        return $endings[2];
    }
    $i = $number % 10;
    switch ($i) {
        case 1: return $endings[0];
        case 2:
        case 3:
        case 4: return $endings[1];
        default: return $endings[2];
    }
}

function getSuggestedProducts($cartItems, $limit = 4) {
    global $pdo;
    
    if (!empty($cartItems)) {
        $categoryIds = [];
        foreach ($cartItems as $item) {
            if (isset($item['category_id'])) {
                $categoryIds[] = (int)$item['category_id'];
            }
        }
        
        if (!empty($categoryIds)) {
            $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id IN ($placeholders) 
                    AND p.in_stock = TRUE 
                    ORDER BY RAND() 
                    LIMIT " . (int)$limit;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($categoryIds);
            $products = $stmt->fetchAll();
            
            if (!empty($products)) {
                return $products;
            }
        }
    }
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.in_stock = TRUE 
                          ORDER BY RAND() 
                          LIMIT " . (int)$limit);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getPopularProducts($limit = 8) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.in_stock = TRUE 
                          ORDER BY p.views DESC, p.created_at DESC 
                          LIMIT " . (int)$limit);
    $stmt->execute();
    return $stmt->fetchAll();
}

function incrementProductViews($productId) {
    global $pdo;
    
    try {
        // Сначала проверим, существует ли колонка views
        $stmt = $pdo->prepare("SHOW COLUMNS FROM products LIKE 'views'");
        $stmt->execute();
        $columnExists = $stmt->fetch();
        
        if ($columnExists) {
            // Колонка существует - обновляем
            $stmt = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
            $stmt->execute([$productId]);
        }
        // Если колонки нет - просто игнорируем, чтобы не было ошибки
    } catch (PDOException $e) {
        // Игнорируем ошибку, чтобы не ломать страницу
        error_log("Error incrementing product views: " . $e->getMessage());
    }
}

function searchProducts($query, $category = null, $limit = null) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ?) 
            AND p.in_stock = TRUE";
    
    $params = ["%$query%", "%$query%"];
    
    if ($category && $category !== 'all') {
        $sql .= " AND c.slug = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// ФУНКЦИИ ДЛЯ РАБОТЫ С ВИШЛИСТОМ
function addToWishlist($productId) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    $userId = $_SESSION['user_id'];
    
    $checkStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$userId, $productId]);
    
    if ($checkStmt->fetch()) {
        return ['success' => true, 'added' => false, 'message' => 'Товар уже в избранном'];
    }
    
    $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
    
    try {
        $result = $stmt->execute([$userId, $productId]);
        if ($result) {
            return ['success' => true, 'added' => true, 'message' => 'Товар добавлен в избранное'];
        } else {
            return ['success' => false, 'error' => 'Ошибка базы данных'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Ошибка добавления в избранное'];
    }
}

function removeFromWishlist($productId) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
    
    try {
        $result = $stmt->execute([$userId, $productId]);
        if ($result) {
            return ['success' => true, 'removed' => true, 'message' => 'Товар удален из избранного'];
        } else {
            return ['success' => false, 'error' => 'Ошибка базы данных'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Ошибка удаления из избранного'];
    }
}

function isProductInWishlist($productId) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    
    return (bool)$stmt->fetch();
}

function getWishlistProducts() {
    global $pdo;
    
    if (!isLoggedIn()) {
        return [];
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM wishlists w 
        JOIN products p ON w.product_id = p.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE w.user_id = ? 
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$userId]);
    
    return $stmt->fetchAll();
}

function toggleWishlist($productId) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return ['success' => false, 'error' => 'Требуется авторизация'];
    }
    
    $userId = $_SESSION['user_id'];
    
    // Проверяем, есть ли уже товар в вишлисте
    $checkStmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?");
    $checkStmt->execute([$userId, $productId]);
    $existingItem = $checkStmt->fetch();
    
    if ($existingItem) {
        // Удаляем из вишлиста
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        
        try {
            $result = $stmt->execute([$userId, $productId]);
            if ($result) {
                return ['success' => true, 'added' => false, 'message' => 'Товар удален из избранного'];
            } else {
                return ['success' => false, 'error' => 'Ошибка базы данных при удалении'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Ошибка удаления из избранного'];
        }
    } else {
        // Добавляем в вишлист
        $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
        
        try {
            $result = $stmt->execute([$userId, $productId]);
            if ($result) {
                return ['success' => true, 'added' => true, 'message' => 'Товар добавлен в избранное'];
            } else {
                return ['success' => false, 'error' => 'Ошибка базы данных при добавлении'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'error' => 'Ошибка добавления в избранное'];
        }
    }
}

// ФУНКЦИИ ДЛЯ РАБОТЫ С ПОЛЬЗОВАТЕЛЕМ
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getUser() {
    return getCurrentUser();
}

function updateProfile($userId, $userData) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['phone'],
            $userData['address'],
            $userData['city'],
            $userData['postal_code'],
            $userId
        ]);
        
        if ($result) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => 'Ошибка обновления профиля'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()];
    }
}

function formatPrice($price) {
    return number_format($price, 0, ',', ' ');
}

// ФУНКЦИИ ДЛЯ РАБОТЫ С ЗАКАЗАМИ
function createOrder($userId, $orderData, $cartItems) {
    global $pdo;
    
    try {
        $orderNumber = generateOrderNumber();
        $trackingNumber = generateTrackingNumber();
        
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, first_name, last_name, email, phone, 
                              address, payment_method, notes, total_amount, status, tracking_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)
        ");
        
        $stmt->execute([
            $userId,
            $orderNumber,
            $orderData['first_name'],
            $orderData['last_name'],
            $orderData['email'],
            $orderData['phone'],
            $orderData['address'],
            $orderData['payment_method'],
            $orderData['notes'],
            $orderData['total_amount'],
            $trackingNumber
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        foreach ($cartItems as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, price, quantity) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['name'],
                $item['price'],
                $item['quantity']
            ]);
        }
        
        $pdo->commit();
        
        return [
            'success' => true,
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'tracking_number' => $trackingNumber
        ];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'error' => 'Ошибка создания заказа: ' . $e->getMessage()
        ];
    }
}

function generateOrderNumber() {
    return 'ORD' . date('YmdHis') . mt_rand(100, 999);
}

function generateTrackingNumber() {
    return 'RB' . mt_rand(100000000, 999999999) . 'RU';
}

function getUserOrders($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getOrderItems($orderId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM order_items 
        WHERE order_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}

function getUserAddresses($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM user_addresses 
        WHERE user_id = ? 
        ORDER BY is_default DESC, created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// ФУНКЦИИ ДЛЯ ОТСЛЕЖИВАНИЯ ЗАКАЗОВ
function getOrderTrackingInfo($order) {
    $status = $order['status'];
    $createdAt = strtotime($order['created_at']);
    
    switch ($status) {
        case 'pending':
            return [
                ['status' => 'pending', 'title' => 'Заказ принят', 'completed' => true, 'active' => true, 'date' => date('d.m.Y, H:i', $createdAt)],
                ['status' => 'processing', 'title' => 'Собран', 'completed' => false, 'active' => false, 'date' => 'Ожидается'],
                ['status' => 'shipped', 'title' => 'Передан в доставку', 'completed' => false, 'active' => false, 'date' => 'Ожидается'],
                ['status' => 'delivered', 'title' => 'Доставлен', 'completed' => false, 'active' => false, 'date' => 'Ожидается']
            ];
            
        case 'processing':
            return [
                ['status' => 'pending', 'title' => 'Заказ принят', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt)],
                ['status' => 'processing', 'title' => 'Собран', 'completed' => true, 'active' => true, 'date' => date('d.m.Y, H:i', $createdAt + 3600)],
                ['status' => 'shipped', 'title' => 'Передан в доставку', 'completed' => false, 'active' => false, 'date' => 'Ожидается'],
                ['status' => 'delivered', 'title' => 'Доставлен', 'completed' => false, 'active' => false, 'date' => 'Ожидается']
            ];
            
        case 'shipped':
            $shippedDate = $order['updated_at'] ? strtotime($order['updated_at']) : $createdAt + 7200;
            return [
                ['status' => 'pending', 'title' => 'Заказ принят', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt)],
                ['status' => 'processing', 'title' => 'Собран', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt + 3600)],
                ['status' => 'shipped', 'title' => 'Передан в доставку', 'completed' => true, 'active' => true, 'date' => date('d.m.Y, H:i', $shippedDate)],
                ['status' => 'delivered', 'title' => 'Доставлен', 'completed' => false, 'active' => false, 'date' => 'Ожидается']
            ];
            
        case 'delivered':
            $deliveredDate = $order['updated_at'] ? strtotime($order['updated_at']) : $createdAt + 86400;
            return [
                ['status' => 'pending', 'title' => 'Заказ принят', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt)],
                ['status' => 'processing', 'title' => 'Собран', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt + 3600)],
                ['status' => 'shipped', 'title' => 'Передан в доставку', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt + 7200)],
                ['status' => 'delivered', 'title' => 'Доставлен', 'completed' => true, 'active' => true, 'date' => date('d.m.Y, H:i', $deliveredDate)]
            ];
            
        case 'cancelled':
            return [
                ['status' => 'pending', 'title' => 'Заказ принят', 'completed' => true, 'active' => false, 'date' => date('d.m.Y, H:i', $createdAt)],
                ['status' => 'cancelled', 'title' => 'Отменен', 'completed' => true, 'active' => true, 'date' => $order['updated_at'] ? date('d.m.Y, H:i', strtotime($order['updated_at'])) : date('d.m.Y, H:i', $createdAt + 3600)]
            ];
            
        default:
            return [];
    }
}

function getStatusText($status) {
    $statusText = [
        'pending' => 'Принят',
        'processing' => 'Обрабатывается',
        'shipped' => 'В пути',
        'delivered' => 'Доставлен',
        'cancelled' => 'Отменен'
    ];
    return $statusText[$status] ?? $status;
}

function getStatusClass($status) {
    $statusClass = [
        'pending' => 'pending',
        'processing' => 'processing',
        'shipped' => 'in-progress',
        'delivered' => 'delivered',
        'cancelled' => 'cancelled'
    ];
    return $statusClass[$status] ?? 'pending';
}

function updateOrderTracking($orderId, $trackingNumber = null) {
    global $pdo;
    
    if (!$trackingNumber) {
        $trackingNumber = generateTrackingNumber();
    }
    
    $stmt = $pdo->prepare("UPDATE orders SET tracking_number = ?, status = 'shipped', updated_at = NOW() WHERE id = ?");
    return $stmt->execute([$trackingNumber, $orderId]);
}

// ФУНКЦИИ ДЛЯ СОВМЕСТИМОСТИ С СЕССИЕЙ
function saveCartToDB($userId) {
    global $pdo;
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    foreach ($_SESSION['cart'] as $productId => $cartItem) {
        $quantity = 1;
        
        if (is_array($cartItem) && isset($cartItem['quantity'])) {
            $quantity = $cartItem['quantity'];
        } elseif (is_numeric($cartItem)) {
            $quantity = $cartItem;
        }
        
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $productId, $quantity]);
    }
}

function restoreCartFromDB($userId) {
    global $pdo;
    
    $_SESSION['cart'] = [];
    
    $stmt = $pdo->prepare("SELECT product_id, quantity FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll();
    
    foreach ($cartItems as $item) {
        $_SESSION['cart'][$item['product_id']] = ['quantity' => $item['quantity']];
    }
}

function migrateSessionCartToDB($userId) {
    global $pdo;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $productId => $cartItem) {
            $quantity = 1;
            
            if (is_array($cartItem) && isset($cartItem['quantity'])) {
                $quantity = $cartItem['quantity'];
            } elseif (is_numeric($cartItem)) {
                $quantity = $cartItem;
            }
            
            $checkStmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
            $checkStmt->execute([$userId, $productId]);
            $existingItem = $checkStmt->fetch();
            
            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$newQuantity, $userId, $productId]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $productId, $quantity]);
            }
        }
    }
    
    restoreCartFromDB($userId);
}

function restoreWishlistFromDB($userId) {
    global $pdo;
    
    $_SESSION['wishlist'] = [];
    
    $stmt = $pdo->prepare("SELECT product_id FROM wishlists WHERE user_id = ?");
    $stmt->execute([$userId]);
    $wishlistItems = $stmt->fetchAll();
    
    foreach ($wishlistItems as $item) {
        $_SESSION['wishlist'][$item['product_id']] = true;
    }
}

// ФУНКЦИИ ДЛЯ РАБОТЫ С АВАТАРАМИ
function uploadAvatar($file, $userId) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Разрешены только JPG, PNG, GIF и WebP форматы'];
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Размер файла не должен превышать 5MB'];
    }
    
    $uploadDir = 'uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    $oldAvatar = getCurrentUserAvatar($userId);
    if ($oldAvatar && file_exists($oldAvatar)) {
        unlink($oldAvatar);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        
        if ($stmt->execute([$fileName, $userId])) {
            return ['success' => true, 'file_path' => $filePath, 'file_name' => $fileName];
        } else {
            unlink($filePath);
            return ['success' => false, 'error' => 'Ошибка обновления базы данных'];
        }
    } else {
        return ['success' => false, 'error' => 'Ошибка сохранения файла'];
    }
}

function getCurrentUserAvatar($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user && $user['avatar']) {
        return 'uploads/avatars/' . $user['avatar'];
    }
    
    return null;
}

function deleteAvatar($userId) {
    $avatarPath = getCurrentUserAvatar($userId);
    
    if ($avatarPath && file_exists($avatarPath)) {
        unlink($avatarPath);
    }
    
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET avatar = NULL WHERE id = ?");
    return $stmt->execute([$userId]);
}

function updateProfileWithAvatar($userId, $userData, $avatarFile = null) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, updated_at = NOW() WHERE id = ?");
        $result = $stmt->execute([
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['phone'],
            $userData['address'],
            $userData['city'],
            $userData['postal_code'],
            $userId
        ]);
        
        if (!$result) {
            throw new Exception('Ошибка обновления профиля');
        }
        
        if ($avatarFile && $avatarFile['error'] === UPLOAD_ERR_OK) {
            $avatarResult = uploadAvatar($avatarFile, $userId);
            if (!$avatarResult['success']) {
                throw new Exception($avatarResult['error']);
            }
        }
        
        if (isset($userData['remove_avatar']) && $userData['remove_avatar']) {
            deleteAvatar($userId);
        }
        
        $pdo->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}


// Функции для работы с отзывами
function addReview($productId, $userId, $rating, $comment) {
    global $pdo;
    
    try {
        // Проверяем, не оставлял ли пользователь уже отзыв
        $checkStmt = $pdo->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
        $checkStmt->execute([$productId, $userId]);
        
        if ($checkStmt->fetch()) {
            return ['success' => false, 'error' => 'Вы уже оставляли отзыв на этот товар'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO reviews (product_id, user_id, rating, comment, status) 
            VALUES (?, ?, ?, ?, 'approved')
        ");
        
        $result = $stmt->execute([$productId, $userId, $rating, $comment]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Отзыв успешно добавлен'];
        } else {
            return ['success' => false, 'error' => 'Ошибка добавления отзыва'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Ошибка базы данных: ' . $e->getMessage()];
    }
}

function getProductReviews($productId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, u.first_name, u.last_name 
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? AND r.status = 'approved' 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}



// Функции для работы с галереей изображений


function getCategoryById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
    
}



// Функции для работы со страницей товара
function getProductGallery($productId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT image_path, is_main 
            FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_main DESC, sort_order ASC
        ");
        $stmt->execute([$productId]);
        $images = $stmt->fetchAll();
        
        // Если нет изображений в галерее, используем основное изображение товара
        if (empty($images)) {
            $product = getProductById($productId);
            if ($product && !empty($product['image'])) {
                $images = [['image_path' => $product['image'], 'is_main' => 1]];
            } else {
                // Если вообще нет изображений - используем placeholder
                $images = [['image_path' => 'placeholder.jpg', 'is_main' => 1]];
            }
        }
        
        return $images;
    } catch (PDOException $e) {
        // Если произошла ошибка - используем основное изображение товара
        $product = getProductById($productId);
        if ($product && !empty($product['image'])) {
            return [['image_path' => $product['image'], 'is_main' => 1]];
        }
        // Если вообще нет изображений - используем placeholder
        return [['image_path' => 'placeholder.jpg', 'is_main' => 1]];
    }
}

function getProductRatingStats($productId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
            FROM reviews 
            WHERE product_id = ? AND status = 'approved'
        ");
        $stmt->execute([$productId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

function hasUserReviewedProduct($productId, $userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM reviews 
            WHERE product_id = ? AND user_id = ?
        ");
        $stmt->execute([$productId, $userId]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        return false;
    }
}


function getCorrectImagePath($imagePath) {
    if (empty($imagePath)) {
        return 'images/placeholder.jpg';
    }
    
    // Исправляем двойные дефисы в путях
    $correctedPath = str_replace('--', '-', $imagePath);
    
    // Проверяем существование файлов
    $possiblePaths = [
        'images/products/' . $correctedPath,
        'images/products/' . $imagePath,
        'uploads/products/' . $correctedPath, 
        'uploads/products/' . $imagePath,
        $correctedPath,
        $imagePath
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // Если ничего не найдено - возвращаем placeholder
    return 'images/placeholder.jpg';
}


// Проверка, купил ли пользователь товар
function hasUserPurchasedProduct($userId, $productId) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT oi.id 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.user_id = ? AND oi.product_id = ? AND o.status != 'cancelled'
        LIMIT 1
    ");
    $stmt->execute([$userId, $productId]);
    
    return $stmt->fetch() !== false;
}

// Функция для проверки высоты контента (будет использоваться в JavaScript)
function isContentOverflowing($text, $maxHeight = 150) {
    // Эта функция будет дополнена JavaScript для точного определения
    // Пока используем приблизительную проверку по количеству символов
    return strlen($text) > 500; // Эмпирическое значение
}

// Получение отзывов с информацией о покупке
function getProductReviewsWithPurchaseInfo($productId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, u.first_name, u.last_name,
                   EXISTS(
                       SELECT 1 FROM order_items oi 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.user_id = r.user_id 
                       AND oi.product_id = r.product_id 
                       AND o.status != 'cancelled'
                   ) as has_purchased
            FROM reviews r 
            LEFT JOIN users u ON r.user_id = u.id 
            WHERE r.product_id = ? AND r.status = 'approved' 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}   

// Функция для исправления имен изображений в галерее
function fixGalleryImageNames($galleryImages, $productImage) {
    $fixedImages = [];
    
    foreach ($galleryImages as $image) {
        $originalName = $image['image_path'];
        $fixedName = $originalName;
        
        // Пробуем разные варианты имен
        $possibleNames = [
            $originalName, // оригинальное имя
            preg_replace('/-\d+(\.(jpg|png|jpeg))$/i', '$1', $originalName), // убираем -1, -2
            str_replace('--', '-', $originalName), // заменяем двойные дефисы на одинарные
            preg_replace('/-\d+(\.(jpg|png|jpeg))$/i', '$1', str_replace('--', '-', $originalName)), // оба исправления
            $productImage // основное изображение товара
        ];
        
        // Убираем дубликаты
        $possibleNames = array_unique($possibleNames);
        
        // Ищем существующий файл
        $foundName = $originalName;
        foreach ($possibleNames as $testName) {
            $testPath = 'assets/images/catalog--photos/' . $testName;
            if (file_exists($testPath)) {
                $foundName = $testName;
                break;
            }
        }
        
        $fixedImages[] = [
            'image_path' => $foundName,
            'is_main' => $image['is_main']
        ];
    }
    
    return $fixedImages;
}
?>