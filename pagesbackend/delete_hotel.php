<?php
// =============================================
//  php/delete_hotel.php  –  Supprimer un hôtel
//  Méthode : POST
//  Champs  : id
// =============================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

try {
    $pdo = getDB();

    // Récupérer le chemin image avant suppression
    $sel = $pdo->prepare("SELECT image FROM hotels WHERE id = :id");
    $sel->execute([':id' => $id]);
    $row = $sel->fetch();

    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Hôtel introuvable']);
        exit;
    }

    // Supprimer le fichier image si c'est un fichier uploadé
    if ($row['image'] && strpos($row['image'], 'images/hotels/') === 0) {
        $filePath = __DIR__ . '/../' . $row['image'];
        if (file_exists($filePath)) unlink($filePath);
    }

    // Supprimer l'enregistrement
    $del = $pdo->prepare("DELETE FROM hotels WHERE id = :id");
    $del->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Hôtel supprimé avec succès']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}