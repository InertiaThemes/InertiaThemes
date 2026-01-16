import { usePage } from '@inertiajs/react'
import { lazy, Suspense, useMemo } from 'react'

// Discover all theme block components using Vite's glob import
// This is evaluated at build time, making it SSR-compatible
const themeModules = import.meta.glob('/resources/js/themes/**/blocks/*.jsx')

/**
 * Blocks component - renders theme-aware blocks with optional area filtering
 *
 * @param {Object} props
 * @param {string} [props.area] - Filter blocks by area (e.g., 'header', 'content', 'footer')
 * @param {Array} [props.blocks] - Override blocks array (instead of reading from page props)
 * @param {string} [props.theme] - Override theme ID (instead of reading from page props)
 */
export default function Blocks({ area = null, blocks = null, theme = null }) {
    const { props: pageProps } = usePage()

    // Get theme ID from props or page
    const themeId = theme || pageProps.theme?.id || 'default'

    // Get blocks from props or page
    const allBlocks = blocks || pageProps.blocks || []

    // Filter by area if specified
    const filteredBlocks = useMemo(() => {
        if (!area) return allBlocks
        return allBlocks.filter(block => block.area === area)
    }, [area, allBlocks])

    // Resolve component for a block
    const getComponent = (blockType) => {
        // Try theme-specific component first
        const themePath = `/resources/js/themes/${themeId}/blocks/${blockType}.jsx`
        if (themeModules[themePath]) {
            return lazy(themeModules[themePath])
        }

        // Fall back to base theme
        const basePath = `/resources/js/themes/_base/blocks/${blockType}.jsx`
        if (themeModules[basePath]) {
            return lazy(themeModules[basePath])
        }

        // Fall back to default theme
        const defaultPath = `/resources/js/themes/default/blocks/${blockType}.jsx`
        if (themeModules[defaultPath]) {
            return lazy(themeModules[defaultPath])
        }

        return null
    }

    return (
        <>
            {filteredBlocks.map((block) => {
                const Component = getComponent(block.type)

                if (!Component) {
                    return (
                        <div
                            key={block.id}
                            style={{
                                padding: '1rem',
                                background: '#fef2f2',
                                border: '1px dashed #ef4444',
                                color: '#991b1b',
                                textAlign: 'center',
                                fontFamily: 'monospace',
                            }}
                        >
                            Block component not found: {block.type}
                        </div>
                    )
                }

                return (
                    <Suspense key={block.id} fallback={null}>
                        <Component
                            content={block.content || {}}
                            settings={block.settings || {}}
                            blockId={block.id}
                            blockType={block.type}
                        />
                    </Suspense>
                )
            })}
        </>
    )
}
