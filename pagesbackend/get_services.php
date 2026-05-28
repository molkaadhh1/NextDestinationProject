<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

$hotel_id = (int)($_GET['hotel_id'] ?? 0);

if (!$hotel_id) {
    die(json_encode(['success' => false, 'message' => 'Hotel ID requis']));
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM hotel_services WHERE hotel_id = ?");
    $stmt->execute([$hotel_id]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
