<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

$hotel_id = (int)($_POST['hotel_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$icon = trim($_POST['icon'] ?? 'fas fa-check');

if (!$hotel_id || empty($name)) {
    die(json_encode(['success' => false, 'message' => 'Hotel ID et nom du service requis']));
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO hotel_services (hotel_id, name, icon) VALUES (?, ?, ?)");
    $stmt->execute([$hotel_id, $name, $icon]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
