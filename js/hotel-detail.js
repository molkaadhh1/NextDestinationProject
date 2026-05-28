// ═══════════════════════════════════════
// VARIABLES GLOBALES
// ═══════════════════════════════════════

// BASE_PRICE et PROMO_PERCENT sont maintenant définis dynamiquement dans hotel-detail.php
let mealExtra = 0;      // supplément pension choisi (mis à jour par selectMeal)


// ═══════════════════════════════════════
// FONCTION : CHANGER D'ONGLET
// ═══════════════════════════════════════

function showTab(id, btn) {
   // 1. Cacher tous les contenus d'onglets
   document.querySelectorAll('.tab-content').forEach(function(tab) {
      tab.classList.remove('active');
   });

   // 2. Enlever la classe active de tous les boutons
   document.querySelectorAll('.tab-btn').forEach(function(b) {
      b.classList.remove('active');
   });

   // 3. Afficher le contenu de l'onglet choisi
   document.getElementById('tab-' + id).classList.add('active');

   // 4. Mettre en surbrillance le bouton cliqué
   btn.classList.add('active');
}


// ═══════════════════════════════════════
// FONCTION : SÉLECTIONNER UNE PENSION
// ═══════════════════════════════════════

function selectMeal(el, price) {
   // 1. Retirer la sélection de toutes les options
   document.querySelectorAll('.meal-option').forEach(function(m) {
      m.classList.remove('selected');
   });

   // 2. Marquer l'option cliquée comme sélectionnée
   el.classList.add('selected');

   // 3. Mettre à jour le supplément pension global
   mealExtra = price;

   // 4. Recalculer le prix total
   calcPrice();
}


// ═══════════════════════════════════════
// FONCTION : CALCULER LE PRIX TOTAL
// ═══════════════════════════════════════

function calcPrice() {
   // Récupérer les valeurs saisies
   const checkin   = new Date(document.getElementById('checkin').value);
   const checkout  = new Date(document.getElementById('checkout').value);
   const rooms     = parseInt(document.getElementById('rooms').value);
   const adults    = parseInt(document.getElementById('adults').value);
   const roomExtra = parseInt(document.getElementById('room-type').value);

   // Si les dates sont invalides ou checkout avant checkin → afficher tirets
   if (!checkin || !checkout || isNaN(checkin) || isNaN(checkout) || checkout <= checkin) {
      document.getElementById('p-base').textContent     = '– TND';
      document.getElementById('p-room').textContent     = '– TND';
      document.getElementById('p-meal').textContent     = '– TND';
      document.getElementById('p-discount').textContent = '– TND';
      document.getElementById('p-total').textContent    = '– TND';
      return;
   }

   // Calculer le nombre de nuits
   const nights = Math.round((checkout - checkin) / (1000 * 60 * 60 * 24));

   // Calculer chaque composante du prix
   const base     = BASE_PRICE * adults * nights * rooms;  // prix de base
   const roomSup  = roomExtra  * nights * rooms;           // supplément type chambre
   const mealSup  = mealExtra  * adults * nights;          // supplément pension
   const subtotal = base + roomSup + mealSup;              // sous-total avant réduction
   const discount = Math.round(subtotal * (PROMO_PERCENT / 100)); // réduction dynamique
   const total    = subtotal - discount;                   // prix final

   // Afficher les résultats dans le résumé
   document.getElementById('p-base').textContent =
      base + ' TND (' + nights + ' nuit' + (nights > 1 ? 's' : '') + ')';

   document.getElementById('p-room').textContent     = roomSup  + ' TND';
   document.getElementById('p-meal').textContent     = mealSup  + ' TND';
   document.getElementById('p-discount').textContent = '-' + discount + ' TND';
   document.getElementById('p-total').textContent    = total     + ' TND';
}


// ═══════════════════════════════════════
// FONCTION : CONFIRMER LA RÉSERVATION
// ═══════════════════════════════════════

function confirmBook() {
   if (!IS_LOGGED_IN) {
      alert('Vous devez être connecté pour réserver. Vous allez être redirigé vers la page de connexion.');
      window.location.href = '../auth/login.php';
      return;
   }

   const totalText = document.getElementById('p-total').textContent;

   // Vérifier que le prix a bien été calculé
   if (totalText === '– TND') {
      alert('Veuillez sélectionner vos dates de séjour.');
      return;
   }

   const checkin = document.getElementById('checkin').value;
   const checkout = document.getElementById('checkout').value;
   const rooms = document.getElementById('rooms').value;
   const adults = document.getElementById('adults').value;
   
   const roomSelect = document.getElementById('room-type');
   const roomTypeText = roomSelect.options[roomSelect.selectedIndex].text.split(' (')[0];
   
   const mealSelected = document.querySelector('input[name="meal"]:checked');
   const mealLabel = mealSelected ? mealSelected.nextElementSibling.textContent : 'Logement seul';
   
   const totalPrice = parseFloat(totalText.replace(' TND', ''));

   const fd = new FormData();
   fd.append('hotel_id', HOTEL_ID);
   fd.append('checkin', checkin);
   fd.append('checkout', checkout);
   fd.append('rooms', rooms);
   fd.append('adults', adults);
   fd.append('room_type', roomTypeText);
   fd.append('meal_type', mealLabel);
   fd.append('total_price', totalPrice);

   const btn = document.querySelector('.book-btn');
   const originalHtml = btn.innerHTML;
   btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Réservation...';
   btn.disabled = true;

   fetch('../pagesbackend/book_hotel.php', {
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


// ═══════════════════════════════════════
// INITIALISATION AU CHARGEMENT DE LA PAGE
// ═══════════════════════════════════════

// Définir des dates par défaut : aujourd'hui et dans 3 jours
const today = new Date();
const d2    = new Date();
d2.setDate(today.getDate() + 3);

document.getElementById('checkin').value  = today.toISOString().split('T')[0];
document.getElementById('checkout').value = d2.toISOString().split('T')[0];

// Lancer le calcul initial
calcPrice();
// ✅ Déclarations correctes
let currentSlide  = 0;
const slides      = document.querySelectorAll('.slide');
const dots        = document.querySelectorAll('.dot'); // si tu as les dots
const slidesTotal = slides.length; // ← plus de conflit avec "total"
let autoTimer     = null;

function updateSlide() {
   slides.forEach(s => s.classList.remove('active'));
   dots.forEach(d   => d.classList.remove('active'));
   slides[currentSlide].classList.add('active');
   if (dots[currentSlide]) dots[currentSlide].classList.add('active');
   document.getElementById('slide-current').textContent = currentSlide + 1;
   document.getElementById('slide-total').textContent   = slidesTotal;
}

function changeSlide(direction) {
   currentSlide = (currentSlide + direction + slidesTotal) % slidesTotal;
   updateSlide();
   resetAutoPlay();
}

function goToSlide(index) {
   currentSlide = index;
   updateSlide();
   resetAutoPlay();
}

function startAutoPlay() {
   autoTimer = setInterval(function () {
      currentSlide = (currentSlide + 1) % slidesTotal;
      updateSlide();
   }, 4000);
}

function resetAutoPlay() {
   clearInterval(autoTimer);
   startAutoPlay();
}

updateSlide();
startAutoPlay();