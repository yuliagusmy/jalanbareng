<template>
  <div>
    <!-- Modern Hero Section -->
    <v-sheet class="aktivasi-hero" :style="{
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
        style="position: relative; z-index: 2; padding-top: 120px; padding-bottom: 80px">
        <v-row align="center" justify="center">
          <v-col cols="12" md="8" class="text-center">
            <v-chip color="white" variant="flat" class="mb-4" size="large">
              <v-icon start color="primary">mdi-star-four-points</v-icon>
              <span class="text-primary font-weight-bold">Aktivasi Komunitas</span>
            </v-chip>

            <h1 class="text-h3 text-md-h2 font-weight-bold text-white mb-4" style="line-height: 1.2;">
              Temukan Aktivasi
              <br>
              <span class="gradient-text">Jalan Bareng</span>
            </h1>

            <p class="text-h6 text-md-h5 text-white mb-8" style="opacity: 0.95; max-width: 700px; margin: 0 auto;">
              Bergabunglah dengan berbagai program dan kegiatan Jalan Bareng di seluruh Indonesia
            </p>
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
      <!-- Category Filter -->
      <v-row class="mb-8">
        <v-col cols="12">
          <div class="d-flex align-center justify-center flex-wrap ga-3">
            <v-chip :variant="selectedCategory === '' ? 'flat' : 'outlined'"
              :color="selectedCategory === '' ? 'primary' : 'default'" size="large" @click="selectedCategory = ''"
              class="filter-chip">
              <v-icon start>mdi-view-grid</v-icon>
              Semua
            </v-chip>
            <v-chip :variant="selectedCategory === 'city' ? 'flat' : 'outlined'"
              :color="selectedCategory === 'city' ? 'blue' : 'default'" size="large" @click="selectedCategory = 'city'"
              class="filter-chip">
              <v-icon start>mdi-city</v-icon>
              Aktivasi Kota
            </v-chip>
            <v-chip :variant="selectedCategory === 'theme' ? 'flat' : 'outlined'"
              :color="selectedCategory === 'theme' ? 'purple' : 'default'" size="large"
              @click="selectedCategory = 'theme'" class="filter-chip">
              <v-icon start>mdi-palette</v-icon>
              Aktivasi Tematik
            </v-chip>
            <v-chip :variant="selectedCategory === 'space' ? 'flat' : 'outlined'"
              :color="selectedCategory === 'space' ? 'pink' : 'default'" size="large"
              @click="selectedCategory = 'space'" class="filter-chip">
              <v-icon start>mdi-store</v-icon>
              Ruang Kreatif
            </v-chip>
          </div>
        </v-col>
      </v-row>

      <!-- Featured Activations -->
      <div v-if="featuredActivations.length > 0" class="mb-12">
        <div class="text-center mb-8">
          <v-chip color="warning" variant="flat" class="mb-4">
            <v-icon start>mdi-star</v-icon>
            Unggulan
          </v-chip>
          <h2 class="text-h4 font-weight-bold mb-2">Aktivasi Unggulan</h2>
          <p class="text-body-1 text-grey-darken-1">Program pilihan yang paling diminati</p>
        </div>

        <v-row>
          <v-col v-for="activation in featuredActivations" :key="activation.id" cols="12" md="6">
            <v-card :to="`/aktivasi/${activation.slug}`" elevation="0" rounded="xl"
              class="featured-activation-card h-100">
              <v-row no-gutters>
                <v-col cols="12" sm="5">
                  <div class="featured-image-wrapper">
                    <v-img v-if="activation.hero_image" :src="`${apiBase}/storage/${activation.hero_image}`"
                      :alt="activation.name" cover height="100%">
                      <template v-slot:placeholder>
                        <div class="d-flex align-center justify-center fill-height">
                          <v-progress-circular indeterminate color="primary"></v-progress-circular>
                        </div>
                      </template>
                    </v-img>
                    <div v-else class="placeholder-image">
                      <v-icon size="64" color="grey-lighten-1">mdi-image</v-icon>
                    </div>
                    <div class="featured-badge">
                      <v-chip color="warning" size="small">
                        <v-icon start size="small">mdi-star</v-icon>
                        Unggulan
                      </v-chip>
                    </div>
                  </div>
                </v-col>
                <v-col cols="12" sm="7">
                  <v-card-text class="pa-6">
                    <v-chip :color="getCategoryColor(activation.category)" size="small" class="mb-3">
                      {{ getCategoryLabel(activation.category) }}
                    </v-chip>

                    <h3 class="text-h5 font-weight-bold mb-3">{{ activation.name }}</h3>

                    <p v-if="activation.tagline" class="text-body-2 text-grey-darken-1 mb-4">
                      {{ activation.tagline }}
                    </p>

                    <div v-if="activation.city" class="d-flex align-center mb-4">
                      <v-icon size="small" color="grey" class="mr-2">mdi-map-marker</v-icon>
                      <span class="text-caption">{{ activation.city }}</span>
                    </div>

                    <v-btn :color="activation.color_theme || 'primary'" variant="flat" rounded="pill" block>
                      Lihat Detail
                      <v-icon end>mdi-arrow-right</v-icon>
                    </v-btn>
                  </v-card-text>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
      </div>

      <!-- All Activations -->
      <div>
        <div class="d-flex justify-space-between align-center mb-6">
          <div>
            <h2 class="text-h4 font-weight-bold mb-2">{{ categoryTitle }}</h2>
            <p class="text-body-1 text-grey-darken-1">
              {{ filteredActivations.length }} aktivasi tersedia
            </p>
          </div>
        </div>

        <!-- Activations Grid -->
        <v-row v-if="!loading && filteredActivations.length > 0">
          <v-col v-for="activation in filteredActivations" :key="activation.id" cols="12" sm="6" md="4">
            <v-card :to="`/aktivasi/${activation.slug}`" elevation="0" rounded="xl" class="activation-card h-100">
              <div class="activation-image-wrapper">
                <v-img v-if="activation.hero_image" :src="`${apiBase}/storage/${activation.hero_image}`"
                  :alt="activation.name" cover height="220">
                  <template v-slot:placeholder>
                    <div class="d-flex align-center justify-center fill-height">
                      <v-progress-circular indeterminate color="primary"></v-progress-circular>
                    </div>
                  </template>
                </v-img>
                <div v-else class="placeholder-image" style="height: 220px;">
                  <v-icon size="64" color="grey-lighten-1">mdi-image</v-icon>
                </div>
                <div class="activation-overlay"></div>
                <div class="activation-badge">
                  <v-chip :color="getCategoryColor(activation.category)" size="small">
                    {{ getCategoryLabel(activation.category) }}
                  </v-chip>
                </div>
                <v-avatar v-if="activation.icon" size="60" class="activation-icon" style="border: 3px solid white">
                  <v-img :src="`${apiBase}/storage/${activation.icon}`"></v-img>
                </v-avatar>
              </div>

              <v-card-text class="pa-6">
                <h3 class="text-h6 font-weight-bold mb-2">{{ activation.name }}</h3>

                <p v-if="activation.tagline" class="text-caption text-grey-darken-1 mb-3">
                  {{ activation.tagline }}
                </p>

                <div v-if="activation.city" class="d-flex align-center mb-4">
                  <v-icon size="small" color="grey" class="mr-2">mdi-map-marker</v-icon>
                  <span class="text-caption">{{ activation.city }}</span>
                </div>

                <v-divider class="mb-3"></v-divider>

                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center">
                    <v-icon size="small" color="grey" class="mr-1">mdi-calendar</v-icon>
                    <span class="text-caption">{{ activation.events_count || 0 }} Event</span>
                  </div>
                  <v-btn :color="activation.color_theme || 'primary'" variant="text" size="small" rounded="pill">
                    Lihat Detail
                    <v-icon end size="small">mdi-arrow-right</v-icon>
                  </v-btn>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <v-row v-if="!loading && filteredActivations.length === 0">
          <v-col cols="12" class="text-center py-12">
            <v-icon size="64" color="grey-lighten-1">mdi-folder-open-outline</v-icon>
            <h3 class="text-h6 text-grey mt-4 mb-2">Belum ada aktivasi</h3>
            <p class="text-body-2 text-grey-darken-1">
              Belum ada aktivasi untuk kategori ini
            </p>
            <v-btn color="primary" variant="outlined" rounded="pill" class="mt-4" @click="selectedCategory = ''">
              Lihat Semua Aktivasi
            </v-btn>
          </v-col>
        </v-row>

        <!-- Loading State -->
        <v-row v-if="loading">
          <v-col v-for="i in 6" :key="i" cols="12" sm="6" md="4">
            <v-skeleton-loader type="card" />
          </v-col>
        </v-row>
      </div>

      <!-- CTA Section -->
      <v-card elevation="0" rounded="xl" class="pa-8 pa-md-12 text-center mt-12"
        style="background: linear-gradient(135deg, #5d71c9 0%, #6b4591 100%);">
        <v-icon size="64" color="white" class="mb-4">mdi-account-group</v-icon>
        <h2 class="text-h4 text-white font-weight-bold mb-4">
          Ingin Membuat Aktivasi Sendiri?
        </h2>
        <p class="text-h6 text-white mb-8" style="opacity: 0.9; max-width: 600px; margin: 0 auto;">
          Hubungi admin untuk mendaftarkan program aktivasi komunitas Anda
        </p>
        <v-btn color="white" size="x-large" rounded="pill" class="px-10" elevation="8"
          href="mailto:admin@jalanbareng.id">
          <v-icon start>mdi-email</v-icon>
          Hubungi Admin
        </v-btn>
      </v-card>
    </v-container>
  </div>
</template>

<script setup lang="ts">
const api = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const selectedCategory = ref('')
const activations = ref<any[]>([])
const loading = ref(true)

// Fetch activations
const fetchActivations = async () => {
  loading.value = true
  try {
    const params: any = {}
    if (selectedCategory.value) {
      params.category = selectedCategory.value
    }

    const response = await api.get('/activations', { params })
    activations.value = response.data
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    loading.value = false
  }
}

// Computed
const featuredActivations = computed(() => {
  return activations.value.filter(a => a.is_featured && a.is_active)
})

const filteredActivations = computed(() => {
  return activations.value.filter(a => !a.is_featured && a.is_active)
})

const categoryTitle = computed(() => {
  const titles: Record<string, string> = {
    '': 'Semua Aktivasi',
    'city': 'Aktivasi Kota',
    'theme': 'Aktivasi Tematik',
    'space': 'Ruang Kreatif'
  }
  return titles[selectedCategory.value] || 'Semua Aktivasi'
})

// Helper functions
const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    city: 'blue',
    theme: 'purple',
    space: 'pink',
    other: 'grey'
  }
  return colors[category] || colors.other
}

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Kota',
    theme: 'Tematik',
    space: 'Ruang Kreatif',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

// Watch category changes
watch(selectedCategory, () => {
  fetchActivations()
})

// Initial fetch
onMounted(() => {
  fetchActivations()
})

// SEO
useSeoMeta({
  title: 'Aktivasi Kota - Jalan Bareng',
  ogTitle: 'Aktivasi Kota - Jalan Bareng',
  description: 'Temukan komunitas Jalan Bareng di kotamu dan bergabunglah dengan berbagai program seru.',
  ogDescription: 'Temukan komunitas Jalan Bareng di kotamu dan bergabunglah dengan berbagai program seru.',
  ogImage: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
.aktivasi-hero {
  position: relative;
  overflow: hidden;
  margin-top: -70px;
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

.filter-chip {
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-chip:hover {
  transform: translateY(-2px);
}

/* Featured Activation Card */
.featured-activation-card {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

.featured-activation-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.featured-image-wrapper {
  position: relative;
  height: 100%;
  min-height: 300px;
  overflow: hidden;
}

.featured-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 2;
}

/* Activation Card */
.activation-card {
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
  overflow: hidden;
}

.activation-image-wrapper :deep(img) {
  transition: transform 0.5s ease;
}

.activation-card:hover .activation-image-wrapper :deep(img) {
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

.activation-icon {
  position: absolute;
  bottom: -30px;
  right: 16px;
  z-index: 3;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.placeholder-image {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  height: 100%;
}
</style>
