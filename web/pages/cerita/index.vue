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
              <span class="d-block">Cerita &amp; Kabar</span>
              <span class="d-block text-primary-red">Jalan Bareng</span>
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
                class="font-weight-bold px-6 text-white text-none"
                @click="handleOpenSubmitDialog"
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
      <!-- Featured Top Stories (Cerita Terbaik / Pilihan) -->
      <section v-if="!searchQuery && featuredStories.length > 0" class="mb-8 mb-md-10">
        <div class="d-flex align-center ga-2 mb-3">
          <v-icon color="#DC2626" size="18">mdi-star-four-points</v-icon>
          <h2 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-0">
            Cerita Pilihan &amp; Terpopuler
          </h2>
        </div>

        <v-row dense>
          <v-col
            v-for="story in featuredStories"
            :key="`featured-${story.id}`"
            cols="12"
            sm="6"
          >
            <div
              class="featured-top-card cursor-pointer"
              @click="navigateTo(`/cerita/${story.slug}`)"
            >
              <v-img
                :src="story.cover_image_url || 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=800&fit=crop'"
                height="190"
                cover
                class="featured-top-img"
              >
                <div class="featured-top-overlay d-flex flex-column justify-space-between pa-4">
                  <div class="d-flex align-center justify-space-between">
                    <span class="top-badge-pill">✦ Pilihan</span>
                    <span v-if="story.views_count" class="top-views-pill">
                      <v-icon size="11" class="mr-1">mdi-eye-outline</v-icon>
                      {{ story.views_count }} dibaca
                    </span>
                  </div>
                  <div>
                    <div class="text-caption text-white opacity-80 mb-0.5" style="font-size: 0.72rem !important;">
                      {{ formatDate(story.published_at) }} • {{ story.author_name }}
                    </div>
                    <h3 class="text-subtitle-2 text-sm-subtitle-1 font-weight-bold text-white line-clamp-2">
                      {{ story.title }}
                    </h3>
                  </div>
                </div>
              </v-img>
            </div>
          </v-col>
        </v-row>
      </section>

      <!-- Section Headline & Sort/Search Controls -->
      <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-5 ga-3">
        <div>
          <h2 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-0">
            {{ searchQuery ? 'Hasil Pencarian' : 'Semua Tulisan' }}
          </h2>
          <span class="text-caption text-grey-darken-1">
            Menampilkan {{ stories.length }} cerita
          </span>
        </div>

        <div class="d-flex align-center ga-2 flex-wrap w-100 w-sm-auto justify-space-between justify-sm-end">
          <!-- Sort Filter Chips -->
          <div class="d-flex ga-2">
            <button
              type="button"
              class="sort-chip-btn"
              :class="{ active: sortOption === 'latest' }"
              @click="setSort('latest')"
            >
              <v-icon start size="14">mdi-clock-outline</v-icon>
              Terbaru
            </button>
            <button
              type="button"
              class="sort-chip-btn"
              :class="{ active: sortOption === 'popular' }"
              @click="setSort('popular')"
            >
              <v-icon start size="14">mdi-fire</v-icon>
              Terpopuler
            </button>
          </div>

          <!-- Search Field -->
          <v-text-field
            v-model="searchQuery"
            placeholder="Cari judul / penulis..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            rounded="pill"
            hide-details
            clearable
            style="max-width: 230px;"
            class="stories-search-field"
            @update:model-value="debounceSearch"
          ></v-text-field>
        </div>
      </div>

      <!-- Loading State -->
      <v-row v-if="loading" dense>
        <v-col cols="12" sm="6" md="4" v-for="i in 6" :key="i">
          <v-skeleton-loader type="card" height="400" rounded="xl"></v-skeleton-loader>
        </v-col>
      </v-row>

      <!-- Stories Grid (2 columns on mobile 390px) -->
      <v-row v-else-if="stories.length > 0" dense>
        <v-col
          v-for="story in stories"
          :key="story.id"
          cols="6"
          sm="6"
          md="4"
        >
          <v-card
            rounded="xl"
            elevation="0"
            class="story-archive-card h-100 d-flex flex-column border"
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
                class="archive-img"
                cover
              >
                <div class="archive-pill">{{ story.author_name }}</div>
              </v-img>
              <div v-else class="archive-placeholder-banner pa-4 d-flex align-center justify-center">
                <v-icon size="32" color="white" style="opacity: 0.7;">mdi-book-open-page-variant</v-icon>
              </div>
            </div>

            <v-card-text class="pa-3 pa-sm-4 flex-grow-1 d-flex flex-column">
              <div class="text-caption text-grey-darken-1 mb-1" style="font-size: 0.68rem !important;">
                {{ formatDate(story.published_at) }}
              </div>
              <h3 class="card-title text-subtitle-2 text-sm-h6 font-weight-bold text-grey-darken-4 mb-1 archive-title">
                {{ story.title }}
              </h3>
              <p v-if="story.excerpt" class="text-caption text-grey-darken-2 mb-2 archive-excerpt d-none d-sm-block flex-grow-1">
                {{ story.excerpt }}
              </p>
              <div v-else class="flex-grow-1"></div>

              <div class="d-flex align-center justify-space-between pt-2 border-t text-caption text-primary font-weight-bold mt-auto">
                <span class="d-none d-sm-inline">Baca Selengkapnya</span>
                <span class="d-inline d-sm-none">Baca</span>
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
        <v-btn color="primary" rounded="pill" class="text-none" @click="handleOpenSubmitDialog">
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
import { useAuthGuard } from '~/composables/useAuthGuard'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'
import { defaultDummyStories } from '~/utils/dummyStories'

useSeoMeta({
  title: 'Cerita & Tulisan Jalan Bareng - Komunitas Pejalan Kaki',
  description: 'Kumpulan catatan lapangan, riset rute jalan kaki, dan cerita lorong perkotaan dari komunitas Jalan Bareng.',
})

const { api } = useApi()
const { requireAuth } = useAuthGuard()

const stories = ref<any[]>([])
const featuredStories = ref<any[]>([])
const loading = ref(true)
const searchQuery = ref('')
const sortOption = ref<'latest' | 'popular'>('latest')
const showSubmitDialog = ref(false)

const handleOpenSubmitDialog = () => {
  requireAuth({
    action: 'submit_story',
    title: 'Kirim Tulisan Pejalan Kaki',
    message: 'Masuk atau buat akun Jalan Bareng agar tulisanmu terhubung dengan profil penulis kontribusimu.',
    redirect: '/cerita',
    onSuccess: () => {
      showSubmitDialog.value = true
    },
  })
}

const setSort = (sort: 'latest' | 'popular') => {
  if (sortOption.value === sort) return
  sortOption.value = sort
  fetchStories()
}

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

const fetchFeatured = async () => {
  try {
    const res = await api.get('/stories', {
      params: { sort: 'popular', limit: 2 }
    })
    const fetched = res.data.stories || res.data.data || []
    if (fetched && fetched.length > 0) {
      featuredStories.value = fetched
    } else {
      featuredStories.value = [...defaultDummyStories].sort((a, b) => b.views_count - a.views_count).slice(0, 2)
    }
  } catch {
    featuredStories.value = [...defaultDummyStories].sort((a, b) => b.views_count - a.views_count).slice(0, 2)
  }
}

const fetchStories = async () => {
  loading.value = true
  try {
    const res = await api.get('/stories', {
      params: {
        q: searchQuery.value || undefined,
        sort: sortOption.value,
        per_page: 24,
      }
    })
    const fetched = res.data.stories || res.data.data || []
    if (fetched && fetched.length > 0) {
      stories.value = fetched
    } else {
      let dummy = filterFallbackStories(searchQuery.value)
      if (sortOption.value === 'popular') {
        dummy = [...dummy].sort((a, b) => b.views_count - a.views_count)
      }
      stories.value = dummy
    }
  } catch (err) {
    let dummy = filterFallbackStories(searchQuery.value)
    if (sortOption.value === 'popular') {
      dummy = [...dummy].sort((a, b) => b.views_count - a.views_count)
    }
    stories.value = dummy
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
  fetchFeatured()
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

.archive-img {
  height: 170px;
  width: 100%;
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
  top: 10px;
  left: 10px;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  color: white;
  padding: 3px 8px;
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

@media (max-width: 600px) {
  .stories-hero {
    padding-top: 24px;
    padding-bottom: 24px;
  }

  .hero-title {
    font-size: 1.95rem;
    line-height: 1.15;
  }

  .hero-subtitle {
    font-size: 0.85rem;
    line-height: 1.5;
    margin-bottom: 20px !important;
  }

  .story-archive-card {
    border-radius: 14px !important;
  }

  .archive-img {
    height: 105px !important;
  }

  .archive-pill {
    top: 6px;
    left: 6px;
    padding: 2px 6px;
    font-size: 0.625rem;
  }

  .archive-title {
    font-size: 0.825rem !important;
    line-height: 1.3 !important;
  }
}

.featured-top-card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.featured-top-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14);
}

.featured-top-img {
  position: relative;
}

.featured-top-overlay {
  height: 100%;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.85) 100%);
}

.top-badge-pill {
  padding: 3px 8px;
  background: #DC2626;
  color: #fff;
  border-radius: 9999px;
  font-size: 0.65rem;
  font-weight: 700;
}

.top-views-pill {
  padding: 2px 7px;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(4px);
  color: #fff;
  border-radius: 9999px;
  font-size: 0.65rem;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.35;
}

.sort-chip-btn {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 0.78rem;
  font-weight: 600;
  border: 1px solid #E2E8F0;
  background: #fff;
  color: #475569;
  cursor: pointer;
  transition: all 0.2s ease;
}

.sort-chip-btn.active {
  background: #FEF2F2;
  border-color: #DC2626;
  color: #DC2626;
}

/* Search field responsive — min-width hanya di sm+ agar tidak crowded di 320px */
.stories-search-field {
  min-width: 140px;
}

@media (min-width: 600px) {
  .stories-search-field {
    min-width: 170px;
  }
}
</style>
