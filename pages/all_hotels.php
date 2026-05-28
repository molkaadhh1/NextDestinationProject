<?php
require_once '../includes/session.php';
require_once '../config/db.php';
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM agency_settings LIMIT 1");
$agency_settings = $stmt->fetch(PDO::FETCH_ASSOC);

$agency_name = $agency_settings['agency_name'] ?? 'Next Destination';
$agency_email = $agency_settings['contact_email'] ?? 'contact@nextdestination.tn';
$agency_phone = $agency_settings['phone'] ?? '+216 71 000 000';
$agency_address = $agency_settings['address'] ?? 'Lafayette, Tunis, Tunisie';
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Tous les Hôtels - <?= htmlspecialchars($agency_name) ?></title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      <link rel="stylesheet" href="../css/style.css">  
      <style>
         body { padding-top: 100px; } /* pour que le header fixe ne cache pas le titre */
      </style>
   </head>
   <body>
      <header>
         <div id="menu-bar" class="fas fa-bars"></div>
         <a href="../index.php" class="logo"><span>N</span>ext <span>D</span>estination</a>
         <nav class="navbar">
            <a href="../index.php#home">home</a>
            <a href="../index.php#book">book</a>
            <a href="../index.php#packages">packages</a>
            <a href="../index.php#services">services</a>
            <a href="../index.php#gallery">gallery</a>
            <a href="../index.php#review">review</a>
            <a href="../index.php#contact">contact</a>
         </nav>
      </header>

      <section class="hotels" id="all-hotels" style="margin-top: 5rem;">
         <h1 class="heading">
               <span>t</span>
               <span>o</span>
               <span>u</span>
               <span>s</span>
               <span class="space"> </span>
               <span>h</span>
               <span>ô</span>
               <span>t</span>
               <span>e</span>
               <span>l</span>
               <span>s</span>
         </h1>
         <div class="hotel-cards">
            <?php
               $location = $_GET['location'] ?? '';
               $checkin = $_GET['checkin'] ?? '';
               $checkout = $_GET['checkout'] ?? '';
               $rooms_req = (int)($_GET['rooms'] ?? 1);
               $adults_req = (int)($_GET['adults'] ?? 2);
               $children_req = (int)($_GET['children'] ?? 0);
               
               $has_search = (!empty($location) || !empty($checkin) || !empty($checkout) || isset($_GET['adults']));
               
               $sql = "SELECT * FROM hotels ORDER BY created_at DESC";
               $params = [];
               
               if ($has_search) {
                   if (empty($checkin) || empty($checkout)) {
                       if (!empty($location)) {
                           $sql = "SELECT * FROM hotels WHERE location LIKE ? ORDER BY created_at DESC";
                           $params[] = "%$location%";
                       }
                   } else {
                       $sql = "
                           SELECT DISTINCT h.* 
                           FROM hotels h
                           JOIN hotel_rooms hr ON h.id = hr.hotel_id
                           WHERE (hr.capacity_adults >= ? AND hr.capacity_children >= ?)
                       ";
                       $params[] = $adults_req;
                       $params[] = $children_req;
                       
                       if (!empty($location)) {
                           $sql .= " AND h.location LIKE ? ";
                           $params[] = "%$location%";
                       }
               
                       $sql .= "
                           AND hr.total_rooms - (
                               SELECT COALESCE(SUM(rooms), 0)
                               FROM hotel_reservations res
                               WHERE res.hotel_id = h.id 
                               AND res.room_type = hr.room_type
                               AND res.status != 'cancelled'
                               AND (res.checkin_date < ? AND res.checkout_date > ?)
                           ) >= ?
                           ORDER BY h.created_at DESC
                       ";
                       $params[] = $checkout;
                       $params[] = $checkin;
                       $params[] = $rooms_req;
                   }
               }

               try {
                   $stmt = $pdo->prepare($sql);
                   $stmt->execute($params);
                   $hotels = $stmt->fetchAll();
                   
                   if (empty($hotels)) {
                       echo "<p style='text-align:center; font-size: 1.5rem;'>Aucun hôtel disponible pour le moment" . ($has_search ? " selon vos critères" : "") . ".</p>";
                   } else {
                       foreach ($hotels as $h) {
                           $imagePath = !empty($h['image']) ? htmlspecialchars($h['image']) : '../images/Nour_Palace_Resort_et_Thalasso_.jpg';
                           if (strpos($imagePath, '../') !== 0 && strpos($imagePath, 'images/') === 0) {
                               $imagePath = '../' . $imagePath;
                           }
                           
                           $hotelId = (int)$h['id'];
                           $promoBadge = $h['promo'] > 0 ? "<div class=\"badge-discount\">-{$h['promo']}%</div>" : '';
                           
                           $starsHtml = '';
                           for ($i = 0; $i < $h['stars']; $i++) {
                               $starsHtml .= '<i class="fas fa-star"></i>';
                           }
                           
                           echo "
                           <div class=\"card\" onclick=\"window.location.href='hotel-detail.php?id={$hotelId}'\" style=\"cursor:pointer;\">
                              <div class=\"card-img-wrap\">
                                 <img src=\"{$imagePath}\" alt=\"" . htmlspecialchars($h['name']) . "\">
                                 {$promoBadge}
                                 <div class=\"price-overlay\">
                                    Dès <strong>{$h['price']}</strong> TND /Nuit/Pers/En Logement petit déjeuner
                                 </div>
                              </div>
                              <div class=\"card-body\">
                                 <h3 class=\"card-title\">" . htmlspecialchars($h['name']) . "</h3>
                                 <div class=\"stars\">
                                    {$starsHtml}
                                 </div>
                                 <p class=\"card-location\">" . htmlspecialchars($h['location']) . "</p>
                                 <p class=\"card-promo\">{$h['promo']}%</p>
                              </div>
                           </div>
                           ";
                       }
                   }
               } catch (PDOException $e) {
                   echo "<p>Erreur de connexion à la base de données.</p>";
               }
            ?>
         </div>
      </section>

      <section class="footer">
         <h1 class="credit">created by <span>molka dhahri</span>| all rights reserved</h1>
      </section>

      <script src="../js/script.js"></script>
   </body>
</html>
