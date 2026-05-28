let searchBtn = document.querySelector('#search-btn');
let searchBar = document.querySelector('.search-bar-container');
let formBtn = document.querySelector('#login-btn');
let loginFormContainer = document.querySelector('.login-form-container');
let formClose = document.querySelector('#form-close');
let menu = document.querySelector('#menu-bar');
let navbar = document.querySelector('.navbar');
let slides = document.querySelectorAll('.slider');
let contents = document.querySelectorAll('.content');
const leftArrow = document.querySelector('.left-arrow');
const rightArrow = document.querySelector('.right-arrow');

let current = 0;

// ─── SCROLL FERME TOUT ───────────────────────────────────
window.onscroll = () => {
    if (searchBtn) searchBtn.classList.remove('fa-times');
    if (searchBar) searchBar.classList.remove('active');
    if (menu) menu.classList.remove('fa-times');
    if (navbar) navbar.classList.remove('active');
    if (loginFormContainer) loginFormContainer.classList.remove('active');
};

// ─── SEARCH ──────────────────────────────────────────────
if (searchBtn) {
    searchBtn.addEventListener('click', () => {
        searchBtn.classList.toggle('fa-times');
        if (searchBar) searchBar.classList.toggle('active');
    });
}

// ─── LOGIN MODAL ─────────────────────────────────────────
if (formBtn) {
    formBtn.addEventListener('click', () => {
        loginFormContainer.classList.add('active');
    });
}

if (formClose) {
    formClose.addEventListener('click', () => {
        loginFormContainer.classList.remove('active');
    });
}

// ─── MENU BURGER ─────────────────────────────────────────
menu.addEventListener('click', () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
});

// ─── SLIDER ──────────────────────────────────────────────
function goToSlide(index) {
    slides[current].classList.remove('active');
    if (contents.length > 0) contents[current].classList.remove('active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('active');
    if (contents.length > 0) contents[current].classList.add('active');
}

if (leftArrow)  leftArrow.addEventListener('click',  () => goToSlide(current - 1));
if (rightArrow) rightArrow.addEventListener('click', () => goToSlide(current + 1));

// ─── TRAVELER DROPDOWN ───────────────────────────────────
let rooms = 1, adults = 2, children = 0;
const trigger = document.getElementById('traveler-trigger');
const panel   = document.getElementById('traveler-panel');

if (trigger) trigger.addEventListener('click', () => panel.classList.toggle('active'));
document.getElementById('close-dropdown')?.addEventListener('click',    () => panel.classList.remove('active'));
document.getElementById('validate-dropdown')?.addEventListener('click', () => panel.classList.remove('active'));

function updateTraveler() {
    const adultCountEl = document.getElementById('adult-count');
    const childCountEl = document.getElementById('child-count');
    if (adultCountEl) adultCountEl.textContent = adults;
    if (childCountEl) childCountEl.textContent = children;
    
    const textEl = document.getElementById('traveler-text');
    if (textEl) {
        textEl.textContent = `${rooms} Chambre${rooms > 1 ? 's' : ''}, ${adults} Adulte${adults > 1 ? 's' : ''}, ${children} Enfant${children > 1 ? 's' : ''}`;
    }

    const hRooms = document.getElementById('hidden-rooms');
    const hAdults = document.getElementById('hidden-adults');
    const hChildren = document.getElementById('hidden-children');
    if (hRooms) hRooms.value = rooms;
    if (hAdults) hAdults.value = adults;
    if (hChildren) hChildren.value = children;
}

document.getElementById('adult-plus')?.addEventListener('click',  (e) => { e.preventDefault(); adults++; updateTraveler(); });
document.getElementById('adult-minus')?.addEventListener('click', (e) => { e.preventDefault(); if (adults > 1) { adults--; updateTraveler(); } });
document.getElementById('child-plus')?.addEventListener('click',  (e) => { e.preventDefault(); children++; updateTraveler(); });
document.getElementById('child-minus')?.addEventListener('click', (e) => { e.preventDefault(); if (children > 0) { children--; updateTraveler(); } });

document.querySelector('.add-room-btn')?.addEventListener('click', (e) => {
    e.preventDefault();
    rooms++;
    adults++; // 1 more adult minimum for new room
    updateTraveler();
});

// ─── LOGIN / REGISTER SWITCH ─────────────────────────────
let isLoginMode = true;

function switchMode(mode) {
    isLoginMode = (mode === 'login');

    document.getElementById('form-title').textContent = isLoginMode ? 'login'    : 'sign up';
    document.getElementById('form-submit').value      = isLoginMode ? 'login now' : 'register now';
    document.getElementById('reg-name').style.display = isLoginMode ? 'none'      : 'block';
    document.getElementById('auth-msg').innerHTML     = '';

    document.getElementById('switch-text').innerHTML = isLoginMode
        ? `don't have an account? <a href="#" id="switch-to-register">register now</a>`
        : `already have an account? <a href="#" id="switch-to-register">login now</a>`;

    // re-bind après innerHTML
    document.getElementById('switch-to-register').addEventListener('click', (e) => {
        e.preventDefault();
        switchMode(isLoginMode ? 'register' : 'login');
    });
}

document.getElementById('switch-to-register')?.addEventListener('click', (e) => {
    e.preventDefault();
    switchMode('register');
});

// ─── SUBMIT LOGIN / REGISTER ─────────────────────────────
document.querySelector('.login-form-container form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg      = document.getElementById('auth-msg');
    const email    = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    msg.innerHTML = '<span style="color:#888">Chargement...</span>';

    if (isLoginMode) {
        const res  = await fetch('auth/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            msg.innerHTML = `<span style="color:red">${data.errors.join('<br>')}</span>`;
        }
    } else {
        const name = document.getElementById('reg-name').value;
        const res  = await fetch('auth/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            msg.innerHTML = `<span style="color:red">${data.errors.join('<br>')}</span>`;
        }
    }
});

// ─── USER DROPDOWN ────────────────────────────────────────
const userBtn      = document.getElementById('user-btn');
const userDropdown = document.getElementById('user-dropdown');

if (userBtn) {
    userBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (userDropdown && !userDropdown.contains(e.target) && e.target !== userBtn) {
            userDropdown.classList.remove('active');
        }
    });
}