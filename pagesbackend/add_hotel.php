<?php
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once __DIR__ . '/../config/db.php'; // ← corrigé (/../)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// ── Validation ──
$name        = trim($_POST['name']        ?? '');
$location    = trim($_POST['location']    ?? '');
$stars       = (int)($_POST['stars']      ?? 5);
$price       = (float)($_POST['price']    ?? 0);
$promo       = (int)($_POST['promo']      ?? 0);
$description = trim($_POST['description'] ?? '');

if (!$name || !$location || $price <= 0) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants (name, location, price)']);
    exit;
}

if ($stars < 1 || $stars > 5)  $stars = 5;
if ($promo < 0 || $promo > 99) $promo = 0;

// ── Gestion image ──
$imagePath = null;
if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir  = __DIR__ . '/../images/hotels/';
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize    = 5 * 1024 * 1024;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExt)) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Format non autorisé (jpg, jpeg, png, webp)']);
        exit;
    }

    if ($_FILES['image']['size'] > $maxSize) {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Image trop lourde (max 5 Mo)']);
        exit;
    }

    $filename = uniqid('hotel_', true) . '.' . $ext;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
        ob_end_clean();
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur déplacement fichier']);
        exit;
    }
    $imagePath = 'images/hotels/' . $filename;
}

// ── Insertion BDD ──  (on utilise $pdo directement depuis db.php)
try {
    $pdo = getDB();

    $stmt = $pdo->prepare("
        INSERT INTO hotels (name, location, stars, price, promo, description, image)
        VALUES (:name, :location, :stars, :price, :promo, :description, :image)
    ");
    $stmt->execute([
        ':name'        => $name,
        ':location'    => $location,
        ':stars'       => $stars,
        ':price'       => $price,
        ':promo'       => $promo,
        ':description' => $description,
        ':image'       => $imagePath,
    ]);

    $newId = (int)$pdo->lastInsertId();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Hôtel ajouté avec succès',
        'id'      => $newId,
        'image'   => $imagePath,
    ]);

} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}