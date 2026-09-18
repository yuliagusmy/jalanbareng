<template>
  <div class="community-stories-section mb-12 mb-md-16">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 ga-3">
      <div>
        <div class="d-flex align-center ga-2 mb-1">
          <span class="text-caption font-weight-bold text-uppercase tracking-wider text-primary">
            Publikasi & Catatan Lapangan
          </span>
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">
            Komunitas
          </v-chip>
        </div>
        <h2 class="text-h4 text-md-h3 font-weight-bold text-grey-darken-4 stories-heading">
          Cerita Jalan Bareng
        </h2>
        <p class="text-body-2 text-md-body-1 text-grey-darken-1 mt-1 mb-0" style="max-width: 680px;">
          Kumpulan refleksi, riset jalur jalan kaki, dan cerita lorong perkotaan yang ditulis langsung oleh teman-teman pejalan dan dikurasi tim Jalan Bareng.
        </p>
      </div>

      <div class="d-flex align-center ga-2">
        <v-btn
          variant="outlined"
          color="primary"
          rounded="pill"
          size="large"
          class="px-5 font-weight-bold"
          @click="showSubmitDialog = true"
        >
          <v-icon start>mdi-pencil-plus-outline</v-icon>
          Kirim Tulisan
        </v-btn>
        <v-btn
          to="/cerita"
          variant="text"
          color="grey-darken-2"
          rounded="pill"
          size="large"
          class="px-4"
        >
          Lihat Semua
          <v-icon end>mdi-arrow-right</v-icon>
        </v-btn>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <v-row v-if="loading" dense>
      <v-col cols="12" sm="6" md="4" v-for="i in 3" :key="i">
        <v-skeleton-loader type="card" height="420" rounded="xl"></v-skeleton-loader>
      </v-col>
    </v-row>

    <!-- Cards Grid (Inspired by Kota Kita Updates) -->
    <v-row v-else-if="stories.length > 0" class="stories-grid">
      <v-col
        v-for="(story, index) in stories"
        :key="story.id"
        cols="6"
        sm="6"
        :md="getColSpan(index, stories.length)"
      >
        <div
          class="story-card"
          :class="`theme-${story.card_style || 'coral'}`"
          @click="navigateTo(`/cerita/${story.slug}`)"
        >
          <!-- Card Layout Option 1: Booklet / Report Card (Coral & Magenta) -->
          <template v-if="story.card_style === 'coral' || story.card_style === 'magenta'">
            <div class="card-inner-report">
              <!-- Book / Booklet Mockup Center -->
              <div class="book-mockup-wrapper">
                <div class="book-mockup">
                  <div class="book-spine"></div>
                  <div class="book-cover">
                    <div class="book-badge">
                      <v-icon size="small" color="white">mdi-map-marker-path</v-icon>
                      <span>Jalan Bareng</span>
                    </div>
                    <div class="book-cover-title">{{ story.title }}</div>
                    <div class="book-cover-footer">
                      <span>{{ story.author_name }}</span>
                      <span>{{ formatDate(story.published_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Card Title & Excerpt at Bottom -->
              <div class="card-text-container">
                <h3 class="card-headline text-white">{{ story.title }}</h3>
                <p class="card-subtext text-white" v-if="story.excerpt">
                  {{ story.excerpt }}
                </p>
                <div class="card-footer-meta text-white">
                  <span class="author-label">Oleh {{ story.author_name }}</span>
                  <span class="read-more-link">
                    Baca Selengkapnya
                    <v-icon size="small">mdi-arrow-right</v-icon>
                  </span>
                </div>
              </div>
            </div>
          </template>

          <!-- Card Layout Option 2: Photo with Amber / Colored Lower Banner (Amber & Dark) -->
          <template v-else-if="story.card_style === 'amber'">
            <div class="card-inner-photo-split">
              <!-- Photo Top Section -->
              <div class="card-photo-box">
                <v-img
                  :src="story.cover_image_url || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&fit=crop'"
                  height="220"
                  cover
                  class="card-img-top"
                >
                  <div class="photo-category-pill">Catatan Lapangan</div>
                </v-img>
              </div>

              <!-- Amber Solid Lower Section -->
              <div class="card-amber-lower">
                <h3 class="card-headline text-white">{{ story.title }}</h3>
                <p class="card-subtext text-white" v-if="story.excerpt">
                  {{ story.excerpt }}
                </p>
                <div class="card-footer-meta text-white">
                  <span class="author-label">Oleh {{ story.author_name }}</span>
                  <span class="read-more-link">
                    Baca Cerita
                    <v-icon size="small">mdi-arrow-right</v-icon>
                  </span>
                </div>
              </div>
            </div>
          </template>

          <!-- Card Layout Option 3: Full Photo Card with Gradient Overlay -->
          <template v-else>
            <div class="card-inner-photo-full">
              <v-img
                :src="story.cover_image_url || 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&fit=crop'"
                height="100%"
                cover
                class="full-bg-image"
              >
                <div class="full-photo-overlay">
                  <div class="photo-category-pill mb-auto">Cerita Perjalanan</div>
                  <div class="card-text-container">
                    <h3 class="card-headline text-white">{{ story.title }}</h3>
                    <p class="card-subtext text-white" v-if="story.excerpt">
                      {{ story.excerpt }}
                    </p>
                    <div class="card-footer-meta text-white">
                      <span class="author-label">Oleh {{ story.author_name }}</span>
                      <span class="read-more-link">
                        Baca Cerita
                        <v-icon size="small">mdi-arrow-right</v-icon>
                      </span>
                    </div>
                  </div>
                </div>
              </v-img>
            </div>
          </template>
        </div>
      </v-col>
    </v-row>

    <!-- Empty State -->
    <v-card v-else variant="tonal" rounded="xl" class="pa-8 text-center bg-grey-lighten-4">
      <v-icon size="48" color="grey" class="mb-2">mdi-book-open-blank-variant</v-icon>
      <div class="text-h6 font-weight-bold text-grey-darken-3">Belum ada tulisan yang dipublikasikan</div>
      <p class="text-body-2 text-grey-darken-1 mb-4">Jadilah yang pertama membagikan cerita jelajahmu!</p>
      <v-btn color="primary" rounded="pill" @click="showSubmitDialog = true">
        Kirim Tulisan Sekarang
      </v-btn>
    </v-card>

    <!-- SUBMIT STORY CTA BANNER (Inserted Underneath Cards) -->
    <v-card
      elevation="0"
      rounded="xl"
      class="mt-8 pa-6 pa-md-8 registration-cta-banner"
    >
      <v-row align="center" justify="space-between">
        <v-col cols="12" lg="7" md="7">
          <div class="d-flex align-center ga-3 mb-2">
            <v-avatar color="white" size="42" class="elevation-2">
              <v-icon color="#2563EB" size="24">mdi-feather</v-icon>
            </v-avatar>
            <span class="text-subtitle-2 font-weight-bold text-white text-uppercase tracking-wider">
              Ruang Kontribusi Pejalan
            </span>
          </div>
          <h3 class="text-h5 text-md-h4 font-weight-bold text-white mb-2 banner-heading">
            Punya Catatan & Cerita Menarik dari Trotoar Kotamu?
          </h3>
          <p class="text-body-1 text-white opacity-90 mb-0" style="max-width: 580px; line-height: 1.6;">
            Bagikan refleksi jalan santai, kisah lorong kota, atau catatan riset pejalan kakimu. Setiap tulisan yang masuk akan dikurasi dan dipublikasikan di kanal Cerita Jalan Bareng.
          </p>
        </v-col>

        <v-col cols="12" lg="5" md="5" class="text-md-right mt-4 mt-md-0">
          <div class="cta-btn-group justify-start justify-md-end">
            <v-btn
              color="white"
              size="large"
              rounded="pill"
              class="font-weight-bold px-6 text-primary elevation-4 cta-reg-btn"
              @click="showSubmitDialog = true"
            >
              <v-icon start size="20">mdi-feather</v-icon>
              Submit Tulisan Sekarang
            </v-btn>

            <v-btn
              to="/cerita"
              variant="outlined"
              color="white"
              size="large"
              rounded="pill"
              class="font-weight-bold px-5"
            >
              <v-icon start size="18">mdi-book-open-page-variant-outline</v-icon>
              Baca Arsip Cerita
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-card>

    <!-- Dialog Submit Story -->
    <SubmitStoryDialog
      v-model="showSubmitDialog"
      @submitted="fetchStories"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from '~/stores/auth'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'
import { defaultDummyStories } from '~/utils/dummyStories'

const { api } = useApi()
const authStore = useAuthStore()

const stories = ref<any[]>([])
const loading = ref(true)
const showSubmitDialog = ref(false)

const getColSpan = (index: number, total: number) => {
  if (total === 1) return 12
  if (total === 2) return 6
  // If index is 0 or 1, maybe 6 or 4
  return 4
}

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
      params: { limit: 3 }
    })
    const fetched = res.data.stories || res.data.data || []
    if (fetched && fetched.length > 0) {
      stories.value = fetched
    } else {
      stories.value = defaultDummyStories.slice(0, 3)
    }
  } catch (err) {
    console.error('Failed to load community stories from API, using fallback:', err)
    stories.value = defaultDummyStories.slice(0, 3)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchStories()
})
</script>

<style scoped>
.stories-heading {
  letter-spacing: -0.5px;
}

/* Card Container */
.story-card {
  height: 480px;
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  cursor: pointer;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}

.story-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 35px -5px rgba(0, 0, 0, 0.2), 0 10px 15px -5px rgba(0, 0, 0, 0.1);
}

/* Color Themes */
.theme-coral {
  background: linear-gradient(180deg, #FA4D56 0%, #D82631 100%);
}

.theme-magenta {
  background: linear-gradient(180deg, #E02F6B 0%, #B81B50 100%);
}

.theme-amber {
  background: #F4A228;
}

.theme-photo, .theme-dark {
  background: #1e293b;
}

/* Report Card Internal Layout */
.card-inner-report {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 24px;
}

/* Realistic Book / Booklet Mockup */
.book-mockup-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 0 20px;
}

.book-mockup {
  position: relative;
  width: 170px;
  height: 220px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 6px 12px 12px 6px;
  box-shadow:
    -5px 0 0 rgba(0, 0, 0, 0.2),
    0 15px 30px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.35);
  transition: transform 0.35s ease;
  overflow: hidden;
}

.story-card:hover .book-mockup {
  transform: scale(1.04) rotate(-1deg);
}

.book-spine {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 10px;
  background: linear-gradient(90deg, rgba(0,0,0,0.25) 0%, rgba(255,255,255,0.2) 50%, rgba(0,0,0,0.15) 100%);
}

.book-cover {
  padding: 16px 14px 14px 20px;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.book-badge {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.9);
  letter-spacing: 0.5px;
}

.book-cover-title {
  font-size: 0.85rem;
  font-weight: 800;
  color: #ffffff;
  line-height: 1.25;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.book-cover-footer {
  display: flex;
  flex-direction: column;
  font-size: 0.65rem;
  color: rgba(255, 255, 255, 0.8);
  border-top: 1px solid rgba(255, 255, 255, 0.3);
  padding-top: 6px;
}

/* Card Text Section */
.card-text-container {
  margin-top: auto;
}

.card-headline {
  font-size: 1.15rem;
  font-weight: 800;
  line-height: 1.35;
  margin-bottom: 8px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-subtext {
  font-size: 0.825rem;
  line-height: 1.45;
  opacity: 0.9;
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-footer-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 0.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.25);
  padding-top: 10px;
}

.author-label {
  font-weight: 600;
  opacity: 0.9;
}

.read-more-link {
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: gap 0.2s ease;
}

.story-card:hover .read-more-link {
  gap: 8px;
}

/* Split Photo + Amber Layout */
.card-inner-photo-split {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.card-photo-box {
  position: relative;
  height: 220px;
  overflow: hidden;
}

.card-img-top {
  transition: transform 0.4s ease;
}

.story-card:hover .card-img-top {
  transform: scale(1.06);
}

.card-amber-lower {
  flex: 1;
  background: #E89A21;
  padding: 20px 22px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.photo-category-pill {
  position: absolute;
  top: 14px;
  left: 14px;
  background: rgba(0, 0, 0, 0.65);
  backdrop-filter: blur(4px);
  color: white;
  padding: 4px 10px;
  border-radius: 30px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Full Photo Card */
.card-inner-photo-full {
  height: 100%;
}

.full-bg-image {
  height: 100%;
  transition: transform 0.4s ease;
}

.story-card:hover .full-bg-image {
  transform: scale(1.05);
}

.full-photo-overlay {
  height: 100%;
  background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.85) 75%, rgba(0,0,0,0.95) 100%);
  padding: 22px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Registration CTA Banner */
.registration-cta-banner {
  background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #4F46E5 100%);
  box-shadow: 0 15px 35px -5px rgba(37, 99, 235, 0.35);
  position: relative;
  overflow: hidden;
}

.registration-cta-banner::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -20%;
  width: 350px;
  height: 350px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
  pointer-events: none;
}

.banner-heading {
  word-break: break-word;
  overflow-wrap: break-word;
  line-height: 1.3;
}

.cta-btn-group {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
}

.cta-reg-btn {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cta-reg-btn:hover {
  transform: scale(1.03);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
}

/* Mobile 2-column layout refinements */
@media (max-width: 600px) {
  .story-card {
    height: 285px;
    border-radius: 16px;
  }

  .card-inner-report {
    padding: 12px 10px;
  }

  .book-mockup-wrapper {
    transform: scale(0.65);
    transform-origin: center center;
    margin-top: -12px;
    margin-bottom: -18px;
  }

  .card-headline {
    font-size: 0.8rem !important;
    line-height: 1.25 !important;
    margin-bottom: 4px !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-subtext {
    display: none !important;
  }

  .card-footer-meta {
    font-size: 0.68rem !important;
    margin-top: 4px;
    padding-top: 4px;
  }

  .author-label {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-width: 80px;
  }

  .read-more-link {
    font-size: 0.68rem !important;
  }

  .read-more-link :deep(.v-icon) {
    font-size: 14px !important;
  }

  .card-inner-photo-compact {
    padding: 10px;
  }

  .compact-photo-wrapper {
    height: 110px;
    margin-bottom: 8px;
    border-radius: 10px;
  }

  .photo-headline {
    font-size: 0.8rem !important;
    line-height: 1.25 !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .photo-subtext {
    display: none !important;
  }

  .photo-footer-meta {
    font-size: 0.68rem !important;
  }
}
</style>
