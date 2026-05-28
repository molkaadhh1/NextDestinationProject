<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID manquant.']);
    exit;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT media_path FROM home_sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($slider) {
        $filePath = '../' . $slider['media_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $stmt = $pdo->prepare("DELETE FROM home_sliders WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Slider non trouvé.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'DB Error: ' . $e->getMessage()]);
}
