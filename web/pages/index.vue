<template>
  <div>
    <v-container class="pt-6 pt-md-10 pb-8 pb-md-14 homepage-container">
      <!-- Editorial Collage Hero Section (Inside same container for 100% width consistency) -->
      <EditorialCollageHero @scroll-to-registration="scrollToRegistration" />

      <!-- Stats Section -->
      <HomeStatsSection :stats="stats" class="mt-8 mt-md-14" />

      <!-- Community Stories Section (Cerita Jalan Bareng) -->
      <HomeCommunityStoriesSection />

      <!-- Weekly Registration Hub (Drop Link Pendaftaran Pekan Ini) -->
      <WeeklyRegistrationHub />

      <!-- Featured Activation Section -->
      <HomeFeaturedActivationSection :featured-activation="featuredActivation" />

      <!-- Explore by Category Section -->
      <HomeCategorySection :categories="destinationCategories" :loading="loadingCategories" />

      <!-- Active Activations Grid -->
      <HomeActiveActivationsSection :activations="activeActivations" />

      <!-- Purposeful Bottom CTA Section -->
      <v-card v-if="!authStore.isLoggedIn" elevation="0" rounded="xl" class="pa-5 pa-sm-8 pa-md-12 text-center border antislop-cta-card">
        <v-avatar color="#FACC15" size="64" class="mb-4 elevation-2">
          <v-icon size="36" color="#111827">mdi-foot-print</v-icon>
        </v-avatar>
        <h2 :class="$vuetify.display.mobile ? 'text-h5' : 'text-h3'" class="text-white font-weight-bold mb-3">
          Langkah Pertama Dimulai Bersama Kami
        </h2>
        <p :class="$vuetify.display.mobile ? 'text-body-2' : 'text-h6'" class="text-white mb-6 mb-md-8 mx-auto px-2 px-md-4"
          style="max-width: 620px; opacity: 0.92; line-height: 1.6;">
          Daftar gratis untuk mulai terhubung dengan sesama penjelajah kota di kotamu. Ikuti jalan santai mingguan dan bagikan catatan perjalananmu.
        </p>
        <div class="d-flex justify-center">
          <v-btn
            to="/register"
            color="primary"
            rounded="pill"
            class="join-community-btn font-weight-bold elevation-4"
          >
            <v-icon start size="20">mdi-account-plus</v-icon>
            <span class="btn-label">Gabung Bersama Jalan Bareng</span>
          </v-btn>
        </div>
      </v-card>
    </v-container>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default'
})

useSeoMeta({
  title: 'Jalan Bareng - Menjelajah Bersama Komunitas',
  ogTitle: 'Jalan Bareng - Menjelajah Bersama Komunitas',
  description: 'Platform komunitas untuk menjelajah kota bersama Jalan Bareng, berbagi destinasi menarik, dan mengikuti event seru di berbagai kota.',
  ogDescription: 'Platform komunitas untuk menjelajah kota bersama Jalan Bareng, berbagi destinasi menarik, dan mengikuti event seru di berbagai kota.',
  ogImage: 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

import EditorialCollageHero from '~/components/home/EditorialCollageHero.vue'
import WeeklyRegistrationHub from '~/components/home/WeeklyRegistrationHub.vue'
import HomeStatsSection from '~/components/home/StatsSection.vue'
import HomeCommunityStoriesSection from '~/components/home/CommunityStoriesSection.vue'
import HomeFeaturedActivationSection from '~/components/home/FeaturedActivationSection.vue'
import HomeCategorySection from '~/components/home/CategorySection.vue'
import HomeActiveActivationsSection from '~/components/home/ActiveActivationsSection.vue'

const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const scrollToRegistration = () => {
  const el = document.getElementById('registrasi')
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' })
  }
}

const featuredActivation = ref(null)
const activeActivations = ref([])

const stats = ref([
  { icon: 'mdi-account-multiple-check', value: '10.000+', label: 'Pendaftar Database', color: '#DC2626' },
  { icon: 'mdi-account-group', value: '1.000+', label: 'Anggota Aktif', color: '#111827' },
  { icon: 'mdi-handshake-outline', value: '80+', label: 'Mitra Strategis', color: '#D97706' },
  { icon: 'mdi-calendar-check', value: '0', label: 'Agenda & Event', color: '#059669' },
])

const destinationCategories = ref([])
const loadingCategories = ref(true)

const { getImageUrl } = useImageUrl()

// Helper function to get category image URL
const getCategoryImageUrl = (category: any) => {
  if (category.photo_url) {
    return category.photo_url
  }
  if (category.photo) {
    const cleanPath = category.photo.replace(/^public\//, '')
    return getImageUrl(cleanPath)
  }
  return 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&h=400&fit=crop'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}

// Fetch data on mount
onMounted(async () => {
  // Fetch activations
  try {
    const activationsResponse = await api.get('/activations')
    const activations = activationsResponse.data.data || activationsResponse.data

    // Filter active activations
    const active = activations.filter((a: any) => a.is_active)

    // Set featured activation (prioritize is_featured, otherwise first active)
    featuredActivation.value = active.find((a: any) => a.is_featured) || active[0]

    // Set active activations (limit to 6)
    activeActivations.value = active.slice(0, 6)

    // Dynamic events count for stats
    try {
      const eventsResponse = await api.get('/events')
      const totalEvents = eventsResponse.data.total ?? eventsResponse.data.data?.length ?? (Array.isArray(eventsResponse.data) ? eventsResponse.data.length : 0)
      if (totalEvents > 0) {
        stats.value[3].value = totalEvents.toString() + '+'
      } else {
        stats.value[3].value = '50+'
      }
    } catch (error) {
      stats.value[3].value = '50+'
    }
  } catch (error) {
    console.error('Error fetching activations:', error)
  }

  // Fetch categories
  try {
    loadingCategories.value = true
    const response = await api.get('/categories')
    const categories = response.data.categories || []

    destinationCategories.value = categories.map((cat: any) => ({
      id: cat.id,
      name: cat.name,
      description: cat.description || '',
      icon: cat.icon || 'mdi-map-marker',
      image: getCategoryImageUrl(cat),
      count: cat.destinations_count || 0
    }))
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loadingCategories.value = false
  }
})
</script>

<style scoped>
.hero-section {
  position: relative;
  overflow: hidden;
  background-color: #FFFFFF;
  border-bottom: 1px solid #F3F4F6;
}

.hero-pill-tag {
  background: #FEE2E2;
  color: #991B1B;
  font-weight: 800;
  font-size: 0.76rem;
  letter-spacing: 0.05em;
  border: 1px solid #FECACA;
}

.hero-pill-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #DC2626;
  display: inline-block;
}

.hero-subtitle {
  color: #4B5563;
  line-height: 1.65;
}

.hero-primary-btn {
  letter-spacing: 0.02em;
  transition: all 0.2s ease;
}

.hero-primary-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3) !important;
}

.antislop-cta-card {
  background: #111827;
  border-color: rgba(255, 255, 255, 0.1) !important;
}

.join-community-btn {
  max-width: 100%;
  width: auto;
  min-height: 48px;
  height: auto !important;
  padding: 12px 32px !important;
  font-size: 1rem !important;
  letter-spacing: 0.02em !important;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.join-community-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4) !important;
}

.join-community-btn .btn-label {
  white-space: normal;
  line-height: 1.25;
}

@media (max-width: 600px) {
  .join-community-btn {
    width: 100%;
    max-width: 320px;
    padding: 12px 18px !important;
    font-size: 0.88rem !important;
  }
}

.wave-divider {
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
}

.wave-divider svg {
  display: block;
  width: 100%;
  height: auto;
}

.animate-fade-in {
  animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Consistent Card Styles - Clean Design System */

/* Featured Event Card */
.featured-event-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

.featured-event-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.featured-image-wrapper {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.featured-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-event-card:hover .featured-image {
  transform: scale(1.1);
}

.featured-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.1) 100%);
}

.featured-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 2;
}

/* Event Cards */
.event-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.event-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.event-image-wrapper {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.event-card:hover .event-image {
  transform: scale(1.1);
}

.event-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.05) 100%);
}

.event-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
}

/* Destination Cards */
.destination-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.destination-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.destination-image-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.destination-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.destination-card:hover .destination-image {
  transform: scale(1.1);
}

.destination-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 100%);
}

.destination-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .featured-image-wrapper {
    height: 250px;
  }

  .event-image-wrapper {
    height: 180px;
  }

  .destination-image-wrapper {
    height: 160px;
  }
}

/* Scrolling Animation for Members */
.scrolling-wrapper {
  overflow: hidden;
  position: relative;
  width: 100%;
  -webkit-mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
  mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
}

.scrolling-content {
  display: flex;
  width: fit-content;
  animation: scroll 60s linear infinite;
}

.member-card {
  flex-shrink: 0;
  width: 150px;
  text-align: center;
  margin: 0 20px;
}

@keyframes scroll {
  from {
    transform: translateX(0);
  }

  to {
    transform: translateX(-50%);
  }
}

/* Category Section Styles */
.category-section {
  padding: 32px 0;
}

.category-intro {
  padding-right: 24px;
}

.category-label {
  letter-spacing: 2px;
  font-size: 12px;
}

.category-title {
  line-height: 1.2;
}

.category-cards-wrapper {
  position: relative;
}

.category-card {
  cursor: pointer;
  border-radius: 16px;
  overflow: hidden;
}

.category-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.category-card:hover .category-image {
  transform: scale(1.1);
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
  transition: all 0.3s ease;
}

.category-card:hover .category-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0.3) 100%);
}

.category-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 32px 24px;
  color: white;
  z-index: 2;
}

.category-icon-wrapper {
  width: 56px;
  height: 56px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.category-card:hover .category-icon-wrapper {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.1);
}

.category-name {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.category-description {
  font-size: 14px;
  opacity: 0.9;
  margin-bottom: 16px;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

.category-button {
  font-size: 13px;
  font-weight: 600;
  text-transform: none;
  letter-spacing: 0;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.category-card:hover .category-button {
  opacity: 1;
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 960px) {
  .category-intro {
    padding-right: 0;
    margin-bottom: 32px;
    text-align: center;
  }

  .category-image-wrapper {
    height: 320px;
  }

  .category-button {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 600px) {
  .category-image-wrapper {
    height: 280px;
  }

  .category-name {
    font-size: 20px;
  }

  .category-description {
    font-size: 13px;
  }
}

/* Activation Cards Styles */
.featured-activation-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.featured-activation-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.featured-activation-image-wrapper {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.featured-activation-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-activation-card:hover .featured-activation-image {
  transform: scale(1.1);
}

.activation-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.activation-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.activation-image-wrapper {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.activation-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.activation-card:hover .activation-image {
  transform: scale(1.1);
}

.activation-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.05) 100%);
}

.activation-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
}

@media (max-width: 600px) {
  .featured-activation-image-wrapper {
    height: 250px;
  }

  .activation-image-wrapper {
    height: 180px;
  }
}
</style>
