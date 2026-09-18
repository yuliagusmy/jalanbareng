<template>
  <div class="stories-archive-page">
    <!-- Editorial Hero Section -->
    <section class="stories-hero">
      <v-container class="hero-content">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Eyebrow Badge -->
            <div class="hero-badge-pill mb-4">
              <v-icon start size="16" color="#DC2626">mdi-feather</v-icon>
              <span>KOLEKSI TULISAN KOMUNITAS</span>
            </div>

            <!-- Main Headline -->
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              Cerita &amp; Kabar
              <span class="text-primary-red">Jalan Bareng</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Temukan refleksi perjalanan, kisah inklusi warga, dan laporan observasi lorong perkotaan dari sudut pandang pejalan kaki.
            </p>

            <!-- Actions Row -->
            <div class="d-flex align-center justify-center flex-wrap ga-3">
              <v-btn
                color="#DC2626"
                size="large"
                rounded="pill"
                elevation="0"
                class="font-weight-bold px-6 text-white"
                @click="showSubmitDialog = true"
              >
                <v-icon start size="18">mdi-feather</v-icon>
                Kirim Tulisan Anda
              </v-btn>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Container -->
    <v-container class="py-6 py-md-10">
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
import { defaultDummyStories } from '~/utils/dummyStories'

useSeoMeta({
  title: 'Cerita & Tulisan Jalan Bareng - Komunitas Pejalan Kaki',
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

const filterFallbackStories = (query: string) => {
  if (!query) return defaultDummyStories
  const q = query.toLowerCase()
  return defaultDummyStories.filter(s =>
    s.title.toLowerCase().includes(q) ||
    s.author_name.toLowerCase().includes(q) ||
    s.excerpt.toLowerCase().includes(q)
  )
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
    const fetched = res.data.stories || res.data.data || []
    if (fetched && fetched.length > 0) {
      stories.value = fetched
    } else {
      stories.value = filterFallbackStories(searchQuery.value)
    }
  } catch (err) {
    console.error('Failed to load stories archive from API, using fallback:', err)
    stories.value = filterFallbackStories(searchQuery.value)
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
.stories-hero {
  background: #F8FAFC;
  border-bottom: 1px solid #E2E8F0;
  padding: 56px 0 48px;
}

.hero-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border-radius: 9999px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  color: #DC2626;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.hero-title {
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  letter-spacing: -0.035em;
  line-height: 1.12;
  color: #111827;
}

.text-primary-red {
  color: #DC2626;
}

.hero-subtitle {
  font-size: 1.1rem;
  line-height: 1.6;
  max-width: 680px;
}

@media (max-width: 600px) {
  .stories-hero {
    padding-top: 32px;
    padding-bottom: 32px;
  }

  .hero-title {
    font-size: 1.95rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }
}

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
