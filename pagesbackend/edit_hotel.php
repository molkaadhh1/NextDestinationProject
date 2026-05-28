<?php
// =============================================
//  php/edit_hotel.php  –  Modifier un hôtel
//  Méthode : POST (multipart/form-data)
//  Champs  : id, name, location, stars, price, promo, description, image (optionnel)
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

$id          = (int)($_POST['id']          ?? 0);
$name        = trim($_POST['name']         ?? '');
$location    = trim($_POST['location']     ?? '');
$stars       = (int)($_POST['stars']       ?? 5);
$price       = (float)($_POST['price']     ?? 0);
$promo       = (int)($_POST['promo']       ?? 0);
$description = trim($_POST['description']  ?? '');

if (!$id || !$name || !$location || $price <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
    exit;
}

if ($stars < 1 || $stars > 5)  $stars = 5;
if ($promo < 0 || $promo > 99) $promo = 0;

try {
    $pdo = getDB();

    // Récupérer l'ancienne image
    $old = $pdo->prepare("SELECT image FROM hotels WHERE id = :id");
    $old->execute([':id' => $id]);
    $row = $old->fetch();
    if (!$row) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Hôtel introuvable']);
        exit;
    }

    $imagePath = $row['image'];  // Conserver l'ancienne par défaut

    // Nouvelle image uploadée ?
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir  = __DIR__ . '/../images/hotels/';
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize    = 5 * 1024 * 1024;

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Format image non autorisé']);
            exit;
        }
        if ($_FILES['image']['size'] > $maxSize) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Image trop lourde (max 5 Mo)']);
            exit;
        }

        $filename = uniqid('hotel_', true) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
            // Supprimer l'ancienne image si elle était dans hotels/
            if ($imagePath && strpos($imagePath, 'images/hotels/') === 0) {
                $oldFile = __DIR__ . '/../' . $imagePath;
                if (file_exists($oldFile)) unlink($oldFile);
            }
            $imagePath = 'images/hotels/' . $filename;
        }
    }

    $stmt = $pdo->prepare("
        UPDATE hotels
        SET name=:name, location=:location, stars=:stars,
            price=:price, promo=:promo, description=:description, image=:image
        WHERE id=:id
    ");
    $stmt->execute([
        ':name'        => $name,
        ':location'    => $location,
        ':stars'       => $stars,
        ':price'       => $price,
        ':promo'       => $promo,
        ':description' => $description,
        ':image'       => $imagePath,
        ':id'          => $id,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Hôtel mis à jour avec succès',
        'image'   => $imagePath,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}