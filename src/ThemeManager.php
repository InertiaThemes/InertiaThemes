<?php

namespace InertiaThemes;

use Illuminate\Support\Collection;
use InertiaThemes\Contracts\Theme;

class ThemeManager
{
    /**
     * Registered theme classes (not instantiated)
     */
    protected array $registered = [];

    /**
     * Instantiated theme cache
     */
    protected array $instances = [];

    /**
     * Current theme ID
     */
    protected ?string $currentThemeId = null;

    /**
     * Config array
     */
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Register a theme class by ID
     */
    public function register(string $id, string $themeClass): self
    {
        if (!is_subclass_of($themeClass, Theme::class)) {
            throw new \InvalidArgumentException(
                "Theme [{$themeClass}] must implement " . Theme::class
            );
        }

        $this->registered[$id] = $themeClass;

        return $this;
    }

    /**
     * Register a theme class (auto-discovers ID from class)
     */
    public function registerClass(string $themeClass): self
    {
        if (!is_subclass_of($themeClass, Theme::class)) {
            throw new \InvalidArgumentException(
                "Theme [{$themeClass}] must implement " . Theme::class
            );
        }

        // Instantiate to get the ID
        $instance = new $themeClass();
        $id = $instance->id();

        $this->registered[$id] = $themeClass;
        $this->instances[$id] = $instance;

        return $this;
    }

    /**
     * Set the current theme by ID (lazy loads only this theme)
     */
    public function use(string $id): self
    {
        if (!isset($this->registered[$id])) {
            $id = $this->config['default'] ?? array_key_first($this->registered);
        }

        $this->currentThemeId = $id;

        // Lazy load only this theme
        $this->resolve($id);

        return $this;
    }

    /**
     * Resolve/instantiate a single theme (lazy)
     */
    protected function resolve(string $id): ?Theme
    {
        // Return cached instance if exists
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        // Check if registered
        if (!isset($this->registered[$id])) {
            return null;
        }

        // Instantiate and cache
        $themeClass = $this->registered[$id];
        $this->instances[$id] = new $themeClass();

        return $this->instances[$id];
    }

    /**
     * Get the current theme (only loads current theme)
     */
    public function current(): ?Theme
    {
        $id = $this->currentThemeId ?? $this->config['default'] ?? null;

        if (!$id) {
            return null;
        }

        return $this->resolve($id);
    }

    /**
     * Get a specific theme by ID (lazy loads only that theme)
     */
    public function get(string $id): ?Theme
    {
        return $this->resolve($id);
    }

    /**
     * Get all registered theme IDs (without loading them)
     */
    public function registered(): array
    {
        return array_keys($this->registered);
    }

    /**
     * Load ALL themes - use sparingly (e.g., admin theme picker)
     * This is explicit so developers know they're loading everything
     */
    public function loadAll(): Collection
    {
        foreach ($this->registered as $id => $class) {
            $this->resolve($id);
        }

        return collect($this->instances);
    }

    /**
     * Get theme list for UI - loads all themes
     * Only call this in admin/site builder contexts
     */
    public function list(): array
    {
        return $this->loadAll()->map(fn (Theme $theme) => [
            'id' => $theme->id(),
            'name' => $theme->name(),
            'description' => $theme->description(),
            'colors' => $theme->colors(),
            'preview' => $theme->preview(),
        ])->values()->all();
    }

    /**
     * Get minimal theme list (IDs and names only) - loads all themes
     */
    public function listMinimal(): array
    {
        return $this->loadAll()->map(fn (Theme $theme) => [
            'id' => $theme->id(),
            'name' => $theme->name(),
        ])->values()->all();
    }

    /**
     * Resolve block content with current theme defaults
     */
    public function resolveBlockContent(string $blockType, array $content = []): array
    {
        $theme = $this->current();

        if (!$theme) {
            return $content;
        }

        return array_merge($theme->defaultContent($blockType), $content);
    }

    /**
     * Create page blocks from current theme defaults
     */
    public function createPageBlocks(): array
    {
        $theme = $this->current();

        if (!$theme) {
            return [];
        }

        return collect($theme->defaultBlocks())->map(function ($blockType, $index) use ($theme) {
            return [
                'id' => 'block-' . time() . '-' . $index . '-' . substr(md5(mt_rand()), 0, 9),
                'type' => $blockType,
                'content' => $theme->defaultContent($blockType),
                'settings' => [],
            ];
        })->all();
    }

    /**
     * Check if a theme is registered
     */
    public function has(string $id): bool
    {
        return isset($this->registered[$id]);
    }

    /**
     * Get config value
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
