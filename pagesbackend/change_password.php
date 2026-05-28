<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');
if (!isAdmin()) { echo json_encode(['success' => false, 'message' => 'Accès refusé']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

$current = trim($data['current'] ?? '');
$newpass = trim($data['newpass'] ?? '');

if (!$current || !$newpass) {
    echo json_encode(['success' => false, 'message' => 'Veuillez remplir tous les champs.']);
    exit;
}

$pdo = getDB();
try {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect.']);
        exit;
    }

    if (strlen($newpass) < 6) {
        echo json_encode(['success' => false, 'message' => 'Le nouveau mot de passe doit comporter au moins 6 caractères.']);
        exit;
    }

    $hash = password_hash($newpass, PASSWORD_BCRYPT);
    $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $upd->execute([$hash, $_SESSION['user_id']]);

    echo json_encode(['success' => true, 'message' => 'Mot de passe mis à jour avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
