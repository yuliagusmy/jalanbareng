<template>
  <div v-if="!loading && activation">
    <!-- Modern EduFlex-Inspired Hero Section -->
    <section class="activation-hero-section">
      <div class="hero-ambient-glow" aria-hidden="true"></div>

      <v-container class="hero-inner-container">
        <v-row align="center" class="hero-main-row">
          <!-- Left Column: Eyebrow, Headline, Description, Dual CTAs, Stats -->
          <v-col cols="12" md="6" lg="6" class="hero-text-col">
            <!-- Eyebrow Chip & Social Badge -->
            <div class="d-flex align-center flex-wrap ga-2 ga-md-3 mb-3 mb-md-5">
              <div class="hero-eyebrow-pill">
                <span class="eyebrow-dot"></span>
                <span class="eyebrow-text">
                  {{ activation.category === 'city' ? '#CITYCHAPTER' : '#AKTIVASI' }} • {{ (activation.city || activation.name).toUpperCase() }}
                </span>
              </div>

              <a
                v-if="activation.social_instagram"
                :href="`https://instagram.com/${activation.social_instagram}`"
                target="_blank"
                rel="noopener noreferrer"
                class="hero-ig-badge"
              >
                <v-icon size="16" color="#E11D48" class="mr-1">mdi-instagram</v-icon>
                <span>@{{ activation.social_instagram }}</span>
              </a>
            </div>

            <!-- Main Headline: Stacked 2-line editorial style -->
            <h1 class="hero-main-title font-weight-black text-grey-darken-4 mb-3 mb-md-4">
              <span class="hero-title-line">{{ titleLine1 }}</span>
              <span v-if="titleLine2" class="hero-title-line">{{ titleLine2 }}</span>
            </h1>

            <!-- Lead Copy -->
            <p class="hero-lead-text mb-4 mb-md-6">
              {{ heroDescription }}
            </p>

            <!-- Action Buttons -->
            <div class="hero-actions-group d-flex flex-wrap align-center ga-2 ga-md-3 mb-4 mb-md-8">
              <v-btn
                @click="scrollToEventsOrRegister"
                color="#DC2626"
                size="default"
                rounded="pill"
                class="hero-primary-btn font-weight-bold"
                elevation="0"
              >
                Ikut Jalan Pekan Ini
                <v-icon end size="16">mdi-arrow-right</v-icon>
              </v-btn>

              <v-btn
                @click="scrollToAbout"
                variant="outlined"
                size="default"
                rounded="pill"
                class="hero-secondary-btn font-weight-bold"
              >
                Tentang
              </v-btn>
            </div>

            <!-- Stats Counter Row with Dividers -->
            <div class="hero-stats-row">
              <div class="stat-item">
                <div class="stat-value">1.800+</div>
                <div class="stat-label">Pejalan Terhubung</div>
              </div>

              <div class="stat-divider"></div>

              <div class="stat-item">
                <div class="stat-value">65+</div>
                <div class="stat-label">Edisi Jalan</div>
              </div>

              <div class="stat-divider"></div>

              <div class="stat-item">
                <div class="stat-value d-flex align-center ga-1">
                  <!-- Interlocking loops icon like in reference image -->
                  <svg class="stat-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="8" cy="12" r="5" />
                    <circle cx="16" cy="12" r="5" />
                  </svg>
                  <span>15+</span>
                </div>
                <div class="stat-label">Rute & Lorong</div>
              </div>
            </div>
          </v-col>

          <!-- Right Column: 2-Column Collage (hidden on mobile xs, shown on sm+) -->
          <v-col cols="12" md="6" lg="6" class="hero-visual-col d-none d-sm-flex">
            <div class="activation-hero-collage">
              <!-- Column 1: Tall Vertical Card (Lane 3 style) -->
              <div class="collage-lane-tall">
                <div class="collage-card card-tall elevation-2">
                  <div class="card-img-wrapper">
                    <img
                      :src="heroCard1Image"
                      :alt="activation.name"
                      class="collage-card-img"
                    />
                  </div>
                </div>
              </div>

              <!-- Column 2: Two Stacked Cards (Lane 4 style) -->
              <div class="collage-lane-stacked">
                <div class="collage-card card-stacked-top elevation-2">
                  <div class="card-img-wrapper">
                    <img
                      :src="heroCard2Image"
                      alt="Dokumentasi Komunitas Jalan Bareng"
                      class="collage-card-img"
                    />
                  </div>
                </div>
                <div class="collage-card card-stacked-bottom elevation-2">
                  <div class="card-img-wrapper">
                    <img
                      :src="heroCard3Image"
                      alt="Aktivitas Komunitas Jalan Bareng"
                      class="collage-card-img"
                    />
                  </div>
                </div>
              </div>
            </div>
          </v-col>

          <!-- Mobile-only: compact single photo strip -->
          <v-col cols="12" class="d-flex d-sm-none hero-mobile-photo-strip">
            <div class="mobile-photo-row">
              <div class="mobile-photo-item">
                <img :src="heroCard1Image" :alt="activation.name" class="mobile-photo-img" />
              </div>
              <div class="mobile-photo-item">
                <img :src="heroCard2Image" alt="Komunitas Jalan Bareng" class="mobile-photo-img" />
              </div>
              <div class="mobile-photo-item">
                <img :src="heroCard3Image" alt="Aktivitas Jalan Bareng" class="mobile-photo-img" />
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <v-container class="py-5 py-md-10">
      <!-- Chapter Quick Facts Bar (Compact on 390px) -->
      <div class="chapter-quick-bar mb-6 mb-md-10">
        <div class="quick-fact-item">
          <v-icon size="15" color="#DC2626">mdi-map-marker</v-icon>
          <span>{{ activation.city || activation.name }}</span>
        </div>
        <div class="quick-fact-dot">•</div>
        <div class="quick-fact-item">
          <v-icon size="15" color="#059669">mdi-calendar-check</v-icon>
          <span>Agenda Pekanan</span>
        </div>
        <div class="quick-fact-dot">•</div>
        <div class="quick-fact-item">
          <v-icon size="15" color="#D97706">mdi-account-group-outline</v-icon>
          <span>Terbuka untuk Umum</span>
        </div>
      </div>

      <!-- 1. ABOUT SECTION (URUTAN 1) -->
      <div id="about-section" class="about-section mb-8 mb-md-14">
        <v-row>
          <v-col cols="12">
            <!-- About Header -->
            <div class="mb-4 mb-md-8">
              <h2 class="section-heading font-weight-bold mb-2 mb-md-3"
                :style="{ color: activation.color_theme || '#DC2626' }">
                Tentang
              </h2>
              <div class="about-content text-body-1 font-weight-regular text-grey-darken-2"
                v-html="activation.description"></div>
            </div>

            <!-- Gallery Integration -->
            <div v-if="activation.show_gallery && activation.media && activation.media.length > 0"
              class="gallery-integration mt-6 mt-md-8">
              <ActivationGallery :media="activation.media" />
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- 2. FEATURED EVENT SECTION (Event Pilihan Minggu Ini) -->
      <div v-if="activation.featured_event" class="featured-section mb-8 mb-md-14">
        <div class="section-header-center text-center mb-4 mb-md-6">
          <h2 class="section-heading font-weight-bold mb-1">Event Pilihan Minggu Ini</h2>
          <p class="section-subtitle text-grey-darken-1">Jangan lewatkan agenda seru terdekat dari komunitas</p>
        </div>

        <v-card elevation="0" class="featured-event-card overflow-hidden" rounded="xl">
          <v-row no-gutters>
            <v-col cols="12" md="6">
              <div class="featured-image-wrapper">
                <img v-if="activation.featured_event.poster"
                  :src="`${apiBase}/storage/${activation.featured_event.poster}`" :alt="activation.featured_event.name"
                  class="featured-image" />
                <div v-else class="featured-placeholder">
                  <v-icon size="56" color="grey-lighten-1">mdi-calendar-star</v-icon>
                </div>
                <div class="featured-overlay"></div>
                <div class="featured-badge">
                  <v-chip color="success" size="x-small" class="font-weight-bold">
                    <v-icon start size="12">mdi-calendar-check</v-icon>
                    Mendatang
                  </v-chip>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <v-card-text class="pa-4 pa-sm-5 pa-md-6 d-flex flex-column justify-center h-100">
                <h3 class="featured-card-title font-weight-bold mb-2">{{ activation.featured_event.name }}</h3>

                <div class="d-flex align-center mb-2 text-caption text-grey-darken-2">
                  <v-icon size="14" :color="activation.color_theme || '#DC2626'" class="mr-1.5">mdi-calendar</v-icon>
                  <span>{{ formatDate(activation.featured_event.date) }}</span>
                </div>

                <p class="featured-card-desc mb-4 text-grey-darken-1">
                  {{ stripHtml(activation.featured_event.description)?.substring(0, 130) }}...
                </p>

                <div>
                  <v-btn :color="activation.color_theme || '#DC2626'" size="small"
                    :to="`/events/${activation.featured_event.id}`" rounded="pill" class="px-5 font-weight-bold" variant="flat">
                    Lihat Detail
                    <v-icon end size="14">mdi-arrow-right</v-icon>
                  </v-btn>
                </div>
              </v-card-text>
            </v-col>
          </v-row>
        </v-card>
      </div>

      <!-- 3. UPCOMING EVENTS SECTION (Event Mendatang) -->
      <div v-if="upcomingEvents && upcomingEvents.length > 0"
        id="upcoming-events"
        class="upcoming-events-section mb-8 mb-md-14">
        <div class="section-header-row mb-3 mb-md-5">
          <div>
            <h2 class="section-heading font-weight-bold mb-0.5">Event Mendatang</h2>
            <p class="section-subtitle text-grey-darken-1 mb-0">Ikuti kegiatan seru bersama komunitas</p>
          </div>
          <v-btn
            :to="`/events?activation_id=${activation.id}`"
            :color="activation.color_theme || '#DC2626'"
            variant="tonal"
            rounded="pill"
            size="small"
            class="flex-shrink-0 font-weight-bold"
          >
            Lihat semua
            <v-icon end size="14">mdi-arrow-right</v-icon>
          </v-btn>
        </div>

        <!-- Mobile: horizontal scroll strip (compact, touch-friendly) -->
        <div class="events-hscroll d-flex d-md-none">
          <div
            v-for="event in upcomingEvents.slice(0, 6)"
            :key="event.id"
            class="event-strip-card"
            @click="navigateTo(`/events/${event.id}`)"
          >
            <div class="event-strip-img-wrap">
              <img v-if="event.poster"
                :src="`${apiBase}/storage/${event.poster}`"
                :alt="event.name"
                class="event-strip-img"
              />
              <div v-else class="event-strip-placeholder">
                <v-icon size="24" color="grey-lighten-1">mdi-walk</v-icon>
              </div>
              <div class="event-strip-date">
                <span class="date-day-sm">{{ getDay(event.date) }}</span>
                <span class="date-month-sm">{{ getMonth(event.date) }}</span>
              </div>
            </div>
            <div class="event-strip-body">
              <p class="event-strip-name">{{ event.name }}</p>
              <p class="event-strip-date-text">{{ formatDate(event.date) }}</p>
              <div class="d-flex align-center justify-space-between mt-2 pt-1 border-top-subtle">
                <span class="text-caption text-grey font-weight-medium">Detail</span>
                <v-icon size="14" color="#DC2626">mdi-arrow-right</v-icon>
              </div>
            </div>
          </div>
        </div>

        <!-- Desktop: card grid -->
        <v-row class="d-none d-md-flex">
          <v-col v-for="event in upcomingEvents.slice(0, 3)" :key="event.id" cols="12" md="4">
            <v-card elevation="0" class="event-card-modern h-100" :to="`/events/${event.id}`" rounded="xl">
              <div class="event-image-wrapper-modern">
                <img v-if="event.poster" :src="`${apiBase}/storage/${event.poster}`" :alt="event.name"
                  class="event-image" />
                <div v-else class="event-placeholder">
                  <v-icon size="56" color="grey-lighten-1">mdi-calendar</v-icon>
                </div>
                <div class="event-overlay-modern"></div>
                <div class="event-date-badge">
                  <div class="date-day">{{ getDay(event.date) }}</div>
                  <div class="date-month">{{ getMonth(event.date) }}</div>
                </div>
                <div class="event-type-badge">
                  <v-chip :color="event.type === 'walking' ? 'success' : activation.color_theme || 'primary'"
                    size="small" variant="flat">
                    <v-icon start size="small">{{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar' }}</v-icon>
                    {{ event.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}
                  </v-chip>
                </div>
              </div>
              <v-card-text class="pa-4">
                <h4 class="text-subtitle-1 font-weight-bold mb-2 event-title">{{ event.name }}</h4>
                <div class="d-flex align-center text-grey-darken-1">
                  <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
                  <span class="text-caption">{{ formatDate(event.date) }}</span>
                </div>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </div>

      <!-- 4. PAST EVENTS SECTION (Kegiatan Selesai / Arsip Dokumentasi) -->
      <div v-if="pastEvents && pastEvents.length > 0"
        id="past-events"
        class="past-events-section mb-8 mb-md-14">
        <div class="section-header-row mb-3 mb-md-5">
          <div>
            <h2 class="section-heading font-weight-bold mb-0.5">Kegiatan Selesai</h2>
            <p class="section-subtitle text-grey-darken-1 mb-0">Arsip edisi jalan dan dokumentasi bersama pejalan</p>
          </div>
          <v-chip size="small" variant="tonal" color="grey-darken-2" class="font-weight-medium">
            {{ pastEvents.length }} Edisi
          </v-chip>
        </div>

        <!-- 2-column compact grid on mobile & desktop -->
        <div class="past-events-grid">
          <div
            v-for="event in pastEvents.slice(0, 6)"
            :key="event.id"
            class="past-event-card"
            @click="navigateTo(`/events/${event.id}`)"
          >
            <div class="past-event-img-wrap">
              <img
                v-if="event.poster"
                :src="`${apiBase}/storage/${event.poster}`"
                :alt="event.name"
                class="past-event-img"
              />
              <div v-else class="past-event-placeholder">
                <v-icon size="24" color="grey-lighten-1">mdi-walk</v-icon>
              </div>
              <span class="past-status-tag">Selesai</span>
            </div>
            <div class="past-event-body">
              <span class="past-event-date-chip">{{ formatDate(event.date) }}</span>
              <h4 class="past-event-title">{{ event.name }}</h4>
              <div v-if="event.distance || event.start_point" class="past-event-meta">
                <span v-if="event.distance" class="mr-2">
                  <v-icon size="11" class="mr-0.5">mdi-map-marker-distance</v-icon>{{ event.distance }} km
                </span>
                <span v-if="event.start_point" class="text-truncate">
                  <v-icon size="11" class="mr-0.5">mdi-map-marker</v-icon>{{ event.start_point }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="pastEvents.length > 6" class="text-center mt-3 mt-md-4">
          <v-btn
            :to="`/events?activation_id=${activation.id}&status=past`"
            variant="outlined"
            rounded="pill"
            size="small"
            color="grey-darken-2"
          >
            Lihat Semua {{ pastEvents.length }} Kegiatan Selesai
            <v-icon end size="14">mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </div>

      <!-- 5. TESTIMONIALS SECTION -->
      <div v-if="activation.show_testimonials && activation.testimonials && activation.testimonials.length > 0"
        class="testimonials-section mb-8 mb-md-14">
        <div class="section-header-center text-center mb-4 mb-md-6">
          <h2 class="section-heading font-weight-bold mb-1">Kata Mereka</h2>
          <p class="section-subtitle text-grey-darken-1">Pengalaman dari kawan-kawan yang telah melangkah bersama</p>
        </div>
        <ActivationTestimonials :testimonials="activation.testimonials" />
      </div>

      <!-- 6. FAQ SECTION -->
      <div v-if="activation.show_faq && activation.faqs && activation.faqs.length > 0" class="faq-section mb-8 mb-md-14">
        <div class="section-header-center text-center mb-4 mb-md-6">
          <h2 class="section-heading font-weight-bold mb-1">Pertanyaan Umum</h2>
          <p class="section-subtitle text-grey-darken-1">Jawaban seputar kegiatan dan cara bergabung</p>
        </div>
        <v-row>
          <v-col cols="12" md="10" lg="9" xl="8" class="mx-auto">
            <ActivationFAQ :faqs="activation.faqs" />
          </v-col>
        </v-row>
      </div>

      <!-- 7. CTA SECTION: Compact, balanced on mobile 390px -->
      <div v-if="activation.social_instagram || activation.cta_primary_label" class="cta-section mb-4">
        <div class="cta-inner" :style="{
          background: `linear-gradient(135deg, ${activation.color_theme || '#DC2626'} 0%, ${darkenColor(activation.color_theme || '#991B1B', 25)} 100%)`
        }">
          <div class="cta-content">
            <v-icon size="28" color="white" class="mb-1.5">mdi-account-group</v-icon>
            <h2 class="cta-title text-white font-weight-bold">Bergabung dengan {{ activation.name }}</h2>
            <p class="cta-subtitle text-white">Jadilah bagian dari pejalan dan komunitas kami</p>
          </div>
          <div class="cta-actions">
            <v-btn
              v-if="activation.social_instagram"
              :href="`https://instagram.com/${activation.social_instagram}`"
              target="_blank"
              rel="noopener noreferrer"
              color="white"
              rounded="pill"
              size="small"
              class="font-weight-bold cta-pill-btn"
              elevation="1"
            >
              <v-icon start size="15" color="#E11D48">mdi-instagram</v-icon>
              @{{ activation.social_instagram }}
            </v-btn>
            <v-btn
              v-if="activation.cta_primary_label"
              :href="activation.cta_primary_url"
              target="_blank"
              rel="noopener noreferrer"
              variant="outlined"
              color="white"
              rounded="pill"
              size="small"
              class="cta-pill-btn"
            >
              {{ activation.cta_primary_label }}
              <v-icon end size="13">mdi-open-in-new</v-icon>
            </v-btn>
          </div>
        </div>
      </div>
    </v-container>
  </div>

  <!-- Loading State -->
  <v-container v-else-if="loading" class="py-16">
    <v-skeleton-loader type="article, article, article" />
  </v-container>

  <!-- Error State -->
  <v-container v-else class="py-16">
    <v-row class="text-center">
      <v-col cols="12">
        <v-icon size="64" color="grey">mdi-alert-circle-outline</v-icon>
        <h2 class="text-h5 mt-4 mb-6">Aktivasi tidak ditemukan</h2>
        <v-btn to="/aktivasi" color="primary" size="large" rounded="pill" class="px-8">
          <v-icon start>mdi-arrow-left</v-icon>
          Kembali ke Daftar Aktivasi
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
const route = useRoute()
const api = useApi()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const activation = ref<any>(null)
const loading = ref(true)




// Fetch activation detail
const fetchActivation = async () => {
  loading.value = true
  try {
    const response = await api.get(`/activations/${route.params.slug}`)
    activation.value = response.data

    // Debug: Log activation data
    console.log('=== ACTIVATION DATA DEBUG ===')
    console.log('Full Activation:', activation.value)
    console.log('Featured Event:', activation.value.featured_event)
    console.log('Has Featured Event?', !!activation.value.featured_event)
    console.log('Upcoming Events:', activation.value.upcoming_events)
    console.log('Upcoming Events Count:', activation.value.upcoming_events?.length)
    console.log('Popular Destinations:', activation.value.popular_destinations)
    console.log('Popular Destinations Count:', activation.value.popular_destinations?.length)
    console.log('===========================')
  } catch (error) {
    console.error('Error fetching activation:', error)
  } finally {
    loading.value = false
  }
}

// Format date
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// Get day from date
const getDay = (dateString: string) => {
  const date = new Date(dateString)
  return date.getDate()
}

// Get month from date
const getMonth = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase()
}

// Strip HTML tags
const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '')
}

// Smooth scroll to about section
const scrollToAbout = () => {
  const aboutSection = document.getElementById('about-section')
  if (aboutSection) {
    aboutSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

// Smooth scroll to events or navigate to registration
const scrollToEventsOrRegister = () => {
  const eventsSection = document.getElementById('upcoming-events')
  if (eventsSection) {
    eventsSection.scrollIntoView({ behavior: 'smooth', block: 'start' })
  } else {
    navigateTo('/#registrasi')
  }
}

// Computed properties for EduFlex-inspired Hero Section
const heroCard1Image = computed(() => {
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return '/images/activations/hero_makassar.jpg'
  }
  if (activation.value?.hero_image) {
    return `${apiBase}/storage/${activation.value.hero_image}`
  }
  return '/images/hero/walk_2.jpg'
})

const heroCard2Image = computed(() => {
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return '/images/activations/makassar/makassar_sign_group.jpg'
  }
  if (activation.value?.media && activation.value.media.length > 0) {
    const photo = activation.value.media[0]
    const path = photo?.file_url || photo?.file_path
    return path ? `${apiBase}/storage/${path}` : '/images/hero/walk_1.jpg'
  }
  return '/images/hero/walk_1.jpg'
})

const heroCard3Image = computed(() => {
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return '/images/activations/makassar/makassar_circle_sharing.jpg'
  }
  if (activation.value?.media && activation.value.media.length > 1) {
    const photo = activation.value.media[1]
    const path = photo?.file_url || photo?.file_path
    return path ? `${apiBase}/storage/${path}` : '/images/hero/walk_5.jpg'
  }
  return '/images/hero/walk_5.jpg'
})

const heroTitleLine1 = computed(() => {
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return 'Langkah Santai &'
  }
  return 'Ruang Berjalan &'
})

const heroTitleLine2 = computed(() => {
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return 'Teman Baru di Makassar'
  }
  return activation.value?.name || 'Jalan Bareng'
})

const titleLine1 = computed(() => {
  if (!activation.value?.name) return ''
  const words = activation.value.name.trim().split(/\s+/)
  return words.slice(0, 2).join(' ')
})

const titleLine2 = computed(() => {
  if (!activation.value?.name) return ''
  const words = activation.value.name.trim().split(/\s+/)
  return words.slice(2).join(' ')
})

const heroDescription = computed(() => {
  if (activation.value?.tagline) {
    return activation.value.tagline
  }
  if (activation.value?.slug === 'jalan-bareng-makassar' || activation.value?.city?.toLowerCase() === 'makassar') {
    return 'Berbagi kesenangan menyusuri sudut-sudut kota dengan berjalan kaki sambil menyapa wajah-wajah baru tanpa beban ekspektasi. Gerakan ruang bersama yang telah berdenyut dan melangkah bersama selama lebih dari dua tahun di Makassar.'
  }
  return stripHtml(activation.value?.description || '').substring(0, 180) + '...'
})

// Computed upcoming & past events with fallback to full events array
const upcomingEvents = computed(() => {
  if (activation.value?.upcoming_events && activation.value.upcoming_events.length > 0) {
    return activation.value.upcoming_events
  }
  if (activation.value?.events && activation.value.events.length > 0) {
    const now = new Date()
    return activation.value.events.filter((e: any) => new Date(e.date) >= now)
  }
  return []
})

const pastEvents = computed(() => {
  if (activation.value?.past_events && activation.value.past_events.length > 0) {
    return activation.value.past_events
  }
  if (activation.value?.events && activation.value.events.length > 0) {
    const now = new Date()
    return activation.value.events.filter((e: any) => new Date(e.date) < now)
  }
  return []
})


// Helper functions
const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    city: '#2196F3',
    theme: '#9C27B0',
    space: '#E91E63',
    other: '#607D8B'
  }
  return colors[category] || colors.other
}

const getCategoryIcon = (category: string) => {
  const icons: Record<string, string> = {
    city: 'mdi-city',
    theme: 'mdi-palette',
    space: 'mdi-store',
    other: 'mdi-star-four-points'
  }
  return icons[category] || icons.other
}

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Aktivasi Kota',
    theme: 'Aktivasi Tematik',
    space: 'Ruang Kreatif',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

const darkenColor = (color: string, percent: number) => {
  const num = parseInt(color.replace('#', ''), 16)
  const amt = Math.round(2.55 * percent)
  const R = (num >> 16) - amt
  const G = (num >> 8 & 0x00FF) - amt
  const B = (num & 0x0000FF) - amt
  return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
    (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
    (B < 255 ? B < 1 ? 0 : B : 255))
    .toString(16)
    .slice(1)
}

const hexToRgba = (hex: string, alpha: number) => {
  const num = parseInt(hex.replace('#', ''), 16)
  const r = (num >> 16) & 255
  const g = (num >> 8) & 255
  const b = num & 255
  return `rgba(${r}, ${g}, ${b}, ${alpha})`
}

// Initial fetch
onMounted(() => {
  fetchActivation()
})

// SEO
// SEO
useSeoMeta({
  title: () => activation.value ? `${activation.value.name} - Jalan Bareng` : 'Aktivasi - Jalan Bareng',
  ogTitle: () => activation.value ? `${activation.value.name} - Jalan Bareng` : 'Aktivasi - Jalan Bareng',
  description: () => activation.value ? (activation.value.tagline || activation.value.description || '') : 'Detail aktivasi kota di Jalan Bareng.',
  ogDescription: () => activation.value ? (activation.value.tagline || activation.value.description || '') : 'Detail aktivasi kota di Jalan Bareng.',
  ogImage: () => activation.value?.hero_image ? `${apiBase}/storage/${activation.value.hero_image}` : 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})
</script>

<style scoped>
/* ==========================================================================
   EDUFLEX-INSPIRED HERO SECTION FOR JALAN BARENG
   ========================================================================== */
.activation-hero-section {
  position: relative;
  background: #FFFFFF;
  padding: 32px 0 48px 0;
  overflow: hidden;
  border-bottom: 1px solid #F1F5F9;
}

.hero-ambient-glow {
  position: absolute;
  top: -120px;
  right: -100px;
  width: 550px;
  height: 550px;
  background: radial-gradient(circle, rgba(254, 226, 226, 0.45) 0%, rgba(240, 253, 250, 0.25) 50%, transparent 70%);
  pointer-events: none;
  z-index: 0;
}

.hero-inner-container {
  position: relative;
  z-index: 1;
}

/* Eyebrow Chip */
.hero-eyebrow-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 14px;
  border-radius: 999px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
}

.hero-ig-badge {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 999px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  color: #475569;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
}

.hero-ig-badge:hover {
  background: #FFF1F2;
  border-color: #FECDD3;
  color: #E11D48;
  transform: translateY(-1px);
}

.eyebrow-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #DC2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
  animation: pulseSlow 2.5s infinite ease-in-out;
}

.eyebrow-text {
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #DC2626;
  text-transform: uppercase;
}

/* Headline: editorial stacked words — big on every screen */
.hero-main-title {
  font-weight: 900 !important;
  line-height: 1.05;
  letter-spacing: -0.04em;
  color: #111827;
}

.hero-title-line {
  display: block;
  font-size: clamp(3rem, 12vw, 5rem);
  line-height: 1.05;
}

/* Lead text */
.hero-lead-text {
  font-size: 1.08rem;
  line-height: 1.7;
  color: #475569;
  max-width: 520px;
}

/* Action Buttons */
.hero-actions-group {
  margin-bottom: 36px;
}

.hero-primary-btn {
  background-color: #DC2626 !important;
  color: #FFFFFF !important;
  padding: 0 28px !important;
  height: 48px !important;
  font-size: 0.95rem !important;
  letter-spacing: 0.01em !important;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.28) !important;
  transition: all 0.25s ease !important;
}

.hero-primary-btn:hover {
  background-color: #B91C1C !important;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(220, 38, 38, 0.38) !important;
}

.hero-secondary-btn {
  border: 1.5px solid #CBD5E1 !important;
  color: #1E293B !important;
  background: #FFFFFF !important;
  padding: 0 24px !important;
  height: 48px !important;
  font-size: 0.95rem !important;
  transition: all 0.25s ease !important;
}

.hero-secondary-btn:hover {
  border-color: #94A3B8 !important;
  background: #F8FAFC !important;
  transform: translateY(-2px);
}

.hero-instagram-btn {
  color: #475569 !important;
  font-weight: 600 !important;
  font-size: 0.92rem !important;
}

/* Stats Counter Row */
.hero-stats-row {
  display: flex;
  align-items: center;
  gap: 28px;
  padding-top: 24px;
  border-top: 1px solid #E2E8F0;
  max-width: 500px;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.75rem;
  font-weight: 800;
  color: #0F172A;
  line-height: 1.15;
  letter-spacing: -0.02em;
}

.stat-label {
  font-size: 0.8rem;
  color: #64748B;
  font-weight: 500;
  margin-top: 2px;
}

.stat-divider {
  width: 1px;
  height: 38px;
  background-color: #E2E8F0;
}

/* --------------------------------------------------------------------------
   RIGHT COLUMN: 2-COLUMN COLLAGE (Home Columns 3 & 4 Style)
   -------------------------------------------------------------------------- */
.hero-visual-col {
  display: flex;
  justify-content: center;
  align-items: center;
}

.activation-hero-collage {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  width: 100%;
  max-width: 530px;
  margin: 0 auto;
}

/* Lane 1: Tall Vertical Card (Lane 3 style) */
.collage-lane-tall {
  flex: 1.15;
  height: 420px;
}

.card-tall {
  width: 100%;
  height: 100%;
  border-radius: 28px;
  overflow: hidden;
  background-color: #F3F4F6;
  border: none;
  box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.12) !important;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  cursor: pointer;
}

.card-tall:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 20px 42px -6px rgba(0, 0, 0, 0.16) !important;
}

/* Lane 2: Two Stacked Cards (Lane 4 style) */
.collage-lane-stacked {
  flex: 1.45;
  height: 420px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  justify-content: space-between;
}

.card-stacked-top,
.card-stacked-bottom {
  width: 100%;
  height: calc((100% - 16px) / 2);
  border-radius: 22px;
  overflow: hidden;
  background-color: #F3F4F6;
  border: none;
  box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.1) !important;
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  cursor: pointer;
}

.card-stacked-top:hover,
.card-stacked-bottom:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 16px 32px -4px rgba(0, 0, 0, 0.14) !important;
}

.card-img-wrapper {
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: inherit;
}

.collage-card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  border-radius: inherit;
  transition: transform 0.4s ease;
}

.card-tall:hover .collage-card-img,
.card-stacked-top:hover .collage-card-img,
.card-stacked-bottom:hover .collage-card-img {
  transform: scale(1.04);
}

/* Mobile-only photo strip (xs only) */
.hero-mobile-photo-strip {
  padding: 0 16px;
  margin-top: 4px;
}

.mobile-photo-row {
  display: flex;
  gap: 10px;
  width: 100%;
  height: 140px;
}

.mobile-photo-item {
  flex: 1;
  border-radius: 16px;
  overflow: hidden;
  background: #F3F4F6;
}

.mobile-photo-item:first-child {
  flex: 1.3;
}

.mobile-photo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Responsive Breakpoints */
@media (max-width: 960px) {
  .activation-hero-section {
    padding: 28px 0 40px 0;
  }

  .activation-hero-collage {
    margin-top: 32px;
    max-width: 480px;
  }

  .collage-lane-tall,
  .collage-lane-stacked {
    height: 320px;
  }

  .card-tall {
    border-radius: 22px;
  }

  .card-stacked-top,
  .card-stacked-bottom {
    border-radius: 18px;
  }
}

@media (max-width: 600px) {
  .activation-hero-section {
    padding: 20px 0 24px 0;
  }

  /* hero-main-title intentionally not overridden here — clamp(2.75rem, 9vw, 3.5rem)
     naturally forces "Jalan", "Bareng", "Makassar" each to its own line at 390px */

  .hero-lead-text {
    font-size: 0.92rem;
    line-height: 1.6;
  }

  .hero-stats-row {
    gap: 12px;
    padding-top: 16px;
  }

  .stat-value {
    font-size: 1.25rem;
  }

  .stat-label {
    font-size: 0.7rem;
  }

  .stat-divider {
    height: 30px;
  }

  .hero-primary-btn,
  .hero-secondary-btn {
    font-size: 0.875rem !important;
    height: 40px !important;
    padding: 0 16px !important;
  }

  .featured-image-wrapper {
    height: 200px;
  }

  .event-image-wrapper-modern {
    height: 160px;
  }

  .upcoming-events-section {
    padding: 16px 0;
  }
}

/* Section headings – consistent across all content blocks */
.section-heading {
  font-size: clamp(1.35rem, 5vw, 2.125rem);
  line-height: 1.2;
  letter-spacing: -0.015em;
  color: #111827;
}

.section-subtitle {
  font-size: 0.9rem;
  line-height: 1.5;
  color: #64748B;
}

@media (min-width: 960px) {
  .section-heading {
    font-size: 2.125rem;
  }

  .section-subtitle {
    font-size: 1.25rem;
  }
}

/* About Section - Modern & Informative */
.about-section {
  padding: 20px 0;
}

.divider-line {
  width: 80px;
  height: 4px;
  border-radius: 2px;
}

.about-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
  border: 1px solid #e0e0e0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
}

.about-content {
  line-height: 1.9;
  color: #424242;
  font-size: 1.05rem;
}

.about-content :deep(p) {
  margin-bottom: 20px;
}

.about-content :deep(h1),
.about-content :deep(h2),
.about-content :deep(h3) {
  margin-top: 28px;
  margin-bottom: 16px;
  font-weight: 700;
  color: #212121;
}

.about-content :deep(ul),
.about-content :deep(ol) {
  margin-left: 28px;
  margin-bottom: 20px;
}

.about-content :deep(strong) {
  color: #212121;
  font-weight: 600;
}

.gallery-integration {
  margin-top: 40px;
}

/* Featured Event Card */
.featured-event-card {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
}

.featured-event-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.featured-image-wrapper {
  position: relative;
  height: 400px;
  overflow: hidden;
}

.featured-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.featured-event-card:hover .featured-image {
  transform: scale(1.1);
}

.featured-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.1) 100%);
}

.featured-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 2;
}

.featured-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
}

/* Upcoming Events - Modern Style */
.upcoming-events-section {
  padding: 40px 0;
}

.event-card-modern {
  border-radius: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
  cursor: pointer;
  border: 1px solid #f0f0f0;
}

.event-card-modern:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

.event-image-wrapper-modern {
  position: relative;
  height: 240px;
  overflow: hidden;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s ease;
}

.event-card-modern:hover .event-image {
  transform: scale(1.15) rotate(2deg);
}

.event-overlay-modern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.1) 60%, transparent 100%);
}

.event-date-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  background: white;
  border-radius: 12px;
  padding: 8px 12px;
  text-align: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 2;
}

.date-day {
  font-size: 24px;
  font-weight: 700;
  line-height: 1;
  color: #212121;
}

.date-month {
  font-size: 11px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-top: 2px;
}

.event-type-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 2;
}

.event-title {
  line-height: 1.3;
  min-height: 60px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-description {
  line-height: 1.6;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
}

/* Destination Cards */
.destination-card {
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  overflow: hidden;
  cursor: pointer;
}

.destination-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.destination-image-wrapper {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.destination-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.destination-card:hover .destination-image {
  transform: scale(1.1);
}

.destination-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0) 100%);
}

.destination-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  z-index: 2;
}

/* Category Section Styles */
.category-section {
  padding: 40px 0;
}

.category-intro {
  padding-right: 32px;
}

.category-label {
  letter-spacing: 2px;
  font-size: 14px;
}

.category-title {
  line-height: 1.2;
}

.category-cards-wrapper {
  padding-left: 0;
}

.category-card {
  border-radius: 24px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}

.category-image-wrapper {
  position: relative;
  height: 320px;
  overflow: hidden;
}

.category-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.category-card:hover .category-image {
  transform: scale(1.1);
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
  transition: all 0.3s ease;
}

.category-card:hover .category-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0.3) 100%);
}

.category-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 32px 24px;
  color: white;
  z-index: 2;
}

.category-icon-wrapper {
  width: 56px;
  height: 56px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}

.category-card:hover .category-icon-wrapper {
  background: rgba(255, 255, 255, 0.25);
  transform: scale(1.1);
}

.category-name {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 8px;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.category-description {
  font-size: 14px;
  opacity: 0.9;
  margin-bottom: 16px;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

.category-button {
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.category-card:hover .category-button {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 960px) {
  .category-intro {
    padding-right: 0;
    margin-bottom: 32px;
    text-align: center;
  }

  .category-image-wrapper {
    height: 280px;
  }

  .event-image-wrapper-modern {
    height: 200px;
  }

  .event-title {
    min-height: auto;
  }
}

.activation-cta-btn {
  min-height: 48px;
  height: auto !important;
  padding: 12px 28px !important;
  font-size: 0.95rem !important;
  letter-spacing: 0.02em !important;
  max-width: 100%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.activation-cta-btn:hover {
  transform: translateY(-2px);
}

.activation-cta-btn .cta-btn-text {
  white-space: normal;
  word-break: break-word;
  line-height: 1.3;
}

@media (max-width: 600px) {
  .activation-cta-btn {
    width: 100%;
    max-width: 320px;
    padding: 12px 18px !important;
    font-size: 0.88rem !important;
  }
}

.about-content {
  max-width: 960px;
  line-height: 1.85 !important;
  color: #334155 !important;
}

.about-content :deep(p) {
  margin-bottom: 1.25rem;
  line-height: 1.85;
}

/* Chapter Quick Facts Bar (Mobile Friendly) */
.chapter-quick-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 9999px;
  padding: 8px 16px;
  width: fit-content;
  max-width: 100%;
  overflow-x: auto;
  white-space: nowrap;
  -webkit-overflow-scrolling: touch;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.quick-fact-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #374151;
}

.quick-fact-dot {
  color: #D1D5DB;
  font-size: 0.8rem;
}

/* Section Header Layouts */
.section-header-row {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 12px;
}

.section-header-center {
  max-width: 580px;
  margin-left: auto;
  margin-right: auto;
}

.featured-card-title {
  font-size: 1.1rem;
  line-height: 1.3;
  color: #111827;
}

@media (min-width: 600px) {
  .featured-card-title {
    font-size: 1.35rem;
  }
}

.featured-card-desc {
  font-size: 0.85rem;
  line-height: 1.55;
}

/* Horizontal Scroll Strip for Upcoming Events on Mobile */
.events-hscroll {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 4px 2px 14px 2px;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.events-hscroll::-webkit-scrollbar {
  display: none;
}

.event-strip-card {
  flex: 0 0 190px;
  scroll-snap-align: start;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.event-strip-card:active {
  transform: scale(0.98);
}

.event-strip-img-wrap {
  position: relative;
  width: 100%;
  height: 105px;
  background: #F3F4F6;
  overflow: hidden;
}

.event-strip-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.event-strip-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
}

.event-strip-date {
  position: absolute;
  top: 8px;
  left: 8px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(4px);
  border-radius: 8px;
  padding: 3px 6px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
  line-height: 1;
}

.date-day-sm {
  display: block;
  font-size: 13px;
  font-weight: 800;
  color: #111827;
}

.date-month-sm {
  display: block;
  font-size: 8.5px;
  font-weight: 700;
  color: #DC2626;
  text-transform: uppercase;
}

.event-strip-body {
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.event-strip-name {
  font-size: 0.85rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.3;
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-strip-date-text {
  font-size: 0.72rem;
  color: #6B7280;
  margin-bottom: 0;
}

.border-top-subtle {
  border-top: 1px solid #F3F4F6;
}

/* Past Events Grid (Arsip Kegiatan Selesai) */
.past-events-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
}

@media (min-width: 600px) {
  .past-events-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
  }
}

@media (min-width: 960px) {
  .past-events-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }
}

.past-event-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  display: flex;
  flex-direction: column;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.past-event-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
}

.past-event-img-wrap {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 3;
  background: #F3F4F6;
  overflow: hidden;
}

.past-event-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.35s ease;
}

.past-event-card:hover .past-event-img {
  transform: scale(1.05);
}

.past-event-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
}

.past-status-tag {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(17, 24, 39, 0.75);
  backdrop-filter: blur(4px);
  color: #FFFFFF;
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  padding: 2px 6px;
  border-radius: 6px;
  letter-spacing: 0.03em;
}

.past-event-body {
  padding: 9px 10px 11px 10px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.past-event-date-chip {
  font-size: 0.68rem;
  color: #6B7280;
  font-weight: 500;
  margin-bottom: 2px;
}

.past-event-title {
  font-size: 0.825rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.3;
  margin-bottom: 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.past-event-meta {
  display: flex;
  align-items: center;
  font-size: 0.68rem;
  color: #9CA3AF;
  margin-top: auto;
}

/* CTA Section (Siap Bergabung - Compact on 390px) */
.cta-section {
  width: 100%;
}

.cta-inner {
  border-radius: 18px;
  padding: 22px 16px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(220, 38, 38, 0.16);
}

@media (min-width: 600px) {
  .cta-inner {
    border-radius: 24px;
    padding: 36px 32px;
  }
}

.cta-title {
  font-size: 1.15rem;
  line-height: 1.3;
}

@media (min-width: 600px) {
  .cta-title {
    font-size: 1.6rem;
  }
}

.cta-subtitle {
  font-size: 0.825rem;
  opacity: 0.92;
  margin-bottom: 16px;
  line-height: 1.45;
}

@media (min-width: 600px) {
  .cta-subtitle {
    font-size: 1rem;
    margin-bottom: 22px;
  }
}

.cta-actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.cta-pill-btn {
  height: 36px !important;
  font-size: 0.8rem !important;
  padding: 0 16px !important;
  letter-spacing: 0.01em;
}
</style>
