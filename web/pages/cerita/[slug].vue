<template>
  <div class="story-detail-page">
    <!-- Reading Progress Bar -->
    <div class="reading-progress-bar" :style="{ width: readingProgress + '%' }" aria-hidden="true"></div>

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
    <article v-else>

      <!-- ══ Full-Width Hero ══ -->
      <section class="story-hero" :class="`theme-${story.card_style || 'coral'}`">
        <!-- Cover photo overlay -->
        <div
          v-if="story.cover_image_url"
          class="hero-cover-img"
          :style="{ backgroundImage: `url(${story.cover_image_url})` }"
          aria-hidden="true"
        ></div>
        <div class="hero-overlay" aria-hidden="true"></div>

        <v-container class="hero-inner">
          <!-- Breadcrumb -->
          <nav class="hero-breadcrumb mb-6" aria-label="Navigasi">
            <nuxt-link to="/" class="bc-link">Beranda</nuxt-link>
            <v-icon size="14" class="mx-1 opacity-70">mdi-chevron-right</v-icon>
            <nuxt-link to="/cerita" class="bc-link">Cerita</nuxt-link>
            <v-icon size="14" class="mx-1 opacity-70">mdi-chevron-right</v-icon>
            <span class="bc-current">{{ story.title }}</span>
          </nav>

          <v-chip size="small" color="white" variant="flat" class="font-weight-bold mb-5 text-uppercase tracking-wider"
            style="color: #DC2626; font-size: 0.7rem; letter-spacing: 0.1em;">
            Catatan Jalan Bareng
          </v-chip>

          <h1 class="hero-title mb-6">{{ story.title }}</h1>

          <!-- Meta row -->
          <div class="d-flex flex-wrap align-center ga-3 ga-sm-5 hero-meta">
            <div class="d-flex align-center ga-2">
              <v-avatar size="36" color="white" class="elevation-2">
                <v-icon color="primary" size="20">mdi-fountain-pen-tip</v-icon>
              </v-avatar>
              <div>
                <div class="text-white font-weight-bold text-body-2">{{ story.author_name }}</div>
                <div class="text-white text-caption opacity-75">Kontributor</div>
              </div>
            </div>

            <div class="hero-meta-divider d-none d-sm-block"></div>

            <div class="d-flex align-center ga-4 text-white opacity-85 text-body-2">
              <div class="d-flex align-center ga-1">
                <v-icon size="16">mdi-calendar-outline</v-icon>
                <span>{{ formatDate(story.published_at) }}</span>
              </div>
              <div class="d-flex align-center ga-1">
                <v-icon size="16">mdi-clock-outline</v-icon>
                <span>{{ readingTime }} mnt baca</span>
              </div>
              <div class="d-flex align-center ga-1">
                <v-icon size="16">mdi-eye-outline</v-icon>
                <span>{{ story.views_count?.toLocaleString('id') }} dibaca</span>
              </div>
            </div>
          </div>
        </v-container>
      </section>

      <!-- ══ Main Content ══ -->
      <v-container class="article-container py-8 py-md-14">
        <v-row justify="center">
          <v-col cols="12" md="8" lg="7">

            <!-- Pull Quote / Excerpt -->
            <div v-if="story.excerpt" class="pull-quote mb-10">
              <v-icon color="primary" size="28" class="mb-2">mdi-format-quote-open</v-icon>
              <p class="text-h6 font-italic text-grey-darken-3" style="line-height: 1.6;">{{ story.excerpt }}</p>
            </div>

            <!-- Article body -->
            <div class="article-body" v-html="story.content" ref="articleEl"></div>

            <!-- ─ Divider ─ -->
            <v-divider class="my-10"></v-divider>

            <!-- Like + Share row -->
            <div class="d-flex flex-column flex-sm-row align-start align-sm-center justify-space-between ga-4 mb-10">
              <div class="d-flex align-center ga-3">
                <StoryLikeButton
                  v-if="story.id"
                  :story-id="story.id"
                  :initial-likes-count="story.likes_count ?? 0"
                />
                <div class="d-flex align-center ga-1 text-caption text-grey">
                  <v-icon size="14">mdi-comment-outline</v-icon>
                  <span>{{ commentCount }} komentar</span>
                </div>
              </div>

            <!-- Share & Tags row -->
            <div class="d-flex flex-column flex-sm-row align-start align-sm-center justify-end ga-4">
              <div>
                <div class="text-caption text-uppercase font-weight-bold text-grey-darken-1 mb-2">Bagikan tulisan ini</div>
                <div class="d-flex ga-2">
                  <v-btn
                    color="#25D366"
                    variant="flat"
                    size="small"
                    rounded="pill"
                    class="text-white font-weight-bold"
                    @click="shareWhatsApp"
                    aria-label="Bagikan via WhatsApp"
                  >
                    <v-icon start size="16">mdi-whatsapp</v-icon>
                    WhatsApp
                  </v-btn>
                  <v-btn
                    variant="outlined"
                    size="small"
                    rounded="pill"
                    class="font-weight-bold"
                    @click="copyShareLink"
                    :aria-label="copied ? 'Tautan tersalin' : 'Salin tautan'"
                  >
                    <v-icon start size="16">{{ copied ? 'mdi-check' : 'mdi-link-variant' }}</v-icon>
                    {{ copied ? 'Tersalin!' : 'Salin Tautan' }}
                  </v-btn>
                </div>
              </div>

              <div v-if="story.author_instagram" class="d-flex align-center ga-2">
                <v-icon color="pink-darken-1" size="20">mdi-instagram</v-icon>
                <a
                  :href="`https://instagram.com/${story.author_instagram.replace('@','')}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="text-body-2 font-weight-bold text-decoration-none"
                  style="color: #E1306C;"
                >{{ story.author_instagram }}</a>
              </div>
            </div>
            </div>

            <!-- Author Bio Card -->
            <div class="author-card mb-12">
              <div class="author-card-accent"></div>
              <div class="d-flex align-center ga-4">
                <v-avatar size="64" color="#FEF2F2" class="elevation-3 flex-shrink-0">
                  <v-icon size="36" color="#DC2626">mdi-fountain-pen-tip</v-icon>
                </v-avatar>
                <div class="flex-grow-1">
                  <div class="text-caption text-uppercase font-weight-bold text-grey mb-1" style="letter-spacing: 0.08em;">Penulis Kontributor</div>
                  <h3 class="text-subtitle-1 font-weight-black text-grey-darken-4 mb-1">{{ story.author_name }}</h3>
                  <p v-if="story.author_bio" class="text-body-2 text-grey-darken-2 mb-2" style="line-height: 1.6;">{{ story.author_bio }}</p>
                  <a
                    v-if="story.author_instagram"
                    :href="`https://instagram.com/${story.author_instagram.replace('@','')}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="d-inline-flex align-center ga-1 text-caption font-weight-bold text-decoration-none"
                    style="color: #E1306C;"
                  >
                    <v-icon size="14" color="#E1306C">mdi-instagram</v-icon>
                    {{ story.author_instagram }}
                  </a>
                </div>
                <!-- Tombol edit untuk pemilik tulisan -->
                <div v-if="canEditStory" class="flex-shrink-0">
                  <v-btn
                    variant="outlined"
                    color="primary"
                    rounded="pill"
                    size="small"
                    class="font-weight-bold"
                    @click="showEditDialog = true; openEditDialog()"
                  >
                    <v-icon start size="16">mdi-pencil-outline</v-icon>
                    Edit Tulisan
                  </v-btn>
                </div>
              </div>
            </div>
            </div>

            <!-- ─ Comments ─ -->
            <div class="mb-12">
              <StoryCommentSection
                v-if="story.id"
                :story-id="story.id"
                :initial-comments-count="story.comments_count ?? 0"
                @count-change="commentCount = $event"
              />
            </div>

            <!-- Bottom CTA -->
            <div class="cta-card mb-12">
              <div class="cta-bg" aria-hidden="true"></div>
              <div class="cta-content text-center">
                <v-icon size="44" color="white" class="mb-3">mdi-account-group</v-icon>
                <h3 class="text-h5 font-weight-bold text-white mb-2">Ingin Berbagi Pengalaman Jelajahmu?</h3>
                <p class="text-body-2 text-white mb-6 mx-auto" style="max-width: 480px; opacity: 0.9;">
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
                    class="font-weight-bold px-6"
                    style="color: #DC2626;"
                  >
                    Daftar di Jalan Bareng
                  </v-btn>
                  <v-btn
                    color="white"
                    variant="outlined"
                    rounded="pill"
                    size="large"
                    class="font-weight-bold px-6 text-white"
                    @click="showSubmitDialog = true"
                  >
                    <v-icon start>mdi-feather</v-icon>
                    Kirim Tulisan Anda
                  </v-btn>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- ═══ Related Stories ═══ -->
        <div v-if="related.length > 0" class="related-section mt-4">
          <div class="d-flex align-center ga-3 mb-6">
            <div class="related-accent-line"></div>
            <h2 class="text-h5 font-weight-black text-grey-darken-4">Cerita Lainnya</h2>
          </div>
          <v-row>
            <v-col v-for="item in related" :key="item.id" cols="12" sm="6" md="4">
              <nuxt-link :to="`/cerita/${item.slug}`" class="related-card text-decoration-none" tabindex="0">
                <div class="related-card-img" :class="`theme-${item.card_style || 'coral'}`">
                  <img
                    v-if="item.cover_image_url"
                    :src="item.cover_image_url"
                    :alt="item.title"
                    class="related-cover-photo"
                  />
                  <div class="related-img-overlay"></div>
                  <v-chip size="x-small" color="white" variant="flat" class="related-chip font-weight-bold text-uppercase">
                    Cerita
                  </v-chip>
                </div>
                <div class="related-card-body">
                  <h3 class="related-title">{{ item.title }}</h3>
                  <p class="related-excerpt">{{ item.excerpt }}</p>
                  <div class="d-flex align-center justify-space-between mt-3">
                    <span class="text-caption text-grey font-weight-medium">{{ item.author_name }}</span>
                    <span class="text-caption text-grey">
                      <v-icon size="12">mdi-eye-outline</v-icon>
                      {{ item.views_count }}
                    </span>
                  </div>
                </div>
              </nuxt-link>
            </v-col>
          </v-row>
        </div>
      </v-container>
    </article>

    <!-- Submit Story Dialog -->
    <SubmitStoryDialog v-model="showSubmitDialog" />

    <!-- Edit Story Dialog -->
    <v-dialog v-model="showEditDialog" max-width="680" scrollable>
      <v-card rounded="xl">
        <v-card-title class="d-flex align-center justify-space-between pa-5 border-b">
          <div class="d-flex align-center ga-2">
            <v-icon color="primary" size="20">mdi-pencil-outline</v-icon>
            <span class="text-subtitle-1 font-weight-bold text-grey-darken-4">Edit Tulisan</span>
          </div>
          <div class="d-flex align-center ga-2">
            <v-chip size="small" color="warning" variant="tonal" class="font-weight-bold">
              Akan dikirim ulang ke kurator
            </v-chip>
            <v-btn icon="mdi-close" variant="text" density="comfortable" @click="showEditDialog = false" />
          </div>
        </v-card-title>

        <v-card-text class="pa-5">
          <v-alert type="info" variant="tonal" rounded="lg" class="mb-4 text-body-2">
            Setelah diedit, tulisan akan dikirim ulang ke tim kurator Jalan Bareng untuk ditinjau sebelum dipublikasikan kembali.
          </v-alert>

          <div class="d-flex flex-column ga-4">
            <v-text-field
              v-model="editForm.title"
              label="Judul Tulisan *"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
            />
            <v-textarea
              v-model="editForm.excerpt"
              label="Ringkasan (Excerpt)"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              rows="2"
              auto-grow
            />
            <div>
              <div class="text-caption font-weight-bold text-grey-darken-2 mb-2">Isi Tulisan *</div>
              <v-textarea
                v-model="editForm.content"
                variant="outlined"
                rounded="lg"
                density="comfortable"
                hide-details
                rows="8"
                auto-grow
                placeholder="Tulis cerita kamu di sini..."
              />
            </div>
            <v-text-field
              v-model="editForm.author_instagram"
              label="Instagram (opsional)"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              prefix="@"
            />
            <v-textarea
              v-model="editForm.author_bio"
              label="Bio Singkat Penulis"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              rows="2"
            />
          </div>
        </v-card-text>

        <v-card-actions class="pa-5 border-t">
          <v-btn variant="text" rounded="pill" @click="showEditDialog = false">Batal</v-btn>
          <v-spacer />
          <v-btn
            color="primary"
            variant="flat"
            rounded="pill"
            :loading="editSaving"
            :disabled="!editForm.title || !editForm.content"
            @click="submitEdit"
          >
            <v-icon start size="16">mdi-send-outline</v-icon>
            Simpan & Kirim Ulang
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Edit Snackbar -->
    <v-snackbar v-model="editSnackbar" :color="editSnackbarColor" rounded="pill" location="bottom" :timeout="4000">
      {{ editSnackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from '~/stores/auth'
import SubmitStoryDialog from '~/components/stories/SubmitStoryDialog.vue'
import StoryLikeButton from '~/components/stories/StoryLikeButton.vue'
import StoryCommentSection from '~/components/stories/StoryCommentSection.vue'
import { defaultDummyStories } from '~/utils/dummyStories'

const route = useRoute()
const { api } = useApi()
const authStore = useAuthStore()

const story = ref<any>(null)
const related = ref<any[]>([])
const loading = ref(true)
const copied = ref(false)
const showSubmitDialog = ref(false)
const commentCount = ref(0)
const articleEl = ref<HTMLElement | null>(null)
const readingProgress = ref(0)

// Edit story state
const showEditDialog = ref(false)
const editSaving = ref(false)
const editSnackbar = ref(false)
const editSnackbarText = ref('')
const editSnackbarColor = ref('success')

// Can the current user edit this story?
const canEditStory = computed(() => {
  if (!authStore.isLoggedIn || !story.value) return false
  // Owner can edit if story is pending or rejected (not published)
  return (
    authStore.user?.id === story.value.user_id &&
    story.value.status !== 'approved'
  )
})

const editForm = ref({
  title: '',
  excerpt: '',
  content: '',
  author_bio: '',
  author_instagram: '',
  card_style: 'coral',
})

const openEditDialog = () => {
  if (!story.value) return
  editForm.value = {
    title: story.value.title || '',
    excerpt: story.value.excerpt || '',
    content: story.value.content || '',
    author_bio: story.value.author_bio || '',
    author_instagram: story.value.author_instagram || '',
    card_style: story.value.card_style || 'coral',
  }
  showEditDialog.value = true
}

const submitEdit = async () => {
  if (!story.value) return
  editSaving.value = true
  try {
    const fd = new FormData()
    Object.entries(editForm.value).forEach(([k, v]) => {
      if (v) fd.append(k, v)
    })
    const res = await api.post(`/stories/${story.value.id}`, fd)
    story.value = { ...story.value, ...res.data.story, status: 'pending' }
    showEditDialog.value = false
    editSnackbarText.value = 'Tulisan berhasil diperbarui dan dikirim ulang untuk ditinjau!'
    editSnackbarColor.value = 'success'
    editSnackbar.value = true
  } catch (err: any) {
    editSnackbarText.value = err?.response?.data?.message || 'Gagal menyimpan perubahan'
    editSnackbarColor.value = 'error'
    editSnackbar.value = true
  } finally {
    editSaving.value = false
  }
}

// Reading time estimator (~200 words per minute reading speed)
const readingTime = computed(() => {
  if (!story.value?.content) return 1
  const text = story.value.content.replace(/<[^>]+>/g, '')
  const words = text.trim().split(/\s+/).length
  return Math.max(1, Math.ceil(words / 200))
})

const formatDate = (dateStr: string) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
}

// Reading progress scroll tracker
const updateProgress = () => {
  const el = articleEl.value
  if (!el) return
  const rect = el.getBoundingClientRect()
  const totalHeight = el.offsetHeight
  const scrolled = -rect.top + window.innerHeight
  readingProgress.value = Math.min(100, Math.max(0, (scrolled / totalHeight) * 100))
}

const fetchStory = async () => {
  loading.value = true
  const slug = String(route.params.slug)
  try {
    const res = await api.get(`/stories/${slug}`)
    story.value = res.data.story || res.data.data
    related.value = res.data.related || []
  } catch (err) {
    console.error('Failed to load story from API, checking fallback:', err)
  }

  // Fallback to local dummy stories
  if (!story.value) {
    const found = defaultDummyStories.find((s: any) => s.slug === slug)
    if (found) {
      story.value = found
      related.value = defaultDummyStories.filter((s: any) => s.slug !== slug).slice(0, 3)
    }
  }

  if (story.value) {
    useSeoMeta({
      title: `${story.value.title} - Jalan Bareng`,
      description: story.value.excerpt || story.value.title,
      ogTitle: story.value.title,
      ogDescription: story.value.excerpt || story.value.title,
      ogImage: story.value.cover_image_url || undefined,
    })
  }
  loading.value = false
}

const copyShareLink = () => {
  if (process.client) {
    navigator.clipboard.writeText(window.location.href)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2500)
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
  window.addEventListener('scroll', updateProgress, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', updateProgress)
})
</script>

<style scoped>
/* ─── Reading Progress Bar ─── */
.reading-progress-bar {
  position: fixed;
  top: 70px;
  left: 0;
  height: 3px;
  background: linear-gradient(90deg, #DC2626, #F97316);
  z-index: 1001;
  transition: width 0.1s linear;
  border-radius: 0 2px 2px 0;
}

/* ─── Hero ─── */
.story-hero {
  position: relative;
  overflow: hidden;
  padding: 80px 0 60px;
  min-height: 460px;
  display: flex;
  align-items: flex-end;
}

.hero-cover-img {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  filter: brightness(0.5) saturate(0.8);
  transform: scale(1.03);
  transition: transform 8s ease;
}

.story-hero:hover .hero-cover-img {
  transform: scale(1);
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.3) 60%, rgba(0,0,0,0.15) 100%);
}

.theme-coral   { background: linear-gradient(135deg, #FA4D56 0%, #D82631 100%); }
.theme-magenta { background: linear-gradient(135deg, #E02F6B 0%, #B81B50 100%); }
.theme-amber   { background: linear-gradient(135deg, #F4A228 0%, #D97706 100%); }
.theme-photo,
.theme-dark    { background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); }

.hero-inner {
  position: relative;
  z-index: 1;
}

.hero-breadcrumb {
  display: flex;
  align-items: center;
  font-size: 0.8rem;
}

.bc-link {
  color: rgba(255,255,255,0.8);
  text-decoration: none;
  transition: color 0.2s;
}
.bc-link:hover { color: white; }
.bc-current { color: rgba(255,255,255,0.6); }

.hero-title {
  font-size: clamp(1.75rem, 4vw, 3rem);
  font-weight: 900;
  color: white;
  line-height: 1.2;
  letter-spacing: -0.03em;
  text-shadow: 0 2px 12px rgba(0,0,0,0.3);
  max-width: 760px;
}

.hero-meta { color: white; }

.hero-meta-divider {
  width: 1px;
  height: 32px;
  background: rgba(255,255,255,0.3);
}

/* ─── Article Body ─── */
.article-container {
  max-width: 1140px;
}

.pull-quote {
  background: #F8FAFC;
  border-left: 4px solid #DC2626;
  border-radius: 0 16px 16px 0;
  padding: 20px 28px;
}

.article-body {
  font-size: 1.1rem;
  line-height: 1.9;
  color: #374151;
}

.article-body :deep(h2),
.article-body :deep(h3) {
  margin-top: 2.5rem;
  margin-bottom: 1rem;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.02em;
}

.article-body :deep(h2) { font-size: 1.5rem; }
.article-body :deep(h3) { font-size: 1.2rem; }

.article-body :deep(p) { margin-bottom: 1.4rem; }

.article-body :deep(ul),
.article-body :deep(ol) {
  margin-bottom: 1.4rem;
  padding-left: 1.5rem;
}

.article-body :deep(li) { margin-bottom: 0.5rem; }

.article-body :deep(blockquote) {
  border-left: 4px solid #DC2626;
  margin: 2rem 0;
  padding: 1rem 1.5rem;
  background: #FEF2F2;
  border-radius: 0 12px 12px 0;
  font-style: italic;
  color: #374151;
}

.article-body :deep(img) {
  max-width: 100%;
  border-radius: 16px;
  margin: 2rem 0;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
}

/* ─── Author Card ─── */
.author-card {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  padding: 24px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
}

.author-card-accent {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #DC2626, #F97316);
}

/* ─── CTA Card ─── */
.cta-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
}

.cta-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #3B82F6 100%);
}

.cta-content {
  position: relative;
  z-index: 1;
  padding: 48px 32px;
}

/* ─── Related Stories ─── */
.related-section {
  max-width: 1000px;
  margin: 0 auto;
}

.related-accent-line {
  width: 40px;
  height: 4px;
  background: #DC2626;
  border-radius: 2px;
}

.related-card {
  display: block;
  border-radius: 16px;
  overflow: hidden;
  background: white;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.related-card:hover {
  transform: translateY(-4px) scale(1.01);
  box-shadow: 0 12px 32px rgba(0,0,0,0.12);
}

.related-card-img {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.related-cover-photo {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.7);
  transition: transform 0.4s ease;
}

.related-card:hover .related-cover-photo {
  transform: scale(1.05);
}

.related-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, transparent 60%);
}

.related-chip {
  position: absolute;
  top: 10px;
  left: 10px;
  font-size: 0.65rem;
  letter-spacing: 0.05em;
  color: #DC2626 !important;
}

.related-card-body {
  padding: 16px;
}

.related-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 6px;
  letter-spacing: -0.01em;
}

.related-excerpt {
  font-size: 0.82rem;
  color: #6B7280;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin: 0;
  line-height: 1.5;
}
</style>
