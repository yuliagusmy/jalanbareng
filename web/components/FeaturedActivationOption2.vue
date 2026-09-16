<template>
  <!-- OPSI 2: Full-Width Image Overlay (280px height, content on image) -->
  <v-card elevation="0" rounded="xl" class="featured-activation-card-option2 overflow-hidden"
    :to="`/aktivasi/${activation.slug}`">
    <div class="featured-image-wrapper-option2">
      <img :src="getImageUrl(activation.hero_image)" :alt="activation.name" class="featured-image-option2" />
      <div class="featured-overlay-dark"></div>

      <!-- Content overlay on image -->
      <div class="featured-content-overlay">


        <div class="featured-bottom-content">
          <div class="d-flex flex-wrap ga-2 mb-2">
            <v-chip size="small" variant="tonal" color="white">
              <v-icon start size="small">mdi-calendar-multiple</v-icon>
              {{ activation.events_count || 0 }} Event
            </v-chip>
            <v-chip size="small" variant="tonal" color="white">
              <v-icon start size="small">mdi-map-marker</v-icon>
              {{ activation.destinations_count || 0 }} Destinasi
            </v-chip>
          </div>

          <p class="text-body-2 text-white mb-3" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);">
            {{ stripHtml(activation.description)?.substring(0, 120) }}...
          </p>

          <div class="d-flex flex-column flex-sm-row ga-2">
            <v-btn :to="`/aktivasi/${activation.slug}`" color="white" size="large" rounded="pill" class="px-6"
              variant="flat">
              Lihat Detail
              <v-icon end>mdi-arrow-right</v-icon>
            </v-btn>
            <v-btn :to="`/destinations?activation_id=${activation.id}`" variant="outlined" color="white" size="large"
              rounded="pill" class="px-6">
              <v-icon start>mdi-compass</v-icon>
              Jelajahi
            </v-btn>
          </div>
        </div>
      </div>
    </div>
  </v-card>
</template>

<script setup lang="ts">
const props = defineProps({
  activation: {
    type: Object,
    required: true
  }
})

const { getImageUrl } = useImageUrl()

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}
</script>

<style scoped>
.featured-activation-card-option2 {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
}

.featured-activation-card-option2:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.featured-image-wrapper-option2 {
  position: relative;
  height: 280px;
  overflow: hidden;
}

.featured-image-option2 {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-activation-card-option2:hover .featured-image-option2 {
  transform: scale(1.05);
}

.featured-overlay-dark {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.75) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
}

.featured-content-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  z-index: 2;
}

.featured-badge-top {
  align-self: flex-start;
}

.featured-bottom-content {
  margin-top: auto;
}

@media (max-width: 960px) {
  .featured-image-wrapper-option2 {
    height: 320px;
  }
}
</style>
