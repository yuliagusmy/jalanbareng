<template>
    <v-footer class="modern-footer pa-0">
        <div class="footer-gradient w-100">
            <v-container class="py-16">
                <v-row justify="space-between">
                    <!-- Brand Section -->
                    <v-col cols="12" md="4" class="mb-8 mb-md-0">
                        <h2 class="text-h5 text-white font-weight-bold mb-4" style="position: relative;">
                            {{ brandTitle }}
                        </h2>
                        <p class="text-white mb-6" style="opacity: 0.9; line-height: 1.8; font-size: 15px;">
                            {{ brandDescription }}
                        </p>
                        <div class="d-flex" style="gap: 12px;">
                            <v-btn v-if="settings.footer_social_facebook" icon variant="outlined" color="white"
                                class="social-btn" size="default" :href="settings.footer_social_facebook"
                                target="_blank">
                                <v-icon size="20">mdi-facebook</v-icon>
                            </v-btn>
                            <v-btn v-if="settings.footer_social_instagram" icon variant="outlined" color="white"
                                class="social-btn" size="default" :href="settings.footer_social_instagram"
                                target="_blank">
                                <v-icon size="20">mdi-instagram</v-icon>
                            </v-btn>
                            <v-btn v-if="settings.footer_social_twitter" icon variant="outlined" color="white"
                                class="social-btn" size="default" :href="settings.footer_social_twitter"
                                target="_blank">
                                <v-icon size="20">mdi-twitter</v-icon>
                            </v-btn>
                        </div>
                    </v-col>

                    <!-- Quick Links -->
                    <v-col cols="6" md="3" class="mb-8 mb-md-0">
                        <h4 class="text-white font-weight-bold mb-4" style="font-size: 16px; position: relative;">Menu
                        </h4>
                        <div class="footer-links">
                            <NuxtLink to="/" class="footer-link">Home</NuxtLink>
                            <NuxtLink to="/destinations" class="footer-link">Destinasi</NuxtLink>
                            <NuxtLink to="/events" class="footer-link">Event</NuxtLink>
                            <NuxtLink to="/map" class="footer-link">Peta</NuxtLink>
                        </div>
                    </v-col>

                    <!-- Community Links -->
                    <v-col cols="6" md="3" class="mb-8 mb-md-0">
                        <h4 class="text-white font-weight-bold mb-4" style="font-size: 16px; position: relative;">
                            Komunitas</h4>
                        <div class="footer-links">
                            <NuxtLink to="/profile" class="footer-link">Profil</NuxtLink>
                            <NuxtLink v-for="page in pages" :key="page.id" :to="`/pages/${page.slug}`"
                                class="footer-link">
                                {{ page.title }}
                            </NuxtLink>
                        </div>
                    </v-col>
                </v-row>

                <v-divider class="my-8" style="border-color: rgba(255, 255, 255, 0.2);"></v-divider>

                <div class="text-center">
                    <p class="text-white mb-0" style="opacity: 0.85; font-size: 14px;">
                        © {{ new Date().getFullYear() }} Jalan Bareng. Dibuat dengan <span
                            style="color: #ff6b9d;">❤</span> untuk Indonesia.
                    </p>
                </div>
            </v-container>
        </div>
    </v-footer>
</template>

<script setup lang="ts">
const api = useApi()

const settings = ref<any>({})
const pages = ref<any[]>([])

const fetchFooterData = async () => {
    try {
        // Fetch settings and pages in parallel
        const [settingsRes, pagesRes] = await Promise.all([
            api.get('/settings/footer'),
            api.get('/pages/footer')
        ])

        settings.value = settingsRes.data.settings || {}
        pages.value = pagesRes.data.pages || []
    } catch (error) {
        console.error('Error fetching footer data:', error)
    }
}

const brandTitle = computed(() => settings.value.footer_brand_title || 'Jalan Bareng')
const brandDescription = computed(() => settings.value.footer_brand_description || 'Platform komunitas untuk berbagi dan menemukan destinasi menarik.')

onMounted(() => {
    fetchFooterData()
})
</script>

<style scoped>
/* Modern Footer Styles */
.modern-footer {
    position: relative;
    overflow: hidden;
    border: none !important;
}

.w-100 {
    width: 100%;
}

.footer-gradient {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    position: relative;
    overflow: hidden;
    width: 100%;
}

.footer-gradient::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.footer-gradient .v-container {
    position: relative;
    z-index: 1;
}

.footer-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.footer-link {
    color: white;
    text-decoration: none;
    opacity: 0.9;
    transition: all 0.2s ease;
    display: inline-block;
    font-size: 15px;
}

.footer-link:hover {
    opacity: 1;
    transform: translateX(4px);
    color: white;
}

.social-btn {
    transition: all 0.3s ease;
    border-width: 2px;
}

.social-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px);
}
</style>
