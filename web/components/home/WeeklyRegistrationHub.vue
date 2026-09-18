<template>
  <section id="registrasi" class="weekly-registration-section mb-16 pt-4">
    <!-- Header Box with Grounded Antislop Aesthetic -->
    <div class="registration-header text-center mb-8">
      <div class="d-inline-flex align-center ga-2 px-4 py-1 rounded-pill status-pill mb-3">
        <span class="pulse-dot"></span>
        <span class="text-caption font-weight-bold tracking-wider text-uppercase">
          Aktivasi & Agenda Pekan Ini
        </span>
      </div>

      <h2 class="text-h4 text-md-h3 font-weight-black text-grey-darken-4 mb-3">
        Pilih Aktivasi & Registrasi
      </h2>

      <p class="text-body-1 text-grey-darken-1 mx-auto" style="max-width: 680px; line-height: 1.6;">
        Setiap pekan ada beragam agenda jalan dan aktivasi di berbagai sudut kota. Pilih agenda yang ingin kamu ikuti di bawah ini untuk mengisi formulir pendaftaran melalui Google Form masing-masing.
      </p>

      <!-- Multi-Registration Friendly Callout -->
      <div class="d-inline-flex align-center ga-2 px-4 py-2 rounded-lg bg-grey-lighten-4 mt-2 info-badge">
        <v-icon size="small" color="#16A34A">mdi-check-circle-outline</v-icon>
        <span class="text-caption text-grey-darken-3 font-weight-medium">
          Kamu boleh mendaftar di <strong>lebih dari satu aktivasi</strong> selama jadwalnya tidak bentrok!
        </span>
      </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="d-flex justify-center flex-wrap ga-2 mb-8 px-2">
      <v-chip
        v-for="filter in filters"
        :key="filter.value"
        :variant="selectedFilter === filter.value ? 'flat' : 'outlined'"
        :color="selectedFilter === filter.value ? 'primary' : 'default'"
        size="default"
        class="font-weight-bold filter-chip"
        @click="selectedFilter = filter.value"
      >
        <v-icon start size="18">{{ filter.icon }}</v-icon>
        {{ filter.label }}
        <span class="count-badge ml-1" :class="{ 'count-active': selectedFilter === filter.value }">
          {{ getCountByFilter(filter.value) }}
        </span>
      </v-chip>
    </div>

    <!-- Activation Cards Grid -->
    <v-row>
      <v-col
        v-for="item in filteredActivations"
        :key="item.id"
        cols="12"
        sm="6"
        lg="6"
      >
        <v-card
          elevation="0"
          rounded="xl"
          class="registration-card h-100 d-flex flex-column border overflow-hidden"
        >
          <!-- Card Cover Image with Overlay Badges -->
          <div v-if="item.image" class="card-cover-image position-relative">
            <v-img :src="item.image" height="190" cover class="rounded-t-xl">
              <div class="d-flex justify-space-between align-center pa-4">
                <span
                  class="activation-type-tag elevation-1"
                  :style="{ backgroundColor: item.themeColorBg, color: item.themeColorText }"
                >
                  <v-icon size="14" class="mr-1">{{ item.icon }}</v-icon>
                  {{ item.categoryLabel }}
                </span>

                <span class="open-status-badge elevation-1">
                  <span class="status-indicator-dot"></span>
                  {{ item.statusText }}
                </span>
              </div>
            </v-img>
          </div>

          <!-- Card Header Info -->
          <div class="card-top-bar pa-5 pb-3">
            <div v-if="!item.image" class="d-flex justify-space-between align-center mb-2">
              <span
                class="activation-type-tag"
                :style="{ backgroundColor: item.themeColorBg, color: item.themeColorText }"
              >
                <v-icon size="14" class="mr-1">{{ item.icon }}</v-icon>
                {{ item.categoryLabel }}
              </span>

              <span class="open-status-badge">
                <span class="status-indicator-dot"></span>
                {{ item.statusText }}
              </span>
            </div>

            <h3 class="text-h5 font-weight-bold text-grey-darken-4 mt-1">
              {{ item.title }}
            </h3>

            <!-- Collaborator / Brand Partner Badge -->
            <div v-if="item.collaborator" class="collaborator-badge d-flex align-center ga-2 px-3 py-1-5 rounded-lg my-2">
              <v-icon size="16" color="#D97706">mdi-handshake-outline</v-icon>
              <div class="text-caption font-weight-medium text-grey-darken-3 d-flex flex-wrap align-center ga-1">
                <span>Kolaborasi bersama:</span>
                <strong class="text-grey-darken-4">{{ item.collaborator.name }}</strong>
                <span class="collaborator-role-pill">{{ item.collaborator.role }}</span>
              </div>
            </div>

            <p class="text-body-2 text-grey-darken-1 mb-0 mt-1 line-clamp-2">
              {{ item.description }}
            </p>
          </div>

          <!-- Schedule & Location Details -->
          <div class="card-meta-details px-5 py-3 bg-grey-lighten-5 my-1">
            <div class="meta-row d-flex align-center ga-3 mb-2">
              <div class="meta-icon-box">
                <v-icon size="18" color="#DC2626">mdi-calendar-clock</v-icon>
              </div>
              <div>
                <div class="text-caption text-grey-darken-1 font-weight-medium">Waktu Kegiatan</div>
                <div class="text-body-2 font-weight-bold text-grey-darken-3">{{ item.schedule }}</div>
              </div>
            </div>

            <div class="meta-row d-flex align-center ga-3 mb-2">
              <div class="meta-icon-box">
                <v-icon size="18" color="#0284C7">mdi-map-marker-radius</v-icon>
              </div>
              <div>
                <div class="text-caption text-grey-darken-1 font-weight-medium">Titik Kumpul</div>
                <div class="text-body-2 font-weight-bold text-grey-darken-3">{{ item.meetingPoint }}</div>
              </div>
            </div>

            <div class="meta-row d-flex align-center ga-3">
              <div class="meta-icon-box">
                <v-icon size="18" color="#16A34A">mdi-ticket-outline</v-icon>
              </div>
              <div>
                <div class="text-caption text-grey-darken-1 font-weight-medium">Biaya & Kuota</div>
                <div class="text-body-2 font-weight-bold text-grey-darken-3">{{ item.feeAndQuota }}</div>
              </div>
            </div>
          </div>

          <!-- Card Actions (Direct to Google Form) -->
          <div class="card-action-bar pa-5 mt-auto">
            <v-btn
              :href="item.gformUrl"
              target="_blank"
              rel="noopener noreferrer"
              color="primary"
              size="large"
              block
              rounded="pill"
              class="font-weight-bold gform-btn elevation-2"
            >
              <v-icon start size="20">mdi-clipboard-edit-outline</v-icon>
              <span>Daftar & Amankan Slot</span>
              <v-icon end size="16">mdi-arrow-top-right</v-icon>
            </v-btn>

            <div class="d-flex justify-space-between align-center mt-2 px-1">
              <span class="text-caption text-grey-darken-1">
                <v-icon size="14" class="mr-1">mdi-clock-outline</v-icon>
                Batas daftar: {{ item.deadline }}
              </span>

              <NuxtLink
                v-if="item.activationSlug"
                :to="`/aktivasi/${item.activationSlug}`"
                class="text-caption text-primary font-weight-bold text-decoration-none"
              >
                Tentang Aktivasi
                <v-icon size="12">mdi-chevron-right</v-icon>
              </NuxtLink>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Bottom Help Text for Registrants -->
    <div class="text-center mt-8 px-4">
      <p class="text-caption text-grey-darken-1 mb-0">
        Ada pertanyaan seputar registrasi atau aktivasi? Hubungi kami via Instagram
        <a
          href="https://instagram.com/jalanbarengind"
          target="_blank"
          rel="noopener noreferrer"
          class="text-primary font-weight-bold text-decoration-none"
        >
          @jalanbarengind
        </a>
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const selectedFilter = ref('all')

const filters = [
  { value: 'all', label: 'Semua Dibuka', icon: 'mdi-view-grid-outline' },
  { value: 'walking', label: 'Jalan Santai', icon: 'mdi-walk' },
  { value: 'book', label: 'Diskusi Buku', icon: 'mdi-book-open-page-variant' },
  { value: 'culinary', label: 'Makan & Kuliner', icon: 'mdi-silverware-fork-knife' },
  { value: 'explore', label: 'Eksplorasi', icon: 'mdi-compass-outline' }
]

// Activations that open registration this week
const weeklyActivations = ref([
  {
    id: 1,
    category: 'walking',
    categoryLabel: 'Jalan Santai • City Chapter',
    icon: 'mdi-walk',
    themeColorBg: '#FEE2E2',
    themeColorText: '#DC2626',
    title: 'Jalan Bareng Makassar',
    activationSlug: 'jalan-bareng-makassar',
    image: '/images/hero/walk_2.jpg',
    collaborator: {
      name: 'Kolektif Trotoar Kota',
      role: 'Komunitas Pejalan'
    },
    description: 'Jalan santai menyusuri trotoar dan lorong heritage kota Makassar. Terbuka untuk semua warga dan kawan perantau.',
    schedule: 'Sabtu pagi, 06.00 – 08.30 WITA',
    meetingPoint: 'Anjungan Pantai Losari (Depan Masjid Amirul Mukminin)',
    feeAndQuota: 'Gratis • Terbuka untuk umum',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 2,
    category: 'book',
    categoryLabel: 'Literasi • Aktivasi Tematik',
    icon: 'mdi-book-open-page-variant',
    themeColorBg: '#E0F2FE',
    themeColorText: '#0284C7',
    title: 'Diskusi Buku Bareng',
    activationSlug: 'diskusi-buku-bareng',
    image: '/images/hero/walk_7.jpg',
    collaborator: {
      name: 'Pojok Baca & Penerbit Indie',
      role: 'Partner Literasi'
    },
    description: 'Membawa buku bacaan favorit, jalan santai sore hari, dan sesi melingkar untuk saling bertukar refleksi bacaan.',
    schedule: 'Minggu sore, 16.00 – 18.00 WITA',
    meetingPoint: 'Taman Macan / Gazebo Ruang Publik Makassar',
    feeAndQuota: 'Gratis • Bawa buku sendiri',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 3,
    category: 'culinary',
    categoryLabel: 'Kuliner • Cerita Rasa',
    icon: 'mdi-silverware-fork-knife',
    themeColorBg: '#FEF3C7',
    themeColorText: '#B45309',
    title: 'Makan Bareng',
    activationSlug: 'makan-bareng',
    image: '/images/hero/walk_9.jpg',
    collaborator: {
      name: 'Kedai Kopi & Kuliner Pecinan',
      role: 'F&B Partner'
    },
    description: 'Jalan kaki menyusuri gang kuliner lokal legendaris sembari menikmati santapan khas dan mendengar cerita penjual lokal.',
    schedule: 'Sabtu sore, 16.30 – 19.00 WITA',
    meetingPoint: 'Kawasan Pecinan / Jalan Sulawesi Makassar',
    feeAndQuota: 'Mandiri (Bayar makanan masing-masing)',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  },
  {
    id: 4,
    category: 'explore',
    categoryLabel: 'Eksplorasi • Rute Khusus',
    icon: 'mdi-compass-outline',
    themeColorBg: '#DCFCE7',
    themeColorText: '#15803D',
    title: 'Explore Bareng',
    activationSlug: 'explore-bareng',
    image: '/images/hero/walk_1.jpg',
    collaborator: {
      name: 'Inisiatif Heritage Makassar',
      role: 'Knowledge Partner'
    },
    description: 'Menelusuri sudut kota dengan rute tematik tersembunyi, cagar budaya, dan cerita sejarah yang jarang diangkat.',
    schedule: 'Minggu pagi, 06.15 – 09.00 WITA',
    meetingPoint: 'Benteng Rotterdam Makassar (Pintu Utama)',
    feeAndQuota: 'Gratis • Kuota 25 orang',
    deadline: 'Jumat malam, 22.00 WITA',
    statusText: 'Open Registration',
    gformUrl: 'https://instagram.com/jalanbarengind'
  }
])

const filteredActivations = computed(() => {
  if (selectedFilter.value === 'all') {
    return weeklyActivations.value
  }
  return weeklyActivations.value.filter(item => item.category === selectedFilter.value)
})

const getCountByFilter = (filterVal: string) => {
  if (filterVal === 'all') {
    return weeklyActivations.value.length
  }
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
  box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
  animation: pulse-ring 1.8s infinite;
}

@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(22, 163, 74, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
  }
}

.info-badge {
  border: 1px solid #E5E7EB;
}

.filter-chip {
  cursor: pointer;
  transition: all 0.2s ease;
}

.count-badge {
  font-size: 0.75rem;
  background: #E5E7EB;
  color: #374151;
  padding: 1px 6px;
  border-radius: 10px;
}

.count-active {
  background: rgba(255, 255, 255, 0.3);
  color: #FFFFFF;
}

/* Card Styling */
.registration-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB !important;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.registration-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
}

.activation-type-tag {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
}

.open-status-badge {
  font-size: 0.75rem;
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

.meta-icon-box {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
  flex-shrink: 0;
}

.gform-btn {
  height: 48px !important;
  font-size: 0.95rem;
  letter-spacing: 0.02em;
  text-transform: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.collaborator-badge {
  background: #FFFBEB;
  border: 1px solid #FDE68A;
}

.collaborator-role-pill {
  background: #FEF3C7;
  color: #92400E;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 9999px;
  border: 1px solid #FCD34D;
}
</style>
