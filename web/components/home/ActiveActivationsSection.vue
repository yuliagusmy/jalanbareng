<template>
  <div v-if="activations.length > 0" class="mb-10 mb-md-16">
    <div class="d-flex justify-space-between align-center mb-6 mb-md-8 px-2">
      <div>
        <h2 :class="$vuetify.display.mobile ? 'text-h6' : 'text-h3'" class="font-weight-bold mb-1 mb-md-2">Aktivasi
          Tersedia</h2>
        <p :class="$vuetify.display.mobile ? 'text-caption' : 'text-body-1'" class="text-grey-darken-1 mb-0">Pilih
          aktivasi untuk memulai petualangan</p>
      </div>
      <v-btn to="/aktivasi" variant="text" color="primary" :size="$vuetify.display.mobile ? 'small' : 'large'">
        <span class="hidden-sm-and-down">Lihat Semua</span>
        <v-icon :end="!$vuetify.display.xs">mdi-arrow-right</v-icon>
      </v-btn>
    </div>

    <v-row>
      <v-col v-for="activation in activations" :key="activation.id" cols="12" sm="6" md="4">
        <v-card elevation="0" class="activation-card h-100" :to="`/aktivasi/${activation.slug}`" rounded="xl">
          <div class="activation-image-wrapper">
            <img :src="getImageUrl(activation.hero_image)" :alt="activation.name" class="activation-image" />
            <div class="activation-overlay"></div>
            <div class="activation-badge">
              <v-chip :color="activation.color_theme || 'primary'" size="small" class="font-weight-bold text-white">
                {{ getBadgeLabel(activation) }}
              </v-chip>
            </div>
          </div>

          <v-card-text class="pa-4 pa-md-5">
            <h4 class="text-h6 font-weight-bold mb-2">{{ activation.name }}</h4>
            <p class="text-caption text-grey-darken-1 mb-3" style="min-height: 40px;">
              {{ activation.tagline }}
            </p>

            <div class="d-flex align-center justify-space-between">
              <div class="d-flex align-center">
                <v-icon size="small" color="primary" class="mr-1">mdi-calendar</v-icon>
                <span class="text-caption">{{ activation.events_count || 0 }} Event</span>
              </div>
              <div class="d-flex align-center">
                <v-icon size="small" color="success" class="mr-1">mdi-map-marker</v-icon>
                <span class="text-caption">{{ activation.destinations_count || 0 }} Tempat</span>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
const props = defineProps({
  activations: {
    type: Array,
    required: true
  }
})

const { getImageUrl } = useImageUrl()

const getBadgeLabel = (activation: any) => {
  if (activation.city) return activation.city
  if (activation.category === 'theme') return 'Tematik'
  if (activation.category === 'space') return 'Ruang Kreatif'
  return 'Jalan Bareng'
}
</script>

<style scoped>
.activation-card {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
}

.activation-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.activation-image-wrapper {
  position: relative;
  height: 240px;
  overflow: hidden;
}

.activation-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.activation-card:hover .activation-image {
  transform: scale(1.05);
}

.activation-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0) 100%);
}

.activation-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 2;
}
</style>
