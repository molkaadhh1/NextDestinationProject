<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $_POST['id'] ?? ($data['id'] ?? null);

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID manquant.']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM hotel_rooms WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Chambre supprimée.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur DB: ' . $e->getMessage()]);
}
