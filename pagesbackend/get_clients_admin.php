<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit;
}

$pdo = getDB();

try {
    $sql = "
        SELECT u.id, u.name, u.email, u.created_at,
               (SELECT COUNT(*) FROM hotel_reservations hr WHERE hr.user_id = u.id) +
               (SELECT COUNT(*) FROM package_reservations pr WHERE pr.user_id = u.id) as bookings,
               COALESCE((SELECT SUM(total_price) FROM hotel_reservations hr WHERE hr.user_id = u.id), 0) +
               COALESCE((SELECT SUM(total_price) FROM package_reservations pr WHERE pr.user_id = u.id), 0) as total
        FROM users u
        WHERE u.role = 'user'
        HAVING bookings > 0
        ORDER BY u.created_at DESC
    ";
    
    $stmt = $pdo->query($sql);
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format date
    foreach ($clients as &$c) {
        $c['date'] = date('M Y', strtotime($c['created_at']));
        $c['phone'] = '-'; // no phone in users table
    }

    echo json_encode(['success' => true, 'data' => $clients]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur SQL : ' . $e->getMessage()]);
}
?>
