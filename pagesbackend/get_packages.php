<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $packages]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
