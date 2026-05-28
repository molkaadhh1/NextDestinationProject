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
      <title>Tous les Packages - <?= htmlspecialchars($agency_name) ?></title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      <link rel="stylesheet" href="../css/style.css">  
      <style>
         body { padding-top: 100px; }
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

      <section class="packages" id="all-packages" style="margin-top: 5rem;">
         <h1 class="heading">
               <span>t</span>
               <span>o</span>
               <span>u</span>
               <span>s</span>
               <span class="space"> </span>
               <span>p</span>
               <span>a</span>
               <span>c</span>
               <span>k</span>
               <span>a</span>
               <span>g</span>
               <span>e</span>
               <span>s</span>
         </h1>
         <div class="box-container">
            <?php
               try {
                   $stmt = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC");
                   $packages = $stmt->fetchAll();
                   
                   if (empty($packages)) {
                       echo "<p style='text-align:center; font-size: 1.5rem;'>Aucun package disponible pour le moment.</p>";
                   } else {
                       foreach ($packages as $p) {
                           $images = json_decode($p['image'], true);
                           $imagePath = !empty($images) && is_array($images) ? htmlspecialchars($images[0]) : '../images/default-package.jpg';
                           if (strpos($imagePath, '../') !== 0 && strpos($imagePath, 'images/') === 0) {
                               $imagePath = '../' . $imagePath;
                           }
                           
                           $packageId = (int)$p['id'];
                           
                           $starsHtml = '';
                           for ($i = 0; $i < $p['stars']; $i++) {
                               $starsHtml .= '<i class="fas fa-star"></i>';
                           }
                           
                           $oldPriceHtml = $p['old_price'] ? "<span>{$p['old_price']} TND</span>" : "";
                           
                           echo "
                           <div class=\"box\" onclick=\"window.location.href='package-detail.php?id={$packageId}'\" style=\"cursor:pointer;\">
                              <img src=\"{$imagePath}\" alt=\"" . htmlspecialchars($p['name']) . "\">
                              <div class=\"content\">
                                 <h3> <i class=\"fas fa-map-marker-alt\"></i> " . htmlspecialchars($p['location']) . " </h3>
                                 <p>" . htmlspecialchars($p['name']) . "</p>
                                 <div class=\"stars\">
                                    {$starsHtml}
                                 </div>
                                 <div class=\"price\">
                                    {$p['price']} TND {$oldPriceHtml}
                                 </div>
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
