<template>
  <div class="event-detail-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <v-progress-circular indeterminate color="#DC2626" size="48" width="4"></v-progress-circular>
      <p class="text-body-2 text-grey-darken-1 mt-4">Memuat agenda kegiatan...</p>
    </div>

    <!-- Error / Not Found State -->
    <v-container v-else-if="!event" class="text-center py-16">
      <div class="not-found-card pa-8 pa-md-12 mx-auto">
        <v-avatar color="#FEE2E2" size="72" class="mb-4">
          <v-icon size="36" color="#DC2626">mdi-calendar-remove-outline</v-icon>
        </v-avatar>
        <h2 class="text-h5 font-weight-bold text-grey-darken-4 mb-2">Agenda Tidak Ditemukan</h2>
        <p class="text-body-2 text-grey-darken-1 mb-6 mx-auto" style="max-width: 420px;">
          Agenda yang Anda cari mungkin sudah ditutup, diubah tanggalnya, atau belum dipublikasikan.
        </p>
        <v-btn to="/events" color="#DC2626" rounded="pill" variant="flat" class="text-white font-weight-bold px-6">
          <v-icon start size="18">mdi-arrow-left</v-icon>
          Kembali ke Katalog Event
        </v-btn>
      </div>
    </v-container>

    <!-- Main Content -->
    <div v-else class="event-main-wrapper">
      <!-- 1. Clean Editorial Header Section (Background Netral Elegan, Tidak Menimpa Poster) -->
      <section class="event-header-section">
        <v-container>
          <!-- Breadcrumb Navigation -->
          <nav aria-label="Breadcrumb" class="event-breadcrumbs mb-4">
            <NuxtLink to="/" class="bc-link">
              <v-icon size="14" class="mr-1">mdi-home-outline</v-icon>
              Beranda
            </NuxtLink>
            <span class="bc-sep">/</span>
            <NuxtLink to="/events" class="bc-link">Event</NuxtLink>
            <span class="bc-sep">/</span>
            <span class="bc-current" :title="event.name">{{ event.name }}</span>
          </nav>

          <!-- Top Meta Row: Badge & Admin Edit -->
          <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-3">
            <div class="d-flex align-center flex-wrap ga-2">
              <!-- Type Chip -->
              <span class="event-type-badge" :class="event.type === 'walking' ? 'type-walking' : 'type-regular'">
                <v-icon size="14" class="mr-1">{{ event.type === 'walking' ? 'mdi-walk' : 'mdi-calendar-star' }}</v-icon>
                {{ event.type === 'walking' ? 'Jalan Kaki • City Chapter' : 'Agenda Tematik • Komunitas' }}
              </span>

              <!-- Status Badge (Mendatang / Selesai) -->
              <span v-if="isUpcoming" class="event-status-pill upcoming">
                <span class="pulse-dot"></span>
                Pendaftaran Dibuka
              </span>
              <span v-else class="event-status-pill past">
                <v-icon size="12" class="mr-1">mdi-check-circle-outline</v-icon>
                Telah Terlaksana
              </span>
            </div>

            <!-- Admin Shortcut -->
            <div v-if="canEdit" class="d-flex align-center ga-2">
              <v-btn
                :to="`/events/${event.id}/edit`"
                variant="outlined"
                color="#DC2626"
                rounded="pill"
                size="small"
                class="font-weight-bold"
              >
                <v-icon start size="15">mdi-pencil-outline</v-icon>
                Edit Event
              </v-btn>
              <v-btn
                variant="text"
                color="grey-darken-1"
                icon
                size="small"
                @click="confirmDelete"
                aria-label="Hapus Event"
              >
                <v-icon size="18">mdi-delete-outline</v-icon>
              </v-btn>
            </div>
          </div>

          <!-- Main Title -->
          <h1 class="event-headline mb-3">{{ event.name }}</h1>

          <!-- Host & City Meta -->
          <div class="event-organizer-row d-flex align-center flex-wrap ga-3">
            <div class="d-flex align-center ga-2">
              <v-avatar size="28" color="#FEE2E2">
                <v-icon size="16" color="#DC2626">mdi-account-group</v-icon>
              </v-avatar>
              <span class="organizer-name">
                Diselenggarakan oleh <strong>{{ event.user?.name || 'Komunitas Jalan Bareng' }}</strong>
              </span>
              <span class="verified-tag">
                <v-icon size="12" color="#16A34A">mdi-check-decagram</v-icon>
                Resmi
              </span>
            </div>

            <div v-if="event.activation?.title" class="chapter-info d-flex align-center ga-1 text-grey-darken-2">
              <span class="text-grey-lighten-1">•</span>
              <v-icon size="14" color="#0284C7">mdi-city-variant-outline</v-icon>
              <span>{{ event.activation.title }}</span>
            </div>
          </div>
        </v-container>
      </section>

      <!-- 2. Main Body Grid: Kolom Poster & Registrasi (Kiri) + Kolom Detail & Rute (Kanan) -->
      <v-container class="event-body-container py-6 py-md-10">
        <v-row class="ga-y-8">
          <!-- SISI KIRI (Showcase Poster Instagram & Card Registrasi) -->
          <v-col cols="12" md="5" lg="4" class="order-1 order-md-1">
            <div class="sidebar-sticky-wrap">
              <!-- Poster Card (Aspect Ratio Utuh, Tidak Terpotong) -->
              <div class="poster-card-frame">
                <div class="poster-image-container">
                  <img
                    v-if="event.poster"
                    :src="getImageUrl(event.poster)"
                    :alt="event.name"
                    class="poster-img"
                    @click="lightboxDialog = true"
                  />
                  <div v-else class="poster-placeholder" @click="lightboxDialog = true">
                    <v-icon size="48" color="#9CA3AF">mdi-image-outline</v-icon>
                    <span class="text-caption text-grey mt-2">Poster Kegiatan</span>
                  </div>

                  <!-- Overlay Zoom Button -->
                  <button
                    v-if="event.poster"
                    type="button"
                    class="poster-zoom-btn"
                    title="Perbesar poster Instagram"
                    @click.stop="lightboxDialog = true"
                  >
                    <v-icon size="18">mdi-magnify-plus-outline</v-icon>
                    <span>Perbesar Poster</span>
                  </button>
                </div>
              </div>

              <!-- Registration Box (CTA Utama - Khusus Desktop) -->
              <div class="registration-cta-card mt-4 d-none d-md-block">
                <div class="d-flex align-center justify-space-between mb-2">
                  <span class="cta-status-label" :class="{ 'status-open': isUpcoming }">
                    {{ isUpcoming ? (isCuratedTikum ? 'Kurasi Peserta Dibuka' : 'Pendaftaran Tersedia') : 'Pendaftaran Ditutup' }}
                  </span>
                  <span class="cta-quota-badge">{{ isCuratedTikum ? 'Sistem Kurasi' : 'Terbuka untuk Umum' }}</span>
                </div>

                <h3 class="cta-title">Siap Jalan Bareng?</h3>
                <p class="cta-desc">
                  {{ isCuratedTikum 
                    ? 'Kegiatan ini menerapkan kurasi peserta demi kenyamanan rute bersama. Daftarkan diri, admin akan menghubungi peserta yang lolos.' 
                    : 'Amankan slot jalan santaimu. Pendaftaran gratis dan kuota terbatas untuk kenyamanan bersama.' 
                  }}
                </p>

                <!-- Tombol CTA Merah (#DC2626) -->
                <v-btn
                  v-if="isUpcoming"
                  :href="event.registration_link || 'https://instagram.com/jalanbarengind'"
                  target="_blank"
                  rel="noopener noreferrer"
                  color="#DC2626"
                  size="large"
                  block
                  rounded="pill"
                  class="text-white font-weight-bold cta-submit-btn elevation-3 mb-3"
                >
                  <v-icon start size="18">mdi-clipboard-edit-outline</v-icon>
                  <span>Daftar Kegiatan</span>
                  <v-icon end size="16">mdi-arrow-right</v-icon>
                </v-btn>

                <v-btn
                  v-else
                  disabled
                  color="grey-lighten-2"
                  size="large"
                  block
                  rounded="pill"
                  class="font-weight-bold mb-3"
                >
                  <v-icon start size="18">mdi-calendar-check</v-icon>
                  <span>Kegiatan Telah Selesai</span>
                </v-btn>

                <!-- Share Buttons -->
                <div class="share-box pt-3 border-top">
                  <span class="share-label mb-2 d-block">Bagikan ke Teman / Grup:</span>
                  <div class="d-flex align-center ga-2">
                    <button
                      type="button"
                      class="share-icon-btn whatsapp"
                      title="Bagikan via WhatsApp"
                      @click="shareEvent('whatsapp')"
                    >
                      <v-icon size="18">mdi-whatsapp</v-icon>
                      <span class="share-name">WhatsApp</span>
                    </button>
                    <button
                      type="button"
                      class="share-icon-btn copy-link flex-grow-1"
                      title="Salin Link Halaman Ini"
                      @click="copyLink"
                    >
                      <v-icon size="16">mdi-link-variant</v-icon>
                      <span class="share-name">Salin Link</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </v-col>

          <!-- SISI KANAN (Info Kunci, Rute, Deskripsi, Video, Tips) -->
          <v-col cols="12" md="7" lg="8" class="order-2 order-md-2">
            <!-- 1. Quick Info Pills Grid (Tanggal, Jam, Titik Kumpul, Jarak) -->
            <div class="specs-grid-card mb-6">
              <!-- Tanggal -->
              <div class="spec-cell">
                <div class="spec-icon-wrap icon-date">
                  <v-icon size="20" color="#DC2626">mdi-calendar-clock</v-icon>
                </div>
                <div class="spec-text">
                  <div class="spec-label">Hari &amp; Tanggal</div>
                  <div class="spec-value">{{ formatDate(event.date) }}</div>
                </div>
              </div>

              <!-- Waktu -->
              <div class="spec-cell">
                <div class="spec-icon-wrap icon-time">
                  <v-icon size="20" color="#D97706">mdi-clock-outline</v-icon>
                </div>
                <div class="spec-text">
                  <div class="spec-label">Waktu Kumpul</div>
                  <div class="spec-value">{{ event.time ? `${event.time} WITA` : '06.00 WITA - Selesai' }}</div>
                </div>
              </div>

              <!-- Titik Kumpul -->
              <div class="spec-cell" :class="{ 'spec-cell-curated': isCuratedTikum }">
                <div class="spec-icon-wrap" :class="isCuratedTikum ? 'icon-secret' : 'icon-location'">
                  <v-icon size="20" :color="isCuratedTikum ? '#D97706' : '#0284C7'">
                    {{ isCuratedTikum ? 'mdi-shield-lock-outline' : 'mdi-map-marker-radius' }}
                  </v-icon>
                </div>
                <div class="spec-text">
                  <div class="spec-label d-flex align-center ga-1">
                    <span>Titik Kumpul (Tikum)</span>
                    <span v-if="isCuratedTikum" class="secret-chip">Rahasia</span>
                  </div>
                  <div class="spec-value" :class="{ 'text-amber-darken-3 font-weight-bold': isCuratedTikum }">
                    {{ isCuratedTikum ? '🔒 Rahasia (Sistem Kurasi)' : getEventLocation(event) }}
                  </div>
                  <div v-if="isCuratedTikum" class="spec-subnote mt-0.5">
                    Dikirim via WhatsApp/DM setelah kurasi
                  </div>
                </div>
              </div>

              <!-- Jarak & Durasi (jika tipe walking) -->
              <div v-if="event.type === 'walking' && (event.distance || event.estimated_duration)" class="spec-cell">
                <div class="spec-icon-wrap icon-walk">
                  <v-icon size="20" color="#16A34A">mdi-map-marker-distance</v-icon>
                </div>
                <div class="spec-text">
                  <div class="spec-label">Estimasi Jarak &amp; Durasi</div>
                  <div class="spec-value">
                    {{ event.distance ? `${event.distance} km` : '±3–5 km' }}
                    <span v-if="event.estimated_duration"> • ±{{ event.estimated_duration }} Menit</span>
                  </div>
                </div>
              </div>

              <!-- HTM / Biaya -->
              <div class="spec-cell">
                <div class="spec-icon-wrap icon-fee">
                  <v-icon size="20" color="#6366F1">mdi-ticket-outline</v-icon>
                </div>
                <div class="spec-text">
                  <div class="spec-label">Biaya &amp; Sistem</div>
                  <div class="spec-value">{{ isCuratedTikum ? 'Gratis • Sistem Kurasi' : 'Gratis • Terbuka untuk Umum' }}</div>
                </div>
              </div>
            </div>

            <!-- Curation Info Banner (Khusus Tikum Rahasia Jalan Bareng Makassar) -->
            <div v-if="isCuratedTikum" class="curation-info-banner mb-5 pa-3 pa-sm-4 rounded-xl border">
              <div class="d-flex align-center ga-2 mb-2">
                <v-avatar color="#FEF3C7" size="32" class="flex-shrink-0">
                  <v-icon size="18" color="#D97706">mdi-shield-lock-outline</v-icon>
                </v-avatar>
                <div>
                  <div class="font-weight-bold text-subtitle-2 text-grey-darken-4 d-flex align-center ga-1.5 flex-wrap">
                    <span>Sistem Kurasi Peserta</span>
                    <span class="secret-chip">Private Tikum</span>
                  </div>
                </div>
              </div>
              <p class="curation-intro-text text-caption text-grey-darken-2 mb-2.5" style="line-height: 1.45;">
                Demi ketertiban dan kenyamanan pejalan kaki, titik kumpul dirahasiakan &amp; dikirimkan privat ke WhatsApp/DM khusus bagi peserta yang lolos kurasi.
              </p>
              <div class="curation-mini-flow d-flex align-center justify-space-between ga-1 pa-2 rounded-lg">
                <div class="flow-step text-center">
                  <span class="flow-num">1</span>
                  <span class="flow-txt">Isi Form</span>
                </div>
                <v-icon size="14" color="#D97706">mdi-chevron-right</v-icon>
                <div class="flow-step text-center">
                  <span class="flow-num">2</span>
                  <span class="flow-txt">Kurasi Tim</span>
                </div>
                <v-icon size="14" color="#D97706">mdi-chevron-right</v-icon>
                <div class="flow-step text-center">
                  <span class="flow-num">3</span>
                  <span class="flow-txt">Tikum via WA/DM</span>
                </div>
              </div>
            </div>

            <!-- 2. Mobile In-Page Single Hero CTA Bar (SATU-SATUNYA Tombol Utama di Mobile) -->
            <div ref="heroCtaRef" class="mobile-single-cta-wrap d-block d-md-none mb-6">
              <v-btn
                v-if="isUpcoming"
                :href="event.registration_link || 'https://instagram.com/jalanbarengind'"
                target="_blank"
                rel="noopener noreferrer"
                color="#DC2626"
                size="large"
                block
                rounded="pill"
                class="text-white font-weight-bold mobile-primary-btn elevation-3 mb-2.5"
              >
                <v-icon start size="18">mdi-clipboard-edit-outline</v-icon>
                <span>Daftar Kegiatan</span>
                <v-icon end size="16">mdi-arrow-right</v-icon>
              </v-btn>

              <div class="d-flex align-center justify-space-between px-1">
                <span class="text-caption text-grey-darken-2 font-weight-medium">
                  {{ isCuratedTikum ? '🔒 Seleksi Peserta via Form' : '🎟️ Pendaftaran Terbuka' }}
                </span>
                <div class="d-flex align-center ga-1.5">
                  <button
                    type="button"
                    class="mobile-mini-share whatsapp"
                    @click="shareEvent('whatsapp')"
                    title="Bagikan ke WhatsApp"
                  >
                    <v-icon size="13" color="#15803D">mdi-whatsapp</v-icon>
                    <span>WA</span>
                  </button>
                  <button
                    type="button"
                    class="mobile-mini-share"
                    @click="copyLink"
                    title="Salin Link Event"
                  >
                    <v-icon size="13">mdi-link-variant</v-icon>
                    <span>Salin</span>
                  </button>
                </div>
              </div>
            </div>

            <!-- 3. Seksi Tentang Event (Deskripsi) -->
            <section class="content-panel mb-8">
              <div class="panel-header d-flex align-center ga-2 mb-4">
                <v-icon size="22" color="#DC2626">mdi-text-box-outline</v-icon>
                <h2 class="panel-title mb-0">Tentang Agenda Ini</h2>
              </div>
              <div class="description-rich-text" v-html="event.description || '<p>Tidak ada deskripsi rinci untuk agenda ini.</p>'"></div>
            </section>

            <!-- 4. Seksi Rute Jalan Kaki (Google Maps Viewer) -->
            <section v-if="event.type === 'walking' && event.route && event.route.length > 0" class="content-panel mb-8">
              <div class="panel-header d-flex align-center justify-space-between flex-wrap ga-2 mb-4">
                <div class="d-flex align-center ga-2">
                  <v-icon size="22" color="#16A34A">mdi-map-legend</v-icon>
                  <h2 class="panel-title mb-0">Rute &amp; Peta Penjelajahan</h2>
                </div>
                <span class="route-length-badge" v-if="event.distance">
                  {{ event.distance }} km Jalur Pejalan
                </span>
              </div>

              <!-- Start / Finish Indicator Cards -->
              <div class="route-points-banner d-flex align-center ga-4 mb-4 flex-wrap">
                <div class="point-badge start">
                  <v-icon size="16" :color="isCuratedTikum ? '#D97706' : '#16A34A'">
                    {{ isCuratedTikum ? 'mdi-shield-lock-outline' : 'mdi-flag-variant' }}
                  </v-icon>
                  <div>
                    <span class="point-role">Titik Start:</span>
                    <span class="point-name">
                      {{ isCuratedTikum ? '🔒 Rahasia (Dikirim ke peserta lolos kurasi)' : getEventLocation(event) }}
                    </span>
                  </div>
                </div>
                <v-icon color="grey-lighten-1" size="18" class="d-none d-sm-block">mdi-arrow-right</v-icon>
                <div class="point-badge finish">
                  <v-icon size="16" color="#DC2626">mdi-flag-checkered</v-icon>
                  <div>
                    <span class="point-role">Titik Finish:</span>
                    <span class="point-name">Area Sekitar &amp; Kafe Titik Kumpul</span>
                  </div>
                </div>
              </div>

              <!-- Google Maps Viewer -->
              <div class="map-wrapper-card">
                <ClientOnly>
                  <GoogleMapRouteViewer :route="event.route" />
                </ClientOnly>
              </div>
            </section>

            <!-- 5. Seksi Video YouTube (Jika Ada) -->
            <section v-if="event.youtube_link" class="content-panel mb-8">
              <div class="panel-header d-flex align-center ga-2 mb-4">
                <v-icon size="22" color="#DC2626">mdi-youtube</v-icon>
                <h2 class="panel-title mb-0">Dokumentasi &amp; Teaser</h2>
              </div>
              <div class="video-container-card">
                <iframe
                  :src="getYouTubeEmbedUrl(event.youtube_link)"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen
                  class="youtube-frame"
                ></iframe>
              </div>
            </section>

            <!-- 6. Seksi Perlengkapan & Tips Jalan Bareng -->
            <section class="content-panel mb-8">
              <div class="panel-header d-flex align-center ga-2 mb-4">
                <v-icon size="22" color="#D97706">mdi-check-circle-outline</v-icon>
                <h2 class="panel-title mb-0">Tips &amp; Perlengkapan yang Perlu Disiapkan</h2>
              </div>

              <v-row dense class="ga-y-3">
                <v-col v-for="(tip, idx) in eventTips" :key="idx" cols="12" sm="6">
                  <div class="tip-card d-flex align-start ga-3 pa-4">
                    <div class="tip-number-circle">{{ idx + 1 }}</div>
                    <div class="tip-text">{{ tip.text }}</div>
                  </div>
                </v-col>
              </v-row>
            </section>

            <!-- 7. Benefit & Budaya Jalan Bareng -->
            <section class="content-panel">
              <div class="panel-header d-flex align-center ga-2 mb-4">
                <v-icon size="22" color="#0284C7">mdi-account-heart-outline</v-icon>
                <h2 class="panel-title mb-0">Suasana di Jalan Bareng</h2>
              </div>
              <v-row dense class="ga-y-3">
                <v-col v-for="item in whatToExpect" :key="item.title" cols="12" sm="6">
                  <div class="benefit-card d-flex align-start ga-3 pa-4">
                    <v-avatar size="36" color="#EFF6FF" class="flex-shrink-0">
                      <v-icon size="20" color="#0284C7">{{ item.icon }}</v-icon>
                    </v-avatar>
                    <div>
                      <div class="benefit-card-title">{{ item.title }}</div>
                      <div class="benefit-card-subtitle">{{ item.subtitle }}</div>
                    </div>
                  </div>
                </v-col>
              </v-row>
            </section>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <!-- 3. Mobile Floating Sticky CTA Button (Auto-scroll ke tombol asli) -->
    <Transition name="cta-morph">
      <div
        v-if="showMobileStickyBar && event && isUpcoming"
        class="mobile-floating-cta-container d-flex d-md-none"
      >
        <button
          type="button"
          class="mobile-floating-pill-btn"
          @click="scrollToHeroCta"
          aria-label="Gulir ke Form Pendaftaran Kegiatan"
        >
          <div class="d-flex align-center justify-center ga-2">
            <v-icon size="18" color="white">mdi-clipboard-edit-outline</v-icon>
            <span class="pill-btn-text">Daftar Kegiatan</span>
            <v-icon size="17" color="white" class="pill-btn-arrow">
              {{ isAboveHeroCta ? 'mdi-arrow-down' : 'mdi-arrow-up' }}
            </v-icon>
          </div>
        </button>
      </div>
    </Transition>

    <!-- 4. Lightbox Modal Zoom Poster Resolusi Penuh -->
    <v-dialog v-model="lightboxDialog" max-width="700">
      <v-card rounded="xl" class="lightbox-card pa-3">
        <div class="d-flex align-center justify-space-between pb-2 px-2 border-bottom">
          <span class="font-weight-bold text-subtitle-2 text-grey-darken-3">Poster Resolusi Penuh</span>
          <v-btn icon size="small" variant="text" @click="lightboxDialog = false">
            <v-icon size="20">mdi-close</v-icon>
          </v-btn>
        </div>
        <div class="lightbox-img-wrap pa-2 text-center">
          <img
            v-if="event?.poster"
            :src="getImageUrl(event.poster)"
            :alt="event?.name"
            class="lightbox-img"
          />
        </div>
        <div class="pa-2 d-flex justify-end ga-2 border-top">
          <v-btn
            v-if="event?.poster"
            :href="getImageUrl(event.poster)"
            target="_blank"
            rel="noopener noreferrer"
            variant="text"
            color="primary"
            size="small"
            class="font-weight-bold"
          >
            <v-icon start size="16">mdi-open-in-new</v-icon>
            Buka Tab Baru
          </v-btn>
          <v-btn variant="flat" color="grey-lighten-3" size="small" rounded="pill" @click="lightboxDialog = false">
            Tutup
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- 5. Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="420">
      <v-card rounded="xl" class="pa-4">
        <v-card-title class="font-weight-bold text-h6 text-grey-darken-4">Hapus Agenda Event?</v-card-title>
        <v-card-text class="text-body-2 text-grey-darken-1">
          Apakah Anda yakin ingin menghapus <strong>{{ event?.name }}</strong>? Tindakan ini tidak dapat dibatalkan.
        </v-card-text>
        <v-card-actions class="pt-2">
          <v-spacer></v-spacer>
          <v-btn variant="text" rounded="pill" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="#DC2626" variant="flat" rounded="pill" class="text-white px-5" @click="deleteEvent" :loading="deleting">
            Hapus
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- 6. Snackbar Feedback -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000" location="top" rounded="pill">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { defineAsyncComponent } from 'vue'
import { useHead } from '#app'

const RouteMapViewer = defineAsyncComponent(() => import('~/components/events/RouteMapViewer.vue'))
const GoogleMapRouteViewer = defineAsyncComponent(() => import('~/components/events/GoogleMapRouteViewer.vue'))

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const router = useRouter()
const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const event = ref<any>(null)
const loading = ref(true)
const deleteDialog = ref(false)
const deleting = ref(false)
const lightboxDialog = ref(false)
const showMobileStickyBar = ref(false)
const isAboveHeroCta = ref(true)
const heroCtaRef = ref<HTMLElement | null>(null)

const stripHtml = (html: string) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, '').trim()
}

useSeoMeta({
  title: () => event.value ? `${event.value.name} - Jalan Bareng` : 'Detail Event - Jalan Bareng',
  ogTitle: () => event.value ? `${event.value.name} - Jalan Bareng` : 'Detail Event - Jalan Bareng',
  description: () => event.value ? `Ikuti event ${event.value.name}. ${event.value.description ? stripHtml(event.value.description).substring(0, 150) : ''}...` : 'Detail event komunitas di Jalan Bareng.',
  ogDescription: () => event.value ? `Ikuti event ${event.value.name}. ${event.value.description ? stripHtml(event.value.description).substring(0, 150) : ''}...` : 'Detail event komunitas di Jalan Bareng.',
  ogImage: () => event.value?.poster ? getImageUrl(event.value.poster) : 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const canEdit = computed(() => {
  return authStore.isAdmin || authStore.isCommunityAdmin
})

const isUpcoming = computed(() => {
  if (!event.value?.date) return true
  const eventDate = new Date(event.value.date)
  const now = new Date()
  return eventDate >= now
})

const isCuratedTikum = computed(() => {
  if (!event.value) return false
  const activationSlug = event.value.activation?.slug || ''
  const activationName = (event.value.activation?.name || event.value.activation?.title || '').toLowerCase()
  const eventName = (event.value.name || '').toLowerCase()
  const desc = (event.value.description || '').toLowerCase()

  // Jalan Bareng Makassar is specifically curated with secret tikum
  if (activationSlug === 'jalan-bareng-makassar' || activationName.includes('jalan bareng makassar') || event.value.activation_id === 2) {
    return true
  }
  if (eventName.includes('jalan bareng makassar') || (eventName.includes('makassar') && event.value.type === 'walking')) {
    return true
  }
  if (desc.includes('kurasi') || desc.includes('tikum rahasia') || desc.includes('titik kumpul rahasia')) {
    return true
  }
  return false
})

const whatToExpect = ref([
  { icon: 'mdi-account-group', title: 'Komunitas Terbuka', subtitle: 'Bertemu kawan baru dan saling sapa sesama pejalan kaki' },
  { icon: 'mdi-run', title: 'Aktivitas Menyehatkan', subtitle: 'Olahraga santai akhir pekan menyusuri ruang publik' },
  { icon: 'mdi-lightbulb-on-outline', title: 'Wawasan & Refleksi', subtitle: 'Mengenal sejarah kota, literasi buku, dan cerita lokal' },
  { icon: 'mdi-camera', title: 'Dokumentasi Menarik', subtitle: 'Momen kebersamaan diabadikan tim dokumentasi komunitas' }
])

const eventTips = ref([
  { text: 'Datang 15 menit lebih awal di titik kumpul untuk briefing & pemanasan' },
  { text: 'Kenakan pakaian santai yang menyerap keringat dan sepatu nyaman' },
  { text: 'Bawa botol minum pribadi (tumbler) untuk kurangi sampah plastik sekali pakai' },
  { text: 'Ikuti instruksi tim pemandu (marshal) demi keamanan di penyeberangan jalan' }
])

const { getImageUrl } = useImageUrl()

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getEventLocation = (ev: any) => {
  if (!ev) return 'Makassar & Sekitarnya'
  if (isCuratedTikum.value) {
    return '🔒 Rahasia (Sistem Kurasi Peserta)'
  }
  if (ev.meeting_point) return ev.meeting_point
  if (ev.description) {
    const match = ev.description.match(/Titik Kumpul:?\s*<\/strong>\s*([^<]+)/i) ||
                  ev.description.match(/Titik Kumpul:?\s*([^<\n]+)/i)
    if (match && match[1]) return match[1].trim()
    const ruteMatch = ev.description.match(/Rute:?\s*<\/strong>\s*([^<]+)/i) ||
                      ev.description.match(/Rute:?\s*([^<\n]+)/i)
    if (ruteMatch && ruteMatch[1]) return ruteMatch[1].trim().split('-')[0].trim()
  }
  if (ev.activation?.city) return ev.activation.city
  return 'Makassar'
}

const getYouTubeEmbedUrl = (url: string) => {
  if (!url) return ''
  const videoId = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/)?.[1]
  return videoId ? `https://www.youtube.com/embed/${videoId}` : ''
}

const shareEvent = (platform: string) => {
  const url = window.location.href
  const text = `Yuk ikut event ${event.value?.name || 'Jalan Bareng'}:`

  let shareUrl = ''
  if (platform === 'whatsapp') {
    shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
  }

  if (shareUrl) {
    window.open(shareUrl, '_blank', 'noopener,noreferrer')
  }
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    showSnackbar('Link event berhasil disalin!', 'success')
  } catch (error) {
    showSnackbar('Gagal menyalin link', 'error')
  }
}

const showSnackbar = (text: string, color: string = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

const fetchEvent = async () => {
  loading.value = true
  try {
    const response = await api.get(`/events/${route.params.id}`)
    event.value = response.data.event
  } catch (error) {
    console.error('Error fetching event:', error)
    event.value = null
  } finally {
    loading.value = false
    await nextTick()
    setupCtaObserver()
  }
}

const confirmDelete = () => {
  deleteDialog.value = true
}

const deleteEvent = async () => {
  deleting.value = true
  try {
    await api.delete(`/events/${route.params.id}`)
    showSnackbar('Event berhasil dihapus', 'success')
    setTimeout(() => {
      router.push('/events')
    }, 1200)
  } catch (error) {
    console.error('Error deleting event:', error)
    showSnackbar('Gagal menghapus event', 'error')
  } finally {
    deleting.value = false
    deleteDialog.value = false
  }
}

let ctaObserver: IntersectionObserver | null = null

const checkCtaVisibility = () => {
  if (typeof window === 'undefined') return

  if (window.innerWidth >= 960) {
    showMobileStickyBar.value = false
    return
  }

  const el = heroCtaRef.value
  if (!el) {
    showMobileStickyBar.value = true
    isAboveHeroCta.value = true
    return
  }

  const rect = el.getBoundingClientRect()
  // The real in-page CTA is considered in the viewport when:
  // its bottom is below top bar (> 70px) AND its top is above the bottom nav (< window.innerHeight - 80px)
  const isInViewport = rect.bottom > 70 && rect.top < (window.innerHeight - 80)

  // Floating button shows when the real button is OUT of viewport (either above or below)!
  // When real button is IN viewport, floating button is hidden!
  showMobileStickyBar.value = !isInViewport

  // If rect.top >= (window.innerHeight - 80), user is above the button (arrow down)
  // Otherwise user has scrolled past it to the bottom (arrow up)
  isAboveHeroCta.value = rect.top >= (window.innerHeight - 80)
}

const setupCtaObserver = () => {
  if (typeof window === 'undefined') return

  if (ctaObserver) {
    ctaObserver.disconnect()
    ctaObserver = null
  }

  const el = heroCtaRef.value
  if (!el) return

  if ('IntersectionObserver' in window) {
    ctaObserver = new IntersectionObserver(
      () => {
        checkCtaVisibility()
      },
      {
        root: null,
        rootMargin: '-70px 0px -80px 0px',
        threshold: [0, 0.2, 0.5, 1.0]
      }
    )
    ctaObserver.observe(el)
  }

  checkCtaVisibility()
}

const scrollToHeroCta = () => {
  if (!heroCtaRef.value) return
  heroCtaRef.value.scrollIntoView({
    behavior: 'smooth',
    block: 'center'
  })
}

watch(event, async (newVal) => {
  if (newVal) {
    await nextTick()
    setupCtaObserver()
  }
})

onMounted(() => {
  fetchEvent()
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', checkCtaVisibility, { passive: true })
    window.addEventListener('resize', checkCtaVisibility, { passive: true })
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', checkCtaVisibility)
    window.removeEventListener('resize', checkCtaVisibility)
  }
  if (ctaObserver) {
    ctaObserver.disconnect()
    ctaObserver = null
  }
})
</script>

<style scoped>
/* ============================================
   JALAN BARENG - EVENT DETAIL EDITORIAL SYSTEM
   Design System: #DC2626 primary, #111827 text, #FAFAF9 bg
   ============================================ */

.event-detail-page {
  background-color: #FAFAF9;
  min-height: 100vh;
  color: #111827;
}

.loading-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}

.not-found-card {
  max-width: 500px;
  background: white;
  border-radius: 20px;
  border: 1px solid #E5E7EB;
}

/* 1. Clean Editorial Header Section */
.event-header-section {
  background: white;
  border-bottom: 1px solid #E5E7EB;
  padding: 24px 0 28px;
}

.event-breadcrumbs {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  font-size: 13px;
  color: #6B7280;
}

.bc-link {
  display: inline-flex;
  align-items: center;
  color: #6B7280;
  text-decoration: none;
  transition: color 0.15s ease;
}

.bc-link:hover {
  color: #DC2626;
}

.bc-sep {
  color: #D1D5DB;
}

.bc-current {
  color: #111827;
  font-weight: 600;
  max-width: 280px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.event-type-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.type-walking {
  background: #DCFCE7;
  color: #15803D;
  border: 1px solid #BBF7D0;
}

.type-regular {
  background: #E0F2FE;
  color: #0369A1;
  border: 1px solid #BAE6FD;
}

.event-status-pill {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 700;
}

.event-status-pill.upcoming {
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FECACA;
}

.event-status-pill.past {
  background: #F3F4F6;
  color: #6B7280;
  border: 1px solid #E5E7EB;
}

.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #DC2626;
  margin-right: 6px;
  animation: pulse-ring 1.8s infinite;
}

@keyframes pulse-ring {
  0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
  70% { box-shadow: 0 0 0 6px rgba(220, 38, 38, 0); }
  100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
}

.event-headline {
  font-size: 32px;
  font-weight: 800;
  line-height: 1.25;
  letter-spacing: -0.03em;
  color: #111827;
}

@media (min-width: 960px) {
  .event-headline {
    font-size: 40px;
  }
}

.event-organizer-row {
  font-size: 14px;
}

.organizer-name strong {
  color: #111827;
}

.verified-tag {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 11px;
  font-weight: 600;
  color: #16A34A;
  background: #F0FDF4;
  padding: 2px 8px;
  border-radius: 9999px;
}

/* 2. Poster Showcase & Action Cards */
.sidebar-sticky-wrap {
  position: sticky;
  top: 90px;
}

.poster-card-frame {
  background: white;
  border-radius: 20px;
  padding: 8px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
}

.poster-image-container {
  position: relative;
  width: 100%;
  aspect-ratio: 4 / 5;
  border-radius: 16px;
  overflow: hidden;
  background-color: #1F2937;
  cursor: pointer;
}

.poster-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  transition: transform 0.3s ease;
}

.poster-image-container:hover .poster-img {
  transform: scale(1.02);
}

.poster-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #F3F4F6;
}

.poster-zoom-btn {
  position: absolute;
  bottom: 12px;
  right: 12px;
  background: rgba(17, 24, 39, 0.75);
  backdrop-filter: blur(4px);
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: background 0.2s ease;
}

.poster-zoom-btn:hover {
  background: rgba(17, 24, 39, 0.95);
}

/* Registration CTA Box */
.registration-cta-card {
  background: white;
  border-radius: 20px;
  padding: 24px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
}

.cta-status-label {
  font-size: 12px;
  font-weight: 700;
  color: #6B7280;
}

.cta-status-label.status-open {
  color: #16A34A;
}

.cta-quota-badge {
  font-size: 11px;
  background: #F3F4F6;
  color: #4B5563;
  padding: 2px 8px;
  border-radius: 9999px;
  font-weight: 600;
}

.cta-title {
  font-size: 18px;
  font-weight: 800;
  color: #111827;
  margin: 0 0 6px;
}

.cta-desc {
  font-size: 13px;
  color: #6B7280;
  line-height: 1.5;
  margin: 0 0 16px;
}

.cta-submit-btn {
  letter-spacing: 0.01em;
  font-size: 15px !important;
  text-transform: none;
  height: 48px !important;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35) !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}

.cta-submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(220, 38, 38, 0.45) !important;
}

.share-label {
  font-size: 12px;
  font-weight: 600;
  color: #6B7280;
}

.share-icon-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
  border: 1px solid #E5E7EB;
  background: #F9FAFB;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
}

.share-icon-btn:hover {
  background: white;
  border-color: #D1D5DB;
  transform: translateY(-1px);
}

.share-icon-btn.whatsapp:hover {
  background: #DCFCE7;
  color: #15803D;
  border-color: #86EFAC;
}

/* 3. Specs Grid */
.specs-grid-card {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  background: white;
  padding: 16px;
  border-radius: 20px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.04);
}

.spec-cell {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  border-radius: 14px;
  background: #F9FAFB;
  transition: background 0.2s ease;
}

.spec-cell:hover {
  background: #F3F4F6;
}

.spec-icon-wrap {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-date { background: #FEE2E2; }
.icon-time { background: #FEF3C7; }
.icon-location { background: #E0F2FE; }
.icon-walk { background: #DCFCE7; }
.icon-fee { background: #EEF2FF; }
.icon-secret { background: #FEF3C7; }

.secret-chip {
  font-size: 10px;
  font-weight: 700;
  color: #B45309;
  background: #FEF3C7;
  padding: 2px 7px;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  border: 1px solid #FDE68A;
}

.spec-subnote {
  font-size: 11px;
  color: #6B7280;
  line-height: 1.3;
}

.spec-cell-curated {
  background: #FFFDF5 !important;
  border: 1px solid #FDE68A;
}

.curation-info-banner {
  background: #FFFDF5;
  border-color: #FDE68A !important;
  box-shadow: 0 4px 15px -3px rgba(217, 119, 6, 0.08);
}

.spec-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  color: #6B7280;
  margin-bottom: 2px;
}

.spec-value {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  line-height: 1.3;
}

/* 4. Content Panels */
.content-panel {
  background: white;
  border-radius: 20px;
  padding: 24px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.04);
}

.panel-title {
  font-size: 18px;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.02em;
}

.description-rich-text {
  font-size: 15px;
  line-height: 1.8;
  color: #374151;
}

.description-rich-text :deep(p) {
  margin-bottom: 14px;
}

.description-rich-text :deep(p:last-child) {
  margin-bottom: 0;
}

.description-rich-text :deep(strong) {
  color: #111827;
  font-weight: 700;
}

.description-rich-text :deep(ul), .description-rich-text :deep(ol) {
  padding-left: 20px;
  margin-bottom: 14px;
}

.description-rich-text :deep(li) {
  margin-bottom: 6px;
}

/* Route Map */
.route-length-badge {
  font-size: 12px;
  font-weight: 700;
  color: #15803D;
  background: #DCFCE7;
  padding: 4px 12px;
  border-radius: 9999px;
}

.route-points-banner {
  background: #F9FAFB;
  border-radius: 14px;
  padding: 12px 16px;
  border: 1px solid #F3F4F6;
}

.point-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.point-role {
  font-size: 11px;
  font-weight: 600;
  color: #6B7280;
  display: block;
}

.point-name {
  font-weight: 700;
  color: #111827;
}

.map-wrapper-card {
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #E5E7EB;
}

/* Video */
.video-container-card {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  border-radius: 16px;
  background: #000;
}

.youtube-frame {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* Tips & Benefits */
.tip-card {
  background: #F9FAFB;
  border-radius: 14px;
  border: 1px solid #F3F4F6;
  height: 100%;
}

.tip-number-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #D97706;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
}

.tip-text {
  font-size: 13px;
  color: #4B5563;
  line-height: 1.5;
}

.benefit-card {
  background: #F9FAFB;
  border-radius: 14px;
  border: 1px solid #F3F4F6;
  height: 100%;
}

.benefit-card-title {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 2px;
}

.benefit-card-subtitle {
  font-size: 12px;
  color: #6B7280;
  line-height: 1.4;
}

/* Curation Mini Flow Stepper */
.curation-mini-flow {
  background: rgba(254, 243, 199, 0.5);
  border: 1px dashed #FDE68A;
}

.flow-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  flex: 1;
}

.flow-num {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #D97706;
  color: white;
  font-size: 10px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
}

.flow-txt {
  font-size: 10px;
  font-weight: 700;
  color: #92400E;
  white-space: nowrap;
}

/* Mobile In-Page Single Hero CTA Box */
.mobile-single-cta-wrap {
  background: white;
  border-radius: 18px;
  padding: 14px;
  border: 1px solid #E5E7EB;
  box-shadow: 0 4px 15px -2px rgba(0, 0, 0, 0.04);
}

.mobile-primary-btn {
  letter-spacing: 0.01em;
  font-size: 15px !important;
  text-transform: none;
  height: 46px !important;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35) !important;
}

.mobile-mini-share {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 9999px;
  font-size: 11px;
  font-weight: 600;
  border: 1px solid #E5E7EB;
  background: #F9FAFB;
  color: #4B5563;
  cursor: pointer;
  transition: all 0.15s ease;
}

.mobile-mini-share:hover {
  background: white;
  border-color: #D1D5DB;
}

.mobile-mini-share.whatsapp:hover {
  background: #DCFCE7;
  color: #15803D;
  border-color: #86EFAC;
}

/* 5. Mobile Floating Pill CTA (Auto-scroll to Real Button) */
.mobile-floating-cta-container {
  position: fixed;
  bottom: calc(5.2rem + env(safe-area-inset-bottom, 0px));
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 990;
  pointer-events: none;
  padding: 0 20px;
}

.mobile-floating-pill-btn {
  pointer-events: auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  max-width: 330px;
  width: 100%;
  height: 46px;
  background: #DC2626;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.25);
  border-radius: 9999px;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: 0.01em;
  box-shadow: 0 10px 25px -4px rgba(220, 38, 38, 0.5), 0 4px 12px -2px rgba(0, 0, 0, 0.2);
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.mobile-floating-pill-btn:hover {
  background: #B91C1C;
  transform: translateY(-2px);
  box-shadow: 0 12px 28px -4px rgba(220, 38, 38, 0.6);
}

.mobile-floating-pill-btn:active {
  transform: translateY(1px) scale(0.98);
}

.pill-btn-text {
  font-weight: 700;
  color: #FFFFFF;
}

.pill-btn-arrow {
  animation: floatArrow 1.5s ease-in-out infinite;
}

@keyframes floatArrow {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(3px);
  }
}

/* CTA Morph Transition: Smooth Spring Slide-up and Scale */
.cta-morph-enter-active,
.cta-morph-leave-active {
  transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
              opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1);
  will-change: transform, opacity;
}

.cta-morph-enter-from,
.cta-morph-leave-to {
  opacity: 0;
  transform: translateY(28px) scale(0.95);
}

/* Lightbox Modal */
.lightbox-card {
  background: white;
}

.lightbox-img-wrap {
  max-height: 75vh;
  overflow-y: auto;
}

.lightbox-img {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
  border-radius: 8px;
}

/* Responsive adjustments (< 768px / Mobile 390px) */
@media (max-width: 768px) {
  /* Tighter, clean editorial mobile header */
  .event-header-section {
    padding: 14px 0 16px;
  }

  .event-breadcrumbs {
    font-size: 11px;
    margin-bottom: 8px !important;
    gap: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .event-type-badge {
    font-size: 11px;
    padding: 2px 10px;
  }

  .event-status-pill {
    font-size: 11px;
    padding: 2px 10px;
  }

  .event-headline {
    font-size: 25px;
    line-height: 1.28;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
  }

  .event-organizer-row {
    font-size: 12px;
    gap: 6px;
  }

  /* Body container top space & bottom space for floating nav */
  .event-body-container {
    padding-top: 14px !important;
    padding-bottom: 120px !important;
  }

  /* Poster frame on mobile: neatly sized portrait frame */
  .poster-card-frame {
    max-width: 270px;
    margin: 0 auto;
    padding: 6px;
    border-radius: 16px;
  }

  .poster-image-container {
    aspect-ratio: 4 / 5;
    border-radius: 12px;
  }

  .poster-zoom-btn {
    bottom: 8px;
    right: 8px;
    padding: 4px 10px;
    font-size: 11px;
  }

  /* Compact 2-column specs grid (Reduces vertical space by 60%!) */
  .specs-grid-card {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    padding: 10px;
    border-radius: 16px;
    margin-bottom: 16px !important;
  }

  .spec-cell {
    padding: 8px 10px;
    border-radius: 10px;
    gap: 8px;
  }

  /* Titik Kumpul spans across both columns */
  .spec-cell:nth-child(3) {
    grid-column: 1 / -1;
  }

  .spec-icon-wrap {
    width: 28px;
    height: 28px;
    border-radius: 8px;
  }

  .spec-icon-wrap :deep(.v-icon) {
    font-size: 16px !important;
  }

  .spec-label {
    font-size: 9.5px;
    margin-bottom: 1px;
  }

  .spec-value {
    font-size: 12px;
    line-height: 1.25;
  }

  .spec-subnote {
    font-size: 10px;
    line-height: 1.2;
  }

  /* Content Panels on Mobile */
  .content-panel {
    padding: 16px 14px;
    border-radius: 16px;
    margin-bottom: 16px !important;
  }

  .panel-title {
    font-size: 16px;
  }

  .description-rich-text {
    font-size: 14px;
    line-height: 1.65;
  }

  /* Route indicators */
  .route-points-banner {
    padding: 10px 12px;
    gap: 10px;
  }

  .point-badge {
    font-size: 12px;
  }
}
</style>
