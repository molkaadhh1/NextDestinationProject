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
    // Hotel reservations
    $stmt1 = $pdo->prepare("
        SELECT hr.id, u.name as client, h.name as dest, hr.checkin_date as cin, hr.checkout_date as cout, hr.adults as pers, hr.total_price as amount, hr.status, 'hotel' as type, hr.created_at
        FROM hotel_reservations hr
        JOIN users u ON hr.user_id = u.id
        JOIN hotels h ON hr.hotel_id = h.id
        ORDER BY hr.created_at DESC
    ");
    $stmt1->execute();
    $hotel_res = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Package reservations
    $stmt2 = $pdo->prepare("
        SELECT pr.id, u.name as client, p.name as dest, pr.departure_date as cin, '-' as cout, (pr.adults + pr.children) as pers, pr.total_price as amount, pr.status, 'package' as type, pr.created_at
        FROM package_reservations pr
        JOIN users u ON pr.user_id = u.id
        JOIN packages p ON pr.package_id = p.id
        ORDER BY pr.created_at DESC
    ");
    $stmt2->execute();
    $package_res = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Merge and sort by created_at DESC
    $all_bookings = array_merge($hotel_res, $package_res);
    usort($all_bookings, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Format IDs
    foreach ($all_bookings as &$b) {
        $prefix = $b['type'] === 'hotel' ? 'H' : 'P';
        $b['formatted_id'] = $prefix . sprintf('%04d', $b['id']);
    }

    echo json_encode(['success' => true, 'data' => $all_bookings]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de BDD: ' . $e->getMessage()]);
}
?>
