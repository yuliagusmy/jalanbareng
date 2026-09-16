<template>
  <v-row>
    <v-col cols="12">
      <h2 class="text-h4 font-weight-bold mb-4">Gallery Kegiatan</h2>
    </v-col>

    <v-col cols="12">
      <v-row>
        <v-col
          v-for="(item, index) in media"
          :key="item.id"
          cols="12"
          sm="6"
          md="4"
        >
          <!-- Photo -->
          <v-card
            v-if="item.type === 'photo' && item.file_url"
            @click="openLightbox(index)"
            class="gallery-item"
            hover
          >
            <v-img
              :src="`${apiBase}/storage/${item.file_url}`"
              :alt="item.description"
              aspect-ratio="1"
              cover
            >
              <template v-slot:placeholder>
                <v-row class="fill-height ma-0" align="center" justify="center">
                  <v-progress-circular indeterminate color="grey-lighten-5" />
                </v-row>
              </template>
            </v-img>
            <v-card-text v-if="item.description" class="text-caption">
              {{ item.description }}
            </v-card-text>
          </v-card>

          <!-- YouTube Video -->
          <v-card v-else-if="item.type === 'youtube' && item.external_url">
            <div class="video-container">
              <iframe
                :src="getYouTubeEmbedUrl(item.external_url)"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              />
            </div>
            <v-card-text v-if="item.description" class="text-caption">
              {{ item.description }}
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-col>

    <!-- Lightbox Dialog -->
    <v-dialog v-model="lightboxOpen" max-width="90vw">
      <v-card>
        <v-card-text class="pa-0">
          <v-img
            v-if="currentMedia && currentMedia.file_url"
            :src="`${apiBase}/storage/${currentMedia.file_url}`"
            :alt="currentMedia.description"
            max-height="80vh"
            contain
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn icon @click="previousImage">
            <v-icon>mdi-chevron-left</v-icon>
          </v-btn>
          <v-btn icon @click="nextImage">
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
          <v-btn icon @click="lightboxOpen = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
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

const photoMedia = computed(() => {
  return props.media.filter(m => m.type === 'photo' && m.file_url)
})

const currentMedia = computed(() => {
  return photoMedia.value[currentIndex.value]
})

const openLightbox = (index: number) => {
  const photoIndex = photoMedia.value.findIndex(m => m.id === props.media[index].id)
  if (photoIndex !== -1) {
    currentIndex.value = photoIndex
    lightboxOpen.value = true
  }
}

const nextImage = () => {
  currentIndex.value = (currentIndex.value + 1) % photoMedia.value.length
}

const previousImage = () => {
  currentIndex.value = (currentIndex.value - 1 + photoMedia.value.length) % photoMedia.value.length
}

const getYouTubeEmbedUrl = (url: string) => {
  // Extract video ID from various YouTube URL formats
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  const videoId = match && match[2].length === 11 ? match[2] : null
  return videoId ? `https://www.youtube.com/embed/${videoId}` : url
}
</script>

<style scoped>
.gallery-item {
  cursor: pointer;
  transition: transform 0.2s;
}

.gallery-item:hover {
  transform: scale(1.02);
}

.video-container {
  position: relative;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
  overflow: hidden;
}

.video-container iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}
</style>
