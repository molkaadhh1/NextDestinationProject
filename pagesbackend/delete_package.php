<?php
// session_start();
require_once '../config/db.php';

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'error' => 'Unauthorized']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) ? (int)$data['id'] : 0;

    if ($id) {
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("DELETE FROM packages WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
}
?>
