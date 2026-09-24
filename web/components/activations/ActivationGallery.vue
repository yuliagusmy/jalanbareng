<template>
  <div>
    <!-- Gallery Header -->
    <div class="gallery-header mb-4 mb-md-6">
      <h3 class="gallery-title font-weight-bold">Dokumentasi Kegiatan</h3>
      <p class="gallery-count text-grey-darken-1">{{ photoMedia.length }} foto</p>
    </div>

    <!-- Photo Grid: 2 col mobile, 3 col desktop -->
    <div class="gallery-grid">
      <div
        v-for="(item, index) in displayedMedia"
        :key="item.id"
        class="gallery-cell"
        :class="{ 'gallery-cell--wide': index === 0 }"
        @click="openLightbox(index)"
      >
        <div class="gallery-img-wrapper">
          <img
            :src="getImageUrl(item.file_url)"
            :alt="item.description || 'Dokumentasi kegiatan'"
            class="gallery-img"
            loading="lazy"
          />
          <div class="gallery-overlay">
            <v-icon color="white" size="24">mdi-magnify-plus-outline</v-icon>
          </div>
        </div>
      </div>

      <!-- YouTube videos -->
      <div
        v-for="item in videoMedia"
        :key="item.id"
        class="gallery-cell gallery-cell--video"
      >
        <div class="video-container">
          <iframe
            :src="getYouTubeEmbedUrl(item.external_url)"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen
            loading="lazy"
          />
        </div>
      </div>
    </div>

    <!-- Show More -->
    <div v-if="photoMedia.length > 6" class="text-center mt-4">
      <v-btn
        variant="tonal"
        rounded="pill"
        size="small"
        @click="showAll = !showAll"
      >
        {{ showAll ? 'Tampilkan lebih sedikit' : `Lihat semua ${photoMedia.length} foto` }}
        <v-icon end size="16">{{ showAll ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
      </v-btn>
    </div>

    <!-- Lightbox Dialog -->
    <v-dialog v-model="lightboxOpen" max-width="92vw" :max-height="'90vh'">
      <v-card rounded="xl" class="overflow-hidden" style="background:#000">
        <div class="lightbox-img-wrapper">
          <img
            v-if="currentMedia"
            :src="getImageUrl(currentMedia.file_url)"
            :alt="currentMedia.description"
            class="lightbox-img"
          />
        </div>
        <p v-if="currentMedia?.description" class="pa-3 text-caption text-white text-center ma-0" style="background: rgba(0,0,0,0.6);">
          {{ currentMedia.description }}
        </p>
        <!-- Controls -->
        <div class="lightbox-controls">
          <v-btn icon size="small" color="white" variant="text" @click="previousImage">
            <v-icon>mdi-chevron-left</v-icon>
          </v-btn>
          <span class="text-caption text-white">{{ currentIndex + 1 }} / {{ photoMedia.length }}</span>
          <v-btn icon size="small" color="white" variant="text" @click="nextImage">
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
          <v-btn icon size="small" color="white" variant="text" @click="lightboxOpen = false" class="ml-2">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
interface Props {
  media: any[]
}

const props = defineProps<Props>()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const lightboxOpen = ref(false)
const currentIndex = ref(0)
const showAll = ref(false)

const photoMedia = computed(() =>
  props.media.filter(m => m.type === 'photo' && m.file_url)
)

const videoMedia = computed(() =>
  props.media.filter(m => m.type === 'youtube' && m.external_url)
)

const displayedMedia = computed(() =>
  showAll.value ? photoMedia.value : photoMedia.value.slice(0, 6)
)

const currentMedia = computed(() => photoMedia.value[currentIndex.value])

const openLightbox = (index: number) => {
  currentIndex.value = index
  lightboxOpen.value = true
}

const nextImage = () => {
  currentIndex.value = (currentIndex.value + 1) % photoMedia.value.length
}

const previousImage = () => {
  currentIndex.value = (currentIndex.value - 1 + photoMedia.value.length) % photoMedia.value.length
}

const getImageUrl = (url?: string) => {
  if (!url) return ''
  if (url.startsWith('http') || url.startsWith('/')) return url
  return `${apiBase}/storage/${url}`
}

const getYouTubeEmbedUrl = (url: string) => {
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  const videoId = match && match[2].length === 11 ? match[2] : null
  return videoId ? `https://www.youtube.com/embed/${videoId}` : url
}
</script>

<style scoped>
.gallery-header {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.gallery-title {
  font-size: 1.05rem;
  color: #111827;
}

.gallery-count {
  font-size: 0.8rem;
}

/* 2-column grid on mobile, 3-column on desktop */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}

@media (min-width: 600px) {
  .gallery-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
  }
}

.gallery-cell {
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  aspect-ratio: 1 / 1;
  background: #F3F4F6;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

@media (min-width: 600px) {
  .gallery-cell {
    border-radius: 14px;
  }
}

.gallery-cell--wide {
  /* On mobile keep uniform, on tablet+ allow wide */
  grid-column: span 1;
}

@media (min-width: 960px) {
  .gallery-cell--wide {
    grid-column: span 2;
    aspect-ratio: 2 / 1;
  }
}

.gallery-cell--video {
  aspect-ratio: 16 / 9;
  grid-column: span 2;
  cursor: default;
}

@media (min-width: 600px) {
  .gallery-cell--video {
    grid-column: span 1;
  }
}

.gallery-img-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.35s ease;
}

.gallery-cell:hover .gallery-img {
  transform: scale(1.06);
}

.gallery-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.25s ease;
}

.gallery-cell:hover .gallery-overlay {
  background: rgba(0, 0, 0, 0.35);
}

.gallery-overlay .v-icon {
  opacity: 0;
  transition: opacity 0.25s ease;
}

.gallery-cell:hover .gallery-overlay .v-icon {
  opacity: 1;
}

/* Video */
.video-container {
  position: relative;
  width: 100%;
  height: 100%;
}

.video-container iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
}

/* Lightbox */
.lightbox-img-wrapper {
  width: 100%;
  max-height: 70vh;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #000;
}

.lightbox-img {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
  display: block;
}

.lightbox-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px 12px;
  background: rgba(0, 0, 0, 0.7);
}
</style>
