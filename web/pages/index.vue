<template>
  <div>
    <!-- Modern Hero Section with Gradient -->
    <v-sheet class="hero-section" :style="{
      background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      position: 'relative',
      overflow: 'hidden'
    }">
      <!-- Animated Background Shapes -->
      <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>

      <v-container class="hero-content"
        style="position: relative; z-index: 2; padding-top: 100px; padding-bottom: 80px">
        <v-row align="center" justify="center" class="pt-16 pb-16 pb-md-20">
          <v-col cols="12" md="8" class="text-center pb-8 pb-md-12">
            <div class="animate-fade-in">

              <h1 class="text-h5 text-sm-h4 text-md-h3 text-lg-h2 font-weight-bold text-white mb-4 mb-md-6"
                style="line-height: 1.2;">
                Bergabung dalam<br>
                <span class="gradient-text">Aktivasi Komunitas</span>
              </h1>

              <p class="text-body-1 text-sm-h6 text-md-h5 text-white mb-6 mb-md-8 mx-auto px-4"
                style="max-width: 700px; opacity: 0.95;">
                Temukan destinasi menarik, ikuti event seru, dan berbagi pengalaman
                bersama komunitas penjelajah lokal di berbagai kota
              </p>

              <div class="d-flex flex-column flex-sm-row justify-center align-center ga-4 px-4">
                <v-btn :size="$vuetify.display.mobile ? 'large' : 'x-large'" color="white" class="px-6 px-sm-8"
                  rounded="pill" elevation="8" to="/aktivasi" :block="$vuetify.display.mobile">
                  <v-icon start>mdi-map-marker-radius</v-icon>
                  Lihat Aktivasi
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>

      <!-- Wave Divider -->
      <div class="wave-divider">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"
            fill="white" />
        </svg>
      </div>
    </v-sheet>

    <v-container class="py-8 py-md-12">
      <!-- Stats Section -->
      <HomeStatsSection :stats="stats" />

      <!-- Featured Activation Section -->
      <HomeFeaturedActivationSection :featured-activation="featuredActivation" />

      <!-- Explore by Category Section -->
      <HomeCategorySection :categories="destinationCategories" :loading="loadingCategories" />

      <!-- Active Activations Grid -->
      <HomeActiveActivationsSection :activations="activeActivations" />

      <!-- CTA Section -->
      <v-card v-if="!authStore.isLoggedIn" elevation="0" rounded="xl" class="pa-6 pa-md-12 text-center"
        style="background: linear-gradient(135deg, #5d71c9 0%, #6b4591 100%);">
        <v-icon :size="$vuetify.display.mobile ? 48 : 64" color="white" class="mb-3 mb-md-4">mdi-account-group</v-icon>
        <h2 :class="$vuetify.display.mobile ? 'text-h5' : 'text-h3'" class="text-white font-weight-bold mb-3 mb-md-4">
          Bergabung dengan Komunitas
        </h2>
        <p :class="$vuetify.display.mobile ? 'text-body-2' : 'text-h6'" class="text-white mb-6 mb-md-8 mx-auto px-4"
          style="max-width: 600px; opacity: 0.9;">
          Daftar sekarang dan mulai berbagi pengalaman jalan-jalan Anda
        </p>
        <v-btn v-if="!authStore.isLoggedIn" to="/register" color="white"
          :size="$vuetify.display.mobile ? 'large' : 'x-large'" rounded="pill" class="px-8 px-md-10" elevation="8"
          :block="$vuetify.display.mobile">
          <v-icon start>mdi-google</v-icon>
          Daftar Sekarang
        </v-btn>
        <v-btn v-else to="/profile" color="white" :size="$vuetify.display.mobile ? 'large' : 'x-large'" rounded="pill"
          class="px-8 px-md-10" elevation="8" :block="$vuetify.display.mobile">
          <v-icon start>mdi-account</v-icon>
          Lihat Profil
        </v-btn>
      </v-card>
    </v-container>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'hero'
})

useSeoMeta({
  title: 'Jalan Bareng - Temukan Teman Jalanmu',
  ogTitle: 'Jalan Bareng - Temukan Teman Jalanmu',
  description: 'Platform komunitas untuk mencari teman jalan, berbagi destinasi, dan mengikuti event seru di berbagai kota.',
  ogDescription: 'Platform komunitas untuk mencari teman jalan, berbagi destinasi, dan mengikuti event seru di berbagai kota.',
  ogImage: 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

import HomeStatsSection from '~/components/home/StatsSection.vue'
import HomeFeaturedActivationSection from '~/components/home/FeaturedActivationSection.vue'
import HomeCategorySection from '~/components/home/CategorySection.vue'
import HomeActiveActivationsSection from '~/components/home/ActiveActivationsSection.vue'

const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const featuredActivation = ref(null)
const activeActivations = ref([])

const stats = ref([
  { icon: 'mdi-map-marker-radius', value: '0', label: 'Aktivasi', color: '#667eea' },
  { icon: 'mdi-map-marker', value: '0', label: 'Destinasi', color: '#764ba2' },
  { icon: 'mdi-calendar-star', value: '0', label: 'Event', color: '#f093fb' },
  { icon: 'mdi-account-group', value: '0', label: 'Anggota', color: '#4facfe' },
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

    // Update stats - activations count
    stats.value[0].value = active.length.toString()
  } catch (error) {
    console.error('Error fetching activations:', error)
  }

  // Fetch destinations for stats
  try {
    const destinationsResponse = await api.get('/destinations')
    const destinations = destinationsResponse.data.data || destinationsResponse.data
    stats.value[1].value = destinations.length.toString()
  } catch (error) {
    console.error('Error fetching destinations:', error)
  }

  // Fetch events for stats
  try {
    const eventsResponse = await api.get('/events')
    const events = eventsResponse.data.data || eventsResponse.data
    stats.value[2].value = events.length.toString()
  } catch (error) {
    console.error('Error fetching events:', error)
  }

  // Fetch users for stats
  try {
    const usersResponse = await api.get('/users')
    const users = usersResponse.data.data || usersResponse.data
    stats.value[3].value = users.length.toString()
  } catch (error) {
    console.error('Error fetching users:', error)
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
}

.hero-bg-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 20s infinite ease-in-out;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -100px;
  left: -100px;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: -50px;
  right: 100px;
  animation-delay: 5s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  right: -50px;
  animation-delay: 10s;
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0) translateX(0);
  }

  33% {
    transform: translateY(-30px) translateX(20px);
  }

  66% {
    transform: translateY(20px) translateX(-20px);
  }
}

.gradient-text {
  background: linear-gradient(135deg, #fff 0%, #f4f4f4 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
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

.hero-section {
  margin-top: -80px !important;
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
