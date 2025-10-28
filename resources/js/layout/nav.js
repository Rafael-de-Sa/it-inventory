// resources/js/nav.js
document.addEventListener('DOMContentLoaded', () => {
    // Mobile toggle
    const btnMobile = document.getElementById('btn-mobile');
    const mobileMenu = document.getElementById('mobile-menu');
    if (btnMobile && mobileMenu) {
        btnMobile.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Dropdowns (desktop)
    const DROPDOWNS = document.querySelectorAll('[data-dropdown]');
    function closeAllDropdowns(except = null) {
        DROPDOWNS.forEach(d => {
            if (d !== except) {
                const btn = d.querySelector('[data-dropdown-button]');
                const menu = d.querySelector('[data-dropdown-menu]');
                if (btn && menu) {
                    btn.setAttribute('aria-expanded', 'false');
                    menu.classList.add('invisible', 'opacity-0', 'pointer-events-none');
                }
            }
        });
    }

    DROPDOWNS.forEach(drop => {
        const button = drop.querySelector('[data-dropdown-button]');
        const menu = drop.querySelector('[data-dropdown-menu]');
        if (!button || !menu) return;

        // inicia fechado
        menu.classList.add('invisible', 'opacity-0', 'pointer-events-none');

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = button.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                button.setAttribute('aria-expanded', 'false');
                menu.classList.add('invisible', 'opacity-0', 'pointer-events-none');
            } else {
                closeAllDropdowns(drop);
                button.setAttribute('aria-expanded', 'true');
                menu.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
            }
        });
    });

    // Fecha ao clicar fora / ESC
    document.addEventListener('click', () => closeAllDropdowns());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAllDropdowns();
    });
});
