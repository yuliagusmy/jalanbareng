<template>
  <div>
    <!-- Compact Header -->
    <v-sheet color="primary" class="py-8">
      <v-container>
        <div class="text-center">
          <v-icon color="white" size="48">mdi-map-search</v-icon>
          <h1 class="text-h4 text-md-h3 font-weight-bold mt-3 mb-2 text-white">Peta Destinasi</h1>
          <p class="text-body-1 text-white" style="opacity: 0.9">
            Jelajahi destinasi menarik dari berbagai kota
          </p>
        </div>
      </v-container>
    </v-sheet>

    <!-- Filter Section -->
    <v-container class="py-6">
      <v-card elevation="2" rounded="lg" class="filter-card">
        <v-card-text class="pa-4 pa-md-6">
          <v-row align="center" dense>
            <v-col cols="12" md="9">
              <v-select v-model="selectedActivation" :items="activationOptions" item-title="text" item-value="value"
                label="Pilih Aktivasi Kota" variant="outlined" density="comfortable" rounded="lg"
                prepend-inner-icon="mdi-city" hide-details :loading="loadingActivations">
                <template v-slot:selection="{ item }">
                  <div class="d-flex align-center">
                    <v-icon size="small" class="mr-2" color="primary">mdi-map-marker-radius</v-icon>
                    <span>{{ item.title }}</span>
                  </div>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" md="3" class="text-center text-md-right">
              <v-chip color="primary" variant="tonal" size="large" class="px-4">
                <v-icon start size="small">mdi-map-marker</v-icon>
                <span class="font-weight-bold">{{ filteredMapDestinations.length }}</span>
                <span class="ml-1 hidden-sm-and-down">Destinasi</span>
              </v-chip>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-container>

    <!-- Map Section -->
    <v-container class="pb-0">
      <v-card elevation="3" rounded="lg" class="overflow-hidden map-container">
        <div class="map-wrapper">
          <ClientOnly>
            <DestinationMapViewer v-if="filteredMapDestinations.length > 0" :destinations="filteredMapDestinations"
              :mapCenter="mapCenter" />
            <div v-else class="d-flex align-center justify-center h-100 pa-8">
              <div class="text-center">
                <v-icon size="64" color="grey-lighten-1">mdi-map-marker-off</v-icon>
                <p class="text-h6 text-grey mt-4 mb-0">Tidak ada destinasi untuk aktivasi ini</p>
              </div>
            </div>
          </ClientOnly>
        </div>
      </v-card>
    </v-container>

    <!-- List Section -->
    <v-container class="py-8 py-md-12">
      <div class="mb-6 mb-md-8">
        <h2 class="text-h5 text-md-h4 font-weight-bold mb-2">Daftar Destinasi</h2>
        <p class="text-body-2 text-grey-darken-1 mb-0">Temukan tempat favoritmu di sini</p>
      </div>

      <v-row v-if="!loadingPaginated && filteredPaginatedDestinations.length > 0">
        <v-col v-for="destination in filteredPaginatedDestinations" :key="destination.id" cols="12" sm="6" md="4"
          lg="3">
          <v-card elevation="2" class="destination-card h-100" :to="`/destinations/${destination.id}`" hover>
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
      </v-row>

      <!-- Empty State for Filter -->
      <v-row v-else-if="!loadingPaginated && filteredPaginatedDestinations.length === 0">
        <v-col cols="12">
          <v-card elevation="0" rounded="xl" class="pa-12 text-center">
            <v-icon size="80" color="grey-lighten-2">mdi-filter-off-outline</v-icon>
            <h3 class="text-h5 font-weight-bold mt-4 mb-2">Tidak ada destinasi</h3>
            <p class="text-grey">Tidak ada destinasi untuk aktivasi yang dipilih</p>
          </v-card>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <v-row v-else>
        <v-col v-for="n in 8" :key="n" cols="12" sm="6" md="4" lg="3">
          <v-skeleton-loader type="card-avatar"></v-skeleton-loader>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row v-if="pagination.total > pagination.per_page" class="mt-8">
        <v-col cols="12" class="d-flex justify-center">
          <v-pagination v-model="pagination.current_page" :length="Math.ceil(pagination.total / pagination.per_page)"
            color="primary" rounded="circle" @update:model-value="fetchPaginatedDestinations"></v-pagination>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, defineAsyncComponent } from 'vue'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'

const DestinationMapViewer = defineAsyncComponent(() => import('~/components/destinations/DestinationMapViewer.vue'))

definePageMeta({
  layout: 'solid'
})

useSeoMeta({
  title: 'Peta Destinasi - Jalan Bareng',
  ogTitle: 'Peta Destinasi - Jalan Bareng',
  description: 'Jelajahi peta destinasi wisata populer di berbagai kota dan temukan tempat menarik di sekitarmu.',
  ogDescription: 'Jelajahi peta destinasi wisata populer di berbagai kota dan temukan tempat menarik di sekitarmu.',
  ogImage: 'https://images.unsplash.com/photo-1524661135-423995f22d0b?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const { api } = useApi()
const config = useRuntimeConfig()

// Activations
const activations = ref([])
const selectedActivation = ref(null)
const loadingActivations = ref(false)

// For the map
const mapDestinations = ref([])

// For the paginated list
const paginatedDestinations = ref([])
const loadingPaginated = ref(true)
const pagination = ref({
  current_page: 1,
  per_page: 12,
  total: 0
})

const getImageUrl = (path: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&h=600&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

// Fetch all destinations for the map
const fetchMapDestinations = async () => {
  try {
    const response = await api.get('/destinations/map/all')
    mapDestinations.value = response.data.destinations || []
  } catch (error) {
    console.error('Error fetching destinations for map:', error)
  }
}

// Fetch city activations
const fetchCityActivations = async () => {
  loadingActivations.value = true
  try {
    const response = await api.get('/activations', {
      params: {
        category: 'city',
        is_active: 1
      }
    })
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    loadingActivations.value = false
  }
}

// Activation options for dropdown
const activationOptions = computed(() => {
  const options = [
    { text: 'Semua Destinasi', value: null }
  ]

  activations.value.forEach(activation => {
    options.push({
      text: activation.name,
      value: activation.id
    })
  })

  return options
})

// Filtered destinations for map
const filteredMapDestinations = computed(() => {
  if (!selectedActivation.value) {
    return mapDestinations.value
  }

  return mapDestinations.value.filter(destination =>
    destination.activation_id === selectedActivation.value
  )
})

// Filtered destinations for paginated list
const filteredPaginatedDestinations = computed(() => {
  if (!selectedActivation.value) {
    return paginatedDestinations.value
  }

  return paginatedDestinations.value.filter(destination =>
    destination.activation_id === selectedActivation.value
  )
})

// Map center based on selected activation
const mapCenter = computed(() => {
  if (!selectedActivation.value) {
    // No activation selected - return null to use default
    return null
  }

  // Find the selected activation
  const activation = activations.value.find(a => a.id === selectedActivation.value)
  if (activation && activation.city_latitude && activation.city_longitude) {
    return {
      lat: Number(activation.city_latitude),
      lng: Number(activation.city_longitude)
    }
  }

  return null
})

// Fetch destinations for the paginated list
const fetchPaginatedDestinations = async (page = 1) => {
  loadingPaginated.value = true
  try {
    const params = {
      page: page,
      per_page: pagination.value.per_page
    }
    const response = await api.get('/destinations', { params })

    paginatedDestinations.value = response.data.data || []
    pagination.value = {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error fetching paginated destinations:', error)
  } finally {
    loadingPaginated.value = false
  }
}

onMounted(() => {
  fetchMapDestinations()
  fetchPaginatedDestinations(1)
  fetchCityActivations()
})
</script>

<style scoped>
/* Destination Cards - Consistent with /destinations page */
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
  .destination-image-wrapper {
    height: 160px;
  }
}
</style>

<style scoped>
/* Modern, Clean Styles */
.filter-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.filter-card:hover {
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.map-container {
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.map-wrapper {
  height: 500px;
  width: 100%;
}

@media (min-width: 960px) {
  .map-wrapper {
    height: 600px;
  }
}

/* Destination Cards - Mobile First */
.destination-card {
  border-radius: 12px;
  border: 1px solid rgba(0, 0, 0, 0.06);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  height: 100%;
}

.destination-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  border-color: rgba(0, 0, 0, 0.12);
}

.destination-image-wrapper {
  position: relative;
  height: 180px;
  overflow: hidden;
  background: #f5f5f5;
}

@media (min-width: 600px) {
  .destination-image-wrapper {
    height: 200px;
  }
}

.destination-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.destination-card:hover .destination-image {
  transform: scale(1.08);
}

.destination-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 50%);
  pointer-events: none;
}

.destination-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 2;
}

@media (min-width: 600px) {
  .destination-badge {
    top: 12px;
    left: 12px;
  }
}

/* Utility classes for mobile */
@media (max-width: 959px) {
  .hidden-sm-and-down {
    display: none !important;
  }
}
</style>
