// ══════════════════════════════════════
// VARIABLES GLOBALES
// ══════════════════════════════════════

let PRICE_ADULT = 0; // will be dynamically loaded from DOM
const PRICE_CHILD_PC = 0.70;  // enfant = 70% du prix adulte (–30%)
const DISCOUNT_PC    = 0.09;  // réduction globale 9%

let currentSlide = 0;         // index du slide actif
const slides     = document.querySelectorAll('.slide');
const dots       = document.querySelectorAll('.dot');
const total      = slides.length;
let autoTimer    = null;       // référence au timer automatique


// ══════════════════════════════════════
// SLIDESHOW
// ══════════════════════════════════════

// Met à jour l'affichage du slide
function updateSlide() {
   // Retirer active de tous les slides et dots
   slides.forEach(s => s.classList.remove('active'));
   dots.forEach(d   => d.classList.remove('active'));

   // Activer le slide et le dot courant
   slides[currentSlide].classList.add('active');
   dots[currentSlide].classList.add('active');

   // Mettre à jour le compteur "2 / 5"
   document.getElementById('slide-current').textContent = currentSlide + 1;
   document.getElementById('slide-total').textContent   = total;
}

// Flèche précédent / suivant  (+1 ou -1)
function changeSlide(direction) {
   currentSlide = (currentSlide + direction + total) % total;
   updateSlide();
   resetAutoPlay();  // relancer le timer après un clic manuel
}

// Clic direct sur un dot
function goToSlide(index) {
   currentSlide = index;
   updateSlide();
   resetAutoPlay();
}

// Défilement automatique toutes les 4 secondes
function startAutoPlay() {
   autoTimer = setInterval(function () {
      currentSlide = (currentSlide + 1) % total;
      updateSlide();
   }, 4000);
}

// Remettre à zéro le timer (après clic manuel)
function resetAutoPlay() {
   clearInterval(autoTimer);
   startAutoPlay();
}

// Initialisation du slideshow au chargement
updateSlide();
startAutoPlay();


// ══════════════════════════════════════
// ONGLETS
// ══════════════════════════════════════

function showTab(id, btn) {
   // Cacher tous les contenus
   document.querySelectorAll('.tab-content').forEach(function (tab) {
      tab.classList.remove('active');
   });
   // Désactiver tous les boutons
   document.querySelectorAll('.tab-btn').forEach(function (b) {
      b.classList.remove('active');
   });
   // Afficher l'onglet choisi
   document.getElementById('tab-' + id).classList.add('active');
   btn.classList.add('active');
}


// ══════════════════════════════════════
// CALCUL DU PRIX
// ══════════════════════════════════════

function calcPackagePrice() {
   // Dynamic base price
   const basePriceEl = document.getElementById('basePriceElement');
   if (basePriceEl && basePriceEl.dataset.price) {
       PRICE_ADULT = parseFloat(basePriceEl.dataset.price);
   } else {
       PRICE_ADULT = 0;
   }

   // Récupérer les valeurs
   const adults   = parseInt(document.getElementById('pkg-adults').value);
   const children = parseInt(document.getElementById('pkg-children').value);
   const roomSup  = parseInt(document.getElementById('pkg-room').value);

   // Lire les options cochées
   const optQuad     = document.getElementById('opt-quad').checked;
   const optPhoto    = document.getElementById('opt-photo').checked;
   const optTransfer = document.getElementById('opt-transfer').checked;

   // Calcul de chaque partie
   const priceAdults   = PRICE_ADULT * adults;
   const priceChildren = Math.round(PRICE_ADULT * PRICE_CHILD_PC) * children;
   const priceRoomSup  = roomSup * (adults + children);

   // Options (certaines sont par personne, d'autres fixes)
   let priceOptions = 0;
   if (optQuad)     priceOptions += 150 * (adults + children); // 150 TND/pers
   if (optPhoto)    priceOptions += 100;                       // 100 TND fixe
   if (optTransfer) priceOptions += 300;                       // 300 TND fixe

   // Sous-total avant réduction
   const subtotal = priceAdults + priceChildren + priceRoomSup + priceOptions;

   // Réduction 9%
   const discount = Math.round(subtotal * DISCOUNT_PC);
   const total    = subtotal - discount;

   // Afficher dans le résumé
   document.getElementById('p-adults').textContent   = priceAdults   + ' TND';
   document.getElementById('p-children').textContent = priceChildren  + ' TND';
   document.getElementById('p-room-sup').textContent = priceRoomSup   + ' TND';
   document.getElementById('p-options').textContent  = priceOptions   + ' TND';
   document.getElementById('p-discount').textContent = '-' + discount + ' TND';
   document.getElementById('p-total').textContent    = total          + ' TND';
}

// ══════════════════════════════════════
// CONFIRMATION DE RÉSERVATION
// ══════════════════════════════════════

function confirmPackage() {
   if (!IS_LOGGED_IN) {
      alert('Vous devez être connecté pour réserver. Vous allez être redirigé vers la page de connexion.');
      window.location.href = '../auth/login.php';
      return;
   }

   const depart = document.getElementById('depart').value;

   if (!depart) {
      alert('Veuillez choisir une date de départ.');
      return;
   }

   const totalText = document.getElementById('p-total').textContent;
   if (totalText === '– TND') {
       alert('Veuillez vérifier les options de réservation.');
       return;
   }

   const adults = document.getElementById('pkg-adults').value;
   const children = document.getElementById('pkg-children').value;
   
   const roomSelect = document.getElementById('pkg-room');
   const roomTypeText = roomSelect.options[roomSelect.selectedIndex].text.split(' (+')[0];
   
   const options = [];
   if (document.getElementById('opt-quad').checked) options.push('Safari Quad');
   if (document.getElementById('opt-photo').checked) options.push('Séance photo');
   if (document.getElementById('opt-transfer').checked) options.push('Transfert privé');
   
   const totalPrice = parseFloat(totalText.replace(' TND', ''));

   const fd = new FormData();
   fd.append('package_id', PACKAGE_ID);
   fd.append('depart', depart);
   fd.append('adults', adults);
   fd.append('children', children);
   fd.append('room_type', roomTypeText);
   fd.append('options', JSON.stringify(options));
   fd.append('total_price', totalPrice);

   const btn = document.querySelector('.book-btn');
   const originalHtml = btn.innerHTML;
   btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Réservation...';
   btn.disabled = true;

   fetch('../pagesbackend/book_package.php', {
      method: 'POST',
      body: fd
   })
   .then(res => res.json())
   .then(data => {
      if (data.success) {
         alert('✅ ' + data.message + '\nMontant total : ' + totalText);
      } else {
         alert('Erreur : ' + data.message);
      }
   })
   .catch(err => {
      alert('Erreur de communication avec le serveur.');
      console.error(err);
   })
   .finally(() => {
      btn.innerHTML = originalHtml;
      btn.disabled = false;
   });
}

// ══════════════════════════════════════
// INITIALISATION AU CHARGEMENT
// ══════════════════════════════════════

// Date de départ par défaut = dans 7 jours
const today7 = new Date();
today7.setDate(today7.getDate() + 7);
document.getElementById('depart').value = today7.toISOString().split('T')[0];

// Lancer le calcul initial
calcPackagePrice();
