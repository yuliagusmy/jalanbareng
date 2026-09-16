<template>
  <div>
    <v-sheet class="hero-section mb-8" :style="{
      background: 'linear-gradient(135deg, #d084e8 0%, #e05167 100%)',
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

      <v-container class="hero-content" style="position: relative; z-index: 2; padding-top: 100px;">
        <v-row align="center" class="pt-16 pb-16 pb-md-20">
          <v-col cols="12" md="8" class="text-white pb-8 pb-md-12">
            <div class="animate-fade-in">
              <h1 class="text-h3 text-md-h2 font-weight-bold mb-6" style="line-height: 1.2;">
                Gabung Dalam Kegiatan Seru<br>
                <span class="gradient-text-white">Bersama Jalan Bareng</span>
              </h1>
              <p class="text-h6 mb-0" style="opacity: 0.95; max-width: 650px; line-height: 1.6;">
                Bergabunglah dalam kegiatan jalan kaki, workshop, dan acara seru lainnya bersama Teman Jalan Makassar
              </p>
            </div>
          </v-col>
          <v-col cols="12" md="4" class="text-md-right">
            <v-btn v-if="authStore.isCommunityAdmin || authStore.isAdmin" color="white" size="x-large"
              to="/events/create" prepend-icon="mdi-plus" rounded="pill" elevation="8" class="px-8">Buat Event
              Baru</v-btn>
            <v-btn v-else color="white" size="x-large" href="#events-list" prepend-icon="mdi-arrow-down" rounded="pill"
              elevation="8" class="px-8">Lihat Event</v-btn>
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
      <!-- Filter Section -->
      <v-row class="mb-6">
        <v-col cols="12" md="4">
          <v-select v-model="selectedActivation" :items="activations" item-title="name" item-value="id"
            label="Filter berdasarkan Aktivasi" prepend-inner-icon="mdi-star-four-points" variant="outlined"
            rounded="lg" density="comfortable" clearable @update:model-value="handleActivationChange"></v-select>
        </v-col>
      </v-row>

      <!-- Tabs -->
      <div id="events-list" class="mb-8">
        <v-tabs v-model="tab" color="primary" rounded="lg" class="mb-6" height="60">
          <v-tab value="upcoming" class="text-h6">
            <v-icon start>mdi-calendar-clock</v-icon>
            Event Mendatang
          </v-tab>
          <v-tab value="past" class="text-h6">
            <v-icon start>mdi-calendar-check</v-icon>
            Event Selesai
          </v-tab>
        </v-tabs>
      </div>

      <!-- Events Grid -->
      <v-window v-model="tab">
        <v-window-item value="upcoming">
          <div>
            <v-row v-if="!loadingUpcoming" class="pb-6">
              <v-col v-for="event in upcomingEvents" :key="event.id" cols="12" sm="6" md="4">
                <v-card elevation="0" class="event-card h-100" :to="`/events/${event.id}`">
                  <div class="event-image-wrapper">
                    <img :src="getImageUrl(event.poster)" :alt="event.name" class="event-image" />
                    <div class="event-overlay"></div>
                    <div class="event-badge">
                      <v-chip :color="event.type === 'walking' ? 'success' : 'primary'" size="small">
                        <v-icon start size="small">
                          {{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar' }}
                        </v-icon>
                        {{ event.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}
                      </v-chip>
                    </div>
                  </div>
                  <v-card-text class="pa-6">
                    <h4 class="text-h6 font-weight-bold mb-3">{{ event.name }}</h4>
                    <div class="d-flex align-center text-grey-darken-1 mb-2">
                      <v-icon size="small" class="mr-2">mdi-calendar</v-icon>
                      <span class="text-caption">{{ formatDate(event.date) }}</span>
                    </div>
                    <p class="text-caption text-grey-darken-1 mb-0">{{ stripHtml(event.description)?.substring(0, 80)
                    }}...</p>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col v-if="upcomingEvents.length === 0" cols="12">
                <v-card elevation="0" class="text-center pa-8" rounded="xl">
                  <v-icon size="64" color="grey">mdi-calendar-blank</v-icon>
                  <h3 class="text-h5 mt-4">Tidak ada event mendatang</h3>
                  <p class="text-body-1 mt-2">Nantikan event menarik dari komunitas!</p>
                </v-card>
              </v-col>
            </v-row>
            <v-row v-else-if="loadingUpcoming">
              <v-col v-for="n in 6" :key="n" cols="12" sm="6" md="4">
                <v-skeleton-loader type="card"></v-skeleton-loader>
              </v-col>
            </v-row>
            <v-row v-if="upcomingPagination.last_page > 1" class="mt-8">
              <v-col cols="12" class="d-flex justify-center">
                <v-pagination v-model="upcomingPagination.current_page" :length="upcomingPagination.last_page"
                  color="primary" rounded="circle"
                  @update:model-value="(page) => fetchEvents('upcoming', page)"></v-pagination>
              </v-col>
            </v-row>
          </div>
        </v-window-item> <v-window-item value="past">
          <div>
            <v-row v-if="!loadingPast" class="pb-6">
              <v-col v-for="event in pastEvents" :key="event.id" cols="12" sm="6" md="4">
                <v-card elevation="0" class="event-card h-100" :to="`/events/${event.id}`">
                  <div class="event-image-wrapper">
                    <img :src="getImageUrl(event.poster)" :alt="event.name" class="event-image" />
                    <div class="event-overlay"></div>
                    <div class="event-badge">
                      <v-chip :color="event.type === 'walking' ? 'success' : 'primary'" size="small">
                        <v-icon start size="small">
                          {{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar' }}
                        </v-icon>
                        {{ event.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}
                      </v-chip>
                    </div>
                  </div>
                  <v-card-text class="pa-6">
                    <h4 class="text-h6 font-weight-bold mb-3">{{ event.name }}</h4>
                    <div class="d-flex align-center text-grey-darken-1 mb-2">
                      <v-icon size="small" class="mr-2">mdi-calendar</v-icon>
                      <span class="text-caption">{{ formatDate(event.date) }}</span>
                    </div>
                    <p class="text-caption text-grey-darken-1 mb-0">{{ stripHtml(event.description)?.substring(0, 80)
                    }}...</p>
                  </v-card-text>
                </v-card>
              </v-col>
              <v-col v-if="pastEvents.length === 0" cols="12">
                <v-card elevation="0" class="text-center pa-8" rounded="xl">
                  <v-icon size="64" color="grey">mdi-calendar-check</v-icon>
                  <h3 class="text-h5 mt-4">Tidak ada event yang selesai</h3>
                </v-card>
              </v-col>
            </v-row>
            <v-row v-else-if="loadingPast">
              <v-col v-for="n in 6" :key="n" cols="12" sm="6" md="4">
                <v-skeleton-loader type="card"></v-skeleton-loader>
              </v-col>
            </v-row>
            <v-row v-if="pastPagination.last_page > 1" class="mt-8">
              <v-col cols="12" class="d-flex justify-center">
                <v-pagination v-model="pastPagination.current_page" :length="pastPagination.last_page" color="primary"
                  rounded="circle" @update:model-value="(page) => fetchEvents('past', page)"></v-pagination>
              </v-col>
            </v-row>
          </div>
        </v-window-item>
      </v-window>

      <!-- Event Types Showcase Section -->
      <v-row class="mt-16 mb-8">
        <v-col cols="12">
          <div class="text-center mb-8 px-4">
            <v-chip color="secondary" variant="flat" class="mb-4">
              <v-icon start>mdi-star</v-icon>
              Jenis Event
            </v-chip>
            <h2 class="text-h3 font-weight-bold mb-3">Beragam Kegiatan untuk Anda</h2>
            <p class="text-h6 text-grey-darken-1">Temukan event yang sesuai dengan minat Anda</p>
          </div>
          <v-row>
            <v-col cols="12" md="6">
              <v-card elevation="0" rounded="xl" class="event-type-card overflow-hidden"
                style="background: linear-gradient(135deg, #3ec770 0%, #2dcdb8 100%);">
                <v-card-text class="pa-8 pa-md-12 text-white">
                  <v-icon size="72" color="white" class="mb-4">mdi-walk</v-icon>
                  <h3 class="text-h4 font-weight-bold mb-4">Event Jalan Kaki</h3>
                  <p class="text-h6 mb-6" style="opacity: 0.95;">Jelajahi sudut-sudut kota Makassar sambil berjalan kaki
                    bersama komunitas</p>
                  <v-chip color="white" variant="flat" size="large" class="mt-4">
                    <v-icon start>mdi-check-circle</v-icon>
                    Gratis & Terbuka
                  </v-chip>
                </v-card-text>
              </v-card>
            </v-col>
            <v-col cols="12" md="6">
              <v-card elevation="0" rounded="xl" class="event-type-card overflow-hidden"
                style="background: linear-gradient(135deg, #e06488 0%, #e8c742 100%);">
                <v-card-text class="pa-8 pa-md-12 text-white">
                  <v-icon size="72" color="white" class="mb-4">mdi-calendar-star</v-icon>
                  <h3 class="text-h4 font-weight-bold mb-4">Event Regular</h3>
                  <p class="text-h6 mb-6" style="opacity: 0.95;">Workshop, seminar, dan kegiatan sosial lainnya yang
                    bermanfaat</p>
                  <v-chip color="white" variant="flat" size="large" class="mt-4">
                    <v-icon start>mdi-form-select</v-icon>
                    Pendaftaran Online
                  </v-chip>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>

      <!-- How to Join Section -->
      <v-row class="mt-16 mb-8">
        <v-col cols="12">
          <v-card elevation="0" rounded="xl" class="pa-8 pa-md-12"
            style="background: linear-gradient(135deg, #5d71c9 0%, #6b4591 100%);">
            <div class="text-center text-white mb-8">
              <h2 class="text-h3 font-weight-bold mb-3">Cara Bergabung dalam Event</h2>
              <p class="text-h6" style="opacity: 0.95;">Mudah dan cepat! Ikuti 3 langkah sederhana ini</p>
            </div>
            <v-row justify="center">
              <v-col cols="12" md="4" v-for="(step, index) in howToJoinSteps" :key="index" class="d-flex">
                <v-card elevation="0" rounded="xl" class="pa-6 text-center how-to-step d-flex flex-column"
                  color="rgba(255,255,255,0.1)" style="width: 100%;">
                  <div class="d-flex align-center justify-center mb-4">
                    <v-icon size="48" color="white" class="mr-4">{{ step.icon }}</v-icon>
                    <h4 class="text-h5 font-weight-bold text-white">{{ step.title }}</h4>
                  </div>
                  <p class="text-body-1 text-white" style="opacity: 0.9;">{{ step.description }}</p>
                </v-card>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'

definePageMeta({
  layout: 'hero'
})

useSeoMeta({
  title: 'Event Komunitas - Jalan Bareng',
  ogTitle: 'Event Komunitas - Jalan Bareng',
  description: 'Gabung dalam kegiatan seru bersama Jalan Bareng.',
  ogDescription: 'Gabung dalam kegiatan seru bersama Jalan Bareng.',
  ogImage: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const authStore = useAuthStore()
const { api } = useApi()
const config = useRuntimeConfig()

const tab = ref('upcoming')
const upcomingEvents = ref([])
const pastEvents = ref([])
const loadingUpcoming = ref(true)
const loadingPast = ref(false)

const upcomingPagination = ref({ current_page: 1, per_page: 6, total: 0, last_page: 1 })
const pastPagination = ref({ current_page: 1, per_page: 6, total: 0, last_page: 1 })
const selectedActivation = ref(null)
const activations = ref([])

const eventStats = ref([
  { icon: 'mdi-calendar-check', value: '0', label: 'Event Mendatang', image: 'https://images.unsplash.com/photo-1506869640319-fe1a24fd76dc?w=400&h=300&fit=crop', overlay: ['rgba(240, 147, 251, 0.8)', 'rgba(245, 87, 108, 0.8)'] },
  { icon: 'mdi-calendar-clock', value: '0', label: 'Event Selesai', image: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=400&h=300&fit=crop', overlay: ['rgba(79, 172, 254, 0.75)', 'rgba(0, 242, 254, 0.75)'] },
  { icon: 'mdi-walk', value: '0', label: 'Jalan Kaki', image: 'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=400&h=300&fit=crop', overlay: ['rgba(67, 233, 123, 0.75)', 'rgba(56, 249, 215, 0.75)'] },
  { icon: 'mdi-account-multiple', value: '500+', label: 'Partisipan', image: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?w=400&h=300&fit=crop', overlay: ['rgba(102, 126, 234, 0.8)', 'rgba(118, 75, 162, 0.8)'] },
])

const howToJoinSteps = ref([
  { icon: 'mdi-calendar-search', title: 'Pilih Event', description: 'Browse dan pilih event yang sesuai dengan minat dan jadwal Anda' },
  { icon: 'mdi-form-select', title: 'Daftar Online', description: 'Klik tombol daftar dan isi form pendaftaran melalui Google Form' },
  { icon: 'mdi-party-popper', title: 'Ikuti Event', description: 'Datang di waktu dan tempat yang ditentukan, nikmati kegiatan bersama!' }
])

const fetchEvents = async (status = 'upcoming', page = 1) => {
  const loading = status === 'upcoming' ? loadingUpcoming : loadingPast
  loading.value = true

  try {
    const params: any = { status, page, per_page: 6 }

    // Add activation filter if selected
    if (selectedActivation.value) {
      params.activation_id = selectedActivation.value
    }

    const response = await api.get('/events', { params })
    const { data, ...pagination } = response.data

    if (status === 'upcoming') {
      upcomingEvents.value = data
      upcomingPagination.value = pagination
      eventStats.value[0].value = pagination.total.toString()
    } else {
      pastEvents.value = data
      pastPagination.value = pagination
      eventStats.value[1].value = pagination.total.toString()
    }
  } catch (error) {
    console.error(`Error fetching ${status} events:`, error)
  } finally {
    loading.value = false
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

const handleActivationChange = () => {
  // Reset to first page and fetch both tabs
  upcomingPagination.value.current_page = 1
  pastPagination.value.current_page = 1
  fetchEvents('upcoming', 1)
  if (tab.value === 'past') {
    fetchEvents('past', 1)
  }
}

watch(tab, (newTab) => {
  if (newTab === 'past' && pastEvents.value.length === 0) {
    fetchEvents('past', 1)
  }
})

onMounted(() => {
  // Read activation_id from URL query parameter
  const route = useRoute()
  if (route.query.activation_id) {
    selectedActivation.value = Number.parseInt(route.query.activation_id as string)
  }

  fetchActivations()
  fetchEvents('upcoming', 1)
})

const { getImageUrl } = useImageUrl()

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
}

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}
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

.gradient-text-white {
  background: linear-gradient(135deg, #fff 0%, #ffe0f0 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Event Cards - Consistent with Home Page */
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

/* Event Type Cards */
.event-type-card {
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.event-type-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
}

/* How to Join Steps */
.how-to-step {
  transition: all 0.3s ease;
}

.how-to-step:hover {
  transform: translateY(-5px);
  background: rgba(255, 255, 255, 0.15) !important;
}

/* Responsive adjustments */
@media (max-width: 600px) {
  .event-image-wrapper {
    height: 180px;
  }
}
</style>
