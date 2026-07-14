
<div id="globalPageLoader" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/50" aria-hidden="true">
  <div class="flex flex-col items-center gap-3 rounded-xl bg-white/90 px-6 py-5 shadow-xl dark:bg-gray-900/90">
    <img src="<?php echo e(asset('images/loader.svg')); ?>" class="h-14 w-14 animate-spin" alt="Loading indicator">
    <span id="globalPageLoaderText" class="text-sm font-medium text-gray-700 dark:text-gray-200">Loading...</span>
  </div>
</div>

<script>
(function () {
    if (window.__globalPageLoaderInitialized) return;
    window.__globalPageLoaderInitialized = true;

    const loader = document.getElementById('globalPageLoader');
    const loaderText = document.getElementById('globalPageLoaderText');
    if (!loader) return;

    const defaultMessage = 'Loading...';
    const messageForUrl = (url) => {
        const path = (url.pathname || '').toLowerCase();
        if (path.includes('/reports') || path.includes('/clients/reports')) return 'Loading report...';
        if (path.includes('/transaction-processing')) return 'Loading transaction processing...';
        if (path.includes('/bulkupload') || path.includes('/bulk-upload')) return 'Loading bulk upload preview...';
        return defaultMessage;
    };

    const setLoaderMessage = (message) => {
        if (loaderText) loaderText.textContent = message || defaultMessage;
    };

    const showLoader = (message) => {
        setLoaderMessage(message);
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

    const downloadOrExportPattern = /(?:\/download(?:\/|$)|\/export(?:\/|[-_])|(?:^|[-_\/])(excel|pdf)(?:[-_\/]|$)|\.(?:pdf|xlsx?|csv)(?:$|[?#]))/i;

    const shouldSkipLoaderForUrl = (url) => downloadOrExportPattern.test(url.pathname + url.search);

    const shouldSkipLoaderForForm = (form) => {
        const action = form.getAttribute('action') || window.location.href;
        const method = (form.getAttribute('method') || 'get').toLowerCase();
        const url = new URL(action, window.location.href);

        return form.target && form.target !== '_self'
            || method === 'get' && shouldSkipLoaderForUrl(url)
            || method === 'post' && /(?:download|export|excel|pdf)/i.test(url.pathname);
    };

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.loader === 'false' || shouldSkipLoaderForForm(form)) return;

        setTimeout(function () {
            if (!event.defaultPrevented) showLoader(messageForUrl(new URL(form.getAttribute('action') || window.location.href, window.location.href)));
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
        if (shouldSkipLoaderForUrl(url)) return;
        
        setTimeout(function () {
            if (!event.defaultPrevented) showLoader(messageForUrl(url));
        }, 0);
    });

     document.addEventListener('change', function (event) {
        const field = event.target;
        if (!(field instanceof HTMLSelectElement) || field.dataset.loader === 'false') return;
        const hasInlineNavigation = /window\.location|location\.href|location\.assign/i.test(field.getAttribute('onchange') || '');
        if (hasInlineNavigation && field.value) {
            showLoader(messageForUrl(new URL(field.value, window.location.href)));
        }
    }, true);

    window.addEventListener('beforeunload', function () {
        showLoader(messageForUrl(new URL(window.location.href)));
    });

    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', hideLoader);
})();
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/shared/_loader.blade.php ENDPATH**/ ?>