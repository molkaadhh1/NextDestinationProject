<?php
require_once '../includes/session.php';
require_once '../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    header("Location: ../index.php");
    exit;
}

$pdo = getDB();

// Fetch Package Details
$stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
$stmt->execute([$id]);
$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    header("Location: ../index.php");
    exit;
}

$imagePaths = json_decode($package['image'], true);
if (empty($imagePaths) || !is_array($imagePaths)) {
    $imagePaths = [$package['image'] ? $package['image'] : 'images/tunisia_oasi_di_montagna_foto_i._fornasiero.jpg'];
}
$starsHtml = '';
for ($i = 0; $i < $package['stars']; $i++) {
    $starsHtml .= '<i class="fas fa-star"></i>';
}
$duration = $package['duration'] ?: 'N/A';
$groupSize = $package['group_size'] ?: 'N/A';
$language = $package['language'] ?: 'N/A';
$difficulty = $package['difficulty'] ?: 'N/A';

$programmeData = json_decode($package['programme'], true) ?: [];
$programmeTagsMap = [
  'hebergement' => ['label' => 'Hébergement', 'icon' => 'fas fa-bed'],
  'petitdej' => ['label' => 'Petit Déj.', 'icon' => 'fas fa-coffee'],
  'dejeuner' => ['label' => 'Déjeuner', 'icon' => 'fas fa-utensils'],
  'diner' => ['label' => 'Dîner', 'icon' => 'fas fa-hamburger'],
  'transport' => ['label' => 'Transport', 'icon' => 'fas fa-bus'],
  'vol' => ['label' => 'Vol', 'icon' => 'fas fa-plane'],
  'guide' => ['label' => 'Guide', 'icon' => 'fas fa-user-tie'],
  'activite' => ['label' => 'Activité', 'icon' => 'fas fa-hiking'],
  'dromadaire' => ['label' => 'Dromadaire', 'icon' => 'fas fa-horse'],
  'quad' => ['label' => 'Quad', 'icon' => 'fas fa-motorcycle'],
  'feucamp' => ['label' => 'Feu de Camp', 'icon' => 'fas fa-fire']
];

$inclusLines = array_filter(array_map('trim', explode("\n", $package['inclus'] ?? '')));
$exclusLines = array_filter(array_map('trim', explode("\n", $package['exclus'] ?? '')));

// Fetch approved reviews for this package
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE package_id = ? AND status = 'approved' ORDER BY created_at DESC");
$revStmt->execute([$id]);
$approvedReviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= htmlspecialchars($package['name']) ?> – Next Destination</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="../css/package-detail.css">
    <link rel="stylesheet" href="../css/hotel-detail.css">
     <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- ════════════════════════════
     HEADER
════════════════════════════ -->
<header>
   <a href="../index.php" class="back-btn">
      <i class="fas fa-arrow-left"></i> 
   </a>
   <a href="../index.php" class="logo">
      <span>N</span>ext <span>D</span>estination
   </a>
  
</header>


<!-- ════════════════════════════
     CONTENU PRINCIPAL
════════════════════════════ -->
<div class="package-page">

   <!-- ─────────────────────────
        COLONNE GAUCHE
   ───────────────────────── -->
   <div class="package-left">

      <!-- Onglets -->
      <div class="tabs">
         <button class="tab-btn active" onclick="showTab('description', this)">
            <i class="fas fa-info-circle"></i> Description
         </button>
         <button class="tab-btn" onclick="showTab('programme', this)">
            <i class="fas fa-calendar-check"></i> Programme
         </button>
         <button class="tab-btn" onclick="showTab('inclus', this)">
            <i class="fas fa-check-circle"></i> Inclus / Exclus
         </button>
         <button class="tab-btn" onclick="showTab('avis', this)">
            <i class="fas fa-star"></i> Avis
         </button>
      </div>

      <!-- ── Onglet Description ── -->
      <div class="tab-content active" id="tab-description">
         <h2>À propos de ce package</h2>
         <p><?= nl2br(htmlspecialchars($package['description'] ?: 'Aucune description disponible pour ce package.')) ?></p>

         <!-- Highlights -->
         <div class="highlights">
            <div class="highlight-item">
               <i class="fas fa-clock"></i>
               <div>
                  <strong>Durée</strong>
                  <span><?= htmlspecialchars($duration) ?></span>
               </div>
            </div>
            <div class="highlight-item">
               <i class="fas fa-users"></i>
               <div>
                  <strong>Groupe</strong>
                  <span><?= htmlspecialchars($groupSize) ?></span>
               </div>
            </div>
            <div class="highlight-item">
               <i class="fas fa-language"></i>
               <div>
                  <strong>Langue</strong>
                  <span><?= htmlspecialchars($language) ?></span>
               </div>
            </div>
            <div class="highlight-item">
               <i class="fas fa-star"></i>
               <div>
                  <strong>Difficulté</strong>
                  <span><?= htmlspecialchars($difficulty) ?></span>
               </div>
            </div>
         </div>
      </div>

      <!-- ── Onglet Programme ── -->
      <div class="tab-content" id="tab-programme">
         <h2>Programme jour par jour</h2>

         <?php if(empty($programmeData)): ?>
            <p>Le programme détaillé n'est pas encore disponible pour ce package.</p>
         <?php else: ?>
            <?php foreach($programmeData as $index => $day): ?>
            <div class="day-card">
               <div class="day-number">Jour <?= $index + 1 ?></div>
               <div class="day-content">
                  <h3><?= htmlspecialchars($day['title'] ?? '') ?></h3>
                  <p><?= nl2br(htmlspecialchars($day['desc'] ?? '')) ?></p>
                  <?php if(!empty($day['tags'])): ?>
                  <div class="day-activities">
                     <?php foreach($day['tags'] as $tagId): ?>
                        <?php if(isset($programmeTagsMap[$tagId])): ?>
                           <span><i class="<?= $programmeTagsMap[$tagId]['icon'] ?>"></i> <?= $programmeTagsMap[$tagId]['label'] ?></span>
                        <?php endif; ?>
                     <?php endforeach; ?>
                  </div>
                  <?php endif; ?>
               </div>
            </div>
            <?php endforeach; ?>
         <?php endif; ?>

      </div><!-- fin tab-programme -->

      <!-- ── Onglet Inclus / Exclus ── -->
      <div class="tab-content" id="tab-inclus">
         <div class="inclus-grid">

            <div class="inclus-box">
               <h2><i class="fas fa-check-circle"></i> Inclus</h2>
               <?php if(empty($inclusLines)): ?>
                  <p style="color:var(--mid);font-size:1.4rem;">Aucune information.</p>
               <?php else: ?>
               <ul>
                  <?php foreach($inclusLines as $line): ?>
                     <li><i class="fas fa-check"></i> <?= htmlspecialchars($line) ?></li>
                  <?php endforeach; ?>
               </ul>
               <?php endif; ?>
            </div>

            <div class="exclus-box">
               <h2><i class="fas fa-times-circle"></i> Non inclus</h2>
               <?php if(empty($exclusLines)): ?>
                  <p style="color:var(--mid);font-size:1.4rem;">Aucune information.</p>
               <?php else: ?>
               <ul>
                  <?php foreach($exclusLines as $line): ?>
                     <li><i class="fas fa-times"></i> <?= htmlspecialchars($line) ?></li>
                  <?php endforeach; ?>
               </ul>
               <?php endif; ?>
            </div>

         </div>
      </div>

      <!-- ── Onglet Avis ── -->
      <div class="tab-content" id="tab-avis">
         
         <!-- Affichage des avis -->
         <div class="reviews-list">
            <?php if(empty($approvedReviews)): ?>
                <p>No reviews yet. Be the first to share your experience!</p>
            <?php else: ?>
                <?php foreach($approvedReviews as $rev): ?>
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
                   <input type="hidden" name="package_id" value="<?= $id ?>">
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

      </div><!-- fin tab-avis -->
      <div class="slideshow-wrapper">

       <!-- Slides -->
         <?php foreach($imagePaths as $index => $img): ?>
         <div class="slide <?= $index === 0 ? 'active' : '' ?>">
            <img src="<?= '../' . htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($package['name']) ?>">
         </div>
         <?php endforeach; ?>

         <?php if(count($imagePaths) > 1): ?>
         <!-- Flèches navigation -->
         <button class="slide-arrow slide-prev" onclick="changeSlide(-1)">
            <i class="fas fa-chevron-left"></i>
         </button>
         <button class="slide-arrow slide-next" onclick="changeSlide(1)">
            <i class="fas fa-chevron-right"></i>
         </button>
         <?php endif; ?>

         <!-- Compteur -->
         <div class="slide-counter" <?= count($imagePaths) <= 1 ? 'style="display:none"' : '' ?>>
            <span id="slide-current">1</span> / <span id="slide-total"><?= count($imagePaths) ?></span>
         </div>

      <!-- Nom du package par-dessus l'image -->
      <div class="slide-overlay-info">
         <h1><?= htmlspecialchars($package['name']) ?></h1>
         <div class="slide-stars">
            <?= $starsHtml ?>
         </div>
         <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($package['location']) ?></p>
      </div>

   </div>

   </div><!-- fin package-left -->

   <!-- ─────────────────────────
        COLONNE DROITE : RÉSERVATION
   ───────────────────────── -->
   <div class="package-right">
      <div class="booking-card">

         <h2>Réserver ce package</h2>
         <p class="base-price">
            À partir de <strong id="basePriceElement" data-price="<?= $package['price'] ?>"><?= number_format($package['price'], 0, '', ' ') ?> TND</strong> / pers
            <?php if ($package['old_price'] && $package['old_price'] > 0): ?>
               <span class="old-price"><?= number_format($package['old_price'], 0, '', ' ') ?> TND</span>
            <?php endif; ?>
         </p>

         <!-- Date de départ -->
         <label><i class="fas fa-calendar-alt"></i> Date de départ</label>
         <input type="date" id="depart" onchange="calcPackagePrice()">

         <!-- Nombre de personnes -->
         <div class="row-2">
            <div>
               <label><i class="fas fa-users"></i> Adultes</label>
               <select id="pkg-adults" onchange="calcPackagePrice()">
                  <option value="1">1 Adulte</option>
                  <option value="2" selected>2 Adultes</option>
                  <option value="3">3 Adultes</option>
                  <option value="4">4 Adultes</option>
                  <option value="5">5 Adultes</option>
               </select>
            </div>
            <div>
               <label><i class="fas fa-child"></i> Enfants</label>
               <select id="pkg-children" onchange="calcPackagePrice()">
                  <option value="0" selected>0 Enfant</option>
                  <option value="1">1 Enfant</option>
                  <option value="2">2 Enfants</option>
                  <option value="3">3 Enfants</option>
               </select>
            </div>
         </div>

         <!-- Type de chambre -->
         <label><i class="fas fa-bed"></i> Type de chambre</label>
         <select id="pkg-room" onchange="calcPackagePrice()">
            <option value="0">Chambre Standard (+0 TND)</option>
            <option value="200">Chambre Supérieure (+200 TND/pers)</option>
            <option value="500">Suite (+500 TND/pers)</option>
         </select>

         <!-- Options supplémentaires -->
         <label><i class="fas fa-plus-circle"></i> Options supplémentaires</label>
         <div class="options-list">
            <label class="option-item">
               <input type="checkbox" id="opt-quad" onchange="calcPackagePrice()">
               <span class="option-label">Safari Quad supplémentaire (2h)</span>
               <span class="option-price">+150 TND/pers</span>
            </label>
            <label class="option-item">
               <input type="checkbox" id="opt-photo" onchange="calcPackagePrice()">
               <span class="option-label">Séance photo professionnelle</span>
               <span class="option-price">+100 TND</span>
            </label>
            <label class="option-item">
               <input type="checkbox" id="opt-transfer" onchange="calcPackagePrice()">
               <span class="option-label">Transfert privé (voiture)</span>
               <span class="option-price">+300 TND</span>
            </label>
         </div>

         <!-- Résumé des prix -->
         <div class="price-summary">
            <div class="price-row">
               <span>Prix package (adultes)</span>
               <span id="p-adults">– TND</span>
            </div>
            <div class="price-row">
               <span>Enfants (–30%)</span>
               <span id="p-children">0 TND</span>
            </div>
            <div class="price-row">
               <span>Supplément chambre</span>
               <span id="p-room-sup">0 TND</span>
            </div>
            <div class="price-row">
               <span>Options</span>
               <span id="p-options">0 TND</span>
            </div>
            <div class="price-row discount">
               <span>Réduction –9%</span>
               <span id="p-discount">– TND</span>
            </div>
            <div class="price-row total">
               <span>Total</span>
               <span id="p-total">– TND</span>
            </div>
         </div>

         <button class="book-btn" onclick="confirmPackage()">
            <i class="fas fa-check-circle"></i> Réserver maintenant
         </button>

         <!-- Badge garantie -->
         <div class="guarantee">
            <i class="fas fa-shield-alt"></i>
            <span>Annulation gratuite jusqu'à 7 jours avant le départ</span>
         </div>

      </div>
   </div><!-- fin package-right -->

</div><!-- fin package-page -->

<script>
    const IS_LOGGED_IN = <?= isLoggedIn() ? 'true' : 'false' ?>;
    const PACKAGE_ID = <?= $package['id'] ?>;
</script>
<script src="../js/package-detail.js"></script>
</body>
</html>
