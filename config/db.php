<?php
$host = 'localhost';
$dbname = 'next_destination';
$db_user = 'root';
$db_pass = '';  // ton mot de passe XAMPP/WAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'errors' => ['Connexion BDD échouée']]));
}

function getDB() {
    global $pdo;
    return $pdo;
}
?>