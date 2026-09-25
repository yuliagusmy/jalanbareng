export const useImageUrl = () => {
    const config = useRuntimeConfig()

    // Mapping seed/demo asset names to local high-speed bundled images or curated CDNs
    const staticMap: Record<string, string> = {
        // Activations Heroes (bundled in /images/activations/)
        'hero_bone.jpg': '/images/activations/hero_bone.jpg',
        'hero_creative_space.jpg': '/images/activations/hero_creative_space.jpg',
        'hero_diskusi_buku.jpg': '/images/activations/hero_diskusi_buku.jpg',
        'hero_djari_djemari.jpg': '/images/activations/hero_djari_djemari.jpg',
        'hero_explore_bareng.jpg': '/images/activations/hero_explore_bareng.jpg',
        'hero_gowa.jpg': '/images/activations/hero_gowa.jpg',
        'hero_indonesia.jpg': '/images/activations/hero_indonesia.jpg',
        'hero_jaksel.jpg': '/images/activations/hero_jaksel.jpg',
        'hero_lanjut_bergerak.jpg': '/images/activations/hero_lanjut_bergerak.jpg',
        'hero_makan_bareng.jpg': '/images/activations/hero_makan_bareng.jpg',
        'hero_makassar.jpg': '/images/activations/hero_makassar.jpg',
        'hero_palopo.jpg': '/images/activations/hero_palopo.jpg',

        // Events Seed Photos -> bundled local images
        'media_heritage_1.jpg': '/images/activations/hero_gowa.jpg',
        'media_heritage_2.jpg': '/images/activations/hero_jaksel.jpg',
        'media_walking_1.jpg': '/images/hero/walk_1.jpg',
        'media_walking_2.jpg': '/images/hero/walk_2.jpg',
        'media_walking_3.jpg': '/images/hero/walk_3.jpg',
        'media_walking_4.jpg': '/images/activations/hero_palopo.jpg',
        'media_social_1.jpg': '/images/activations/hero_lanjut_bergerak.jpg',
        'media_social_2.jpg': '/images/hero/walk_13.jpg',
        'media_craft_1.jpg': '/images/activations/hero_djari_djemari.jpg',
        'media_craft_2.jpg': '/images/hero/walk_6.jpg',
        'media_books_1.jpg': '/images/activations/hero_diskusi_buku.jpg',
        'media_books_2.jpg': '/images/hero/walk_8.jpg',
        'media_culinary_1.jpg': '/images/activations/hero_makan_bareng.jpg',
        'media_culinary_2.jpg': '/images/hero/walk_9.jpg',
        'media_space_1.jpg': '/images/activations/hero_creative_space.jpg',
        'media_space_2.jpg': '/images/hero/walk_10.jpg',
        'walking-1.jpg': '/images/hero/walk_1.jpg',
        'walking-2.jpg': '/images/hero/walk_2.jpg',
        'walking-3.jpg': '/images/hero/walk_3.jpg',
        'cleanup.jpg': '/images/hero/walk_7.jpg',
        'culinary.jpg': '/images/activations/hero_makan_bareng.jpg',
        'festival.jpg': '/images/hero/walk_5.jpg',
        'gathering.jpg': '/images/hero/walk_4.jpg',
        'photography.jpg': '/images/hero/walk_11.jpg',

        // Destinations Seed Photos -> curated fast-loading CDN images
        'pantai-1.jpg': 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80',
        'pantai-2.jpg': 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=800&auto=format&fit=crop&q=80',
        'coto.jpg': 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80',
        'seafood.jpg': 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?w=800&auto=format&fit=crop&q=80',
        'island.jpg': 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800&auto=format&fit=crop&q=80',
        'fort.jpg': 'https://images.unsplash.com/photo-1599833975787-5c143f373c30?w=800&auto=format&fit=crop&q=80',
        'mosque.jpg': 'https://images.unsplash.com/photo-1584551246679-0daf3d275d0f?w=800&auto=format&fit=crop&q=80',
        'monument.jpg': 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800&auto=format&fit=crop&q=80',
        'park.jpg': 'https://images.unsplash.com/photo-1519331379826-f10be5486c6f?w=800&auto=format&fit=crop&q=80',
        'bridge.jpg': 'https://images.unsplash.com/photo-1513694203232-719a280e022f?w=800&auto=format&fit=crop&q=80',
        'mall.jpg': 'https://images.unsplash.com/photo-1567449303078-57ad995bd301?w=800&auto=format&fit=crop&q=80',
        'market.jpg': 'https://images.unsplash.com/photo-1533900298318-6b8da08a523e?w=800&auto=format&fit=crop&q=80',
        'food-1.jpg': 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&auto=format&fit=crop&q=80',
        'food-2.jpg': 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=800&auto=format&fit=crop&q=80',
        'food-3.jpg': 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=800&auto=format&fit=crop&q=80',
        'dessert.jpg': 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=800&auto=format&fit=crop&q=80',
    }

    const getImageUrl = (path: string | null | undefined): string => {
        // Return placeholder if no path
        if (!path) {
            return 'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800&auto=format&fit=crop&q=80'
        }

        // Check if path ends with any of our known static assets
        for (const [filename, targetUrl] of Object.entries(staticMap)) {
            if (path.endsWith(filename)) {
                return targetUrl
            }
        }

        // If already a full URL, return as is (with mobile LAN localhost rewrite if needed)
        // If already a full URL, return as is (with mobile LAN localhost rewrite if needed)
        const isLocalNetwork = import.meta.client && /^(192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/.test(window.location.hostname)
        if (path.startsWith('http://') || path.startsWith('https://')) {
            if (isLocalNetwork && path.includes('localhost:8001')) {
                return path.replace('localhost:8001', `${window.location.hostname}:8001`)
            }
            return path
        }

        // Use apiBase which is already without /api suffix
        let apiBase = config.public.apiBase || 'http://localhost:8001'

        // On client-side mobile testing on local LAN (e.g. accessed via 192.168.x.x), replace localhost with LAN hostname
        if (isLocalNetwork && apiBase.includes('localhost')) {
            apiBase = apiBase.replace('localhost', window.location.hostname)
        }

        // Remove leading slash from path if exists
        const cleanPath = path.startsWith('/') ? path.substring(1) : path

        return `${apiBase}/storage/${cleanPath}`
    }

    return {
        getImageUrl
    }
}
