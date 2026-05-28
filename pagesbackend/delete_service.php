<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    die(json_encode(['success' => false, 'message' => 'ID requis']));
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM hotel_services WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
