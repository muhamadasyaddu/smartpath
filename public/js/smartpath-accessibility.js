(function () {
    function enhanceMapMarker(element) {
        if (!element.matches('.leaflet-marker-icon')) return;

        const label =
            element.getAttribute('aria-label')
            || element.getAttribute('alt')
            || element.getAttribute('title');

        if (!label) return;

        element.setAttribute('role', 'button');
        element.setAttribute('tabindex', '0');
        element.setAttribute('aria-label', label);
        element.dataset.accessibilityEnhanced = 'true';
    }

    function hideDecorativeIcons(root) {
        if (root.matches && root.matches('.leaflet-marker-icon')) {
            enhanceMapMarker(root);
        }

        root.querySelectorAll('.leaflet-marker-icon').forEach(enhanceMapMarker);

        const icons = root.querySelectorAll('i, svg');

        icons.forEach(icon => {
            if (
                icon.hasAttribute('aria-label')
                || icon.hasAttribute('aria-labelledby')
                || icon.getAttribute('role') === 'img'
                || icon.querySelector('title, desc')
            ) {
                return;
            }

            icon.setAttribute('aria-hidden', 'true');
        });
    }

    function initialize() {
        hideDecorativeIcons(document);

        document.addEventListener('keydown', event => {
            const marker = event.target.closest('.leaflet-marker-icon');

            if (marker && event.key === ' ') {
                event.preventDefault();
                marker.click();
                return;
            }

            if (event.key === 'Escape') {
                const popupClose = document.querySelector(
                    '.leaflet-popup-close-button'
                );
                if (popupClose) popupClose.click();
            }
        });

        const observer = new MutationObserver(records => {
            records.forEach(record => {
                record.addedNodes.forEach(node => {
                    if (node.nodeType !== Node.ELEMENT_NODE) return;
                    if (node.matches('.leaflet-marker-icon, i, svg')) {
                        hideDecorativeIcons(node.parentElement || document);
                    } else {
                        hideDecorativeIcons(node);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize, { once: true });
    } else {
        initialize();
    }
})();
