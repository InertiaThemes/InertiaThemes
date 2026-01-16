<?php

namespace InertiaThemes;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use InertiaThemes\Contracts\Block;

/**
 * Block Registry
 *
 * Central registry for managing block types in InertiaThemes.
 * Provides lazy loading of block instances for performance.
 *
 * Blocks are registered by class name and instantiated on-demand.
 * Use the Blocks facade for convenient static access.
 *
 * @package InertiaThemes
 */
class BlockRegistry
{
    /**
     * Registered block classes indexed by type.
     *
     * @var array<string, class-string<Block>>
     */
    protected array $registered = [];

    /**
     * Instantiated block cache indexed by type.
     *
     * @var array<string, Block>
     */
    protected array $instances = [];

    /**
     * Register a block class.
     *
     * @param class-string<Block> $blockClass The fully qualified block class name
     * @return $this
     *
     * @throws InvalidArgumentException If the class doesn't implement Block
     */
    public function register(string $blockClass): self
    {
        if (!is_subclass_of($blockClass, Block::class)) {
            throw new InvalidArgumentException(
                "Block [{$blockClass}] must implement " . Block::class
            );
        }

        $instance = new $blockClass();
        $this->registered[$instance->type()] = $blockClass;
        $this->instances[$instance->type()] = $instance;

        return $this;
    }

    /**
     * Register multiple block classes.
     *
     * @param array<int, class-string<Block>> $blockClasses Array of block class names
     * @return $this
     */
    public function registerMany(array $blockClasses): self
    {
        foreach ($blockClasses as $blockClass) {
            $this->register($blockClass);
        }

        return $this;
    }

    /**
     * Get a block by type.
     *
     * @param string $type The block type identifier
     * @return Block|null The block instance, or null if not found
     */
    public function get(string $type): ?Block
    {
        if (isset($this->instances[$type])) {
            return $this->instances[$type];
        }

        if (!isset($this->registered[$type])) {
            return null;
        }

        $this->instances[$type] = new $this->registered[$type]();

        return $this->instances[$type];
    }

    /**
     * Get all registered block types.
     *
     * @return array<int, string> List of block type identifiers
     */
    public function types(): array
    {
        return array_keys($this->registered);
    }

    /**
     * Get all block instances.
     *
     * Instantiates any blocks that haven't been loaded yet.
     *
     * @return Collection<string, Block>
     */
    public function all(): Collection
    {
        foreach ($this->registered as $type => $class) {
            if (!isset($this->instances[$type])) {
                $this->instances[$type] = new $class();
            }
        }

        return collect($this->instances);
    }

    /**
     * Get the block list for UI components.
     *
     * Returns an array suitable for block picker interfaces.
     *
     * @return array<int, array{type: string, name: string, category: string, ...}>
     */
    public function list(): array
    {
        return $this->all()
            ->map(fn (Block $block) => $block->toArray())
            ->values()
            ->all();
    }

    /**
     * Get blocks grouped by category.
     *
     * Returns blocks organized by their category for hierarchical UIs.
     *
     * @return array<string, array<int, array>>
     */
    public function byCategory(): array
    {
        return $this->all()
            ->groupBy(fn (Block $block) => $block->category())
            ->map(fn ($blocks) => $blocks->map(fn (Block $block) => $block->toArray())->values()->all())
            ->all();
    }

    /**
     * Check if a block type is registered.
     *
     * @param string $type The block type identifier
     * @return bool
     */
    public function has(string $type): bool
    {
        return isset($this->registered[$type]);
    }

    /**
     * Get default content for a block type.
     *
     * @param string $type The block type identifier
     * @return array<string, mixed> Default content, or empty array if block not found
     */
    public function defaultContent(string $type): array
    {
        return $this->get($type)?->defaultContent() ?? [];
    }

    /**
     * Get the component path for a block type.
     *
     * @param string $type The block type identifier
     * @return string|null Component path, or null if block not found
     */
    public function component(string $type): ?string
    {
        return $this->get($type)?->component();
    }
}
