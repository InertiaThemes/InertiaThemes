<?php

namespace InertiaThemes\Contracts;

/**
 * Theme Contract
 *
 * Defines the interface for theme implementations in InertiaThemes.
 * Themes provide visual styling, color palettes, and default block
 * configurations for Inertia.js applications.
 *
 * @package InertiaThemes\Contracts
 */
interface Theme
{
    /**
     * Get the unique theme identifier.
     *
     * @return string The theme ID (e.g., 'classic-orange', 'modern-blue')
     */
    public function id(): string;

    /**
     * Get the human-readable theme name.
     *
     * @return string The display name (e.g., 'Classic Orange Theme')
     */
    public function name(): string;

    /**
     * Get the theme description.
     *
     * @return string A brief description of the theme's style and purpose
     */
    public function description(): string;

    /**
     * Get the theme color palette.
     *
     * Returns an associative array of color definitions used by the theme.
     *
     * @return array<string, string> Color palette (e.g., ['primary' => '#FF6B00', 'secondary' => '#1A1A2E'])
     */
    public function colors(): array;

    /**
     * Get the preview image URL.
     *
     * @return string|null URL or path to a preview/thumbnail image, or null if unavailable
     */
    public function preview(): ?string;

    /**
     * Get the default blocks for this theme.
     *
     * Returns an array of block type identifiers that should be used
     * when creating a new page with this theme.
     *
     * @return array<int, string> List of block types (e.g., ['hero', 'features', 'cta'])
     */
    public function defaultBlocks(): array;

    /**
     * Get default content for a specific block type.
     *
     * Returns theme-specific default content values for the given block type.
     *
     * @param string $blockType The block type identifier
     * @return array<string, mixed> Default content values for the block
     */
    public function defaultContent(string $blockType): array;

    /**
     * Get the component override path for a block type.
     *
     * Allows themes to provide custom Vue/React/Svelte component implementations
     * for specific block types, overriding the base block component.
     *
     * @param string $blockType The block type identifier
     * @return string|null Component path for the override, or null to use base component
     */
    public function blockOverride(string $blockType): ?string;

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
    public function toArray(): array;
}
