<template>
  <div class="profile-page">
    <!-- Hero Section: Editorial Jalan Bareng Aesthetic -->
    <section class="profile-hero">
      <div class="hero-overlay-glow"></div>
      <v-container class="hero-container">
        <div class="d-flex align-center flex-column flex-md-row ga-6 hero-content-row">
          <!-- Avatar with dynamic fallback -->
          <div class="avatar-wrapper">
            <v-avatar :size="isMobile ? 104 : 124" class="profile-avatar">
              <v-img
                v-if="userPhotoUrl"
                :src="userPhotoUrl"
                :alt="authStore.user?.name || 'Profil'"
                cover
              >
                <template v-slot:error>
                  <span class="avatar-initial">{{ userInitial }}</span>
                </template>
              </v-img>
              <span v-else class="avatar-initial">{{ userInitial }}</span>
            </v-avatar>
            <button
              type="button"
              class="avatar-edit-badge"
              aria-label="Ubah foto profil"
              @click="isEditing = true"
            >
              <v-icon size="15" color="white">mdi-camera</v-icon>
            </button>
          </div>

          <!-- User Details -->
          <div class="user-hero-details flex-grow-1 text-center text-md-left">
            <div class="d-flex align-center justify-center justify-md-start flex-wrap ga-2 mb-2">
              <span class="profile-kicker">PROFIL PEJALAN KAKI</span>
              <span class="role-pill" :class="roleClass">
                <v-icon size="13" class="mr-1">{{ roleIcon }}</v-icon>
                {{ roleLabel }}
              </span>
            </div>

            <h1 class="user-name">
              {{ authStore.user?.name || 'Pengguna Jalan Bareng' }}
            </h1>

            <p class="user-email">
              <v-icon size="16" class="mr-1 opacity-75">mdi-email-outline</v-icon>
              <span>{{ authStore.user?.email || 'Memuat akun...' }}</span>
            </p>

            <!-- Quick Stat Counters -->
            <div class="user-hero-stats">
              <div class="stat-pill">
                <v-icon size="16" color="#DC2626">mdi-map-marker</v-icon>
                <span class="stat-num">{{ profileData?.destinations_count || 0 }}</span>
                <span class="stat-txt">Destinasi</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-pill">
                <v-icon size="16" color="#0284C7">mdi-text-box-outline</v-icon>
                <span class="stat-num">{{ profileData?.stories_count || 0 }}</span>
                <span class="stat-txt">Tulisan</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-pill">
                <v-icon size="16" color="#16A34A">mdi-calendar-check</v-icon>
                <span class="stat-num">{{ profileData?.events_count || 0 }}</span>
                <span class="stat-txt">Event</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-pill">
                <v-icon size="16" color="#D97706">mdi-wallet-bifold-outline</v-icon>
                <span class="stat-num">{{ profileData?.points || 0 }}</span>
                <span class="stat-txt">Poin</span>
              </div>
            </div>
          </div>

          <!-- Hero Action Button -->
          <div class="hero-action-box d-none d-md-block">
            <v-btn
              color="#DC2626"
              variant="flat"
              rounded="pill"
              size="large"
              class="font-weight-bold text-white px-6 edit-hero-btn"
              @click="isEditing = true"
            >
              <v-icon start size="18">mdi-pencil</v-icon>
              Edit Profil
            </v-btn>
          </div>
        </div>
      </v-container>
    </section>

    <!-- Main Content Container -->
    <v-container class="content-section">
      <v-row>
        <!-- Main Content (Tabs & Content) -->
        <v-col cols="12" lg="8">
          <v-card elevation="0" rounded="xl" class="main-card">
            <!-- Tabs Bar -->
            <div class="tabs-bar-wrapper">
              <v-tabs
                v-model="tab"
                color="#DC2626"
                slider-color="#DC2626"
                align-tabs="start"
                class="custom-tabs"
              >
                <v-tab value="profile" class="tab-item">
                  <v-icon start size="18">mdi-badge-account-outline</v-icon>
                  Informasi Profil
                </v-tab>
                <v-tab value="points" class="tab-item">
                  <v-icon start size="18" color="#DC2626">mdi-wallet-bifold-outline</v-icon>
                  Poin Kontributor
                </v-tab>
                <v-tab value="destinations" class="tab-item">
                  <v-icon start size="18">mdi-map-marker-multiple-outline</v-icon>
                  Destinasi
                  <span v-if="profileData?.destinations_count" class="tab-badge ml-2">
                    {{ profileData.destinations_count }}
                  </span>
                </v-tab>
                <v-tab value="events" class="tab-item">
                  <v-icon start size="18">mdi-calendar-star-outline</v-icon>
                  Event
                  <span v-if="profileData?.events_count" class="tab-badge success-badge ml-2">
                    {{ profileData.events_count }}
                  </span>
                </v-tab>
                <v-tab value="stories" class="tab-item">
                  <v-icon start size="18">mdi-text-box-outline</v-icon>
                  Cerita
                  <span v-if="profileData?.stories_count" class="tab-badge ml-2">
                    {{ profileData.stories_count }}
                  </span>
                </v-tab>
                <v-tab value="comments" class="tab-item">
                  <v-icon start size="18">mdi-comment-multiple-outline</v-icon>
                  Komentar
                </v-tab>
                <v-tab value="saved" class="tab-item">
                  <v-icon start size="18" color="#DC2626">mdi-bookmark-outline</v-icon>
                  Disimpan
                  <span v-if="bookmarksCount" class="tab-badge ml-2">
                    {{ bookmarksCount }}
                  </span>
                </v-tab>
              </v-tabs>
            </div>

            <v-divider></v-divider>

            <!-- Tabs Windows -->
            <v-window v-model="tab">
              <!-- TAB 1: Profile Information -->
              <v-window-item value="profile">
                <div class="tab-inner-content">
                  <div class="section-title-row d-flex align-center justify-space-between flex-wrap ga-3 mb-6">
                    <div>
                      <h2 class="section-title">Informasi Pribadi & Kontak</h2>
                      <p class="section-subtitle">Data akun Anda yang terdaftar di komunitas Jalan Bareng</p>
                    </div>
                    <v-btn
                      variant="outlined"
                      color="#DC2626"
                      rounded="pill"
                      size="default"
                      class="font-weight-bold"
                      @click="isEditing = true"
                    >
                      <v-icon start size="16">mdi-pencil-outline</v-icon>
                      Perbarui Data
                    </v-btn>
                  </div>

                  <!-- Details Grid -->
                  <div class="info-tiles-grid">
                    <!-- Nama Lengkap -->
                    <div class="info-tile">
                      <div class="tile-icon-box">
                        <v-icon color="#DC2626" size="20">mdi-account</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Nama Lengkap</span>
                        <div class="tile-value">{{ authStore.user?.name || '-' }}</div>
                      </div>
                    </div>

                    <!-- Email -->
                    <div class="info-tile">
                      <div class="tile-icon-box">
                        <v-icon color="#0284C7" size="20">mdi-email</v-icon>
                      </div>
                      <div class="tile-content">
                        <div class="d-flex align-center ga-2">
                          <span class="tile-label">Alamat Email</span>
                          <span class="verified-chip">Terverifikasi</span>
                        </div>
                        <div class="tile-value">{{ authStore.user?.email || '-' }}</div>
                      </div>
                    </div>

                    <!-- Telepon / WA -->
                    <div class="info-tile">
                      <div class="tile-icon-box">
                        <v-icon color="#16A34A" size="20">mdi-whatsapp</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">WhatsApp / Telepon</span>
                        <div class="tile-value">
                          <a
                            v-if="authStore.user?.phone"
                            :href="`https://wa.me/${cleanPhone(authStore.user.phone)}`"
                            target="_blank"
                            class="active-link"
                          >
                            {{ authStore.user.phone }}
                            <v-icon size="14" class="ml-1">mdi-open-in-new</v-icon>
                          </a>
                          <span v-else class="text-grey">Belum diisi</span>
                        </div>
                      </div>
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div class="info-tile">
                      <div class="tile-icon-box">
                        <v-icon color="#F59E0B" size="20">mdi-calendar-check</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Bergabung Sejak</span>
                        <div class="tile-value">{{ formatJoinDate(authStore.user?.created_at) }}</div>
                      </div>
                    </div>

                    <!-- Instagram -->
                    <div class="info-tile">
                      <div class="tile-icon-box instagram">
                        <v-icon color="#E1306C" size="20">mdi-instagram</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Instagram</span>
                        <div class="tile-value">
                          <a
                            v-if="authStore.user?.instagram"
                            :href="formatSocialUrl(authStore.user.instagram, 'instagram')"
                            target="_blank"
                            class="active-link"
                          >
                            {{ getSocialHandle(authStore.user.instagram) }}
                            <v-icon size="14" class="ml-1">mdi-open-in-new</v-icon>
                          </a>
                          <span v-else class="text-grey">Belum dihubungkan</span>
                        </div>
                      </div>
                    </div>

                    <!-- Facebook -->
                    <div class="info-tile">
                      <div class="tile-icon-box facebook">
                        <v-icon color="#1877F2" size="20">mdi-facebook</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Facebook</span>
                        <div class="tile-value">
                          <a
                            v-if="authStore.user?.facebook"
                            :href="formatSocialUrl(authStore.user.facebook, 'facebook')"
                            target="_blank"
                            class="active-link"
                          >
                            {{ getSocialHandle(authStore.user.facebook) }}
                            <v-icon size="14" class="ml-1">mdi-open-in-new</v-icon>
                          </a>
                          <span v-else class="text-grey">Belum dihubungkan</span>
                        </div>
                      </div>
                    </div>

                    <!-- Twitter / X -->
                    <div class="info-tile">
                      <div class="tile-icon-box x-twitter">
                        <v-icon color="#111827" size="20">mdi-twitter</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Twitter / X</span>
                        <div class="tile-value">
                          <a
                            v-if="authStore.user?.twitter"
                            :href="formatSocialUrl(authStore.user.twitter, 'twitter')"
                            target="_blank"
                            class="active-link"
                          >
                            {{ getSocialHandle(authStore.user.twitter) }}
                            <v-icon size="14" class="ml-1">mdi-open-in-new</v-icon>
                          </a>
                          <span v-else class="text-grey">Belum dihubungkan</span>
                        </div>
                      </div>
                    </div>

                    <!-- Status Role -->
                    <div class="info-tile">
                      <div class="tile-icon-box">
                        <v-icon color="#7C3AED" size="20">mdi-shield-check</v-icon>
                      </div>
                      <div class="tile-content">
                        <span class="tile-label">Status Keanggotaan</span>
                        <div class="tile-value">{{ roleLabel }}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </v-window-item>

              <!-- TAB 2: Poin Kontributor -->
              <v-window-item value="points">
                <div class="tab-inner-content pt-4">
                  <ContributorPointsWallet />
                </div>
              </v-window-item>

              <!-- TAB 3: Destinasi Ditambahkan -->
              <v-window-item value="destinations">
                <div class="tab-inner-content">
                  <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="#DC2626" size="44"></v-progress-circular>
                    <p class="mt-3 text-caption text-grey">Memuat daftar destinasi...</p>
                  </div>

                  <div v-else-if="userDestinations.length > 0" class="destinations-grid">
                    <v-row>
                      <v-col
                        v-for="destination in userDestinations"
                        :key="destination.id"
                        cols="12"
                        sm="6"
                      >
                        <v-card
                          hover
                          rounded="xl"
                          class="destination-tile-card"
                          :to="`/destinations/${destination.id}`"
                        >
                          <v-img
                            :src="getImageUrl(destination.primary_photo)"
                            height="180"
                            cover
                            class="dest-img"
                          >
                            <template v-slot:placeholder>
                              <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                <v-progress-circular indeterminate color="#DC2626"></v-progress-circular>
                              </div>
                            </template>
                          </v-img>
                          <v-card-text class="pa-4">
                            <h4 class="dest-title">{{ destination.name }}</h4>
                            <p class="dest-category">
                              <v-icon size="14" class="mr-1" color="#DC2626">mdi-folder-outline</v-icon>
                              {{ destination.category?.name || 'Tanpa Kategori' }}
                            </p>
                            <div class="dest-meta d-flex align-center ga-3">
                              <span class="meta-stat">
                                <v-icon size="14" color="#EF4444" class="mr-1">mdi-heart</v-icon>
                                {{ destination.likes_count || 0 }}
                              </span>
                              <span class="meta-stat">
                                <v-icon size="14" color="#0284C7" class="mr-1">mdi-comment-outline</v-icon>
                                {{ destination.comments_count || 0 }}
                              </span>
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <div v-else class="empty-tab-state text-center py-12">
                    <v-icon size="64" color="#D1D5DB">mdi-map-marker-off-outline</v-icon>
                    <h3 class="empty-title mt-3">Belum Ada Destinasi</h3>
                    <p class="empty-subtitle">Bagikan spot jalan kaki favorit Anda kepada teman komunitas</p>
                    <v-btn
                      color="#DC2626"
                      variant="flat"
                      to="/destinations/create"
                      rounded="pill"
                      size="large"
                      class="mt-4 font-weight-bold text-white px-6"
                    >
                      <v-icon start size="18">mdi-plus</v-icon>
                      Tambah Destinasi Baru
                    </v-btn>
                  </div>
                </div>
              </v-window-item>

              <!-- TAB 4: Event Diikuti -->
              <v-window-item value="events">
                <div class="tab-inner-content">
                  <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="#DC2626" size="44"></v-progress-circular>
                    <p class="mt-3 text-caption text-grey">Memuat daftar event...</p>
                  </div>

                  <div v-else-if="userEvents.length > 0" class="events-grid">
                    <v-row>
                      <v-col
                        v-for="event in userEvents"
                        :key="event.id"
                        cols="12"
                        sm="6"
                      >
                        <v-card
                          hover
                          rounded="xl"
                          class="event-tile-card"
                          :to="`/events/${event.id}`"
                        >
                          <v-img
                            :src="getImageUrl(event.poster)"
                            height="180"
                            cover
                            class="event-img"
                          >
                            <span v-if="event.type === 'walking'" class="event-type-badge">
                              <v-icon size="13" class="mr-1">mdi-walk</v-icon>
                              Jalan Kaki
                            </span>
                          </v-img>
                          <v-card-text class="pa-4">
                            <h4 class="event-title">{{ event.name }}</h4>
                            <p class="event-date">
                              <v-icon size="14" class="mr-1" color="#16A34A">mdi-calendar-outline</v-icon>
                              {{ formatEventDate(event.date) }}
                            </p>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <div v-else class="empty-tab-state text-center py-12">
                    <v-icon size="64" color="#D1D5DB">mdi-calendar-blank-outline</v-icon>
                    <h3 class="empty-title mt-3">Belum Mengikuti Event</h3>
                    <p class="empty-subtitle">Ikuti jalan santai atau kegiatan komunitas berikutnya bersama kami</p>
                    <v-btn
                      color="#DC2626"
                      variant="flat"
                      to="/events"
                      rounded="pill"
                      size="large"
                      class="mt-4 font-weight-bold text-white px-6"
                    >
                      <v-icon start size="18">mdi-compass-outline</v-icon>
                      Jelajahi Event Komunitas
                    </v-btn>
                  </div>
                </div>
              </v-window-item>

              <!-- TAB 5: Cerita yang Dikirim -->
              <v-window-item value="stories">
                <div class="tab-inner-content">
                  <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="#DC2626" size="44"></v-progress-circular>
                    <p class="mt-3 text-caption text-grey">Memuat daftar cerita...</p>
                  </div>

                  <div v-else-if="userStories.length > 0" class="stories-grid">
                    <v-row>
                      <v-col
                        v-for="story in userStories"
                        :key="story.id"
                        cols="12"
                        sm="6"
                      >
                        <v-card
                          hover
                          rounded="xl"
                          class="story-tile-card"
                          :to="`/cerita/${story.slug}`"
                        >
                          <v-img
                            v-if="story.cover_image_url"
                            :src="story.cover_image_url"
                            height="160"
                            cover
                            class="story-img"
                          >
                            <template v-slot:placeholder>
                              <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                                <v-progress-circular indeterminate color="#DC2626"></v-progress-circular>
                              </div>
                            </template>
                          </v-img>
                          <div v-else class="story-img-placeholder">
                            <v-icon size="48" color="#D1D5DB">mdi-text-box-outline</v-icon>
                          </div>
                          <v-card-text class="pa-4">
                            <h4 class="story-title">{{ story.title }}</h4>
                            <p v-if="story.excerpt" class="story-excerpt">{{ story.excerpt }}</p>
                            <div class="story-meta d-flex align-center justify-space-between mt-3">
                              <span class="meta-stat">
                                <v-icon size="14" color="#0284C7" class="mr-1">mdi-eye-outline</v-icon>
                                {{ story.views_count || 0 }} views
                              </span>
                              <span class="text-caption text-grey">
                                {{ formatDate(story.published_at) }}
                              </span>
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <div v-else class="empty-tab-state text-center py-12">
                    <v-icon size="64" color="#D1D5DB">mdi-text-box-off-outline</v-icon>
                    <h3 class="empty-title mt-3">Belum Ada Cerita</h3>
                    <p class="empty-subtitle">Bagikan pengalaman jalan kaki Anda dengan komunitas</p>
                    <v-btn
                      color="#DC2626"
                      variant="flat"
                      to="/cerita"
                      rounded="pill"
                      size="large"
                      class="mt-4 font-weight-bold text-white px-6"
                    >
                      <v-icon start size="18">mdi-pencil</v-icon>
                      Tulis Cerita Baru
                    </v-btn>
                  </div>
                </div>
              </v-window-item>

              <!-- TAB 6: Komentar Aktif -->
              <v-window-item value="comments">
                <div class="tab-inner-content">
                  <div v-if="loadingComments" class="text-center py-12">
                    <v-progress-circular indeterminate color="#DC2626" size="44"></v-progress-circular>
                    <p class="mt-3 text-caption text-grey">Memuat komentar...</p>
                  </div>

                  <div v-else-if="userComments.length > 0" class="comments-list">
                    <div
                      v-for="comment in userComments"
                      :key="comment.id"
                      class="comment-card"
                    >
                      <div class="comment-header d-flex align-center justify-space-between mb-2">
                        <div class="d-flex align-center ga-2">
                          <v-icon size="16" :color="getCommentTypeColor(comment.commentable_type)">
                            {{ getCommentTypeIcon(comment.commentable_type) }}
                          </v-icon>
                          <span class="comment-type-label">{{ getCommentTypeLabel(comment.commentable_type) }}</span>
                        </div>
                        <span class="text-caption text-grey">{{ formatCommentDate(comment.created_at) }}</span>
                      </div>

                      <NuxtLink
                        :to="getCommentLink(comment)"
                        class="comment-target-link mb-2"
                      >
                        <v-icon size="14" class="mr-1">mdi-open-in-new</v-icon>
                        {{ comment.commentable?.name || comment.commentable?.title || 'Lihat konten' }}
                      </NuxtLink>

                      <p class="comment-content">{{ comment.content }}</p>

                      <div class="comment-footer d-flex align-center ga-3 mt-2">
                        <span class="meta-stat">
                          <v-icon size="14" color="#EF4444" class="mr-1">mdi-heart</v-icon>
                          {{ comment.likes_count || 0 }}
                        </span>
                        <span v-if="comment.replies_count" class="meta-stat">
                          <v-icon size="14" color="#0284C7" class="mr-1">mdi-comment-outline</v-icon>
                          {{ comment.replies_count }} balasan
                        </span>
                      </div>
                    </div>
                  </div>

                  <div v-else class="empty-tab-state text-center py-12">
                    <v-icon size="64" color="#D1D5DB">mdi-comment-off-outline</v-icon>
                    <h3 class="empty-title mt-3">Belum Ada Komentar</h3>
                    <p class="empty-subtitle">Mulai berinteraksi dengan destinasi, event, atau cerita komunitas</p>
                    <v-btn
                      color="#DC2626"
                      variant="flat"
                      to="/destinations"
                      rounded="pill"
                      size="large"
                      class="mt-4 font-weight-bold text-white px-6"
                    >
                      <v-icon start size="18">mdi-compass-outline</v-icon>
                      Jelajahi Konten
                    </v-btn>
                  </div>
                </div>
              </v-window-item>

              <!-- TAB 7: Destinasi yang Disimpan / Ingin Dikunjungi -->
              <v-window-item value="saved">
                <div class="tab-inner-content">
                  <div v-if="bookmarks.length > 0" class="destinations-grid">
                    <v-row>
                      <v-col
                        v-for="destination in bookmarks"
                        :key="destination.id"
                        cols="12"
                        sm="6"
                      >
                        <v-card
                          hover
                          rounded="xl"
                          class="destination-tile-card"
                          :to="`/destinations/${destination.id}`"
                        >
                          <div class="position-relative">
                            <v-img
                              :src="getImageUrl(destination.primary_photo)"
                              height="180"
                              cover
                              class="dest-img"
                            ></v-img>
                            <button
                              type="button"
                              class="card-remove-bookmark-btn"
                              title="Hapus dari tersimpan"
                              aria-label="Hapus dari tersimpan"
                              @click.stop.prevent="removeBookmark(destination.id)"
                            >
                              <v-icon size="16" color="#DC2626">mdi-trash-can-outline</v-icon>
                            </button>
                          </div>
                          <v-card-text class="pa-4">
                            <h4 class="dest-title">{{ destination.name }}</h4>
                            <p class="dest-category">
                              <v-icon size="14" class="mr-1" color="#DC2626">mdi-folder-outline</v-icon>
                              {{ destination.category?.name || destination.category_name || 'Tanpa Kategori' }}
                            </p>
                            <div class="dest-meta d-flex align-center justify-space-between">
                              <span class="meta-stat">
                                <v-icon size="14" color="#EF4444" class="mr-1">mdi-heart</v-icon>
                                {{ destination.likes_count || 0 }}
                              </span>
                              <span class="text-caption font-weight-bold" style="color: #DC2626;">
                                Buka Spot <v-icon size="12">mdi-arrow-right</v-icon>
                              </span>
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <div v-else class="empty-tab-state text-center py-12">
                    <v-icon size="64" color="#D1D5DB">mdi-bookmark-outline</v-icon>
                    <h3 class="empty-title mt-3">Belum Ada Destinasi Disimpan</h3>
                    <p class="empty-subtitle">Tandai tempat jalan kaki yang ingin Anda kunjungi saat menjelajahi katalog</p>
                    <v-btn
                      color="#DC2626"
                      variant="flat"
                      to="/destinations"
                      rounded="pill"
                      size="large"
                      class="mt-4 font-weight-bold text-white px-6"
                    >
                      <v-icon start size="18">mdi-compass-outline</v-icon>
                      Jelajahi Direktori Destinasi
                    </v-btn>
                  </div>
                </div>
              </v-window-item>
            </v-window>
          </v-card>
        </v-col>

        <!-- Sidebar -->
        <v-col cols="12" lg="4">
          <!-- Administrator Quick Panel (for Admin / Community Admin) -->
          <v-card
            v-if="authStore.isAdmin || authStore.isCommunityAdmin"
            elevation="0"
            rounded="xl"
            class="admin-access-card mb-4"
          >
            <v-card-text class="pa-5">
              <div class="d-flex align-center ga-3 mb-3">
                <v-avatar color="rgba(255,255,255,0.12)" size="42">
                  <v-icon color="#F59E0B" size="22">mdi-shield-crown</v-icon>
                </v-avatar>
                <div>
                  <h3 class="text-subtitle-1 font-weight-bold text-white mb-0">
                    {{ authStore.isAdmin ? 'Panel Administrator' : 'Panel Manajemen' }}
                  </h3>
                  <p class="text-caption text-grey-lighten-2 mb-0">
                    {{ authStore.isAdmin ? 'Kelola website, user, & aktivasi' : 'Kelola aktivasi & destinasi' }}
                  </p>
                </div>
              </div>
              <v-btn
                block
                color="#DC2626"
                variant="flat"
                size="large"
                rounded="pill"
                to="/manage/activations"
                class="font-weight-bold text-white"
              >
                <v-icon start size="18">mdi-view-dashboard-outline</v-icon>
                Buka Panel Manajemen
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Aksi Cepat Card -->
          <v-card elevation="0" rounded="xl" class="sidebar-card mb-4">
            <v-card-text class="pa-5">
              <h3 class="sidebar-card-title mb-4">
                <v-icon size="18" color="#DC2626" class="mr-1">mdi-flash-outline</v-icon>
                Aksi Cepat
              </h3>
              <div class="quick-actions-list d-flex flex-column ga-2">
                <v-btn
                  block
                  color="#DC2626"
                  variant="flat"
                  size="large"
                  rounded="pill"
                  class="font-weight-bold text-white"
                  @click="isEditing = true"
                >
                  <v-icon start size="18">mdi-account-edit-outline</v-icon>
                  Edit Profil Saya
                </v-btn>

                <v-btn
                  block
                  variant="outlined"
                  color="#111827"
                  size="large"
                  rounded="pill"
                  to="/destinations/create"
                  class="font-weight-bold"
                >
                  <v-icon start size="18" color="#16A34A">mdi-map-marker-plus</v-icon>
                  Tambah Destinasi
                </v-btn>

                <v-btn
                  block
                  variant="tonal"
                  color="#4B5563"
                  size="large"
                  rounded="pill"
                  to="/destinations/my-posts"
                  class="font-weight-medium"
                >
                  <v-icon start size="18">mdi-post-outline</v-icon>
                  Kelola Postingan Saya
                </v-btn>

                <v-divider class="my-2"></v-divider>

                <v-btn
                  block
                  variant="text"
                  color="#EF4444"
                  size="large"
                  rounded="pill"
                  class="font-weight-bold logout-btn"
                  @click="handleLogout"
                >
                  <v-icon start size="18">mdi-logout</v-icon>
                  Keluar dari Akun
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Status & Keanggotaan Card -->
          <v-card elevation="0" rounded="xl" class="sidebar-card">
            <v-card-text class="pa-5">
              <h3 class="sidebar-card-title mb-4">
                <v-icon size="18" color="#0284C7" class="mr-1">mdi-shield-account-outline</v-icon>
                Keanggotaan
              </h3>
              <div class="account-summary-list d-flex flex-column ga-3">
                <div class="summary-row d-flex align-center justify-space-between py-2 border-b">
                  <span class="text-caption text-grey-darken-1">Status Komunitas</span>
                  <span class="role-pill-sm" :class="roleClass">{{ roleLabel }}</span>
                </div>
                <div class="summary-row d-flex align-center justify-space-between py-2 border-b">
                  <span class="text-caption text-grey-darken-1">Bergabung Sejak</span>
                  <span class="text-caption font-weight-bold text-grey-darken-4">
                    {{ formatJoinDate(authStore.user?.created_at) }}
                  </span>
                </div>
                <div class="summary-row d-flex align-center justify-space-between py-2">
                  <span class="text-caption text-grey-darken-1">ID Pejalan</span>
                  <span class="text-caption font-family-mono text-grey-darken-2">
                    #{{ authStore.user?.id || '-' }}
                  </span>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Modular Edit Profile Dialog -->
    <ProfileEditDialog
      v-model="isEditing"
      @saved="onProfileSaved"
    />

    <!-- Toast Notification -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" location="top" rounded="pill">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import ContributorPointsWallet from '~/components/profile/ContributorPointsWallet.vue'
import ProfileEditDialog from '~/components/profile/ProfileEditDialog.vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useImageUrl } from '~/composables/useImageUrl'
import { useBookmarks } from '~/composables/useBookmarks'
import { useDisplay } from 'vuetify'

definePageMeta({
  layout: 'default',
  middleware: 'auth'
})

const authStore = useAuthStore()
const { api } = useApi()
const { getImageUrl } = useImageUrl()
const { mobile } = useDisplay()
const { bookmarks, bookmarksCount, removeBookmark } = useBookmarks()

const isMobile = computed(() => mobile.value)
const tab = ref('profile')
const isEditing = ref(false)
const loading = ref(false)

const profileData = ref<any>(null)
const userDestinations = ref<any[]>([])
const userEvents = ref<any[]>([])
const userStories = ref<any[]>([])
const userComments = ref<any[]>([])
const loadingComments = ref(false)

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const showToast = (text: string, color: string = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

const userInitial = computed(() => {
  if (authStore.user?.name) {
    return authStore.user.name.charAt(0).toUpperCase()
  }
  return 'J'
})

const userPhotoUrl = computed(() => {
  if (!authStore.user?.photo) return null
  return getImageUrl(authStore.user.photo)
})

const roleLabel = computed(() => {
  if (authStore.isAdmin) return 'Administrator'
  if (authStore.isCommunityAdmin) return 'Community Admin'
  return 'Member Pejalan Kaki'
})

const roleClass = computed(() => {
  if (authStore.isAdmin) return 'role-admin'
  if (authStore.isCommunityAdmin) return 'role-comm-admin'
  return 'role-member'
})

const roleIcon = computed(() => {
  if (authStore.isAdmin) return 'mdi-shield-crown'
  if (authStore.isCommunityAdmin) return 'mdi-shield-star'
  return 'mdi-walk'
})

const cleanPhone = (phone: string) => {
  return phone.replace(/\D/g, '')
}

const formatSocialUrl = (url: string, platform: string) => {
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  const clean = url.replace('@', '')
  if (platform === 'instagram') return `https://instagram.com/${clean}`
  if (platform === 'facebook') return `https://facebook.com/${clean}`
  if (platform === 'twitter') return `https://x.com/${clean}`
  return url
}

const getSocialHandle = (url: string) => {
  if (!url) return ''
  const clean = url.replace(/\/$/, '')
  const parts = clean.split('/')
  const handle = parts[parts.length - 1]
  return handle.startsWith('@') ? handle : `@${handle}`
}

const formatEventDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const formatJoinDate = (date: string | undefined) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    month: 'long',
    year: 'numeric'
  })
}

const handleLogout = async () => {
  await authStore.logout()
}

const fetchProfileData = async () => {
  if (!authStore.user?.id) return

  loading.value = true
  try {
    const response = await api.get(`/profile/${authStore.user.id}`)
    profileData.value = response.data.user
    userDestinations.value = response.data.user.destinations || []
    userEvents.value = response.data.user.participated_events || []
    userStories.value = response.data.user.stories || []
  } catch (error) {
    console.error('Error fetching profile data:', error)
  } finally {
    loading.value = false
  }
}

const fetchUserComments = async () => {
  if (!authStore.user?.id) return

  loadingComments.value = true
  try {
    const response = await api.get(`/comments?user_id=${authStore.user.id}`)
    userComments.value = response.data
  } catch (error) {
    console.error('Error fetching comments:', error)
    userComments.value = []
  } finally {
    loadingComments.value = false
  }
}

// Comment helper functions
const getCommentTypeIcon = (type: string) => {
  if (type.includes('Destination')) return 'mdi-map-marker'
  if (type.includes('Event')) return 'mdi-calendar'
  if (type.includes('Story')) return 'mdi-text-box'
  return 'mdi-comment'
}

const getCommentTypeColor = (type: string) => {
  if (type.includes('Destination')) return '#DC2626'
  if (type.includes('Event')) return '#16A34A'
  if (type.includes('Story')) return '#0284C7'
  return '#6B7280'
}

const getCommentTypeLabel = (type: string) => {
  if (type.includes('Destination')) return 'Destinasi'
  if (type.includes('Event')) return 'Event'
  if (type.includes('Story')) return 'Cerita'
  return 'Komentar'
}

const getCommentLink = (comment: any) => {
  if (comment.commentable_type.includes('Destination')) {
    return `/destinations/${comment.commentable_id}`
  }
  if (comment.commentable_type.includes('Event')) {
    return `/events/${comment.commentable_id}`
  }
  if (comment.commentable_type.includes('Story')) {
    return `/cerita/${comment.commentable?.slug || comment.commentable_id}`
  }
  return '#'
}

const formatCommentDate = (date: string) => {
  if (!date) return ''
  const d = new Date(date)
  const now = new Date()
  const diff = now.getTime() - d.getTime()
  const days = Math.floor(diff / (1000 * 60 * 60 * 24))

  if (days === 0) return 'Hari ini'
  if (days === 1) return 'Kemarin'
  if (days < 7) return `${days} hari lalu`
  if (days < 30) return `${Math.floor(days / 7)} minggu lalu`
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatDate = (date: string) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const onProfileSaved = async () => {
  showToast('Profil Anda berhasil diperbarui!', 'success')
  await fetchProfileData()
}

// Watch user if populated later
watch(
  () => authStore.user?.id,
  (newId) => {
    if (newId && !profileData.value) {
      fetchProfileData()
    }
  }
)

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
  await fetchProfileData()
})

// Fetch comments when tab changes to comments
watch(tab, (newTab) => {
  if (newTab === 'comments' && userComments.value.length === 0) {
    fetchUserComments()
  }
})
</script>

<style scoped>
.profile-page {
  background: #FAFAF9;
  min-height: 100vh;
}

/* ==========================================================================
   Editorial Hero Section
   ========================================================================== */
.profile-hero {
  position: relative;
  background: #0F172A;
  background: linear-gradient(145deg, #0F172A 0%, #1E293B 100%);
  padding: 40px 0 48px;
  overflow: hidden;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.hero-overlay-glow {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(circle at 15% 30%, rgba(220, 38, 38, 0.15) 0%, transparent 60%),
    radial-gradient(circle at 85% 80%, rgba(245, 158, 11, 0.08) 0%, transparent 60%);
  pointer-events: none;
}

.hero-container {
  position: relative;
  z-index: 2;
  padding-top: 20px;
}

.avatar-wrapper {
  position: relative;
  flex-shrink: 0;
}

.profile-avatar {
  border: 4px solid #FFFFFF;
  background: #FEF2F2;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
}

.avatar-initial {
  font-size: 42px;
  font-weight: 800;
  color: #DC2626;
}

.avatar-edit-badge {
  position: absolute;
  bottom: 4px;
  right: 4px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #DC2626;
  border: 2.5px solid #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
  transition: transform 0.2s ease;
}

.avatar-edit-badge:hover {
  transform: scale(1.1);
}

.profile-kicker {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  color: #94A3B8;
  text-transform: uppercase;
}

.role-pill {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.role-pill.role-admin {
  background: #DC2626;
  color: #FFFFFF;
}

.role-pill.role-comm-admin {
  background: #D97706;
  color: #FFFFFF;
}

.role-pill.role-member {
  background: rgba(255, 255, 255, 0.15);
  color: #F1F5F9;
}

.user-name {
  font-size: clamp(1.6rem, 3.5vw, 2.25rem);
  font-weight: 800;
  letter-spacing: -0.025em;
  color: #FFFFFF;
  margin: 0 0 4px;
  line-height: 1.15;
}

.user-email {
  font-size: 0.95rem;
  color: #CBD5E1;
  margin: 0 0 16px;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (min-width: 960px) {
  .user-email {
    justify-content: flex-start;
  }
}

.user-hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 14px;
}

@media (min-width: 960px) {
  .user-hero-stats {
    justify-content: flex-start;
  }
}

.stat-pill {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.08);
  padding: 4px 12px;
  border-radius: 9999px;
  border: 1px solid rgba(255, 255, 255, 0.08);
}

.stat-num {
  font-size: 0.95rem;
  font-weight: 700;
  color: #FFFFFF;
}

.stat-txt {
  font-size: 0.8rem;
  color: #94A3B8;
}

.stat-divider {
  width: 1px;
  height: 16px;
  background: rgba(255, 255, 255, 0.15);
}

.edit-hero-btn {
  box-shadow: 0 4px 16px rgba(220, 38, 38, 0.35) !important;
  letter-spacing: 0.02em;
}

/* ==========================================================================
   Content Section & Cards
   ========================================================================== */
.content-section {
  margin-top: -24px;
  padding-bottom: 80px;
}

.main-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
  overflow: hidden;
}

.tabs-bar-wrapper {
  background: #FFFFFF;
  padding: 4px 8px 0;
}

.tab-item {
  text-transform: none !important;
  font-weight: 600 !important;
  font-size: 0.9rem !important;
  letter-spacing: 0 !important;
  min-height: 48px !important;
}

.tab-badge {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 1px 7px;
  border-radius: 9999px;
  background: #FEF2F2;
  color: #DC2626;
}

.tab-badge.success-badge {
  background: #F0FDF4;
  color: #16A34A;
}

.tab-inner-content {
  padding: 28px;
  min-height: 380px;
}

@media (max-width: 600px) {
  .tab-inner-content {
    padding: 18px 14px;
  }
}

.section-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.02em;
  margin: 0;
}

.section-subtitle {
  font-size: 0.85rem;
  color: #6B7280;
  margin: 2px 0 0;
}

/* ==========================================================================
   Info Tiles Grid
   ========================================================================== */
.info-tiles-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 14px;
}

.info-tile {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  background: #FAFAF9;
  border-radius: 16px;
  border: 1px solid #F1F5F9;
  transition: all 0.2s ease;
}

.info-tile:hover {
  background: #FFFFFF;
  border-color: #E2E8F0;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  transform: translateY(-1px);
}

.tile-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.tile-icon-box.instagram {
  background: #FDF2F8;
}

.tile-icon-box.facebook {
  background: #EFF6FF;
}

.tile-icon-box.x-twitter {
  background: #F3F4F6;
}

.tile-content {
  flex: 1;
  min-width: 0;
}

.tile-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: #9CA3AF;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: block;
}

.tile-value {
  font-size: 0.95rem;
  font-weight: 600;
  color: #111827;
  margin-top: 2px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.active-link {
  color: #DC2626;
  text-decoration: none;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
}

.active-link:hover {
  text-decoration: underline;
}

.verified-chip {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 9999px;
  background: #ECFDF5;
  color: #059669;
}

/* ==========================================================================
   Destinations & Events Cards
   ========================================================================== */
.destination-tile-card,
.event-tile-card {
  border: 1px solid #E5E7EB;
  transition: all 0.25s ease;
  overflow: hidden;
}

.destination-tile-card:hover,
.event-tile-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}

.dest-title,
.event-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 4px;
}

.dest-category,
.event-date {
  font-size: 0.8rem;
  color: #6B7280;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
}

.meta-stat {
  font-size: 0.8rem;
  color: #6B7280;
  display: flex;
  align-items: center;
}

.event-type-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(22, 163, 74, 0.9);
  color: white;
  padding: 4px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  backdrop-filter: blur(4px);
}

/* ==========================================================================
   Sidebar
   ========================================================================== */
.admin-access-card {
  background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
  border: 1px solid #334155;
  box-shadow: 0 4px 16px rgba(15, 23, 42, 0.15);
}

.sidebar-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}

.sidebar-card-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #111827;
  display: flex;
  align-items: center;
}

.role-pill-sm {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 9999px;
}

.role-pill-sm.role-admin {
  background: #FEF2F2;
  color: #DC2626;
}

.role-pill-sm.role-comm-admin {
  background: #FFFBEB;
  color: #D97706;
}

.role-pill-sm.role-member {
  background: #F1F5F9;
  color: #475569;
}

.logout-btn:hover {
  background: #FEF2F2 !important;
}

.card-remove-bookmark-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  transition: transform 0.2s ease;
}

.card-remove-bookmark-btn:hover {
  transform: scale(1.1);
  background: #FEF2F2;
}

/* Stories Grid */
.stories-grid {
  margin-top: 8px;
}

.story-tile-card {
  border: 1px solid #E5E7EB;
  transition: all 0.3s ease;
  background: white;
}

.story-tile-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
  border-color: #CBD5E1;
}

.story-img {
  object-fit: cover;
}

.story-img-placeholder {
  height: 160px;
  background: #F3F4F6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.story-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #111827;
  margin-bottom: 8px;
  line-height: 1.35;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-wrap: break-word;
}

.story-excerpt {
  font-size: 0.8rem;
  color: #6B7280;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Comments List */
.comments-list {
  margin-top: 8px;
}

.comment-card {
  padding: 16px 20px;
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 12px;
  margin-bottom: 12px;
  transition: all 0.2s ease;
}

.comment-card:hover {
  border-color: #CBD5E1;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.comment-type-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.comment-target-link {
  display: inline-flex;
  align-items: center;
  font-size: 0.85rem;
  font-weight: 600;
  color: #DC2626;
  text-decoration: none;
  transition: color 0.2s ease;
}

.comment-target-link:hover {
  color: #B91C1C;
  text-decoration: underline;
}

.comment-content {
  font-size: 0.9rem;
  color: #374151;
  line-height: 1.6;
  margin: 8px 0 0 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.comment-footer {
  padding-top: 8px;
  border-top: 1px solid #F3F4F6;
}
</style>
