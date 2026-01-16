<script>
    import { page } from '@inertiajs/svelte'

    /**
     * Filter blocks by area (e.g., 'header', 'content', 'footer')
     * If not provided, renders all blocks
     */
    export let area = null

    /**
     * Override blocks array (instead of reading from page props)
     */
    export let blocks = null

    /**
     * Override theme ID (instead of reading from page props)
     */
    export let theme = null

    // Discover all theme block components using Vite's glob import
    // This is evaluated at build time, making it SSR-compatible
    const themeModules = import.meta.glob('/resources/js/themes/**/blocks/*.svelte', { eager: true })

    // Get theme ID from props or page
    $: themeId = theme || $page.props.theme?.id || 'default'

    // Get blocks from props or page
    $: allBlocks = blocks || $page.props.blocks || []

    // Filter by area if specified
    $: filteredBlocks = area
        ? allBlocks.filter(block => block.area === area)
        : allBlocks

    // Resolve component for a block
    function getComponent(blockType) {
        // Try theme-specific component first
        const themePath = `/resources/js/themes/${themeId}/blocks/${blockType}.svelte`
        if (themeModules[themePath]) {
            return themeModules[themePath].default
        }

        // Fall back to base theme
        const basePath = `/resources/js/themes/_base/blocks/${blockType}.svelte`
        if (themeModules[basePath]) {
            return themeModules[basePath].default
        }

        // Fall back to default theme
        const defaultPath = `/resources/js/themes/default/blocks/${blockType}.svelte`
        if (themeModules[defaultPath]) {
            return themeModules[defaultPath].default
        }

        return null
    }
</script>

{#each filteredBlocks as block (block.id)}
    {#if getComponent(block.type)}
        <svelte:component
            this={getComponent(block.type)}
            content={block.content || {}}
            settings={block.settings || {}}
            blockId={block.id}
            blockType={block.type}
        />
    {:else}
        <div class="inertiathemes-missing-block">
            Block component not found: {block.type}
        </div>
    {/if}
{/each}

<style>
    .inertiathemes-missing-block {
        padding: 1rem;
        background: #fef2f2;
        border: 1px dashed #ef4444;
        color: #991b1b;
        text-align: center;
        font-family: monospace;
    }
</style>
