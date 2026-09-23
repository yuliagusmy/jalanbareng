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

      <h2 class="text-h4 text-md-h3 font-weight-black text-grey-darken-4 mb-3">
        Pilih Aktivasi &amp; Registrasi
      </h2>

      <p class="text-body-1 text-grey-darken-1 mx-auto" style="max-width: 600px; line-height: 1.6;">
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
        size="default"
        class="font-weight-bold filter-chip"
        @click="selectedFilter = filter.value"
      >
        <v-icon start size="16">{{ filter.icon }}</v-icon>
        {{ filter.label }}
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
              <div class="compact-meta-row">
                <v-icon size="13" color="#0284C7">mdi-map-marker-radius</v-icon>
                <span>{{ item.locationShort }}</span>
              </div>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>

    <!-- Bottom Help Text -->
    <div class="text-center mt-6 px-4">
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

          <!-- Badges over image -->
          <div class="detail-badges">
            <span
              class="activation-type-tag"
              :style="{ backgroundColor: selected.themeColorBg, color: selected.themeColorText }"
            >
              <v-icon size="13" class="mr-1">{{ selected.icon }}</v-icon>
              {{ selected.categoryLabel }}
            </span>
            <span class="open-status-badge">
              <span class="status-indicator-dot"></span>
              {{ selected.statusText }}
            </span>
          </div>
        </div>

        <!-- Content -->
        <div class="detail-content pa-5">
          <!-- Title -->
          <h2 class="detail-title text-grey-darken-4 mb-2">{{ selected.title }}</h2>

          <!-- Collaborator -->
          <div v-if="selected.collaborator" class="collaborator-badge d-flex align-center ga-2 px-3 py-2 rounded-lg mb-3">
            <v-icon size="16" color="#D97706">mdi-handshake-outline</v-icon>
            <div class="text-caption font-weight-medium text-grey-darken-3 d-flex flex-wrap align-center ga-1">
              <span>Kolaborasi bersama:</span>
              <strong class="text-grey-darken-4">{{ selected.collaborator.name }}</strong>
              <span class="collaborator-role-pill">{{ selected.collaborator.role }}</span>
            </div>
          </div>

          <!-- Description -->
          <p class="text-body-2 text-grey-darken-1 mb-4" style="line-height:1.6;">{{ selected.description }}</p>

          <!-- Detail Meta -->
          <div class="detail-meta-box mb-4">
            <div class="detail-meta-row">
              <v-icon size="18" color="#DC2626">mdi-calendar-clock</v-icon>
              <div>
                <div class="meta-label">Waktu Kegiatan</div>
                <div class="meta-value">{{ selected.schedule }}</div>
              </div>
            </div>
            <div class="detail-meta-row">
              <v-icon size="18" color="#0284C7">mdi-map-marker-radius</v-icon>
              <div>
                <div class="meta-label">Titik Kumpul</div>
                <div class="meta-value">{{ selected.meetingPoint }}</div>
              </div>
            </div>
            <div class="detail-meta-row">
              <v-icon size="18" color="#16A34A">mdi-ticket-outline</v-icon>
              <div>
                <div class="meta-label">Biaya &amp; Kuota</div>
                <div class="meta-value">{{ selected.feeAndQuota }}</div>
              </div>
            </div>
          </div>

          <!-- Deadline -->
          <div class="deadline-note mb-5">
            <v-icon size="15" color="#DC2626">mdi-clock-alert-outline</v-icon>
            <span>Batas daftar: <strong>{{ selected.deadline }}</strong></span>
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
            class="font-weight-bold gform-btn elevation-2 mb-3"
          >
            <v-icon start size="20">mdi-clipboard-edit-outline</v-icon>
            <span>Daftar &amp; Amankan Slot</span>
            <v-icon end size="16">mdi-arrow-top-right</v-icon>
          </v-btn>

          <!-- Detail Page Link -->
          <div class="text-center">
            <NuxtLink
              v-if="selected.activationSlug"
              :to="`/aktivasi/${selected.activationSlug}`"
              class="text-caption text-primary font-weight-bold text-decoration-none"
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
import { ref, computed } from 'vue'

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
    meetingPoint: 'Anjungan Pantai Losari (Depan Masjid Amirul Mukminin)',
    locationShort: 'Pantai Losari',
    feeAndQuota: 'Gratis • Terbuka untuk umum',
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
}

.compact-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px -4px rgba(0, 0, 0, 0.1);
}

.compact-card-img {
  position: relative;
  height: 100px;
  overflow: hidden;
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
}

.compact-cat-tag {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  gap: 3px;
  margin-bottom: 6px;
}

.compact-title {
  font-size: 0.88rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.3;
  margin: 0 0 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.compact-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
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
  height: 200px;
  overflow: hidden;
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
  background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.35) 100%);
}

.detail-badges {
  position: absolute;
  bottom: 14px;
  left: 16px;
  right: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
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

.detail-title {
  font-size: 1.35rem;
  font-weight: 900;
  line-height: 1.25;
  letter-spacing: -0.02em;
}

.collaborator-badge {
  background: #FFFBEB;
  border: 1px solid #FDE68A;
}

.collaborator-role-pill {
  background: #FEF3C7;
  color: #92400E;
  font-size: 0.68rem;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 9999px;
  border: 1px solid #FCD34D;
}

.detail-meta-box {
  background: #F9FAFB;
  border-radius: 12px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.detail-meta-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.meta-label {
  font-size: 0.72rem;
  color: #9CA3AF;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.meta-value {
  font-size: 0.9rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.35;
}

.deadline-note {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8rem;
  color: #6B7280;
}

.gform-btn {
  height: 50px !important;
  font-size: 0.95rem;
  letter-spacing: 0.02em;
  text-transform: none;
}

@media (max-width: 600px) {
  .category-filter-row {
    gap: 8px;
    padding: 0 12px;
  }

  .filter-chip {
    font-size: 0.8rem;
    min-height: 36px;
  }

  .compact-card-img {
    height: 90px;
  }

  .compact-card-body {
    padding: 10px 12px;
  }

  .compact-title {
    font-size: 0.8rem;
  }

  .compact-meta-row {
    font-size: 0.65rem;
  }
}
</style>
