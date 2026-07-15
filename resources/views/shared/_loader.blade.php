{{-- Global page loader. Hidden by default; shown for normal same-page form submits and same-window navigation. --}}
<div id="globalPageLoader"
     class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-950/60 backdrop-blur-sm"
     aria-hidden="true"
     role="status"
     aria-live="polite">
  <div class="flex min-w-[220px] flex-col items-center gap-4 rounded-2xl border border-white/10 bg-white/95 px-7 py-6 text-center shadow-2xl dark:bg-gray-950/95">
    <div class="relative flex h-16 w-16 items-center justify-center">
      <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sky-400/20"></span>
      <span class="absolute inline-flex h-14 w-14 rounded-full border-4 border-slate-200 dark:border-slate-700"></span>
      <span class="absolute inline-flex h-14 w-14 animate-spin rounded-full border-4 border-transparent border-t-sky-500 border-r-sky-500"></span>
      <img src="{{ asset('images/loader.svg') }}" class="relative h-8 w-8" alt="">
    </div>
    <div class="space-y-1">
      <span id="globalPageLoaderText" class="block text-sm font-semibold text-gray-800 dark:text-gray-100">Loading...</span>
      <span class="block text-xs text-gray-500 dark:text-gray-400">Please wait while the page is prepared.</span>
    </div>
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
    const previewReadyMessage = 'Preparing preview table...';
    const isPreviewPage = (url) => {
        const path = (url.pathname || '').toLowerCase();
        return (path.includes('/transaction-processing') || path.includes('/bulkupload') || path.includes('/bulk-upload'))
            && path.includes('preview');
    };
    const messageForUrl = (url) => {
        const path = (url.pathname || '').toLowerCase();
        if (isPreviewPage(url)) return previewReadyMessage;
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

    const hideLoaderWhenIdle = (delay = 120) => {
        const finish = () => window.setTimeout(hideLoader, delay);
        window.requestAnimationFrame(() => window.requestAnimationFrame(finish));
    };

    window.showGlobalLoader = showLoader;
    window.hideGlobalLoader = hideLoader;
    window.hideGlobalLoaderWhenIdle = hideLoaderWhenIdle;

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

    const currentUrl = new URL(window.location.href);
    const shouldKeepLoaderUntilPreviewSettles = isPreviewPage(currentUrl);
    if (shouldKeepLoaderUntilPreviewSettles) {
        showLoader(messageForUrl(currentUrl));
    }

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

    window.addEventListener('pageshow', function () {
        if (shouldKeepLoaderUntilPreviewSettles) {
            showLoader(messageForUrl(currentUrl));
            hideLoaderWhenIdle(180);
            return;
        }
        hideLoader();
    });
    window.addEventListener('load', function () {
        if (shouldKeepLoaderUntilPreviewSettles) {
            hideLoaderWhenIdle(180);
            return;
        }
        hideLoader();
    });
})();
</script>