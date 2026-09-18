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
              Temukan Aktivasi &amp;
              <span class="text-primary-red">Ruang Bergerak</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Jelajahi inisiatif jalan kaki, jejaring komunitas lokal, dan ruang kreatif di berbagai sudut kota bersama Jalan Bareng.
            </p>

            <!-- Category Filter Chips -->
            <div class="category-filters-wrapper d-flex align-center justify-center flex-wrap ga-2">
              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === '' }]"
                @click="selectedCategory = ''"
              >
                <v-icon start size="18">mdi-view-grid-outline</v-icon>
                Semua Aktivasi
              </button>
              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'city' }]"
                @click="selectedCategory = 'city'"
              >
                <v-icon start size="18">mdi-city-variant-outline</v-icon>
                Aktivasi Kota
              </button>
              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'theme' }]"
                @click="selectedCategory = 'theme'"
              >
                <v-icon start size="18">mdi-palette-outline</v-icon>
                Aktivasi Tematik
              </button>
              <button
                type="button"
                :class="['filter-btn', { active: selectedCategory === 'space' }]"
                @click="selectedCategory = 'space'"
              >
                <v-icon start size="18">mdi-storefront-outline</v-icon>
                Ruang Kreatif
              </button>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Content Container -->
    <v-container class="py-6 py-md-10">
      <!-- Featured Activations (When 'Semua' is active) -->
      <section v-if="featuredActivations.length > 0" class="mb-14">
        <div class="section-header-row mb-6">
          <div>
            <div class="d-inline-flex align-center ga-1 text-caption font-weight-bold text-primary-red text-uppercase tracking-wider mb-1">
              <v-icon size="14" color="#DC2626">mdi-fire</v-icon>
              <span>Sorotan Komunitas</span>
            </div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4">Aktivasi Pilihan</h2>
          </div>
          <span class="text-caption text-grey-darken-1">Program yang aktif bergerak setiap pekan</span>
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
                      :src="`${apiBase}/storage/${activation.hero_image}`"
                      :alt="activation.name"
                      class="featured-img"
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
                  <div class="pa-6 d-flex flex-column h-100">
                    <div class="d-flex align-center justify-space-between mb-2">
                      <span class="category-pill">
                        {{ getCategoryLabel(activation.category) }}
                      </span>
                      <span v-if="activation.city" class="city-indicator">
                        <v-icon size="14" class="mr-1 text-grey">mdi-map-marker-outline</v-icon>
                        {{ activation.city }}
                      </span>
                    </div>

                    <h3 class="card-title text-h5 font-weight-bold text-grey-darken-4 mb-2">
                      {{ activation.name }}
                    </h3>

                    <p v-if="activation.tagline" class="card-desc text-body-2 text-grey-darken-1 mb-4">
                      {{ activation.tagline }}
                    </p>

                    <div class="mt-auto pt-3 border-top d-flex align-center justify-space-between">
                      <span class="event-count-text">
                        <v-icon size="16" class="mr-1 text-grey-darken-1">mdi-calendar-outline</v-icon>
                        {{ activation.events_count || 0 }} Agenda Jalan
                      </span>
                      <span class="action-link-text">
                        Lihat Profil
                        <v-icon size="16" class="ml-1">mdi-arrow-right</v-icon>
                      </span>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </v-card>
          </v-col>
        </v-row>
      </section>

      <!-- All Activations Grid -->
      <section class="mb-14">
        <div class="d-flex align-center justify-space-between flex-wrap ga-2 mb-6">
          <div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">{{ categoryTitle }}</h2>
            <p class="text-body-2 text-grey-darken-1 mb-0">
              Menampilkan {{ filteredActivations.length }} komunitas dan aktivasi jalan kaki
            </p>
          </div>
        </div>

        <!-- Grid Cards -->
        <v-row v-if="!loading && filteredActivations.length > 0">
          <v-col
            v-for="activation in filteredActivations"
            :key="activation.id"
            cols="12"
            sm="6"
            md="4"
          >
            <v-card
              :to="`/aktivasi/${activation.slug}`"
              elevation="0"
              class="activation-card h-100 d-flex flex-column"
            >
              <!-- Card Image Header -->
              <div class="card-header-img-wrapper">
                <img
                  v-if="activation.hero_image"
                  :src="`${apiBase}/storage/${activation.hero_image}`"
                  :alt="activation.name"
                  class="card-top-img"
                />
                <div v-else class="img-placeholder h-100">
                  <v-icon size="48" color="grey-lighten-1">mdi-image-outline</v-icon>
                </div>

                <div class="card-tag-group">
                  <span class="category-pill-solid">
                    {{ getCategoryLabel(activation.category) }}
                  </span>
                </div>

                <!-- Chapter Icon Avatar -->
                <div v-if="activation.icon" class="activation-avatar-badge">
                  <img :src="`${apiBase}/storage/${activation.icon}`" :alt="activation.name" />
                </div>
              </div>

              <!-- Card Content -->
              <div class="pa-6 d-flex flex-column flex-grow-1">
                <div class="d-flex align-center justify-space-between mb-2">
                  <span v-if="activation.city" class="city-indicator">
                    <v-icon size="14" class="mr-1 text-grey">mdi-map-marker-outline</v-icon>
                    {{ activation.city }}
                  </span>
                  <span class="event-count-chip">
                    {{ activation.events_count || 0 }} Event
                  </span>
                </div>

                <h3 class="card-title text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  {{ activation.name }}
                </h3>

                <p v-if="activation.tagline" class="card-desc text-body-2 text-grey-darken-1 mb-4 flex-grow-1">
                  {{ activation.tagline }}
                </p>
                <div v-else class="flex-grow-1"></div>

                <div class="pt-3 border-top d-flex align-center justify-space-between mt-auto">
                  <span class="text-caption text-grey-darken-1 font-weight-medium">
                    Jalan Bareng Chapter
                  </span>
                  <span class="action-link-text">
                    Eksplor
                    <v-icon size="14" class="ml-1">mdi-arrow-right</v-icon>
                  </span>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Empty State -->
        <div v-if="!loading && filteredActivations.length === 0" class="empty-state-box text-center py-16">
          <v-icon size="56" color="grey-lighten-1" class="mb-3">mdi-compass-off-outline</v-icon>
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-2">Belum ada aktivasi untuk kategori ini</h3>
          <p class="text-body-2 text-grey-darken-1 mx-auto mb-4" style="max-width: 420px;">
            Komunitas untuk kategori ini sedang dipersiapkan. Anda dapat menjelajahi seluruh aktivasi yang sudah aktif.
          </p>
          <button type="button" class="btn-primary-pill" @click="selectedCategory = ''">
            Tampilkan Semua Aktivasi
          </button>
        </div>

        <!-- Loading Skeletons -->
        <v-row v-if="loading">
          <v-col v-for="i in 6" :key="i" cols="12" sm="6" md="4">
            <v-skeleton-loader type="image, article" class="rounded-xl border" />
          </v-col>
        </v-row>
      </section>

      <!-- Collaborative Invitation Section (Antislop: Honest & Editorial) -->
      <section class="kolaborasi-section mt-12">
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
import { ref, computed, watch, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useSeoMeta, definePageMeta } from '#imports'

definePageMeta({
  layout: 'default'
})

const { api } = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const selectedCategory = ref('')
const activations = ref<any[]>([])
const loading = ref(true)

// Fetch activations
const fetchActivations = async () => {
  loading.value = true
  try {
    const params: any = {}
    if (selectedCategory.value) {
      params.category = selectedCategory.value
    }

    const response = await api.get('/activations', { params })
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    loading.value = false
  }
}

// Computed
const featuredActivations = computed(() => {
  if (selectedCategory.value !== '') return []
  return activations.value.filter(a => a.is_featured && a.is_active).slice(0, 2)
})

const filteredActivations = computed(() => {
  return activations.value.filter(a => a.is_active)
})

const categoryTitle = computed(() => {
  const titles: Record<string, string> = {
    '': 'Semua Komunitas & Aktivasi',
    'city': 'Aktivasi Kota',
    'theme': 'Aktivasi Tematik',
    'space': 'Ruang Kreatif'
  }
  return titles[selectedCategory.value] || 'Semua Aktivasi'
})

// Helper functions
const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Aktivasi Kota',
    theme: 'Tematik',
    space: 'Ruang Kreatif',
    other: 'Komunitas'
  }
  return labels[category] || 'Komunitas'
}

// Watch category changes
watch(selectedCategory, () => {
  fetchActivations()
})

// Initial fetch
onMounted(() => {
  fetchActivations()
})

// SEO
useSeoMeta({
  title: 'Aktivasi Komunitas - Jalan Bareng',
  ogTitle: 'Aktivasi Komunitas - Jalan Bareng',
  description: 'Temukan berbagai program aktivasi, chapter kota, dan ruang kreatif Jalan Bareng di seluruh Indonesia.',
  ogDescription: 'Temukan berbagai program aktivasi, chapter kota, dan ruang kreatif Jalan Bareng di seluruh Indonesia.',
  ogImage: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&h=630&fit=crop',
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

/* Filter Buttons */
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

/* Activation Card */
.activation-card {
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  text-decoration: none;
}

.activation-card:hover {
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

.activation-card:hover .card-top-img {
  transform: scale(1.05);
}

.card-tag-group {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 2;
}

.category-pill {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 9999px;
  background: #F3F4F6;
  color: #374151;
  font-size: 0.75rem;
  font-weight: 600;
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

.city-indicator {
  display: inline-flex;
  align-items: center;
  font-size: 0.78rem;
  color: #6B7280;
  font-weight: 500;
}

.event-count-chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 6px;
  background: #FEF2F2;
  color: #DC2626;
  font-size: 0.72rem;
  font-weight: 700;
}

.activation-avatar-badge {
  position: absolute;
  bottom: 12px;
  right: 14px;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: 2.5px solid #FFFFFF;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  background: #FFFFFF;
  z-index: 3;
}

.activation-avatar-badge img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.action-link-text {
  display: inline-flex;
  align-items: center;
  color: #DC2626;
  font-weight: 700;
  font-size: 0.85rem;
  transition: transform 0.2s ease;
}

.featured-card:hover .action-link-text,
.activation-card:hover .action-link-text {
  transform: translateX(3px);
}

.event-count-text {
  display: inline-flex;
  align-items: center;
  font-size: 0.82rem;
  color: #6B7280;
}

/* Image Placeholder */
.img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
  width: 100%;
}

/* Kolaborasi Section */
.kolaborasi-card {
  border-radius: 24px;
  border: 1px solid #E2E8F0;
  background: #FAFAFA;
}

.btn-primary-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 24px;
  border-radius: 9999px;
  background: #DC2626;
  color: #FFFFFF;
  font-weight: 700;
  font-size: 0.92rem;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.15s ease;
}

.btn-primary-pill:hover {
  background: #B91C1C;
  transform: translateY(-1px);
}

/* Empty State */
.empty-state-box {
  background: #FAFAFA;
  border: 1px dashed #CBD5E1;
  border-radius: 20px;
}

/* Mobile Responsiveness */
@media (max-width: 600px) {
  .aktivasi-hero {
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

  .featured-img-col {
    min-height: 180px;
  }

  .card-header-img-wrapper {
    height: 170px;
  }
}
</style>
