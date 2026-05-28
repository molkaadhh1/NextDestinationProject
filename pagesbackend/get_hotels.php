<?php
// =============================================
//  php/get_hotels.php  –  Retourne tous les hôtels en JSON
//  Appelé par : admin.html  ET  pages/index.html
// =============================================

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC");
    $hotels = $stmt->fetchAll();

    // Convertir les types numériques
    foreach ($hotels as &$h) {
        $h['id']    = (int)$h['id'];
        $h['stars'] = (int)$h['stars'];
        $h['price'] = (float)$h['price'];
        $h['promo'] = (int)$h['promo'];
    }

    echo json_encode(['success' => true, 'data' => $hotels]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}