import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';

// ── GLOBAL ERROR HANDLERS ─────────────────────────────────────────────────
// Catch unhandled JS errors that would otherwise silently produce a blank page
window.addEventListener('error', (event) => {
    console.error('[Picpic] Uncaught JS error:', {
        message: event.message,
        source: event.filename,
        line: event.lineno,
        col: event.colno,
        error: event.error,
    });
});

// Catch unhandled Promise rejections (e.g. failed dynamic imports)
window.addEventListener('unhandledrejection', (event) => {
    console.error('[Picpic] Unhandled Promise rejection:', event.reason);
});

console.log('[Picpic] app.js initializing ✓');

const appName = import.meta.env.VITE_APP_NAME || 'Picpic';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        console.log('[Picpic] Resolving page component:', name);
        return resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );
    },
    setup({ el, App, props, plugin }) {
        console.log('[Picpic] Mounting Vue app on element:', el, 'with component:', props?.initialPage?.component);

        const pinia = createPinia();
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);

        // ── Vue-level error handler ───────────────────────────────────────
        // Catches errors thrown inside Vue components (setup, render, lifecycle hooks)
        vueApp.config.errorHandler = (err, instance, info) => {
            console.error('[Picpic] Vue errorHandler caught:', {
                error: err,
                info,
                component: instance?.$options?.name ?? instance?.$options?.__name ?? 'unknown',
            });
        };

        // Warn handler — surfaces Vue warnings that can indicate misconfiguration
        vueApp.config.warnHandler = (msg, instance, trace) => {
            console.warn('[Picpic] Vue warning:', msg, trace);
        };

        const mountedApp = vueApp.mount(el);
        console.log('[Picpic] Vue app mounted successfully ✓');
        return mountedApp;
    },
    progress: {
        color: '#7C6BC4',
    },
}).catch((err) => {
    // createInertiaApp itself can reject if page resolution fails
    console.error('[Picpic] createInertiaApp failed:', err);
});
