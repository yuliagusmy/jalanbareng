<template>
  <section class="featured-landmarks-section py-6 py-md-8">
    <v-container>
      <!-- Section Header -->
      <div class="d-flex flex-column flex-sm-row align-sm-end justify-space-between mb-5 ga-4">
        <div>
          <div class="landmarks-eyebrow d-inline-flex align-center mb-2">
            <v-icon size="14" color="#DC2626" class="mr-1.5">mdi-compass-outline</v-icon>
            <span>IKON KOTA &amp; TITIK KUMPUL</span>
          </div>
          <h2 class="landmarks-heading text-grey-darken-4 font-weight-black mb-1">
            Landmark Ikonik Pejalan Kaki
          </h2>
          <p class="landmarks-subheading text-grey-darken-1 mb-0">
            Titik kumpul dan ruang publik bersejarah yang ramah jalur pedestrian di Makassar. Ketuk kartu landmark untuk melihat panduan rute dan detail lengkap.
          </p>
        </div>

        <!-- Navigation Arrows for Carousel -->
        <div class="d-none d-sm-flex align-center ga-2 flex-shrink-0">
          <button
            type="button"
            class="nav-scroll-btn"
            @click="scrollTrack('left')"
            aria-label="Geser ke kiri"
          >
            <v-icon size="20">mdi-chevron-left</v-icon>
          </button>
          <button
            type="button"
            class="nav-scroll-btn"
            @click="scrollTrack('right')"
            aria-label="Geser ke kanan"
          >
            <v-icon size="20">mdi-chevron-right</v-icon>
          </button>
        </div>
      </div>

      <!-- Horizontal Scrollable Track -->
      <div
        ref="scrollContainer"
        class="landmarks-scroll-track"
      >
        <div
          v-for="landmark in LANDMARKS_DATA"
          :key="landmark.id"
          class="landmark-card-item"
          @click="openLandmarkModal(landmark)"
        >
          <div class="landmark-card-inner">
            <!-- Card Image Thumbnail -->
            <div class="landmark-img-box">
              <img
                :src="landmark.image"
                :alt="landmark.name"
                class="landmark-img"
                loading="lazy"
              />
              <div class="landmark-img-overlay"></div>

              <!-- Category Badge -->
              <div class="landmark-category-pill">
                <v-icon size="12" class="mr-1">{{ landmark.categoryIcon }}</v-icon>
                <span>{{ landmark.category }}</span>
              </div>

              <!-- Distance / Walk Spec Badge -->
              <div class="landmark-walk-spec">
                <v-icon size="13" color="#10B981" class="mr-1">mdi-walk</v-icon>
                <span>{{ landmark.walkDistance }}</span>
              </div>
            </div>

            <!-- Card Body Content -->
            <div class="landmark-card-body pa-4">
              <div class="d-flex align-center text-caption text-grey-darken-1 mb-1 font-weight-bold">
                <v-icon size="14" color="#DC2626" class="mr-1">mdi-map-marker</v-icon>
                <span>{{ landmark.city }}</span>
                <span class="mx-1.5">•</span>
                <span class="text-emerald-700">{{ landmark.suitability }}</span>
              </div>

              <h3 class="landmark-title text-grey-darken-4 font-weight-black mb-1.5">
                {{ landmark.name }}
              </h3>

              <p class="landmark-snippet text-grey-darken-2 mb-3">
                {{ landmark.shortDesc }}
              </p>

              <!-- Footer Action -->
              <div class="landmark-card-footer pt-2.5 border-top-subtle d-flex align-center justify-space-between">
                <span class="text-caption font-weight-bold text-primary-red d-flex align-center">
                  <span>Lihat Detail Rute</span>
                  <v-icon size="14" class="ml-1">mdi-arrow-right</v-icon>
                </span>
                <span class="text-caption text-grey font-weight-medium">
                  {{ landmark.bestTime }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </v-container>

    <!-- Detail Landmark Modal Dialog Component -->
    <LandmarkDetailModal
      v-model="modalOpen"
      :landmark="selectedLandmark"
      @filter-landmark="onFilterLandmark"
    />
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { LANDMARKS_DATA, type LandmarkItem } from '~/utils/landmarks'
import LandmarkDetailModal from './LandmarkDetailModal.vue'

const emit = defineEmits<{
  (e: 'select-landmark', landmarkName: string): void
}>()

const scrollContainer = ref<HTMLElement | null>(null)
const modalOpen = ref(false)
const selectedLandmark = ref<LandmarkItem | null>(null)

const scrollTrack = (direction: 'left' | 'right') => {
  if (!scrollContainer.value) return
  const scrollAmount = 340
  scrollContainer.value.scrollBy({
    left: direction === 'left' ? -scrollAmount : scrollAmount,
    behavior: 'smooth'
  })
}

const openLandmarkModal = (landmark: LandmarkItem) => {
  selectedLandmark.value = landmark
  modalOpen.value = true
}

const onFilterLandmark = (landmarkName: string) => {
  emit('select-landmark', landmarkName)
}
</script>

<style scoped>
.featured-landmarks-section {
  background: #FFFFFF;
  border-bottom: 1px solid #E2E8F0;
}

.landmarks-eyebrow {
  padding: 4px 12px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  border-radius: 9999px;
  color: #DC2626;
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.landmarks-heading {
  font-size: clamp(1.35rem, 2.2vw, 1.8rem);
  letter-spacing: -0.025em;
  line-height: 1.25;
}

.landmarks-subheading {
  font-size: 0.92rem;
  max-width: 640px;
  line-height: 1.5;
}

.nav-scroll-btn {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  color: #334155;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.nav-scroll-btn:hover {
  background: #FEF2F2;
  border-color: #FCA5A5;
  color: #DC2626;
  transform: translateY(-1px);
}

/* Horizontal Track */
.landmarks-scroll-track {
  display: flex;
  gap: 18px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding-bottom: 14px;
  padding-top: 4px;
  -webkit-overflow-scrolling: touch;
}

.landmarks-scroll-track::-webkit-scrollbar {
  height: 6px;
}

.landmarks-scroll-track::-webkit-scrollbar-track {
  background: #F1F5F9;
  border-radius: 10px;
}

.landmarks-scroll-track::-webkit-scrollbar-thumb {
  background: #CBD5E1;
  border-radius: 10px;
}

.landmarks-scroll-track::-webkit-scrollbar-thumb:hover {
  background: #94A3B8;
}

/* Landmark Item Card */
.landmark-card-item {
  flex: 0 0 290px;
  max-width: 290px;
  cursor: pointer;
  user-select: none;
}

.landmark-card-inner {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  overflow: hidden;
  height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.05);
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease, border-color 0.25s ease;
}

.landmark-card-item:hover .landmark-card-inner {
  transform: translateY(-5px);
  border-color: #FCA5A5;
  box-shadow: 0 16px 30px -8px rgba(220, 38, 38, 0.14);
}

.landmark-img-box {
  position: relative;
  width: 100%;
  height: 175px;
  overflow: hidden;
  background: #111827;
}

.landmark-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}

.landmark-card-item:hover .landmark-img {
  transform: scale(1.06);
}

.landmark-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.15) 0%, rgba(0, 0, 0, 0.65) 100%);
}

.landmark-category-pill {
  position: absolute;
  top: 10px;
  left: 10px;
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(4px);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.72rem;
  font-weight: 700;
}

.landmark-walk-spec {
  position: absolute;
  bottom: 10px;
  left: 10px;
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 9999px;
  color: #065F46;
  font-size: 0.74rem;
  font-weight: 800;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.landmark-card-body {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.landmark-title {
  font-size: 1.05rem;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.landmark-snippet {
  font-size: 0.84rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex-grow: 1;
}

.text-primary-red {
  color: #DC2626;
}

.border-top-subtle {
  border-top: 1px solid #F1F5F9;
}

@media (max-width: 599.98px) {
  .landmark-card-item {
    flex: 0 0 250px;
    max-width: 250px;
  }
}
</style>
