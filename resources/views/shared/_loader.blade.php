{{-- Global page loader. Hidden by default; shown for same-page submits, same-window navigation, and heavy preview table hydration. --}}
<div id="globalPageLoader" class="global-page-loader hidden" aria-hidden="true" role="status" aria-live="polite">
  <div class="global-page-loader__panel">
    <img src="{{ asset('images/loader.svg') }}" alt="" class="global-page-loader__image" aria-hidden="true">
    <span id="globalPageLoaderText" class="global-page-loader__title">Loading...</span>
  </div>
</div>
<style>
  .global-page-loader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: transparent;
    pointer-events: none;
  }
    .global-page-loader.hidden { display: none; }
  html.bulk-preview-preparing #globalPageLoader { display: flex; }

  .global-page-loader__panel {
    display: flex;
     flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.4rem;
    width: min(92vw, 8.25rem);
    min-height: 9.4rem;
    border: 1px solid rgba(59, 130, 246, 0.28);
    border-radius: 0.875rem;
    background: rgba(15, 23, 42, 0.94);
    padding: 1.8rem 1.25rem 1.55rem;
    box-shadow: 0 24px 70px rgba(2, 6, 23, 0.34);
  }
  
  .global-page-loader__image {
    width: 4rem;
    height: 4rem;
    object-fit: contain;
  }

.global-page-loader__title {
    color: #e5e7eb;
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.35;
    text-align: center;
    letter-spacing: -0.01em;
  }

  .dark .global-page-loader__panel {
    border-color: rgba(59, 130, 246, 0.32);
    background: rgba(15, 23, 42, 0.94);
  }
  
</style>

<script>
(function () {
    if (window.__globalPageLoaderInitialized) return;
    window.__globalPageLoaderInitialized = true;

    const loader = document.getElementById('globalPageLoader');
    const loaderText = document.getElementById('globalPageLoaderText');
    if (!loader) return;

    const defaultMessage = 'Loading...';
    let hideTimer = null;
    let downloadRequested = false;
    let downloadResetTimer = null;
    const messageForUrl = (url) => {
        const path = (url.pathname || '').toLowerCase();
        if (path.includes('/reports') || path.includes('/clients/reports')) return 'Loading report...';
        if (path.includes('/transaction-processing')) return 'Preparing transaction preview...';
        if (path.includes('/bulkupload') || path.includes('/bulk-upload')) return 'Preparing bulk upload preview...';
        return defaultMessage;
    };

    const setLoaderMessage = (message) => {
        if (loaderText) loaderText.textContent = message || defaultMessage;
    };

    const showLoader = (message) => {
        if (hideTimer) {
            clearTimeout(hideTimer);
            hideTimer = null;
        }
        setLoaderMessage(message);
        loader.classList.remove('hidden');
        loader.setAttribute('aria-hidden', 'false');
    };

    const hideLoader = () => {
        loader.classList.add('hidden');
        loader.setAttribute('aria-hidden', 'true');
    };

    const markDownloadRequested = () => {
        downloadRequested = true;
        if (downloadResetTimer) clearTimeout(downloadResetTimer);
        downloadResetTimer = setTimeout(() => {
            downloadRequested = false;
            downloadResetTimer = null;
        }, 10000);
    };

    const hideLoaderWhenIdle = (minimumDelay = 120) => {
        const delay = Math.max(Number(minimumDelay) || 0, 0);
        if (hideTimer) clearTimeout(hideTimer);
        const hide = () => { hideTimer = setTimeout(hideLoader, delay); };
        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(hide, { timeout: 900 });
        } else {
            window.requestAnimationFrame(() => window.requestAnimationFrame(hide));
        }
    };


    window.showGlobalLoader = showLoader;
    window.hideGlobalLoader = hideLoader;
    window.hideGlobalLoaderWhenIdle = hideLoaderWhenIdle;

    // Downloads must not replace the current page. Using a temporary link with the
    // download attribute prevents the beforeunload handler below from leaving the
    // page loader visible after the browser receives a file response.
    window.downloadFile = (url) => {
        if (!url) return;

        // downloadRequested = true;
        markDownloadRequested();
        hideLoader();

        const link = document.createElement('a');
        link.href = url;
        link.download = '';
        link.dataset.loader = 'false';
        link.hidden = true;
        document.body.appendChild(link);
        link.click();
        link.remove();
    };
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

    const isHeavyPreviewPage = () => {
        const path = window.location.pathname.toLowerCase();
        return (path.includes('/transaction-processing/') || path.includes('/bulkupload/'))
            && path.includes('/preview')
            && document.querySelector('table tbody tr');
    };

    if (isHeavyPreviewPage()) {
        showLoader(messageForUrl(new URL(window.location.href)));
        hideLoaderWhenIdle(120);
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
        // if (!link || link.dataset.loader === 'false') return;
        if (!link) return;
        if (link.target && link.target !== '_self') return;
        // if (link.hasAttribute('download')) return;

        const href = link.getAttribute('href') || '';
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search && url.hash) return;
        // if (shouldSkipLoaderForUrl(url)) return;
        // if (link.dataset.loader === 'false' || link.hasAttribute('download') || shouldSkipLoaderForUrl(url)) {
        if (link.dataset.loader === 'false') {
            hideLoader();
            return;
        }

        if (link.hasAttribute('download') || shouldSkipLoaderForUrl(url)) {
            // File responses keep this page open, so beforeunload may fire without
            // a subsequent load/pageshow event to clear the loader.
            // downloadRequested = true;
            markDownloadRequested();
            hideLoader();

            if (!event.defaultPrevented && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey) {
                event.preventDefault();
                window.downloadFile(url.href);
            }
            return;
        }
        
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
        // if (downloadRequested) return;
        if (downloadRequested) {
            hideLoader();
            return;
        }
        showLoader(messageForUrl(new URL(window.location.href)));
    });

    // window.addEventListener('pageshow', hideLoader);
    window.addEventListener('focus', function () {
        if (downloadRequested) hideLoader();
    });

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && downloadRequested) hideLoader();
    });

    window.addEventListener('pageshow', function () {
        downloadRequested = false;
        if (downloadResetTimer) {
            clearTimeout(downloadResetTimer);
            downloadResetTimer = null;
        }
        hideLoader();
    });
    window.addEventListener('load', function () { hideLoaderWhenIdle(120); });
})();
</script>