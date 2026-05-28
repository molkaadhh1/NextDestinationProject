<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to book a package.']);
    exit;
}

$user = currentUser();
$user_id = $user['id'];

$package_id = isset($_POST['package_id']) ? (int)$_POST['package_id'] : 0;
$depart = isset($_POST['depart']) ? $_POST['depart'] : '';
$adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 1;
$children = isset($_POST['children']) ? (int)$_POST['children'] : 0;
$room_type = isset($_POST['room_type']) ? $_POST['room_type'] : 'Standard';
$options = isset($_POST['options']) ? $_POST['options'] : '[]';
$total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0;

if (!$package_id || !$depart || $total_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Veuillez choisir une date de départ valide.']);
    exit;
}

$pdo = getDB();

try {
    $stmt = $pdo->prepare("INSERT INTO package_reservations (user_id, package_id, departure_date, adults, children, room_type, options, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $package_id, $depart, $adults, $children, $room_type, $options, $total_price]);
    
    echo json_encode(['success' => true, 'message' => 'Réservation du package confirmée avec succès !']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la réservation : ' . $e->getMessage()]);
}
?>
