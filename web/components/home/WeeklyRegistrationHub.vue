<template>
  <section id="registrasi" class="weekly-registration-section mb-10 mb-md-16 pt-4">
    <!-- Header -->
    <div class="registration-header text-center mb-8">
      <div class="d-inline-flex align-center ga-2 px-4 py-1 rounded-pill status-pill mb-3">
        <span class="pulse-dot"></span>
        <span class="text-caption font-weight-bold tracking-wider text-uppercase">
          Aktivasi &amp; Agenda Pekan Ini
        </span>
      </div>

      <h2 class="text-h4 text-md-h3 section-headline registration-heading mb-3">
        Pilih Aktivasi &amp; Registrasi
      </h2>

      <p class="text-body-2 text-md-body-1 text-grey-darken-1 mx-auto" style="max-width: 600px; line-height: 1.6;">
        Pilih agenda yang ingin kamu ikuti. Klik kartu untuk detail lengkap dan formulir pendaftaran.
      </p>
    </div>

    <!-- Category Filter -->
    <div class="category-filter-row mb-6">
      <v-chip
        v-for="filter in filters"
        :key="filter.value"
        :variant="selectedFilter === filter.value ? 'flat' : 'outlined'"
        :color="selectedFilter === filter.value ? 'primary' : 'default'"
        :size="$vuetify.display.xs ? 'small' : 'default'"
        class="font-weight-bold filter-chip"
        @click="selectedFilter = filter.value"
      >
        <v-icon :start="!$vuetify.display.xs" size="14">{{ filter.icon }}</v-icon>
        <span class="filter-label-text">{{ filter.label }}</span>
        <span class="count-badge ml-1" :class="{ 'count-active': selectedFilter === filter.value }">
          {{ getCountByFilter(filter.value) }}
        </span>
      </v-chip>
    </div>

    <!-- Cards Grid: 2 per baris di semua ukuran -->
    <v-row dense>
      <v-col
        v-for="item in filteredActivations"
        :key="item.id"
        cols="6"
        sm="6"
        md="6"
        lg="6"
      >
        <!-- Compact Card -->
        <div class="compact-card" @click="openDetail(item)">
          <!-- Image Header -->
          <div class="compact-card-img">
            <img v-if="item.image" :src="item.image" :alt="item.title" />
            <div v-else class="compact-card-img-placeholder" :style="{ background: item.themeColorBg }"></div>

            <!-- Status Badge -->
            <span class="compact-status-dot"></span>
          </div>

          <!-- Card Body -->
          <div class="compact-card-body">
            <!-- Category Tag -->
            <span
              class="compact-cat-tag"
              :style="{ backgroundColor: item.themeColorBg, color: item.themeColorText }"
            >
              <v-icon size="11">{{ item.icon }}</v-icon>
              {{ item.shortCategory }}
            </span>

            <!-- Title -->
            <h3 class="compact-title">{{ item.title }}</h3>

            <!-- Schedule & Location -->
            <div class="compact-meta">
              <div class="compact-meta-row">
                <v-icon size="13" color="#DC2626">mdi-calendar-clock</v-icon>
                <span>{{ item.scheduleShort }}</span>
              </div>
              <div class="compact-meta-row" :class="{ 'text-amber-darken-3 font-weight-bold': item.isCurated }">
                <v-icon size="13" :color="item.isCurated ? '#D97706' : '#0284C7'">
                  {{ item.isCurated ? 'mdi-shield-lock-outline' : 'mdi-map-marker-radius' }}
                </v-icon>
                <span>{{ item.locationShort }}</span>
              </div>
            </div>

            <!-- Action Buttons: Regis Kegiatan & Detail -->
            <div class="compact-card-actions d-flex align-center ga-2 mt-auto pt-2 border-top" @click.stop>
              <!-- Primary Red Regis Button -->
              <v-btn
                :href="item.gformUrl"
                target="_blank"
                rel="noopener noreferrer"
                color="primary"
                rounded="pill"
                size="small"
                class="compact-reg-btn font-weight-bold elevation-1 flex-grow-1"
                @click.stop
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
                @click.stop="openDetail(item)"
              >
                <span>Detail</span>
                <v-icon end size="14">mdi-arrow-right</v-icon>
              </v-btn>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- View All Events Catalog CTA -->
    <div class="text-center mt-8 mb-2">
      <v-btn
        to="/events"
        variant="outlined"
        color="grey-darken-3"
        rounded="pill"
        size="large"
        class="font-weight-bold px-6 catalog-jump-btn"
      >
        <v-icon start size="18" color="#DC2626">mdi-calendar-multiselect</v-icon>
        Lihat Semua Jadwal &amp; Arsip di Katalog Event
        <v-icon end size="16">mdi-arrow-right</v-icon>
      </v-btn>
    </div>

    <!-- Bottom Help Text -->
    <div class="text-center mt-3 px-4">
      <p class="text-caption text-grey-darken-1 mb-0">
        Ada pertanyaan? DM kami di Instagram
        <a
          href="https://instagram.com/jalanbarengind"
          target="_blank"
          rel="noopener noreferrer"
          class="text-primary font-weight-bold text-decoration-none"
        >@jalanbarengind</a>
      </p>
    </div>

    <!-- ===== Detail Bottom Sheet ===== -->
    <v-bottom-sheet v-model="showDetail" max-width="600">
      <v-card v-if="selected" rounded="t-xl" class="detail-sheet">
        <!-- Image Header -->
        <div v-if="selected.image" class="detail-img-wrapper">
          <img :src="selected.image" :alt="selected.title" class="detail-img" />
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
              :style="{ backgroundColor: selected.themeColorBg, color: selected.themeColorText }"
            >
              <v-icon size="13" class="mr-1">{{ selected.icon }}</v-icon>
              {{ selected.shortCategory || selected.categoryLabel }}
            </span>
            <span class="open-status-badge">
              <span class="status-indicator-dot"></span>
              {{ selected.statusText }}
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="detail-content">
          <!-- Header: Title + Host -->
          <div class="detail-header mb-2">
            <h2 class="detail-title">{{ selected.title }}</h2>
            <div v-if="selected.collaborator" class="detail-collaborator d-flex align-center flex-wrap ga-1">
              <v-icon size="14" color="#D97706">mdi-handshake-outline</v-icon>
              <span>Kolaborasi bersama <strong>{{ selected.collaborator.name }}</strong></span>
              <span v-if="selected.collaborator.role" class="collaborator-pill">{{ selected.collaborator.role }}</span>
            </div>
          </div>

          <!-- Description -->
          <p class="detail-desc mb-3">{{ selected.description }}</p>

          <!-- Unified Detail Specs Card with vibrant color accents -->
          <div class="detail-specs-card mb-3">
            <div class="spec-item">
              <div class="spec-icon-box icon-box-schedule">
                <v-icon size="16" color="#DC2626">mdi-calendar-clock</v-icon>
              </div>
              <div class="spec-text">
                <span class="spec-label">Waktu</span>
                <span class="spec-val">{{ selected.schedule }}</span>
              </div>
            </div>

            <div class="spec-item" :class="{ 'spec-item-curated': selected.isCurated }">
              <div class="spec-icon-box" :class="selected.isCurated ? 'icon-box-secret' : 'icon-box-location'">
                <v-icon size="16" :color="selected.isCurated ? '#D97706' : '#0284C7'">
                  {{ selected.isCurated ? 'mdi-shield-lock-outline' : 'mdi-map-marker-radius' }}
                </v-icon>
              </div>
              <div class="spec-text">
                <span class="spec-label">Titik Kumpul</span>
                <span class="spec-val" :class="{ 'text-amber-darken-3 font-weight-bold': selected.isCurated }">{{ selected.meetingPoint }}</span>
              </div>
            </div>

            <div class="spec-item">
              <div class="spec-icon-box icon-box-ticket">
                <v-icon size="16" color="#16A34A">mdi-ticket-outline</v-icon>
              </div>
              <div class="spec-text">
                <span class="spec-label">Biaya &amp; Kuota</span>
                <span class="spec-val">{{ selected.feeAndQuota }}</span>
              </div>
            </div>

            <div class="spec-item spec-item-deadline">
              <div class="spec-icon-box icon-box-deadline">
                <v-icon size="16" color="#DC2626">mdi-clock-alert-outline</v-icon>
              </div>
              <div class="spec-text">
                <span class="spec-label deadline-label">Batas Pendaftaran</span>
                <span class="spec-val deadline-val">{{ selected.deadline }}</span>
              </div>
            </div>
          </div>

          <!-- Curation Alert in Sheet -->
          <div v-if="selected.isCurated" class="pa-2 px-3 rounded-lg border mb-3 d-flex align-center ga-2" style="background: #FFFDF5; border-color: #FDE68A !important;">
            <v-icon size="16" color="#D97706">mdi-information-outline</v-icon>
            <span class="text-caption text-grey-darken-3" style="line-height: 1.4;">
              <strong>Sistem Kurasi:</strong> Titik kumpul rahasia dan akan dikirim langsung via Japri WhatsApp / DM Instagram bagi pendaftar yang lolos kurasi.
            </span>
          </div>

          <!-- CTA Button -->
          <v-btn
            :href="selected.gformUrl"
            target="_blank"
            rel="noopener noreferrer"
            color="primary"
            size="large"
            block
            rounded="pill"
            class="gform-btn elevation-2 mb-2"
          >
            <v-icon start size="18">mdi-clipboard-edit-outline</v-icon>
            <span>{{ selected.isCurated ? 'Daftar &amp; Ikuti Kurasi' : 'Daftar Kegiatan' }}</span>
            <v-icon end size="16">mdi-arrow-right</v-icon>
          </v-btn>


          <!-- Detail Page Link -->
          <div class="text-center">
            <NuxtLink
              v-if="selected.eventId"
              :to="`/events/${selected.eventId}`"
              class="detail-link"
              @click="showDetail = false"
            >
              Lihat halaman lengkap event
              <v-icon size="13">mdi-chevron-right</v-icon>
            </NuxtLink>
            <NuxtLink
              v-else-if="selected.activationSlug"
              :to="`/aktivasi/${selected.activationSlug}`"
              class="detail-link"
              @click="showDetail = false"
            >
              Lihat halaman lengkap aktivasi
              <v-icon size="13">mdi-chevron-right</v-icon>
            </NuxtLink>
          </div>
        </div>
      </v-card>
    </v-bottom-sheet>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import { useImageUrl } from '~/composables/useImageUrl'

const { api } = useApi()
const { getImageUrl } = useImageUrl()

const selectedFilter = ref('all')
const showDetail = ref(false)
const selected = ref<any>(null)

const openDetail = (item: any) => {
  selected.value = item
  showDetail.value = true
}

const filters = [
  { value: 'all', label: 'Semua', icon: 'mdi-view-grid-outline' },
  { value: 'walking', label: 'Jalan Santai', icon: 'mdi-walk' },
  { value: 'book', label: 'Buku', icon: 'mdi-book-open-page-variant' },
  { value: 'culinary', label: 'Kuliner', icon: 'mdi-silverware-fork-knife' },
  { value: 'explore', label: 'Eksplorasi', icon: 'mdi-compass-outline' }
]

const weeklyActivations = ref([
  {
    id: 1,
    category: 'walking',
    categoryLabel: 'Jalan Santai • City Chapter',
    shortCategory: 'Jalan Santai',
    icon: 'mdi-walk',
    themeColorBg: '#FEE2E2',
    themeColorText: '#DC2626',
    title: 'Jalan Bareng Makassar',
    activationSlug: 'jalan-bareng-makassar',
    image: '/images/hero/walk_2.jpg',
    collaborator: { name: 'Kolektif Trotoar Kota', role: 'Komunitas Pejalan' },
    description: 'Jalan santai menyusuri trotoar dan lorong heritage kota Makassar. Terbuka untuk semua warga dan kawan perantau.',
    schedule: 'Sabtu pagi, 06.00 – 08.30 WITA',
    scheduleShort: 'Sabtu, 06.00 WITA',
    meetingPoint: '🔒 Rahasia • Dikirim via WhatsApp/DM bagi peserta yang lolos kurasi',
    locationShort: '🔒 Tikum Rahasia',
    feeAndQuota: 'Gratis • Sistem Kurasi Peserta',
    isCurated: true,
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 2,
    category: 'book',
    categoryLabel: 'Literasi • Aktivasi Tematik',
    shortCategory: 'Diskusi Buku',
    icon: 'mdi-book-open-page-variant',
    themeColorBg: '#E0F2FE',
    themeColorText: '#0284C7',
    title: 'Diskusi Buku Bareng',
    activationSlug: 'diskusi-buku-bareng',
    image: '/images/hero/walk_7.jpg',
    collaborator: { name: 'Pojok Baca & Penerbit Indie', role: 'Partner Literasi' },
    description: 'Membawa buku bacaan favorit, jalan santai sore hari, dan sesi melingkar untuk saling bertukar refleksi bacaan.',
    schedule: 'Minggu sore, 16.00 – 18.00 WITA',
    scheduleShort: 'Minggu, 16.00 WITA',
    meetingPoint: 'Taman Macan / Gazebo Ruang Publik Makassar',
    locationShort: 'Taman Macan',
    feeAndQuota: 'Gratis • Bawa buku sendiri',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 3,
    category: 'culinary',
    categoryLabel: 'Kuliner • Cerita Rasa',
    shortCategory: 'Kuliner',
    icon: 'mdi-silverware-fork-knife',
    themeColorBg: '#FEF3C7',
    themeColorText: '#B45309',
    title: 'Makan Bareng',
    activationSlug: 'makan-bareng',
    image: '/images/hero/walk_9.jpg',
    collaborator: { name: 'Kedai Kopi & Kuliner Pecinan', role: 'F&B Partner' },
    description: 'Jalan kaki menyusuri gang kuliner lokal legendaris sembari menikmati santapan khas dan mendengar cerita penjual lokal.',
    schedule: 'Sabtu sore, 16.30 – 19.00 WITA',
    scheduleShort: 'Sabtu, 16.30 WITA',
    meetingPoint: 'Kawasan Pecinan / Jalan Sulawesi Makassar',
    locationShort: 'Kawasan Pecinan',
    feeAndQuota: 'Mandiri (Bayar makanan masing-masing)',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 4,
    category: 'explore',
    categoryLabel: 'Eksplorasi • Rute Khusus',
    shortCategory: 'Eksplorasi',
    icon: 'mdi-compass-outline',
    themeColorBg: '#DCFCE7',
    themeColorText: '#15803D',
    title: 'Explore Bareng',
    activationSlug: 'explore-bareng',
    image: '/images/hero/walk_1.jpg',
    collaborator: { name: 'Inisiatif Heritage Makassar', role: 'Knowledge Partner' },
    description: 'Menelusuri sudut kota dengan rute tematik tersembunyi, cagar budaya, dan cerita sejarah yang jarang diangkat.',
    schedule: 'Minggu pagi, 06.15 – 09.00 WITA',
    scheduleShort: 'Minggu, 06.15 WITA',
    meetingPoint: 'Benteng Rotterdam Makassar (Pintu Utama)',
    locationShort: 'Benteng Rotterdam',
    feeAndQuota: 'Gratis • Kuota 25 orang',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  }
])

const filteredActivations = computed(() => {
  if (selectedFilter.value === 'all') return weeklyActivations.value
  return weeklyActivations.value.filter(item => item.category === selectedFilter.value)
})

const getCountByFilter = (filterVal: string) => {
  if (filterVal === 'all') return weeklyActivations.value.length
  return weeklyActivations.value.filter(item => item.category === filterVal).length
}

const isCuratedEvent = (ev: any) => {
  if (!ev) return false
  const actSlug = ev.activation?.slug || ''
  const actName = (ev.activation?.name || ev.activation?.title || '').toLowerCase()
  const evName = (ev.name || '').toLowerCase()
  const desc = (ev.description || '').toLowerCase()

  if (actSlug === 'jalan-bareng-makassar' || actName.includes('jalan bareng makassar') || ev.activation_id === 2) {
    return true
  }
  if (evName.includes('jalan bareng makassar') || (evName.includes('makassar') && ev.type === 'walking')) {
    return true
  }
  if (desc.includes('kurasi') || desc.includes('tikum rahasia') || desc.includes('titik kumpul rahasia')) {
    return true
  }
  return false
}

const getEventLocation = (event: any) => {
  if (!event) return 'Makassar & Sekitarnya'
  if (isCuratedEvent(event)) return '🔒 Tikum Rahasia'
  if (event.meeting_point) return event.meeting_point
  if (event.description) {
    const match = event.description.match(/Titik Kumpul:?\s*<\/strong>\s*([^<]+)/i) ||
                  event.description.match(/Titik Kumpul:?\s*([^<\n]+)/i)
    if (match && match[1]) return match[1].trim()
    const ruteMatch = event.description.match(/Rute:?\s*<\/strong>\s*([^<]+)/i) ||
                      event.description.match(/Rute:?\s*([^<\n]+)/i)
    if (ruteMatch && ruteMatch[1]) return ruteMatch[1].trim().split('-')[0].trim()
  }
  if (event.activation?.city) return event.activation.city
  return 'Makassar'
}

const formatDateSchedule = (dateStr: string) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    hour: '2-digit',
    minute: '2-digit'
  }) + ' WITA'
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

onMounted(async () => {
  try {
    const res = await api.get('/events', { params: { status: 'upcoming', per_page: 6 } })
    const events = res.data?.data || res.data || []
    if (events && events.length > 0) {
      weeklyActivations.value = events.map((ev: any) => {
        const isWalking = ev.type === 'walking'
        const isCurated = isCuratedEvent(ev)
        return {
          id: ev.id,
          eventId: ev.id,
          category: isWalking ? 'walking' : 'explore',
          categoryLabel: ev.activation?.title || (isWalking ? 'Jalan Santai • City Chapter' : 'Agenda Tematik • Komunitas'),
          shortCategory: isWalking ? 'Jalan Santai' : 'Tematik',
          icon: isWalking ? 'mdi-walk' : 'mdi-calendar-text-outline',
          themeColorBg: isWalking ? '#FEE2E2' : '#E0F2FE',
          themeColorText: isWalking ? '#DC2626' : '#0284C7',
          title: ev.name,
          activationSlug: ev.activation?.slug || null,
          image: ev.poster ? getImageUrl(ev.poster) : '/images/hero/walk_2.jpg',
          collaborator: ev.user?.name ? { name: ev.user.name, role: 'Penyelenggara' } : null,
          description: ev.description ? ev.description.replace(/<[^>]*>/g, '').trim() : 'Agenda jalan dan berkumpul bersama komunitas pejalan kaki.',
          schedule: formatDateSchedule(ev.date),
          scheduleShort: formatScheduleShort(ev.date),
          meetingPoint: isCurated ? '🔒 Rahasia • Dikirim via WhatsApp/DM bagi peserta yang lolos kurasi' : getEventLocation(ev),
          locationShort: isCurated ? '🔒 Tikum Rahasia' : getEventLocation(ev),
          feeAndQuota: isCurated ? 'Gratis • Sistem Kurasi Peserta' : 'Gratis • Terbuka untuk umum',
          deadline: 'Sebelum kegiatan dimulai',
          statusText: isCurated ? 'Kurasi Dibuka' : 'Open Registration',
          isCurated: isCurated,
          gformUrl: ev.registration_link || 'https://instagram.com/jalanbarengind'
        }
      })
    }
  } catch (err) {
    console.error('Error fetching upcoming events in WeeklyRegistrationHub:', err)
  }
})
</script>

<style scoped>
.weekly-registration-section {
  scroll-margin-top: 90px;
}

.status-pill {
  background: #DCFCE7;
  color: #166534;
  border: 1px solid #BBF7D0;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #16A34A;
  display: inline-block;
  animation: pulse-ring 1.8s infinite;
}

@keyframes pulse-ring {
  0%   { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7); }
  70%  { box-shadow: 0 0 0 8px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}

.registration-heading {
  font-weight: 800 !important;
  letter-spacing: -0.035em !important;
  line-height: 1.2 !important;
}

.catalog-jump-btn {
  background: white !important;
  border: 1px solid #E2E8F0 !important;
  transition: all 0.25s ease !important;
}

.catalog-jump-btn:hover {
  border-color: #DC2626 !important;
  color: #DC2626 !important;
  background: #FEF2F2 !important;
  transform: translateY(-2px);
}

.category-filter-row {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
  padding: 0 16px;
}

.filter-chip {
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  min-height: 40px;
}

.count-badge {
  font-size: 0.72rem;
  background: #E5E7EB;
  color: #374151;
  padding: 1px 6px;
  border-radius: 10px;
}

.count-active {
  background: rgba(255, 255, 255, 0.3);
  color: #FFFFFF;
}

/* ===== COMPACT CARD ===== */
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

.compact-card-img {
  position: relative;
  height: 160px;
  overflow: hidden;
}

@media (min-width: 960px) {
  .compact-card-img {
    height: 175px;
  }
}

.compact-card-actions {
  border-top-color: #F3F4F6 !important;
}

.compact-reg-btn {
  text-transform: none;
  letter-spacing: 0.01em;
  font-size: 0.78rem !important;
  height: 32px !important;
}

.compact-detail-btn {
  text-transform: none;
  letter-spacing: 0.01em;
  font-size: 0.75rem !important;
  height: 32px !important;
}

.compact-card-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.compact-card-img-placeholder {
  width: 100%;
  height: 100%;
}

.compact-status-dot {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #16A34A;
  border: 2px solid white;
  box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.4);
  animation: pulse-ring 1.8s infinite;
}

.compact-card-body {
  padding: 10px 12px 12px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.compact-cat-tag {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  align-self: flex-start;
  gap: 3px;
  margin-bottom: 6px;
}

.compact-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.35;
  margin: 0 0 6px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  letter-spacing: -0.01em;
}

.compact-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: auto; /* Pushes action buttons down, leaving blank space between meta and buttons */
  padding-bottom: 12px;
}

.compact-meta-row {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.72rem;
  color: #6B7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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

.collaborator-pill {
  font-size: 0.65rem;
  font-weight: 600;
  color: #92400E;
  background: #FEF3C7;
  border: 1px solid #FDE68A;
  padding: 1px 7px;
  border-radius: 9999px;
}

.detail-desc {
  font-size: 0.84rem;
  line-height: 1.5;
  color: #4B5563;
}

/* Crafted Detail Specs Card */
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

.icon-box-secret {
  background: #FFFBEB !important;
  border-color: #FDE68A !important;
}

.spec-item-curated {
  background: #FFFDF5 !important;
  border: 1px solid #FDE68A !important;
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

@media (max-width: 600px) {
  .category-filter-row {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    padding: 0 4px;
    margin-bottom: 20px !important;
  }

  .filter-chip {
    font-size: 0.7rem !important;
    min-height: 26px !important;
    height: 26px !important;
    padding: 0 8px !important;
  }

  .filter-label-text {
    font-size: 0.7rem;
    line-height: 1;
  }

  .count-badge {
    font-size: 0.62rem;
    padding: 0 4px;
    margin-left: 3px !important;
    min-width: 15px;
    text-align: center;
  }

  .compact-card-img {
    height: 110px;
  }

  .compact-card-body {
    padding: 10px 10px 12px;
  }

  .compact-title {
    font-size: 0.98rem;
    line-height: 1.35;
    margin-bottom: 6px;
  }

  .compact-meta {
    padding-bottom: 8px;
  }

  .compact-meta-row {
    font-size: 0.65rem;
  }

  .compact-reg-btn {
    font-size: 0.72rem !important;
    padding: 0 10px !important;
    height: 28px !important;
  }

  .detail-img-wrapper {
    height: 250px;
  }

  .detail-badges {
    bottom: 10px;
    left: 12px;
    right: 12px;
  }

  .detail-content {
    padding: 14px 14px 18px !important;
  }

  .detail-title {
    font-size: 1.15rem !important;
    line-height: 1.25 !important;
  }

  .detail-collaborator {
    font-size: 0.75rem !important;
  }

  .collaborator-pill {
    font-size: 0.6rem !important;
    padding: 1px 6px !important;
  }

  .detail-desc {
    font-size: 0.78rem !important;
    line-height: 1.45 !important;
    margin-bottom: 10px !important;
  }

  .detail-specs-card {
    border-radius: 12px !important;
    margin-bottom: 12px !important;
  }

  .spec-item {
    padding: 8px 10px !important;
    gap: 10px !important;
  }

  .spec-icon-box {
    width: 28px !important;
    height: 28px !important;
    border-radius: 7px !important;
  }

  .spec-label {
    font-size: 0.65rem !important;
  }

  .spec-val {
    font-size: 0.78rem !important;
  }

  .gform-btn {
    height: 42px !important;
    font-size: 0.85rem !important;
    margin-bottom: 6px !important;
  }
}
</style>
