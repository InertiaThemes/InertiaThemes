<?php

namespace InertiaThemes\Contracts;

interface Theme
{
    /**
     * Get the theme ID
     */
    public function id(): string;

    /**
     * Get the theme display name
     */
    public function name(): string;

    /**
     * Get the theme description
     */
    public function description(): string;

    /**
     * Get the theme color palette
     */
    public function colors(): array;

    /**
     * Get the preview image URL
     */
    public function preview(): ?string;

    /**
     * Get default blocks for this theme
     */
    public function defaultBlocks(): array;

    /**
     * Get default content for a specific block type
     */
    public function defaultContent(string $blockType): array;

    /**
     * Get block component override path (null if using base)
     */
    public function blockOverride(string $blockType): ?string;

    /**
     * Convert theme to array for Inertia sharing
     */
    public function toArray(): array;
}
