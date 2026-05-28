<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = isset($data['id']) ? (int)$data['id'] : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID invalide.']);
    exit;
}

$pdo = getDB();
try {
    $pdo->beginTransaction();
    $stmt1 = $pdo->prepare("DELETE FROM hotel_reservations WHERE user_id = ?");
    $stmt1->execute([$id]);
    $stmt2 = $pdo->prepare("DELETE FROM package_reservations WHERE user_id = ?");
    $stmt2->execute([$id]);
    $stmt3 = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt3->execute([$id]);
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
