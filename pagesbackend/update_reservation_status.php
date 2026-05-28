<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$type = isset($_POST['type']) ? $_POST['type'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

if (!$id || !in_array($type, ['hotel', 'package']) || !in_array($status, ['confirmed', 'cancelled', 'pending', 'completed'])) {
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

$pdo = getDB();

try {
    $table = $type === 'hotel' ? 'hotel_reservations' : 'package_reservations';
    $stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
