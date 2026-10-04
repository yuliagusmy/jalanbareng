<template>
  <div class="destination-walker-guide">
    <!-- 1. Karakter Jalur & Aksesibilitas -->
    <section class="content-block">
      <div class="d-flex align-center justify-space-between mb-4 flex-wrap ga-2">
        <h2 class="section-title mb-0">Panduan Pejalan Kaki</h2>
        <span class="guide-tag-pill">
          <v-icon size="14" class="mr-1" color="#DC2626">mdi-walk</v-icon>
          Pedestrian Experience
        </span>
      </div>

      <!-- Feature Grid -->
      <div class="walker-features-grid">
        <div class="walker-feature-card">
          <div class="feature-icon-box bg-red-light">
            <v-icon size="20" color="#DC2626">mdi-road-variant</v-icon>
          </div>
          <div>
            <div class="feature-title">Kualitas Jalur</div>
            <div class="feature-desc">{{ getWalkabilityText }}</div>
          </div>
        </div>

        <div class="walker-feature-card">
          <div class="feature-icon-box bg-green-light">
            <v-icon size="20" color="#16A34A">mdi-tree</v-icon>
          </div>
          <div>
            <div class="feature-title">Keteduhan & Rindang</div>
            <div class="feature-desc">{{ getShadeText }}</div>
          </div>
        </div>

        <div class="walker-feature-card">
          <div class="feature-icon-box bg-blue-light">
            <v-icon size="20" color="#0284C7">mdi-wheelchair-accessibility</v-icon>
          </div>
          <div>
            <div class="feature-title">Aksesibilitas</div>
            <div class="feature-desc">Ramah stroller anak, kursi roda, & lansia</div>
          </div>
        </div>

        <div class="walker-feature-card">
          <div class="feature-icon-box bg-amber-light">
            <v-icon size="20" color="#D97706">mdi-clock-outline</v-icon>
          </div>
          <div>
            <div class="feature-title">Waktu Terbaik</div>
            <div class="feature-desc">Pagi 06:30–08:30 & Sore 16:30–18:15</div>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Fasilitas di Sekitar (Nearby Amenities) -->
    <section class="content-block">
      <h2 class="section-title">Fasilitas Pejalan di Sekitar</h2>
      <div class="amenities-pills-row">
        <div class="amenity-chip">
          <v-icon size="16" color="#0284C7" class="mr-1.5">mdi-water-outline</v-icon>
          <span>Warung & Titik Minum (~50m)</span>
        </div>
        <div class="amenity-chip">
          <v-icon size="16" color="#059669" class="mr-1.5">mdi-toilet</v-icon>
          <span>Toilet Umum & Musholla</span>
        </div>
        <div class="amenity-chip">
          <v-icon size="16" color="#D97706" class="mr-1.5">mdi-seat</v-icon>
          <span>Bangku Istirahat Warga</span>
        </div>
        <div class="amenity-chip">
          <v-icon size="16" color="#6366F1" class="mr-1.5">mdi-bus</v-icon>
          <span>Akses Bus / Angkot</span>
        </div>
        <div class="amenity-chip">
          <v-icon size="16" color="#E11D48" class="mr-1.5">mdi-camera-outline</v-icon>
          <span>Spot Sunset & Foto Estetik</span>
        </div>
      </div>
    </section>

    <!-- 3. Etika & Kebiasaan Baik Pejalan (Walker's Code) -->
    <section class="content-block">
      <div class="walker-code-box">
        <div class="d-flex align-center ga-2 mb-2">
          <v-icon size="18" color="#DC2626">mdi-leaf</v-icon>
          <span class="font-weight-bold text-subtitle-2 text-grey-darken-4">Etika Pejalan Jalan Bareng</span>
        </div>
        <ul class="walker-code-list mb-0">
          <li><strong>Leave No Trace:</strong> Selalu bawa pulang sampah pribadi atau buang pada tempat sampah terdekat.</li>
          <li><strong>Berbagi Ruang Publik:</strong> Berikan prioritas pada pejalan santai, anak-anak, dan lansia.</li>
          <li><strong>Hormati Warga Sekitar:</strong> Nikmati keindahan kota tanpa merusak ketenteraman dan fasilitas bersama.</li>
        </ul>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  categoryName?: string
  destinationName?: string
}

const props = defineProps<Props>()

const getWalkabilityText = computed(() => {
  const cat = (props.categoryName || '').toLowerCase()
  if (cat.includes('pantai') || cat.includes('taman')) return 'Jalur pedestrian luas, rata, dan nyaman'
  if (cat.includes('sejarah') || cat.includes('spot')) return 'Trotoar kota beraspal & paving rapi'
  if (cat.includes('kuliner')) return 'Koridor jalan santai kuliner pejalan kaki'
  return 'Jalur ramah pejalan kaki'
})

const getShadeText = computed(() => {
  const cat = (props.categoryName || '').toLowerCase()
  if (cat.includes('taman') || cat.includes('alam')) return 'Sangat teduh dinaungi pepohonan rindang'
  if (cat.includes('pantai') || cat.includes('spot')) return 'Terbuka luas, disarankan topi atau kacamata'
  return 'Cukup teduh dengan semilir angin kota'
})
</script>

<style scoped>
.destination-walker-guide {
  margin-top: 8px;
}

.content-block {
  margin-bottom: 32px;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.015em;
  margin-bottom: 16px;
}

.guide-tag-pill {
  display: inline-flex;
  align-items: center;
  background: #FEF2F2;
  color: #DC2626;
  font-size: 0.76rem;
  font-weight: 700;
  padding: 4px 10px;
  border-radius: 9999px;
  border: 1px solid #FEE2E2;
}

/* Features Grid */
.walker-features-grid {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  gap: 12px;
}

@media (min-width: 600px) {
  .walker-features-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }
}

.walker-feature-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 16px;
  background: #FAFAF9;
  border: 1px solid #E7E5E4;
  border-radius: 16px;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.walker-feature-card:hover {
  transform: translateY(-2px);
  border-color: #D6D3D1;
}

.feature-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.bg-red-light {
  background: #FEE2E2;
}

.bg-green-light {
  background: #DCFCE7;
}

.bg-blue-light {
  background: #E0F2FE;
}

.bg-amber-light {
  background: #FEF3C7;
}

.feature-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1F2937;
  margin-bottom: 2px;
}

.feature-desc {
  font-size: 0.8rem;
  color: #6B7280;
  line-height: 1.4;
}

/* Amenities Row */
.amenities-pills-row {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.amenity-chip {
  display: inline-flex;
  align-items: center;
  padding: 8px 14px;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 9999px;
  font-size: 0.82rem;
  font-weight: 600;
  color: #374151;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

/* Walker Code Box */
.walker-code-box {
  background: #FFF5F5;
  border: 1px solid #FEE2E2;
  border-radius: 16px;
  padding: 16px 20px;
}

.walker-code-list {
  padding-left: 20px;
  font-size: 0.84rem;
  color: #4B5563;
  line-height: 1.6;
}

.walker-code-list li {
  margin-bottom: 4px;
}
</style>
