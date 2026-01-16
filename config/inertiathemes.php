<?php

/**
 * InertiaThemes Configuration
 *
 * This file contains the configuration options for the InertiaThemes package.
 * Publish this file using: php artisan vendor:publish --tag=inertiathemes-config
 *
 * @package InertiaThemes
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The default theme ID to use when no theme is explicitly set via
    | middleware parameter, session, or custom resolver. This should match
    | the id() return value of one of your registered theme classes.
    |
    */

    'default' => env('INERTIA_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Auto Discovery
    |--------------------------------------------------------------------------
    |
    | When enabled, themes and blocks are automatically discovered from the
    | configured paths on application boot. Set to false if you prefer to
    | manually register themes and blocks in a service provider.
    |
    */

    'auto_discover' => true,

    /*
    |--------------------------------------------------------------------------
    | Discovery Paths
    |--------------------------------------------------------------------------
    |
    | The directories to scan for theme and block classes when auto-discovery
    | is enabled. These paths should contain PHP classes that extend BaseTheme
    | or BaseBlock respectively.
    |
    */

    'paths' => [
        'themes' => app_path('Themes'),
        'blocks' => app_path('Blocks'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Discovery Namespaces
    |--------------------------------------------------------------------------
    |
    | The PHP namespaces corresponding to the discovery paths. These are used
    | to construct the fully qualified class names when auto-discovering
    | themes and blocks.
    |
    */

    'namespaces' => [
        'themes' => 'App\\Themes',
        'blocks' => 'App\\Blocks',
    ],

];
