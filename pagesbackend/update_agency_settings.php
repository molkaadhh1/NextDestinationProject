<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');
if (!isAdmin()) { echo json_encode(['success' => false]); exit; }

$name = $_POST['agency_name'] ?? '';
$email = $_POST['contact_email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';

$pdo = getDB();
try {
    $stmt = $pdo->prepare("UPDATE agency_settings SET agency_name=?, contact_email=?, phone=?, address=? WHERE id=1");
    $stmt->execute([$name, $email, $phone, $address]);
    echo json_encode(['success' => true, 'message' => 'Paramètres mis à jour']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
