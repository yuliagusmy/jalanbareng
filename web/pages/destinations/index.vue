<template>
  <div class="destinations-index-page">
    <!-- Editorial Hero Section -->
    <section class="destinations-hero">
      <v-container class="hero-content">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Eyebrow Badge -->
            <div class="hero-badge-pill mb-4">
              <v-icon start size="16" color="#DC2626">mdi-map-marker-radius-outline</v-icon>
              <span>DIREKTORI &amp; PETA KOTA</span>
            </div>

            <!-- Main Headline -->
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              Temukan Destinasi &amp;
              <span class="text-primary-red">Sudut Kota</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Rekomendasi titik kumpul, jalur pesisir, bangunan bersejarah, dan ruang publik yang dibagikan dan dikurasi oleh kawan-kawan pejalan Jalan Bareng.
            </p>

            <!-- Category Filter Chips -->
            <div class="category-filters-wrapper d-flex align-center justify-center flex-wrap ga-2">
              <button
                type="button"
                :class="['filter-btn', { active: !selectedCategory }]"
                @click="selectCategory(null)"
              >
                <v-icon start size="18">mdi-view-grid-outline</v-icon>
                Semua Kategori ({{ pagination.total || destinations.length }})
              </button>
              <button
                v-for="category in categories"
                :key="category.id"
                type="button"
                :class="['filter-btn', { active: selectedCategory === category.id }]"
                @click="selectCategory(category.id)"
              >
                <v-icon start size="18">{{ category.icon || 'mdi-map-marker' }}</v-icon>
                {{ category.name }}
              </button>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 d-flex align-center justify-center ga-3 flex-wrap">
              <v-btn
                v-if="authStore.isLoggedIn"
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-6 text-white"
                to="/destinations/create"
              >
                <v-icon start size="18">mdi-plus</v-icon>
                Tambah Destinasi
              </v-btn>
              <v-btn
                v-else
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-6 text-white"
                to="/register"
              >
                <v-icon start size="18">mdi-account-plus-outline</v-icon>
                Daftar untuk Berbagi
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Content Container -->
    <v-container class="py-6 py-md-10">
      <!-- Search, City Filter & View Switcher Bar -->
      <div class="search-filter-bar pa-4 pa-md-5 mb-8">
        <v-row dense align="center" justify="space-between">
          <v-col cols="12" md="5">
            <v-text-field
              v-model="search"
              label="Cari nama tempat, rute, atau jalan..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              clearable
              @update:model-value="() => { pagination.current_page = 1; fetchDestinations(); }"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="selectedActivation"
              :items="activationOptions"
              item-title="text"
              item-value="value"
              label="Aktivasi Kota"
              prepend-inner-icon="mdi-city-variant-outline"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              clearable
              @update:model-value="() => { pagination.current_page = 1; fetchDestinations(); }"
            ></v-select>
          </v-col>
          
          <!-- View Switcher (Grid vs Map) -->
          <v-col cols="12" sm="6" md="4" class="d-flex justify-sm-end mt-2 mt-sm-0">
            <div class="view-switch-tabs">
              <button
                type="button"
                :class="['view-switch-btn', { active: viewMode === 'grid' }]"
                @click="setViewMode('grid')"
              >
                <v-icon start size="16">mdi-view-grid-outline</v-icon>
                <span>Grid ({{ pagination.total || destinations.length }})</span>
              </button>
              <button
                type="button"
                :class="['view-switch-btn', { active: viewMode === 'map' }]"
                @click="setViewMode('map')"
              >
                <v-icon start size="16">mdi-map-marker-radius-outline</v-icon>
                <span>Peta ({{ filteredMapDestinations.length }})</span>
              </button>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Active Filter Reset Bar -->
      <div v-if="selectedCategory || selectedActivation || search" class="d-flex align-center justify-space-between mb-6 px-2">
        <div class="d-flex align-center ga-2 flex-wrap text-body-2 text-grey-darken-2">
          <span>Filter aktif:</span>
          <v-chip
            v-if="selectedCategoryName"
            size="small"
            closable
            color="#DC2626"
            variant="tonal"
            @click:close="selectCategory(null)"
          >
            Kategori: {{ selectedCategoryName }}
          </v-chip>
          <v-chip
            v-if="selectedActivationName"
            size="small"
            closable
            color="#DC2626"
            variant="tonal"
            @click:close="selectedActivation = null; fetchDestinations();"
          >
            Kota: {{ selectedActivationName }}
          </v-chip>
          <v-chip
            v-if="search"
            size="small"
            closable
            color="#DC2626"
            variant="tonal"
            @click:close="search = ''; fetchDestinations();"
          >
            Pencarian: "{{ search }}"
          </v-chip>
        </div>

        <v-btn
          variant="text"
          color="#DC2626"
          size="small"
          class="font-weight-bold"
          @click="resetFilters"
        >
          Reset Semua
          <v-icon end size="16">mdi-close</v-icon>
        </v-btn>
      </div>

      <!-- VIEW 1: INTERACTIVE MAP VIEW -->
      <section v-if="viewMode === 'map'" class="mb-14">
        <div class="map-view-card mb-8">
          <div class="map-card-header d-flex align-center justify-space-between flex-wrap ga-2 px-6 py-4 border-b">
            <div class="d-flex align-center ga-2">
              <v-icon color="#DC2626" size="20">mdi-map-marker-radius</v-icon>
              <h2 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-0">
                Peta Interaktif Destinasi
                <span v-if="selectedActivationName" class="text-grey-darken-1 font-weight-medium">
                  • {{ selectedActivationName }}
                </span>
              </h2>
              <span class="count-pill">{{ filteredMapDestinations.length }} Titik Lokasi</span>
            </div>
            <div class="text-caption text-grey-darken-1">
              Klik pada pin di peta untuk melihat ringkasan destinasi
            </div>
          </div>

          <div class="map-embed-wrapper">
            <ClientOnly>
              <DestinationMapViewer
                v-if="filteredMapDestinations.length > 0"
                :destinations="filteredMapDestinations"
                :mapCenter="mapCenter"
              />
              <div v-else class="d-flex align-center justify-center h-100 pa-12 bg-grey-lighten-4">
                <div class="text-center">
                  <v-icon size="64" color="grey-lighten-1">mdi-map-marker-off</v-icon>
                  <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-4 mb-1">
                    Tidak ada titik lokasi ditemukan
                  </h3>
                  <p class="text-body-2 text-grey-darken-1 mb-0">
                    Coba sesuaikan filter kategori atau kota yang dipilih
                  </p>
                </div>
              </div>
            </ClientOnly>
          </div>
        </div>

        <!-- Destinations List under Map for Quick Selection -->
        <div v-if="filteredMapDestinations.length > 0" class="map-destinations-preview">
          <div class="d-flex align-center justify-space-between mb-4">
            <div>
              <h3 class="text-h5 font-weight-black text-grey-darken-4 mb-1">Destinasi di Peta</h3>
              <p class="text-body-2 text-grey-darken-1 mb-0">
                Jelajahi {{ filteredMapDestinations.length }} titik destinasi yang tersemat pada peta di atas
              </p>
            </div>
          </div>

          <v-row>
            <v-col
              v-for="dest in filteredMapDestinations.slice(0, 8)"
              :key="dest.id"
              cols="6"
              sm="4"
              md="3"
            >
              <v-card elevation="0" class="destination-card h-100" :to="`/destinations/${dest.id}`">
                <div class="card-header-img-wrapper">
                  <img :src="getImageUrl(dest.primary_photo)" :alt="dest.name" class="card-top-img" />
                  <div class="card-img-overlay"></div>
                  <div class="card-tag-group" v-if="dest.category_name">
                    <span class="category-pill-solid">{{ dest.category_name }}</span>
                  </div>
                </div>
                <div class="pa-4">
                  <h4 class="card-title text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1">
                    {{ dest.name }}
                  </h4>
                  <div class="d-flex align-center justify-space-between mt-2">
                    <span class="text-caption text-grey-darken-1">
                      <v-icon size="14" color="#DC2626" class="mr-1">mdi-map-marker</v-icon>
                      Lihat Detail
                    </span>
                    <span class="action-link-text">
                      <v-icon size="14">mdi-arrow-right</v-icon>
                    </span>
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </div>
      </section>

      <!-- VIEW 2: GRID CARDS VIEW -->
      <section v-else class="mb-14">
        <div class="d-flex align-center justify-space-between flex-wrap ga-2 mb-6">
          <div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">Direktori Destinasi</h2>
            <p class="text-body-2 text-grey-darken-1 mb-0">
              Menampilkan {{ destinations.length }} dari {{ pagination.total }} destinasi komunitas
            </p>
          </div>
        </div>

        <!-- Grid Cards -->
        <v-row v-if="!loading && destinations.length > 0">
          <v-col v-for="destination in destinations" :key="destination.id" cols="6" sm="6" md="3">
            <v-card elevation="0" class="destination-card h-100" :to="`/destinations/${destination.id}`">
              <div class="card-header-img-wrapper">
                <img :src="getImageUrl(destination.primary_photo)" :alt="destination.name" class="card-top-img" />
                <div class="card-img-overlay"></div>
                <div class="card-tag-group" v-if="destination.category">
                  <span class="category-pill-solid">
                    {{ destination.category.name }}
                  </span>
                </div>
              </div>

              <div class="pa-4">
                <h4 class="card-title text-subtitle-1 font-weight-bold text-grey-darken-4 mb-2">
                  {{ destination.name }}
                </h4>

                <div class="d-flex align-center justify-space-between">
                  <div class="d-flex align-center ga-3">
                    <span class="stat-chip">
                      <v-icon size="14" color="#DC2626" class="mr-1">mdi-heart</v-icon>
                      {{ destination.likes_count || 0 }}
                    </span>
                    <span class="stat-chip">
                      <v-icon size="14" color="#6B7280" class="mr-1">mdi-comment-outline</v-icon>
                      {{ destination.comments_count || 0 }}
                    </span>
                  </div>
                  <span class="action-link-text">
                    <v-icon size="14">mdi-arrow-right</v-icon>
                  </span>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <div v-else-if="!loading && destinations.length === 0" class="empty-state-box text-center pa-12">
          <v-icon size="56" color="grey-lighten-1">mdi-map-marker-off-outline</v-icon>
          <h3 class="text-h5 font-weight-bold text-grey-darken-3 mt-4 mb-2">Tidak ada destinasi ditemukan</h3>
          <p class="text-body-2 text-grey-darken-1 mb-0">Coba ubah kata kunci atau filter pencarian</p>
        </div>

        <!-- Loading State -->
        <v-row v-else>
          <v-col v-for="n in 8" :key="n" cols="12" sm="6" md="4" lg="3">
            <v-skeleton-loader type="card"></v-skeleton-loader>
          </v-col>
        </v-row>

        <!-- Pagination -->
        <v-row v-if="pagination.total > pagination.per_page" class="mt-8">
          <v-col cols="12" class="d-flex justify-center">
            <v-pagination
              v-model="pagination.current_page"
              :length="Math.ceil(pagination.total / pagination.per_page)"
              color="#DC2626"
              rounded="circle"
              @update:model-value="fetchDestinations"
            ></v-pagination>
          </v-col>
        </v-row>
      </section>

      <!-- Community CTA Section -->
      <section class="mb-8">
        <div class="kolaborasi-card pa-8 pa-md-12 text-center">
          <v-row justify="center">
            <v-col cols="12" md="8">
              <div class="hero-badge-pill mb-4 d-inline-flex">
                <v-icon start size="16" color="#DC2626">mdi-heart-multiple-outline</v-icon>
                <span>KONTRIBUSI KOMUNITAS</span>
              </div>

              <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-3" style="letter-spacing: -0.02em;">
                Bagikan Tempat Favorit Anda
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-8 mx-auto" style="max-width: 560px; line-height: 1.7;">
                Punya rekomendasi tempat menarik di kotamu? Bagikan kepada komunitas dan bantu teman-teman menemukan destinasi baru.
              </p>

              <v-row justify="center" class="mb-8">
                <v-col cols="4" sm="4" class="text-center">
                  <div class="contrib-feature">
                    <v-icon size="32" color="#DC2626" class="mb-2">mdi-camera-plus-outline</v-icon>
                    <div class="text-subtitle-2 font-weight-bold text-grey-darken-3">Foto</div>
                    <div class="text-caption text-grey-darken-1">Upload foto lokasi</div>
                  </div>
                </v-col>
                <v-col cols="4" sm="4" class="text-center">
                  <div class="contrib-feature">
                    <v-icon size="32" color="#DC2626" class="mb-2">mdi-map-marker-plus-outline</v-icon>
                    <div class="text-subtitle-2 font-weight-bold text-grey-darken-3">Lokasi</div>
                    <div class="text-caption text-grey-darken-1">Tandai di peta</div>
                  </div>
                </v-col>
                <v-col cols="4" sm="4" class="text-center">
                  <div class="contrib-feature">
                    <v-icon size="32" color="#DC2626" class="mb-2">mdi-text-box-outline</v-icon>
                    <div class="text-subtitle-2 font-weight-bold text-grey-darken-3">Cerita</div>
                    <div class="text-caption text-grey-darken-1">Tulis pengalaman</div>
                  </div>
                </v-col>
              </v-row>

              <v-btn
                v-if="authStore.isLoggedIn"
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-8 text-white"
                to="/destinations/create"
              >
                <v-icon start>mdi-plus</v-icon>
                Tambah Destinasi Baru
              </v-btn>
              <v-btn
                v-else
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-8 text-white"
                to="/register"
              >
                <v-icon start>mdi-account-plus-outline</v-icon>
                Daftar untuk Berkontribusi
              </v-btn>
            </v-col>
          </v-row>
        </div>
      </section>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useRoute, useRouter, useSeoMeta } from '#app'

definePageMeta({
  layout: 'default'
})

useSeoMeta({
  title: 'Direktori & Peta Destinasi - Jalan Bareng',
  ogTitle: 'Direktori & Peta Destinasi - Jalan Bareng',
  description: 'Jelajahi peta interaktif dan rekomendasi destinasi ramah pejalan kaki yang dikurasi oleh komunitas Jalan Bareng.',
  ogDescription: 'Jelajahi peta interaktif dan rekomendasi destinasi ramah pejalan kaki yang dikurasi oleh komunitas Jalan Bareng.',
  ogImage: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const authStore = useAuthStore()
const { api } = useApi()
const route = useRoute()
const router = useRouter()

// View Mode State: 'grid' or 'map'
const viewMode = ref<'grid' | 'map'>((route.query.view as 'grid' | 'map') || 'grid')

const setViewMode = (mode: 'grid' | 'map') => {
  viewMode.value = mode
  router.replace({
    query: {
      ...route.query,
      view: mode === 'map' ? 'map' : undefined
    }
  })
}

// Data State
const destinations = ref([])
const categories = ref([])
const activations = ref([])
const mapDestinations = ref([])
const loading = ref(false)
const search = ref('')
const selectedCategory = ref<number | null>(null)
const selectedActivation = ref<number | null>(null)
const pagination = ref({
  current_page: 1,
  per_page: 12,
  total: 0
})

const { getImageUrl } = useImageUrl()

// Computed Filter Labels
const selectedCategoryName = computed(() => {
  if (!selectedCategory.value) return null
  const cat = categories.value.find((c: any) => c.id === selectedCategory.value)
  return cat ? cat.name : null
})

const selectedActivationName = computed(() => {
  if (!selectedActivation.value) return null
  const act = activations.value.find((a: any) => a.id === selectedActivation.value)
  return act ? act.name : null
})

// Activation Options for Select
const activationOptions = computed(() => {
  const options = [{ text: 'Semua Kota', value: null }]
  activations.value.forEach((act: any) => {
    options.push({
      text: act.name,
      value: act.id
    })
  })
  return options
})

// Filtered Map Destinations
const filteredMapDestinations = computed(() => {
  return mapDestinations.value.filter((dest: any) => {
    // Filter by activation
    if (selectedActivation.value && dest.activation_id !== selectedActivation.value) {
      return false
    }
    // Filter by category
    if (selectedCategory.value && dest.category_id !== selectedCategory.value) {
      return false
    }
    // Filter by search text
    if (search.value && search.value.trim()) {
      const q = search.value.toLowerCase().trim()
      const matchName = dest.name?.toLowerCase().includes(q)
      const matchAddress = dest.address?.toLowerCase().includes(q)
      return matchName || matchAddress
    }
    return true
  })
})

// Map Center Coordinates
const mapCenter = computed(() => {
  if (!selectedActivation.value) return null
  const activation = activations.value.find((a: any) => a.id === selectedActivation.value)
  if (activation && activation.city_latitude && activation.city_longitude) {
    return {
      lat: Number(activation.city_latitude),
      lng: Number(activation.city_longitude)
    }
  }
  return null
})

const resetFilters = () => {
  search.value = ''
  selectedCategory.value = null
  selectedActivation.value = null
  pagination.value.current_page = 1
  fetchDestinations()
}

const selectCategory = (categoryId: number | null) => {
  selectedCategory.value = categoryId
  pagination.value.current_page = 1
  fetchDestinations()
}

// Fetch Paginated Destinations for Grid View
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

// Fetch All Destinations with Coordinates for Map View
const fetchMapDestinations = async () => {
  try {
    const response = await api.get('/destinations/map/all')
    mapDestinations.value = response.data.destinations || []
  } catch (error) {
    console.error('Error fetching map destinations:', error)
  }
}

// Fetch Categories
const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.categories || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Fetch Activations
const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

onMounted(() => {
  if (route.query.activation_id) {
    selectedActivation.value = parseInt(route.query.activation_id as string)
  }
  if (route.query.category_id) {
    selectedCategory.value = parseInt(route.query.category_id as string)
  }
  if (route.query.view === 'map') {
    viewMode.value = 'map'
  }

  fetchDestinations()
  fetchMapDestinations()
  fetchCategories()
  fetchActivations()
})
</script>

<style scoped>
/* Hero Section - Editorial Light */
.destinations-hero {
  background: #FAFAFA;
  padding-top: 48px;
  padding-bottom: 56px;
  border-bottom: 1px solid #F1F5F9;
}

.hero-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 16px;
  border-radius: 9999px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  font-size: 0.74rem;
  font-weight: 700;
  color: #DC2626;
  letter-spacing: 0.06em;
}

.hero-title {
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  letter-spacing: -0.035em;
  line-height: 1.12;
  color: #111827;
}

.text-primary-red {
  color: #DC2626;
}

.hero-subtitle {
  font-size: 1.1rem;
  line-height: 1.6;
  max-width: 680px;
}

/* Category Filter Buttons */
.category-filters-wrapper {
  margin-top: 8px;
}

.filter-btn {
  display: inline-flex;
  align-items: center;
  padding: 8px 18px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  color: #4B5563;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #111827;
}

.filter-btn.active {
  background: #DC2626;
  border-color: #DC2626;
  color: #FFFFFF;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

/* Search/Filter Bar */
.search-filter-bar {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 20px;
}

/* View Switcher Tabs */
.view-switch-tabs {
  display: inline-flex;
  background: #F1F5F9;
  padding: 4px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
}

.view-switch-btn {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 9999px;
  border: none;
  background: transparent;
  color: #64748B;
  font-size: 0.84rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.view-switch-btn:hover {
  color: #1E293B;
}

.view-switch-btn.active {
  background: #FFFFFF;
  color: #DC2626;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

/* Map View Card */
.map-view-card {
  border: 1px solid #E2E8F0;
  border-radius: 24px;
  background: #FFFFFF;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
}

.map-card-header {
  background: #FAFAFA;
}

.count-pill {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 9999px;
  background: #FEF2F2;
  color: #DC2626;
  font-size: 0.75rem;
  font-weight: 700;
}

.map-embed-wrapper {
  height: 560px;
  width: 100%;
  position: relative;
  background: #E2E8F0;
}

/* Destination Cards */
.destination-card {
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  text-decoration: none;
}

.destination-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
  border-color: #CBD5E1;
}

.card-header-img-wrapper {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: #F3F4F6;
}

.card-top-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.destination-card:hover .card-top-img {
  transform: scale(1.05);
}

.card-img-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.12) 0%, rgba(0, 0, 0, 0) 50%);
}

.card-tag-group {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 2;
}

.category-pill-solid {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 9999px;
  background: rgba(17, 24, 39, 0.75);
  backdrop-filter: blur(4px);
  color: #FFFFFF;
  font-size: 0.72rem;
  font-weight: 600;
}

.card-title {
  letter-spacing: -0.015em;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.stat-chip {
  display: inline-flex;
  align-items: center;
  font-size: 0.78rem;
  color: #6B7280;
  font-weight: 500;
}

.action-link-text {
  display: inline-flex;
  align-items: center;
  color: #DC2626;
  transition: transform 0.2s ease;
}

.destination-card:hover .action-link-text {
  transform: translateX(3px);
}

/* Kolaborasi / CTA Section */
.kolaborasi-card {
  border-radius: 24px;
  border: 1px solid #E2E8F0;
  background: #FAFAFA;
}

.contrib-feature {
  padding: 1rem 0.5rem;
  transition: transform 0.2s ease;
}

.contrib-feature:hover {
  transform: translateY(-3px);
}

/* Empty State */
.empty-state-box {
  background: #FAFAFA;
  border: 1px dashed #CBD5E1;
  border-radius: 20px;
}

/* Mobile Responsiveness */
@media (max-width: 600px) {
  .destinations-hero {
    padding-top: 32px;
    padding-bottom: 32px;
  }

  .hero-title {
    font-size: 1.95rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }

  .filter-btn {
    font-size: 0.8rem;
    padding: 6px 14px;
  }

  .card-header-img-wrapper {
    height: 160px;
  }

  .map-embed-wrapper {
    height: 380px;
  }
}
</style>
