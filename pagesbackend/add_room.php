<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$hotel_id = $_POST['hotel_id'] ?? null;
$room_type = $_POST['room_type'] ?? '';
$total_rooms = $_POST['total_rooms'] ?? null;
$capacity_adults = $_POST['capacity_adults'] ?? 2;
$capacity_children = $_POST['capacity_children'] ?? 0;

if (!$hotel_id || empty($room_type) || $total_rooms === null) {
    echo json_encode(['success' => false, 'error' => 'Veuillez remplir tous les champs obligatoires.']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO hotel_rooms (hotel_id, room_type, total_rooms, capacity_adults, capacity_children) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$hotel_id, $room_type, $total_rooms, $capacity_adults, $capacity_children]);
    echo json_encode(['success' => true, 'message' => 'Chambre ajoutée avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur DB: ' . $e->getMessage()]);
}
