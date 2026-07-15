
<div id="globalPageLoader" class="global-page-loader hidden" aria-hidden="true" role="status" aria-live="polite">
  <div class="global-page-loader__panel">
    <div class="global-page-loader__mark" aria-hidden="true">
      <img src="<?php echo e(asset('images/loader.svg')); ?>" alt="" class="global-page-loader__image">
    </div>
    <div class="global-page-loader__copy">
      <span id="globalPageLoaderText" class="global-page-loader__title">Loading...</span>
      <span class="global-page-loader__subtitle">Please wait while we prepare the data.</span>
    </div>
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
    background: rgba(15, 23, 42, 0.58);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }
  .global-page-loader.hidden { display: none; }
  .global-page-loader__panel {
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: min(92vw, 23rem);
    border: 1px solid rgba(148, 163, 184, 0.22);
    border-radius: 1rem;
    background: rgba(255, 255, 255, 0.96);
    padding: 1rem 1.15rem;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.28);
  }
  .dark .global-page-loader__panel {
    border-color: rgba(71, 85, 105, 0.7);
    background: rgba(15, 23, 42, 0.96);
  }
  .global-page-loader__mark {
    width: 4rem;
    height: 4rem;
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .global-page-loader__image {
    width: 4rem;
    height: 4rem;
    object-fit: contain;
    filter: invert(1) brightness(0);
  }

  .global-page-loader__copy { display: flex; min-width: 0; flex-direction: column; gap: 0.2rem; }
  .global-page-loader__title { color: #0f172a; font-size: 0.95rem; font-weight: 700; letter-spacing: -0.01em; }
  .global-page-loader__subtitle { color: #64748b; font-size: 0.78rem; font-weight: 500; }
  .dark .global-page-loader__title { color: #f8fafc; }
  .dark .global-page-loader__subtitle { color: #cbd5e1; }
  
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
        hideLoaderWhenIdle(260);
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

    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', function () { hideLoaderWhenIdle(120); });
})();
</script><?php /**PATH D:\xampp\htdocs\balantro\resources\views/shared/_loader.blade.php ENDPATH**/ ?>