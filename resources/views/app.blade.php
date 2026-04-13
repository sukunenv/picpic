<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#6367FF">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Picpic">
        <meta name="description" content="Aplikasi pemesanan menu kafe digital Picpic">
        <meta name="msapplication-TileColor" content="#6367FF">

        <title inertia>{{ config('app.name', 'Picpic') }}</title>

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">

        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="96x96" href="/icons/icon-96x96.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <!-- Blank-page guard: if Vue never mounts, show a visible error instead of a white screen -->
        <script>
            (function () {
                var TIMEOUT_MS = 8000; // wait up to 8 s for Vue to mount
                var timer = setTimeout(function () {
                    var app = document.getElementById('app');
                    // If the element is still empty (no child nodes rendered by Vue), surface an error
                    if (app && app.children.length === 0) {
                        console.error('[Picpic] Blank-page guard triggered: #app has no children after ' + TIMEOUT_MS + 'ms. Vue likely failed to mount.');
                        app.innerHTML = [
                            '<div style="font-family:sans-serif;padding:32px;max-width:520px;margin:40px auto;',
                            'background:#fff8f8;border:1px solid #f5c6cb;border-radius:12px;color:#721c24;">',
                            '<h2 style="margin:0 0 12px;font-size:18px;">⚠️ Aplikasi gagal dimuat</h2>',
                            '<p style="margin:0 0 8px;font-size:14px;">',
                            'Terjadi kesalahan saat menginisialisasi halaman. ',
                            'Silakan buka <strong>DevTools → Console</strong> untuk melihat detail error.',
                            '</p>',
                            '<p style="margin:0;font-size:13px;color:#856404;background:#fff3cd;',
                            'padding:8px 12px;border-radius:6px;">',
                            'Coba <a href="javascript:location.reload()" style="color:#533f03;">muat ulang halaman</a>. ',
                            'Jika masalah berlanjut, hubungi administrator.',
                            '</p>',
                            '</div>',
                        ].join('');
                    }
                }, TIMEOUT_MS);

                // Cancel the guard as soon as Vue successfully renders something
                document.addEventListener('inertia:finish', function () {
                    clearTimeout(timer);
                });
            })();
        </script>

        <!-- Register Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', function () {
                    navigator.serviceWorker.register('/sw.js')
                        .then(function (registration) {
                            console.log('[Picpic] Service Worker registered:', registration.scope);
                        })
                        .catch(function (error) {
                            console.log('[Picpic] Service Worker registration failed:', error);
                        });
                });
            }
        </script>
    </body>
</html>
