<?php
require_once '../config/db.php';
require_once '../includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'errors' => ['Méthode non autorisée']]);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$errors   = [];

if (empty($email) || empty($password)) {
    $errors[] = "Email et mot de passe requis.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role']       = $user['role'];

    $base = str_replace('/auth', '', dirname($_SERVER['PHP_SELF']));

        $redirect = $user['role'] === 'admin'
            ? $base . '/pages/next_destination_admin.php'
            : $base . '/index.php';

        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        echo json_encode(['success' => false, 'errors' => ['Email ou mot de passe incorrect.']]);
    }
exit;
?>