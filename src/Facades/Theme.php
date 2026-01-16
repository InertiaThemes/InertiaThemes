<?php

namespace InertiaThemes\Facades;

use Illuminate\Support\Facades\Facade;
use InertiaThemes\ThemeManager;

/**
 * @method static \InertiaThemes\ThemeManager register(string $id, string $themeClass)
 * @method static \InertiaThemes\ThemeManager use(string $id)
 * @method static \InertiaThemes\Contracts\Theme|null current()
 * @method static \InertiaThemes\Contracts\Theme|null get(string $id)
 * @method static array registered()
 * @method static \Illuminate\Support\Collection loadAll()
 * @method static array list()
 * @method static array listMinimal()
 * @method static array resolveBlockContent(string $blockType, array $content = [])
 * @method static array createPageBlocks()
 * @method static bool has(string $id)
 * @method static mixed config(string $key, mixed $default = null)
 *
 * @see \InertiaThemes\ThemeManager
 */
class Theme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ThemeManager::class;
    }
}
