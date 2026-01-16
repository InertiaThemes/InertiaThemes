# InertiaThemes

A theming system for Laravel/Inertia apps. Works with Vue, React, and Svelte.

Visit our website at InertiaThemes.com for further information

[![InertiaThemes](https://img.shields.io/badge/Go%20to-inertiathemes.com-2563EB?style=for-the-badge)](https://inertiathemes.com)

## Install

```bash
composer require inertiathemes/inertiathemes
```

Publish the component for your framework:

```bash
# Vue
php artisan vendor:publish --tag=inertiathemes-vue

# React
php artisan vendor:publish --tag=inertiathemes-react

# Svelte
php artisan vendor:publish --tag=inertiathemes-svelte
```

## How It Works

1. You create **themes** (PHP classes with colors and settings)
2. You create **blocks** (PHP classes that define content schemas)
3. You create **block components** (Vue/React/Svelte files in theme folders)
4. Use the `<Blocks>` component to render them

The active theme determines which components get rendered.

## Quick Start

### 1. Create a Theme

```php
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
        ];
    }
}
```

### 2. Create a Block

```php
// app/Blocks/HeroBlock.php
namespace App\Blocks;

use InertiaThemes\BaseBlock;

class HeroBlock extends BaseBlock
{
    protected string $type = 'Hero';
    protected string $name = 'Hero Section';
    protected string $category = 'Content';
    protected string $component = 'Hero';

    public function contentSchema(): array
    {
        return [
            'headline' => ['type' => 'text', 'label' => 'Headline'],
            'subheadline' => ['type' => 'textarea', 'label' => 'Subheadline'],
        ];
    }

    public function defaultContent(): array
    {
        return [
            'headline' => 'Welcome',
            'subheadline' => 'This is the subheadline',
        ];
    }
}
```

### 3. Create Block Components

Put your components in theme folders:

```
resources/js/themes/
  default/
    blocks/
      Hero.vue
      Footer.vue
  modern/
    blocks/
      Hero.vue
      Footer.vue
```

Example Vue component:

```vue
<!-- resources/js/themes/default/blocks/Hero.vue -->
<script setup>
defineProps({
    content: Object,
    settings: Object,
})
</script>

<template>
    <section class="hero">
        <h1>{{ content.headline }}</h1>
        <p>{{ content.subheadline }}</p>
    </section>
</template>
```

### 4. Set Up Routes

Use the theme middleware:

```php
// routes/web.php
Route::middleware('theme:default')->group(function () {
    Route::get('/', [PageController::class, 'home']);
});
```

Or create custom middleware to resolve theme dynamically:

```php
// app/Http/Middleware/SetOrganizationTheme.php
namespace App\Http\Middleware;

use InertiaThemes\Middleware\SetTheme;
use Illuminate\Http\Request;

class SetOrganizationTheme extends SetTheme
{
    protected function resolveFromRequest(Request $request): ?string
    {
        // Return theme ID from database, session, etc.
        return $request->user()?->organization?->theme_id;
    }
}
```

### 5. Pass Blocks from Controller

```php
use Inertia\Inertia;

class PageController extends Controller
{
    public function home()
    {
        return Inertia::render('Home', [
            'blocks' => [
                [
                    'id' => 'block-1',
                    'type' => 'Hero',
                    'area' => 'content',
                    'content' => ['headline' => 'Hello', 'subheadline' => 'World'],
                    'settings' => [],
                ],
            ],
        ]);
    }
}
```

### 6. Render Blocks

```vue
<script setup>
import Blocks from '@/Components/Blocks.vue'
</script>

<template>
    <Blocks />
</template>
```

That's it. The `<Blocks>` component automatically:
- Reads the theme from Inertia shared props
- Reads the blocks from page props
- Renders the right component for each block based on the active theme

## Block Placement (Areas)

Use the `area` prop to render blocks in specific sections:

```vue
<template>
    <header>
        <Blocks area="header" />
    </header>

    <main>
        <Blocks area="content" />
    </main>

    <footer>
        <Blocks area="footer" />
    </footer>
</template>
```

Blocks are filtered by their `area` property. Without the `area` prop, all blocks render.

## Theme Fallbacks

Components are resolved in this order:

1. `themes/{current-theme}/blocks/{BlockType}.vue`
2. `themes/_base/blocks/{BlockType}.vue`
3. `themes/default/blocks/{BlockType}.vue`

Use `_base` for shared components, theme folders for overrides.

## API Reference

### Theme Facade

```php
use InertiaThemes\Facades\Theme;

Theme::use('theme-id');        // Set current theme
Theme::current();              // Get current theme
Theme::get('theme-id');        // Get specific theme
Theme::list();                 // Get all themes
Theme::has('theme-id');        // Check if theme exists
```

### Blocks Facade

```php
use InertiaThemes\Facades\Blocks;

Blocks::list();                // All blocks
Blocks::get('hero');           // Get specific block
Blocks::byCategory();          // Blocks grouped by category
Blocks::has('hero');           // Check if block exists
```

### useTheme Composable (Vue)

```vue
<script setup>
import { useTheme } from '@/composables/useTheme'

const { theme, themeId, colors, themeStyles } = useTheme()
</script>

<template>
    <div :style="themeStyles">
        Current theme: {{ themeId }}
    </div>
</template>
```

## Config

Publish the config file:

```bash
php artisan vendor:publish --tag=inertiathemes-config
```

```php
// config/inertiathemes.php
return [
    'default' => 'default',
    'auto_discover' => true,
    'paths' => [
        'themes' => app_path('Themes'),
        'blocks' => app_path('Blocks'),
    ],
];
```

## License

MIT
