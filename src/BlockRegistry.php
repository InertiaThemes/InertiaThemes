<?php

namespace InertiaThemes;

use Illuminate\Support\Collection;
use InertiaThemes\Contracts\Block;

class BlockRegistry
{
    /**
     * Registered block classes (not instantiated)
     */
    protected array $registered = [];

    /**
     * Instantiated block cache
     */
    protected array $instances = [];

    /**
     * Register a block class
     */
    public function register(string $blockClass): self
    {
        if (!is_subclass_of($blockClass, Block::class)) {
            throw new \InvalidArgumentException(
                "Block [{$blockClass}] must implement " . Block::class
            );
        }

        // Get the type from a temporary instance
        $instance = new $blockClass();
        $this->registered[$instance->type()] = $blockClass;
        $this->instances[$instance->type()] = $instance;

        return $this;
    }

    /**
     * Register multiple block classes
     */
    public function registerMany(array $blockClasses): self
    {
        foreach ($blockClasses as $blockClass) {
            $this->register($blockClass);
        }

        return $this;
    }

    /**
     * Get a block by type
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
     * Get all registered block types
     */
    public function types(): array
    {
        return array_keys($this->registered);
    }

    /**
     * Get all blocks
     */
    public function all(): Collection
    {
        // Ensure all are instantiated
        foreach ($this->registered as $type => $class) {
            if (!isset($this->instances[$type])) {
                $this->instances[$type] = new $class();
            }
        }

        return collect($this->instances);
    }

    /**
     * Get block list for UI (block picker)
     */
    public function list(): array
    {
        return $this->all()->map(fn (Block $block) => $block->toArray())->values()->all();
    }

    /**
     * Get blocks grouped by category
     */
    public function byCategory(): array
    {
        return $this->all()
            ->groupBy(fn (Block $block) => $block->category())
            ->map(fn ($blocks) => $blocks->map(fn (Block $block) => $block->toArray())->values()->all())
            ->all();
    }

    /**
     * Check if a block type is registered
     */
    public function has(string $type): bool
    {
        return isset($this->registered[$type]);
    }

    /**
     * Get default content for a block type
     */
    public function defaultContent(string $type): array
    {
        return $this->get($type)?->defaultContent() ?? [];
    }

    /**
     * Get component path for a block type
     */
    public function component(string $type): ?string
    {
        return $this->get($type)?->component();
    }
}
