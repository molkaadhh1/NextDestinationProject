<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to book a hotel.']);
    exit;
}

$user = currentUser();
$user_id = $user['id'];

$hotel_id = isset($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : 0;
$checkin = isset($_POST['checkin']) ? $_POST['checkin'] : '';
$checkout = isset($_POST['checkout']) ? $_POST['checkout'] : '';
$rooms = isset($_POST['rooms']) ? (int)$_POST['rooms'] : 1;
$adults = isset($_POST['adults']) ? (int)$_POST['adults'] : 1;
$room_type = isset($_POST['room_type']) ? $_POST['room_type'] : 'Standard';
$meal_type = isset($_POST['meal_type']) ? $_POST['meal_type'] : 'Logement seul';
$total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0;

if (!$hotel_id || !$checkin || !$checkout || $total_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs et sélectionner des dates valides.']);
    exit;
}

$pdo = getDB();

try {
    $stmt = $pdo->prepare("INSERT INTO hotel_reservations (user_id, hotel_id, checkin_date, checkout_date, rooms, adults, room_type, meal_type, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $hotel_id, $checkin, $checkout, $rooms, $adults, $room_type, $meal_type, $total_price]);
    
    echo json_encode(['success' => true, 'message' => 'Réservation confirmée avec succès !']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la réservation : ' . $e->getMessage()]);
}
?>
