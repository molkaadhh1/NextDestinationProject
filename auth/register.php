<?php
require_once '../config/db.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'errors' => ['Méthode non autorisée']]);
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$errors   = [];

if (empty($name) || empty($email) || empty($password)) {
    $errors[] = "Tous les champs sont requis.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email invalide.";
}
if (strlen($password) < 6) {
    $errors[] = "Mot de passe trop court (min 6 caractères).";
}

// Email déjà utilisé ?
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $errors[] = "Cet email est déjà utilisé.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
$stmt->execute([$name, $email, $hash]);

// Auto-login après inscription
$_SESSION['user_id']    = $pdo->lastInsertId();
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;
$_SESSION['role']       = 'user';

echo json_encode(['success' => true, 'redirect' => '/index.php']);
exit;
?>