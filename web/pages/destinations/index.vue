<template>
  <div>
    <!-- Hero Section with Gradient -->
    <v-sheet class="hero-section mb-8" :style="{
      background: 'linear-gradient(135deg, #4a9dd4 0%, #00c5d4 100%)',
      position: 'relative',
      overflow: 'hidden',
      height: '500px'
    }">
      <!-- Animated Background Shapes -->
      <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>

      <v-container class="hero-content"
        style="position: relative; z-index: 2; padding-top: 100px; padding-bottom: 80px">
        <v-row align="center" class="pt-16 pb-16 pb-md-20">
          <v-col cols="12" md="8" class="text-white pb-8 pb-md-12">
            <div class="animate-fade-in">
              <h1 class="text-h3 text-md-h2 font-weight-bold mb-6" style="line-height: 1.2;">
                Temukan Destinasi<br>
                <span class="gradient-text-white">Menarik di Makassar</span>
              </h1>

              <p class="text-h6 mb-0" style="opacity: 0.95; max-width: 600px; line-height: 1.6;">
                Jelajahi tempat-tempat menarik yang dibagikan oleh komunitas Teman Jalan
              </p>
            </div>
          </v-col>
          <v-col cols="12" md="4" class="text-md-right">
            <v-btn v-if="authStore.isLoggedIn" color="white" size="x-large" to="/destinations/create"
              prepend-icon="mdi-plus" rounded="pill" elevation="8" class="px-8">
              Tambah Destinasi
            </v-btn>
            <v-btn v-else color="white" size="x-large" to="/register" prepend-icon="mdi-account-plus" rounded="pill"
              elevation="8" class="px-8">
              Daftar untuk Berbagi
            </v-btn>
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

    <v-container>

      <!-- Filters -->
      <v-row class="mb-6">
        <v-col cols="12" md="3">
          <v-text-field v-model="search" label="Cari destinasi..." prepend-inner-icon="mdi-magnify" variant="outlined"
            rounded="lg" density="comfortable" clearable @update:model-value="fetchDestinations"></v-text-field>
        </v-col>
        <v-col cols="12" md="3">
          <v-select v-model="selectedActivation" :items="activations" item-title="name" item-value="id" label="Aktivasi"
            prepend-inner-icon="mdi-star-four-points" variant="outlined" rounded="lg" density="comfortable" clearable
            @update:model-value="fetchDestinations"></v-select>
        </v-col>
        <v-col cols="12" md="3">
          <v-select v-model="selectedCategory" :items="categories" item-title="name" item-value="id" label="Kategori"
            prepend-inner-icon="mdi-filter" variant="outlined" rounded="lg" density="comfortable" clearable
            @update:model-value="fetchDestinations"></v-select>
        </v-col>
      </v-row>

      <!-- Destinations Grid -->
      <v-row v-if="!loading">
        <v-col v-for="destination in destinations" :key="destination.id" cols="6" sm="6" md="3">
          <v-card elevation="0" class="destination-card h-100" :to="`/destinations/${destination.id}`">
            <div class="destination-image-wrapper">
              <img :src="getImageUrl(destination.primary_photo)" :alt="destination.name" class="destination-image" />
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

        <v-col v-if="destinations.length === 0" cols="12">
          <v-card elevation="0" class="text-center pa-8" rounded="xl">
            <v-icon size="64" color="grey">mdi-map-marker-off</v-icon>
            <h3 class="text-h5 mt-4">Tidak ada destinasi ditemukan</h3>
            <p class="text-body-1 mt-2">Coba ubah filter pencarian Anda</p>
          </v-card>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <v-row v-else>
        <v-col v-for="n in 8" :key="n" cols="12" sm="6" md="4" lg="3">
          <v-skeleton-loader type="card"></v-skeleton-loader>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row v-if="pagination.total > pagination.per_page" class="mt-8">
        <v-col cols="12" class="d-flex justify-center">
          <v-pagination v-model="pagination.current_page" :length="Math.ceil(pagination.total / pagination.per_page)"
            color="primary" rounded="circle" @update:model-value="fetchDestinations"></v-pagination>
        </v-col>
      </v-row>

      <!-- Interactive Map Teaser Section -->
      <v-row class="mt-16 mb-8">
        <v-col cols="12">
          <v-card elevation="8" rounded="xl" class="overflow-hidden">
            <v-row no-gutters>
              <v-col cols="12" md="6" class="pa-8 pa-md-12 d-flex flex-column justify-center">
                <v-icon size="64" color="primary" class="mb-4">mdi-map-search</v-icon>
                <h2 class="text-h3 font-weight-bold mb-4">
                  Jelajahi dengan Peta Interaktif
                </h2>
                <p class="text-h6 text-grey-darken-1 mb-6">
                  Lihat semua destinasi di peta Makassar dan temukan tempat menarik di sekitar Anda
                </p>
                <div class="d-flex flex-column flex-sm-row ga-3">
                  <v-btn color="primary" size="x-large" rounded="pill" prepend-icon="mdi-map" class="px-6" to="/map">
                    Buka Peta
                  </v-btn>
                  <v-btn variant="outlined" color="primary" size="x-large" rounded="pill"
                    prepend-icon="mdi-calendar-star" class="px-6" to="/events">
                    Lihat Event
                  </v-btn>
                </div>

                <!-- Features List -->
                <v-list density="compact" class="bg-transparent mt-6">
                  <v-list-item prepend-icon="mdi-map-marker-radius" class="px-0">
                    <v-list-item-title class="text-body-1">
                      <strong>Lokasi Akurat</strong> - Setiap destinasi dengan koordinat tepat
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item prepend-icon="mdi-filter" class="px-0">
                    <v-list-item-title class="text-body-1">
                      <strong>Filter Kategori</strong> - Temukan berdasarkan jenis destinasi
                    </v-list-item-title>
                  </v-list-item>
                  <v-list-item prepend-icon="mdi-walk" class="px-0">
                    <v-list-item-title class="text-body-1">
                      <strong>Rute Jalan Kaki</strong> - Lihat rute event walking
                    </v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-col>

              <v-col cols="12" md="6" class="position-relative pa-0">
                <div class="map-preview-container" :style="{
                  background: 'linear-gradient(135deg, #4a9dd4 0%, #00c5d4 100%)',
                  minHeight: '500px',
                  height: '100%',
                  position: 'relative',
                  overflow: 'hidden'
                }">
                  <!-- Decorative Map Icons -->
                  <div class="map-decorations">
                    <v-icon v-for="(icon, i) in mapIcons" :key="i" :style="{
                      position: 'absolute',
                      top: icon.top,
                      left: icon.left,
                      opacity: icon.opacity,
                      fontSize: icon.size + 'px',
                      color: 'white',
                      animation: `float ${icon.duration}s infinite ease-in-out`,
                      animationDelay: icon.delay + 's'
                    }">
                      {{ icon.name }}
                    </v-icon>
                  </div>

                  <!-- Centered Map Icon -->
                  <div class="d-flex align-center justify-center fill-height">
                    <div class="text-center text-white pa-8">
                      <v-icon size="120" color="white" class="mb-4 pulse-animation">
                        mdi-google-maps
                      </v-icon>
                      <h3 class="text-h5 font-weight-bold">
                        Peta Interaktif
                      </h3>
                      <p class="text-body-1 mt-2" style="opacity: 0.9;">
                        Jelajahi {{ destinations.length }}+ destinasi
                      </p>
                    </div>
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>

      <!-- Community Contribution CTA -->
      <v-row class="mt-12 mb-8">
        <v-col cols="12">
          <v-card elevation="0" rounded="xl" class="pa-8 pa-md-12 text-center"
            style="background: linear-gradient(135deg, #d084e8 0%, #e05167 100%);">
            <v-row justify="center">
              <v-col cols="12" md="8">
                <v-icon size="64" color="white" class="mb-4">mdi-heart-multiple</v-icon>
                <h2 class="text-h3 text-white font-weight-bold mb-4">
                  Bagikan Tempat Favorit Anda
                </h2>
                <p class="text-h6 text-white mb-8" style="opacity: 0.95;">
                  Punya rekomendasi tempat menarik di Makassar? Bagikan kepada komunitas dan bantu teman-teman menemukan
                  destinasi baru!
                </p>

                <v-row justify="center" class="mb-6">
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="contribution-stat">
                      <v-icon size="40" color="white" class="mb-2">mdi-camera-plus</v-icon>
                      <div class="text-h5 text-white font-weight-bold">Foto</div>
                      <div class="text-body-1 text-white" style="opacity: 0.9;">Upload foto lokasi</div>
                    </div>
                  </v-col>
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="contribution-stat">
                      <v-icon size="40" color="white" class="mb-2">mdi-map-marker-plus</v-icon>
                      <div class="text-h5 text-white font-weight-bold">Lokasi</div>
                      <div class="text-body-1 text-white" style="opacity: 0.9;">Tandai di peta</div>
                    </div>
                  </v-col>
                  <v-col cols="12" sm="4" class="text-center">
                    <div class="contribution-stat">
                      <v-icon size="40" color="white" class="mb-2">mdi-text-box</v-icon>
                      <div class="text-h5 text-white font-weight-bold">Cerita</div>
                      <div class="text-body-1 text-white" style="opacity: 0.9;">Tulis pengalaman</div>
                    </div>
                  </v-col>
                </v-row>

                <v-btn v-if="authStore.isLoggedIn" color="white" size="x-large" rounded="pill" class="px-10"
                  elevation="8" to="/destinations/create">
                  <v-icon start>mdi-plus-circle</v-icon>
                  Tambah Destinasi Baru
                </v-btn>
                <v-btn v-else color="white" size="x-large" rounded="pill" class="px-10" elevation="8" to="/register">
                  <v-icon start>mdi-account-plus</v-icon>
                  Daftar untuk Berkontribusi
                </v-btn>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useHead } from '#app'

definePageMeta({
  layout: 'hero'
})

useSeoMeta({
  title: 'Jelajahi Destinasi - Jalan Bareng',
  ogTitle: 'Jelajahi Destinasi - Jalan Bareng',
  description: 'Temukan rekomendasi tempat wisata terbaik dan hidden gems yang dibagikan oleh komunitas Jalan Bareng.',
  ogDescription: 'Temukan rekomendasi tempat wisata terbaik dan hidden gems yang dibagikan oleh komunitas Jalan Bareng.',
  ogImage: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const authStore = useAuthStore()
const { api } = useApi()

const destinations = ref([])
const categories = ref([])
const activations = ref([])
const loading = ref(false)
const search = ref('')
const selectedCategory = ref(null)
const selectedActivation = ref(null)
const pagination = ref({
  current_page: 1,
  per_page: 12,
  total: 0
})

// Decorative map icons for the teaser section
const mapIcons = ref([
  { name: 'mdi-map-marker', top: '10%', left: '15%', opacity: 0.6, size: 32, duration: 6, delay: 0 },
  { name: 'mdi-map-marker', top: '25%', left: '75%', opacity: 0.5, size: 28, duration: 8, delay: 1 },
  { name: 'mdi-map-marker', top: '60%', left: '20%', opacity: 0.7, size: 36, duration: 7, delay: 2 },
  { name: 'mdi-map-marker', top: '70%', left: '80%', opacity: 0.6, size: 30, duration: 9, delay: 1.5 },
  { name: 'mdi-map-marker', top: '40%', left: '85%', opacity: 0.5, size: 26, duration: 7.5, delay: 0.5 },
])

const { getImageUrl } = useImageUrl()


const fetchDestinations = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    }

    if (search.value) {
      params.search = search.value
    }

    if (selectedCategory.value) {
      params.category_id = selectedCategory.value
    }

    if (selectedActivation.value) {
      params.activation_id = selectedActivation.value
    }

    const response = await api.get('/destinations', { params })
    destinations.value = response.data.data || []

    pagination.value = {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching destinations:', error)
    destinations.value = []
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.categories || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

onMounted(() => {
  // Read query parameters from URL
  const route = useRoute()
  if (route.query.activation_id) {
    selectedActivation.value = parseInt(route.query.activation_id as string)
  }
  if (route.query.category_id) {
    selectedCategory.value = parseInt(route.query.category_id as string)
  }

  fetchDestinations()
  fetchCategories()
  fetchActivations()
})
</script>

<style scoped>
.hero-section {
  position: relative;
  overflow: hidden;
  margin-top: -80px !important;
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
  background: rgba(255, 255, 255, 0.15);
  animation: float 20s infinite ease-in-out;
}

.shape-1 {
  width: 250px;
  height: 250px;
  top: -80px;
  right: 10%;
  animation-delay: 0s;
}

.shape-2 {
  width: 180px;
  height: 180px;
  bottom: -60px;
  left: 15%;
  animation-delay: 7s;
}

.shape-3 {
  width: 200px;
  height: 200px;
  top: 40%;
  left: -80px;
  animation-delay: 3s;
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0) translateX(0) rotate(0deg);
  }

  33% {
    transform: translateY(-25px) translateX(25px) rotate(120deg);
  }

  66% {
    transform: translateY(15px) translateX(-15px) rotate(240deg);
  }
}

.gradient-text-white {
  background: linear-gradient(135deg, #fff 0%, #e0f7ff 100%);
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

/* Destination Cards - Consistent with Home Page */
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

.map-preview-container {
  position: relative;
}

.map-decorations {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.pulse-animation {
  animation: pulse 2s infinite ease-in-out;
}

@keyframes pulse {

  0%,
  100% {
    transform: scale(1);
    opacity: 1;
  }

  50% {
    transform: scale(1.1);
    opacity: 0.8;
  }
}

.contribution-stat {
  padding: 1rem;
  transition: transform 0.3s ease;
}

.contribution-stat:hover {
  transform: translateY(-5px);
}

.fill-height {
  height: 100%;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .destination-image-wrapper {
    height: 160px;
  }
}
</style>
