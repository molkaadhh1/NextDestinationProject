<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Méthode non autorisée");
}

$hotel_id    = !empty($_POST['hotel_id']) ? (int)$_POST['hotel_id'] : null;
$package_id  = !empty($_POST['package_id']) ? (int)$_POST['package_id'] : null;
$client_name = trim($_POST['client_name'] ?? '');
$rating      = (int)($_POST['rating'] ?? 5);
$comment     = trim($_POST['comment'] ?? '');

if ((!$hotel_id && !$package_id) || empty($client_name)) {
    die("L'ID de l'hôtel ou du package et votre nom sont requis.");
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO reviews (hotel_id, package_id, client_name, rating, comment, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$hotel_id, $package_id, $client_name, $rating, $comment]);

    $redirectUrl = $hotel_id ? "../pages/hotel-detail.php?id=" . $hotel_id : "../pages/package-detail.php?id=" . $package_id;

    echo "<script>
        alert('Votre avis a été soumis avec succès. Il sera visible après validation par un administrateur.');
        window.location.href = '" . $redirectUrl . "';
    </script>";

} catch (PDOException $e) {
    die("Erreur lors de l'ajout de l'avis: " . $e->getMessage());
}
?>
