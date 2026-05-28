<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Méthode non autorisée']));
}

$id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['pending', 'approved', 'rejected'])) {
    die(json_encode(['success' => false, 'message' => 'Paramètres invalides']));
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE reviews SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
