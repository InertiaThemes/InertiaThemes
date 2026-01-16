<?php

namespace InertiaThemes\Contracts;

/**
 * Block Contract
 *
 * Defines the interface for block implementations in InertiaThemes.
 * Blocks are reusable content components that can be arranged on pages
 * and customized through content and settings schemas.
 *
 * @package InertiaThemes\Contracts
 */
interface Block
{
    /**
     * Get the unique block type identifier.
     *
     * @return string The block type (e.g., 'hero', 'features', 'testimonials')
     */
    public function type(): string;

    /**
     * Get the human-readable block name.
     *
     * @return string The display name (e.g., 'Hero Section', 'Feature Grid')
     */
    public function name(): string;

    /**
     * Get the block category.
     *
     * Categories are used to group blocks in the block picker UI.
     *
     * @return string The category name (e.g., 'Layout', 'Content', 'Marketing')
     */
    public function category(): string;

    /**
     * Get the block icon.
     *
     * @return string SVG path data or icon identifier for the block picker
     */
    public function icon(): string;

    /**
     * Get the frontend component path.
     *
     * Returns the path to the Vue/React/Svelte component that renders this block.
     *
     * @return string Component path relative to the components directory
     */
    public function component(): string;

    /**
     * Get the content schema for the editor.
     *
     * Defines the editable content fields and their types for the page editor.
     *
     * @return array<string, array{type: string, label: string, ...}> Field definitions
     */
    public function contentSchema(): array;

    /**
     * Get the settings schema for the editor.
     *
     * Defines the configurable settings and their types for the page editor.
     *
     * @return array<string, array{type: string, label: string, ...}> Setting definitions
     */
    public function settingsSchema(): array;

    /**
     * Get the default content values.
     *
     * Returns initial content values used when adding a new instance of this block.
     *
     * @return array<string, mixed> Default content keyed by field name
     */
    public function defaultContent(): array;

    /**
     * Get the default settings values.
     *
     * Returns initial settings values used when adding a new instance of this block.
     *
     * @return array<string, mixed> Default settings keyed by setting name
     */
    public function defaultSettings(): array;

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
    public function toArray(): array;
}
