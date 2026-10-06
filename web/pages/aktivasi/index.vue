<template>
  <div class="aktivasi-index-page">
    <!-- Editorial Hero Section -->
    <section class="aktivasi-hero">
      <v-container class="hero-content">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Eyebrow Badge -->
            <div class="hero-badge-pill mb-4">
              <v-icon start size="16" color="#DC2626">mdi-compass-outline</v-icon>
              <span>JEJARING KOMUNITAS</span>
            </div>

            <!-- Main Headline -->
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              <span class="d-block">Temukan Aktivasi</span>
              <span class="d-block text-primary-red">&amp; Ruang Bergerak</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Jelajahi inisiatif jalan kaki, jejaring komunitas lokal, dan ruang kreatif di berbagai sudut kota bersama Jalan Bareng.
            </p>

            <!-- Category Filter Chips (Mirrors Bottom Sheet Style) -->
            <div class="category-filters-wrapper">
              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === '' }]"
                @click="selectedCategory = ''"
              >
                <v-icon start size="16">mdi-view-grid-outline</v-icon>
                <span>Semua</span>
                <span class="chip-count">{{ activeTotal }}</span>
              </button>

              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'city' }]"
                @click="selectedCategory = 'city'"
              >
                <v-icon start size="16">mdi-city-variant-outline</v-icon>
                <span>Kota</span>
                <span class="chip-count">{{ cityActivations.length }}</span>
              </button>

              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'theme' }]"
                @click="selectedCategory = 'theme'"
              >
                <v-icon start size="16">mdi-palette-outline</v-icon>
                <span>Tematik</span>
                <span class="chip-count">{{ themeActivations.length }}</span>
              </button>

              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'space' }]"
                @click="selectedCategory = 'space'"
              >
                <v-icon start size="16">mdi-storefront-outline</v-icon>
                <span>Ruang Komunal</span>
                <span class="chip-count">{{ spaceActivations.length }}</span>
              </button>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Content Container -->
    <v-container class="py-6 py-md-10">
      <!-- Active Filter Banner (When a specific category is chosen) -->
      <div v-if="selectedCategory" class="filter-status-banner mb-8 d-flex align-center justify-space-between flex-wrap ga-3">
        <div class="d-flex align-center ga-2">
          <span class="text-body-2 font-weight-medium text-grey-darken-2">Menyaring berdasarkan:</span>
          <span class="filter-status-pill">
            {{ getCategoryTitle(selectedCategory) }} ({{ getCategoryList(selectedCategory).length }})
          </span>
        </div>
        <button type="button" class="btn-reset-filter" @click="selectedCategory = ''">
          <v-icon size="14" class="mr-1">mdi-close-circle-outline</v-icon>
          Tampilkan Semua Bagian
        </button>
      </div>

      <!-- Featured Activations (Shown on 'Semua' view) -->
      <section v-if="selectedCategory === '' && featuredActivations.length > 0" class="mb-14">
        <div class="section-header-row mb-6">
          <div>
            <div class="section-eyebrow d-inline-flex align-center ga-2 mb-2">
              <v-icon size="14" color="#DC2626">mdi-fire</v-icon>
              <span>SOROTAN KOMUNITAS</span>
            </div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-2">Aktivasi Pilihan</h2>
            <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-0">Program yang aktif bergerak setiap pekan</p>
          </div>
        </div>

        <v-row>
          <v-col
            v-for="activation in featuredActivations"
            :key="activation.id"
            cols="12"
            md="6"
          >
            <v-card
              :to="`/aktivasi/${activation.slug}`"
              elevation="0"
              class="featured-card h-100"
            >
              <v-row no-gutters class="h-100">
                <v-col cols="12" sm="5" class="featured-img-col">
                  <div class="featured-img-box">
                    <img
                      v-if="activation.hero_image"
                      :src="getImageUrl(activation.hero_image)"
                      :alt="activation.name"
                      class="featured-img"
                      loading="lazy"
                    />
                    <div v-else class="img-placeholder">
                      <v-icon size="48" color="grey-lighten-1">mdi-image-outline</v-icon>
                    </div>
                    <span class="badge-tag featured-badge">
                      <v-icon start size="12">mdi-star</v-icon>
                      Unggulan
                    </span>
                  </div>
                </v-col>
                <v-col cols="12" sm="7" class="d-flex flex-column">
                  <div class="pa-4 pa-sm-6 d-flex flex-column h-100">
                    <div class="d-flex align-center justify-space-between mb-2">
                      <span class="category-pill">
                        {{ getCategoryLabel(activation.category) }}
                      </span>
                      <span v-if="activation.city" class="city-indicator">
                        <v-icon size="13" class="mr-1 text-grey">mdi-map-marker-outline</v-icon>
                        {{ activation.city }}
                      </span>
                    </div>

                    <h3 class="featured-card-heading font-weight-bold text-grey-darken-4 mb-1">
                      {{ activation.name }}
                    </h3>

                    <p v-if="activation.tagline" class="card-desc text-caption text-grey-darken-1 mb-3">
                      {{ activation.tagline }}
                    </p>

                    <div class="mt-auto pt-3 border-top d-flex align-center justify-space-between">
                      <span class="event-count-text">
                        <v-icon size="14" class="mr-1 text-grey-darken-1">mdi-calendar-outline</v-icon>
                        {{ activation.events_count || 0 }} Agenda
                      </span>
                      <span class="action-link-text">
                        Lihat Profil
                        <v-icon size="14" class="ml-0.5">mdi-arrow-right</v-icon>
                      </span>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
      </section>

      <!-- Loading Skeletons -->
      <v-row v-if="loading" class="mb-12">
        <v-col v-for="i in 6" :key="i" cols="12" sm="6" md="4">
          <v-skeleton-loader type="image, article" class="rounded-xl border" />
        </v-col>
      </v-row>

      <div v-if="!loading">
        <!-- 1. BAGIAN AKTIVASI KOTA -->
        <section
          v-if="(selectedCategory === '' || selectedCategory === 'city') && cityActivations.length > 0"
          id="section-kota"
          class="activation-section mb-14"
        >
          <div class="section-header-box mb-6">
            <div class="d-flex align-center justify-space-between flex-wrap ga-3">
              <div>
                <div class="section-eyebrow d-inline-flex align-center ga-2 mb-2">
                  <v-icon size="15" color="#DC2626">mdi-city-variant-outline</v-icon>
                  <span>JEJARING REGIONAL</span>
                </div>
                <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">Aktivasi Kota</h2>
                <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-0">
                  Chapter pejalan kaki di berbagai kota yang rutin berkumpul dan mengeksplorasi sudut kota bersama warga
                </p>
              </div>
              <div class="section-badge-counter">
                <v-icon size="16" class="mr-1">mdi-map-marker-multiple-outline</v-icon>
                <span>{{ cityActivations.length }} Chapter Kota</span>
              </div>
            </div>
          </div>

          <v-row dense>
            <v-col
              v-for="activation in cityActivations"
              :key="activation.id"
              cols="12"
              sm="6"
              md="4"
            >
              <ActivationCard :activation="activation" />
            </v-col>
          </v-row>
        </section>

        <!-- 2. BAGIAN AKTIVASI TEMATIK -->
        <section
          v-if="(selectedCategory === '' || selectedCategory === 'theme') && themeActivations.length > 0"
          id="section-tematik"
          class="activation-section mb-14"
        >
          <div class="section-header-box mb-6">
            <div class="d-flex align-center justify-space-between flex-wrap ga-3">
              <div>
                <div class="section-eyebrow d-inline-flex align-center ga-2 mb-2">
                  <v-icon size="15" color="#DC2626">mdi-palette-outline</v-icon>
                  <span>INISIATIF & MINAT</span>
                </div>
                <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">Aktivasi Tematik</h2>
                <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-0">
                  Eksplorasi jalan kaki dengan fokus minat khusus: literasi buku, denyut kuliner rempah, kerelawanan, dan kriya tangan
                </p>
              </div>
              <div class="section-badge-counter">
                <v-icon size="16" class="mr-1">mdi-lightbulb-on-outline</v-icon>
                <span>{{ themeActivations.length }} Inisiatif Tematik</span>
              </div>
            </div>
          </div>

          <v-row dense>
            <v-col
              v-for="activation in themeActivations"
              :key="activation.id"
              cols="12"
              sm="6"
              md="4"
            >
              <ActivationCard :activation="activation" />
            </v-col>
          </v-row>
        </section>

        <!-- 3. BAGIAN RUANG KOMUNAL -->
        <section
          v-if="(selectedCategory === '' || selectedCategory === 'space') && spaceActivations.length > 0"
          id="section-ruang-komunal"
          class="activation-section mb-14"
        >
          <div class="section-header-box mb-6">
            <div class="d-flex align-center justify-space-between flex-wrap ga-3">
              <div>
                <div class="section-eyebrow d-inline-flex align-center ga-2 mb-2">
                  <v-icon size="15" color="#DC2626">mdi-storefront-outline</v-icon>
                  <span>SIMPUL & RUANG FISIK</span>
                </div>
                <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">Ruang Komunal & Kreatif</h2>
                <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-0">
                  Titik temu fisik, studio workshop kriya, sudut baca warga, dan rumah inkubasi kolaborasi komunitas pejalan
                </p>
              </div>
              <div class="section-badge-counter">
                <v-icon size="16" class="mr-1">mdi-home-heart</v-icon>
                <span>{{ spaceActivations.length }} Ruang Komunal</span>
              </div>
            </div>
          </div>

          <v-row dense>
            <v-col
              v-for="activation in spaceActivations"
              :key="activation.id"
              cols="12"
              sm="6"
              md="4"
            >
              <ActivationCard :activation="activation" />
            </v-col>
          </v-row>
        </section>

        <!-- Empty State jika kategori kosong -->
        <div v-if="filteredTotal === 0" class="empty-state-box text-center py-16">
          <v-icon size="56" color="grey-lighten-1" class="mb-3">mdi-compass-off-outline</v-icon>
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">Belum ada aktivasi untuk bagian ini</h3>
          <p class="text-body-2 text-grey-darken-1 mx-auto mb-4" style="max-width: 420px;">
            Inisiatif komunitas untuk bagian ini sedang dipersiapkan. Anda dapat menjelajahi seluruh aktivasi yang sudah aktif.
          </p>
          <button type="button" class="btn-primary-pill" @click="selectedCategory = ''">
            Tampilkan Semua Aktivasi
          </button>
        </div>
      </div>

      <!-- Kolaborasi Section -->
      <section class="kolaborasi-section mt-8">
        <div class="kolaborasi-card pa-8 pa-md-12">
          <v-row align="center">
            <v-col cols="12" md="8">
              <div class="d-inline-flex align-center ga-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(220, 38, 38, 0.08); color: #DC2626; font-size: 0.78rem; font-weight: 700;">
                <v-icon size="14" color="#DC2626">mdi-handshake-outline</v-icon>
                <span>INISIASI BERSAMA</span>
              </div>
              <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-3">
                Ingin Menginisiasi Aktivasi di Kotamu?
              </h2>
              <p class="text-body-1 text-grey-darken-2 mb-0" style="max-width: 620px; line-height: 1.6;">
                Jalan Bareng terbuka bagi inisiator lokal, penggiat jalan kaki, dan ruang kreatif yang ingin berjejaring membangun ruang interaksi yang hangat dan inklusif.
              </p>
            </v-col>
            <v-col cols="12" md="4" class="text-md-right mt-4 mt-md-0">
              <a
                href="mailto:admin@jalanbareng.id"
                class="btn-primary-pill d-inline-flex align-center text-decoration-none"
              >
                <v-icon start size="18">mdi-email-outline</v-icon>
                Hubungi Kolaborasi
              </a>
            </v-col>
          </v-row>
        </div>
      </section>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useSeoMeta, definePageMeta } from '#imports'
import ActivationCard from '~/components/activations/ActivationCard.vue'

definePageMeta({
  layout: 'default'
})

const { api } = useApi()
const { getImageUrl } = useImageUrl()
const config = useRuntimeConfig()

const selectedCategory = ref('')
const loading = ref(true)
const activations = ref<any[]>([])

const fetchActivations = async () => {
  loading.value = true
  try {
    const response = await api.get('/activations')
    const data = response.data?.data || response.data
    if (Array.isArray(data)) {
      activations.value = data
    }
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    loading.value = false
  }
}

// Computeds for separation
const activeActivations = computed(() => activations.value.filter(a => a.is_active))
const activeTotal = computed(() => activeActivations.value.length)

const cityActivations = computed(() =>
  activeActivations.value.filter(a => a.category === 'city')
)

const themeActivations = computed(() =>
  activeActivations.value.filter(a => a.category === 'theme')
)

const spaceActivations = computed(() =>
  activeActivations.value.filter(a => a.category === 'space')
)

const featuredActivations = computed(() =>
  activeActivations.value.filter(a => a.is_featured).slice(0, 2)
)

const filteredTotal = computed(() => {
  if (selectedCategory.value === 'city') return cityActivations.value.length
  if (selectedCategory.value === 'theme') return themeActivations.value.length
  if (selectedCategory.value === 'space') return spaceActivations.value.length
  return activeTotal.value
})

const getCategoryList = (cat: string) => {
  if (cat === 'city') return cityActivations.value
  if (cat === 'theme') return themeActivations.value
  if (cat === 'space') return spaceActivations.value
  return activeActivations.value
}

const getCategoryTitle = (cat: string) => {
  const titles: Record<string, string> = {
    city: 'Aktivasi Kota',
    theme: 'Aktivasi Tematik',
    space: 'Ruang Komunal'
  }
  return titles[cat] || 'Semua Aktivasi'
}

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Aktivasi Kota',
    theme: 'Tematik',
    space: 'Ruang Komunal',
    other: 'Komunitas'
  }
  return labels[category] || 'Komunitas'
}

onMounted(() => {
  fetchActivations()
})

useSeoMeta({
  title: 'Aktivasi Komunitas - Jalan Bareng',
  ogTitle: 'Aktivasi Komunitas - Jalan Bareng',
  description: 'Temukan berbagai program aktivasi, chapter kota, gerakan tematik, dan ruang komunal Jalan Bareng di seluruh Indonesia.',
  ogDescription: 'Temukan berbagai program aktivasi, chapter kota, gerakan tematik, dan ruang komunal Jalan Bareng di seluruh Indonesia.',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
.aktivasi-index-page {
  background-color: #FFFFFF;
  min-height: 100vh;
}

/* Editorial Hero Section */
.aktivasi-hero {
  background: #FAFAFA;
  border-bottom: 1px solid #F1F5F9;
  padding: 56px 0 48px;
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

/* Filter Buttons (Mirrors Bottom Sheet Style) */
.category-filters-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 8px;
}

.filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 16px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  color: #475569;
  font-size: 0.86rem;
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
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.28);
}

.chip-count {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 1px 7px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  background: #F1F5F9;
  color: #475569;
}

.filter-btn.active .chip-count {
  background: rgba(255, 255, 255, 0.25);
  color: #FFFFFF;
}

/* Filter Status Banner */
.filter-status-banner {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  padding: 12px 18px;
  border-radius: 14px;
}

.filter-status-pill {
  background: #DC2626;
  color: #FFFFFF;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 3px 10px;
  border-radius: 9999px;
}

.btn-reset-filter {
  display: inline-flex;
  align-items: center;
  background: transparent;
  border: none;
  color: #64748B;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: all 0.15s ease;
}

.btn-reset-filter:hover {
  color: #DC2626;
  background: #FEF2F2;
}

/* Section Header Box */
.section-header-box {
  padding-bottom: 8px;
  border-bottom: 2px solid #F1F5F9;
}

.section-eyebrow {
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.06em;
  color: #DC2626;
  text-transform: uppercase;
}

.section-badge-counter {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 9999px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  font-size: 0.82rem;
  font-weight: 700;
  color: #475569;
}

/* Featured Activation Card */
.featured-card {
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  text-decoration: none;
}

.featured-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
  border-color: #CBD5E1;
}

.featured-img-col {
  min-height: 220px;
}

.featured-img-box {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 220px;
  overflow: hidden;
  background: #F3F4F6;
}

.featured-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.featured-card:hover .featured-img {
  transform: scale(1.05);
}

.featured-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  background: #DC2626;
  color: #FFFFFF;
  font-size: 0.72rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 9999px;
  display: inline-flex;
  align-items: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.category-pill {
  font-size: 0.72rem;
  font-weight: 700;
  color: #DC2626;
  background: #FEF2F2;
  padding: 2px 8px;
  border-radius: 9999px;
}

.city-indicator {
  display: inline-flex;
  align-items: center;
  font-size: 0.78rem;
  font-weight: 600;
  color: #4B5563;
}

.featured-card-heading {
  font-size: 1.25rem;
  line-height: 1.3;
}

.card-desc {
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-count-text {
  font-size: 0.78rem;
  font-weight: 600;
  color: #4B5563;
  display: inline-flex;
  align-items: center;
}

.action-link-text {
  display: inline-flex;
  align-items: center;
  font-size: 0.82rem;
  font-weight: 700;
  color: #DC2626;
}

.btn-primary-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 28px;
  border-radius: 9999px;
  background: #DC2626;
  color: #FFFFFF;
  font-weight: 700;
  font-size: 0.95rem;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);
}

.btn-primary-pill:hover {
  background: #B91C1C;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(220, 38, 38, 0.45);
}

/* Kolaborasi Card */
.kolaborasi-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 24px;
  position: relative;
  overflow: hidden;
}

.kolaborasi-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #DC2626, #F87171);
}

.empty-state-box {
  background: #F9FAFB;
  border: 1px dashed #E5E7EB;
  border-radius: 20px;
}

.img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  background: #F3F4F6;
}
</style>
