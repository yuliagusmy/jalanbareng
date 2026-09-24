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
              <span class="d-block">Langkah Bersama,</span>
              <span class="d-block text-primary-red">Jadwal &amp; Teman Baru</span>
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
            <!-- Events Grid (1 column on mobile 390px, 2 on tablet, 3 on desktop) -->
            <v-row v-if="!loadingUpcoming && upcomingEvents.length > 0" dense>
              <v-col
                v-for="event in upcomingEvents"
                :key="event.id"
                cols="12"
                sm="6"
                md="4"
              >
                <!-- Compact Regis Card Style -->
                <div class="compact-card" @click="openDetail(event)">
                  <!-- Image Header -->
                  <div class="compact-card-img">
                    <img
                      v-if="event.poster"
                      :src="getImageUrl(event.poster)"
                      :alt="event.name"
                      class="compact-card-poster"
                    />
                    <div v-else class="compact-card-img-placeholder"></div>

                    <!-- Status Dot -->
                    <span class="compact-status-dot"></span>
                  </div>

                  <!-- Card Body -->
                  <div class="compact-card-body">
                    <!-- Category Tag -->
                    <span
                      class="compact-cat-tag"
                      :style="{ backgroundColor: getEventCategory(event).bg, color: getEventCategory(event).color }"
                    >
                      <v-icon size="11">{{ getEventCategory(event).icon }}</v-icon>
                      {{ getEventCategory(event).label }}
                    </span>

                    <!-- Title -->
                    <h3 class="compact-title">{{ event.name }}</h3>

                    <!-- Schedule & Location -->
                    <div class="compact-meta">
                      <div class="compact-meta-row">
                        <v-icon size="13" color="#DC2626">mdi-calendar-clock</v-icon>
                        <span>{{ formatScheduleShort(event.date) || formatDate(event.date) }}</span>
                      </div>
                      <div class="compact-meta-row">
                        <v-icon size="13" color="#0284C7">mdi-map-marker-radius</v-icon>
                        <span>{{ getEventLocation(event) }}</span>
                      </div>
                    </div>

                    <!-- Action Buttons: Regis Kegiatan & Detail -->
                    <div class="compact-card-actions d-flex align-center ga-2 mt-auto pt-2 border-top" @click.stop>
                      <!-- Primary Red Regis Button -->
                      <v-btn
                        color="primary"
                        rounded="pill"
                        size="small"
                        class="compact-reg-btn font-weight-bold elevation-1 flex-grow-1"
                        @click.stop="handleDaftarClick(event)"
                      >
                        <v-icon start size="14">mdi-clipboard-edit-outline</v-icon>
                        <span class="reg-btn-label">Daftar</span>
                      </v-btn>

                      <!-- Secondary Subtle Detail Button -->
                      <v-btn
                        variant="outlined"
                        color="grey-darken-2"
                        rounded="pill"
                        size="small"
                        class="compact-detail-btn font-weight-bold"
                        @click.stop="openDetail(event)"
                      >
                        <span>Detail</span>
                        <v-icon end size="14">mdi-arrow-right</v-icon>
                      </v-btn>
                    </div>
                  </div>
                </div>
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
            <!-- Events Grid (1 column on mobile 390px, 2 on tablet, 3 on desktop) -->
            <v-row v-if="!loadingPast && pastEvents.length > 0" dense>
              <v-col
                v-for="event in pastEvents"
                :key="event.id"
                cols="12"
                sm="6"
                md="4"
              >
                <!-- Compact Regis Card Style (Past Event) -->
                <div class="compact-card past-compact-card" @click="openDetail(event)">
                  <!-- Image Header -->
                  <div class="compact-card-img">
                    <img
                      v-if="event.poster"
                      :src="getImageUrl(event.poster)"
                      :alt="event.name"
                      class="compact-card-poster"
                    />
                    <div v-else class="compact-card-img-placeholder"></div>
                  </div>

                  <!-- Card Body -->
                  <div class="compact-card-body">
                    <!-- Status / Category Tag -->
                    <span class="compact-cat-tag past-cat-tag">
                      <v-icon size="11">mdi-check-circle-outline</v-icon>
                      Selesai
                    </span>

                    <!-- Title -->
                    <h3 class="compact-title">{{ event.name }}</h3>

                    <!-- Schedule & Location -->
                    <div class="compact-meta">
                      <div class="compact-meta-row">
                        <v-icon size="13" color="#64748B">mdi-calendar-check-outline</v-icon>
                        <span>{{ formatDate(event.date) }}</span>
                      </div>
                      <div class="compact-meta-row">
                        <v-icon size="13" color="#64748B">mdi-map-marker-radius</v-icon>
                        <span>{{ getEventLocation(event) }}</span>
                      </div>
                    </div>

                    <!-- Action Button: Detail / Rangkuman -->
                    <div class="compact-card-actions d-flex align-center ga-2 mt-auto pt-2 border-top" @click.stop>
                      <v-btn
                        variant="outlined"
                        color="grey-darken-2"
                        rounded="pill"
                        size="small"
                        class="compact-detail-btn font-weight-bold flex-grow-1"
                        @click.stop="openDetail(event)"
                      >
                        <span>Lihat Rangkuman</span>
                        <v-icon end size="14">mdi-arrow-right</v-icon>
                      </v-btn>
                    </div>
                  </div>
                </div>
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

      <!-- ===== Detail Bottom Sheet (Identik dengan Beranda) ===== -->
      <v-bottom-sheet v-model="showDetail" max-width="600">
        <v-card v-if="selected" rounded="t-xl" class="detail-sheet">
          <!-- Image Header -->
          <div v-if="selected.poster" class="detail-img-wrapper">
            <img :src="getImageUrl(selected.poster)" :alt="selected.name" class="detail-img" />
            <div class="detail-img-overlay"></div>

            <!-- Close Button (X) at Top Right -->
            <v-btn
              icon
              size="small"
              class="detail-close-btn"
              @click="showDetail = false"
              aria-label="Tutup detail kegiatan"
            >
              <v-icon size="18" color="grey-darken-3">mdi-close</v-icon>
            </v-btn>

            <!-- Badges over image -->
            <div class="detail-badges">
              <span
                class="activation-type-tag"
                :style="{ backgroundColor: getEventCategory(selected).bg, color: getEventCategory(selected).color }"
              >
                <v-icon size="13" class="mr-1">{{ getEventCategory(selected).icon }}</v-icon>
                {{ getEventCategory(selected).label }}
              </span>
              <span v-if="tab === 'upcoming'" class="open-status-badge">
                <span class="status-indicator-dot"></span>
                Pendaftaran Terbuka
              </span>
              <span v-else class="open-status-badge past-badge">
                Selesai
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="detail-content">
            <!-- Header: Title + Host -->
            <div class="detail-header mb-2">
              <h2 class="detail-title">{{ selected.name }}</h2>
              <div v-if="selected.user?.name" class="detail-collaborator d-flex align-center flex-wrap ga-1">
                <v-icon size="14" color="#D97706">mdi-account-circle-outline</v-icon>
                <span>Diselenggarakan oleh <strong>{{ selected.user.name }}</strong></span>
              </div>
            </div>

            <!-- Description -->
            <p v-if="selected.description" class="detail-desc mb-3">
              {{ stripHtml(selected.description) }}
            </p>

            <!-- Unified Detail Specs Card -->
            <div class="detail-specs-card mb-3">
              <div class="spec-item">
                <div class="spec-icon-box icon-box-schedule">
                  <v-icon size="16" color="#DC2626">mdi-calendar-clock</v-icon>
                </div>
                <div class="spec-text">
                  <span class="spec-label">Waktu</span>
                  <span class="spec-val">{{ formatDate(selected.date) }}</span>
                </div>
              </div>

              <div class="spec-item" v-if="getEventLocation(selected)">
                <div class="spec-icon-box icon-box-location">
                  <v-icon size="16" color="#0284C7">mdi-map-marker-radius</v-icon>
                </div>
                <div class="spec-text">
                  <span class="spec-label">Titik Kumpul</span>
                  <span class="spec-val">{{ getEventLocation(selected) }}</span>
                </div>
              </div>

              <div class="spec-item" v-if="selected.distance">
                <div class="spec-icon-box icon-box-ticket">
                  <v-icon size="16" color="#16A34A">mdi-map-marker-distance</v-icon>
                </div>
                <div class="spec-text">
                  <span class="spec-label">Jarak &amp; Estimasi</span>
                  <span class="spec-val">{{ selected.distance }} km • {{ selected.estimated_duration || 90 }} Menit</span>
                </div>
              </div>

              <div class="spec-item spec-item-deadline">
                <div class="spec-icon-box icon-box-deadline">
                  <v-icon size="16" color="#DC2626">mdi-ticket-outline</v-icon>
                </div>
                <div class="spec-text">
                  <span class="spec-label deadline-label">Biaya / HTM</span>
                  <span class="spec-val deadline-val">Gratis • Terbuka untuk umum</span>
                </div>
              </div>
            </div>

            <!-- CTA Button -->
            <v-btn
              v-if="tab === 'upcoming'"
              color="primary"
              size="large"
              block
              rounded="pill"
              class="gform-btn elevation-2 mb-2 font-weight-bold"
              @click="handleDaftarClick(selected)"
            >
              <v-icon start size="18">mdi-clipboard-edit-outline</v-icon>
              <span>Daftar Kegiatan</span>
              <v-icon end size="16">mdi-arrow-right</v-icon>
            </v-btn>

            <!-- Detail Page Link -->
            <div class="text-center mt-2">
              <NuxtLink
                :to="`/events/${selected.id}`"
                class="detail-link"
                @click="showDetail = false"
              >
                Lihat halaman lengkap agenda event
                <v-icon size="13">mdi-chevron-right</v-icon>
              </NuxtLink>
            </div>
          </div>
        </v-card>
      </v-bottom-sheet>

      <!-- Walk Participation Guide (Antislop: Honest & Editorial) -->
      <section class="mt-10 mt-md-16 mb-8">
        <div class="guide-card">
          <div class="text-center max-w-xl mx-auto mb-6 mb-md-8">
            <div class="d-inline-flex align-center ga-1 text-caption font-weight-bold text-primary-red text-uppercase tracking-wider mb-2 mb-md-2.5">
              <v-icon size="14" color="#DC2626">mdi-walk</v-icon>
              <span>ALUR PARTISIPASI</span>
            </div>
            <h2 class="guide-title font-weight-black text-grey-darken-4 mb-2 mb-md-3">
              Cara Ikut Jalan Bareng
            </h2>
            <p class="guide-subtitle text-body-2 text-md-body-1 text-grey-darken-1 mb-0" style="line-height: 1.6;">
              Agenda komunitas kami terbuka dan ramah untuk siapa saja. Ikuti langkah sederhana ini untuk bergabung:
            </p>
          </div>

          <v-row class="guide-steps-row ga-y-3 ga-y-md-0">
            <v-col cols="12" md="4">
              <div class="guide-step-card h-100">
                <div class="d-flex d-md-block align-center ga-3 mb-2 mb-md-3">
                  <div class="step-badge">1</div>
                  <h3 class="step-title font-weight-bold text-grey-darken-4 mb-0 mb-md-2">Pilih Agenda Komunitas</h3>
                </div>
                <p class="step-desc text-grey-darken-1 mb-0">
                  Telusuri daftar jalan santai rutin atau agenda aktivasi komunitas (diskusi buku, workshop kreatif, tur rute tematik) yang cocok dengan waktumu.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="guide-step-card h-100">
                <div class="d-flex d-md-block align-center ga-3 mb-2 mb-md-3">
                  <div class="step-badge">2</div>
                  <h3 class="step-title font-weight-bold text-grey-darken-4 mb-0 mb-md-2">Cek Detail &amp; Registrasi</h3>
                </div>
                <p class="step-desc text-grey-darken-1 mb-0">
                  Buka detail event untuk konfirmasi kehadiran. Sebagian besar jalan santai 100% gratis, sementara untuk workshop atau edisi khusus berbayar, informasi HTM dan kuota tertera transparan.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="guide-step-card h-100">
                <div class="d-flex d-md-block align-center ga-3 mb-2 mb-md-3">
                  <div class="step-badge">3</div>
                  <h3 class="step-title font-weight-bold text-grey-darken-4 mb-0 mb-md-2">Hadir &amp; Nikmati Waktu</h3>
                </div>
                <p class="step-desc text-grey-darken-1 mb-0">
                  Kenakan pakaian santai serta sepatu jalan yang nyaman, datang tepat waktu di titik kumpul, dan nikmati interaksi hangat bersama teman baru.
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

// Detail Modal & Action Logic (Identik dengan Beranda)
const showDetail = ref(false)
const selected = ref<any>(null)

const openDetail = (item: any) => {
  selected.value = item
  showDetail.value = true
}

const handleDaftarClick = (event: any) => {
  if (event.registration_link) {
    window.open(event.registration_link, '_blank', 'noopener,noreferrer')
  } else {
    openDetail(event)
  }
}

const getEventLocation = (event: any) => {
  if (!event) return 'Makassar & Sekitarnya'
  if (event.meeting_point) return event.meeting_point
  if (event.description) {
    const match = event.description.match(/Titik Kumpul:?\s*<\/strong>\s*([^<]+)/i) ||
                  event.description.match(/Titik Kumpul:?\s*([^<\n]+)/i)
    if (match && match[1]) {
      return match[1].trim()
    }
    const ruteMatch = event.description.match(/Rute:?\s*<\/strong>\s*([^<]+)/i) ||
                      event.description.match(/Rute:?\s*([^<\n]+)/i)
    if (ruteMatch && ruteMatch[1]) {
      return ruteMatch[1].trim().split('-')[0].trim()
    }
  }
  if (event.activation?.city) {
    return event.activation.city
  }
  return 'Lihat titik di detail'
}

const formatScheduleShort = (dateStr: string) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  }) + ' WITA'
}

const getEventCategory = (event: any) => {
  if (event.activation?.title) {
    return {
      label: event.activation.title,
      icon: 'mdi-walk',
      bg: '#FEE2E2',
      color: '#DC2626'
    }
  }
  if (event.type === 'walking') {
    return {
      label: 'Jalan Santai',
      icon: 'mdi-walk',
      bg: '#FEE2E2',
      color: '#DC2626'
    }
  }
  return {
    label: 'Tematik',
    icon: 'mdi-calendar-text-outline',
    bg: '#E0F2FE',
    color: '#0284C7'
  }
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

/* ===== COMPACT CARD (Identik dengan Beranda) ===== */
.compact-card {
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  cursor: pointer;
  transition: transform 0.22s ease, box-shadow 0.22s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.compact-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px -4px rgba(0, 0, 0, 0.1);
}

.past-compact-card {
  opacity: 0.92;
}

.compact-card-img {
  position: relative;
  height: 175px;
  overflow: hidden;
  background: #F3F4F6;
}

.compact-card-poster {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.35s ease;
}

.compact-card:hover .compact-card-poster {
  transform: scale(1.05);
}

.compact-card-img-placeholder {
  width: 100%;
  height: 100%;
  background: #FEE2E2;
}

.compact-status-dot {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #16A34A;
  border: 2px solid white;
  box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.4);
  animation: pulse-ring 1.8s infinite;
}

@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7); }
  70%  { box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}

.compact-card-body {
  padding: 14px 16px 16px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.compact-cat-tag {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  align-self: flex-start;
  gap: 4px;
  margin-bottom: 8px;
}

.past-cat-tag {
  background: #F1F5F9 !important;
  color: #64748B !important;
}

.compact-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.35;
  margin: 0 0 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  letter-spacing: -0.01em;
}

.compact-meta {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin-bottom: auto;
  padding-bottom: 12px;
}

.compact-meta-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  color: #6B7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.compact-card-actions {
  border-top-color: #F3F4F6 !important;
}

.compact-reg-btn {
  text-transform: none;
  letter-spacing: 0.01em;
  font-size: 0.8rem !important;
  height: 34px !important;
}

.compact-detail-btn {
  text-transform: none;
  letter-spacing: 0.01em;
  font-size: 0.78rem !important;
  height: 34px !important;
}

/* ===== DETAIL BOTTOM SHEET ===== */
.detail-sheet {
  max-height: 90vh;
  overflow-y: auto;
}

.detail-img-wrapper {
  position: relative;
  height: 270px;
  overflow: hidden;
  background: #111827;
}

.detail-close-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 4;
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(4px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
  width: 32px !important;
  height: 32px !important;
  border-radius: 50% !important;
  transition: all 0.2s ease;
}

.detail-close-btn:hover {
  background: #FFFFFF !important;
  transform: scale(1.08);
}

.detail-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.detail-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.45) 100%);
}

.detail-badges {
  position: absolute;
  bottom: 12px;
  left: 14px;
  right: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.detail-content {
  padding: 18px 22px 24px;
}

.activation-type-tag {
  font-size: 0.73rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
}

.open-status-badge {
  font-size: 0.73rem;
  font-weight: 700;
  color: #166534;
  background: #F0FDF4;
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  border: 1px solid #DCFCE7;
}

.past-badge {
  color: #475569 !important;
  background: #F1F5F9 !important;
  border-color: #E2E8F0 !important;
}

.status-indicator-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #16A34A;
}

.detail-header {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.detail-title {
  font-size: 1.25rem;
  font-weight: 800;
  line-height: 1.25;
  color: #111827;
  letter-spacing: -0.02em;
}

.detail-collaborator {
  font-size: 0.8rem;
  color: #6B7280;
  line-height: 1.3;
}

.detail-collaborator strong {
  color: #1F2937;
  font-weight: 600;
}

.detail-desc {
  font-size: 0.84rem;
  line-height: 1.5;
  color: #4B5563;
}

.detail-specs-card {
  background: #F9FAFB;
  border: 1px solid #E5E7EB;
  border-radius: 14px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.spec-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-bottom: 1px solid #F3F4F6;
}

.spec-item:last-child {
  border-bottom: none;
}

.spec-icon-box {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-box-schedule {
  background: #FEF2F2 !important;
  border-color: #FECACA !important;
}

.icon-box-location {
  background: #F0F9FF !important;
  border-color: #BAE6FD !important;
}

.icon-box-ticket {
  background: #F0FDF4 !important;
  border-color: #BBF7D0 !important;
}

.icon-box-deadline {
  background: #FEF2F2 !important;
  border-color: #FECACA !important;
}

.spec-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
  min-width: 0;
}

.spec-label {
  font-size: 0.72rem;
  color: #6B7280;
  font-weight: 500;
  line-height: 1.2;
}

.spec-val {
  font-size: 0.84rem;
  color: #111827;
  font-weight: 600;
  line-height: 1.3;
}

.spec-item-deadline {
  background: #FFF8F8;
}

.deadline-label {
  color: #DC2626 !important;
  font-weight: 600 !important;
}

.deadline-val {
  color: #991B1B !important;
  font-weight: 700 !important;
}

.gform-btn {
  height: 46px !important;
  font-size: 0.9rem;
  font-weight: 700;
  letter-spacing: 0.01em;
  text-transform: none;
}

.detail-link {
  font-size: 0.78rem;
  font-weight: 600;
  color: #DC2626;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  transition: opacity 0.2s;
}

.detail-link:hover {
  opacity: 0.8;
  text-decoration: underline;
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
  border-radius: 28px;
  padding: 44px 36px 40px;
}

.guide-title {
  font-size: clamp(1.4rem, 2.5vw, 2rem);
  letter-spacing: -0.025em;
  line-height: 1.2;
}

.guide-subtitle {
  font-size: 0.95rem;
  line-height: 1.55;
}

.guide-step-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  padding: 24px 22px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.guide-step-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.06);
}

.step-title {
  font-size: 1.05rem;
  letter-spacing: -0.015em;
  line-height: 1.3;
}

.step-desc {
  font-size: 0.875rem;
  line-height: 1.55;
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

/* Responsive (Mobile 390px Compact Standards) */
@media (max-width: 600px) {
  .events-hero {
    padding-top: 24px;
    padding-bottom: 24px;
  }

  .hero-title {
    font-size: clamp(1.45rem, 6.2vw, 2.05rem);
    line-height: 1.15;
  }

  .hero-subtitle {
    font-size: 0.85rem;
    line-height: 1.5;
    margin-bottom: 20px !important;
  }

  .filter-bar-card {
    padding: 10px 12px !important;
    border-radius: 16px;
    margin-bottom: 20px !important;
  }

  .compact-card {
    border-radius: 16px;
    margin-bottom: 8px;
  }

  .compact-card-img {
    height: 170px;
  }

  .compact-card-body {
    padding: 12px 14px 14px;
  }

  .compact-title {
    font-size: 1.05rem;
    line-height: 1.3;
    margin-bottom: 6px;
  }

  .compact-meta {
    padding-bottom: 10px;
  }

  .compact-reg-btn {
    height: 36px !important;
    font-size: 0.82rem !important;
  }

  .compact-detail-btn {
    height: 36px !important;
    font-size: 0.8rem !important;
  }

  .detail-img-wrapper {
    height: 220px;
  }

  .detail-content {
    padding: 16px 16px 20px;
  }

  .detail-title {
    font-size: 1.15rem;
  }

  .tab-switcher-pill {
    width: 100%;
    justify-content: center;
    padding: 3px;
  }

  .tab-pill-btn {
    flex: 1;
    justify-content: center;
    padding: 6px 8px;
    font-size: 0.74rem;
  }

  .counter-badge {
    font-size: 0.65rem;
    padding: 1px 5px;
  }

  .guide-card {
    border-radius: 20px;
    padding: 32px 18px 26px !important;
  }

  .guide-title {
    font-size: clamp(1.3rem, 5vw, 1.55rem);
    line-height: 1.25;
    margin-bottom: 8px !important;
  }

  .guide-subtitle {
    font-size: 0.85rem;
    line-height: 1.55;
    margin-bottom: 22px !important;
  }

  .guide-step-card {
    border-radius: 16px;
    padding: 18px 16px !important;
    margin-bottom: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  }

  .step-badge {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
    flex-shrink: 0;
  }

  .step-title {
    font-size: 0.95rem;
  }

  .step-desc {
    font-size: 0.825rem;
    line-height: 1.55;
    margin-top: 6px;
  }
}
</style>
