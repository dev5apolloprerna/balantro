
<div id="globalPageLoader" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/50" aria-hidden="true">
  <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-6 py-5 shadow-xl dark:bg-gray-900/90">
    <img src="<?php echo e(asset('images/loader.svg')); ?>" class="h-14 w-14 animate-spin" alt="Loading indicator">
    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Loading...</span>
  </div>
</div>

<script>
(function () {
    if (window.__globalPageLoaderInitialized) return;
    window.__globalPageLoaderInitialized = true;

    const loader = document.getElementById('globalPageLoader');
    if (!loader) return;

    const showLoader = () => {
        loader.classList.remove('hidden');
        loader.classList.add('flex');
        loader.setAttribute('aria-hidden', 'false');
    };

    const hideLoader = () => {
        loader.classList.add('hidden');
        loader.classList.remove('flex');
        loader.setAttribute('aria-hidden', 'true');
    };

    window.showGlobalLoader = showLoader;
    window.hideGlobalLoader = hideLoader;

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.loader === 'false') return;

        setTimeout(function () {
            if (!event.defaultPrevented) showLoader();
        }, 0);
    });

    document.addEventListener('click', function (event) {
        if (!(event.target instanceof Element)) return;

        const link = event.target.closest('a[href]');
        if (!link || link.dataset.loader === 'false') return;
        if (link.target && link.target !== '_self') return;
        if (link.hasAttribute('download')) return;

        const href = link.getAttribute('href') || '';
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;

        setTimeout(function () {
            if (!event.defaultPrevented) showLoader();
        }, 0);
    });

    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', hideLoader);
})();
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/shared/_loader.blade.php ENDPATH**/ ?>