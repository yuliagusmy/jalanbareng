<template>
  <v-dialog
    :model-value="modelValue"
    max-width="680"
    scrollable
    transition="dialog-bottom-transition"
    @update:model-value="(val) => emit('update:modelValue', val)"
  >
    <v-card v-if="landmark" rounded="xl" class="landmark-detail-modal">
      <!-- Banner Image & Close Button -->
      <div class="modal-banner-container">
        <img
          :src="landmark.image"
          :alt="landmark.name"
          class="modal-banner-img"
        />
        <div class="modal-banner-overlay"></div>

        <!-- Close Action -->
        <button
          type="button"
          class="modal-close-btn"
          @click="emit('update:modelValue', false)"
          aria-label="Tutup Detail Landmark"
        >
          <v-icon size="20" color="white">mdi-close</v-icon>
        </button>

        <!-- Banner Title Block -->
        <div class="modal-banner-caption pa-5 pa-md-6">
          <div class="d-flex align-center ga-2 mb-2 flex-wrap">
            <span class="modal-badge-chip">
              <v-icon size="12" class="mr-1">{{ landmark.categoryIcon }}</v-icon>
              {{ landmark.category }}
            </span>
            <span class="modal-badge-chip modal-badge-location">
              <v-icon size="12" class="mr-1">mdi-map-marker</v-icon>
              {{ landmark.city }}
            </span>
          </div>
          <h2 class="text-h5 text-md-h4 font-weight-black text-white mb-1">
            {{ landmark.name }}
          </h2>
          <p class="text-white text-caption text-md-body-2 mb-0" style="opacity: 0.92;">
            {{ landmark.tagline }}
          </p>
        </div>
      </div>

      <!-- Modal Body Content -->
      <v-card-text class="pa-5 pa-md-6 modal-scroll-body">
        <!-- Quick Highlight Specs -->
        <div class="quick-specs-grid mb-6">
          <div class="spec-box">
            <v-icon size="20" color="#DC2626" class="mb-1">mdi-map-marker-distance</v-icon>
            <span class="spec-label">Jarak / Langkah</span>
            <span class="spec-val">{{ landmark.walkDistance }}</span>
          </div>
          <div class="spec-box">
            <v-icon size="20" color="#D97706" class="mb-1">mdi-clock-time-four-outline</v-icon>
            <span class="spec-label">Waktu Terbaik</span>
            <span class="spec-val">{{ landmark.bestTime }}</span>
          </div>
          <div class="spec-box">
            <v-icon size="20" color="#16A34A" class="mb-1">mdi-shield-check-outline</v-icon>
            <span class="spec-label">Jalur Pedestrian</span>
            <span class="spec-val">{{ landmark.suitability }}</span>
          </div>
        </div>

        <!-- Section 1: Sejarah & Daya Tarik -->
        <div class="mb-5">
          <h4 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-2 d-flex align-center">
            <v-icon size="18" color="#DC2626" class="mr-1.5">mdi-book-open-page-variant-outline</v-icon>
            Daya Tarik &amp; Sejarah Ruang
          </h4>
          <p class="text-body-2 text-grey-darken-2" style="line-height: 1.7;">
            {{ landmark.historyDesc }}
          </p>
        </div>

        <!-- Section 2: Panduan Rute Jalan Santai -->
        <div class="mb-5 pa-4 rounded-xl bg-stone-50 border">
          <h4 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-2 d-flex align-center">
            <v-icon size="18" color="#DC2626" class="mr-1.5">mdi-sign-direction</v-icon>
            Panduan Rute Jalan Santai
          </h4>
          <ul class="text-body-2 text-grey-darken-2 pl-4 mb-0 route-list">
            <li class="mb-1.5"><strong>Titik Awal Mulai:</strong> {{ landmark.startPoint }}</li>
            <li class="mb-1.5"><strong>Rute Rekomendasi:</strong> {{ landmark.routeSteps }}</li>
            <li><strong>Tips Jalan Bareng:</strong> {{ landmark.communityTips }}</li>
          </ul>
        </div>

        <!-- Section 3: Fasilitas di Sekitar -->
        <div class="mb-4">
          <h4 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-2 d-flex align-center">
            <v-icon size="18" color="#DC2626" class="mr-1.5">mdi-check-circle-outline</v-icon>
            Fasilitas Terdekat bagi Pejalan
          </h4>
          <div class="d-flex flex-wrap ga-2">
            <v-chip
              v-for="(fac, fIdx) in landmark.facilities"
              :key="fIdx"
              size="small"
              variant="outlined"
              color="#4B5563"
              class="font-weight-medium"
            >
              <v-icon start size="14">mdi-check</v-icon>
              {{ fac }}
            </v-chip>
          </div>
        </div>
      </v-card-text>

      <!-- Modal Footer Actions -->
      <v-divider></v-divider>
      <v-card-actions class="pa-4 pa-md-5 d-flex justify-space-between align-center flex-wrap ga-3">
        <v-btn
          variant="text"
          color="grey-darken-2"
          class="font-weight-bold"
          @click="emit('update:modelValue', false)"
        >
          Tutup
        </v-btn>

        <div class="d-flex align-center ga-2 flex-wrap">
          <v-btn
            v-if="landmark.gmapsUrl"
            variant="outlined"
            color="#DC2626"
            rounded="pill"
            class="font-weight-bold text-caption"
            :href="landmark.gmapsUrl"
            target="_blank"
            rel="noopener noreferrer"
          >
            <v-icon start size="16">mdi-google-maps</v-icon>
            Buka Google Maps
          </v-btn>

          <v-btn
            color="#DC2626"
            rounded="pill"
            class="font-weight-bold text-white px-5"
            elevation="0"
            @click="onFilterClick"
          >
            <v-icon start size="16">mdi-magnify</v-icon>
            Cari Destinasi di Sekitar
          </v-btn>
        </div>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import type { LandmarkItem } from '~/utils/landmarks'

const props = defineProps<{
  modelValue: boolean
  landmark: LandmarkItem | null
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'filter-landmark', landmarkName: string): void
}>()

const onFilterClick = () => {
  if (props.landmark) {
    emit('filter-landmark', props.landmark.name)
    emit('update:modelValue', false)
  }
}
</script>

<style scoped>
.landmark-detail-modal {
  background: #FFFFFF;
  overflow: hidden;
}

.modal-banner-container {
  position: relative;
  width: 100%;
  height: clamp(200px, 32vh, 260px);
  overflow: hidden;
  background: #111827;
}

.modal-banner-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.modal-banner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.85) 100%);
}

.modal-close-btn {
  position: absolute;
  top: 14px;
  right: 14px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(0, 0, 0, 0.55);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 5;
  transition: background 0.2s ease;
}

.modal-close-btn:hover {
  background: rgba(220, 38, 38, 0.9);
}

.modal-banner-caption {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 4;
}

.modal-badge-chip {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  background: rgba(220, 38, 38, 0.9);
  color: #FFFFFF;
  font-size: 0.72rem;
  font-weight: 700;
}

.modal-badge-location {
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(4px);
}

.modal-scroll-body {
  max-height: 55vh;
}

.quick-specs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.spec-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 14px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.spec-label {
  font-size: 0.72rem;
  color: #64748B;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.spec-val {
  font-size: 0.84rem;
  font-weight: 800;
  color: #1E293B;
  margin-top: 2px;
}

.route-list li {
  line-height: 1.6;
}

.bg-stone-50 {
  background-color: #FAFAF9;
}

@media (max-width: 599.98px) {
  .quick-specs-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .spec-box {
    flex-direction: row;
    justify-content: space-between;
    padding: 8px 12px;
  }
}
</style>
