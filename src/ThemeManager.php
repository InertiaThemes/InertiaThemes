<?php

namespace InertiaThemes;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use InertiaThemes\Contracts\Theme;

/**
 * Theme Manager
 *
 * Central manager for theme registration and resolution in InertiaThemes.
 * Implements lazy loading to only instantiate themes when needed.
 *
 * Themes are registered by class name and instantiated on-demand.
 * Use the Theme facade for convenient static access.
 *
 * @package InertiaThemes
 */
class ThemeManager
{
    /**
     * Registered theme classes indexed by ID.
     *
     * @var array<string, class-string<Theme>>
     */
    protected array $registered = [];

    /**
     * Instantiated theme cache indexed by ID.
     *
     * @var array<string, Theme>
     */
    protected array $instances = [];

    /**
     * The currently active theme ID.
     *
     * @var string|null
     */
    protected ?string $currentThemeId = null;

    /**
     * Configuration array from inertiathemes config.
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * Create a new ThemeManager instance.
     *
     * @param array<string, mixed> $config Configuration from inertiathemes.php
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Register a theme class with an explicit ID.
     *
     * @param string $id The theme identifier
     * @param class-string<Theme> $themeClass The fully qualified theme class name
     * @return $this
     *
     * @throws InvalidArgumentException If the class doesn't implement Theme
     */
    public function register(string $id, string $themeClass): self
    {
        if (!is_subclass_of($themeClass, Theme::class)) {
            throw new InvalidArgumentException(
                "Theme [{$themeClass}] must implement " . Theme::class
            );
        }

        $this->registered[$id] = $themeClass;

        return $this;
    }

    /**
     * Register a theme class, auto-discovering its ID.
     *
     * Instantiates the theme to retrieve its ID, then caches the instance.
     *
     * @param class-string<Theme> $themeClass The fully qualified theme class name
     * @return $this
     *
     * @throws InvalidArgumentException If the class doesn't implement Theme
     */
    public function registerClass(string $themeClass): self
    {
        if (!is_subclass_of($themeClass, Theme::class)) {
            throw new InvalidArgumentException(
                "Theme [{$themeClass}] must implement " . Theme::class
            );
        }

        $instance = new $themeClass();
        $id = $instance->id();

        $this->registered[$id] = $themeClass;
        $this->instances[$id] = $instance;

        return $this;
    }

    /**
     * Set the current theme by ID.
     *
     * Falls back to the default theme if the requested ID is not registered.
     *
     * @param string $id The theme identifier
     * @return $this
     */
    public function use(string $id): self
    {
        if (!isset($this->registered[$id])) {
            $id = $this->config['default'] ?? array_key_first($this->registered);
        }

        $this->currentThemeId = $id;
        $this->resolve($id);

        return $this;
    }

    /**
     * Resolve and instantiate a single theme.
     *
     * Uses cached instance if available.
     *
     * @param string $id The theme identifier
     * @return Theme|null The theme instance, or null if not registered
     */
    protected function resolve(string $id): ?Theme
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->registered[$id])) {
            return null;
        }

        $themeClass = $this->registered[$id];
        $this->instances[$id] = new $themeClass();

        return $this->instances[$id];
    }

    /**
     * Get the current theme.
     *
     * Returns the active theme, falling back to the default if none is set.
     *
     * @return Theme|null The current theme, or null if none is available
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
     * Get a specific theme by ID.
     *
     * Lazy loads only the requested theme.
     *
     * @param string $id The theme identifier
     * @return Theme|null The theme instance, or null if not found
     */
    public function get(string $id): ?Theme
    {
        return $this->resolve($id);
    }

    /**
     * Get all registered theme IDs.
     *
     * Does not instantiate themes.
     *
     * @return array<int, string> List of theme IDs
     */
    public function registered(): array
    {
        return array_keys($this->registered);
    }

    /**
     * Load all registered themes.
     *
     * Use sparingly as this instantiates all themes.
     * Appropriate for admin/theme picker contexts.
     *
     * @return Collection<string, Theme>
     */
    public function loadAll(): Collection
    {
        foreach ($this->registered as $id => $class) {
            $this->resolve($id);
        }

        return collect($this->instances);
    }

    /**
     * Get the theme list for UI components.
     *
     * Returns full theme data for theme picker interfaces.
     * Loads all themes.
     *
     * @return array<int, array{id: string, name: string, description: string, colors: array, preview: string|null}>
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
     * Get a minimal theme list.
     *
     * Returns only IDs and names for lightweight dropdowns.
     * Loads all themes.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function listMinimal(): array
    {
        return $this->loadAll()->map(fn (Theme $theme) => [
            'id' => $theme->id(),
            'name' => $theme->name(),
        ])->values()->all();
    }

    /**
     * Resolve block content with current theme defaults.
     *
     * Merges the provided content with the theme's default content for the block type.
     *
     * @param string $blockType The block type identifier
     * @param array<string, mixed> $content User-provided content
     * @return array<string, mixed> Merged content with theme defaults
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
     * Create page blocks from current theme defaults.
     *
     * Generates block instances for a new page using the theme's default blocks.
     *
     * @return array<int, array{id: string, type: string, content: array, settings: array}>
     */
    public function createPageBlocks(): array
    {
        $theme = $this->current();

        if (!$theme) {
            return [];
        }

        return collect($theme->defaultBlocks())->map(function ($blockType, $index) use ($theme) {
            return [
                'id' => 'block-' . time() . '-' . $index . '-' . substr(md5((string) mt_rand()), 0, 9),
                'type' => $blockType,
                'content' => $theme->defaultContent($blockType),
                'settings' => [],
            ];
        })->all();
    }

    /**
     * Check if a theme is registered.
     *
     * @param string $id The theme identifier
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->registered[$id]);
    }

    /**
     * Get a configuration value.
     *
     * @param string $key Dot-notation config key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
