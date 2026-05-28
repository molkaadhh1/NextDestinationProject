<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query("
        SELECT r.*, 
               COALESCE(h.name, p.name) as target_name,
               IF(h.id IS NOT NULL, 'Hôtel', 'Package') as target_type
        FROM reviews r 
        LEFT JOIN hotels h ON r.hotel_id = h.id 
        LEFT JOIN packages p ON r.package_id = p.id
        ORDER BY r.created_at DESC
    ");
    $reviews = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $reviews]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
