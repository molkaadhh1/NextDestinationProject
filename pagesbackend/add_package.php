<?php
// session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../config/db.php';

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     http_response_code(403);
//     echo json_encode(['success' => false, 'message' => 'Non autorisé']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $stars = (int)($_POST['stars'] ?? 5);
    $price = (float)($_POST['price'] ?? 0);
    $old_price = !empty($_POST['old_price']) ? (float)$_POST['old_price'] : null;
    $duration = trim($_POST['duration'] ?? '');
    $group_size = trim($_POST['group_size'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $difficulty = trim($_POST['difficulty'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $inclus = trim($_POST['inclus'] ?? '');
    $exclus = trim($_POST['exclus'] ?? '');
    $programme = trim($_POST['programme'] ?? '');

    if (empty($name) || empty($location) || empty($price)) {
        echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
        exit;
    }

    $imagePaths = [];
    if (isset($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
        $uploadDir = '../images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $filename = uniqid() . '_' . basename($_FILES['images']['name'][$key]);
                $targetFile = $uploadDir . $filename;
                if (move_uploaded_file($tmp_name, $targetFile)) {
                    $imagePaths[] = 'images/' . $filename;
                }
            }
        }
    }
    
    if (empty($imagePaths)) {
        // Fallback to singular image upload just in case
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePaths[] = 'images/' . $filename;
            }
        }
    }

    if (empty($imagePaths)) {
        $imagePaths[] = 'images/tunisia_oasi_di_montagna_foto_i._fornasiero.jpg'; // default
    }
    
    $imagePathJson = json_encode($imagePaths);

    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO packages (name, location, stars, price, old_price, duration, group_size, language, difficulty, description, inclus, exclus, programme, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $location, $stars, $price, $old_price, $duration, $group_size, $language, $difficulty, $description, $inclus, $exclus, $programme, $imagePathJson]);
        
        echo json_encode(['success' => true, 'message' => 'Package ajouté avec succès']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Erreur BD: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
?>
