<template>
  <v-card
    :to="`/aktivasi/${activation.slug}`"
    hover
    :elevation="featured ? 8 : 2"
    class="activation-card h-100"
  >
    <!-- Hero Image -->
    <v-img
      v-if="activation.hero_image"
      :src="`${apiBase}/storage/${activation.hero_image}`"
      height="200"
      cover
      class="text-white"
    >
      <div class="d-flex flex-column fill-height pa-4">
        <!-- Featured Badge -->
        <div class="d-flex justify-end">
          <v-chip
            v-if="activation.is_featured"
            color="warning"
            size="small"
            prepend-icon="mdi-star"
          >
            Unggulan
          </v-chip>
        </div>

        <!-- Icon -->
        <v-spacer />
        <div v-if="activation.icon" class="mb-2">
          <v-avatar size="48" color="white">
            <v-img :src="`${apiBase}/storage/${activation.icon}`" />
          </v-avatar>
        </div>
      </div>
    </v-img>

    <!-- Fallback if no image -->
    <div
      v-else
      :style="{ backgroundColor: activation.color_theme || '#1976D2' }"
      class="pa-6 text-white"
      style="height: 200px"
    >
      <div class="d-flex flex-column fill-height">
        <div class="d-flex justify-end">
          <v-chip
            v-if="activation.is_featured"
            color="warning"
            size="small"
            prepend-icon="mdi-star"
          >
            Unggulan
          </v-chip>
        </div>
        <v-spacer />
        <v-icon size="64" color="white">mdi-map-marker-multiple</v-icon>
      </div>
    </div>

    <!-- Content -->
    <v-card-text>
      <!-- Category Badge -->
      <v-chip
        :color="categoryColor"
        size="small"
        class="mb-2"
      >
        {{ categoryLabel }}
      </v-chip>

      <!-- Title -->
      <h3 class="text-h6 font-weight-bold mb-2">
        {{ activation.name }}
      </h3>

      <!-- Tagline -->
      <p v-if="activation.tagline" class="text-body-2 text-medium-emphasis mb-3">
        {{ activation.tagline }}
      </p>

      <!-- City -->
      <div v-if="activation.city" class="d-flex align-center text-caption text-medium-emphasis">
        <v-icon size="16" class="mr-1">mdi-map-marker</v-icon>
        {{ activation.city }}
      </div>

      <!-- Events Count -->
      <div v-if="activation.events_count" class="d-flex align-center text-caption text-medium-emphasis mt-1">
        <v-icon size="16" class="mr-1">mdi-calendar-multiple</v-icon>
        {{ activation.events_count }} event
      </div>
    </v-card-text>

    <!-- Actions -->
    <v-card-actions>
      <v-btn
        :color="activation.color_theme || 'primary'"
        variant="text"
        append-icon="mdi-arrow-right"
      >
        Lihat Detail
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup lang="ts">
interface Props {
  activation: any
  featured?: boolean
}

const props = defineProps<Props>()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const categoryColor = computed(() => {
  const colors: Record<string, string> = {
    city: 'blue',
    theme: 'purple',
    space: 'pink',
    other: 'grey'
  }
  return colors[props.activation.category] || 'grey'
})

const categoryLabel = computed(() => {
  const labels: Record<string, string> = {
    city: 'Kota',
    theme: 'Tematik',
    space: 'Ruang Kreatif',
    other: 'Lainnya'
  }
  return labels[props.activation.category] || 'Lainnya'
})
</script>

<style scoped>
.activation-card {
  transition: transform 0.2s;
}

.activation-card:hover {
  transform: translateY(-4px);
}
</style>
