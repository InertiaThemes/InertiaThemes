<?php

namespace InertiaThemes\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use InertiaThemes\ThemeManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetTheme Middleware
 *
 * HTTP middleware that sets the active theme for the current request
 * and shares the theme data with Inertia for SSR compatibility.
 *
 * Usage:
 * - Route::middleware('theme:classic-orange')->group(...) - Specific theme
 * - Route::middleware('theme')->group(...) - Uses default or session theme
 *
 * Theme resolution priority:
 * 1. Middleware parameter
 * 2. Custom resolver (override resolveFromRequest)
 * 3. Session value
 * 4. Config default
 *
 * @package InertiaThemes\Middleware
 */
class SetTheme
{
    /**
     * Create a new middleware instance.
     *
     * @param ThemeManager $themeManager The theme manager instance
     */
    public function __construct(
        protected ThemeManager $themeManager
    ) {}

    /**
     * Handle an incoming request.
     *
     * Sets the current theme based on the resolution priority and shares
     * the theme data with Inertia for frontend consumption.
     *
     * @param Request $request The incoming HTTP request
     * @param Closure(Request): Response $next The next middleware handler
     * @param string|null $theme Optional theme ID from route middleware parameter
     * @return Response
     */
    public function handle(Request $request, Closure $next, ?string $theme = null): Response
    {
        $themeId = $theme
            ?? $this->resolveFromRequest($request)
            ?? $request->session()->get('theme')
            ?? config('inertiathemes.default');

        if ($themeId && $this->themeManager->has($themeId)) {
            $this->themeManager->use($themeId);

            Inertia::share('theme', fn () => $this->themeManager->current()?->toArray());
        }

        return $next($request);
    }

    /**
     * Resolve the theme from the incoming request.
     *
     * Override this method in a custom middleware to resolve theme from
     * application-specific logic such as organization settings, subdomain,
     * user preferences, or database lookups.
     *
     * @param Request $request The incoming HTTP request
     * @return string|null The resolved theme ID, or null to continue resolution chain
     */
    protected function resolveFromRequest(Request $request): ?string
    {
        return null;
    }
}
