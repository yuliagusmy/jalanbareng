<template>
  <div class="community-stories-section mb-12 mb-md-16">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 mb-md-8 ga-4">
      <div>
        <div class="d-flex align-center ga-2 mb-2">
          <span class="text-caption font-weight-bold text-uppercase tracking-wider text-primary">
            Publikasi & Catatan Lapangan
          </span>
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">
            Komunitas
          </v-chip>
        </div>
        <h2 class="text-h4 text-md-h3 section-headline stories-heading mb-2">
          Cerita & Kabar Jalan Bareng
        </h2>
        <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-0" style="max-width: 680px; line-height: 1.6;">
          Refleksi perjalanan, riset rute pejalan kaki, dan cerita lorong perkotaan yang ditulis langsung oleh warga.
        </p>
      </div>

      <div class="d-flex align-center ga-2 flex-wrap pt-1 pt-sm-0">
        <v-btn
          variant="outlined"
          color="primary"
          rounded="pill"
          :size="$vuetify.display.mobile ? 'default' : 'large'"
          class="px-4 font-weight-bold text-none"
          @click="handleOpenSubmit"
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
          class="px-3 font-weight-bold text-none"
        >
          <span>Lihat Semua</span>
          <v-icon end size="18">mdi-arrow-right</v-icon>
        </v-btn>
      </div>
    </div>

    <!-- Loading State -->
    <v-row v-if="loading" dense>
      <v-col cols="12" md="7">
        <v-skeleton-loader type="card" height="420" rounded="xl"></v-skeleton-loader>
      </v-col>
      <v-col cols="12" md="5">
        <div class="d-flex flex-column ga-3">
          <v-skeleton-loader v-for="i in 3" :key="i" type="list-item-avatar-three-line" rounded="xl"></v-skeleton-loader>
        </div>
      </v-col>
    </v-row>

    <!-- Editorial Featured Layout (1 Best Story + 3 Recent Stories) -->
    <v-row v-else-if="featuredStory" dense class="stories-editorial-row">
      <!-- Left: Featured Story (Paling Ramai / Pilihan) -->
      <v-col cols="12" md="7">
        <div
          class="featured-story-card h-100 cursor-pointer"
          @click="navigateTo(`/cerita/${featuredStory.slug}`)"
        >
          <v-img
            :src="featuredStory.cover_image_url || 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&fit=crop'"
            height="100%"
            min-height="340"
            cover
            class="featured-story-img"
          >
            <div class="featured-story-overlay d-flex flex-column justify-space-between pa-5 pa-sm-7">
              <!-- Top Badges -->
              <div class="d-flex align-center justify-space-between flex-wrap ga-2">
                <span class="badge-featured-pill">
                  <v-icon size="14" class="mr-1">mdi-star-four-points</v-icon>
                  Cerita Pilihan Komunitas
                </span>
                <span v-if="featuredStory.views_count" class="badge-views-pill">
                  <v-icon size="12" class="mr-1">mdi-eye-outline</v-icon>
                  {{ featuredStory.views_count }} dibaca
                </span>
              </div>

              <!-- Content Bottom -->
              <div class="featured-story-text">
                <div class="text-caption text-white opacity-80 mb-1">
                  {{ formatDate(featuredStory.published_at) }} • Oleh {{ featuredStory.author_name }}
                </div>
                <h3 class="featured-title text-white font-weight-black mb-2">
                  {{ featuredStory.title }}
                </h3>
                <p v-if="featuredStory.excerpt" class="featured-excerpt text-white opacity-90 mb-4 d-none d-sm-block">
                  {{ featuredStory.excerpt }}
                </p>
                <div class="d-flex align-center text-white font-weight-bold text-caption text-sm-body-2">
                  <span>Baca Tulisan Lengkap</span>
                  <v-icon end size="16">mdi-arrow-right</v-icon>
                </div>
              </div>
            </div>
          </v-img>
        </div>
      </v-col>

      <!-- Right: 3 Recent Stories Stack -->
      <v-col cols="12" md="5">
        <div class="recent-stories-stack d-flex flex-column ga-3 h-100 justify-space-between">
          <div
            v-for="story in recentStories"
            :key="story.id"
            class="recent-story-item pa-3 pa-sm-4 rounded-xl cursor-pointer"
            @click="navigateTo(`/cerita/${story.slug}`)"
          >
            <div class="d-flex ga-3 align-center">
              <v-img
                :src="story.cover_image_url || 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=400&fit=crop'"
                width="84"
                height="84"
                cover
                rounded="lg"
                class="flex-shrink-0"
              ></v-img>
              <div class="min-w-0 flex-grow-1">
                <div class="text-caption text-grey-darken-1 mb-1 d-flex align-center ga-1.5">
                  <span>{{ formatDate(story.published_at) }}</span>
                  <span>•</span>
                  <span class="text-truncate">{{ story.author_name }}</span>
                </div>
                <h4 class="recent-title text-subtitle-2 text-sm-subtitle-1 font-weight-bold text-grey-darken-4 line-clamp-2 mb-1">
                  {{ story.title }}
                </h4>
                <div class="text-caption text-primary font-weight-bold d-flex align-center">
                  <span>Baca cerita</span>
                  <v-icon size="12" class="ml-1">mdi-arrow-right</v-icon>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom View All Link Card -->
          <NuxtLink to="/cerita" class="more-stories-link pa-3 text-center rounded-xl d-block text-decoration-none">
            <span class="text-caption font-weight-bold text-primary">
              Lihat Seluruh Arsip Cerita Komunitas ({{ allStoriesCount || 4 }}+ Tulisan) →
            </span>
          </NuxtLink>
        </div>
      </v-col>
    </v-row>

    <!-- Compact Story Contribution Strip -->
    <div class="story-invite-strip mt-6">
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
          class="font-weight-bold px-4 invite-action-btn flex-shrink-0 text-none"
          @click="handleOpenSubmit"
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
import { useAuthGuard } from '~/composables/useAuthGuard'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'
import { defaultDummyStories } from '~/utils/dummyStories'

const { api } = useApi()
const { requireAuth } = useAuthGuard()

const featuredStory = ref<any>(null)
const recentStories = ref<any[]>([])
const allStoriesCount = ref(0)
const loading = ref(true)
const showSubmitDialog = ref(false)

const handleOpenSubmit = () => {
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
    const [popularRes, latestRes] = await Promise.all([
      api.get('/stories', { params: { sort: 'popular', limit: 1 } }),
      api.get('/stories', { params: { sort: 'latest', limit: 4 } })
    ])
    const popStories = popularRes.data.stories || popularRes.data.data || []
    const latStories = latestRes.data.stories || latestRes.data.data || []

    if (popStories.length > 0) {
      featuredStory.value = popStories[0]
      recentStories.value = latStories.filter((s: any) => s.id !== featuredStory.value.id).slice(0, 3)
      if (recentStories.value.length === 0) {
        recentStories.value = latStories.slice(1, 4)
      }
    } else if (latStories.length > 0) {
      featuredStory.value = latStories[0]
      recentStories.value = latStories.slice(1, 4)
    } else {
      featuredStory.value = defaultDummyStories[0]
      recentStories.value = defaultDummyStories.slice(1, 4)
    }
    allStoriesCount.value = (latStories.length || defaultDummyStories.length)
  } catch (err) {
    console.error('Failed to load community stories, using fallback:', err)
    featuredStory.value = defaultDummyStories[0]
    recentStories.value = defaultDummyStories.slice(1, 4)
    allStoriesCount.value = defaultDummyStories.length
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

.featured-story-card {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.15);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
  position: relative;
}

.featured-story-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 36px -10px rgba(0, 0, 0, 0.22);
}

.featured-story-img {
  position: relative;
}

.featured-story-overlay {
  height: 100%;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.88) 85%);
}

.badge-featured-pill {
  display: inline-flex;
  align-items: center;
  padding: 5px 12px;
  background: #DC2626;
  color: #fff;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.03em;
}

.badge-views-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  background: rgba(15, 23, 42, 0.65);
  backdrop-filter: blur(4px);
  color: #fff;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 600;
}

.featured-title {
  font-size: clamp(1.2rem, 2.5vw, 1.7rem);
  line-height: 1.25;
}

.featured-excerpt {
  font-size: 0.88rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.recent-story-item {
  background: #fff;
  border: 1px solid #f1f5f9;
  transition: all 0.2s ease;
}

.recent-story-item:hover {
  transform: translateX(4px);
  border-color: #cbd5e1;
  background: #fafafa;
}

.recent-title {
  line-height: 1.35;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.more-stories-link {
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  transition: all 0.2s ease;
}

.more-stories-link:hover {
  background: #f1f5f9;
  border-color: #DC2626;
}

.story-invite-strip {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  padding: 12px 16px;
}

.invite-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #fee2e2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.invite-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #1e293b;
  line-height: 1.3;
}

.invite-subtitle {
  font-size: 0.75rem;
  color: #64748b;
  line-height: 1.3;
}
</style>
