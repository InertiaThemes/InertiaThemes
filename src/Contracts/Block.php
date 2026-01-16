<?php

namespace InertiaThemes\Contracts;

interface Block
{
    /**
     * Get the block type identifier (e.g., 'hero', 'features')
     */
    public function type(): string;

    /**
     * Get the display name
     */
    public function name(): string;

    /**
     * Get the block category (e.g., 'Layout', 'Content')
     */
    public function category(): string;

    /**
     * Get the icon (SVG path or icon name)
     */
    public function icon(): string;

    /**
     * Get the Vue component path
     */
    public function component(): string;

    /**
     * Get the content schema for the editor
     * Returns array of field definitions
     */
    public function contentSchema(): array;

    /**
     * Get the settings schema for the editor
     */
    public function settingsSchema(): array;

    /**
     * Get default content values
     */
    public function defaultContent(): array;

    /**
     * Get default settings values
     */
    public function defaultSettings(): array;

    /**
     * Convert to array for frontend
     */
    public function toArray(): array;
}
