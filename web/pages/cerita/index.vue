<template>
  <div class="stories-archive-page">
    <!-- Header Hero -->
    <v-sheet class="py-12 py-md-16 bg-grey-lighten-4 border-b">
      <v-container>
        <v-row align="center" justify="space-between">
          <v-col cols="12" md="7">
            <v-chip size="small" color="primary" variant="tonal" class="font-weight-bold mb-3">
              Koleksi Tulisan Komunitas
            </v-chip>
            <h1 class="text-h4 text-md-h3 font-weight-bold text-grey-darken-4 mb-3">
              Cerita & Kabar Teman Jalan
            </h1>
            <p class="text-body-1 text-grey-darken-1 mb-0" style="max-width: 600px;">
              Temukan refleksi perjalanan, kisah inklusi warga, dan laporan observasi lorong perkotaan dari sudut pandang pejalan kaki.
            </p>
          </v-col>

          <v-col cols="12" md="5" class="text-md-right mt-4 mt-md-0">
            <v-btn
              color="primary"
              size="x-large"
              rounded="pill"
              elevation="3"
              class="px-6 font-weight-bold"
              @click="showSubmitDialog = true"
            >
              <v-icon start>mdi-feather</v-icon>
              Kirim Tulisan Anda
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>

    <v-container class="py-10">
      <!-- Search & Filters -->
      <v-row class="mb-6" align="center">
        <v-col cols="12" sm="8" md="6">
          <v-text-field
            v-model="searchQuery"
            placeholder="Cari judul, topik, atau nama penulis..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="comfortable"
            rounded="pill"
            hide-details
            clearable
            @update:model-value="debounceSearch"
          ></v-text-field>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <v-row v-if="loading" dense>
        <v-col cols="12" sm="6" md="4" v-for="i in 6" :key="i">
          <v-skeleton-loader type="card" height="400" rounded="xl"></v-skeleton-loader>
        </v-col>
      </v-row>

      <!-- Stories Grid -->
      <v-row v-else-if="stories.length > 0">
        <v-col
          v-for="story in stories"
          :key="story.id"
          cols="12"
          sm="6"
          md="4"
        >
          <v-card
            rounded="xl"
            elevation="2"
            class="story-archive-card h-100 d-flex flex-column"
            :class="`border-theme-${story.card_style || 'coral'}`"
            @click="navigateTo(`/cerita/${story.slug}`)"
          >
            <!-- Card Header / Image or Color -->
            <div
              class="archive-card-banner"
              :class="`theme-${story.card_style || 'coral'}`"
            >
              <v-img
                v-if="story.cover_image_url"
                :src="story.cover_image_url"
                height="180"
                cover
                class="archive-img"
              >
                <div class="archive-pill">{{ story.author_name }}</div>
              </v-img>
              <div v-else class="archive-placeholder-banner pa-6 d-flex align-center justify-center">
                <v-icon size="48" color="white" style="opacity: 0.7;">mdi-book-open-page-variant</v-icon>
              </div>
            </div>

            <v-card-text class="pa-5 flex-grow-1 d-flex flex-column">
              <div class="text-caption text-grey-darken-1 mb-2">
                {{ formatDate(story.published_at) }}
              </div>
              <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2 archive-title">
                {{ story.title }}
              </h3>
              <p class="text-body-2 text-grey-darken-2 mb-4 archive-excerpt flex-grow-1">
                {{ story.excerpt }}
              </p>

              <div class="d-flex align-center justify-space-between pt-3 border-t text-caption text-primary font-weight-bold">
                <span>Baca Selengkapnya</span>
                <v-icon size="small">mdi-arrow-right</v-icon>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Empty State -->
      <v-card v-else variant="tonal" rounded="xl" class="pa-12 text-center bg-grey-lighten-4 my-8">
        <v-icon size="56" color="grey" class="mb-3">mdi-text-box-search-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-1">Tidak ada tulisan yang cocok</h3>
        <p class="text-body-2 text-grey mb-4">Coba cari dengan kata kunci lain atau kirim tulisan Anda sendiri.</p>
        <v-btn color="primary" rounded="pill" @click="showSubmitDialog = true">
          Kirim Tulisan
        </v-btn>
      </v-card>
    </v-container>

    <SubmitStoryDialog v-model="showSubmitDialog" @submitted="fetchStories" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'

useSeoMeta({
  title: 'Cerita & Tulisan Teman Jalan - Jalan Bareng',
  description: 'Kumpulan catatan lapangan, riset rute jalan kaki, dan cerita lorong perkotaan dari komunitas Jalan Bareng.',
})

const { api } = useApi()

const stories = ref<any[]>([])
const loading = ref(true)
const searchQuery = ref('')
const showSubmitDialog = ref(false)

let searchTimeout: any = null

const formatDate = (dateStr: string) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const fetchStories = async () => {
  loading.value = true
  try {
    const res = await api.get('/stories', {
      params: {
        q: searchQuery.value || undefined,
        per_page: 24,
      }
    })
    stories.value = res.data.stories || res.data.data || []
  } catch (err) {
    console.error('Failed to load stories archive:', err)
  } finally {
    loading.value = false
  }
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchStories()
  }, 400)
}

onMounted(() => {
  fetchStories()
})
</script>

<style scoped>
.story-archive-card {
  transition: all 0.25s ease;
  cursor: pointer;
  overflow: hidden;
}

.story-archive-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.12) !important;
}

.archive-card-banner {
  position: relative;
  overflow: hidden;
}

.theme-coral { background: #FA4D56; }
.theme-magenta { background: #E02F6B; }
.theme-amber { background: #F4A228; }
.theme-photo, .theme-dark { background: #1e293b; }

.archive-pill {
  position: absolute;
  top: 12px;
  left: 12px;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  color: white;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.7rem;
  font-weight: 600;
}

.archive-title {
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.archive-excerpt {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
}
</style>
