<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$hotel_id = $_GET['hotel_id'] ?? null;

if (!$hotel_id) {
    echo json_encode(['success' => false, 'error' => 'ID hôtel manquant.']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM hotel_rooms WHERE hotel_id = ? ORDER BY room_type ASC");
    $stmt->execute([$hotel_id]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rooms]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur DB: ' . $e->getMessage()]);
}
