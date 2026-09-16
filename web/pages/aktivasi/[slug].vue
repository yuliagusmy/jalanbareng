<template>
  <div v-if="!loading && activation">
    <!-- Modern Hero Section with Hero Image Background -->
    <v-sheet class="activation-hero" :style="{
      position: 'relative',
      overflow: 'hidden'
    }">
      <!-- Hero Image Background -->
      <div v-if="activation.hero_image" class="hero-background" :style="{
        backgroundImage: `url(${apiBase}/storage/${activation.hero_image})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        zIndex: 0
      }"></div>

      <!-- Color Overlay -->
      <div class="hero-overlay" :style="{
        background: `linear-gradient(135deg, ${hexToRgba(activation.color_theme || '#667eea', 0.9)} 0%, ${hexToRgba(darkenColor(activation.color_theme || '#764ba2', 20), 0.85)} 100%)`,
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        zIndex: 1
      }"></div>

      <!-- Fallback Gradient (if no hero image) -->
      <div v-if="!activation.hero_image" class="hero-gradient" :style="{
        background: `linear-gradient(135deg, ${activation.color_theme || '#667eea'} 0%, ${darkenColor(activation.color_theme || '#764ba2', 20)} 100%)`,
        position: 'absolute',
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        zIndex: 0
      }"></div>

      <!-- Animated Background Shapes -->
      <div class="hero-bg-shapes" style="z-index: 1">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>

      <v-container class="hero-content"
        style="position: relative; z-index: 2; padding-top: 160px; padding-bottom: 160px">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Icon -->
            <v-avatar v-if="activation.icon" size="100" class="mb-6 elevation-8"
              style="border: 4px solid rgba(255,255,255,0.3)">
              <v-img :src="`${apiBase}/storage/${activation.icon}`" :alt="activation.name"></v-img>
            </v-avatar>

            <!-- Title -->
            <h1 class="text-h3 text-md-h2 font-weight-bold text-white mb-4" style="line-height: 1.2;">
              {{ activation.name }}
            </h1>

            <!-- Tagline -->
            <p v-if="activation.tagline" class="text-h6 text-md-h5 text-white mb-6" style="opacity: 0.95;">
              {{ activation.tagline }}
            </p>

            <!-- City -->
            <div v-if="activation.city" class="d-flex align-center justify-center mb-8">
              <v-icon color="white" class="mr-2">mdi-map-marker</v-icon>
              <span class="text-h6 text-white">{{ activation.city }}</span>
            </div>

            <!-- CTA Button - Scroll to About -->
            <div>
              <v-btn @click="scrollToAbout" size="x-large" color="white" class="px-10" rounded="pill" elevation="8">
                Pelajari Lebih Lanjut
                <v-icon end>mdi-arrow-down</v-icon>
              </v-btn>
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

    <v-container class="py-12">
      <!-- 1. ABOUT SECTION (URUTAN 1) - Modern & Informative with Gallery -->
      <div id="about-section" class="about-section mb-16">
        <v-row>
          <v-col cols="12" lg="10" xl="9" class="mx-auto">
            <!-- About Header -->
            <div class="mb-12">
              <h2 class="text-h3 text-md-h2 font-weight-bold mb-6"
                :style="{ color: activation.color_theme || '#1976D2' }">
                Tentang {{ activation.name }}
              </h2>
              <div class="about-content text-h6 text-md-h5 font-weight-regular text-grey-darken-2 line-height-relaxed"
                v-html="activation.description"></div>
            </div>

            <!-- Gallery Integration -->
            <div v-if="activation.show_gallery && activation.media && activation.media.length > 0"
              class="gallery-integration mt-8">
              <ActivationGallery :media="activation.media" />
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Featured Event Section -->
      <div v-if="activation.featured_event" class="mb-16">
        <div class="text-center mb-8">
          <h2 class="text-h3 font-weight-bold mb-3">Event Pilihan Minggu Ini</h2>
          <p class="text-h6 text-grey-darken-1 font-weight-regular">Jangan lewatkan event menarik dari komunitas</p>
        </div>

        <v-card elevation="0" class="featured-event-card overflow-hidden" rounded="xl">
          <v-row no-gutters>
            <v-col cols="12" md="6">
              <div class="featured-image-wrapper">
                <img v-if="activation.featured_event.poster"
                  :src="`${apiBase}/storage/${activation.featured_event.poster}`" :alt="activation.featured_event.name"
                  class="featured-image" />
                <div v-else class="featured-placeholder">
                  <v-icon size="80" color="grey-lighten-1">mdi-calendar-star</v-icon>
                </div>
                <div class="featured-overlay"></div>
                <div class="featured-badge">
                  <v-chip color="success" size="small">
                    <v-icon start size="small">mdi-calendar-check</v-icon>
                    Mendatang
                  </v-chip>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <v-card-text class="pa-6 pa-md-8">
                <h3 class="text-h5 font-weight-bold mb-4">{{ activation.featured_event.name }}</h3>

                <div class="d-flex align-center mb-3">
                  <v-icon :color="activation.color_theme || 'primary'" class="mr-2">mdi-calendar</v-icon>
                  <span>{{ formatDate(activation.featured_event.date) }}</span>
                </div>

                <p class="text-body-1 mb-6 text-grey-darken-1">
                  {{ stripHtml(activation.featured_event.description)?.substring(0, 150) }}...
                </p>

                <v-btn :color="activation.color_theme || 'primary'" size="large"
                  :to="`/events/${activation.featured_event.id}`" rounded="pill" class="px-6" block variant="flat">
                  Lihat Detail
                  <v-icon end>mdi-arrow-right</v-icon>
                </v-btn>
              </v-card-text>
            </v-col>
          </v-row>
        </v-card>
      </div>

      <!-- UPCOMING EVENTS SECTION - Moved here, limited to 3 -->
      <div v-if="activation.upcoming_events && activation.upcoming_events.length > 0"
        class="upcoming-events-section mb-16">
        <div class="text-center mb-10">
          <h2 class="text-h3 font-weight-bold mb-3">Event Mendatang</h2>
          <p class="text-h6 text-grey-darken-1 font-weight-regular">Ikuti kegiatan seru bersama komunitas</p>
        </div>

        <v-row>
          <v-col v-for="event in activation.upcoming_events.slice(0, 3)" :key="event.id" cols="12" sm="6" md="4">
            <v-card elevation="0" class="event-card-modern h-100" :to="`/events/${event.id}`" rounded="xl">
              <div class="event-image-wrapper-modern">
                <img v-if="event.poster" :src="`${apiBase}/storage/${event.poster}`" :alt="event.name"
                  class="event-image" />
                <div v-else class="event-placeholder">
                  <v-icon size="64" color="grey-lighten-1">mdi-calendar</v-icon>
                </div>
                <div class="event-overlay-modern"></div>

                <!-- Date Badge -->
                <div class="event-date-badge">
                  <div class="date-day">{{ getDay(event.date) }}</div>
                  <div class="date-month">{{ getMonth(event.date) }}</div>
                </div>

                <!-- Type Badge -->
                <div class="event-type-badge">
                  <v-chip :color="event.type === 'walking' ? 'success' : activation.color_theme || 'primary'"
                    size="small" variant="flat">
                    <v-icon start size="small">
                      {{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar' }}
                    </v-icon>
                    {{ event.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}
                  </v-chip>
                </div>
              </div>

              <v-card-text class="pa-6">
                <h4 class="text-h6 font-weight-bold mb-3 event-title">{{ event.name }}</h4>

                <div class="d-flex align-center text-grey-darken-1 mb-3">
                  <v-icon size="small" class="mr-2">mdi-clock-outline</v-icon>
                  <span class="text-caption">{{ formatDate(event.date) }}</span>
                </div>

                <p class="text-caption text-grey-darken-1 event-description mb-0">
                  {{ stripHtml(event.description)?.substring(0, 80) }}...
                </p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <div class="text-center mt-8">
          <v-btn :to="`/events?activation_id=${activation.id}`" :color="activation.color_theme || 'primary'"
            size="x-large" rounded="pill" variant="flat" class="px-10">
            Lihat Semua Event
            <v-icon end>mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- Explore by Category Section -->
      <div class="category-section mb-16">
        <v-row align="center">
          <v-col cols="12" md="4" lg="3">
            <div class="category-intro">
              <p class="category-label text-overline font-weight-bold mb-2"
                :style="{ color: activation.color_theme || '#1976D2' }">
                JELAJAHI {{ activation.city || 'MAKASSAR' }}
              </p>
              <h2 class="category-title text-h4 text-md-h3 font-weight-bold mb-4">
                Temukan Destinasi<br>
                <span :style="{ color: activation.color_theme || '#1976D2' }">Favoritmu</span>
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-6">
                Telusuri berbagai kategori destinasi menarik di {{ activation.city || 'Makassar' }},
                dari pantai eksotis hingga kuliner khas yang menggugah selera.
              </p>
              <v-btn :to="`/destinations?activation_id=${activation.id}`" :color="activation.color_theme || 'primary'"
                size="large" rounded="pill" class="px-8" variant="flat">
                JELAJAHI SEMUA
                <v-icon end>mdi-arrow-right</v-icon>
              </v-btn>
            </div>
          </v-col>

          <v-col cols="12" md="8" lg="9">
            <div class="category-cards-wrapper">
              <v-row>
                <v-col v-for="category in destinationCategories" :key="category.id" cols="12" sm="6" md="6" lg="4">
                  <v-card :to="`/destinations?category_id=${category.id}`" class="category-card" elevation="0">
                    <div class="category-image-wrapper">
                      <img :src="category.image" :alt="category.name" class="category-image" />
                      <div class="category-overlay"></div>
                      <div class="category-content">
                        <div class="category-icon-wrapper">
                          <v-icon size="32" color="white">{{ category.icon }}</v-icon>
                        </div>
                        <h3 class="category-name">{{ category.name }}</h3>
                        <p class="category-description">{{ category.description }}</p>
                        <v-btn variant="outlined" color="white" size="small" rounded="pill" class="category-button">
                          Lihat {{ category.count }}+ tempat
                          <v-icon end size="small">mdi-arrow-right</v-icon>
                        </v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-col>
              </v-row>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Popular Destinations Section -->
      <div v-if="activation.popular_destinations && activation.popular_destinations.length > 0" class="mb-16">
        <div class="text-center mb-8">
          <h2 class="text-h3 font-weight-bold mb-3">Destinasi Populer</h2>
          <p class="text-h6 text-grey-darken-1 font-weight-regular">Tempat favorit yang sering dikunjungi komunitas</p>
        </div>

        <v-row>
          <v-col v-for="destination in activation.popular_destinations" :key="destination.id" cols="6" sm="6" md="3">
            <v-card elevation="0" class="destination-card h-100" :to="`/destinations/${destination.id}`" rounded="xl">
              <div class="destination-image-wrapper">
                <img :src="`${apiBase}/storage/${destination.primary_photo}`" :alt="destination.name"
                  class="destination-image" />
                <div class="destination-overlay"></div>
                <div class="destination-badge">
                  <v-chip v-if="destination.category" color="white" size="small" class="font-weight-medium">
                    {{ destination.category.name }}
                  </v-chip>
                </div>
              </div>

              <v-card-text class="pa-4">
                <h4 class="text-subtitle-1 font-weight-bold mb-2">
                  {{ destination.name }}
                </h4>

                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon size="small" color="error" class="mr-1">mdi-heart</v-icon>
                    <span class="text-caption">{{ destination.likes_count || 0 }}</span>
                  </div>
                  <div class="d-flex align-center">
                    <v-icon size="small" color="grey" class="mr-1">mdi-comment</v-icon>
                    <span class="text-caption">{{ destination.comments_count || 0 }}</span>
                  </div>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <div class="text-center mt-8">
          <v-btn :to="`/destinations?activation_id=${activation.id}`" :color="activation.color_theme || 'primary'"
            size="large" rounded="pill" variant="outlined" class="px-8">
            Jelajahi Semua Destinasi
            <v-icon end>mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- Testimonials Section -->
      <div v-if="activation.show_testimonials && activation.testimonials && activation.testimonials.length > 0"
        class="mb-16">
        <div class="text-center mb-8">
          <h2 class="text-h3 font-weight-bold mb-3">Kata Mereka</h2>
          <p class="text-h6 text-grey-darken-1 font-weight-regular">Pengalaman dari peserta aktivitas kami</p>
        </div>
        <ActivationTestimonials :testimonials="activation.testimonials" />
      </div>

      <!-- 4. FAQ SECTION - Adjusted Width -->
      <div v-if="activation.show_faq && activation.faqs && activation.faqs.length > 0" class="mb-16">
        <div class="text-center mb-8">
          <h2 class="text-h3 font-weight-bold mb-3">Pertanyaan Umum</h2>
          <p class="text-h6 text-grey-darken-1 font-weight-regular">Temukan jawaban untuk pertanyaan Anda</p>
        </div>
        <v-row>
          <v-col cols="12" md="10" lg="9" xl="8" class="mx-auto">
            <ActivationFAQ :faqs="activation.faqs" />
          </v-col>
        </v-row>
      </div>

      <!-- CTA Section -->
      <v-card v-if="activation.cta_primary_label || activation.cta_secondary_label" elevation="0" rounded="xl"
        class="pa-8 pa-md-12 text-center" :style="{
          background: `linear-gradient(135deg, ${activation.color_theme || '#667eea'} 0%, ${darkenColor(activation.color_theme || '#764ba2', 20)} 100%)`
        }">
        <v-icon size="64" color="white" class="mb-4">mdi-account-group</v-icon>
        <h2 class="text-h4 text-white font-weight-bold mb-4">
          Siap Bergabung dengan {{ activation.name }}?
        </h2>
        <p class="text-h6 text-white mb-8" style="opacity: 0.9;">
          Jangan lewatkan kesempatan untuk menjadi bagian dari komunitas kami
        </p>
        <div class="d-flex flex-column flex-sm-row justify-center align-center ga-4">
          <v-btn v-if="activation.cta_primary_label" :href="activation.cta_primary_url" target="_blank" color="white"
            size="x-large" rounded="pill" class="px-10" elevation="8">
            {{ activation.cta_primary_label }}
          </v-btn>
          <v-btn v-if="activation.cta_secondary_label" :href="activation.cta_secondary_url" target="_blank"
            variant="outlined" color="white" size="x-large" rounded="pill" class="px-10">
            {{ activation.cta_secondary_label }}
          </v-btn>
        </div>
      </v-card>
    </v-container>
  </div>

  <!-- Loading State -->
  <v-container v-else-if="loading" class="py-16">
    <v-skeleton-loader type="article, article, article" />
  </v-container>

  <!-- Error State -->
  <v-container v-else class="py-16">
    <v-row class="text-center">
      <v-col cols="12">
        <v-icon size="64" color="grey">mdi-alert-circle-outline</v-icon>
        <h2 class="text-h5 mt-4 mb-6">Aktivasi tidak ditemukan</h2>
        <v-btn to="/aktivasi" color="primary" size="large" rounded="pill" class="px-8">
          <v-icon start>mdi-arrow-left</v-icon>
          Kembali ke Daftar Aktivasi
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
const route = useRoute()
const api = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const activation = ref<any>(null)
const loading = ref(true)

// Destination categories - fetch from API
const destinationCategories = ref([])

// Helper function to get category image URL
const getCategoryImageUrl = (category: any) => {
  // Use photo_url from backend if available
  if (category.photo_url) {
    return category.photo_url
  }
  // Fallback to constructing URL from photo path
  if (category.photo) {
    const cleanPath = category.photo.replace(/^public\//, '')
    return `${apiBase}/storage/${cleanPath}`
  }
  // Default placeholder
  return 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop'
}

// Fetch categories from API
const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    const categories = response.data.categories || []

    // Map categories to include image URL and count
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
  }
}


// Fetch activation detail
const fetchActivation = async () => {
  loading.value = true
  try {
    const response = await api.get(`/activations/${route.params.slug}`)
    activation.value = response.data

    // Debug: Log activation data
    console.log('=== ACTIVATION DATA DEBUG ===')
    console.log('Full Activation:', activation.value)
    console.log('Featured Event:', activation.value.featured_event)
    console.log('Has Featured Event?', !!activation.value.featured_event)
    console.log('Upcoming Events:', activation.value.upcoming_events)
    console.log('Upcoming Events Count:', activation.value.upcoming_events?.length)
    console.log('Popular Destinations:', activation.value.popular_destinations)
    console.log('Popular Destinations Count:', activation.value.popular_destinations?.length)
    console.log('===========================')
  } catch (error) {
    console.error('Error fetching activation:', error)
  } finally {
    loading.value = false
  }
}

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// Get day from date
const getDay = (dateString: string) => {
  const date = new Date(dateString)
  return date.getDate()
}

// Get month from date
const getMonth = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase()
}

// Strip HTML tags
const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}

// Smooth scroll to about section
const scrollToAbout = () => {
  const aboutSection = document.getElementById('about-section')
  if (aboutSection) {
    aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}


// Helper functions
const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    city: '#2196F3',
    theme: '#9C27B0',
    space: '#E91E63',
    other: '#607D8B'
  }
  return colors[category] || colors.other
}

const getCategoryIcon = (category: string) => {
  const icons: Record<string, string> = {
    city: 'mdi-city',
    theme: 'mdi-palette',
    space: 'mdi-store',
    other: 'mdi-star-four-points'
  }
  return icons[category] || icons.other
}

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Aktivasi Kota',
    theme: 'Aktivasi Tematik',
    space: 'Ruang Kreatif',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

const darkenColor = (color: string, percent: number) => {
  const num = parseInt(color.replace('#', ''), 16)
  const amt = Math.round(2.55 * percent)
  const R = (num >> 16) - amt
  const G = (num >> 8 & 0x00FF) - amt
  const B = (num & 0x0000FF) - amt
  return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
    (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
    (B < 255 ? B < 1 ? 0 : B : 255))
    .toString(16)
    .slice(1)
}

const hexToRgba = (hex: string, alpha: number) => {
  const num = parseInt(hex.replace('#', ''), 16)
  const r = (num >> 16) & 255
  const g = (num >> 8) & 255
  const b = num & 255
  return `rgba(${r}, ${g}, ${b}, ${alpha})`
}

// Initial fetch
onMounted(() => {
  fetchActivation()
  fetchCategories()
})

// SEO
// SEO
useSeoMeta({
  title: () => activation.value ? `${activation.value.name} - Jalan Bareng` : 'Aktivasi - Jalan Bareng',
  ogTitle: () => activation.value ? `${activation.value.name} - Jalan Bareng` : 'Aktivasi - Jalan Bareng',
  description: () => activation.value ? (activation.value.tagline || activation.value.description || '') : 'Detail aktivasi kota di Jalan Bareng.',
  ogDescription: () => activation.value ? (activation.value.tagline || activation.value.description || '') : 'Detail aktivasi kota di Jalan Bareng.',
  ogImage: () => activation.value?.hero_image ? `${apiBase}/storage/${activation.value.hero_image}` : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
.activation-hero {
  position: relative;
  overflow: hidden;
  margin-top: -70px;
}

.hero-background {
  filter: blur(2px);
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

.wave-divider {
  position: absolute;
  bottom: -1px;
  left: 0;
  width: 100%;
  z-index: 2;
}

.wave-divider svg {
  display: block;
  width: 100%;
  height: auto;
}

/* About Section - Modern & Informative */
.about-section {
  padding: 20px 0;
}

.divider-line {
  width: 80px;
  height: 4px;
  border-radius: 2px;
}

.about-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border: 1px solid #e0e0e0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
}

.about-content {
  line-height: 1.9;
  color: #424242;
  font-size: 1.05rem;
}

.about-content :deep(p) {
  margin-bottom: 20px;
}

.about-content :deep(h1),
.about-content :deep(h2),
.about-content :deep(h3) {
  margin-top: 28px;
  margin-bottom: 16px;
  font-weight: 700;
  color: #212121;
}

.about-content :deep(ul),
.about-content :deep(ol) {
  margin-left: 28px;
  margin-bottom: 20px;
}

.about-content :deep(strong) {
  color: #212121;
  font-weight: 600;
}

.gallery-integration {
  margin-top: 40px;
}

/* Featured Event Card */
.featured-event-card {
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

.featured-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

/* Upcoming Events - Modern Style */
.upcoming-events-section {
  padding: 40px 0;
}

.event-card-modern {
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  cursor: pointer;
  border: 1px solid #f0f0f0;
}

.event-card-modern:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

.event-image-wrapper-modern {
  position: relative;
  height: 240px;
  overflow: hidden;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.event-card-modern:hover .event-image {
  transform: scale(1.15) rotate(2deg);
}

.event-overlay-modern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.1) 60%, transparent 100%);
}

.event-date-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  background: white;
  border-radius: 12px;
  padding: 8px 12px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 2;
}

.date-day {
  font-size: 24px;
  font-weight: 700;
  line-height: 1;
  color: #212121;
}

.date-month {
  font-size: 11px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 2px;
}

.event-type-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 2;
}

.event-title {
  line-height: 1.3;
  min-height: 60px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-description {
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
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

/* Category Section Styles */
.category-section {
  padding: 40px 0;
}

.category-intro {
  padding-right: 32px;
}

.category-label {
  letter-spacing: 2px;
  font-size: 14px;
}

.category-title {
  line-height: 1.2;
}

.category-cards-wrapper {
  padding-left: 0;
}

.category-card {
  border-radius: 24px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.category-image-wrapper {
  position: relative;
  height: 320px;
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
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.category-card:hover .category-button {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 960px) {
  .category-intro {
    padding-right: 0;
    margin-bottom: 32px;
    text-align: center;
  }

  .category-image-wrapper {
    height: 280px;
  }

  .event-image-wrapper-modern {
    height: 200px;
  }

  .event-title {
    min-height: auto;
  }
}
</style>
