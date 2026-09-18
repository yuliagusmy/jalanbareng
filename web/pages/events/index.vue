<template>
  <div class="events-index-page">
    <!-- Editorial Hero Section -->
    <section class="events-hero">
      <v-container class="hero-content">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Eyebrow Badge -->
            <div class="hero-badge-pill mb-4">
              <v-icon start size="16" color="#DC2626">mdi-calendar-multiselect</v-icon>
              <span>AGENDA &amp; JADWAL JALAN</span>
            </div>

            <!-- Main Headline -->
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              Langkah Bersama,
              <span class="text-primary-red">Jadwal &amp; Teman Baru</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Temukan agenda jalan santai mingguan, eksplorasi sudut kota bersejarah, dan kegiatan tematik ramah pejalan di sekitarmu.
            </p>

            <!-- Actions Row -->
            <div class="d-flex align-center justify-center flex-wrap ga-3">
              <v-btn
                v-if="authStore.isCommunityAdmin || authStore.isAdmin"
                to="/events/create"
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-6 text-white"
              >
                <v-icon start size="18">mdi-plus</v-icon>
                Buat Event Baru
              </v-btn>

              <button
                type="button"
                class="btn-outline-pill"
                @click="scrollToEvents"
              >
                <v-icon start size="18">mdi-arrow-down</v-icon>
                Lihat Semua Jadwal
              </button>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Events Container -->
    <v-container id="events-content" class="py-6 py-md-10">
      <!-- Filter & Tabs Bar -->
      <div class="filter-bar-card pa-4 pa-md-6 mb-8">
        <v-row align="center" justify="space-between" class="ga-y-4">
          <!-- Status Tabs (Pill Switcher) -->
          <v-col cols="12" sm="7" md="6">
            <div class="tab-switcher-pill">
              <button
                type="button"
                :class="['tab-pill-btn', { active: tab === 'upcoming' }]"
                @click="tab = 'upcoming'"
              >
                <v-icon start size="18">mdi-calendar-clock-outline</v-icon>
                Event Mendatang
                <span v-if="upcomingPagination.total > 0" class="counter-badge">
                  {{ upcomingPagination.total }}
                </span>
              </button>
              <button
                type="button"
                :class="['tab-pill-btn', { active: tab === 'past' }]"
                @click="tab = 'past'"
              >
                <v-icon start size="18">mdi-calendar-check-outline</v-icon>
                Telah Terlaksana
              </button>
            </div>
          </v-col>

          <!-- Filter Dropdown by Activation -->
          <v-col cols="12" sm="5" md="4">
            <v-select
              v-model="selectedActivation"
              :items="activations"
              item-title="name"
              item-value="id"
              placeholder="Semua Aktivasi / Chapter"
              prepend-inner-icon="mdi-filter-variant"
              variant="outlined"
              rounded="pill"
              density="compact"
              hide-details
              clearable
              class="filter-select"
              @update:model-value="handleActivationChange"
            />
          </v-col>
        </v-row>
      </div>

      <!-- Events Listing Window -->
      <v-window v-model="tab">
        <!-- Upcoming Events Tab -->
        <v-window-item value="upcoming">
          <div>
            <!-- Events Grid -->
            <v-row v-if="!loadingUpcoming && upcomingEvents.length > 0">
              <v-col
                v-for="event in upcomingEvents"
                :key="event.id"
                cols="12"
                sm="6"
                md="4"
              >
                <v-card
                  elevation="0"
                  class="event-card h-100 d-flex flex-column"
                  :to="`/events/${event.id}`"
                >
                  <!-- Poster Wrapper -->
                  <div class="event-image-wrapper">
                    <img
                      :src="getImageUrl(event.poster)"
                      :alt="event.name"
                      class="event-poster-img"
                    />
                    <div class="event-overlay"></div>

                    <!-- Category Badge -->
                    <div class="event-tag-badge">
                      <span :class="['type-pill', event.type === 'walking' ? 'walking' : 'regular']">
                        <v-icon start size="14">
                          {{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar-text-outline' }}
                        </v-icon>
                        {{ event.type === 'walking' ? 'Jalan Santai' : 'Tematik' }}
                      </span>
                    </div>
                  </div>

                  <!-- Event Details Body -->
                  <div class="pa-6 d-flex flex-column flex-grow-1">
                    <!-- Date & Time Row -->
                    <div class="d-flex align-center date-row mb-2">
                      <v-icon size="16" color="#DC2626" class="mr-2">mdi-calendar-blank-outline</v-icon>
                      <span class="date-text">{{ formatDate(event.date) }}</span>
                    </div>

                    <!-- Event Name -->
                    <h3 class="card-title text-h6 font-weight-bold text-grey-darken-4 mb-2">
                      {{ event.name }}
                    </h3>

                    <!-- Excerpt Description -->
                    <p class="card-desc text-body-2 text-grey-darken-1 mb-4 flex-grow-1">
                      {{ stripHtml(event.description) }}
                    </p>

                    <!-- Footer Details -->
                    <div class="pt-3 border-top d-flex align-center justify-space-between mt-auto">
                      <span class="status-indicator">
                        <span class="pulse-dot"></span>
                        Pendaftaran Terbuka
                      </span>
                      <span class="action-link-text">
                        Detail Jadwal
                        <v-icon size="14" class="ml-1">mdi-arrow-right</v-icon>
                      </span>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>

            <!-- Loading Skeletons -->
            <v-row v-else-if="loadingUpcoming">
              <v-col v-for="n in 6" :key="n" cols="12" sm="6" md="4">
                <v-skeleton-loader type="image, article" class="rounded-xl border" />
              </v-col>
            </v-row>

            <!-- Empty State for Upcoming -->
            <div v-else class="empty-state-box text-center py-16">
              <v-icon size="56" color="grey-lighten-1" class="mb-3">mdi-calendar-blank-outline</v-icon>
              <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">Belum ada agenda mendatang</h3>
              <p class="text-body-2 text-grey-darken-1 mx-auto mb-4" style="max-width: 420px;">
                Agenda jalan berikutnya sedang disiapkan oleh komunitas. Silakan pantau berkala atau lihat dokumentasi event yang telah selesai.
              </p>
              <button
                type="button"
                class="btn-outline-pill"
                @click="tab = 'past'"
              >
                Lihat Event Terdahulu
              </button>
            </div>

            <!-- Pagination -->
            <div v-if="upcomingPagination.last_page > 1" class="d-flex justify-center mt-10">
              <v-pagination
                v-model="upcomingPagination.current_page"
                :length="upcomingPagination.last_page"
                color="#DC2626"
                rounded="circle"
                @update:model-value="(page) => fetchEvents('upcoming', page)"
              />
            </div>
          </div>
        </v-window-item>

        <!-- Past Events Tab -->
        <v-window-item value="past">
          <div>
            <!-- Events Grid -->
            <v-row v-if="!loadingPast && pastEvents.length > 0">
              <v-col
                v-for="event in pastEvents"
                :key="event.id"
                cols="12"
                sm="6"
                md="4"
              >
                <v-card
                  elevation="0"
                  class="event-card past-card h-100 d-flex flex-column"
                  :to="`/events/${event.id}`"
                >
                  <!-- Poster Wrapper -->
                  <div class="event-image-wrapper">
                    <img
                      :src="getImageUrl(event.poster)"
                      :alt="event.name"
                      class="event-poster-img"
                    />
                    <div class="event-overlay"></div>

                    <!-- Category Badge -->
                    <div class="event-tag-badge">
                      <span class="type-pill completed">
                        <v-icon start size="14">mdi-check</v-icon>
                        Selesai
                      </span>
                    </div>
                  </div>

                  <!-- Event Details Body -->
                  <div class="pa-6 d-flex flex-column flex-grow-1">
                    <!-- Date & Time Row -->
                    <div class="d-flex align-center date-row mb-2">
                      <v-icon size="16" color="grey" class="mr-2">mdi-calendar-check-outline</v-icon>
                      <span class="date-text text-grey">{{ formatDate(event.date) }}</span>
                    </div>

                    <!-- Event Name -->
                    <h3 class="card-title text-h6 font-weight-bold text-grey-darken-3 mb-2">
                      {{ event.name }}
                    </h3>

                    <!-- Excerpt Description -->
                    <p class="card-desc text-body-2 text-grey-darken-1 mb-4 flex-grow-1">
                      {{ stripHtml(event.description) }}
                    </p>

                    <!-- Footer Details -->
                    <div class="pt-3 border-top d-flex align-center justify-space-between mt-auto">
                      <span class="text-caption text-grey">Dokumentasi Arsip</span>
                      <span class="action-link-text text-grey-darken-2">
                        Lihat Rangkuman
                        <v-icon size="14" class="ml-1">mdi-arrow-right</v-icon>
                      </span>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>

            <!-- Loading Skeletons -->
            <v-row v-else-if="loadingPast">
              <v-col v-for="n in 6" :key="n" cols="12" sm="6" md="4">
                <v-skeleton-loader type="image, article" class="rounded-xl border" />
              </v-col>
            </v-row>

            <!-- Empty State for Past -->
            <div v-else class="empty-state-box text-center py-16">
              <v-icon size="56" color="grey-lighten-1" class="mb-3">mdi-calendar-check-outline</v-icon>
              <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">Belum ada rekaman arsip event</h3>
            </div>

            <!-- Pagination -->
            <div v-if="pastPagination.last_page > 1" class="d-flex justify-center mt-10">
              <v-pagination
                v-model="pastPagination.current_page"
                :length="pastPagination.last_page"
                color="#DC2626"
                rounded="circle"
                @update:model-value="(page) => fetchEvents('past', page)"
              />
            </div>
          </div>
        </v-window-item>
      </v-window>

      <!-- Walk Participation Guide (Antislop: Honest & Editorial) -->
      <section class="mt-16">
        <div class="guide-card pa-8 pa-md-12">
          <div class="text-center max-w-xl mx-auto mb-10">
            <div class="d-inline-flex align-center ga-1 text-caption font-weight-bold text-primary-red text-uppercase tracking-wider mb-2">
              <v-icon size="14" color="#DC2626">mdi-walk</v-icon>
              <span>ALUR PARTISIPASI</span>
            </div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-3">
              Cara Ikut Jalan Bareng
            </h2>
            <p class="text-body-1 text-grey-darken-1 mb-0">
              Kegiatan kami terbuka untuk siapa saja tanpa syarat rumit. Ikuti langkah sederhana ini untuk bergabung:
            </p>
          </div>

          <v-row>
            <v-col cols="12" md="4">
              <div class="guide-step-card pa-6 h-100">
                <div class="step-badge mb-4">1</div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">Pilih Jadwal &amp; Rute</h3>
                <p class="text-body-2 text-grey-darken-1 mb-0">
                  Telusuri daftar event di atas dan temukan rute jalan santai yang cocok dengan waktu luangmu.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="guide-step-card pa-6 h-100">
                <div class="step-badge mb-4">2</div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">Daftar Tanpa Biaya</h3>
                <p class="text-body-2 text-grey-darken-1 mb-0">
                  Buka detail event untuk konfirmasi kehadiran melalui tautan formulir pendaftaran terbuka.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="guide-step-card pa-6 h-100">
                <div class="step-badge mb-4">3</div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">Hadir &amp; Nikmati Waktu</h3>
                <p class="text-body-2 text-grey-darken-1 mb-0">
                  Kenakan pakaian santai serta sepatu jalan yang nyaman, dan nikmati interaksi hangat bersama warga pejalan.
                </p>
              </div>
            </v-col>
          </v-row>
        </div>
      </section>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useRoute, useSeoMeta, definePageMeta } from '#imports'

definePageMeta({
  layout: 'default'
})

useSeoMeta({
  title: 'Agenda Event Komunitas - Jalan Bareng',
  ogTitle: 'Agenda Event Komunitas - Jalan Bareng',
  description: 'Jadwal dan agenda jalan kaki santai, rute tematik, dan pertemuan komunitas pejalan kaki.',
  ogDescription: 'Jadwal dan agenda jalan kaki santai, rute tematik, dan pertemuan komunitas pejalan kaki.',
  ogImage: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const authStore = useAuthStore()
const { api } = useApi()
const config = useRuntimeConfig()
const { getImageUrl } = useImageUrl()

const tab = ref('upcoming')
const upcomingEvents = ref<any[]>([])
const pastEvents = ref<any[]>([])
const loadingUpcoming = ref(true)
const loadingPast = ref(false)

const upcomingPagination = ref({ current_page: 1, per_page: 6, total: 0, last_page: 1 })
const pastPagination = ref({ current_page: 1, per_page: 6, total: 0, last_page: 1 })
const selectedActivation = ref<number | null>(null)
const activations = ref<any[]>([])

const fetchEvents = async (status = 'upcoming', page = 1) => {
  const loading = status === 'upcoming' ? loadingUpcoming : loadingPast
  loading.value = true

  try {
    const params: any = { status, page, per_page: 6 }

    if (selectedActivation.value) {
      params.activation_id = selectedActivation.value
    }

    const response = await api.get('/events', { params })
    const { data, ...pagination } = response.data

    if (status === 'upcoming') {
      upcomingEvents.value = data || []
      upcomingPagination.value = pagination
    } else {
      pastEvents.value = data || []
      pastPagination.value = pagination
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
  upcomingPagination.value.current_page = 1
  pastPagination.value.current_page = 1
  fetchEvents('upcoming', 1)
  if (tab.value === 'past') {
    fetchEvents('past', 1)
  }
}

const scrollToEvents = () => {
  const target = document.getElementById('events-content')
  if (target) {
    target.scrollIntoView({ behavior: 'smooth' })
  }
}

watch(tab, (newTab) => {
  if (newTab === 'past' && pastEvents.value.length === 0) {
    fetchEvents('past', 1)
  }
})

onMounted(() => {
  const route = useRoute()
  if (route.query.activation_id) {
    selectedActivation.value = Number.parseInt(route.query.activation_id as string)
  }

  fetchActivations()
  fetchEvents('upcoming', 1)
})

const formatDate = (date: string) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '').trim()
}
</script>

<style scoped>
.events-index-page {
  background-color: #FFFFFF;
  min-height: 100vh;
}

/* Editorial Hero Section */
.events-hero {
  background: #FAFAFA;
  border-bottom: 1px solid #F1F5F9;
  padding-top: 48px;
  padding-bottom: 50px;
}

.hero-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 9999px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  color: #DC2626;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.04em;
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

/* Button styles */
.btn-outline-pill {
  display: inline-flex;
  align-items: center;
  padding: 10px 22px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  color: #374151;
  font-size: 0.92rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-outline-pill:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #111827;
}

/* Filter Bar */
.filter-bar-card {
  background: #FAFAFA;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
}

.tab-switcher-pill {
  display: inline-flex;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 9999px;
  padding: 4px;
  gap: 4px;
}

.tab-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 18px;
  border-radius: 9999px;
  border: none;
  background: transparent;
  color: #4B5563;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-pill-btn.active {
  background: #DC2626;
  color: #FFFFFF;
  box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
}

.counter-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
  background: rgba(255, 255, 255, 0.25);
  color: #FFFFFF;
}

.tab-pill-btn:not(.active) .counter-badge {
  background: #F1F5F9;
  color: #4B5563;
}

/* Event Cards */
.event-card {
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  text-decoration: none;
}

.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
  border-color: #CBD5E1;
}

.past-card {
  opacity: 0.9;
}

.event-image-wrapper {
  position: relative;
  width: 100%;
  height: 210px;
  overflow: hidden;
  background: #F3F4F6;
}

.event-poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.event-card:hover .event-poster-img {
  transform: scale(1.05);
}

.event-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.35) 0%, transparent 60%);
}

.event-tag-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 2;
}

.type-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  backdrop-filter: blur(4px);
}

.type-pill.walking {
  background: rgba(16, 185, 129, 0.9);
  color: #FFFFFF;
}

.type-pill.regular {
  background: rgba(220, 38, 38, 0.9);
  color: #FFFFFF;
}

.type-pill.completed {
  background: rgba(107, 114, 128, 0.85);
  color: #FFFFFF;
}

.date-row {
  font-size: 0.82rem;
  font-weight: 600;
  color: #DC2626;
}

.card-title {
  letter-spacing: -0.015em;
  line-height: 1.3;
}

.card-desc {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
}

.border-top {
  border-top: 1px solid #F1F5F9;
}

.status-indicator {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  color: #059669;
}

.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10B981;
}

.action-link-text {
  display: inline-flex;
  align-items: center;
  color: #DC2626;
  font-weight: 700;
  font-size: 0.85rem;
  transition: transform 0.2s ease;
}

.event-card:hover .action-link-text {
  transform: translateX(3px);
}

/* Empty State */
.empty-state-box {
  background: #FAFAFA;
  border: 1px dashed #CBD5E1;
  border-radius: 20px;
}

/* Guide Section */
.guide-card {
  background: #FAFAFA;
  border: 1px solid #E2E8F0;
  border-radius: 24px;
}

.guide-step-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 18px;
}

.step-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #FEF2F2;
  color: #DC2626;
  font-weight: 800;
  font-size: 1rem;
}

/* Responsive */
@media (max-width: 600px) {
  .events-hero {
    padding-top: 32px;
    padding-bottom: 32px;
  }

  .hero-title {
    font-size: 1.95rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }

  .event-image-wrapper {
    height: 175px;
  }

  .tab-switcher-pill {
    width: 100%;
    justify-content: center;
  }

  .tab-pill-btn {
    flex: 1;
    justify-content: center;
    padding: 7px 10px;
    font-size: 0.78rem;
  }
}
</style>
