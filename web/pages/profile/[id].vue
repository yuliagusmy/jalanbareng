<template>
  <div class="public-profile-page">

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <v-progress-circular indeterminate color="#DC2626" size="48" width="4" />
      <p class="text-body-2 text-grey-darken-1 mt-4">Memuat profil...</p>
    </div>

    <!-- Not Found State -->
    <v-container v-else-if="!member" class="text-center py-16">
      <div class="not-found-card pa-8 mx-auto">
        <v-avatar color="#FEE2E2" size="72" class="mb-4">
          <v-icon size="36" color="#DC2626">mdi-account-remove-outline</v-icon>
        </v-avatar>
        <h2 class="text-h5 font-weight-bold text-grey-darken-4 mb-2">Profil Tidak Ditemukan</h2>
        <p class="text-body-2 text-grey-darken-1 mb-6 mx-auto" style="max-width:400px">
          Member yang kamu cari mungkin sudah tidak aktif atau tautan tidak valid.
        </p>
        <v-btn to="/" color="#DC2626" rounded="pill" variant="flat" class="text-white font-weight-bold px-6">
          <v-icon start size="18">mdi-home-outline</v-icon>
          Kembali ke Beranda
        </v-btn>
      </div>
    </v-container>

    <!-- Main Content -->
    <div v-else>
      <!-- ── Hero Section ── -->
      <section class="profile-hero">
        <div class="hero-glow" aria-hidden="true"></div>
        <v-container class="hero-inner">
          <!-- Breadcrumb -->
          <nav class="hero-breadcrumb mb-5" aria-label="Navigasi">
            <NuxtLink to="/" class="bc-link">Beranda</NuxtLink>
            <v-icon size="13" class="mx-1 opacity-60">mdi-chevron-right</v-icon>
            <span class="bc-current">Profil Member</span>
          </nav>

          <div class="d-flex align-center flex-column flex-md-row ga-6">
            <!-- Avatar -->
            <div class="avatar-wrap flex-shrink-0">
              <v-avatar :size="isMobile ? 96 : 120" class="member-avatar elevation-4">
                <v-img v-if="member.photo" :src="member.photo" :alt="member.name" cover>
                  <template v-slot:error>
                    <span class="avatar-initial">{{ memberInitial }}</span>
                  </template>
                </v-img>
                <span v-else class="avatar-initial">{{ memberInitial }}</span>
              </v-avatar>
              <div class="avatar-role-badge" :class="roleBadgeClass" :title="member.role" aria-hidden="true">
                <v-icon size="13">{{ roleIcon }}</v-icon>
              </div>
            </div>

            <!-- Info -->
            <div class="member-info flex-grow-1 text-center text-md-left">
              <div class="d-flex align-center justify-center justify-md-start flex-wrap ga-2 mb-2">
                <span class="profile-kicker">PEJALAN KAKI</span>
                <span class="role-pill" :class="roleBadgeClass">
                  <v-icon size="12" class="mr-1">{{ roleIcon }}</v-icon>
                  {{ member.role }}
                </span>
              </div>

              <h1 class="member-name">{{ member.name }}</h1>

              <p class="member-joined">
                <v-icon size="14" class="mr-1 opacity-70">mdi-calendar-account</v-icon>
                Bergabung sejak {{ joinedDate }}
              </p>

              <!-- Social Links -->
              <div v-if="hasSocials" class="social-links d-flex align-center justify-center justify-md-start flex-wrap ga-2 mt-3">
                <a
                  v-if="member.instagram"
                  :href="`https://instagram.com/${member.instagram.replace('@','')}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-chip instagram"
                  :aria-label="`Instagram ${member.name}`"
                >
                  <v-icon size="15">mdi-instagram</v-icon>
                  <span>{{ member.instagram }}</span>
                </a>
                <a
                  v-if="member.twitter"
                  :href="`https://twitter.com/${member.twitter.replace('@','')}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-chip twitter"
                  :aria-label="`Twitter ${member.name}`"
                >
                  <v-icon size="14">mdi-twitter</v-icon>
                  <span>{{ member.twitter }}</span>
                </a>
                <a
                  v-if="member.facebook"
                  :href="member.facebook.startsWith('http') ? member.facebook : `https://facebook.com/${member.facebook}`"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="social-chip facebook"
                  :aria-label="`Facebook ${member.name}`"
                >
                  <v-icon size="14">mdi-facebook</v-icon>
                  <span>Facebook</span>
                </a>
              </div>
            </div>

            <!-- Stats cluster (desktop) -->
            <div class="stats-cluster d-none d-md-flex flex-column ga-3">
              <div class="stat-card">
                <div class="stat-num">{{ member.destinations_count || 0 }}</div>
                <div class="stat-lbl">
                  <v-icon size="14" color="#DC2626">mdi-map-marker</v-icon> Destinasi
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-num">{{ member.events_count || 0 }}</div>
                <div class="stat-lbl">
                  <v-icon size="14" color="#16A34A">mdi-calendar-check</v-icon> Event Diikuti
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-num">{{ member.stories_count || 0 }}</div>
                <div class="stat-lbl">
                  <v-icon size="14" color="#D97706">mdi-pencil-outline</v-icon> Cerita
                </div>
              </div>
            </div>
          </div>

          <!-- Stats strip (mobile) -->
          <div class="mobile-stats d-flex d-md-none justify-center ga-0 mt-5">
            <div class="mobile-stat">
              <span class="mobile-stat-num">{{ member.destinations_count || 0 }}</span>
              <span class="mobile-stat-lbl">Destinasi</span>
            </div>
            <div class="mobile-stat-divider"></div>
            <div class="mobile-stat">
              <span class="mobile-stat-num">{{ member.events_count || 0 }}</span>
              <span class="mobile-stat-lbl">Event</span>
            </div>
            <div class="mobile-stat-divider"></div>
            <div class="mobile-stat">
              <span class="mobile-stat-num">{{ member.stories_count || 0 }}</span>
              <span class="mobile-stat-lbl">Cerita</span>
            </div>
          </div>
        </v-container>
      </section>

      <!-- ── Tabs Content ── -->
      <v-container class="content-area py-8 py-md-10">
        <v-tabs
          v-model="activeTab"
          color="#DC2626"
          slider-color="#DC2626"
          align-tabs="start"
          class="profile-tabs mb-6"
        >
          <v-tab value="destinations" class="tab-item">
            <v-icon start size="17">mdi-map-marker-multiple-outline</v-icon>
            Destinasi
            <span v-if="member.destinations_count" class="tab-badge ml-2">{{ member.destinations_count }}</span>
          </v-tab>
          <v-tab value="stories" class="tab-item">
            <v-icon start size="17">mdi-pencil-outline</v-icon>
            Cerita
            <span v-if="member.stories_count" class="tab-badge ml-2">{{ member.stories_count }}</span>
          </v-tab>
          <v-tab value="events" class="tab-item">
            <v-icon start size="17">mdi-calendar-clock-outline</v-icon>
            Event Mendatang
            <span v-if="member.events_count" class="tab-badge ml-2">{{ member.events_count }}</span>
          </v-tab>
        </v-tabs>

        <v-window v-model="activeTab">

          <!-- ── Tab: Destinasi ── -->
          <v-window-item value="destinations">
            <div v-if="member.destinations && member.destinations.length">
              <v-row dense class="ga-y-4">
                <v-col
                  v-for="dest in member.destinations"
                  :key="dest.id"
                  cols="12" sm="6" md="4"
                >
                  <NuxtLink :to="`/destinations/${dest.id}`" class="dest-card-link">
                    <div class="dest-card">
                      <div class="dest-img-wrap">
                        <img
                          v-if="dest.photos && dest.photos.length"
                          :src="getImageUrl(dest.photos[0].url)"
                          :alt="dest.name"
                          class="dest-img"
                          loading="lazy"
                        />
                        <div v-else class="dest-img-placeholder">
                          <v-icon size="32" color="#9CA3AF">mdi-image-outline</v-icon>
                        </div>
                        <div class="dest-img-overlay" aria-hidden="true"></div>
                        <span v-if="dest.category" class="dest-category-badge">{{ dest.category.name }}</span>
                      </div>
                      <div class="dest-body pa-3">
                        <h3 class="dest-name">{{ dest.name }}</h3>
                        <div class="d-flex align-center ga-3 mt-1">
                          <span class="dest-meta">
                            <v-icon size="13" color="#DC2626">mdi-heart</v-icon>
                            {{ dest.likes_count || 0 }}
                          </span>
                          <span class="dest-meta">
                            <v-icon size="13" color="#6366F1">mdi-comment-outline</v-icon>
                            {{ dest.comments_count || 0 }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </NuxtLink>
                </v-col>
              </v-row>
            </div>
            <div v-else class="empty-state">
              <v-icon size="48" color="grey-lighten-1" aria-hidden="true">mdi-map-marker-off-outline</v-icon>
              <p class="empty-title">Belum Ada Destinasi</p>
              <p class="empty-desc">{{ member.name }} belum menambahkan destinasi apapun.</p>
            </div>
          </v-window-item>

          <!-- ── Tab: Cerita ── -->
          <v-window-item value="stories">
            <div v-if="member.stories && member.stories.length">
              <v-row dense class="ga-y-4">
                <v-col
                  v-for="story in member.stories"
                  :key="story.id"
                  cols="12" sm="6" md="4"
                >
                  <NuxtLink :to="`/cerita/${story.slug}`" class="story-card-link">
                    <div class="story-card" :class="`theme-${story.card_style || 'coral'}`">
                      <div class="story-img-wrap">
                        <img
                          v-if="story.cover_image_url"
                          :src="story.cover_image_url"
                          :alt="story.title"
                          class="story-img"
                          loading="lazy"
                        />
                        <div v-else class="story-img-placeholder" aria-hidden="true">
                          <v-icon size="28" color="rgba(255,255,255,0.6)">mdi-book-open-page-variant-outline</v-icon>
                        </div>
                        <div class="story-overlay" aria-hidden="true"></div>
                      </div>
                      <div class="story-body pa-3">
                        <h3 class="story-title">{{ story.title }}</h3>
                        <p v-if="story.excerpt" class="story-excerpt">{{ story.excerpt }}</p>
                        <div class="d-flex align-center justify-space-between mt-2">
                          <span class="story-date">{{ formatStoryDate(story.published_at) }}</span>
                          <span class="story-views">
                            <v-icon size="12" aria-hidden="true">mdi-eye-outline</v-icon>
                            {{ story.views_count || 0 }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </NuxtLink>
                </v-col>
              </v-row>
            </div>
            <div v-else class="empty-state">
              <v-icon size="48" color="grey-lighten-1" aria-hidden="true">mdi-book-open-outline</v-icon>
              <p class="empty-title">Belum Ada Cerita</p>
              <p class="empty-desc">{{ member.name }} belum mempublikasikan cerita apapun.</p>
            </div>
          </v-window-item>

          <!-- ── Tab: Event Mendatang ── -->
          <v-window-item value="events">
            <div v-if="member.participated_events && member.participated_events.length">
              <v-row dense class="ga-y-4">
                <v-col
                  v-for="ev in member.participated_events"
                  :key="ev.id"
                  cols="12" sm="6"
                >
                  <NuxtLink :to="`/events/${ev.id}`" class="event-card-link">
                    <div class="event-item d-flex align-center ga-4 pa-4">
                      <div class="event-date-box flex-shrink-0" aria-hidden="true">
                        <span class="event-day">{{ formatEventDay(ev.date) }}</span>
                        <span class="event-month">{{ formatEventMonth(ev.date) }}</span>
                      </div>
                      <div class="flex-grow-1 min-w-0">
                        <div class="event-type-badge mb-1" :class="ev.type === 'walking' ? 'type-walking' : 'type-regular'">
                          <v-icon size="11" class="mr-1" aria-hidden="true">{{ ev.type === 'walking' ? 'mdi-walk' : 'mdi-calendar-star' }}</v-icon>
                          {{ ev.type === 'walking' ? 'Jalan Kaki' : 'Agenda Tematik' }}
                        </div>
                        <h3 class="event-name">{{ ev.name }}</h3>
                        <span class="event-time text-caption text-grey-darken-1">
                          <v-icon size="12" aria-hidden="true">mdi-clock-outline</v-icon>
                          {{ ev.time || '06.00' }} WITA
                        </span>
                      </div>
                      <v-icon size="18" color="grey-lighten-1" class="flex-shrink-0" aria-hidden="true">mdi-chevron-right</v-icon>
                    </div>
                  </NuxtLink>
                </v-col>
              </v-row>
            </div>
            <div v-else class="empty-state">
              <v-icon size="48" color="grey-lighten-1" aria-hidden="true">mdi-calendar-blank-outline</v-icon>
              <p class="empty-title">Belum Ada Event Mendatang</p>
              <p class="empty-desc">{{ member.name }} belum terdaftar di event mendatang.</p>
            </div>
          </v-window-item>

        </v-window>
      </v-container>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useDisplay } from 'vuetify'

definePageMeta({ layout: 'default' })

const route = useRoute()
const { api } = useApi()
const { getImageUrl } = useImageUrl()
const { mobile: isMobile } = useDisplay()

const member = ref<any>(null)
const loading = ref(true)
const activeTab = ref('destinations')

useSeoMeta({
  title: () => member.value
    ? `${member.value.name} — Profil Pejalan Kaki | Jalan Bareng`
    : 'Profil Member | Jalan Bareng',
  description: () => member.value
    ? `Lihat destinasi, cerita, dan kegiatan dari ${member.value.name} di komunitas Jalan Bareng.`
    : 'Profil anggota komunitas Jalan Bareng.',
})

const memberInitial = computed(() =>
  (member.value?.name || 'M').charAt(0).toUpperCase()
)

const joinedDate = computed(() => {
  if (!member.value?.created_at) return '-'
  return new Date(member.value.created_at).toLocaleDateString('id-ID', {
    month: 'long',
    year: 'numeric',
  })
})

const hasSocials = computed(() =>
  !!(member.value?.instagram || member.value?.twitter || member.value?.facebook)
)

const roleBadgeClass = computed(() => {
  const role = (member.value?.role || '').toLowerCase()
  if (role.includes('admin')) return 'badge-admin'
  if (role.includes('community')) return 'badge-community'
  return 'badge-member'
})

const roleIcon = computed(() => {
  const role = (member.value?.role || '').toLowerCase()
  if (role.includes('admin')) return 'mdi-shield-crown'
  if (role.includes('community')) return 'mdi-account-star'
  return 'mdi-account-check'
})

const formatStoryDate = (iso: string | null) => {
  if (!iso) return ''
  return new Date(iso).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric',
  })
}

const formatEventDay = (date: string) =>
  date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric' }) : '-'

const formatEventMonth = (date: string) =>
  date ? new Date(date).toLocaleDateString('id-ID', { month: 'short' }) : ''

const fetchProfile = async () => {
  loading.value = true
  try {
    const response = await api.get(`/profile/${route.params.id}`)
    member.value = response.data.user
  } catch {
    member.value = null
  } finally {
    loading.value = false
  }
}

onMounted(fetchProfile)
</script>

<style scoped>
/* ── Base ── */
.public-profile-page {
  min-height: 100vh;
  background: #FAFAF9;
  font-family: Inter, system-ui, sans-serif;
}

/* ── Loading ── */
.loading-container {
  min-height: 60vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

/* ── Not Found ── */
.not-found-card {
  max-width: 420px;
  background: #fff;
  border-radius: 24px;
  border: 1px solid #F3F4F6;
}

/* ── Hero ── */
.profile-hero {
  position: relative;
  background: #111827;
  overflow: hidden;
  padding: 48px 0 40px;
}
.hero-glow {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 20% 40%, rgba(220,38,38,0.18) 0%, transparent 70%),
    radial-gradient(ellipse 50% 40% at 80% 20%, rgba(99,102,241,0.10) 0%, transparent 60%);
  pointer-events: none;
}
.hero-inner { position: relative; z-index: 1; }

/* Breadcrumb */
.hero-breadcrumb { display: flex; align-items: center; }
.bc-link {
  color: rgba(255,255,255,0.55);
  text-decoration: none;
  font-size: 0.78rem;
  transition: color 0.2s;
}
.bc-link:hover { color: #fff; }
.bc-current { color: rgba(255,255,255,0.85); font-size: 0.78rem; }

/* Avatar */
.avatar-wrap { position: relative; }
.member-avatar { border: 3px solid rgba(220,38,38,0.7); }
.avatar-initial {
  font-size: 2.2rem;
  font-weight: 700;
  color: #fff;
  background: #DC2626;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}
.avatar-role-badge {
  position: absolute;
  bottom: 2px;
  right: 2px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #111827;
}
.badge-admin { background: #DC2626; color: #fff; }
.badge-community { background: #D97706; color: #fff; }
.badge-member { background: #16A34A; color: #fff; }

/* Member info */
.profile-kicker {
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  color: rgba(255,255,255,0.5);
  text-transform: uppercase;
}
.role-pill {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 600;
}
.badge-admin.role-pill  { background: rgba(220,38,38,0.2); color: #FCA5A5; }
.badge-community.role-pill { background: rgba(217,119,6,0.2); color: #FCD34D; }
.badge-member.role-pill { background: rgba(22,163,74,0.2); color: #86EFAC; }

.member-name {
  font-size: clamp(1.5rem, 4vw, 2rem);
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
  margin: 0 0 6px;
}
.member-joined {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
}
@media (min-width: 960px) { .member-joined { justify-content: flex-start; } }

/* Social chips */
.social-chip {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s;
}
.social-chip:hover { opacity: 0.8; }
.social-chip.instagram { background: rgba(225,48,108,0.15); color: #F9A8D4; }
.social-chip.twitter   { background: rgba(29,161,242,0.15);  color: #93C5FD; }
.social-chip.facebook  { background: rgba(59,89,152,0.15);   color: #93C5FD; }

/* Stats cluster desktop */
.stats-cluster { min-width: 120px; }
.stat-card {
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 10px 16px;
  text-align: center;
  min-width: 100px;
}
.stat-num {
  font-size: 1.5rem;
  font-weight: 800;
  color: #fff;
  line-height: 1;
}
.stat-lbl {
  font-size: 0.72rem;
  color: rgba(255,255,255,0.55);
  margin-top: 3px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
}

/* Stats strip mobile */
.mobile-stats {
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
  padding: 12px;
}
.mobile-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  gap: 2px;
}
.mobile-stat-num { font-size: 1.3rem; font-weight: 800; color: #fff; }
.mobile-stat-lbl { font-size: 0.7rem; color: rgba(255,255,255,0.5); }
.mobile-stat-divider {
  width: 1px;
  background: rgba(255,255,255,0.15);
  margin: 0 8px;
  align-self: stretch;
}

/* ── Content Area ── */
.content-area { max-width: 960px; }

/* Tabs */
.profile-tabs :deep(.v-tab) {
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: none;
  letter-spacing: 0;
}
.tab-badge {
  background: #FEE2E2;
  color: #DC2626;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 700;
  padding: 1px 7px;
}

/* ── Destination Cards ── */
.dest-card-link { text-decoration: none; }
.dest-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #F3F4F6;
  transition: transform 0.25s, box-shadow 0.25s;
}
.dest-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 32px rgba(0,0,0,0.10);
}
.dest-img-wrap {
  position: relative;
  aspect-ratio: 4/3;
  overflow: hidden;
  background: #F3F4F6;
}
.dest-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.dest-card:hover .dest-img { transform: scale(1.05); }
.dest-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.dest-img-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.3) 0%, transparent 50%);
}
.dest-category-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(255,255,255,0.92);
  color: #374151;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 2px 10px;
  border-radius: 9999px;
}
.dest-name {
  font-size: 0.92rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dest-meta { font-size: 0.75rem; color: #6B7280; display: flex; align-items: center; gap: 3px; }

/* ── Story Cards ── */
.story-card-link { text-decoration: none; }
.story-card {
  background: #fff;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #F3F4F6;
  transition: transform 0.25s, box-shadow 0.25s;
}
.story-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 32px rgba(0,0,0,0.10);
}
.story-img-wrap { position: relative; aspect-ratio: 16/9; overflow: hidden; }
.story-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
.story-card:hover .story-img { transform: scale(1.05); }
.story-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
.theme-coral .story-img-placeholder  { background: linear-gradient(135deg, #FF6B6B, #FF8E53); }
.theme-ocean .story-img-placeholder  { background: linear-gradient(135deg, #667EEA, #764BA2); }
.theme-forest .story-img-placeholder { background: linear-gradient(135deg, #11998E, #38EF7D); }
.theme-sunset .story-img-placeholder { background: linear-gradient(135deg, #F7971E, #FFD200); }
.story-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.25) 0%, transparent 50%);
}
.story-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.story-excerpt {
  font-size: 0.78rem;
  color: #6B7280;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.4;
}
.story-date  { font-size: 0.72rem; color: #9CA3AF; }
.story-views { font-size: 0.72rem; color: #9CA3AF; display: flex; align-items: center; gap: 3px; }

/* ── Event Items ── */
.event-card-link { text-decoration: none; }
.event-item {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #F3F4F6;
  transition: transform 0.2s, box-shadow 0.2s;
}
.event-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
}
.event-date-box {
  width: 52px;
  height: 52px;
  background: #FEE2E2;
  border-radius: 14px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.event-day   { font-size: 1.3rem; font-weight: 800; color: #DC2626; line-height: 1; }
.event-month { font-size: 0.65rem; font-weight: 600; color: #DC2626; text-transform: uppercase; }
.event-type-badge {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 0.65rem;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 9999px;
}
.type-walking { background: #ECFDF5; color: #16A34A; }
.type-regular { background: #EFF6FF; color: #2563EB; }
.event-name {
  font-size: 0.9rem;
  font-weight: 700;
  color: #111827;
  margin: 2px 0 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.event-time { display: flex; align-items: center; gap: 3px; }

/* ── Empty State ── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 24px;
  text-align: center;
}
.empty-title { font-size: 1rem; font-weight: 700; color: #374151; margin: 12px 0 4px; }
.empty-desc  { font-size: 0.85rem; color: #9CA3AF; margin: 0; }
</style>
