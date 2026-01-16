<script setup>
import { computed, defineAsyncComponent } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    /**
     * Filter blocks by area (e.g., 'header', 'content', 'footer')
     * If not provided, renders all blocks
     */
    area: {
        type: String,
        default: null,
    },
    /**
     * Override blocks array (instead of reading from page props)
     */
    blocks: {
        type: Array,
        default: null,
    },
    /**
     * Override theme ID (instead of reading from page props)
     */
    theme: {
        type: String,
        default: null,
    },
})

const page = usePage()

// Get theme ID from props or page
const themeId = computed(() => props.theme || page.props.theme?.id || 'default')

// Get blocks from props or page
const allBlocks = computed(() => props.blocks || page.props.blocks || [])

// Filter by area if specified
const filteredBlocks = computed(() => {
    if (!props.area) return allBlocks.value
    return allBlocks.value.filter(block => block.area === props.area)
})

// Discover all theme block components using Vite's glob import
// This is evaluated at build time, making it SSR-compatible
const themeModules = import.meta.glob('/resources/js/themes/**/blocks/*.vue')

// Resolve component for a block
const getComponent = (blockType) => {
    // Try theme-specific component first
    const themePath = `/resources/js/themes/${themeId.value}/blocks/${blockType}.vue`
    if (themeModules[themePath]) {
        return defineAsyncComponent(themeModules[themePath])
    }

    // Fall back to base theme
    const basePath = `/resources/js/themes/_base/blocks/${blockType}.vue`
    if (themeModules[basePath]) {
        return defineAsyncComponent(themeModules[basePath])
    }

    // Fall back to default theme
    const defaultPath = `/resources/js/themes/default/blocks/${blockType}.vue`
    if (themeModules[defaultPath]) {
        return defineAsyncComponent(themeModules[defaultPath])
    }

    return null
}
</script>

<template>
    <template v-for="block in filteredBlocks" :key="block.id">
        <component
            v-if="getComponent(block.type)"
            :is="getComponent(block.type)"
            :content="block.content || {}"
            :settings="block.settings || {}"
            :block-id="block.id"
            :block-type="block.type"
        />
        <div v-else class="inertiathemes-missing-block">
            Block component not found: {{ block.type }}
        </div>
    </template>
</template>

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
