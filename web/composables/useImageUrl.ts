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

        // Destinations Seed Photos -> local storage assets
        'pantai-1.jpg': '/storage/destinations/pantai-1.jpg',
        'pantai-2.jpg': '/storage/destinations/pantai-2.jpg',
        'coto.jpg': '/storage/destinations/coto.jpg',
        'seafood.jpg': '/storage/destinations/seafood.jpg',
        'island.jpg': '/storage/destinations/island.jpg',
        'fort.jpg': '/storage/destinations/fort.jpg',
        'mosque.jpg': '/storage/destinations/mosque.jpg',
        'monument.jpg': '/storage/destinations/monument.jpg',
        'park.jpg': '/storage/destinations/park.jpg',
        'bridge.jpg': '/storage/destinations/bridge.jpg',
        'mall.jpg': '/storage/destinations/mall.jpg',
        'market.jpg': '/storage/destinations/market.jpg',
        'food-1.jpg': '/storage/destinations/food-1.jpg',
        'food-2.jpg': '/storage/destinations/food-2.jpg',
        'food-3.jpg': '/storage/destinations/food-3.jpg',
        'dessert.jpg': '/storage/destinations/dessert.jpg',
    }

    const getImageUrl = (path: string | null | undefined): string => {
        // Return placeholder if no path
        if (!path) {
            return '/images/hero/walk_1.jpg'
        }

        // Check if path ends with any of our known static assets
        for (const [filename, targetUrl] of Object.entries(staticMap)) {
            if (path.endsWith(filename)) {
                return targetUrl
            }
        }

        // If path is an unseeded placeholder or generic photo id, provide stable local fallbacks
        if (path.includes('placeholder-') || path.includes('photo-')) {
            const num = parseInt(path.replace(/\D/g, '') || '1', 10)
            const walkIdx = ((num % 14) + 1)
            return `/images/hero/walk_${walkIdx}.jpg`
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
