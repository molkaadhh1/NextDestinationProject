document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const msgContainer = document.getElementById('contact-msg-container');
            const btn = document.getElementById('contact-submit-btn');
            
            const fd = new FormData(this);
            btn.innerHTML = 'Envoi...';
            btn.disabled = true;
            
            try {
                const res = await fetch('pagesbackend/add_message.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.success) {
                    msgContainer.innerHTML = '<span style="color: green;">' + data.message + '</span>';
                    this.reset();
                } else {
                    msgContainer.innerHTML = '<span style="color: red;">' + data.message + '</span>';
                }
            } catch (err) {
                msgContainer.innerHTML = '<span style="color: red;">Erreur de connexion.</span>';
            } finally {
                btn.innerHTML = 'send a message';
                btn.disabled = false;
            }
        });
    }
});
