# InertiaThemes

A theming system for Laravel/Inertia applications with auto-discovery, lazy loading, and SSR support.

## Features

- **Auto-discovery** - Themes and blocks are automatically found in `app/Themes` and `app/Blocks`
- **Lazy loading** - Only the active theme is loaded, not all themes
- **SSR compatible** - Works seamlessly with Inertia SSR
- **Block system** - Define reusable blocks with schemas for page builders

## Installation

```bash
composer require inertiathemes/inertiathemes
```

Publish the config (optional):

```bash
php artisan vendor:publish --tag=inertiathemes-config
```

## Quick Start

### 1. Create a Theme

Create a file in `app/Themes/`:

```php
<?php
// app/Themes/DefaultTheme.php

namespace App\Themes;

use InertiaThemes\BaseTheme;

class DefaultTheme extends BaseTheme
{
    protected string $id = 'default';

    public function name(): string
    {
        return 'Default Theme';
    }

    public function colors(): array
    {
        return [
            'primary' => '#3B82F6',
            'secondary' => '#1F2937',
            'background' => '#FFFFFF',
        ];
    }

    public function defaultBlocks(): array
    {
        return ['header', 'hero', 'footer'];
    }

    public function defaultContent(string $blockType): array
    {
        return match($blockType) {
            'hero' => [
                'headline' => 'Welcome',
                'ctaText' => 'Get Started',
            ],
            default => [],
        };
    }
}
```

That's it! The theme is automatically discovered.

### 2. Create a Block

Create a file in `app/Blocks/`:

```php
<?php
// app/Blocks/HeroBlock.php

namespace App\Blocks;

use InertiaThemes\BaseBlock;

class HeroBlock extends BaseBlock
{
    protected string $type = 'hero';
    protected string $name = 'Hero';
    protected string $category = 'Content';
    protected string $component = 'Blocks/HeroBlock';

    public function contentSchema(): array
    {
        return [
            'headline' => ['type' => 'text', 'label' => 'Headline'],
            'image' => ['type' => 'image', 'label' => 'Background'],
        ];
    }

    public function defaultContent(): array
    {
        return [
            'headline' => 'Welcome to our site',
        ];
    }
}
```

### 3. Use in Routes

Apply the theme middleware to routes that need theming:

```php
// routes/web.php
use App\Http\Middleware\SetOrganizationTheme;

// Frontend routes - loads theme from org settings
Route::middleware(SetOrganizationTheme::class)->group(function () {
    Route::get('/', [SiteController::class, 'home']);
});

// Admin routes - no theme loaded automatically
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('site-builder', [SiteBuilderController::class, 'index']);
});
```

Create your custom middleware:

```php
<?php
// app/Http/Middleware/SetOrganizationTheme.php

namespace App\Http\Middleware;

use App\Models\Organization;
use Illuminate\Http\Request;
use InertiaThemes\Middleware\SetTheme;

class SetOrganizationTheme extends SetTheme
{
    protected function resolveFromRequest(Request $request): ?string
    {
        return Organization::first()?->settings['theme_slug'];
    }
}
```

### 4. Use in Controllers

```php
use InertiaThemes\Facades\Theme;
use InertiaThemes\Facades\Blocks;

class SiteBuilderController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SiteBuilder/Index', [
            'themes' => Theme::list(),     // Load all themes for picker
            'blocks' => Blocks::list(),    // All block definitions
        ]);
    }
}
```

### 5. Use in Vue

The theme is automatically shared when using the middleware:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3'

const theme = usePage().props.theme
</script>

<template>
    <div :style="{ backgroundColor: theme.colors.primary }">
        {{ theme.name }}
    </div>
</template>
```

## API Reference

### Theme Facade

```php
Theme::use('theme-id');        // Set current theme (lazy loads it)
Theme::current();              // Get current theme instance
Theme::get('theme-id');        // Get specific theme (lazy loads it)
Theme::registered();           // Get all registered theme IDs (no loading)
Theme::list();                 // Get all themes as array (loads all)
Theme::has('theme-id');        // Check if theme exists
```

### Blocks Facade

```php
Blocks::list();                // All blocks as array
Blocks::byCategory();          // Blocks grouped by category
Blocks::get('hero');           // Get specific block
Blocks::defaultContent('hero'); // Get block's default content
Blocks::has('hero');           // Check if block exists
```

### Manual Registration

If you prefer not to use auto-discovery:

```php
// config/inertiathemes.php
'auto_discover' => false,

// AppServiceProvider.php
public function boot()
{
    Theme::registerClass(MyTheme::class);
    Blocks::register(MyBlock::class);
}
```

## License

MIT
