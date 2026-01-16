<?php

namespace InertiaThemes;

use InertiaThemes\Contracts\Block;

/**
 * Base Block Implementation
 *
 * Abstract base class providing default implementations for the Block contract.
 * Extend this class to create custom blocks with minimal boilerplate.
 *
 * Example:
 * ```php
 * class HeroBlock extends BaseBlock
 * {
 *     protected string $type = 'hero';
 *     protected string $name = 'Hero Section';
 *     protected string $category = 'Layout';
 *     protected string $component = 'Blocks/Hero';
 *
 *     public function contentSchema(): array
 *     {
 *         return [
 *             'headline' => ['type' => 'text', 'label' => 'Headline'],
 *             'subline' => ['type' => 'textarea', 'label' => 'Subline'],
 *         ];
 *     }
 *
 *     public function defaultContent(): array
 *     {
 *         return [
 *             'headline' => 'Welcome to our site',
 *             'subline' => 'Discover amazing features',
 *         ];
 *     }
 * }
 * ```
 *
 * @package InertiaThemes
 */
abstract class BaseBlock implements Block
{
    /**
     * The unique block type identifier.
     *
     * @var string
     */
    protected string $type;

    /**
     * The human-readable display name.
     *
     * @var string
     */
    protected string $name;

    /**
     * The category for grouping in the block picker.
     *
     * @var string
     */
    protected string $category = 'Content';

    /**
     * The SVG path data for the block icon.
     *
     * @var string
     */
    protected string $icon = 'M4 6h16M4 12h16M4 18h16';

    /**
     * The frontend component path relative to the components directory.
     *
     * @var string
     */
    protected string $component;

    /**
     * Get the unique block type identifier.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Get the human-readable block name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the block category.
     *
     * @return string
     */
    public function category(): string
    {
        return $this->category;
    }

    /**
     * Get the block icon.
     *
     * @return string
     */
    public function icon(): string
    {
        return $this->icon;
    }

    /**
     * Get the frontend component path.
     *
     * @return string
     */
    public function component(): string
    {
        return $this->component;
    }

    /**
     * Get the content schema for the editor.
     *
     * Override to define editable content fields.
     *
     * Example:
     * ```php
     * return [
     *     'headline' => ['type' => 'text', 'label' => 'Headline'],
     *     'description' => ['type' => 'textarea', 'label' => 'Description'],
     *     'image' => ['type' => 'image', 'label' => 'Background Image'],
     * ];
     * ```
     *
     * @return array<string, array{type: string, label: string, ...}>
     */
    public function contentSchema(): array
    {
        return [];
    }

    /**
     * Get the settings schema for the editor.
     *
     * Override to define block settings.
     *
     * Example:
     * ```php
     * return [
     *     'showBadge' => ['type' => 'boolean', 'label' => 'Show Badge'],
     *     'style' => ['type' => 'select', 'label' => 'Style', 'options' => [...]],
     * ];
     * ```
     *
     * @return array<string, array{type: string, label: string, ...}>
     */
    public function settingsSchema(): array
    {
        return [];
    }

    /**
     * Get the default content values.
     *
     * Override to provide initial content when the block is added.
     *
     * @return array<string, mixed>
     */
    public function defaultContent(): array
    {
        return [];
    }

    /**
     * Get the default settings values.
     *
     * Override to provide initial settings when the block is added.
     *
     * @return array<string, mixed>
     */
    public function defaultSettings(): array
    {
        return [];
    }

    /**
     * Convert the block definition to an array for frontend consumption.
     *
     * @return array{
     *     type: string,
     *     name: string,
     *     category: string,
     *     icon: string,
     *     component: string,
     *     contentSchema: array,
     *     settingsSchema: array,
     *     defaultContent: array,
     *     defaultSettings: array
     * }
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'name' => $this->name(),
            'category' => $this->category(),
            'icon' => $this->icon(),
            'component' => $this->component(),
            'contentSchema' => $this->contentSchema(),
            'settingsSchema' => $this->settingsSchema(),
            'defaultContent' => $this->defaultContent(),
            'defaultSettings' => $this->defaultSettings(),
        ];
    }
}
