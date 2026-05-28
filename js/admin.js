/* ════════════════════════════════════════════
   DONNÉES STATIQUES (réservations & clients)
════════════════════════════════════════════ */
let bookings = [];
async function loadBookings() {
  try {
    const res = await fetch('../pagesbackend/get_reservations_admin.php');
    const data = await res.json();
    if (data.success) {
      bookings = data.data.map(b => ({
         id: '#' + b.formatted_id,
         client: b.client,
         dest: b.dest,
         cin: b.cin,
         cout: b.cout,
         pers: b.pers,
         amount: b.amount,
         status: b.status,
         type: b.type
      }));
      renderRecent();
      renderAll();
    }
  } catch (err) {
    console.error('Erreur chargement reservations', err);
  }
}
// Appeler loadBookings() au chargement
document.addEventListener('DOMContentLoaded', () => {
    loadBookings();
    loadMessages();
    if(typeof loadReviews === 'function') loadReviews();
});

let clients = [];
async function loadClients() {
  try {
    const res = await fetch('../pagesbackend/get_clients_admin.php');
    const data = await res.json();
    if (data.success) {
      clients = data.data;
      if (typeof renderClients === 'function') renderClients();
    }
  } catch (err) {
    console.error('Erreur chargement clients', err);
  }
}

let messages = [];
async function loadMessages() {
  try {
    const res = await fetch('../pagesbackend/get_messages.php');
    const data = await res.json();
    if (data.success) {
      messages = data.data;
      renderMessages();
    }
  } catch (err) { console.error(err); }
}

function renderMessages() {
  const tbody = document.getElementById('messagesTable');
  if (!tbody) return;
  if (messages.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:2rem;">Aucun message de contact.</td></tr>';
    return;
  }
  tbody.innerHTML = messages.map(m => `
    <tr style="${m.is_read == 0 ? 'font-weight:bold; background:#f9f9f9;' : ''}">
      <td>#${m.id}</td>
      <td>
        <div>${m.name}</div>
        <div style="font-size:1.2rem;color:#777">${m.email} <br> ${m.phone}</div>
      </td>
      <td>${m.subject}</td>
      <td><div style="max-height: 80px; overflow-y: auto; font-size:1.3rem;">${m.message}</div></td>
      <td>${new Date(m.created_at).toLocaleString('fr-FR')}</td>
      <td>
        ${m.is_read == 0 ? `<button class="action-btn" title="Marquer lu" onclick="markMessageRead(${m.id})"><i class="fas fa-eye"></i></button>` : ''}
        <button class="action-btn" title="Supprimer" onclick="deleteMessage(${m.id})" style="color:var(--red)"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
}

async function markMessageRead(id) {
  try {
    await fetch(`../pagesbackend/get_messages.php?mark_read=${id}`);
    loadMessages();
  } catch(e) { console.error(e); }
}

async function deleteMessage(id) {
  if (!confirm('Voulez-vous supprimer ce message ?')) return;
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res = await fetch('../pagesbackend/delete_message.php', { method:'POST', body: fd });
    const data = await res.json();
    if (data.success) { loadMessages(); } else { alert(data.message); }
  } catch (err) { alert('Erreur: ' + err.message); }
}

let reviews = [];
async function loadReviews() {
  try {
    const res = await fetch('../pagesbackend/get_reviews_admin.php?_=' + new Date().getTime());
    const data = await res.json();
    if(data.success) {
      reviews = data.data;
      renderReviews();
    }
  } catch(e) { console.error(e); }
}

function renderReviews() {
  const tbody = document.getElementById('reviewsTable');
  if(!tbody) return;
  tbody.innerHTML = reviews.map(r => `
    <tr>
      <td><b>${r.target_type}</b><br>${r.target_name}</td>
      <td>${r.client_name}</td>
      <td><span style="color:#F6C90E">${'★'.repeat(r.rating)}</span><span style="color:#ddd">${'★'.repeat(5-r.rating)}</span></td>
      <td><div style="max-width:300px; max-height:80px; overflow-y:auto; font-size:1.3rem;">${r.comment}</div></td>
      <td>${new Date(r.created_at).toLocaleDateString()}</td>
      <td>
        <span class="status-badge status-${r.status === 'approved' ? 'confirmed' : (r.status === 'rejected' ? 'cancelled' : 'pending')}">${r.status}</span>
      </td>
      <td>
        ${r.status === 'pending' ? `
        <button class="btn-icon" style="color:var(--green)" onclick="updateReviewStatus(${r.id}, 'approved')" title="Approuver"><i class="fas fa-check"></i></button>
        <button class="btn-icon" style="color:var(--red)" onclick="updateReviewStatus(${r.id}, 'rejected')" title="Rejeter"><i class="fas fa-times"></i></button>
        ` : ''}
        <button class="btn-icon" style="color:var(--mid)" onclick="updateReviewStatus(${r.id}, 'pending')" title="Remettre en attente"><i class="fas fa-undo"></i></button>
      </td>
    </tr>
  `).join('');
}

async function updateReviewStatus(id, status) {
  const fd = new FormData();
  fd.append('id', id);
  fd.append('status', status);
  try {
    const res = await fetch('../pagesbackend/update_review.php', { method: 'POST', body: fd});
    const data = await res.json();
    if(data.success) { loadReviews(); showToast('Statut mis à jour'); }
    else { alert('Erreur : ' + data.message); }
  } catch(e) { alert('Erreur réseau'); }
}

async function loadDashboardStats() {
  try {
    const res = await fetch('../pagesbackend/get_dashboard_stats.php');
    const data = await res.json();
    if(data.success) {
      document.getElementById('stat-res').textContent = data.reservations;
      document.getElementById('stat-rev').textContent = data.revenue.toLocaleString();
      document.getElementById('stat-clients').textContent = data.clients;
      document.getElementById('stat-canc').textContent = data.cancellations;
    }
  } catch(e) {
    console.error('Erreur chargement stats', e);
  }
}

async function loadAgencySettings() {
  try {
    const res = await fetch('../pagesbackend/get_agency_settings.php');
    const data = await res.json();
    if(data.success) {
      document.getElementById('ag-name').value = data.data.agency_name;
      document.getElementById('ag-email').value = data.data.contact_email;
      document.getElementById('ag-phone').value = data.data.phone;
      document.getElementById('ag-address').value = data.data.address;
    }
  } catch(e) {
    console.error('Erreur chargement settings', e);
  }
}

async function saveAgencySettings() {
  const fd = new FormData();
  fd.append('agency_name', document.getElementById('ag-name').value);
  fd.append('contact_email', document.getElementById('ag-email').value);
  fd.append('phone', document.getElementById('ag-phone').value);
  fd.append('address', document.getElementById('ag-address').value);
  
  try {
    const res = await fetch('../pagesbackend/update_agency_settings.php', {method: 'POST', body: fd});
    const data = await res.json();
    if(data.success) showToast(data.message);
    else alert('Erreur : ' + data.message);
  } catch(e) {
    alert('Erreur réseau');
  }
}

async function changeAdminPassword() {
  const current = document.getElementById('sec-current').value;
  const newpass = document.getElementById('sec-new').value;
  if(!current || !newpass) return alert('Veuillez remplir tous les champs');
  
  try {
    const res = await fetch('../pagesbackend/change_password.php', {
      method: 'POST', 
      body: JSON.stringify({current, newpass})
    });
    const data = await res.json();
    if(data.success) {
      showToast(data.message);
      document.getElementById('sec-current').value = '';
      document.getElementById('sec-new').value = '';
    } else {
      alert(data.message);
    }
  } catch(e) {
    alert('Erreur réseau');
  }
}

/* ════════════════════════════════════════════
   DONNÉES DYNAMIQUES PACKAGES (local JS)
════════════════════════════════════════════ */
let packages = []; // Rempli via BDD
let editingPkgId = null;

/* ════════════════════════════════════════════
   HOTELS — variables (chargées via PHP/BDD)
════════════════════════════════════════════ */
let hotels         = [];   // ← tableau vide, rempli par loadHotels()
let editingHotelId = null;

/* ════════════════════════════════════════════
   ÉTAT PARTAGÉ
════════════════════════════════════════════ */
let pendingDelete = null;

/* ════════════════════════════════════════════
   API ENDPOINTS
════════════════════════════════════════════ */
const API = {
  get    : '../pagesbackend/get_hotels.php',
  add    : '../pagesbackend/add_hotel.php',
  edit   : '../pagesbackend/edit_hotel.php',
  delete : '../pagesbackend/delete_hotel.php',
  getPkg : '../pagesbackend/get_packages.php',
  addPkg : '../pagesbackend/add_package.php',
  editPkg: '../pagesbackend/edit_package.php',
  delPkg : '../pagesbackend/delete_package.php',
};

/* ════════════════════════════════════════════
   TOAST
════════════════════════════════════════════ */
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

/* ════════════════════════════════════════════
   MODAL HELPERS
════════════════════════════════════════════ */
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// Fermer modal en cliquant sur le backdrop
document.addEventListener('click', e => {
  ['hotelModal','packageModal','confirmModal'].forEach(id => {
    const el = document.getElementById(id);
    if (!el.classList.contains('hidden') && e.target === el) closeModal(id);
  });
});

/* ════════════════════════════════════════════
   ÉTOILES
════════════════════════════════════════════ */
function setStar(n) {
  document.getElementById('hStars').value = n;
  document.querySelectorAll('.star-btn').forEach(b => {
    b.classList.toggle('active', parseInt(b.dataset.val) <= n);
  });
}

/* ════════════════════════════════════════════
   HOTELS — MODAL
════════════════════════════════════════════ */
function openAddHotel() {
  editingHotelId = null;
  document.getElementById('hotelModalTitle').textContent = 'Ajouter un hôtel';
  document.getElementById('hName').value  = '';
  document.getElementById('hLoc').value   = '';
  document.getElementById('hPrice').value = '';
  document.getElementById('hPromo').value = '0';
  document.getElementById('hDesc').value  = '';
  document.getElementById('hImages').value = '';
  document.getElementById('hImgPreviewWrap').style.display = 'none';
  setStar(5);
  document.getElementById('hImgPreviewWrap').style.display = 'none';
  openModal('hotelModal');
}

function openEditHotel(id) {
  const h = hotels.find(x => x.id === id);
  if (!h) return;
  editingHotelId = id;
  document.getElementById('hotelModalTitle').textContent = "Modifier l'hôtel";
  document.getElementById('hName').value  = h.name;
  document.getElementById('hLoc').value   = h.location;
  document.getElementById('hPrice').value = h.price;
  document.getElementById('hPromo').value = h.promo;
  document.getElementById('hDesc').value  = h.description || '';
  
  const imgWrap = document.getElementById('hImgPreviewWrap');
  const imgPreview = document.getElementById('hImgPreview');
  if (h.image) {
    try {
      const parsed = JSON.parse(h.image);
      imgPreview.src = Array.isArray(parsed) && parsed.length > 0 ? '../' + parsed[0] : '../' + h.image;
    } catch(e) { imgPreview.src = '../' + h.image; }
    imgWrap.style.display = 'block';
  } else {
    imgWrap.style.display = 'none';
  }
  document.getElementById('hImages').value = '';
  setStar(h.stars);
  openModal('hotelModal');
}

function previewImage(input) {
  const wrap = document.getElementById('hImgPreviewWrap');
  const img  = document.getElementById('hImgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}

/* ════════════════════════════════════════════
   HOTELS — SAVE (ADD / EDIT)
════════════════════════════════════════════ */
async function saveHotel() {
  const name      = document.getElementById('hName').value.trim();
  const loc       = document.getElementById('hLoc').value.trim();
  const price     = parseFloat(document.getElementById('hPrice').value);
  const promo     = parseInt(document.getElementById('hPromo').value) || 0;
  const stars     = parseInt(document.getElementById('hStars').value);
  const desc      = document.getElementById('hDesc').value.trim();
  const imageFiles = document.getElementById('hImages').files;

  if (!name || !loc || !price) {
    alert('Veuillez remplir les champs obligatoires (nom, localisation, prix).');
    return;
  }

  const btn = document.getElementById('saveHotelBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi…';

  const fd = new FormData();
  fd.append('name',        name);
  fd.append('location',    loc);
  fd.append('stars',       stars);
  fd.append('price',       price);
  fd.append('promo',       promo);
  fd.append('description', desc);
  if (imageFiles.length > 0) {
    fd.append('image', imageFiles[0]);
  }
  if (editingHotelId) fd.append('id', editingHotelId);

  const url = editingHotelId ? API.edit : API.add;

  try {
    const res  = await fetch(url, { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      showToast(data.message);
      closeModal('hotelModal');
      await loadHotels();
loadPackages();
    } else {
      alert('Erreur : ' + data.message);
    }
  } catch (err) {
    alert('Erreur réseau : ' + err.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
  }
}

/* ════════════════════════════════════════════
   HOTELS — DELETE
════════════════════════════════════════════ */
function confirmDeleteHotel(id) {
  const h = hotels.find(x => x.id === id);
  if (!h) return;
  document.getElementById('confirmText').innerHTML =
    `Voulez-vous supprimer l'hôtel <strong>${h.name}</strong> ?<br>Cette action est irréversible.`;
  pendingDelete = { type: 'hotel', id };
  openModal('confirmModal');
}

async function executeDeleteHotel(id) {
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res  = await fetch(API.delete, { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      showToast('Hôtel supprimé');
      await loadHotels();
loadPackages();
    } else {
      alert('Erreur : ' + data.message);
    }
  } catch (err) {
    alert('Erreur réseau : ' + err.message);
  }
}

/* ════════════════════════════════════════════
   CONFIRM DELETE — fonction unifiée
   Gère hotels (→ PHP) ET packages (→ local JS)
════════════════════════════════════════════ */
function executeDelete() {
  if (!pendingDelete) return;
  closeModal('confirmModal');

  if (pendingDelete.type === 'hotel') {
    // Appel PHP asynchrone
    executeDeleteHotel(pendingDelete.id);
  } else if (pendingDelete.type === 'service') {
    const hotelId = pendingDelete.hotelId;
    const fd = new FormData();
    fd.append('id', pendingDelete.id);
    fetch('../pagesbackend/delete_service.php', { method: 'POST', body: fd })
      .then(r => r.json())
      .then(res => {
        if(res.success) loadServices(hotelId);
        else alert(res.message);
      });
  } else if (pendingDelete.type === 'package') {
    executeDeletePackage(pendingDelete.id);
  } else if (pendingDelete.type === 'client') {
    executeDeleteClient(pendingDelete.id);
  } else if (pendingDelete.type === 'slider') {
    executeDeleteSlider(pendingDelete.id);
  } else {
    // Suppression locale
    packages = packages.filter(x => x.id !== pendingDelete.id);
    showToast('Supprimé');
  }
  pendingDelete = null;
}

/* ════════════════════════════════════════════
   HOTELS — LOAD depuis BDD
════════════════════════════════════════════ */
async function loadHotels() {
  try {
    const res  = await fetch(API.get);
    const data = await res.json();
    if (data.success) {
      hotels = data.data;
      renderHotels();
    } else {
      console.error('Erreur chargement hotels:', data.message);
    }
  } catch (err) {
    console.error('Erreur réseau:', err);
  }
}

/* ════════════════════════════════════════════
   HOTELS — RENDER
════════════════════════════════════════════ */
function renderHotels() {
  const grid  = document.getElementById('hotelsGrid');
  const empty = document.getElementById('hotelsEmpty');
  document.getElementById('hotelCount').textContent =
    `${hotels.length} hôtel${hotels.length !== 1 ? 's' : ''} dans le catalogue`;

  if (!hotels.length) { grid.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';

  grid.innerHTML = hotels.map(h => {
    const imgHtml = h.image
      ? `<img src="../${h.image}" alt="${h.name}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">`
      : `<i class="fas fa-hotel" style="font-size:4rem;opacity:.25"></i>`;

    const starsHtml =
      '<span style="color:#F6C90E">★</span>'.repeat(h.stars) +
      '<span style="color:#ddd">★</span>'.repeat(5 - h.stars);

    const promoBadge = h.promo > 0
      ? `<span class="status-badge status-confirmed">-${h.promo}%</span>`
      : '';

    return `
      <div class="item-card">
        <div class="item-card-img" style="overflow:hidden;display:flex;align-items:center;justify-content:center">
          ${imgHtml}
        </div>
        <div class="item-card-body">
          <h3>${h.name}</h3>
          <div class="loc"><i class="fas fa-map-marker-alt" style="color:var(--green);margin-right:.4rem"></i>${h.location}</div>
          <div style="margin-bottom:1rem;font-size:1.8rem;line-height:1">${starsHtml}</div>
          <div class="price-row">
            <span class="price">à partir de ${h.price} TND</span>
            ${promoBadge}
          </div>
          ${h.description ? `<p style="font-size:1.2rem;color:var(--mid);margin-top:.8rem;line-height:1.5;
            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">${h.description}</p>` : ''}
        </div>
        <div class="item-card-footer">
          <button class="btn-icon btn-view" title="Voir"><i class="fas fa-eye"></i></button>
          <button class="btn-icon btn-services" title="Services" onclick="openServicesModal(${h.id}, '${h.name.replace(/'/g, "\\'")}')"><i class="fas fa-concierge-bell"></i></button>
          <button class="btn-icon btn-rooms" title="Disponibilité" onclick="openRoomsModal(${h.id}, '${h.name.replace(/'/g, "\\'")}')"><i class="fas fa-door-open"></i></button>
          <button class="btn-icon btn-edit" title="Modifier" onclick="openEditHotel(${h.id})"><i class="fas fa-pen"></i></button>
          <button class="btn-icon btn-del" title="Supprimer" onclick="confirmDeleteHotel(${h.id})"><i class="fas fa-trash"></i></button>
        </div>
      </div>`;
  }).join('');
}

/* ════════════════════════════════════════════
   PACKAGES — CRUD
════════════════════════════════════════════ */
async function loadPackages() {
  try {
    const res = await fetch(API.getPkg);
    const data = await res.json();
    if (data.success) {
      packages = data.data;
      renderPackages();
    } else {
      console.error('Erreur chargement packages:', data.message);
    }
  } catch(err) { console.error('Erreur réseau packages:', err); }
}

function previewPkgImage(input) {
  const wrap = document.getElementById('pkgImgPreviewWrap');
  const img  = document.getElementById('pkgImgPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; wrap.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  } else {
    wrap.style.display = 'none';
    img.src = '';
  }
}

const programmeTags = [
  {id:'hebergement', label:'Hébergement', icon:'fas fa-bed'},
  {id:'petitdej', label:'Petit Déj.', icon:'fas fa-coffee'},
  {id:'dejeuner', label:'Déjeuner', icon:'fas fa-utensils'},
  {id:'diner', label:'Dîner', icon:'fas fa-hamburger'},
  {id:'transport', label:'Transport', icon:'fas fa-bus'},
  {id:'vol', label:'Vol', icon:'fas fa-plane'},
  {id:'guide', label:'Guide', icon:'fas fa-user-tie'},
  {id:'activite', label:'Activité', icon:'fas fa-hiking'},
  {id:'dromadaire', label:'Dromadaire', icon:'fas fa-horse'},
  {id:'quad', label:'Quad', icon:'fas fa-motorcycle'},
  {id:'feucamp', label:'Feu de Camp', icon:'fas fa-fire'}
];

function addProgrammeDay(data = null) {
  const list = document.getElementById('pkgProgrammeList');
  const dayIndex = list.children.length + 1;
  const div = document.createElement('div');
  div.className = 'programme-day-item';
  div.style.cssText = 'border:1px solid var(--border);border-radius:8px;padding:1rem;background:#f9fafb;position:relative;';
  
  let title = data ? data.title : '';
  let desc = data ? data.desc : '';
  let tags = data && data.tags ? data.tags : []; 

  let tagsHtml = programmeTags.map(t => {
    let checked = tags.includes(t.id) ? 'checked' : '';
    return `<label style="display:inline-flex;align-items:center;gap:0.4rem;font-size:1.2rem;background:#fff;padding:0.4rem 0.8rem;border:1px solid var(--border);border-radius:4px;cursor:pointer;margin-bottom:0.5rem;margin-right:0.5rem">
              <input type="checkbox" value="${t.id}" class="day-tag-cb" ${checked}> <i class="${t.icon}" style="color:var(--green)"></i> ${t.label}
            </label>`;
  }).join('');

  div.innerHTML = `
    <button type="button" onclick="this.parentElement.remove(); updateDayNumbers();" style="position:absolute;top:1rem;right:1rem;background:none;border:none;color:var(--red);cursor:pointer" title="Supprimer ce jour"><i class="fas fa-trash"></i></button>
    <div style="font-weight:600;margin-bottom:0.5rem">Jour <span class="day-num">${dayIndex}</span></div>
    <input type="text" class="day-title" placeholder="Titre du jour (ex: Départ De Tunis – Arrivée À Touzeur)" value="${title}" style="width:100%;margin-bottom:0.5rem;padding:0.6rem;border:1px solid var(--border);border-radius:4px;font-size:1.3rem;">
    <textarea class="day-desc" rows="2" placeholder="Description du programme de la journée..." style="width:100%;margin-bottom:0.5rem;padding:0.6rem;border:1px solid var(--border);border-radius:4px;resize:vertical;font-family:inherit;font-size:1.3rem;">${desc}</textarea>
    <div style="margin-top:0.5rem">
      <div style="font-size:1.2rem;color:var(--mid);margin-bottom:0.5rem">Options incluses dans ce jour :</div>
      <div style="display:flex;flex-wrap:wrap;">
        ${tagsHtml}
      </div>
    </div>
  `;
  list.appendChild(div);
  updateDayNumbers();
}

function updateDayNumbers() {
  const list = document.getElementById('pkgProgrammeList');
  Array.from(list.children).forEach((child, index) => {
    const span = child.querySelector('.day-num');
    if(span) span.textContent = index + 1;
  });
}

function setPkgStar(val) {
  document.getElementById('pkgStars').value = val;
  const btns = document.querySelectorAll('#pkgStarRow .star-btn');
  btns.forEach(b => {
    b.classList.toggle('active', parseInt(b.getAttribute('data-val')) <= val);
  });
}

function openAddPackage() {
  editingPkgId = null;
  document.getElementById('pkgModalTitle').textContent = 'Créer un package';
  ['pkgName','pkgLoc','pkgDays','pkgPrice','pkgOld','pkgGroup','pkgDiff','pkgLang','pkgDesc','pkgInclus','pkgExclus','pkgImages'].forEach(id => {
      let el = document.getElementById(id);
      if(el) el.value = '';
  });
  document.getElementById('pkgProgrammeList').innerHTML = '';
  addProgrammeDay();
  setPkgStar(5);
  document.getElementById('pkgImgPreviewWrap').style.display = 'none';
  openModal('packageModal');
}

function openEditPackage(id) {
  const p = packages.find(x => Number(x.id) === Number(id));
  if (!p) return;
  editingPkgId = id;
  document.getElementById('pkgModalTitle').textContent = 'Modifier le package';
  document.getElementById('pkgName').value  = p.name;
  document.getElementById('pkgLoc').value   = p.location;
  document.getElementById('pkgDays').value  = p.duration;
  document.getElementById('pkgPrice').value = p.price;
  document.getElementById('pkgOld').value   = p.old_price || '';
  document.getElementById('pkgGroup').value = p.group_size || '';
  document.getElementById('pkgDiff').value  = p.difficulty || '';
  document.getElementById('pkgLang').value  = p.language || '';
  document.getElementById('pkgDesc').value  = p.description || '';
  document.getElementById('pkgInclus').value = p.inclus || '';
  document.getElementById('pkgExclus').value = p.exclus || '';
  
  const progList = document.getElementById('pkgProgrammeList');
  progList.innerHTML = '';
  if(p.programme) {
    try {
      const days = JSON.parse(p.programme);
      if(Array.isArray(days)) {
        days.forEach(d => addProgrammeDay(d));
      } else { addProgrammeDay(); }
    } catch(e) { addProgrammeDay(); }
  } else { addProgrammeDay(); }
  
  setPkgStar(p.stars);
  
  const imgWrap = document.getElementById('pkgImgPreviewWrap');
  const imgPreview = document.getElementById('pkgImgPreview');
  if (p.image) {
    imgPreview.src = '../' + p.image;
    imgWrap.style.display = 'block';
  } else {
    imgWrap.style.display = 'none';
  }
  document.getElementById('pkgImages').value = '';
  openModal('packageModal');
}

async function savePackage() {
  const name  = document.getElementById('pkgName').value.trim();
  const loc   = document.getElementById('pkgLoc').value.trim();
  const days  = document.getElementById('pkgDays').value.trim();
  const price = parseFloat(document.getElementById('pkgPrice').value);
  const old   = parseFloat(document.getElementById('pkgOld').value) || 0;
  const group = document.getElementById('pkgGroup').value.trim();
  const diff  = document.getElementById('pkgDiff').value.trim();
  const lang  = document.getElementById('pkgLang').value.trim();
  const desc  = document.getElementById('pkgDesc').value.trim();
  const inclus = document.getElementById('pkgInclus').value.trim();
  const exclus = document.getElementById('pkgExclus').value.trim();
  const stars = parseInt(document.getElementById('pkgStars').value);
  
  const progItems = document.querySelectorAll('.programme-day-item');
  let programmeArr = [];
  progItems.forEach(item => {
    let title = item.querySelector('.day-title').value.trim();
    let desc = item.querySelector('.day-desc').value.trim();
    let tags = Array.from(item.querySelectorAll('.day-tag-cb:checked')).map(cb => cb.value);
    if(title || desc) programmeArr.push({title, desc, tags});
  });
  const programme = JSON.stringify(programmeArr);
  const imageFiles = document.getElementById('pkgImages').files;

  if (!name || !loc || !price) { alert('Veuillez remplir les champs obligatoires.'); return; }
  
  const btn = document.getElementById('savePkgBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi…';

  const fd = new FormData();
  fd.append('name', name);
  fd.append('location', loc);
  fd.append('stars', stars);
  fd.append('price', price);
  fd.append('old_price', old);
  fd.append('duration', days);
  fd.append('group_size', group);
  fd.append('language', lang);
  fd.append('difficulty', diff);
  fd.append('description', desc);
  fd.append('programme', programme);
  fd.append('inclus', inclus);
  fd.append('exclus', exclus);
  if (imageFiles.length > 0) {
    for (let i = 0; i < imageFiles.length; i++) {
      fd.append('images[]', imageFiles[i]);
    }
  }
  if (editingPkgId) fd.append('id', editingPkgId);

  const url = editingPkgId ? API.editPkg : API.addPkg;

  try {
    const res = await fetch(url, { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      showToast(data.message);
      closeModal('packageModal');
      await loadPackages();
    } else {
      alert('Erreur : ' + data.message);
    }
  } catch (err) {
    alert('Erreur réseau : ' + err.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
  }
}

function confirmDeletePackage(id) {
  const p = packages.find(x => Number(x.id) === Number(id));
  if (!p) return;
  document.getElementById('confirmText').innerHTML =
    `Voulez-vous supprimer le package <strong>${p.name}</strong> ?<br>Cette action est irréversible.`;
  pendingDelete = {type: 'package', id};
  openModal('confirmModal');
}

async function executeDeletePackage(id) {
  try {
    const res = await fetch(API.delPkg, { method: 'POST', body: JSON.stringify({id: id}) });
    const data = await res.json();
    if (data.success) {
      showToast('Package supprimé');
      await loadPackages();
    } else {
      alert('Erreur : ' + data.error);
    }
  } catch (err) {
    alert('Erreur réseau : ' + err.message);
  }
}

/* ════════════════════════════════════════════
   RENDER — Réservations / Clients / Packages
════════════════════════════════════════════ */
const statusLabel = {confirmed:'Confirmée',pending:'En attente',cancelled:'Annulée',completed:'Terminée'};
const statusClass = {confirmed:'status-confirmed',pending:'status-pending',cancelled:'status-cancelled',completed:'status-completed'};
function badge(s)   { return `<span class="status-badge ${statusClass[s]}">${statusLabel[s]}</span>`; }

function actionsRes(b) {
  let html = `<div class="action-btns">`;
  if (b.status === 'pending') {
     html += `<button class="btn-icon" style="color:var(--green)" onclick="updateResStatus(${b.id.replace('#', '').substring(1)}, '${b.type}', 'confirmed')" title="Approuver"><i class="fas fa-check"></i></button>`;
     html += `<button class="btn-icon" style="color:var(--red)" onclick="updateResStatus(${b.id.replace('#', '').substring(1)}, '${b.type}', 'cancelled')" title="Rejeter"><i class="fas fa-times"></i></button>`;
  }
  html += `</div>`;
  return html;
}
function actions()  { return `<div class="action-btns"><button class="btn-icon btn-view"><i class="fas fa-eye"></i></button><button class="btn-icon btn-edit"><i class="fas fa-pen"></i></button><button class="btn-icon btn-del"><i class="fas fa-trash"></i></button></div>`; }

function renderRecent() {
  document.getElementById('recentBookings').innerHTML = bookings.slice(0,6).map(b =>
    `<tr><td style="font-weight:600;color:var(--green)">${b.id}</td><td>${b.client}</td><td>${b.dest}</td>
     <td>${b.cin} → ${b.cout}</td><td style="font-weight:600">${b.amount.toLocaleString()} TND</td>
     <td>${badge(b.status)}</td></tr>`).join('');
}

function renderAll(data) {
  document.getElementById('allBookings').innerHTML = (data || bookings).map(b =>
    `<tr><td style="font-weight:600;color:var(--green)">${b.id}</td><td>${b.client}</td><td>${b.dest}</td>
     <td>${b.cin}</td><td>${b.cout}</td><td style="text-align:center">${b.pers}</td>
     <td style="font-weight:600">${b.amount.toLocaleString()} TND</td>
     <td>${badge(b.status)}</td><td>${actionsRes(b)}</td></tr>`).join('');
}

function updateResStatus(id, type, status) {
  const fd = new FormData();
  fd.append('id', id);
  fd.append('type', type);
  fd.append('status', status);
  fetch('../pagesbackend/update_reservation_status.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if(res.success) { showToast(res.message); loadBookings(); }
      else alert(res.message);
    });
}

function confirmDeleteClient(id) {
  const c = clients.find(x => Number(x.id) === Number(id));
  if (!c) return;
  document.getElementById('confirmText').innerHTML =
    `Voulez-vous supprimer le client <strong>${c.name}</strong> ?<br>Toutes ses réservations seront supprimées. Cette action est irréversible.`;
  pendingDelete = {type: 'client', id};
  openModal('confirmModal');
}

async function executeDeleteClient(id) {
  try {
    const res = await fetch('../pagesbackend/delete_client.php', { method: 'POST', body: JSON.stringify({id: id}) });
    const data = await res.json();
    if (data.success) {
      showToast('Client supprimé');
      await loadClients();
    } else {
      alert('Erreur : ' + data.message);
    }
  } catch (err) {
    alert('Erreur réseau : ' + err.message);
  }
}

function renderClients() {
  document.getElementById('clientsTable').innerHTML = clients.map(c => {
    const init = c.name.split(' ').map(w => w[0]).join('').slice(0,2).toUpperCase();
    const actionsHtml = `<div class="action-btns"><button class="btn-icon btn-del" onclick="confirmDeleteClient(${c.id})" title="Supprimer"><i class="fas fa-trash"></i></button></div>`;
    return `<tr><td><div style="display:flex;align-items:center;gap:1.2rem">
      <div class="client-avatar">${init}</div>${c.name}</div></td>
      <td style="color:var(--mid)">${c.email}</td><td>${c.phone}</td>
      <td style="text-align:center;font-weight:600">${c.bookings}</td>
      <td style="font-weight:600">${parseFloat(c.total).toLocaleString()} TND</td>
      <td style="color:var(--mid)">${c.date}</td><td>${actionsHtml}</td></tr>`;
  }).join('');
}

function renderPackages() {
  const grid  = document.getElementById('packagesGrid');
  const empty = document.getElementById('packagesEmpty');
  document.getElementById('pkgCount').textContent =
    `${packages.length} package${packages.length!==1?'s':''} disponible${packages.length!==1?'s':''}`;
  if (!packages.length) { grid.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';
  grid.innerHTML = packages.map(p => {
    let imgSrc = '';
    if (p.image) {
      try {
        const parsed = JSON.parse(p.image);
        imgSrc = Array.isArray(parsed) && parsed.length > 0 ? '../' + parsed[0] : '../' + p.image;
      } catch(e) { imgSrc = '../' + p.image; }
    }
    const imgHtml = p.image 
      ? `<img src="${imgSrc}" alt="${p.name}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">` 
      : `<i class="fas fa-suitcase-rolling" style="font-size:4rem;opacity:.25"></i>`;

    const starsHtml =
      '<span style="color:#F6C90E">★</span>'.repeat(p.stars) +
      '<span style="color:#ddd">★</span>'.repeat(5 - p.stars);

    return `
    <div class="item-card">
      <div class="item-card-img" style="overflow:hidden;display:flex;align-items:center;justify-content:center">
        ${imgHtml}
      </div>
      <div class="item-card-body">
        <h3>${p.name}</h3>
        <div class="loc" style="margin-bottom:0.5rem"><i class="fas fa-map-marker-alt" style="color:var(--green);margin-right:.4rem"></i>${p.location}</div>
        <div style="font-size:1.3rem;color:var(--mid);margin-bottom:0.5rem"><i class="fas fa-clock" style="margin-right:.4rem"></i>${p.duration}</div>
        <div style="margin-bottom:1rem;font-size:1.8rem;line-height:1">${starsHtml}</div>
        <div class="price-row">
          <span class="price">à partir de ${parseFloat(p.price).toLocaleString()} TND</span>
          ${p.old_price && p.old_price > 0 ? `<span class="status-badge status-confirmed" style="text-decoration:line-through;opacity:0.8">${parseFloat(p.old_price).toLocaleString()} TND</span>` : ''}
        </div>
        ${p.description ? `<p style="font-size:1.2rem;color:var(--mid);margin-top:.8rem;line-height:1.5;
            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">${p.description}</p>` : ''}
      </div>
      <div class="item-card-footer">
        <button class="btn-icon btn-view" title="Voir" onclick="window.open('../pages/package-detail.php?id=${p.id}', '_blank')"><i class="fas fa-eye"></i></button>
        <button class="btn-icon btn-edit" title="Modifier" onclick="openEditPackage(${p.id})"><i class="fas fa-pen"></i></button>
        <button class="btn-icon btn-del" title="Supprimer" onclick="confirmDeletePackage(${p.id})"><i class="fas fa-trash"></i></button>
      </div>
    </div>`;
  }).join('');
}

/* ════════════════════════════════════════════
   FILTER
════════════════════════════════════════════ */
function filterTable(val) { renderAll(val === 'all' ? null : bookings.filter(b => b.status === val)); }

/* ════════════════════════════════════════════
   NAVIGATION
════════════════════════════════════════════ */
const pageNames = {
  dashboard:'Dashboard', reservations:'Bookings',
  clients:'Clients', hotels:'Hotels', packages:'Packages', settings:'Settings', avis: 'Client Reviews'
};
function showPage(name, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  if (el) el.classList.add('active');
  else { const f = document.querySelector(`.nav-item[onclick*="'${name}'"]`); if (f) f.classList.add('active'); }
  document.getElementById('topbarTitle').textContent = pageNames[name] || name;
  closeSidebar();
}

/* ════════════════════════════════════════════
   SIDEBAR / NOTIFICATIONS
════════════════════════════════════════════ */
function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('overlay').classList.toggle('show'); }
function closeSidebar()  { document.getElementById('sidebar').classList.remove('open'); document.getElementById('overlay').classList.remove('show'); }
function toggleNotif()   { document.getElementById('notifPanel').classList.toggle('open'); }
document.addEventListener('click', e => {
  const p = document.getElementById('notifPanel');
  if (p.classList.contains('open') && !p.contains(e.target) && !e.target.closest('.topbar-icon'))
    p.classList.remove('open');
});

/* ════════════════════════════════════════════
   DATE
════════════════════════════════════════════ */
function setDate() {
  const d = new Date();
  document.getElementById('todayDate').textContent =
    d.toLocaleDateString('fr-FR', {weekday:'short', day:'numeric', month:'short', year:'numeric'});
}

/* ════════════════════════════════════════════
   INIT
   ✔ loadHotels() remplace renderHotels() ici
     car il est async et appelle renderHotels()
     lui-même après le fetch
════════════════════════════════════════════ */
setDate();
loadDashboardStats();
loadAgencySettings();
renderRecent();
renderAll();
loadClients();
loadHotels();
loadPackages();   // ← async : charge depuis BDD puis renderHotels()
loadReviews();  // ← load reviews
loadSliders();  // ← load sliders

/* ════════════════════════════════════════════
   SERVICES D'HÔTEL
════════════════════════════════════════════ */
function openServicesModal(hotelId, hotelName) {
  document.getElementById('servicesModalTitle').textContent = `Services - ${hotelName}`;
  document.getElementById('srvHotelId').value = hotelId;
  document.getElementById('srvSelect').selectedIndex = 0;
  loadServices(hotelId);
  openModal('servicesModal');
}

function loadServices(hotelId) {
  fetch(`../pagesbackend/get_services.php?hotel_id=${hotelId}`)
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        const list = document.getElementById('servicesList');
        if(data.data.length === 0) {
           list.innerHTML = '<p>No services found.</p>';
        } else {
           list.innerHTML = data.data.map(s => `
             <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem;border-bottom:1px solid #eee;">
               <span><i class="${s.icon}" style="width:20px; text-align: center; margin-right: 5px;"></i> ${s.name}</span>
               <button class="btn-icon btn-del" onclick="deleteService(${s.id}, ${hotelId})"><i class="fas fa-trash"></i></button>
             </div>
           `).join('');
        }
      }
    });
}

function addService() {
  const hotelId = document.getElementById('srvHotelId').value;
  const select = document.getElementById('srvSelect');
  if(select.selectedIndex <= 0) return alert('Please select a service');
  
  const option = select.options[select.selectedIndex];
  const name = option.value;
  const icon = option.getAttribute('data-icon');
  const fd = new FormData();
  fd.append('hotel_id', hotelId);
  fd.append('name', name);
  fd.append('icon', icon);
  fetch('../pagesbackend/add_service.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if(res.success) {
        document.getElementById('srvSelect').selectedIndex = 0;
        loadServices(hotelId);
      } else alert(res.message);
    });
}

function deleteService(id, hotelId) {
  document.getElementById('confirmText').innerHTML = 'Are you sure you want to delete this service?<br>This action cannot be undone.';
  pendingDelete = {type: 'service', id: id, hotelId: hotelId};
  openModal('confirmModal');
}

/* ════════════════════════════════════════════
   CHAMBRES D'HÔTEL (DISPONIBILITÉ)
════════════════════════════════════════════ */
function openRoomsModal(hotelId, hotelName) {
  document.getElementById('roomsModalTitle').textContent = `Rooms - ${hotelName}`;
  document.getElementById('rmHotelId').value = hotelId;
  document.getElementById('rmType').value = '';
  document.getElementById('rmTotal').value = '';
  document.getElementById('rmPriceMod').value = '0';
  document.getElementById('rmAdults').value = '2';
  document.getElementById('rmChildren').value = '0';
  loadRooms(hotelId);
  openModal('roomsModal');
}

function loadRooms(hotelId) {
  fetch(`../pagesbackend/get_rooms.php?hotel_id=${hotelId}`)
    .then(r => r.json())
    .then(data => {
      if(data.success) {
        const list = document.getElementById('roomsList');
        if(data.data.length === 0) {
           list.innerHTML = '<p>No rooms found for this hotel.</p>';
        } else {
           list.innerHTML = data.data.map(r => `
             <div style="display:flex;justify-content:space-between;align-items:center;padding:0.8rem;border-bottom:1px solid #eee;">
               <div>
                 <strong>${r.room_type}</strong> (Total: ${r.total_rooms})<br>
                 <small style="color:var(--mid)">Capacité: ${r.capacity_adults} Adultes, ${r.capacity_children} Enfants</small>
               </div>
               <button class="btn-icon btn-del" onclick="deleteRoom(${r.id}, ${hotelId})"><i class="fas fa-trash"></i></button>
             </div>
           `).join('');
        }
      }
    });
}

function addRoom() {
  const hotelId = document.getElementById('rmHotelId').value;
  const type = document.getElementById('rmType').value.trim();
  const total = document.getElementById('rmTotal').value;
  const priceMod = document.getElementById('rmPriceMod').value;
  const adults = document.getElementById('rmAdults').value;
  const children = document.getElementById('rmChildren').value;

  if(!type || !total) return alert('Veuillez remplir le type et le nombre total de chambres.');
  
  const fd = new FormData();
  fd.append('hotel_id', hotelId);
  fd.append('room_type', type);
  fd.append('total_rooms', total);
  fd.append('price_modifier', priceMod);
  fd.append('capacity_adults', adults);
  fd.append('capacity_children', children);

  fetch('../pagesbackend/add_room.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if(res.success) {
        document.getElementById('rmType').value = '';
        document.getElementById('rmTotal').value = '';
        loadRooms(hotelId);
      } else alert(res.error);
    });
}

function deleteRoom(id, hotelId) {
  if(!confirm('Voulez-vous supprimer cette chambre ?')) return;
  const fd = new FormData();
  fd.append('id', id);
  fetch('../pagesbackend/delete_room.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      if(res.success) loadRooms(hotelId);
      else alert(res.error);
    });
}

/* ════════════════════════════════════════════
   SLIDERS
════════════════════════════════════════════ */
let sliders = [];

async function loadSliders() {
  try {
    const res = await fetch('../pagesbackend/get_sliders.php');
    const data = await res.json();
    if (data.success) {
      sliders = data.sliders;
      renderSliders();
    } else {
      console.error('Erreur chargement sliders:', data.error);
    }
  } catch (err) {
    console.error('Erreur réseau sliders:', err);
  }
}

function renderSliders() {
  const grid = document.getElementById('slidersGrid');
  const empty = document.getElementById('slidersEmpty');
  document.getElementById('sliderCount').textContent = `${sliders.length} slider${sliders.length !== 1 ? 's' : ''}`;

  if (!sliders.length) { grid.innerHTML = ''; empty.style.display = 'block'; return; }
  empty.style.display = 'none';

  grid.innerHTML = sliders.map(s => {
    let mediaHtml = s.media_type === 'video' 
      ? `<video src="../${s.media_path}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit" autoplay muted loop></video>`
      : `<img src="../${s.media_path}" alt="${s.title}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit">`;

    return `
      <div class="item-card">
        <div class="item-card-img" style="overflow:hidden;display:flex;align-items:center;justify-content:center;height:180px;">
          ${mediaHtml}
        </div>
        <div class="item-card-body">
          <h3>${s.title}</h3>
          ${s.subtitle ? `<p style="font-size:1.2rem;color:var(--mid);margin-top:.4rem;">${s.subtitle}</p>` : ''}
          <div style="font-size:1rem;color:var(--mid);margin-top:.8rem;text-transform:uppercase;">${s.media_type}</div>
        </div>
        <div class="item-card-footer">
          <button class="btn-icon btn-del" title="Supprimer" onclick="confirmDeleteSlider(${s.id})"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `;
  }).join('');
}

function openAddSlider() {
  document.getElementById('sldTitle').value = '';
  document.getElementById('sldSubtitle').value = '';
  document.getElementById('sldMedia').value = '';
  openModal('sliderModal');
}

async function saveSlider() {
  const title = document.getElementById('sldTitle').value.trim();
  const subtitle = document.getElementById('sldSubtitle').value.trim();
  const mediaFile = document.getElementById('sldMedia').files[0];

  if (!title || !mediaFile) {
    alert('Le titre et le média sont obligatoires.');
    return;
  }

  const btn = document.getElementById('saveSliderBtn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

  const fd = new FormData();
  fd.append('title', title);
  fd.append('subtitle', subtitle);
  fd.append('media', mediaFile);

  try {
    const res = await fetch('../pagesbackend/add_slider.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      showToast('Slider ajouté avec succès');
      closeModal('sliderModal');
      await loadSliders();
    } else {
      alert('Erreur: ' + data.error);
    }
  } catch (err) {
    alert('Erreur réseau: ' + err.message);
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Enregistrer';
  }
}

function confirmDeleteSlider(id) {
  const s = sliders.find(x => x.id === id);
  if (!s) return;
  document.getElementById('confirmText').innerHTML = `Voulez-vous supprimer ce slider ?<br>Cette action est irréversible.`;
  pendingDelete = { type: 'slider', id: id };
  openModal('confirmModal');
}

async function executeDeleteSlider(id) {
  try {
    const res = await fetch('../pagesbackend/delete_slider.php', { method: 'POST', body: JSON.stringify({ id: id }) });
    const data = await res.json();
    if (data.success) {
      showToast('Slider supprimé');
      await loadSliders();
    } else {
      alert('Erreur: ' + data.error);
    }
  } catch (err) {
    alert('Erreur réseau: ' + err.message);
  }
}
