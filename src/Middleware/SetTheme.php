<?php

namespace InertiaThemes\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use InertiaThemes\ThemeManager;
use Symfony\Component\HttpFoundation\Response;

class SetTheme
{
    public function __construct(
        protected ThemeManager $themeManager
    ) {}

    /**
     * Handle an incoming request.
     *
     * Sets the current theme and shares it with Inertia.
     * Only the specified theme is loaded - other themes are ignored.
     *
     * Usage:
     *   Route::middleware('theme:classic-orange')->group(...)
     *   Route::middleware('theme')->group(...) // uses default or session
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $theme = null): Response
    {
        // Resolve theme: parameter > callable > session > config default
        $themeId = $theme
            ?? $this->resolveFromRequest($request)
            ?? $request->session()->get('theme')
            ?? config('inertiathemes.default');

        if ($themeId && $this->themeManager->has($themeId)) {
            // This only loads the single theme class
            $this->themeManager->use($themeId);

            // Share with Inertia for SSR compatibility
            Inertia::share('theme', fn () => $this->themeManager->current()?->toArray());
        }

        return $next($request);
    }

    /**
     * Override this method in a custom middleware to resolve theme from request
     * e.g., from organization settings, subdomain, etc.
     */
    protected function resolveFromRequest(Request $request): ?string
    {
        return null;
    }
}
