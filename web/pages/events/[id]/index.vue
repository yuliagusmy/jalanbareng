<template>
  <div class="event-detail">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
    </div>

    <!-- Error State -->
    <v-container v-else-if="!event" class="text-center py-16">
      <v-icon size="64" color="grey">mdi-alert-circle-outline</v-icon>
      <h2 class="text-h5 mt-4 mb-2">Event tidak ditemukan</h2>
      <p class="text-body-2 text-grey mb-6">Event yang Anda cari tidak tersedia.</p>
      <v-btn color="primary" to="/events" variant="flat">
        Kembali ke Event
      </v-btn>
    </v-container>

    <!-- Event Detail Content -->
    <div v-else>
      <!-- Hero Section with Background Image -->
      <div class="hero-section" :style="{ backgroundImage: `url(${getImageUrl(event.poster)})` }">
        <div class="hero-overlay"></div>
        <v-container class="hero-container">
          <div class="hero-content">
            <!-- Breadcrumbs -->
            <div class="breadcrumbs mb-6">
              <nuxt-link to="/" class="breadcrumb-link">Beranda</nuxt-link>
              <span class="breadcrumb-separator">/</span>
              <nuxt-link to="/events" class="breadcrumb-link">Event</nuxt-link>
              <span class="breadcrumb-separator">/</span>
              <span class="breadcrumb-current">{{ event.name }}</span>
            </div>

            <!-- Title & Badge -->
            <h1 class="hero-title">{{ event.name }}</h1>

            <div class="hero-badges mb-4">
              <v-chip :color="event.type === 'walking' ? 'success' : 'primary'" size="large" label
                class="event-type-chip">
                <v-icon start>{{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar-star' }}</v-icon>
                {{ event.type === 'walking' ? 'Jalan Kaki' : 'Event Regular' }}
              </v-chip>
            </div>

            <p class="hero-subtitle">
              Diselenggarakan oleh Komunitas Jalan Bareng
            </p>
          </div>
        </v-container>
      </div>

      <!-- Content Section -->
      <v-container class="content-section">
        <v-row>
          <!-- Main Content -->
          <v-col cols="12" md="8">
            <!-- Quick Info Stats -->
            <div class="quick-stats">
              <div class="stat-item">
                <v-icon color="primary" size="24" class="stat-icon">mdi-calendar</v-icon>
                <div class="stat-content">
                  <div class="stat-label">Tanggal</div>
                  <div class="stat-value">{{ formatDate(event.date) }}</div>
                </div>
              </div>
              <div v-if="event.time" class="stat-divider"></div>
              <div v-if="event.time" class="stat-item">
                <v-icon color="primary" size="24" class="stat-icon">mdi-clock-outline</v-icon>
                <div class="stat-content">
                  <div class="stat-label">Waktu</div>
                  <div class="stat-value">{{ event.time }}</div>
                </div>
              </div>
              <div v-if="event.type === 'walking' && event.distance" class="stat-divider"></div>
              <div v-if="event.type === 'walking' && event.distance" class="stat-item">
                <v-icon color="success" size="24" class="stat-icon">mdi-map-marker-distance</v-icon>
                <div class="stat-content">
                  <div class="stat-label">Jarak</div>
                  <div class="stat-value">{{ event.distance }} km</div>
                </div>
              </div>
              <div v-if="event.type === 'walking' && event.estimated_duration" class="stat-divider"></div>
              <div v-if="event.type === 'walking' && event.estimated_duration" class="stat-item">
                <v-icon color="info" size="24" class="stat-icon">mdi-timer-outline</v-icon>
                <div class="stat-content">
                  <div class="stat-label">Durasi</div>
                  <div class="stat-value">{{ event.estimated_duration }} menit</div>
                </div>
              </div>
            </div>

            <!-- About Event -->
            <section class="content-block">
              <h2 class="section-title">Tentang Event</h2>
              <div class="description-text" v-html="event.description"></div>
            </section>

            <!-- YouTube Video -->
            <section v-if="event.youtube_link" class="content-block">
              <h2 class="section-title">Video Event</h2>
              <div class="video-card">
                <div class="video-container">
                  <iframe :src="getYouTubeEmbedUrl(event.youtube_link)" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
                </div>
              </div>
            </section>

            <!-- Route Map (Walking Event) -->
            <section v-if="event.type === 'walking' && event.route && event.route.length > 0" class="content-block">
              <h2 class="section-title">Rute Jalan Kaki</h2>
              <div class="map-card">
                <div class="route-points">
                  <div class="route-point">
                    <v-icon color="success" size="20">mdi-flag-variant</v-icon>
                    <div class="route-point-info">
                      <div class="route-point-label">Titik Start</div>
                      <div v-if="event.start_point" class="route-point-value">
                        {{ event.start_point.lat.toFixed(6) }}, {{ event.start_point.lng.toFixed(6) }}
                      </div>
                    </div>
                  </div>
                  <div class="route-point">
                    <v-icon color="error" size="20">mdi-flag-checkered</v-icon>
                    <div class="route-point-info">
                      <div class="route-point-label">Titik Finish</div>
                      <div v-if="event.finish_point" class="route-point-value">
                        {{ event.finish_point.lat.toFixed(6) }}, {{ event.finish_point.lng.toFixed(6) }}
                      </div>
                    </div>
                  </div>
                </div>
                <ClientOnly>
                  <!-- <RouteMapViewer v-if="event && event.route" :route="event.route" :show-user-location="true" /> -->
                  <GoogleMapRouteViewer :route="event.route" />
                </ClientOnly>
              </div>
            </section>

            <!-- What to Expect -->
            <section class="content-block">
              <h2 class="section-title">Yang Akan Kamu Dapatkan</h2>
              <div class="benefits-grid">
                <div v-for="item in whatToExpect" :key="item.title" class="benefit-item">
                  <v-icon color="primary" size="32" class="benefit-icon">{{ item.icon }}</v-icon>
                  <div class="benefit-content">
                    <div class="benefit-title">{{ item.title }}</div>
                    <div class="benefit-subtitle">{{ item.subtitle }}</div>
                  </div>
                </div>
              </div>
            </section>

            <!-- Tips Section -->
            <section class="content-block">
              <h2 class="section-title">Tips Mengikuti Event</h2>
              <div class="tips-list">
                <div v-for="(tip, index) in eventTips" :key="index" class="tip-item">
                  <div class="tip-number">{{ index + 1 }}</div>
                  <div class="tip-text">{{ tip.text }}</div>
                </div>
              </div>
            </section>
          </v-col>

          <!-- Sidebar -->
          <v-col cols="12" md="4">
            <div class="sidebar">
              <!-- Registration Card -->
              <div class="sidebar-card registration-card">
                <h3 class="registration-title">Siap Bergabung?</h3>
                <p class="registration-text">Daftarkan dirimu sekarang dan jangan sampai ketinggalan!</p>
                <v-btn :href="event.registration_link" target="_blank" color="primary" size="large" block variant="flat"
                  class="registration-btn">
                  <v-icon start>mdi-form-select</v-icon>
                  Daftar Sekarang
                </v-btn>
              </div>

              <!-- Share Card -->
              <div class="sidebar-card">
                <h3 class="sidebar-title">Bagikan Event</h3>
                <div class="share-buttons">
                  <button class="share-btn whatsapp" @click="shareEvent('whatsapp')">
                    <v-icon>mdi-whatsapp</v-icon>
                  </button>
                  <button class="share-btn facebook" @click="shareEvent('facebook')">
                    <v-icon>mdi-facebook</v-icon>
                  </button>
                  <button class="share-btn twitter" @click="shareEvent('twitter')">
                    <v-icon>mdi-twitter</v-icon>
                  </button>
                  <button class="share-btn link" @click="copyLink">
                    <v-icon>mdi-link-variant</v-icon>
                  </button>
                </div>
              </div>

              <!-- Info Card -->
              <div class="sidebar-card">
                <h3 class="sidebar-title">Detail Event</h3>
                <div class="info-list">
                  <div class="info-item">
                    <span class="info-label">Tipe Event</span>
                    <span class="info-value">{{ event.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">{{ formatDate(event.date) }}</span>
                  </div>
                  <div v-if="event.time" class="info-item">
                    <span class="info-label">Waktu</span>
                    <span class="info-value">{{ event.time }}</span>
                  </div>
                  <div v-if="event.type === 'walking' && event.distance" class="info-item">
                    <span class="info-label">Jarak</span>
                    <span class="info-value">{{ event.distance }} km</span>
                  </div>
                </div>
              </div>

              <!-- Admin Actions -->
              <div v-if="canEdit" class="sidebar-card">
                <h3 class="sidebar-title">Aksi Admin</h3>
                <v-btn :to="`/events/${event.id}/edit`" color="primary" variant="outlined" block class="mb-2">
                  <v-icon start size="20">mdi-pencil</v-icon>
                  Edit Event
                </v-btn>
                <v-btn color="error" variant="outlined" block @click="confirmDelete">
                  <v-icon start size="20">mdi-delete</v-icon>
                  Hapus Event
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Hapus Event</v-card-title>
        <v-card-text>
          Apakah Anda yakin ingin menghapus event <strong>{{ event?.name }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" @click="deleteEvent" :loading="deleting">Hapus</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000" location="top">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { defineAsyncComponent } from 'vue'
import { useHead } from '#app'

const RouteMapViewer = defineAsyncComponent(() => import('~/components/events/RouteMapViewer.vue'))
const GoogleMapRouteViewer = defineAsyncComponent(() => import('~/components/events/GoogleMapRouteViewer.vue'))

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const router = useRouter()
const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const event = ref<any>(null)
const loading = ref(true)
const deleteDialog = ref(false)
const deleting = ref(false)

useSeoMeta({
  title: () => event.value ? `${event.value.name} - Jalan Bareng` : 'Detail Event - Jalan Bareng',
  ogTitle: () => event.value ? `${event.value.name} - Jalan Bareng` : 'Detail Event - Jalan Bareng',
  description: () => event.value ? `Ikuti event ${event.value.name}. ${event.value.description ? stripHtml(event.value.description).substring(0, 150) : ''}...` : 'Detail event komunitas di Jalan Bareng.',
  ogDescription: () => event.value ? `Ikuti event ${event.value.name}. ${event.value.description ? stripHtml(event.value.description).substring(0, 150) : ''}...` : 'Detail event komunitas di Jalan Bareng.',
  ogImage: () => event.value?.poster ? getImageUrl(event.value.poster) : 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const canEdit = computed(() => {
  return authStore.isAdmin || authStore.isCommunityAdmin
})

const whatToExpect = ref([
  { icon: 'mdi-account-group', title: 'Komunitas Positif', subtitle: 'Bertemu teman baru dan perluas jaringan' },
  { icon: 'mdi-run', title: 'Aktivitas Menyehatkan', subtitle: 'Jaga kebugaran tubuh sambil bersenang-senang' },
  { icon: 'mdi-lightbulb-on-outline', title: 'Wawasan Baru', subtitle: 'Dapatkan pengalaman dan pengetahuan baru' },
  { icon: 'mdi-camera', title: 'Momen Tak Terlupakan', subtitle: 'Ciptakan kenangan indah bersama' }
])

const eventTips = ref([
  { text: 'Datang 15 menit lebih awal untuk registrasi dan persiapan' },
  { text: 'Kenakan pakaian dan sepatu yang nyaman untuk bergerak' },
  { text: 'Bawa botol minum dan gunakan sunscreen jika event di luar ruangan' },
  { text: 'Ikuti instruksi dari panitia untuk keamanan dan kenyamanan bersama' }
])

const { getImageUrl } = useImageUrl()

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getYouTubeEmbedUrl = (url: string) => {
  if (!url) return ''
  const videoId = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/)?.[1]
  return videoId ? `https://www.youtube.com/embed/${videoId}` : ''
}

const shareEvent = (platform: string) => {
  const url = window.location.href
  const text = `Yuk ikut event menarik: ${event.value.name}`

  let shareUrl = ''
  switch (platform) {
    case 'whatsapp':
      shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
      break
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
      break
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`
      break
  }

  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    showSnackbar('Link berhasil disalin!', 'success')
  } catch (error) {
    showSnackbar('Gagal menyalin link', 'error')
  }
}

const showSnackbar = (text: string, color: string = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

const fetchEvent = async () => {
  loading.value = true
  try {
    const response = await api.get(`/events/${route.params.id}`)
    event.value = response.data.event
  } catch (error) {
    console.error('Error fetching event:', error)
    event.value = null
  } finally {
    loading.value = false
  }
}

const confirmDelete = () => {
  deleteDialog.value = true
}

const deleteEvent = async () => {
  deleting.value = true
  try {
    await api.delete(`/events/${route.params.id}`)
    showSnackbar('Event berhasil dihapus', 'success')
    setTimeout(() => {
      router.push('/events')
    }, 1500)
  } catch (error) {
    console.error('Error deleting event:', error)
    showSnackbar('Gagal menghapus event', 'error')
  } finally {
    deleting.value = false
    deleteDialog.value = false
  }
}

onMounted(() => {
  fetchEvent()
})
</script>

<style scoped>
/* ============================================
   CLEAN DESIGN SYSTEM - Event Detail
   ============================================ */

/* Variables */
:root {
  --spacing-unit: 8px;
  --border-radius: 12px;
  --transition: all 0.2s ease;
}

/* Loading */
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}

/* Hero Section with Background Image */
.hero-section {
  position: relative;
  min-height: 480px;
  padding-top: 36px;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  align-items: flex-end;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0.2) 100%);
}

.hero-container {
  position: relative;
  z-index: 2;
  padding-bottom: 48px;
}

.hero-content {
  max-width: 800px;
}

.breadcrumbs {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.breadcrumb-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: var(--transition);
}

.breadcrumb-link:hover {
  color: white;
}

.breadcrumb-separator {
  color: rgba(255, 255, 255, 0.5);
}

.breadcrumb-current {
  color: white;
}

.hero-title {
  font-size: 48px;
  font-weight: 700;
  color: white;
  margin: 0 0 16px;
  line-height: 1.2;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.hero-badges {
  display: flex;
  gap: 12px;
}

.event-type-chip {
  font-weight: 600;
}

.hero-subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 16px;
  margin: 0;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

/* Content Section */
.content-section {
  padding: 48px 16px 80px;
}

/* Quick Stats */
.quick-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  padding: 24px;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  margin-bottom: 48px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 200px;
}

.stat-icon {
  flex-shrink: 0;
}

.stat-content {
  flex: 1;
}

.stat-label {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 16px;
  font-weight: 600;
  color: #1A1A1A;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #E0E0E0;
}

/* Content Blocks */
.content-block {
  margin-bottom: 56px;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: #1A1A1A;
  margin: 0 0 24px;
}

.description-text {
  font-size: 16px;
  line-height: 1.8;
  color: #444;
}

.description-text :deep(p) {
  margin-bottom: 16px;
}

.description-text :deep(p:last-child) {
  margin-bottom: 0;
}

/* Video Card */
.video-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.video-container {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Map Card */
.map-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.route-points {
  display: flex;
  gap: 24px;
  padding: 24px;
  background: #F8F9FA;
  flex-wrap: wrap;
}

.route-point {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 200px;
}

.route-point-info {
  flex: 1;
}

.route-point-label {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.route-point-value {
  font-size: 13px;
  font-family: monospace;
  color: #333;
}

/* Benefits Grid */
.benefits-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
}

.benefit-item {
  display: flex;
  gap: 16px;
  padding: 20px;
  background: #F8F9FA;
  border-radius: var(--border-radius);
}

.benefit-icon {
  flex-shrink: 0;
}

.benefit-content {
  flex: 1;
}

.benefit-title {
  font-size: 16px;
  font-weight: 700;
  color: #1A1A1A;
  margin-bottom: 4px;
}

.benefit-subtitle {
  font-size: 14px;
  color: #666;
  line-height: 1.5;
}

/* Tips List */
.tips-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.tip-item {
  display: flex;
  gap: 16px;
  padding: 20px;
  background: #F8F9FA;
  border-radius: var(--border-radius);
}

.tip-number {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1976D2;
  color: white;
  border-radius: 50%;
  font-weight: 700;
  font-size: 14px;
}

.tip-text {
  flex: 1;
  font-size: 15px;
  line-height: 1.6;
  color: #444;
}

/* Sidebar */
.sidebar {
  position: sticky;
  top: 90px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sidebar-card {
  padding: 24px;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.sidebar-title {
  font-size: 16px;
  font-weight: 700;
  color: #1A1A1A;
  margin: 0 0 16px;
}

/* Registration Card */
.registration-card {
  background: linear-gradient(135deg, #1976D2 0%, #1565C0 100%);
  color: white;
  text-align: center;
}

.registration-title {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 8px;
  color: white;
}

.registration-text {
  font-size: 14px;
  margin: 0 0 20px;
  color: rgba(255, 255, 255, 0.9);
}

.registration-btn {
  font-weight: 600;
  letter-spacing: 0;
  text-transform: none;
}

/* Share Buttons */
.share-buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.share-btn {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #E0E0E0;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
}

.share-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.share-btn.whatsapp:hover {
  background: #25D366;
  border-color: #25D366;
  color: white;
}

.share-btn.facebook:hover {
  background: #1877F2;
  border-color: #1877F2;
  color: white;
}

.share-btn.twitter:hover {
  background: #1DA1F2;
  border-color: #1DA1F2;
  color: white;
}

.share-btn.link:hover {
  background: #666;
  border-color: #666;
  color: white;
}

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #F0F0F0;
}

.info-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.info-label {
  font-size: 14px;
  color: #666;
}

.info-value {
  font-size: 14px;
  font-weight: 600;
  color: #1A1A1A;
  text-align: right;
}

/* Responsive */
@media (max-width: 960px) {
  .hero-section {
    min-height: 450px;
    background-attachment: scroll;
  }

  .hero-title {
    font-size: 36px;
  }

  .quick-stats {
    gap: 16px;
  }

  .stat-item {
    min-width: 150px;
  }

  .sidebar {
    position: relative;
    top: 0;
  }
}

@media (max-width: 600px) {
  .hero-section {
    min-height: 380px;
    padding-top: 24px;
  }

  .content-section {
    padding: 24px 16px 48px;
  }

  .hero-title {
    font-size: 28px;
  }

  .quick-stats {
    flex-direction: column;
    gap: 16px;
  }

  .stat-divider {
    display: none;
  }

  .benefits-grid {
    grid-template-columns: 1fr;
  }
}
</style>
