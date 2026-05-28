<?php
require_once '../includes/session.php';
require_once '../config/db.php';

header('Content-Type: application/json');
if (!isAdmin()) { echo json_encode(['success' => false]); exit; }

$pdo = getDB();

try {
    // Total Reservations
    $stmt = $pdo->query("SELECT (SELECT COUNT(*) FROM hotel_reservations) + (SELECT COUNT(*) FROM package_reservations) as count");
    $totalRes = $stmt->fetchColumn();

    // Total Revenue (only confirmed or completed)
    $stmt = $pdo->query("SELECT COALESCE((SELECT SUM(total_price) FROM hotel_reservations WHERE status IN ('confirmed', 'completed')), 0) + COALESCE((SELECT SUM(total_price) FROM package_reservations WHERE status IN ('confirmed', 'completed')), 0) as rev");
    $totalRev = $stmt->fetchColumn();

    // Total Clients
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $totalClients = $stmt->fetchColumn();

    // Cancellations
    $stmt = $pdo->query("SELECT (SELECT COUNT(*) FROM hotel_reservations WHERE status = 'cancelled') + (SELECT COUNT(*) FROM package_reservations WHERE status = 'cancelled') as count");
    $totalCanc = $stmt->fetchColumn();

    // Monthly reservations for current year
    $year = date('Y');
    $monthly = array_fill(0, 12, 0);
    
    $stmtH = $pdo->prepare("SELECT MONTH(created_at) as m, COUNT(*) as c FROM hotel_reservations WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at)");
    $stmtH->execute([$year]);
    while ($row = $stmtH->fetch(PDO::FETCH_ASSOC)) {
        $monthly[(int)$row['m'] - 1] += (int)$row['c'];
    }
    
    $stmtP = $pdo->prepare("SELECT MONTH(created_at) as m, COUNT(*) as c FROM package_reservations WHERE YEAR(created_at) = ? GROUP BY MONTH(created_at)");
    $stmtP->execute([$year]);
    while ($row = $stmtP->fetch(PDO::FETCH_ASSOC)) {
        $monthly[(int)$row['m'] - 1] += (int)$row['c'];
    }

    echo json_encode([
        'success' => true,
        'reservations' => (int)$totalRes,
        'revenue' => (float)$totalRev,
        'clients' => (int)$totalClients,
        'cancellations' => (int)$totalCanc,
        'monthly' => $monthly
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
