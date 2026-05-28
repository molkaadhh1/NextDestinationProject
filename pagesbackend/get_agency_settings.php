<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$pdo = getDB();
try {
    $stmt = $pdo->query("SELECT * FROM agency_settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($settings) {
        echo json_encode(['success' => true, 'data' => $settings]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No settings found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
