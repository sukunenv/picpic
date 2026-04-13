<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * Uses the Vite manifest file hash so that Inertia triggers a full-page
     * reload whenever assets are rebuilt, preventing stale JS/CSS mismatches
     * that can cause blank pages after a deploy.
     */
    public function version(Request $request): ?string
    {
        $manifest = public_path('build/manifest.json');

        if (file_exists($manifest)) {
            return md5_file($manifest) ?: parent::version($request);
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            // Debug metadata — visible in browser DevTools via the Inertia
            // page object (window.__inertia or the Vue devtools plugin).
            // Remove or gate behind APP_DEBUG=false once the issue is resolved.
            '_debug' => [
                'php'         => PHP_VERSION,
                'laravel'     => app()->version(),
                'environment' => app()->environment(),
                'url'         => $request->fullUrl(),
                'component'   => $request->route()?->getName(),
            ],
        ];
    }
}
