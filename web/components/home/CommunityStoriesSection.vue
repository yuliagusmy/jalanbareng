<template>
  <div class="community-stories-section mb-12 mb-md-16">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 mb-md-8 ga-4">
      <div>
        <div class="d-flex align-center ga-2 mb-2 mb-md-2.5">
          <span class="text-caption font-weight-bold text-uppercase tracking-wider text-primary">
            Publikasi & Catatan Lapangan
          </span>
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">
            Komunitas
          </v-chip>
        </div>
        <h2 class="text-h4 text-md-h3 section-headline stories-heading mb-3 mb-md-4">
          Cerita Jalan Bareng
        </h2>
        <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-2 mb-sm-0" style="max-width: 680px; line-height: 1.6;">
          Kumpulan refleksi, riset jalur jalan kaki, dan cerita lorong perkotaan yang ditulis langsung oleh teman-teman pejalan dan dikurasi tim Jalan Bareng.
        </p>
      </div>

      <div class="d-flex align-center ga-2 flex-wrap pt-1 pt-sm-0">
        <v-btn
          variant="outlined"
          color="primary"
          rounded="pill"
          :size="$vuetify.display.mobile ? 'default' : 'large'"
          class="px-4 font-weight-bold"
          @click="showSubmitDialog = true"
        >
          <v-icon start size="18">mdi-pencil-plus-outline</v-icon>
          <span>Kirim Tulisan</span>
        </v-btn>
        <v-btn
          to="/cerita"
          variant="text"
          color="grey-darken-2"
          rounded="pill"
          :size="$vuetify.display.mobile ? 'default' : 'large'"
          class="px-3 font-weight-bold"
        >
          <span>Lihat Semua</span>
          <v-icon end size="18">mdi-arrow-right</v-icon>
        </v-btn>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="stories-scroll-container">
      <div v-for="i in 4" :key="i" class="story-scroll-item">
        <v-skeleton-loader type="card" height="420" rounded="xl"></v-skeleton-loader>
      </div>
    </div>

    <!-- Cards Scroll Row (Side-by-side with horizontal scroll) -->
    <div v-else-if="stories.length > 0" class="stories-scroll-container">
      <div
        v-for="story in stories"
        :key="story.id"
        class="story-scroll-item"
      >
        <div
          class="story-card story-card-photo-full"
          @click="navigateTo(`/cerita/${story.slug}`)"
        >
          <!-- Full Photo Card with Gradient Overlay (Card 4 Style) -->
          <div class="card-inner-photo-full">
            <v-img
              :src="story.cover_image_url || 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&fit=crop'"
              height="100%"
              cover
              class="full-bg-image"
            >
              <div class="full-photo-overlay">
                <div class="photo-category-pill mb-auto">{{ getStoryCategory(story) }}</div>
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
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <v-card v-else variant="tonal" rounded="xl" class="pa-8 text-center bg-grey-lighten-4">
      <v-icon size="48" color="grey" class="mb-2">mdi-book-open-blank-variant</v-icon>
      <div class="text-h6 font-weight-bold text-grey-darken-3">Belum ada tulisan yang dipublikasikan</div>
      <p class="text-body-2 text-grey-darken-1 mb-4">Jadilah yang pertama membagikan cerita jelajahmu!</p>
      <v-btn color="primary" rounded="pill" @click="showSubmitDialog = true">
        Kirim Tulisan Sekarang
      </v-btn>
    </v-card>

    <!-- Compact Story Contribution Strip -->
    <div class="story-invite-strip mt-5">
      <div class="d-flex align-center justify-space-between ga-3 flex-nowrap">
        <div class="d-flex align-center ga-3 min-w-0">
          <div class="invite-icon-box">
            <v-icon color="#DC2626" size="20">mdi-feather</v-icon>
          </div>
          <div class="invite-text-block min-w-0">
            <div class="invite-title">Punya cerita dari trotoar kotamu?</div>
            <div class="invite-subtitle">Bagikan refleksi & kisah pejalan kakimu</div>
          </div>
        </div>

        <v-btn
          color="primary"
          rounded="pill"
          size="small"
          class="font-weight-bold px-4 invite-action-btn flex-shrink-0"
          @click="showSubmitDialog = true"
        >
          <v-icon start size="15" class="d-none d-sm-inline">mdi-pencil-plus-outline</v-icon>
          <span>Kirim Tulisan</span>
        </v-btn>
      </div>
    </div>

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

const getStoryCategory = (story: any) => {
  if (story.category) return story.category
  const t = (story.title || '').toLowerCase()
  if (t.includes('report') || t.includes('riset') || t.includes('panduan')) return 'Riset & Advokasi'
  if (t.includes('disabilitas') || t.includes('inklusi') || t.includes('akses')) return 'Inklusi Kota'
  if (t.includes('panas') || t.includes('lorong') || t.includes('iklim') || t.includes('hijau')) return 'Catatan Lingkungan'
  if (t.includes('denyut') || t.includes('sejarah') || t.includes('kota tua')) return 'Cerita Perjalanan'
  return 'Cerita Komunitas'
}

const fetchStories = async () => {
  loading.value = true
  try {
    const res = await api.get('/stories', {
      params: { limit: 4 }
    })
    const fetched = res.data.stories || res.data.data || []
    if (fetched && fetched.length > 0) {
      stories.value = fetched
    } else {
      stories.value = defaultDummyStories.slice(0, 4)
    }
  } catch (err) {
    console.error('Failed to load community stories from API, using fallback:', err)
    stories.value = defaultDummyStories.slice(0, 4)
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
  font-weight: 800 !important;
  letter-spacing: -0.035em !important;
  line-height: 1.2 !important;
}

/* Scroll Container */
.stories-scroll-container {
  display: flex;
  overflow-x: auto;
  gap: 16px;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  padding: 8px 4px 20px;
  margin: 0 -4px;
}

.stories-scroll-container::-webkit-scrollbar {
  height: 6px;
}

.stories-scroll-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.04);
  border-radius: 9999px;
}

.stories-scroll-container::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.15);
  border-radius: 9999px;
}

.stories-scroll-container::-webkit-scrollbar-thumb:hover {
  background: rgba(220, 38, 38, 0.4);
}

.story-scroll-item {
  flex: 0 0 320px;
  width: 320px;
  scroll-snap-align: start;
}

@media (max-width: 600px) {
  .story-scroll-item {
    flex: 0 0 260px;
    width: 260px;
  }
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

/* Compact Story Contribution Strip */
.story-invite-strip {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  padding: 12px 18px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
  transition: all 0.2s ease;
}

.story-invite-strip:hover {
  border-color: #CBD5E1;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.invite-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.invite-text-block {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.invite-title {
  font-size: 0.92rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.3;
}

.invite-subtitle {
  font-size: 0.76rem;
  color: #64748B;
  line-height: 1.3;
}

.invite-action-btn {
  text-transform: none;
  letter-spacing: 0.01em;
  height: 34px !important;
}

/* Mobile 1-column layout refinements */
@media (max-width: 600px) {
  .story-card {
    height: 310px;
    border-radius: 16px;
  }

  .full-photo-overlay {
    padding: 16px 14px;
  }

  .story-invite-strip {
    padding: 10px 12px;
    border-radius: 14px;
    margin-top: 14px !important;
  }

  .invite-icon-box {
    width: 32px;
    height: 32px;
    border-radius: 8px;
  }

  .invite-title {
    font-size: 0.8rem;
    font-weight: 700;
    line-height: 1.25;
    color: #111827;
  }

  .invite-subtitle {
    font-size: 0.68rem;
    color: #64748B;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .invite-action-btn {
    height: 32px !important;
    font-size: 0.74rem !important;
    padding: 0 12px !important;
  }

  .card-inner-report {
    padding: 14px 12px;
  }

  .book-mockup-wrapper {
    transform: scale(0.62);
    transform-origin: center center;
    margin-top: -12px;
    margin-bottom: -8px;
  }

  .card-headline {
    font-size: 1.05rem !important;
    line-height: 1.35 !important;
    margin-bottom: 6px !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-subtext {
    display: -webkit-box !important;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: 0.8rem !important;
    line-height: 1.45 !important;
    margin-bottom: 8px !important;
  }

  .card-footer-meta {
    font-size: 0.78rem !important;
    margin-top: auto;
    padding-top: 8px;
  }

  .author-label {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-width: 140px;
  }

  .read-more-link {
    font-size: 0.78rem !important;
  }

  .read-more-link :deep(.v-icon) {
    font-size: 16px !important;
  }

  .card-inner-photo-compact {
    padding: 16px;
  }

  .compact-photo-wrapper {
    height: 160px;
    margin-bottom: 12px;
    border-radius: 12px;
  }

  .photo-headline {
    font-size: 0.95rem !important;
    line-height: 1.35 !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .photo-subtext {
    display: none !important;
  }

  .photo-footer-meta {
    font-size: 0.75rem !important;
  }
}

/* Tablet (601px - 959px) refinements for 2-column layout */
@media (min-width: 601px) and (max-width: 959px) {
  .story-card {
    height: 420px;
  }

  .book-mockup-wrapper {
    transform: scale(0.9);
  }
}
</style>
