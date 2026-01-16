<?php

namespace InertiaThemes\Facades;

use Illuminate\Support\Facades\Facade;
use InertiaThemes\ThemeManager;

/**
 * Theme Facade
 *
 * Provides static access to the ThemeManager for theme operations.
 *
 * @method static ThemeManager register(string $id, string $themeClass) Register a theme class by ID
 * @method static ThemeManager registerClass(string $themeClass) Register a theme class (auto-discovers ID)
 * @method static ThemeManager use(string $id) Set the current theme by ID
 * @method static \InertiaThemes\Contracts\Theme|null current() Get the current theme
 * @method static \InertiaThemes\Contracts\Theme|null get(string $id) Get a theme by ID
 * @method static array registered() Get all registered theme IDs
 * @method static \Illuminate\Support\Collection loadAll() Load all registered themes
 * @method static array list() Get theme list for UI (loads all themes)
 * @method static array listMinimal() Get minimal theme list (IDs and names only)
 * @method static array resolveBlockContent(string $blockType, array $content = []) Resolve block content with theme defaults
 * @method static array createPageBlocks() Create page blocks from current theme defaults
 * @method static bool has(string $id) Check if a theme is registered
 * @method static mixed config(string $key, mixed $default = null) Get config value
 *
 * @see \InertiaThemes\ThemeManager
 *
 * @package InertiaThemes\Facades
 */
class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ThemeManager::class;
    }
}
