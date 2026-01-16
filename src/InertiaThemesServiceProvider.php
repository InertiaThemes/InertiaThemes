<?php

namespace InertiaThemes;

use Illuminate\Support\ServiceProvider;

/**
 * InertiaThemes Service Provider
 *
 * Registers the InertiaThemes package services, configuration, and publishable assets.
 *
 * Services registered:
 * - ThemeManager: Manages theme registration and resolution
 * - BlockRegistry: Manages block type registration
 *
 * Publishable assets:
 * - Config: `php artisan vendor:publish --tag=inertiathemes-config`
 * - Vue: `php artisan vendor:publish --tag=inertiathemes-vue`
 * - React: `php artisan vendor:publish --tag=inertiathemes-react`
 * - Svelte: `php artisan vendor:publish --tag=inertiathemes-svelte`
 * - Stubs: `php artisan vendor:publish --tag=inertiathemes-stubs`
 *
 * @package InertiaThemes
 */
class InertiaThemesServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * Binds the ThemeManager and BlockRegistry as singletons,
     * with auto-discovery enabled by default.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/inertiathemes.php', 'inertiathemes');

        $this->app->singleton(ThemeManager::class, function ($app) {
            $manager = new ThemeManager($app['config']['inertiathemes']);

            if ($app['config']['inertiathemes.auto_discover'] ?? true) {
                $path = $app['config']['inertiathemes.paths.themes'] ?? app_path('Themes');
                $namespace = $app['config']['inertiathemes.namespaces.themes'] ?? 'App\\Themes';

                foreach (Discovery::themes($path, $namespace) as $themeClass) {
                    $manager->registerClass($themeClass);
                }
            }

            return $manager;
        });

        $this->app->singleton(BlockRegistry::class, function ($app) {
            $registry = new BlockRegistry();

            if ($app['config']['inertiathemes.auto_discover'] ?? true) {
                $path = $app['config']['inertiathemes.paths.blocks'] ?? app_path('Blocks');
                $namespace = $app['config']['inertiathemes.namespaces.blocks'] ?? 'App\\Blocks';

                $registry->registerMany(Discovery::blocks($path, $namespace));
            }

            return $registry;
        });

        $this->app->alias(ThemeManager::class, 'theme');
        $this->app->alias(BlockRegistry::class, 'blocks');
    }

    /**
     * Bootstrap application services.
     *
     * Publishes configuration files, frontend components, and stubs.
     * Registers the 'theme' middleware alias.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/inertiathemes.php' => config_path('inertiathemes.php'),
        ], 'inertiathemes-config');

        $this->publishes([
            __DIR__ . '/../resources/js/vue/Blocks.vue' => resource_path('js/Components/Blocks.vue'),
            __DIR__ . '/../resources/js/composables/useTheme.js' => resource_path('js/composables/useTheme.js'),
        ], 'inertiathemes-vue');

        $this->publishes([
            __DIR__ . '/../resources/js/react/Blocks.jsx' => resource_path('js/Components/Blocks.jsx'),
        ], 'inertiathemes-react');

        $this->publishes([
            __DIR__ . '/../resources/js/svelte/Blocks.svelte' => resource_path('js/Components/Blocks.svelte'),
        ], 'inertiathemes-svelte');

        $this->app['router']->aliasMiddleware('theme', Middleware\SetTheme::class);

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/inertiathemes'),
        ], 'inertiathemes-stubs');
    }
}
