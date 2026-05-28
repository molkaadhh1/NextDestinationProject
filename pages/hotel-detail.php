<?php
require_once '../config/db.php';
require_once '../includes/session.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: ../index.php');
    exit;
}
$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) {
    header('Location: ../index.php');
    exit;
}

$imagePaths = [];
if (!empty($hotel['image'])) {
    $decoded = json_decode($hotel['image'], true);
    if (is_array($decoded)) {
        foreach ($decoded as $img) {
            $imagePaths[] = '../' . $img;
        }
    } else {
        $imagePaths[] = '../' . $hotel['image'];
    }
}
if (empty($imagePaths)) {
    $imagePaths[] = '../images/Nour_Palace_Resort_et_Thalasso_.jpg';
}
$starsHtml = str_repeat('<i class="fas fa-star"></i>', $hotel['stars']);
$promoBadge = $hotel['promo'] > 0 ? '<span class="badge-promo">-' . $hotel['promo'] . '% promo</span>' : '';
$discountLabel = $hotel['promo'] > 0 ? "Réduction -{$hotel['promo']}%" : "Réduction";

// Fetch services
$stmtSrv = $pdo->prepare("SELECT * FROM hotel_services WHERE hotel_id = ?");
$stmtSrv->execute([$id]);
$services = $stmtSrv->fetchAll();

// Fetch approved reviews
$stmtRev = $pdo->prepare("SELECT * FROM reviews WHERE hotel_id = ? AND status = 'approved' ORDER BY created_at DESC");
$stmtRev->execute([$id]);
$reviews = $stmtRev->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Détail Hôtel – Next Destination</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="../css/hotel-detail.css">
   <link rel="stylesheet" href="../css/style.css">
   <link rel="stylesheet" href="../css/package-detail.css">
</head>
<body>

<!-- ═══════════════════════════════
     HEADER
════════════════════════════════ -->
<header>
   <a href="../index.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> 
   </a>
   <a href="../index.php" class="logo">
      <span>N</span>ext <span>D</span>estination
   </a>
   
</header>




<!-- ═══════════════════════════════
     LAYOUT PRINCIPAL
════════════════════════════════ -->
<div class="hotel-page">

   <!-- ───────────────────────────
        COLONNE GAUCHE
   ──────────────────────────── -->
   <div class="hotel-left">

      <!-- En-tête hôtel -->
      <div class="hotel-header">
         <h1><?= htmlspecialchars($hotel['name']) ?></h1>
         <div class="hotel-meta">
            <div class="stars">
               <?= $starsHtml ?>
            </div>
            <span class="hotel-location">
               <i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($hotel['location']) ?>
            </span>
            <?= $promoBadge ?>
         </div>
      </div>

      <!-- Onglets -->
      <div class="tabs">
         <button class="tab-btn active" onclick="showTab('description', this)">Description</button>
         <button class="tab-btn"        onclick="showTab('services', this)">Services</button>
         <button class="tab-btn"        onclick="showTab('avis', this)">Reviews</button>
      </div>

      <!-- Contenu onglet : Description -->
      <div class="tab-content active" id="tab-description">
         <p style="font-size: 1.6rem; color: #444; line-height: 1.8; margin-top: 0; padding-bottom: 2rem;">
            <?= nl2br(htmlspecialchars($hotel['description'] ?: 'No description available for this hotel.')) ?>
         </p>

         <div class="slideshow-wrapper" style="margin: 0 auto; max-width: 65rem; height: 40rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background: #f8f8f8;">
          <!-- Slides -->
            <div class="slide active" style="width: 100%; height: 100%;">
               <img src="<?= htmlspecialchars($imagePaths[0]) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" style="width: 100%; height: 100%; object-fit: contain; border-radius: 8px;">
            </div>


         </div>
      </div>

      <!-- Contenu onglet : Services -->
      <div class="tab-content" id="tab-services">
         <div class="services-grid">
            <?php if (empty($services)): ?>
                <p>No specific services listed for this hotel.</p>
            <?php else: ?>
                <?php foreach ($services as $srv): ?>
                    <div class="service-item"><i class="<?= htmlspecialchars($srv['icon']) ?>"></i> <?= htmlspecialchars($srv['name']) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
         </div>
      </div>

      <!-- Contenu onglet : Avis -->
      <div class="tab-content" id="tab-avis">
         
         <!-- Affichage des avis -->
         <div class="reviews-list">
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to share your experience!</p>
            <?php else: ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="reviewer">
                           <div class="reviewer-avatar"><?= strtoupper(substr($rev['client_name'], 0, 2)) ?></div>
                           <div>
                              <div class="reviewer-name"><?= htmlspecialchars($rev['client_name']) ?></div>
                              <div class="reviewer-date"><?= date('F Y', strtotime($rev['created_at'])) ?></div>
                           </div>
                           <div class="stars review-stars">
                              <?php 
                                for($i=1; $i<=5; $i++) {
                                    echo $i <= $rev['rating'] ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                              ?>
                           </div>
                        </div>
                        <p><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
         </div>

         <!-- Formulaire d'ajout d'avis -->
         <style>
            .add-review-section { background: #fff; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 3rem; border: 1px solid #eaeaea; }
            .add-review-section h3 { font-size: 2rem; margin-bottom: 2rem; color: #333; display: flex; align-items: center; gap: 0.8rem; }
            .add-review-section h3::before { content: "\f304"; font-family: "Font Awesome 6 Free"; font-weight: 900; color: var(--orange); }
            .review-user-info { display: flex; align-items: center; gap: 1.2rem; margin-bottom: 2rem; padding: 1.2rem; background: var(--gray); border-radius: 8px; border-left: 4px solid var(--orange); }
            .reviewer-avatar-form { width: 4.5rem; height: 4.5rem; background: var(--orange); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: bold; }
            .reviewer-details { display: flex; flex-direction: column; }
            .reviewer-details .label { font-size: 1.2rem; color: #888; }
            .reviewer-details .name { font-size: 1.5rem; font-weight: bold; color: #222; }
            .form-group { margin-bottom: 1.8rem; }
            .form-group label { display: block; font-size: 1.4rem; font-weight: 700; margin-bottom: 0.8rem; color: #444; }
            .form-group textarea { width: 100%; padding: 1.2rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1.4rem; resize: vertical; transition: all 0.3s ease; font-family: inherit; }
            .form-group textarea:focus { border-color: var(--orange); box-shadow: 0 0 0 3px rgba(64, 138, 113, 0.1); }
            .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 0.5rem; }
            .star-rating input { display: none; }
            .star-rating label { font-size: 3rem; color: #ddd; cursor: pointer; transition: color 0.2s; margin: 0; }
            .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label { color: #ffc107; }
            .submit-review-btn { background: var(--orange); color: #fff; padding: 1.2rem 2.5rem; border: none; border-radius: 8px; font-size: 1.5rem; font-weight: bold; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.8rem; }
            .submit-review-btn:hover { background: #2e6b56; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(64, 138, 113, 0.2); }
            .review-disclaimer { font-size: 1.2rem; color: #777; margin-top: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
            .login-prompt { text-align: center; padding: 3rem; background: var(--gray); border-radius: 8px; border: 1px dashed #ccc; }
            .login-prompt i { font-size: 3.5rem; color: #bbb; margin-bottom: 1.5rem; }
            .login-prompt p { font-size: 1.6rem; color: #555; margin-bottom: 2rem; }
            .login-prompt .btn-primary { display: inline-block; background: var(--orange); color: white; padding: 1rem 2.5rem; border-radius: 6px; font-size: 1.4rem; font-weight: bold; transition: background 0.3s; }
            .login-prompt .btn-primary:hover { background: #2e6b56; }
         </style>
         <div class="add-review-section">
            <h3>Leave a Review</h3>
            <?php if (isLoggedIn()): ?>
                <?php $user = currentUser(); ?>
                <form id="add-review-form" action="../pagesbackend/add_review.php" method="POST">
                   <input type="hidden" name="hotel_id" value="<?= $id ?>">
                   <input type="hidden" name="client_name" value="<?= htmlspecialchars($user['name']) ?>">
                   
                   <div class="review-user-info">
                      <div class="reviewer-avatar-form"><?= strtoupper(substr($user['name'], 0, 2)) ?></div>
                      <div class="reviewer-details">
                         <span class="label">Reviewing as</span>
                         <span class="name"><?= htmlspecialchars($user['name']) ?></span>
                      </div>
                   </div>

                   <div class="form-group">
                      <label>Rating *</label>
                      <div class="star-rating">
                         <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                         <input type="radio" id="star4" name="rating" value="4"/><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                         <input type="radio" id="star3" name="rating" value="3"/><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                         <input type="radio" id="star2" name="rating" value="2"/><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                         <input type="radio" id="star1" name="rating" value="1"/><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                      </div>
                   </div>

                   <div class="form-group">
                      <label>Your Experience</label>
                      <textarea name="comment" rows="4" placeholder="Tell us what you liked or disliked about your stay..."></textarea>
                   </div>

                   <button type="submit" class="submit-review-btn">
                      <i class="fas fa-paper-plane"></i> Submit Review
                   </button>
                   <p class="review-disclaimer"><i class="fas fa-info-circle"></i> Your review will be visible once approved by an admin.</p>
                </form>
            <?php else: ?>
                <div class="login-prompt">
                   <i class="fas fa-user-lock"></i>
                   <p>You must be logged in to share your experience with others.</p>
                   <a href="../auth/login.php" class="btn-primary">Log In Now</a>
                </div>
            <?php endif; ?>
         </div>

      </div>

      

   </div><!-- fin hotel-left -->

   <!-- ───────────────────────────
        COLONNE DROITE : RÉSERVATION
   ──────────────────────────── -->
   <div class="hotel-right">
      <div class="booking-card">

         <h2>Réserver cet hôtel</h2>
         <p class="base-price">À partir de <strong><?= $hotel['price'] ?> TND</strong> / nuit / pers</p>

         <!-- Dates -->
         <div class="row-2">
            <div>
               <label><i class="fas fa-calendar-alt"></i> Check-in</label>
               <input type="date" id="checkin"  onchange="calcPrice()">
            </div>
            <div>
               <label><i class="fas fa-calendar-alt"></i> Check-out</label>
               <input type="date" id="checkout" onchange="calcPrice()">
            </div>
         </div>

         <!-- Chambres et adultes -->
         <div class="row-2">
            <div>
               <label><i class="fas fa-door-open"></i> Chambres</label>
               <select id="rooms" onchange="calcPrice()">
                  <option value="1">1 Chambre</option>
                  <option value="2">2 Chambres</option>
                  <option value="3">3 Chambres</option>
               </select>
            </div>
            <div>
               <label><i class="fas fa-users"></i> Adultes</label>
               <select id="adults" onchange="calcPrice()">
                  <option value="1">1 Adulte</option>
                  <option value="2" selected>2 Adultes</option>
                  <option value="3">3 Adultes</option>
                  <option value="4">4 Adultes</option>
               </select>
            </div>
         </div>

         <!-- Type de chambre -->
         <label><i class="fas fa-bed"></i> Type de chambre</label>
         <select id="room-type" onchange="calcPrice()">
            <option value="0">Chambre Standard    (+0 TND)</option>
            <option value="50">Chambre Supérieure  (+50 TND/nuit)</option>
            <option value="100">Suite Junior        (+100 TND/nuit)</option>
            <option value="200">Suite Prestige      (+200 TND/nuit)</option>
         </select>

         <!-- Type de pension -->
         <label><i class="fas fa-utensils"></i> Type de pension</label>
         <div class="meal-options">
            <label class="meal-option selected" onclick="selectMeal(this, 0)">
               <input type="radio" name="meal" value="0" checked>
               <span class="meal-label">Logement seul</span>
               <span class="meal-price">+0 TND</span>
            </label>
            <label class="meal-option" onclick="selectMeal(this, 30)">
               <input type="radio" name="meal" value="30">
               <span class="meal-label">Petit-déjeuner</span>
               <span class="meal-price">+30 TND/pers</span>
            </label>
            <label class="meal-option" onclick="selectMeal(this, 60)">
               <input type="radio" name="meal" value="60">
               <span class="meal-label">Demi-pension</span>
               <span class="meal-price">+60 TND/pers</span>
            </label>
            <label class="meal-option" onclick="selectMeal(this, 100)">
               <input type="radio" name="meal" value="100">
               <span class="meal-label">Pension complète</span>
               <span class="meal-price">+100 TND/pers</span>
            </label>
            <label class="meal-option" onclick="selectMeal(this, 140)">
               <input type="radio" name="meal" value="140">
               <span class="meal-label">All Inclusive</span>
               <span class="meal-price">+140 TND/pers</span>
            </label>
         </div>

         <!-- Résumé des prix -->
         <div class="price-summary">
            <div class="price-row">
               <span>Prix de base</span>
               <span id="p-base">– TND</span>
            </div>
            <div class="price-row">
               <span>Supplément chambre</span>
               <span id="p-room">0 TND</span>
            </div>
            <div class="price-row">
               <span>Pension</span>
               <span id="p-meal">0 TND</span>
            </div>
            <div class="price-row discount">
               <span><?= $discountLabel ?></span>
               <span id="p-discount">– TND</span>
            </div>
            <div class="price-row total">
               <span>Total</span>
               <span id="p-total">– TND</span>
            </div>
         </div>

         <!-- Bouton confirmer -->
         <button class="book-btn" onclick="confirmBook()">
            <i class="fas fa-check-circle"></i> Confirmer la réservation
         </button>

      </div>
   </div><!-- fin hotel-right -->

</div><!-- fin hotel-page -->

<script>
    const IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;
    const HOTEL_ID = <?= $hotel['id'] ?>;
    const BASE_PRICE = <?= $hotel['price'] ?: 0 ?>;
    const PROMO_PERCENT = <?= $hotel['promo'] ?: 0 ?>;
</script>
<script src="../js/hotel-detail.js?v=1.1"></script>

</body>
</html>
