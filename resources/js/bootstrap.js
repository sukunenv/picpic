import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── DEBUG: Bootstrap initialization tracking ──────────────────────────────
console.log('[Picpic] bootstrap.js loaded ✓');

// Verify the Inertia root element exists in the DOM
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('app');
    if (!el) {
        console.error('[Picpic] CRITICAL: #app element not found in DOM. Inertia cannot mount.');
        return;
    }

    const pageData = el.dataset.page;
    if (!pageData) {
        console.error('[Picpic] CRITICAL: #app[data-page] attribute is missing. Inertia server-side rendering may have failed.');
        return;
    }

    try {
        const parsed = JSON.parse(pageData);
        console.log('[Picpic] Inertia page payload detected ✓', {
            component: parsed.component,
            url: parsed.url,
            version: parsed.version,
            propsKeys: parsed.props ? Object.keys(parsed.props) : [],
        });
    } catch (e) {
        console.error('[Picpic] CRITICAL: Failed to parse data-page JSON:', e, '\nRaw value:', pageData);
    }
});
