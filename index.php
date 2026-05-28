<?php
require_once 'includes/session.php';
require_once 'config/db.php';
$pdo = getDB();
$stmt = $pdo->query("SELECT * FROM agency_settings LIMIT 1");
$agency_settings = $stmt->fetch(PDO::FETCH_ASSOC);

$agency_name = $agency_settings['agency_name'] ?? 'Next Destination';
$agency_email = $agency_settings['contact_email'] ?? 'contact@nextdestination.tn';
$agency_phone = $agency_settings['phone'] ?? '+216 71 000 000';
$agency_address = $agency_settings['address'] ?? 'Lafayette, Tunis, Tunisie';

try {
    $stmt_slider = $pdo->query("SELECT * FROM home_sliders ORDER BY created_at ASC");
    $home_sliders = $stmt_slider->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $home_sliders = [];
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Next Destination</title>
      <!-- font  awesome pour les icones-->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
      <link rel="stylesheet" href="css/style.css">  
   </head>
   <body>
      <!--header section start-->
      <header>
         <div id="menu-bar" class="fas fa-bars"></div>
         <a href="#" class="logo"><span>N</span>ext <span>D</span>estination</a>
         <nav class="navbar">
            <a href="#home">home</a>
            <a href="#book">book</a>
            <a href="#packages">packages</a>
            <a href="#services">services</a>
            <a href="#gallery">gallery</a>
            <a href="#review">review</a>
            <a href="#contact">contact</a>
         </nav>
         <div class="icons">
            
            <?php if (isLoggedIn()): ?>
               <div class="user-avatar-menu">
                     <div class="avatar-circle" id="user-btn">
                        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                     </div>

                     <div class="user-dropdown" id="user-dropdown">
                        <!-- ── Infos utilisateur ── -->
                        <div class="user-info-header">
                           <div class="avatar-circle-lg">
                                 <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                           </div>
                           <div>
                                 <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                                 <span><?= htmlspecialchars($_SESSION['user_email']) ?></span>
                           </div>
                        </div>

                        <hr class="dropdown-divider">

                        <!-- ── Réservations ── -->
                        <div class="dropdown-section-title">
                           <i class="fas fa-calendar-check"></i> Mes Réservations
                        </div>

                        <div class="reservations-list">
                           <?php
                           require_once 'config/db.php';
                           $reservations = [];
                           try {
                                 $pdo = getDB();
                                 $sql = "
                                    SELECT 'hotel' as type, h.name as destination, hr.checkin_date as check_in, hr.checkout_date as check_out, hr.adults, 0 as children, hr.status, hr.created_at
                                    FROM hotel_reservations hr
                                    JOIN hotels h ON hr.hotel_id = h.id
                                    WHERE hr.user_id = ?
                                    UNION
                                    SELECT 'package' as type, p.name as destination, pr.departure_date as check_in, pr.departure_date as check_out, pr.adults, pr.children, pr.status, pr.created_at
                                    FROM package_reservations pr
                                    JOIN packages p ON pr.package_id = p.id
                                    WHERE pr.user_id = ?
                                    ORDER BY created_at DESC LIMIT 5
                                 ";
                                 $stmt = $pdo->prepare($sql);
                                 $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                                 $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                              } catch (Exception $e) {
                                 // table pas encore créée, on ignore
                              }
                           if (empty($reservations)): ?>
                                 <p class="no-reservations">Aucune réservation pour le moment.</p>
                           <?php else: foreach ($reservations as $r): ?>
                                 <div class="reservation-item">
                                    <div class="res-destination">
                                       <i class="fas fa-map-marker-alt"></i>
                                       <?= htmlspecialchars($r['destination']) ?>
                                    </div>
                                    <div class="res-dates">
                                       <i class="fas fa-calendar"></i>
                                       <?= date('d/m/Y', strtotime($r['check_in'])) ?> → <?= date('d/m/Y', strtotime($r['check_out'])) ?>
                                    </div>
                                    <div class="res-details">
                                       <span><?= $r['adults'] ?> adulte(s), <?= $r['children'] ?> enfant(s)</span>
                                       <span class="res-status status-<?= $r['status'] ?>">
                                             <?= ucfirst($r['status']) ?>
                                       </span>
                                    </div>
                                 </div>
                           <?php endforeach; endif; ?>
                        </div>

                        <hr class="dropdown-divider">

                        <!-- ── Actions ── -->
                        <?php if (isAdmin()): ?>
                           <a href="pages/next_destination_admin.php" class="dropdown-link">
                                 
                           </a>
                        <?php endif; ?>

                        <a href="auth/logout.php" class="dropdown-link logout-link">
                           <i class="fas fa-sign-out-alt"></i> log out
                        </a>
                     </div>
               </div>

            <?php else: ?>
               <i class="fas fa-user" id="login-btn"></i>
            <?php endif; ?>
         </div>
         


      </header>
      <!--header section end-->

      <!-- login form container-->
      <div class="login-form-container">
         <i class="fas fa-times" id="form-close"></i>
         <form action="">
            <h3 id="form-title">login</h3>
            <div id="auth-msg" class="auth-msg"></div>

            
            <input type="text" class="box" id="reg-name" placeholder="enter your name" style="display:none">

            <input type="email" class="box" id="login-email" placeholder="enter your email">
            <input type="password" class="box" id="login-password" placeholder="enter your password">
            <input type="submit" id="form-submit" value="login now" class="btn">
            <input type="checkbox" id="remember">
            <label for="remember">remember me</label>
            <p>forget password <a href="#">click here</a></p>
            <p id="switch-text">don't have an account? <a href="#" id="switch-to-register">register now</a></p>
         </form>
      </div>
      

      <!-- login form container end-->

      <!-- home section starts-->
       <section class="home" id="home">
         <?php if (empty($home_sliders)): ?>
            <video class="slider active" src="vids\3.mp4" autoplay muted loop></video>
            <div class="content active">
               <h1>adventure is worthwhile</h1>
               <p> discover new places with us, adventure awaits</p>
            </div>
         <?php else: ?>
            <?php foreach ($home_sliders as $index => $s): ?>
               <?php $activeClass = $index === 0 ? 'active' : ''; ?>
               <?php if ($s['media_type'] === 'video'): ?>
                  <video class="slider <?= $activeClass ?>" src="<?= htmlspecialchars($s['media_path']) ?>" autoplay muted loop></video>
               <?php else: ?>
                  <img class="slider <?= $activeClass ?>" src="<?= htmlspecialchars($s['media_path']) ?>" alt="slider">
               <?php endif; ?>
            <?php endforeach; ?>

            <?php foreach ($home_sliders as $index => $s): ?>
               <?php $activeClass = $index === 0 ? 'active' : ''; ?>
               <div class="content <?= $activeClass ?>">
                  <h1><?= htmlspecialchars($s['title']) ?></h1>
                  <?php if (!empty($s['subtitle'])): ?>
                     <p><?= htmlspecialchars($s['subtitle']) ?></p>
                  <?php endif; ?>
               </div>
            <?php endforeach; ?>
         <?php endif; ?>
         <div class="media-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
         </div>
         <button class="arrow-btn left-arrow">
            <i class="fas fa-chevron-left"></i>
         </button>
         <button class="arrow-btn right-arrow">
            <i class="fas fa-chevron-right"></i>
         </button>
         <div class="booking-form">
            <form action="pages/all_hotels.php" method="GET" class="booking-">
               <input type="hidden" name="rooms" id="hidden-rooms" value="1">
               <input type="hidden" name="adults" id="hidden-adults" value="2">
               <input type="hidden" name="children" id="hidden-children" value="0">
               
               <div class="form-group">
                  <label for="location">location</label>
                  <input type="text" id="location" name="location" placeholder="where are you going?">
               </div>
               <div class="form-group">
                  <label for="check-in">check in</label>
                  <input type="text" id="check-in" name="checkin" placeholder="add check in date " onfocus="(this.type='date')" onblur="(this.type='text')">
               </div>
               <div class="form-group">
                  <label for="check-out">check out</label>
                  <input type="text" id="check-out" name="checkout" placeholder="add check out date " onfocus="(this.type='date')" onblur="(this.type='text')">
               </div>
               <div class="form-group traveler-group">
                  <label>traveler</label>
                  <div class="traveler-input" id="traveler-trigger">
                     <span id="traveler-text">1 Chambre, 2 Adultes, 0 Enfant</span>
                  </div>
                  <div class="traveler-dropdown" id="traveler-panel">
                     <div class="room-header">
                        <span>Chambre 1</span>
                        <button id="close-dropdown">✕</button>
                     </div>
                     <div class="counter-row">
                        <div class="counter-label">Adulte(s)</div>
                        <div class="counter-ctrl">
                        <button class="count-btn" id="adult-minus">−</button>
                        <span id="adult-count">2</span>
                        <button class="count-btn" id="adult-plus">+</button>
                        </div>
                     </div>
                     <div class="counter-row">
                        <div>
                        <div class="counter-label">Enfant(s)</div>
                        <div class="counter-sub">De 0 à 11,99 ans</div>
                        </div>
                        <div class="counter-ctrl">
                        <button class="count-btn" id="child-minus">−</button>
                        <span id="child-count">0</span>
                        <button class="count-btn" id="child-plus">+</button>
                        </div>
                     </div>
                     <button class="add-room-btn">+ Ajouter une autre chambre</button>
                     <button class="validate-btn" id="validate-dropdown">Valider</button>
                  </div>
                  </div>
               <button type="submit" class="form-submit-btn" aria-label="search">
                  <i class="fas fa-search"></i>
               </button>
            </form>
         </div>
         

       </section>

       <!-- home section starts ends -->
       <!-- packages section starts--> 
      <section class="packages" id="packages">
         <h1 class="heading">
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
                   $stmt = $pdo->query("SELECT * FROM packages ORDER BY created_at DESC LIMIT 6");
                   $packages = $stmt->fetchAll();
                   
                   if (empty($packages)) {
                       echo "<p style='text-align:center; font-size: 1.5rem;'>Aucun package disponible pour le moment.</p>";
                   } else {
                       foreach ($packages as $p) {
                           $images = json_decode($p['image'], true);
                           $imagePath = !empty($images) && is_array($images) ? htmlspecialchars($images[0]) : 'images/default-package.jpg';
                           $packageId = (int)$p['id'];
                           
                           $starsHtml = '';
                           for ($i = 0; $i < $p['stars']; $i++) {
                               $starsHtml .= '<i class="fas fa-star"></i>';
                           }
                           
                           $oldPriceHtml = $p['old_price'] ? "<span>{$p['old_price']} TND</span>" : "";
                           
                           echo "
                           <div class=\"box\" onclick=\"window.location.href='pages/package-detail.php?id={$packageId}'\" style=\"cursor:pointer;\">
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
         <?php if (!empty($packages) && count($packages) == 6): ?>
         <div class="text-center" style="margin-top: 2rem; text-align: center;">
            <a href="pages/all_packages.php" class="btn">Afficher tous les packages</a>
         </div>
         <?php endif; ?>
      </section>
       <!-- packages section ends -->
       <!--hotels section starts-->
      <section class="hotels">
         <h1 class="heading">
               <span>h</span>
               <span>o</span>
               <span>t</span>
               <span>e</span>
               <span>l</span>
               <span>s</span>
               
         </h1>
         <div class="hotel-cards">
            <?php
               require_once 'config/db.php';
               $pdo = getDB();
               try {
                   $stmt = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC LIMIT 6");
                   $hotels = $stmt->fetchAll();
                   
                   if (empty($hotels)) {
                       echo "<p style='text-align:center; font-size: 1.5rem;'>Aucun hôtel disponible pour le moment.</p>";
                   } else {
                       foreach ($hotels as $h) {
                           $imagePath = !empty($h['image']) ? htmlspecialchars($h['image']) : 'images/Nour_Palace_Resort_et_Thalasso_.jpg';
                           $hotelId = (int)$h['id'];
                           $promoBadge = $h['promo'] > 0 ? "<div class=\"badge-discount\">-{$h['promo']}%</div>" : '';
                           
                           // Draw stars
                           $starsHtml = '';
                           for ($i = 0; $i < $h['stars']; $i++) {
                               $starsHtml .= '<i class="fas fa-star"></i>';
                           }
                           
                           echo "
                           <div class=\"card\" onclick=\"window.location.href='pages/hotel-detail.php?id={$hotelId}'\" style=\"cursor:pointer;\">
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
         <?php if (!empty($hotels) && count($hotels) == 6): ?>
         <div class="text-center" style="margin-top: 2rem; text-align: center;">
            <a href="pages/all_hotels.php" class="btn">Afficher tous les hôtels</a>
         </div>
         <?php endif; ?>
      </section>
        <!--hotels section ends-->
       <!-- services section start-->
        <section class="services" id="services">
            <h1 class="heading">
               <span>s</span>
               <span>e</span>
               <span>r</span>
               <span>v</span>
               <span>i</span>
               <span>c</span>
               <span>e</span>
               <span>s</span>
            </h1>
            <div class="box-container">
               <div class="box">
                  <i class="fas fa-hotel"></i>
                  <h3>affordable hotels</h3>
                  <p>content content content</p>
               </div>
               <!-- <div class="box">
                  <i class="fas fa-utensils"></i>
                  <h3>food and drinks</h3>
                  <p>content content content</p>
               </div> -->
               <div class="box">
                  <i class="fas fa-bullhorn"></i>
                  <h3>safty guide</h3>
                  <p>content content content</p>
               </div>
               <div class="box">
                  <i class="fas fa-plane"></i>
                  <h3>fastest travel</h3>
                  <p>content content content</p>
               </div>
            </div>
         </section>

       <!-- services section end -->



        <!--contact section starts  -->
        <section class="contact">
            <h1 class="heading">
               <span>c</span>
               <span>o</span>
               <span>n</span>
               <span>t</span>
               <span>a</span>
               <span>c</span>
               <span>t</span>
               
            </h1>

          <div class="row">
           <div class="image">
             <img src="images/message.png" alt="">
           </div> 
            <form id="contact-form" class="form">
               <div id="contact-msg-container" style="text-align: center; margin-bottom: 2rem; font-size: 1.6rem;"></div>
               <div class="inputBox">
                  <input type="text" name="name" id="contact-name" placeholder="name" required>
                  <input type="email" name="email" id="contact-email" placeholder="email" required>
               </div>
               <div class="inputBox">
                  <input type="text" name="phone" id="contact-phone" placeholder="number">
                  <input type="text" name="subject" id="contact-subject" placeholder="subject">
               </div>
               <textarea name="message" id="contact-message" placeholder="message" cols="30" rows="10" required></textarea>
               <button type="submit" class="btn" id="contact-submit-btn">send a message</button>
        </div>

        </section>
        
        <!-- contact section ends -->
         <!--footer section starts -->
         <section class="footer">
            <div class="box-container">
               <div class="box">
                  <h3>about us</h3>
                  <p>this your best choice for a travel agency as far as im concerned</p>

               </div>
               <div class="box">
                  <h3>contact info</h3>
                  <a href="tel:<?= htmlspecialchars($agency_phone) ?>"><i class="fas fa-phone"></i> <?= htmlspecialchars($agency_phone) ?></a>
                  <a href="mailto:<?= htmlspecialchars($agency_email) ?>"><i class="fas fa-envelope"></i> <?= htmlspecialchars($agency_email) ?></a>
                  <a href="#"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($agency_address) ?></a>
               </div>
               <div class="box">
                  <h3>quick links</h3>
                  <a href="">home</a>
                  <a href="">book</a>
                  <a href="">packages</a>
                  <a href="">services</a>
                  
               </div>
            </div>
            <h1 class="credit">created by <span>molka dhahri</span>| all rights reserved</h1>
         </section>
         <!--footer section ends -->
        



      <!--js files-->
      <script src="js/script.js"></script>
      <script src="js/contact.js"></script>
   </body>
</html>