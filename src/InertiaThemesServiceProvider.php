<?php

namespace InertiaThemes;

use Illuminate\Support\ServiceProvider;

class InertiaThemesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/inertiathemes.php', 'inertiathemes');

        // Theme Manager
        $this->app->singleton(ThemeManager::class, function ($app) {
            $manager = new ThemeManager($app['config']['inertiathemes']);

            // Auto-discover themes if enabled
            if ($app['config']['inertiathemes.auto_discover'] ?? true) {
                $path = $app['config']['inertiathemes.paths.themes'] ?? app_path('Themes');
                $namespace = $app['config']['inertiathemes.namespaces.themes'] ?? 'App\\Themes';

                foreach (Discovery::themes($path, $namespace) as $themeClass) {
                    $manager->registerClass($themeClass);
                }
            }

            return $manager;
        });

        // Block Registry
        $this->app->singleton(BlockRegistry::class, function ($app) {
            $registry = new BlockRegistry();

            // Auto-discover blocks if enabled
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

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/inertiathemes.php' => config_path('inertiathemes.php'),
        ], 'inertiathemes-config');

        // Publish Vue Blocks component
        $this->publishes([
            __DIR__ . '/../resources/js/vue/Blocks.vue' => resource_path('js/Components/Blocks.vue'),
            __DIR__ . '/../resources/js/composables/useTheme.js' => resource_path('js/composables/useTheme.js'),
        ], 'inertiathemes-vue');

        // Publish React Blocks component
        $this->publishes([
            __DIR__ . '/../resources/js/react/Blocks.jsx' => resource_path('js/Components/Blocks.jsx'),
        ], 'inertiathemes-react');

        // Publish Svelte Blocks component
        $this->publishes([
            __DIR__ . '/../resources/js/svelte/Blocks.svelte' => resource_path('js/Components/Blocks.svelte'),
        ], 'inertiathemes-svelte');

        // Register middleware alias
        $this->app['router']->aliasMiddleware('theme', Middleware\SetTheme::class);

        // Publish stubs for artisan make commands (future)
        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/inertiathemes'),
        ], 'inertiathemes-stubs');
    }
}
