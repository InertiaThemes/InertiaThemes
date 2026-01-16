<?php

namespace InertiaThemes;

use InertiaThemes\Contracts\Theme;

abstract class BaseTheme implements Theme
{
    /**
     * The theme ID - override in child class
     */
    protected string $id = 'default';

    public function id(): string
    {
        return $this->id;
    }

    /**
     * Override in child class
     */
    abstract public function name(): string;

    /**
     * Override in child class
     */
    public function description(): string
    {
        return '';
    }

    /**
     * Override in child class to define color palette
     */
    abstract public function colors(): array;

    /**
     * Override in child class if you have a preview image
     */
    public function preview(): ?string
    {
        return null;
    }

    /**
     * Override in child class to define default blocks
     */
    public function defaultBlocks(): array
    {
        return [];
    }

    /**
     * Override in child class to define default content per block
     */
    public function defaultContent(string $blockType): array
    {
        return [];
    }

    /**
     * Override in child class to provide component overrides
     * Return the Vue component path for the block, or null to use base
     */
    public function blockOverride(string $blockType): ?string
    {
        return null;
    }

    /**
     * Convert theme to array for Inertia sharing
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'description' => $this->description(),
            'colors' => $this->colors(),
            'preview' => $this->preview(),
            'defaultBlocks' => $this->defaultBlocks(),
        ];
    }
}
