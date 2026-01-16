<?php

namespace InertiaThemes\Facades;

use Illuminate\Support\Facades\Facade;
use InertiaThemes\BlockRegistry;

/**
 * Blocks Facade
 *
 * Provides static access to the BlockRegistry for block operations.
 *
 * @method static BlockRegistry register(string $blockClass) Register a block class
 * @method static BlockRegistry registerMany(array $blockClasses) Register multiple block classes
 * @method static \InertiaThemes\Contracts\Block|null get(string $type) Get a block by type
 * @method static array types() Get all registered block types
 * @method static \Illuminate\Support\Collection all() Get all block instances
 * @method static array list() Get block list for UI (block picker)
 * @method static array byCategory() Get blocks grouped by category
 * @method static bool has(string $type) Check if a block type is registered
 * @method static array defaultContent(string $type) Get default content for a block type
 * @method static string|null component(string $type) Get component path for a block type
 *
 * @see \InertiaThemes\BlockRegistry
 *
 * @package InertiaThemes\Facades
 */
class Blocks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return BlockRegistry::class;
    }
}
