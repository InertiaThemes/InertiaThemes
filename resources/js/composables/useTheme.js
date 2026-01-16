import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * Composable for accessing theme data in Vue components
 */
export function useTheme() {
    const page = usePage()

    /**
     * The current theme object
     */
    const theme = computed(() => page.props.theme || null)

    /**
     * The theme ID
     */
    const themeId = computed(() => theme.value?.id || null)

    /**
     * The theme name
     */
    const themeName = computed(() => theme.value?.name || null)

    /**
     * The theme color palette
     */
    const colors = computed(() => theme.value?.colors || {})

    /**
     * Get a specific color from the palette
     */
    const color = (name, fallback = null) => {
        return colors.value[name] || fallback
    }

    /**
     * The default blocks for this theme
     */
    const defaultBlocks = computed(() => theme.value?.defaultBlocks || [])

    /**
     * Generate CSS custom properties from theme colors
     * Useful for applying theme colors via CSS variables
     */
    const cssVariables = computed(() => {
        const vars = {}
        for (const [key, value] of Object.entries(colors.value)) {
            vars[`--theme-${kebabCase(key)}`] = value
        }
        return vars
    })

    /**
     * Get inline style object with CSS variables
     */
    const themeStyles = computed(() => cssVariables.value)

    return {
        theme,
        themeId,
        themeName,
        colors,
        color,
        defaultBlocks,
        cssVariables,
        themeStyles,
    }
}

/**
 * Convert camelCase to kebab-case
 */
function kebabCase(str) {
    return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase()
}

export default useTheme
