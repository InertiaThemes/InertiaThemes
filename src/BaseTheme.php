<?php

namespace InertiaThemes;

use InertiaThemes\Contracts\Theme;

/**
 * Base Theme Implementation
 *
 * Abstract base class providing default implementations for the Theme contract.
 * Extend this class to create custom themes with minimal boilerplate.
 *
 * Example:
 * ```php
 * class ClassicOrangeTheme extends BaseTheme
 * {
 *     protected string $id = 'classic-orange';
 *
 *     public function name(): string
 *     {
 *         return 'Classic Orange';
 *     }
 *
 *     public function colors(): array
 *     {
 *         return [
 *             'primary' => '#FF6B00',
 *             'secondary' => '#1A1A2E',
 *             'accent' => '#FFB800',
 *         ];
 *     }
 *
 *     public function defaultBlocks(): array
 *     {
 *         return ['hero', 'features', 'cta'];
 *     }
 * }
 * ```
 *
 * @package InertiaThemes
 */
abstract class BaseTheme implements Theme
{
    /**
     * The unique theme identifier.
     *
     * Override in child class with your theme's ID.
     *
     * @var string
     */
    protected string $id = 'default';

    /**
     * Get the unique theme identifier.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * Get the human-readable theme name.
     *
     * @return string
     */
    abstract public function name(): string;

    /**
     * Get the theme description.
     *
     * Override to provide a description of the theme's style and purpose.
     *
     * @return string
     */
    public function description(): string
    {
        return '';
    }

    /**
     * Get the theme color palette.
     *
     * Returns an associative array of color definitions.
     *
     * @return array<string, string>
     */
    abstract public function colors(): array;

    /**
     * Get the preview image URL.
     *
     * Override to provide a preview/thumbnail image for the theme picker.
     *
     * @return string|null
     */
    public function preview(): ?string
    {
        return null;
    }

    /**
     * Get the default blocks for this theme.
     *
     * Override to define which blocks appear when creating a new page.
     *
     * @return array<int, string>
     */
    public function defaultBlocks(): array
    {
        return [];
    }

    /**
     * Get default content for a specific block type.
     *
     * Override to provide theme-specific default content for blocks.
     *
     * @param string $blockType The block type identifier
     * @return array<string, mixed>
     */
    public function defaultContent(string $blockType): array
    {
        return [];
    }

    /**
     * Get the component override path for a block type.
     *
     * Override to provide custom component implementations for specific blocks.
     * Return the Vue/React/Svelte component path, or null to use the base component.
     *
     * @param string $blockType The block type identifier
     * @return string|null
     */
    public function blockOverride(string $blockType): ?string
    {
        return null;
    }

    /**
     * Convert the theme to an array for Inertia sharing.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     colors: array<string, string>,
     *     preview: string|null,
     *     defaultBlocks: array<int, string>
     * }
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
