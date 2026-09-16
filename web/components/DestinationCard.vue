
<template>
  <v-card
    elevation="2"
    rounded="xl"
    class="hover-lift h-100 d-flex flex-column"
    :to="`/destinations/${destination.id}`"
  >
    <v-img
      :src="getImageUrl(destination.primary_photo)"
      height="220"
      cover
      gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.4)"
    >
      <div class="pa-4">
        <v-chip
          v-if="destination.category"
          color="white"
          size="small"
          rounded="lg"
          class="font-weight-medium"
        >
          {{ destination.category.name }}
        </v-chip>
      </div>
    </v-img>

    <v-card-text class="pa-6">
      <h4 class="text-h6 font-weight-bold mb-3">{{ destination.name }}</h4>

      <p class="text-caption text-grey-darken-1 mb-4">
        {{ destination.description?.substring(0, 80) }}...
      </p>

      <div class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <v-icon size="small" color="error" class="mr-1">mdi-heart</v-icon>
          <span class="text-caption">{{ destination.likes_count || 0 }}</span>
        </div>
        <div class="d-flex align-center">
          <v-icon size="small" color="grey" class="mr-1">mdi-comment</v-icon>
          <span class="text-caption">{{ destination.comments_count || 0 }}</span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.hover-lift {
  transition: all 0.3s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15) !important;
}
</style>

<script setup lang="ts">
import { useRuntimeConfig } from '#app'

defineProps({
  destination: {
    type: Object,
    required: true
  }
})

const config = useRuntimeConfig()

const getImageUrl = (path: string) => {
  if (!path) return 'https://via.placeholder.com/400x300'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}
</script>
