<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#7C6BC4">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Picpic">
        <meta name="description" content="Aplikasi pemesanan menu kafe digital Picpic">
        <meta name="msapplication-TileColor" content="#7C6BC4">

        <title inertia>{{ config('app.name', 'Picpic') }}</title>

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">

        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">

        <!-- Favicon -->
        <link rel="icon" type="image/png" sizes="96x96" href="/icons/icon-96x96.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

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
