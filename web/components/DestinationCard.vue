
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
      <h4 class="text-h6 font-weight-bold mb-3" style="text-wrap: balance; line-height: 1.3;">
        {{ destination.name }}
      </h4>

      <p class="text-caption text-grey-darken-1 mb-4 destination-desc">
        {{ truncateDescription(destination.description) }}
      </p>

      <div class="d-flex align-center justify-space-between">
        <button class="stat-btn d-flex align-center" aria-label="Likes" @click.prevent="() => {}">
          <v-icon size="18" color="error" class="mr-1">mdi-heart</v-icon>
          <span class="text-caption">{{ destination.likes_count || 0 }}</span>
        </button>
        <button class="stat-btn d-flex align-center" aria-label="Comments" @click.prevent="() => {}">
          <v-icon size="18" color="grey" class="mr-1">mdi-comment</v-icon>
          <span class="text-caption">{{ destination.comments_count || 0 }}</span>
        </button>
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

.destination-desc {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-wrap: break-word;
  overflow-wrap: break-word;
  line-height: 1.5;
}

.stat-btn {
  background: transparent;
  border: none;
  padding: 8px 12px;
  margin: -8px -12px;
  cursor: pointer;
  border-radius: 8px;
  min-height: 44px;
  min-width: 44px;
  transition: background-color 0.2s ease;
}

.stat-btn:active {
  background-color: rgba(0, 0, 0, 0.05);
}

@media (prefers-reduced-motion: reduce) {
  .hover-lift {
    transition: none;
  }
  .hover-lift:hover {
    transform: none;
  }
  .stat-btn {
    transition: none;
  }
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

const truncateDescription = (text: string | undefined): string => {
  if (!text) return ''

  // Word-aware truncation
  const maxLength = 80
  if (text.length <= maxLength) return text

  // Find last space before maxLength
  const truncated = text.substring(0, maxLength)
  const lastSpace = truncated.lastIndexOf(' ')

  if (lastSpace > 60) {
    return truncated.substring(0, lastSpace) + '...'
  }

  return truncated + '...'
}
</script>
