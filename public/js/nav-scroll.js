document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('nav');
    if (!nav) return;

    let lastScrollTop = 0;
    const scrollThreshold = 80; // Distance before hiding starts
    const delta = 10; // Minimum scroll delta before toggling

    window.addEventListener('scroll', () => {
        const mobileMenu = document.getElementById('mobile-menu');
        const isMenuOpen = mobileMenu && !mobileMenu.classList.contains('hidden');
        
        // Don't hide the nav if the mobile menu is currently open
        if (isMenuOpen) return;

        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Ensure it's shown at the top
        if (scrollTop <= 50) {
            nav.classList.remove('nav-hidden');
            lastScrollTop = scrollTop;
            return;
        }

        // Check if we've scrolled enough to trigger a change
        if (Math.abs(lastScrollTop - scrollTop) <= delta) return;

        if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
            // Scrolling down - hide
            nav.classList.add('nav-hidden');
        } else {
            // Scrolling up - show
            nav.classList.remove('nav-hidden');
        }

        lastScrollTop = scrollTop;
    }, { passive: true });
});
