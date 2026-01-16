<?php

namespace InertiaThemes;

use InertiaThemes\Contracts\Block;

abstract class BaseBlock implements Block
{
    /**
     * The block type identifier
     */
    protected string $type;

    /**
     * The display name
     */
    protected string $name;

    /**
     * The category
     */
    protected string $category = 'Content';

    /**
     * The icon (SVG path)
     */
    protected string $icon = 'M4 6h16M4 12h16M4 18h16';

    /**
     * The Vue component path (relative to components directory)
     */
    protected string $component;

    public function type(): string
    {
        return $this->type;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function icon(): string
    {
        return $this->icon;
    }

    public function component(): string
    {
        return $this->component;
    }

    /**
     * Override to define editable content fields
     *
     * Example:
     * return [
     *     'headline' => ['type' => 'text', 'label' => 'Headline'],
     *     'description' => ['type' => 'textarea', 'label' => 'Description'],
     *     'image' => ['type' => 'image', 'label' => 'Background Image'],
     * ];
     */
    public function contentSchema(): array
    {
        return [];
    }

    /**
     * Override to define block settings
     *
     * Example:
     * return [
     *     'showBadge' => ['type' => 'boolean', 'label' => 'Show Badge'],
     *     'style' => ['type' => 'select', 'label' => 'Style', 'options' => [...]],
     * ];
     */
    public function settingsSchema(): array
    {
        return [];
    }

    /**
     * Override to provide default content values
     */
    public function defaultContent(): array
    {
        return [];
    }

    /**
     * Override to provide default settings values
     */
    public function defaultSettings(): array
    {
        return [];
    }

    /**
     * Convert to array for frontend
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
