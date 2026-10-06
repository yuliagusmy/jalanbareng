<template>
  <v-card
    :to="`/aktivasi/${activation.slug}`"
    elevation="0"
    class="activation-card h-100 d-flex flex-column"
  >
    <!-- Card Image Header -->
    <div class="card-header-img-wrapper">
      <img
        v-if="activation.hero_image"
        :src="getImageUrl(activation.hero_image)"
        :alt="activation.name"
        class="card-top-img"
        loading="lazy"
      />
      <div v-else class="img-placeholder h-100">
        <v-icon size="36" color="grey-lighten-1">mdi-image-outline</v-icon>
      </div>

      <div class="card-tag-group">
        <span class="category-pill-solid" :class="`pill-${activation.category}`">
          {{ getCategoryLabel(activation.category) }}
        </span>
      </div>

      <!-- Chapter Icon Avatar -->
      <div v-if="activation.icon" class="activation-avatar-badge">
        <img :src="getImageUrl(activation.icon)" :alt="activation.name" />
      </div>
    </div>

    <!-- Card Content -->
    <div class="pa-3 pa-sm-4 d-flex flex-column flex-grow-1">
      <div class="d-flex align-center justify-space-between mb-2 gap-2">
        <span v-if="activation.city" class="city-indicator">
          <v-icon size="12" class="mr-1 text-grey">mdi-map-marker-outline</v-icon>
          <span class="text-truncate">{{ activation.city }}</span>
        </span>
        <span v-else class="city-indicator text-grey">
          <v-icon size="12" class="mr-1 text-grey">mdi-tag-outline</v-icon>
          <span class="text-truncate">{{ getCategoryBadge(activation.category) }}</span>
        </span>
        <span class="event-count-chip">
          {{ activation.events_count || 0 }} Event
        </span>
      </div>

      <h3 class="card-title text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1" style="text-wrap: balance;">
        {{ activation.name }}
      </h3>

      <p v-if="activation.tagline" class="card-desc text-caption text-grey-darken-1 mb-3 flex-grow-1">
        {{ activation.tagline }}
      </p>
      <div v-else class="flex-grow-1"></div>

      <div class="pt-2 border-top d-flex align-center justify-space-between mt-auto">
        <span class="text-caption text-grey font-weight-medium">
          {{ getFooterLabel(activation.category) }}
        </span>
        <span class="action-link-text">
          Eksplor
          <v-icon size="14" class="ml-0.5">mdi-arrow-right</v-icon>
        </span>
      </div>
    </div>
  </v-card>
</template>

<script setup lang="ts">
interface Props {
  activation: any
}

defineProps<Props>()

const { getImageUrl } = useImageUrl()

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Kota',
    theme: 'Tematik',
    space: 'Ruang Komunal',
    other: 'Komunitas'
  }
  return labels[category] || 'Komunitas'
}

const getCategoryBadge = (category: string) => {
  if (category === 'space') return 'Ruang Komunal'
  if (category === 'theme') return 'Inisiatif Tematik'
  return 'Komunitas'
}

const getFooterLabel = (category: string) => {
  if (category === 'space') return 'Ruang Temu'
  if (category === 'theme') return 'Gerakan Tematik'
  return 'Jalan Bareng Chapter'
}
</script>

<style scoped>
.activation-card {
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  background: #FFFFFF;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
  text-decoration: none;
}

.activation-card:hover {
  transform: translateY(-4px) scale(1.01);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
  border-color: #CBD5E1;
}

.card-header-img-wrapper {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  background: #F3F4F6;
}

@media (min-width: 600px) {
  .card-header-img-wrapper {
    height: 195px;
  }
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
  top: 10px;
  left: 10px;
  z-index: 2;
}

.category-pill-solid {
  display: inline-flex;
  align-items: center;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 9999px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(4px);
  color: #FFFFFF;
  letter-spacing: 0.02em;
}

.pill-city {
  background: rgba(220, 38, 38, 0.9);
}

.pill-theme {
  background: rgba(15, 23, 42, 0.85);
}

.pill-space {
  background: rgba(225, 29, 72, 0.9);
}

.activation-avatar-badge {
  position: absolute;
  bottom: -16px;
  right: 14px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #FFFFFF;
  border: 2px solid #FFFFFF;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
  overflow: hidden;
  z-index: 2;
}

.activation-avatar-badge img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.city-indicator {
  display: inline-flex;
  align-items: center;
  font-size: 0.78rem;
  font-weight: 600;
  color: #4B5563;
  min-width: 0;
  flex: 1;
  overflow: hidden;
}

.city-indicator .text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.event-count-chip {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  font-weight: 700;
  color: #DC2626;
  background: #FEF2F2;
  padding: 6px 10px;
  min-height: 28px;
  border-radius: 9999px;
  flex-shrink: 0;
  white-space: nowrap;
}

.card-title {
  color: #111827;
  line-height: 1.35;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

.card-desc {
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.action-link-text {
  display: inline-flex;
  align-items: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: #DC2626;
  transition: transform 0.2s ease;
}

.activation-card:hover .action-link-text {
  transform: translateX(2px);
}

.img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
}

@media (prefers-reduced-motion: reduce) {
  .activation-card,
  .card-top-img,
  .action-link-text {
    transition: none;
  }

  .activation-card:hover {
    transform: none;
  }

  .activation-card:hover .card-top-img {
    transform: none;
  }

  .activation-card:hover .action-link-text {
    transform: none;
  }
}
</style>
