<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | The default theme ID to use when no theme is explicitly set.
    | This should match the id() of one of your theme classes.
    |
    */
    'default' => env('INERTIA_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Auto Discovery
    |--------------------------------------------------------------------------
    |
    | When enabled, themes and blocks are automatically discovered from the
    | configured paths. Disable this if you prefer manual registration.
    |
    */
    'auto_discover' => true,

    /*
    |--------------------------------------------------------------------------
    | Discovery Paths
    |--------------------------------------------------------------------------
    |
    | The directories to scan for theme and block classes.
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
    | The PHP namespaces corresponding to the discovery paths.
    |
    */
    'namespaces' => [
        'themes' => 'App\\Themes',
        'blocks' => 'App\\Blocks',
    ],

];
