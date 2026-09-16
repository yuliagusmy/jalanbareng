export const useImageUrl = () => {
    const config = useRuntimeConfig()

    const getImageUrl = (path: string | null | undefined): string => {
        // Return placeholder if no path
        if (!path) {
            return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&h=600&fit=crop'
        }

        // If already a full URL, return as is
        if (path.startsWith('http://') || path.startsWith('https://')) {
            return path
        }

        // Use apiBase which is already without /api suffix
        // e.g., "https://api.jalanbareng.net"
        const apiBase = config.public.apiBase

        // Remove leading slash from path if exists
        const cleanPath = path.startsWith('/') ? path.substring(1) : path

        return `${apiBase}/storage/${cleanPath}`
    }

    return {
        getImageUrl
    }
}
