<template>
  <div class="story-detail-page">
    <!-- Loading State -->
    <v-container v-if="loading" class="py-16 text-center">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      <p class="text-body-1 text-grey mt-4">Memuat tulisan...</p>
    </v-container>

    <!-- Error State -->
    <v-container v-else-if="!story" class="py-16 text-center">
      <v-icon size="64" color="grey" class="mb-4">mdi-file-remove-outline</v-icon>
      <h2 class="text-h5 font-weight-bold text-grey-darken-3 mb-2">Tulisan Tidak Ditemukan</h2>
      <p class="text-body-2 text-grey-darken-1 mb-6">
        Tulisan yang Anda cari mungkin sedang dalam proses kurasi atau tautan tidak valid.
      </p>
      <v-btn to="/cerita" color="primary" rounded="pill">
        Kembali ke Cerita Jalan Bareng
      </v-btn>
    </v-container>

    <!-- Story Content -->
    <div v-else>
      <!-- Hero Header with Dynamic Background -->
      <section
        class="story-hero-section"
        :class="`theme-${story.card_style || 'coral'}`"
      >
        <v-container class="py-10 py-md-16">
          <!-- Back Link -->
          <v-btn
            to="/"
            variant="text"
            color="white"
            class="mb-6 px-0 font-weight-bold"
          >
            <v-icon start>mdi-arrow-left</v-icon>
            Kembali ke Beranda
          </v-btn>

          <v-row align="center">
            <v-col cols="12" md="8">
              <v-chip size="small" color="white" variant="flat" class="font-weight-bold mb-4 text-uppercase">
                Catatan Jalan Bareng
              </v-chip>
              <h1 class="text-h4 text-sm-h3 text-md-h2 font-weight-bold text-white mb-4 story-title">
                {{ story.title }}
              </h1>

              <!-- Author & Date Meta -->
              <div class="d-flex flex-wrap align-center ga-4 text-white opacity-90 text-body-2">
                <div class="d-flex align-center ga-2">
                  <v-avatar size="32" color="white">
                    <v-icon color="primary" size="small">mdi-account</v-icon>
                  </v-avatar>
                  <span class="font-weight-bold">{{ story.author_name }}</span>
                </div>
                <span>•</span>
                <div class="d-flex align-center ga-1">
                  <v-icon size="small">mdi-calendar</v-icon>
                  <span>{{ formatDate(story.published_at) }}</span>
                </div>
                <span>•</span>
                <div class="d-flex align-center ga-1">
                  <v-icon size="small">mdi-eye</v-icon>
                  <span>{{ story.views_count }} dibaca</span>
                </div>
              </div>
            </v-col>

            <!-- Optional Cover Photo / Illustration -->
            <v-col cols="12" md="4" v-if="story.cover_image_url" class="d-none d-md-block text-center">
              <v-img
                :src="story.cover_image_url"
                max-height="280"
                rounded="xl"
                cover
                class="elevation-8 border-sm border-white"
              ></v-img>
            </v-col>
          </v-row>
        </v-container>
      </section>

      <!-- Main Reading Container -->
      <v-container class="py-10 py-md-14">
        <v-row justify="center">
          <v-col cols="12" md="8" lg="7">
            <!-- Excerpt Highlight -->
            <div v-if="story.excerpt" class="story-excerpt-box mb-8 pa-6 pa-md-8">
              <v-icon color="primary" size="32" class="mb-2">mdi-format-quote-open</v-icon>
              <p class="text-h6 text-md-h5 font-weight-medium text-grey-darken-3 mb-0 font-italic" style="line-height: 1.5;">
                {{ story.excerpt }}
              </p>
            </div>

            <!-- Mobile Cover Image -->
            <div v-if="story.cover_image_url" class="d-md-none mb-8">
              <v-img
                :src="story.cover_image_url"
                max-height="300"
                cover
                rounded="xl"
                class="elevation-4"
              ></v-img>
            </div>

            <!-- Full Article Content -->
            <article class="story-article-body text-body-1 mb-12" v-html="story.content"></article>

            <v-divider class="my-8"></v-divider>

            <!-- Share Buttons & Tags -->
            <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center ga-4 mb-10">
              <div>
                <span class="text-subtitle-2 font-weight-bold text-grey-darken-2 d-block mb-1">
                  Bagikan Tulisan Ini:
                </span>
                <div class="d-flex ga-2">
                  <v-btn
                    color="#25D366"
                    variant="flat"
                    size="small"
                    rounded="pill"
                    class="text-white"
                    @click="shareWhatsApp"
                  >
                    <v-icon start>mdi-whatsapp</v-icon>
                    WhatsApp
                  </v-btn>
                  <v-btn
                    variant="outlined"
                    size="small"
                    rounded="pill"
                    @click="copyShareLink"
                  >
                    <v-icon start>mdi-link-variant</v-icon>
                    {{ copied ? 'Tersalin!' : 'Salin Tautan' }}
                  </v-btn>
                </div>
              </div>

              <!-- Author Instagram / Bio -->
              <div v-if="story.author_instagram" class="d-flex align-center ga-2">
                <v-icon color="pink">mdi-instagram</v-icon>
                <a
                  :href="`https://instagram.com/${story.author_instagram}`"
                  target="_blank"
                  rel="noopener"
                  class="text-body-2 font-weight-bold text-decoration-none text-primary"
                >
                  @{{ story.author_instagram }}
                </a>
              </div>
            </div>

            <!-- Author Profile Card -->
            <v-card rounded="xl" variant="flat" class="pa-6 bg-grey-lighten-4 mb-12">
              <div class="d-flex align-center ga-4">
                <v-avatar size="56" color="primary" class="elevation-2">
                  <v-icon size="32" color="white">mdi-fountain-pen-tip</v-icon>
                </v-avatar>
                <div>
                  <div class="text-caption text-uppercase font-weight-bold text-grey-darken-1">Penulis Kontributor</div>
                  <h4 class="text-h6 font-weight-bold text-grey-darken-4">{{ story.author_name }}</h4>
                  <p class="text-body-2 text-grey-darken-2 mb-0" v-if="story.author_bio">
                    {{ story.author_bio }}
                  </p>
                </div>
              </div>
            </v-card>

            <!-- Bottom CTA: Join Community & Submit Story -->
            <v-card
              rounded="xl"
              elevation="0"
              class="pa-8 text-center"
              style="background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%); color: white;"
            >
              <v-icon size="48" color="white" class="mb-3">mdi-account-group</v-icon>
              <h3 class="text-h5 font-weight-bold mb-2">Ingin Berbagi Pengalaman Jelajahmu?</h3>
              <p class="text-body-2 mb-6 mx-auto" style="max-width: 500px; opacity: 0.95;">
                Komunitas Jalan Bareng terbuka untuk catatan perjalanan, rute jalan kaki, riset ruang publik, dan rekomendasi sudut kota darimu.
              </p>
              <div class="d-flex flex-column flex-sm-row justify-center ga-3">
                <v-btn
                  v-if="!authStore.isLoggedIn"
                  to="/register"
                  color="white"
                  variant="flat"
                  rounded="pill"
                  size="large"
                  class="text-primary font-weight-bold px-6"
                >
                  Daftar di Jalan Bareng
                </v-btn>
                <v-btn
                  color="white"
                  variant="outlined"
                  rounded="pill"
                  size="large"
                  class="font-weight-bold px-6"
                  @click="showSubmitDialog = true"
                >
                  <v-icon start>mdi-feather</v-icon>
                  Kirim Tulisan Anda
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <!-- Submit Story Dialog -->
    <SubmitStoryDialog v-model="showSubmitDialog" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from '~/stores/auth'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'

const route = useRoute()
const { api } = useApi()
const authStore = useAuthStore()

const story = ref<any>(null)
const related = ref<any[]>([])
const loading = ref(true)
const copied = ref(false)
const showSubmitDialog = ref(false)

const formatDate = (dateStr: string) => {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const fetchStory = async () => {
  loading.value = true
  try {
    const slug = route.params.slug
    const res = await api.get(`/stories/${slug}`)
    story.value = res.data.story
    related.value = res.data.related || []

    if (story.value) {
      useSeoMeta({
        title: `${story.value.title} - Jalan Bareng`,
        description: story.value.excerpt || story.value.title,
        ogTitle: story.value.title,
        ogDescription: story.value.excerpt || story.value.title,
        ogImage: story.value.cover_image_url || undefined,
      })
    }
  } catch (err) {
    console.error('Failed to load story:', err)
  } finally {
    loading.value = false
  }
}

const copyShareLink = () => {
  if (process.client) {
    navigator.clipboard.writeText(window.location.href)
    copied.value = true
    setTimeout(() => {
      copied.value = false
    }, 2500)
  }
}

const shareWhatsApp = () => {
  if (process.client && story.value) {
    const text = encodeURIComponent(`Baca tulisan "${story.value.title}" di Jalan Bareng: ${window.location.href}`)
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank')
  }
}

onMounted(() => {
  fetchStory()
})
</script>

<style scoped>
.story-hero-section {
  position: relative;
  overflow: hidden;
}

.theme-coral {
  background: linear-gradient(135deg, #FA4D56 0%, #D82631 100%);
}

.theme-magenta {
  background: linear-gradient(135deg, #E02F6B 0%, #B81B50 100%);
}

.theme-amber {
  background: linear-gradient(135deg, #F4A228 0%, #D97706 100%);
}

.theme-photo, .theme-dark {
  background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
}

.story-title {
  line-height: 1.25;
  letter-spacing: -0.5px;
}

.story-excerpt-box {
  background-color: #F8FAFC;
  border-left: 5px solid #1976D2;
  border-radius: 0 16px 16px 0;
}

.story-article-body {
  font-size: 1.125rem;
  line-height: 1.85;
  color: #334155;
}

.story-article-body :deep(h2),
.story-article-body :deep(h3) {
  margin-top: 2rem;
  margin-bottom: 1rem;
  font-weight: 700;
  color: #0F172A;
}

.story-article-body :deep(p) {
  margin-bottom: 1.25rem;
}

.story-article-body :deep(ul),
.story-article-body :deep(ol) {
  margin-bottom: 1.25rem;
  padding-left: 1.5rem;
}

.story-article-body :deep(li) {
  margin-bottom: 0.5rem;
}

.story-article-body :deep(img) {
  max-width: 100%;
  border-radius: 12px;
  margin: 1.5rem 0;
}
</style>
