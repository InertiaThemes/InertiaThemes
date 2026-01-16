<?php

namespace InertiaThemes\Facades;

use Illuminate\Support\Facades\Facade;
use InertiaThemes\BlockRegistry;

/**
 * @method static \InertiaThemes\BlockRegistry register(string $blockClass)
 * @method static \InertiaThemes\BlockRegistry registerMany(array $blockClasses)
 * @method static \InertiaThemes\Contracts\Block|null get(string $type)
 * @method static array types()
 * @method static \Illuminate\Support\Collection all()
 * @method static array list()
 * @method static array byCategory()
 * @method static bool has(string $type)
 * @method static array defaultContent(string $type)
 * @method static string|null component(string $type)
 *
 * @see \InertiaThemes\BlockRegistry
 */
class Blocks extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BlockRegistry::class;
    }
}
