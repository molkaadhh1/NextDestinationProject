<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Non autorisé']);
    exit;
}

$title = $_POST['title'] ?? '';
$subtitle = $_POST['subtitle'] ?? '';

if (empty($title)) {
    echo json_encode(['success' => false, 'error' => 'Le titre est requis.']);
    exit;
}

if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Média requis ou erreur d\'upload.']);
    exit;
}

$fileTmpPath = $_FILES['media']['tmp_name'];
$fileName = $_FILES['media']['name'];
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'webm'];
if (!in_array($fileExtension, $allowedExtensions)) {
    echo json_encode(['success' => false, 'error' => 'Format non supporté (autorisés: jpg, png, webp, mp4, webm).']);
    exit;
}

$mediaType = in_array($fileExtension, ['mp4', 'webm']) ? 'video' : 'image';
$uploadDir = $mediaType === 'video' ? '../vids/' : '../images/';
$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
$destPath = $uploadDir . $newFileName;
$dbPath = ($mediaType === 'video' ? 'vids/' : 'images/') . $newFileName;

if(move_uploaded_file($fileTmpPath, $destPath)) {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO home_sliders (media_path, media_type, title, subtitle) VALUES (?, ?, ?, ?)");
        $stmt->execute([$dbPath, $mediaType, $title, $subtitle]);
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la sauvegarde du fichier.']);
}
