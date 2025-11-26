<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => true, 'count' => 0]);
    exit;
}

$count = getCartCountDB();
echo json_encode(['success' => true, 'count' => $count]);
?>