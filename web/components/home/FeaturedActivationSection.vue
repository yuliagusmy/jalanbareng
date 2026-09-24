<template>
  <div v-if="activations && activations.length > 0" class="featured-activations-section mb-10 mb-md-16">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-3">
      <div>
        <div class="d-flex align-center ga-2 mb-1">
          <span class="text-caption font-weight-bold text-uppercase tracking-wider text-primary">
            Inisiatif Paling Aktif
          </span>
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">
            Top 3
          </v-chip>
        </div>
        <h2 class="text-h4 text-md-h3 section-headline section-title mb-1">
          Aktivasi Unggulan
        </h2>
        <p class="text-body-2 text-md-body-1 text-grey-darken-1 mt-1 mb-0" style="max-width: 620px;">
          Komunitas dan chapter yang paling rajin menggelar jalan santai serta eksplorasi lorong kota bersama pejalan kaki.
        </p>
      </div>

      <v-btn
        to="/aktivasi"
        variant="text"
        color="grey-darken-2"
        rounded="pill"
        :size="$vuetify.display.mobile ? 'default' : 'large'"
        class="px-3 font-weight-bold"
      >
        <span>Lihat Semua</span>
        <v-icon end size="18">mdi-arrow-right</v-icon>
      </v-btn>
    </div>

    <!-- 3 Cards Layout -->
    <v-row class="featured-cards-grid">
      <v-col
        v-for="(item, index) in displayActivations"
        :key="item.id"
        cols="12"
        sm="6"
        md="4"
      >
        <v-card
          :to="`/aktivasi/${item.slug}`"
          elevation="0"
          rounded="xl"
          class="featured-item-card h-100 d-flex flex-column"
        >
          <!-- Image Wrapper with Badges -->
          <div class="card-img-wrapper">
            <img
              :src="getImageUrl(item.hero_image)"
              :alt="item.name"
              class="card-img"
            />
            <div class="card-img-overlay"></div>

            <!-- Rank Badge (1, 2, 3) -->
            <div class="rank-badge" :class="`rank-${index + 1}`">
              <v-icon size="14" color="white" class="mr-1">mdi-fire</v-icon>
              <span>#{{ index + 1 }} Teraktif</span>
            </div>

            <!-- Event Count Float Badge -->
            <div class="event-count-badge">
              <v-icon size="14" class="mr-1">mdi-calendar-check</v-icon>
              <span>{{ item.events_count || 0 }} Kegiatan</span>
            </div>
          </div>

          <!-- Card Body -->
          <div class="pa-5 d-flex flex-column flex-grow-1">
            <div class="d-flex align-center justify-space-between mb-2">
              <span class="city-tag">
                <v-icon size="14" color="#DC2626" class="mr-1">mdi-map-marker</v-icon>
                {{ item.city || getCategoryLabel(item.category) }}
              </span>
              <span class="dest-tag">
                <v-icon size="14" class="mr-1 text-grey">mdi-compass-outline</v-icon>
                {{ item.destinations_count || 0 }} Destinasi
              </span>
            </div>

            <h3 class="card-title text-h6 font-weight-bold text-grey-darken-4 mb-2">
              {{ item.name }}
            </h3>

            <p class="card-desc text-body-2 text-grey-darken-1 mb-4 flex-grow-1">
              {{ item.tagline || stripHtml(item.description)?.substring(0, 95) + '...' }}
            </p>

            <div class="pt-3 border-top d-flex align-center justify-space-between mt-auto">
              <span class="text-caption text-grey-darken-1 font-weight-medium">
                Chapter Komunitas
              </span>
              <span class="explore-link">
                Eksplor
                <v-icon size="14" class="ml-1">mdi-arrow-right</v-icon>
              </span>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps({
  activations: {
    type: Array,
    default: () => []
  }
})

const { getImageUrl } = useImageUrl()

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}

const getCategoryLabel = (category: string) => {
  if (category === 'city') return 'Aktivasi Kota'
  if (category === 'theme') return 'Tematik'
  if (category === 'space') return 'Ruang Kreatif'
  return 'Jalan Bareng'
}

// Top 3 activations ordered by activity (events_count)
const displayActivations = computed(() => {
  if (!props.activations || props.activations.length === 0) return []
  const sorted = [...props.activations].sort((a: any, b: any) => {
    const eventsA = a.events_count || 0
    const eventsB = b.events_count || 0
    if (eventsB !== eventsA) {
      return eventsB - eventsA
    }
    // secondary sort by destinations_count
    return (b.destinations_count || 0) - (a.destinations_count || 0)
  })
  return sorted.slice(0, 3)
})
</script>

<style scoped>
.featured-activations-section {
  position: relative;
}

.section-title {
  font-weight: 800 !important;
  letter-spacing: -0.035em !important;
  line-height: 1.2 !important;
}

.featured-item-card {
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 20px;
  overflow: hidden;
  background: #ffffff;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  cursor: pointer;
}

.featured-item-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.12);
  border-color: rgba(220, 38, 38, 0.25);
}

.card-img-wrapper {
  position: relative;
  height: 210px;
  overflow: hidden;
  background: #f1f5f9;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-item-card:hover .card-img {
  transform: scale(1.05);
}

.card-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.05) 50%, rgba(0, 0, 0, 0.5) 100%);
}

.rank-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  z-index: 2;
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.rank-1 {
  background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
}

.rank-2 {
  background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%);
}

.rank-3 {
  background: linear-gradient(135deg, #475569 0%, #1E293B 100%);
}

.event-count-badge {
  position: absolute;
  bottom: 14px;
  right: 14px;
  z-index: 2;
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #ffffff;
  background: rgba(17, 24, 39, 0.75);
  backdrop-filter: blur(6px);
}

.city-tag {
  display: inline-flex;
  align-items: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: #111827;
}

.dest-tag {
  display: inline-flex;
  align-items: center;
  font-size: 0.78rem;
  color: #64748b;
  font-weight: 500;
}

.card-title {
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-desc {
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.explore-link {
  font-size: 0.82rem;
  font-weight: 700;
  color: #DC2626;
  display: inline-flex;
  align-items: center;
  transition: transform 0.2s ease;
}

.featured-item-card:hover .explore-link {
  transform: translateX(3px);
}

@media (max-width: 600px) {
  .card-img-wrapper {
    height: 135px;
  }

  .featured-item-card :deep(.v-card-text),
  .featured-item-card > div.pa-5 {
    padding: 14px 14px !important;
  }

  .card-title {
    font-size: 0.95rem !important;
  }

  .card-desc {
    font-size: 0.8rem !important;
    margin-bottom: 10px !important;
  }
}
</style>
