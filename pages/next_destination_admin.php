<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — Next Destination</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<!-- TOAST -->
<div class="toast" id="toast"><i class="fas fa-check-circle"></i><span id="toastMsg"></span></div>

<!-- MODAL HOTEL -->
<div class="modal-backdrop hidden" id="hotelModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="hotelModalTitle">Ajouter un hôtel</h3>
      <button class="modal-close" onclick="closeModal('hotelModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Nom de l'hôtel *</label>
        <input type="text" id="hName" placeholder="Ex: Nour Palace Resort">
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group">
          <label>Localisation *</label>
          <input type="text" id="hLoc" placeholder="Ex: Hammamet">
        </div>
        <div class="form-group">
          <label>Étoiles *</label>
          <div class="star-select stars-row" id="starRow">
            <button class="star-btn" data-val="1" onclick="setStar(1)">★</button>
            <button class="star-btn" data-val="2" onclick="setStar(2)">★</button>
            <button class="star-btn" data-val="3" onclick="setStar(3)">★</button>
            <button class="star-btn" data-val="4" onclick="setStar(4)">★</button>
            <button class="star-btn active" data-val="5" onclick="setStar(5)">★</button>
          </div>
          <input type="hidden" id="hStars" value="5">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group">
          <label>Prix / nuit (TND) *</label>
          <input type="number" id="hPrice" placeholder="200" min="1">
        </div>
        <div class="form-group">
          <label>Promotion (%)</label>
          <input type="number" id="hPromo" placeholder="0" min="0" max="99" value="0">
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea id="hDesc" rows="3" placeholder="Description de l'hôtel…"
          style="width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:8px;
                 font-size:1.4rem;resize:vertical;font-family:inherit"></textarea>
      </div>
      <div class="form-group">
        <label>Image de l'hôtel</label>
        <div id="hImgPreviewWrap" style="display:none;margin-bottom:.8rem">
          <img id="hImgPreview" src="" alt="aperçu"
               style="width:100%;max-height:160px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
        </div>
        <input type="file" id="hImages" accept="image/jpeg,image/png,image/webp" multiple
               style="width:100%;padding:.6rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.3rem"
               onchange="previewImage(this)">
        <small style="color:var(--mid);font-size:1.2rem">JPG, PNG ou WebP – max 5 Mo</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal('hotelModal')">Annuler</button>
      <button class="btn-primary" id="saveHotelBtn" onclick="saveHotel()">
        <i class="fas fa-save"></i> Enregistrer
      </button>
    </div>
  </div>
</div>

<!-- MODAL PACKAGE -->
<div class="modal-backdrop hidden" id="packageModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="pkgModalTitle">Créer un package</h3>
      <button class="modal-close" onclick="closeModal('packageModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 1rem;">
      <div class="form-group"><label>Nom du package *</label><input type="text" id="pkgName" placeholder="Ex: Circuit Nord Tunisie"></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group"><label>Destination *</label><input type="text" id="pkgLoc" placeholder="Ex: Bizerte"></div>
        <div class="form-group"><label>Durée *</label><input type="text" id="pkgDays" placeholder="Ex: 5 jours / 4 nuits"></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group"><label>Prix (TND) *</label><input type="number" id="pkgPrice" placeholder="2000" min="1"></div>
        <div class="form-group"><label>Ancien prix (TND)</label><input type="number" id="pkgOld" placeholder="2200" min="0"></div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group"><label>Taille du groupe</label><input type="text" id="pkgGroup" placeholder="Ex: 2 – 20 personnes"></div>
        <div class="form-group"><label>Difficulté</label><input type="text" id="pkgDiff" placeholder="Ex: Facile"></div>
      </div>
      <div class="form-group"><label>Langue</label><input type="text" id="pkgLang" placeholder="Ex: Français / Arabe"></div>
      <div class="form-group"><label>Étoiles *</label>
        <div class="star-select stars-row" id="pkgStarRow">
          <button class="star-btn" data-val="1" onclick="setPkgStar(1)">★</button>
          <button class="star-btn" data-val="2" onclick="setPkgStar(2)">★</button>
          <button class="star-btn" data-val="3" onclick="setPkgStar(3)">★</button>
          <button class="star-btn" data-val="4" onclick="setPkgStar(4)">★</button>
          <button class="star-btn active" data-val="5" onclick="setPkgStar(5)">★</button>
        </div>
        <input type="hidden" id="pkgStars" value="5">
      </div>
      <div class="form-group"><label>Description</label><textarea id="pkgDesc" rows="3" placeholder="Description du package..." style="width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.4rem;resize:vertical;font-family:inherit"></textarea></div>
      <div class="form-group">
        <label>Programme (Jour par jour)</label>
        <div id="pkgProgrammeList" style="display:flex;flex-direction:column;gap:1rem;margin-bottom:1rem;"></div>
        <button type="button" class="btn-secondary" style="width:100%; justify-content:center; padding:.8rem" onclick="addProgrammeDay()">
          <i class="fas fa-plus"></i> Ajouter un jour
        </button>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group"><label>Inclus</label><textarea id="pkgInclus" rows="3" placeholder="Ce qui est inclus..." style="width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.4rem;resize:vertical;font-family:inherit"></textarea></div>
        <div class="form-group"><label>Exclus</label><textarea id="pkgExclus" rows="3" placeholder="Ce qui n'est pas inclus..." style="width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.4rem;resize:vertical;font-family:inherit"></textarea></div>
      </div>
      <div class="form-group">
        <label>Image du package</label>
        <div id="pkgImgPreviewWrap" style="display:none;margin-bottom:.8rem">
          <img id="pkgImgPreview" src="" alt="aperçu" style="width:100%;max-height:160px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
        </div>
        <input type="file" id="pkgImages" accept="image/jpeg,image/png,image/webp" multiple style="width:100%;padding:.6rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.3rem" onchange="previewPkgImage(this)">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal('packageModal')">Annuler</button>
      <button class="btn-primary" id="savePkgBtn" onclick="savePackage()"><i class="fas fa-save"></i> Enregistrer</button>
    </div>
  </div>
</div>

<!-- MODAL SLIDER -->
<div class="modal-backdrop hidden" id="sliderModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="sliderModalTitle">Ajouter un Slider</h3>
      <button class="modal-close" onclick="closeModal('sliderModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Titre principal *</label>
        <input type="text" id="sldTitle" placeholder="Ex: adventure is worthwhile">
      </div>
      <div class="form-group">
        <label>Sous-titre</label>
        <textarea id="sldSubtitle" rows="2" placeholder="Ex: discover new places with us..." style="width:100%;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.4rem;resize:vertical;font-family:inherit"></textarea>
      </div>
      <div class="form-group">
        <label>Média (Image ou Vidéo) *</label>
        <input type="file" id="sldMedia" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" style="width:100%;padding:.6rem;border:1.5px solid var(--border);border-radius:8px;font-size:1.3rem">
        <small style="color:var(--mid);font-size:1.2rem">Formats: JPG, PNG, WebP, MP4, WebM</small>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal('sliderModal')">Annuler</button>
      <button class="btn-primary" id="saveSliderBtn" onclick="saveSlider()"><i class="fas fa-save"></i> Enregistrer</button>
    </div>
  </div>
</div>

<!-- MODAL SERVICES -->
<div class="modal-backdrop hidden" id="servicesModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="servicesModalTitle">Hotel Services</h3>
      <button class="modal-close" onclick="closeModal('servicesModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div id="servicesList" style="margin-bottom: 1rem; max-height: 200px; overflow-y: auto;">
        <!-- Services go here -->
      </div>
      <hr style="margin-bottom: 1rem;">
      <h4>Add a service</h4>
      <input type="hidden" id="srvHotelId">
      <div style="margin-bottom:1rem;">
        <select id="srvSelect" style="width: 100%; padding: 0.8rem; border: 1px solid var(--border); border-radius: 6px;">
          <option value="" disabled selected>-- Select a service --</option>
          <option value="Free Wifi" data-icon="fas fa-wifi">Free Wifi</option>
          <option value="Swimming Pools" data-icon="fas fa-swimming-pool">Swimming Pools</option>
          <option value="Spa & Wellness" data-icon="fas fa-spa">Spa & Wellness</option>
          <option value="Gym" data-icon="fas fa-dumbbell">Gym</option>
          <option value="Restaurants" data-icon="fas fa-utensils">Restaurants</option>
          <option value="Bars / Lounge" data-icon="fas fa-glass-martini-alt">Bars / Lounge</option>
          <option value="Free Parking" data-icon="fas fa-parking">Free Parking</option>
          <option value="Private Beach" data-icon="fas fa-umbrella-beach">Private Beach</option>
          <option value="Kids Club" data-icon="fas fa-child">Kids Club</option>
          <option value="Room Service" data-icon="fas fa-concierge-bell">Room Service</option>
          <option value="Airport Shuttle" data-icon="fas fa-shuttle-van">Airport Shuttle</option>
          <option value="Air Conditioning" data-icon="fas fa-wind">Air Conditioning</option>
          <option value="Elevator" data-icon="fas fa-caret-square-up">Elevator</option>
          <option value="Pets Allowed" data-icon="fas fa-paw">Pets Allowed</option>
        </select>
      </div>
      <button class="btn-primary" onclick="addService()">Add Service</button>
    </div>
    <div class="modal-footer">
      <button class="btn-primary" style="background: rgba(49,130,206,.1); color: var(--blue);" onclick="closeModal('servicesModal')">Done</button>
    </div>
  </div>
</div>

<!-- MODAL ROOMS -->
<div class="modal-backdrop hidden" id="roomsModal">
  <div class="modal">
    <div class="modal-header">
      <h3 id="roomsModalTitle">Hotel Rooms (Availability)</h3>
      <button class="modal-close" onclick="closeModal('roomsModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
      <div id="roomsList" style="margin-bottom: 1rem; max-height: 200px; overflow-y: auto;">
        <!-- Rooms go here -->
      </div>
      <hr style="margin-bottom: 1rem;">
      <h4>Add a Room Type</h4>
      <input type="hidden" id="rmHotelId">
      <div class="form-group">
        <label>Room Type *</label>
        <select id="rmType" style="width: 100%; padding: 1rem; border: 1px solid #ddd; border-radius: .5rem; font-size: 1.4rem;">
          <option value="">Sélectionnez un type</option>
          <option value="Single">Single</option>
          <option value="Double">Double</option>
          <option value="Twin">Twin</option>
          <option value="Triple">Triple</option>
          <option value="Quadruple">Quadruple</option>
          <option value="Chambre Familiale">Chambre Familiale</option>
          <option value="Suite">Suite</option>
          <option value="Suite Junior">Suite Junior</option>
          <option value="Bungalow">Bungalow</option>
          <option value="Appartement">Appartement</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group">
          <label>Total Rooms *</label>
          <input type="number" id="rmTotal" placeholder="10" min="1">
        </div>
        <div class="form-group">
          <label>Price Modifier (+/- TND)</label>
          <input type="number" id="rmPriceMod" placeholder="0" value="0">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.4rem">
        <div class="form-group">
          <label>Adult Capacity *</label>
          <input type="number" id="rmAdults" placeholder="2" min="1" value="2">
        </div>
        <div class="form-group">
          <label>Child Capacity *</label>
          <input type="number" id="rmChildren" placeholder="0" min="0" value="0">
        </div>
      </div>
      <button class="btn-primary" onclick="addRoom()" style="margin-top:0.5rem;">Add Room</button>
    </div>
    <div class="modal-footer">
      <button class="btn-primary" style="background: rgba(49,130,206,.1); color: var(--blue);" onclick="closeModal('roomsModal')">Done</button>
    </div>
  </div>
</div>

<!-- CONFIRM DELETE -->
<div class="modal-backdrop hidden" id="confirmModal">
  <div class="confirm-modal">
    <div class="confirm-body">
      <div class="confirm-icon"><i class="fas fa-trash"></i></div>
      <h3>Confirmer la suppression</h3>
      <p id="confirmText">Êtes-vous sûr de vouloir supprimer cet élément ?<br>Cette action est irréversible.</p>
    </div>
    <div class="confirm-footer">
      <button class="btn-cancel-modal" onclick="closeModal('confirmModal')">Annuler</button>
      <button class="btn-danger" onclick="executeDelete()"><i class="fas fa-trash" style="margin-right:.6rem"></i>Supprimer</button>
    </div>
  </div>
</div>

<!-- NOTIFICATIONS PANEL REMOVED -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-logo"><span>N</span>ext <span>D</span>estination<small>Admin Panel</small></div>
  <div class="nav-section">Principal</div>
  <div class="nav-item active" onclick="showPage('dashboard',this)"><i class="fas fa-chart-pie"></i><span>Tableau de bord</span></div>
  <div class="nav-item" onclick="showPage('reservations',this)"><i class="fas fa-calendar-alt"></i><span>Réservations</span></div>
  <div class="nav-item" onclick="showPage('clients',this)"><i class="fas fa-users"></i><span>Clients</span></div>
  <div class="nav-item" onclick="showPage('messages',this)"><i class="fas fa-envelope"></i><span>Messages</span></div>
  <div class="nav-section">Catalogue</div>
  <div class="nav-item" onclick="showPage('hotels',this)"><i class="fas fa-hotel"></i><span>Hôtels</span></div>
  <div class="nav-item" onclick="showPage('packages',this)"><i class="fas fa-suitcase"></i><span>Packages</span></div>
  <div class="nav-item" onclick="showPage('sliders',this)"><i class="fas fa-images"></i><span>Sliders</span></div>
  <div class="nav-item" onclick="showPage('avis',this)"><i class="fas fa-star"></i><span>Avis</span></div>
  <div class="nav-section">Système</div>
  <div class="nav-item" onclick="showPage('settings',this)"><i class="fas fa-cog"></i><span>Paramètres</span></div>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="avatar">A</div>
      <div class="info"><strong>Admin</strong></div>
      <i class="fas fa-sign-out-alt logout" title="Déconnexion"></i>
    </div>
  </div>
</aside>

<!-- MAIN -->
<div class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:1.4rem">
      <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
      <span class="topbar-title" id="topbarTitle">Tableau de bord</span>
    </div>
    <div class="topbar-actions">
      <span class="topbar-date" id="todayDate"></span>
    </div>
  </header>

  <div class="content">

    <!-- DASHBOARD -->
    <div class="page active" id="page-dashboard">
      <div class="stats-grid">
        <div class="stat-card"><div class="icon green"><i class="fas fa-calendar-check"></i></div><div class="val" id="stat-res">...</div><div class="label">Réservations</div><span class="trend up"></span><div class="mini-bar"><div class="mini-bar-fill" style="width:100%"></div></div></div>
        <div class="stat-card"><div class="icon amber"><i class="fas fa-coins"></i></div><div class="val" id="stat-rev">...</div><div class="label">Revenus TND</div><span class="trend up"></span><div class="mini-bar"><div class="mini-bar-fill" style="width:100%;background:var(--amber)"></div></div></div>
        <div class="stat-card"><div class="icon blue"><i class="fas fa-users"></i></div><div class="val" id="stat-clients">...</div><div class="label">Clients actifs</div><span class="trend up"></span><div class="mini-bar"><div class="mini-bar-fill" style="width:100%;background:var(--blue)"></div></div></div>
        <div class="stat-card"><div class="icon red"><i class="fas fa-times-circle"></i></div><div class="val" id="stat-canc">...</div><div class="label">Annulations</div><span class="trend down"></span><div class="mini-bar"><div class="mini-bar-fill" style="width:100%;background:var(--red)"></div></div></div>
      </div>
      <div class="card" style="margin-top:2rem">
        <div class="card-header"><span class="card-title">Dernières réservations</span><a class="card-action" onclick="showPage('reservations',null)">Voir tout →</a></div>
        <div class="table-wrap"><table><thead><tr><th>#</th><th>Client</th><th>Destination</th><th>Dates</th><th>Montant</th><th>Statut</th></tr></thead><tbody id="recentBookings"></tbody></table></div>
      </div>
    </div>

    <!-- RESERVATIONS -->
    <div class="page" id="page-reservations">
      <div class="page-header"><div><h2>Réservations</h2><p>Gérez toutes les réservations de vos clients</p></div><button class="btn-primary"><i class="fas fa-plus"></i> Nouvelle réservation</button></div>
      <div class="toolbar">
        <div class="search-input"><i class="fas fa-search"></i><input type="text" placeholder="Rechercher une réservation…"></div>
        <select class="filter-select" onchange="filterTable(this.value)"><option value="all">Tous les statuts</option><option value="confirmed">Confirmées</option><option value="pending">En attente</option><option value="cancelled">Annulées</option><option value="completed">Terminées</option></select>
        <button class="btn-secondary"><i class="fas fa-download"></i> Exporter</button>
      </div>
      <div class="full-card"><div class="table-wrap"><table><thead><tr><th>#</th><th>Client</th><th>Destination</th><th>Check-in</th><th>Check-out</th><th>Pers.</th><th>Montant</th><th>Statut</th><th>Actions</th></tr></thead><tbody id="allBookings"></tbody></table></div></div>
    </div>

    <!-- CLIENTS -->
    <div class="page" id="page-clients">
      <div class="page-header"><div><h2>Clients</h2><p>Base de données de vos clients</p></div><button class="btn-primary"><i class="fas fa-user-plus"></i> Ajouter client</button></div>
      <div class="toolbar"><div class="search-input"><i class="fas fa-search"></i><input type="text" placeholder="Rechercher un client…"></div><button class="btn-secondary"><i class="fas fa-download"></i> Exporter CSV</button></div>
      <div class="full-card"><div class="table-wrap"><table><thead><tr><th>Client</th><th>Email</th><th>Téléphone</th><th>Réservations</th><th>Total dépensé</th><th>Inscrit le</th><th>Actions</th></tr></thead><tbody id="clientsTable"></tbody></table></div></div>
    </div>

    <!-- MESSAGES -->
    <div class="page" id="page-messages">
      <div class="page-header">
        <div><h2>Messages de Contact</h2><p>Lisez les messages envoyés depuis le site</p></div>
        <button class="btn-primary" onclick="loadMessages()"><i class="fas fa-sync"></i> Actualiser</button>
      </div>
      <div class="full-card">
        <div class="table-wrap">
          <table>
            <thead><tr><th>ID</th><th>Client</th><th>Sujet</th><th>Message</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody id="messagesTable"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- HOTELS -->
    <div class="page" id="page-hotels">
      <div class="page-header">
        <div><h2>Hôtels</h2><p id="hotelCount"></p></div>
        <button class="btn-primary" onclick="openAddHotel()"><i class="fas fa-plus"></i> Ajouter hôtel</button>
      </div>
      <div class="items-grid" id="hotelsGrid"></div>
      <div id="hotelsEmpty" style="display:none;text-align:center;padding:6rem 2rem;color:var(--mid)">
        <i class="fas fa-hotel" style="font-size:4rem;opacity:.3;display:block;margin-bottom:1.6rem"></i>
        <p style="font-size:1.6rem">Aucun hôtel dans le catalogue.<br>Commencez par en ajouter un !</p>
      </div>
    </div>

    <!-- PACKAGES -->
    <div class="page" id="page-packages">
      <div class="page-header">
        <div><h2>Packages</h2><p id="pkgCount"></p></div>
        <button class="btn-primary" onclick="openAddPackage()"><i class="fas fa-plus"></i> Créer package</button>
      </div>
      <div class="items-grid" id="packagesGrid"></div>
      <div id="packagesEmpty" style="display:none;text-align:center;padding:6rem 2rem;color:var(--mid)">
        <i class="fas fa-suitcase-rolling" style="font-size:4rem;opacity:.3;display:block;margin-bottom:1.6rem"></i>
        <p style="font-size:1.6rem">Aucun package disponible.<br>Créez votre première offre !</p>
      </div>
    </div>

    <!-- SLIDERS -->
    <div class="page" id="page-sliders">
      <div class="page-header">
        <div><h2>Home Sliders</h2><p id="sliderCount"></p></div>
        <button class="btn-primary" onclick="openAddSlider()"><i class="fas fa-plus"></i> Ajouter Slider</button>
      </div>
      <div class="items-grid" id="slidersGrid"></div>
      <div id="slidersEmpty" style="display:none;text-align:center;padding:6rem 2rem;color:var(--mid)">
        <i class="fas fa-images" style="font-size:4rem;opacity:.3;display:block;margin-bottom:1.6rem"></i>
        <p style="font-size:1.6rem">Aucun slider configuré.<br>La vidéo de fallback par défaut sera affichée.</p>
      </div>
    </div>

    <!-- REVIEWS -->
    <div class="page" id="page-avis">
      <div class="page-header">
        <div><h2>Client Reviews</h2><p>Manage pending reviews</p></div>
      </div>
      <div class="full-card">
        <div class="table-wrap">
          <table>
            <thead><tr><th>Hôtel / Package</th><th>Client</th><th>Rating</th><th>Comment</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="reviewsTable"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- SETTINGS -->
    <div class="page" id="page-settings">
      <div class="page-header"><div><h2>Paramètres</h2><p>Configuration de l'agence</p></div><button class="btn-primary" onclick="saveAgencySettings()"><i class="fas fa-save"></i> Sauvegarder</button></div>
      <div class="settings-grid">
        <div class="card">
          <div class="card-header"><span class="card-title">Informations agence</span></div>
          <div class="form-group"><label>Nom de l'agence</label><input type="text" id="ag-name" value="Next Destination"></div>
          <div class="form-group"><label>Email de contact</label><input type="email" id="ag-email" value="contact@nextdestination.tn"></div>
          <div class="form-group"><label>Téléphone</label><input type="tel" id="ag-phone" value="+216 71 000 000"></div>
          <div class="form-group"><label>Adresse</label><textarea id="ag-address" rows="2">Lafayette, Tunis, Tunisie</textarea></div>
          <div class="form-group"><label>Devise</label><select><option selected>TND – Dinar Tunisien</option><option>EUR – Euro</option><option>USD – Dollar</option></select></div>
        </div>
        <div class="card">

          <div class="card-header" style="margin-top:2.4rem"><span class="card-title">Sécurité</span></div>
          <div class="form-group"><label>Mot de passe actuel</label><input type="password" id="sec-current" placeholder="••••••••"></div>
          <div class="form-group"><label>Nouveau mot de passe</label><input type="password" id="sec-new" placeholder="••••••••"></div>
          <button class="btn-primary" onclick="changeAdminPassword()" style="width:100%;justify-content:center;margin-top:.4rem">Changer le mot de passe</button>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="../js/admin.js"></script>
</body>
</html>

