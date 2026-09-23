<template>
  <div class="community-page-wrapper">
    <!-- 1. HERO HEADER SECTION -->
    <div class="community-hero-section py-6 py-md-12">
      <v-container>
        <!-- Breadcrumbs -->
        <nav class="d-flex align-center ga-2 text-caption mb-4" aria-label="Breadcrumb">
          <NuxtLink to="/" class="breadcrumb-link">Beranda</NuxtLink>
          <span class="text-grey-lighten-1">/</span>
          <span class="text-grey-lighten-1">Komunitas</span>
          <span class="text-grey-lighten-1">/</span>
          <span class="text-white font-weight-medium">{{ pageMeta.title }}</span>
        </nav>

        <!-- Category Badge -->
        <div class="d-inline-flex align-center ga-2 px-3 py-1-5 rounded-pill hero-topic-badge mb-3">
          <v-icon size="16" :color="pageMeta.badgeColor">{{ pageMeta.icon }}</v-icon>
          <span class="text-caption font-weight-bold tracking-wider text-uppercase" :style="{ color: pageMeta.badgeTextColor }">
            {{ pageMeta.badgeLabel }}
          </span>
        </div>

        <!-- Page Title & Subtitle -->
        <h1 class="text-h4 text-md-h3 font-weight-black text-white mb-3 page-main-title">
          {{ pageMeta.title }}
        </h1>

        <p class="text-body-1 text-grey-lighten-2 mb-4 hero-lead" style="max-width: 680px; line-height: 1.6;">
          {{ pageMeta.subtitle }}
        </p>

        <!-- Meta Pills -->
        <div class="d-flex flex-wrap align-center ga-4 text-caption text-grey-lighten-2 pt-2">
          <div class="d-flex align-center ga-1">
            <v-icon size="15" color="#94A3B8">mdi-calendar-clock</v-icon>
            <span>Diperbarui: {{ lastUpdatedText }}</span>
          </div>
          <div class="d-flex align-center ga-1">
            <v-icon size="15" color="#94A3B8">mdi-clock-outline</v-icon>
            <span>Estimasi baca: {{ pageMeta.readTime }}</span>
          </div>
          <div class="d-flex align-center ga-1">
            <v-icon size="15" color="#10B981">mdi-shield-check</v-icon>
            <span>Panduan Resmi Jalan Bareng</span>
          </div>
        </div>
      </v-container>
    </div>

    <!-- 2. MAIN 2-COLUMN CONTENT SECTION -->
    <v-container class="py-6 py-md-10">
      <v-row>
        <!-- LEFT COLUMN: Sticky Navigation & Help Sidebar -->
        <v-col cols="12" md="4" lg="3">
          <div class="sticky-sidebar">
            <!-- Navigation Card -->
            <v-card elevation="0" rounded="xl" class="border pa-4 mb-4 bg-white">
              <div class="text-caption font-weight-bold text-uppercase text-grey-darken-1 tracking-wider mb-3 px-2">
                Halaman Komunitas
              </div>
              <v-list density="comfortable" nav class="pa-0">
                <v-list-item
                  v-for="item in communityNavItems"
                  :key="item.slug"
                  :to="`/pages/${item.slug}`"
                  rounded="lg"
                  class="nav-page-item mb-1"
                  :class="{ 'nav-item-active': currentSlug === item.slug }"
                >
                  <template v-slot:prepend>
                    <v-icon size="18" :color="currentSlug === item.slug ? 'primary' : 'grey-darken-1'">
                      {{ item.icon }}
                    </v-icon>
                  </template>
                  <v-list-item-title class="font-weight-medium text-body-2">
                    {{ item.title }}
                  </v-list-item-title>
                  <template v-slot:append v-if="currentSlug === item.slug">
                    <span class="active-dot"></span>
                  </template>
                </v-list-item>
              </v-list>
            </v-card>

            <!-- Instagram / Direct Help Box -->
            <v-card elevation="0" rounded="xl" class="border pa-5 bg-grey-lighten-5 mb-4">
              <div class="d-flex align-center ga-2 mb-2">
                <v-icon color="#E1306C" size="20">mdi-instagram</v-icon>
                <span class="text-subtitle-2 font-weight-bold text-grey-darken-4">
                  Punya Pertanyaan?
                </span>
              </div>
              <p class="text-caption text-grey-darken-2 mb-3" style="line-height: 1.5;">
                Ingin berdiskusi seputar aktivasi, titik kumpul, atau kolaborasi brand? Hubungi tim kami langsung via DM Instagram.
              </p>
              <v-btn
                href="https://instagram.com/jalanbarengind"
                target="_blank"
                rel="noopener noreferrer"
                color="white"
                block
                rounded="pill"
                size="small"
                class="font-weight-bold border elevation-1 text-grey-darken-4"
              >
                <v-icon start size="16" color="#E1306C">mdi-message-outline</v-icon>
                DM @jalanbarengind
              </v-btn>
            </v-card>

            <!-- Quick Registration Link -->
            <v-card elevation="0" rounded="xl" class="pa-5 cta-mini-box text-white">
              <div class="text-caption font-weight-bold text-uppercase tracking-wider opacity-90 mb-1">
                Aktivasi Pekan Ini
              </div>
              <h4 class="text-subtitle-1 font-weight-bold mb-2">
                Siap Melangkah Bersama?
              </h4>
              <p class="text-caption opacity-90 mb-3" style="line-height: 1.4;">
                Daftar agenda jalan santai atau diskusi buku akhir pekan ini.
              </p>
              <v-btn
                to="/#registrasi"
                color="white"
                block
                rounded="pill"
                size="small"
                class="font-weight-bold text-primary elevation-1"
              >
                <v-icon start size="16">mdi-ticket-outline</v-icon>
                Pilih Aktivasi Pekan Ini
              </v-btn>
            </v-card>
          </div>
        </v-col>

        <!-- RIGHT COLUMN: Rich Content per Slug -->
        <v-col cols="12" md="8" lg="9">
          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12 bg-white rounded-xl border">
            <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
            <p class="mt-4 text-body-2 text-grey-darken-1">Memuat halaman komunitas...</p>
          </div>

          <!-- A. CUSTOM VIEW FOR: TENTANG KAMI -->
          <div v-else-if="currentSlug === 'tentang-kami'" class="page-content-card">
            <v-card elevation="0" rounded="xl" class="border pa-6 pa-md-10 bg-white">
              <!-- Story Origin Section -->
              <div class="story-highlight-box pa-6 rounded-xl mb-8">
                <div class="d-flex align-center ga-2 mb-2 text-primary font-weight-bold text-caption text-uppercase">
                  <v-icon size="18" color="primary">mdi-compass-outline</v-icon>
                  Awal Mula Gerakan
                </div>
                <h2 class="text-h5 text-md-h4 font-weight-black text-grey-darken-4 mb-3">
                  Dari Trotoar Kota Makassar, Melangkah Lebih Dekat dengan Kota Kita
                </h2>
                <p class="text-body-1 text-grey-darken-3 mb-0" style="line-height: 1.8;">
                  Jalan Bareng lahir dari kegelisahan sederhana: kota sering kali kita lewati begitu cepat di balik kaca kendaraan bermotor. Kami lupa pada nama-nama lorong, tidak kenal siapa tetangga di ujung jalan, dan trotoar kerap kali ditinggalkan. Kami memilih melambatkan langkah, berjalan kaki bersama, dan menghidupkan kembali ruang publik sebagai tempat perjumpaan yang hangat dan setara.
                </p>
              </div>

              <!-- 4 Core Pillars -->
              <div class="mb-10">
                <h3 class="text-h5 font-weight-bold text-grey-darken-4 mb-2">
                  4 Nilai Utama Gerakan Kami
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-6">
                  Setiap langkah dan aktivasi yang kami gelar selalu berpegang pada empat prinsip utama ini:
                </p>

                <v-row>
                  <v-col cols="12" sm="6" v-for="(pillar, idx) in aboutPillars" :key="idx">
                    <v-card elevation="0" rounded="xl" class="pillar-card pa-5 h-100 border">
                      <div class="pillar-icon-box mb-3" :style="{ backgroundColor: pillar.bg, color: pillar.color }">
                        <v-icon size="24">{{ pillar.icon }}</v-icon>
                      </div>
                      <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1">
                        {{ pillar.title }}
                      </h4>
                      <p class="text-body-2 text-grey-darken-2 mb-0" style="line-height: 1.6;">
                        {{ pillar.desc }}
                      </p>
                    </v-card>
                  </v-col>
                </v-row>
              </div>

              <!-- Activities Overview -->
              <div class="mb-10">
                <h3 class="text-h5 font-weight-bold text-grey-darken-4 mb-4">
                  Ragam Aktivasi yang Kami Jalankan
                </h3>
                <div class="d-flex flex-column ga-3">
                  <div
                    v-for="(act, idx) in aboutActivities"
                    :key="idx"
                    class="d-flex align-start ga-4 pa-4 rounded-xl border bg-grey-lighten-5"
                  >
                    <v-avatar :color="act.color" size="44" class="elevation-1 flex-shrink-0">
                      <v-icon color="white" size="22">{{ act.icon }}</v-icon>
                    </v-avatar>
                    <div>
                      <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1">
                        {{ act.title }}
                      </h4>
                      <p class="text-body-2 text-grey-darken-2 mb-0" style="line-height: 1.6;">
                        {{ act.desc }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Open Collaboration Banner -->
              <div class="collab-callout-box pa-6 rounded-xl border text-center">
                <v-icon size="36" color="#B45309" class="mb-2">mdi-handshake-outline</v-icon>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  Terbuka untuk Kolaborasi Brand, Komunitas, & Inisiatif Warga
                </h3>
                <p class="text-body-2 text-grey-darken-2 mx-auto mb-4" style="max-width: 580px; line-height: 1.6;">
                  Kami rutin berkolaborasi dengan jenama lokal, kedai kopi, kolektif seni, inisiatif lingkungan, dan ruang baca publik untuk menyelenggarakan aktivasi bermakna.
                </p>
                <v-btn
                  href="https://instagram.com/jalanbarengind"
                  target="_blank"
                  rel="noopener noreferrer"
                  color="primary"
                  rounded="pill"
                  class="font-weight-bold px-6"
                >
                  <v-icon start size="18">mdi-instagram</v-icon>
                  Diskusikan Kolaborasi Bersama Kami
                </v-btn>
              </div>

              <!-- Database Additional Content if any -->
              <div v-if="page?.content" class="mt-8 pt-8 border-t">
                <div class="page-content" v-html="page.content"></div>
              </div>
            </v-card>
          </div>

          <!-- B. CUSTOM VIEW FOR: PANDUAN & ETIKA KOMUNITAS -->
          <div v-else-if="currentSlug === 'panduan-komunitas'" class="page-content-card">
            <v-card elevation="0" rounded="xl" class="border pa-6 pa-md-10 bg-white">
              <!-- Guide Intro Banner -->
              <div class="guide-intro-banner pa-6 rounded-xl mb-8">
                <div class="d-flex align-center ga-2 mb-2 text-success font-weight-bold text-caption text-uppercase">
                  <v-icon size="18" color="success">mdi-shield-check</v-icon>
                  Etika Pejalan Santai
                </div>
                <h2 class="text-h5 text-md-h4 font-weight-black text-grey-darken-4 mb-2">
                  Menjaga Ruang Bersama Tetap Ramah, Aman, dan Menyenangkan
                </h2>
                <p class="text-body-1 text-grey-darken-3 mb-0" style="line-height: 1.7;">
                  Jalan santai bukan perlombaan kecepatan. Setiap pejalan diharapkan saling menghargai sesama peserta, warga lorong yang kita lintasi, dan ruang publik kota yang kita tempati bersama.
                </p>
              </div>

              <!-- 4 Essential Rules Cards -->
              <div class="mb-10">
                <h3 class="text-h5 font-weight-bold text-grey-darken-4 mb-4">
                  4 Panduan Dasar di Lapangan
                </h3>
                <v-row>
                  <v-col cols="12" sm="6" v-for="(rule, idx) in guideRules" :key="idx">
                    <v-card elevation="0" rounded="xl" class="pa-5 border h-100 rule-card">
                      <div class="d-flex align-center ga-3 mb-3">
                        <div class="rule-icon-box" :style="{ backgroundColor: rule.bg, color: rule.color }">
                          <v-icon size="22">{{ rule.icon }}</v-icon>
                        </div>
                        <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-4">
                          {{ rule.title }}
                        </h4>
                      </div>
                      <p class="text-body-2 text-grey-darken-2 mb-0" style="line-height: 1.6;">
                        {{ rule.desc }}
                      </p>
                    </v-card>
                  </v-col>
                </v-row>
              </div>

              <!-- DOs & DONTs Comparative Grid -->
              <div class="mb-10">
                <h3 class="text-h5 font-weight-bold text-grey-darken-4 mb-2">
                  Panduan Lapangan: Yang Dianjurkan & Yang Dihindari
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-6">
                  Prinsip sederhana agar setiap orang pulang dengan senyuman dan cerita baru:
                </p>

                <v-row>
                  <!-- DOs (Yang Dianjurkan) -->
                  <v-col cols="12" md="6">
                    <div class="dos-box pa-5 rounded-xl border h-100">
                      <div class="d-flex align-center ga-2 mb-4 text-green-darken-2 font-weight-bold text-subtitle-1">
                        <v-icon color="#16A34A" size="22">mdi-check-circle</v-icon>
                        Sangat Dianjurkan (DOs)
                      </div>
                      <ul class="clean-checklist dos-list">
                        <li v-for="(item, idx) in dosList" :key="idx" class="d-flex align-start ga-3 mb-3 text-body-2 text-grey-darken-3">
                          <v-icon size="18" color="#16A34A" class="mt-0-5 flex-shrink-0">mdi-check</v-icon>
                          <span>{{ item }}</span>
                        </li>
                      </ul>
                    </div>
                  </v-col>

                  <!-- DONTs (Yang Dihindari) -->
                  <v-col cols="12" md="6">
                    <div class="donts-box pa-5 rounded-xl border h-100">
                      <div class="d-flex align-center ga-2 mb-4 text-red-darken-2 font-weight-bold text-subtitle-1">
                        <v-icon color="#DC2626" size="22">mdi-close-circle</v-icon>
                        Wajib Dihindari (DONTs)
                      </div>
                      <ul class="clean-checklist donts-list">
                        <li v-for="(item, idx) in dontsList" :key="idx" class="d-flex align-start ga-3 mb-3 text-body-2 text-grey-darken-3">
                          <v-icon size="18" color="#DC2626" class="mt-0-5 flex-shrink-0">mdi-close</v-icon>
                          <span>{{ item }}</span>
                        </li>
                      </ul>
                    </div>
                  </v-col>
                </v-row>
              </div>

              <!-- Database Additional Content -->
              <div v-if="page?.content" class="mt-8 pt-8 border-t">
                <div class="page-content" v-html="page.content"></div>
              </div>
            </v-card>
          </div>

          <!-- C. CUSTOM VIEW FOR: TANYA JAWAB (FAQ) -->
          <div v-else-if="currentSlug === 'faq'" class="page-content-card">
            <v-card elevation="0" rounded="xl" class="border pa-6 pa-md-10 bg-white">
              <!-- Search & Category Filters -->
              <div class="faq-header-box mb-6">
                <h2 class="text-h5 text-md-h4 font-weight-black text-grey-darken-4 mb-2">
                  Pertanyaan yang Sering Diajukan
                </h2>
                <p class="text-body-2 text-grey-darken-1 mb-5">
                  Cari jawaban cepat seputar tata cara pendaftaran, titik kumpul, biaya, dan perlengkapan jalan santai.
                </p>

                <!-- Search Input -->
                <v-text-field
                  v-model="faqSearchQuery"
                  placeholder="Cari pertanyaan... (contoh: biaya, registrasi, anak-anak, rute)"
                  prepend-inner-icon="mdi-magnify"
                  variant="outlined"
                  rounded="lg"
                  density="comfortable"
                  clearable
                  hide-details
                  class="mb-4 search-faq-field"
                ></v-text-field>

                <!-- Category Chips -->
                <div class="d-flex flex-wrap ga-2 pt-1">
                  <v-chip
                    v-for="cat in faqCategories"
                    :key="cat.value"
                    :variant="selectedFaqCategory === cat.value ? 'flat' : 'outlined'"
                    :color="selectedFaqCategory === cat.value ? 'primary' : 'default'"
                    size="small"
                    class="font-weight-medium"
                    @click="selectedFaqCategory = cat.value"
                  >
                    {{ cat.label }}
                  </v-chip>
                </div>
              </div>

              <!-- FAQ Accordion -->
              <div v-if="filteredFaqs.length > 0">
                <v-expansion-panels variant="accordion" class="faq-accordion-group">
                  <v-expansion-panel
                    v-for="(faq, idx) in filteredFaqs"
                    :key="idx"
                    rounded="xl"
                    class="faq-panel border mb-3 overflow-hidden"
                    elevation="0"
                  >
                    <v-expansion-panel-title class="font-weight-bold text-subtitle-1 py-4 text-grey-darken-4">
                      <div class="d-flex align-center ga-3">
                        <span class="faq-category-tag" :style="{ backgroundColor: faq.tagBg, color: faq.tagColor }">
                          {{ faq.categoryLabel }}
                        </span>
                        <span>{{ faq.q }}</span>
                      </div>
                    </v-expansion-panel-title>
                    <v-expansion-panel-text class="text-body-2 text-grey-darken-2 pt-2 pb-4" style="line-height: 1.7;">
                      {{ faq.a }}
                    </v-expansion-panel-text>
                  </v-expansion-panel>
                </v-expansion-panels>
              </div>

              <!-- No FAQ Search Match -->
              <div v-else class="text-center py-10 bg-grey-lighten-5 rounded-xl border">
                <v-icon size="48" color="grey-lighten-1" class="mb-2">mdi-help-circle-outline</v-icon>
                <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-3 mb-1">
                  Tidak Ditemukan Pertanyaan yang Sesuai
                </h4>
                <p class="text-caption text-grey-darken-1 mb-4">
                  Coba gunakan kata kunci lain atau tanyakan langsung kepada tim kami melalui Instagram.
                </p>
                <v-btn
                  href="https://instagram.com/jalanbarengind"
                  target="_blank"
                  rel="noopener noreferrer"
                  color="primary"
                  rounded="pill"
                  size="small"
                  class="font-weight-bold"
                >
                  <v-icon start size="16">mdi-instagram</v-icon>
                  Tanya via DM Instagram
                </v-btn>
              </div>
            </v-card>
          </div>

          <!-- D. DEFAULT VIEW FOR: SYARAT & KETENTUAN, KEBIJAKAN PRIVASI, DLL -->
          <div v-else class="page-content-card">
            <v-card elevation="0" rounded="xl" class="border pa-6 pa-md-10 bg-white">
              <!-- Legal Highlight / Summary Callout -->
              <div class="legal-summary-box pa-6 rounded-xl mb-8">
                <div class="d-flex align-center ga-2 mb-2 text-primary font-weight-bold text-caption text-uppercase">
                  <v-icon size="18" color="primary">mdi-shield-account-outline</v-icon>
                  Ringkasan Inti
                </div>
                <h2 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  Transparan, Beretika, dan Mengutamakan Kenyamanan Bersama
                </h2>
                <p class="text-body-2 text-grey-darken-3 mb-0" style="line-height: 1.7;">
                  Dokumen ini disusun untuk memastikan seluruh interaksi dan kegiatan berjalan dengan transparan, saling menghormati hak privasi, dan memberikan rasa aman bagi seluruh pejalan kaki.
                </p>
              </div>

              <!-- Render HTML content from database or fallback -->
              <div v-if="page?.content" class="page-content" v-html="page.content"></div>
              <div v-else class="page-content">
                <p class="text-body-1 text-grey-darken-2" style="line-height: 1.8;">
                  Konten halaman sedang diperbarui oleh pengelola Jalan Bareng. Silakan hubungi kami apabila memerlukan informasi lebih lanjut.
                </p>
              </div>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'

definePageMeta({
  layout: 'default'
})

const { api } = useApi()
const route = useRoute()

const loading = ref(true)
const page = ref<any>(null)
const currentSlug = computed(() => (route.params.slug as string) || '')

// Community Navigation items
const communityNavItems = [
  { slug: 'tentang-kami', title: 'Tentang Kami', icon: 'mdi-account-group' },
  { slug: 'panduan-komunitas', title: 'Panduan & Etika', icon: 'mdi-shield-check' },
  { slug: 'faq', title: 'Tanya Jawab (FAQ)', icon: 'mdi-help-circle' },
  { slug: 'syarat-ketentuan', title: 'Syarat & Ketentuan', icon: 'mdi-file-document-check' },
  { slug: 'kebijakan-privasi', title: 'Kebijakan Privasi', icon: 'mdi-lock-outline' }
]

// Metadata configuration per slug
const pageMetaMap: Record<string, any> = {
  'tentang-kami': {
    title: 'Tentang Jalan Bareng',
    subtitle: 'Gerakan ruang terbuka untuk merawat trotoar, melambatkan langkah, dan mempertemukan kawan baru di setiap lorong perkotaan.',
    badgeLabel: 'Gerakan & Nilai Komunitas',
    icon: 'mdi-account-group',
    badgeColor: '#EF4444',
    badgeTextColor: '#F87171',
    readTime: '3 menit'
  },
  'panduan-komunitas': {
    title: 'Panduan & Etika Komunitas',
    subtitle: 'Pedoman sederhana agar jalan santai di trotoar dan lorong perkampungan tetap aman, nyaman, santun, dan menyenangkan untuk semua.',
    badgeLabel: 'Etika & Solidaritas Pejalan',
    icon: 'mdi-shield-check',
    badgeColor: '#10B981',
    badgeTextColor: '#34D399',
    readTime: '4 menit'
  },
  'faq': {
    title: 'Tanya Jawab (FAQ)',
    subtitle: 'Kumpulan jawaban seputar pendaftaran, titik kumpul, biaya, perlengkapan, dan kolaborasi bersama Jalan Bareng.',
    badgeLabel: 'Pusat Bantuan & Panduan',
    icon: 'mdi-help-circle',
    badgeColor: '#38BDF8',
    badgeTextColor: '#38BDF8',
    readTime: '5 menit'
  },
  'syarat-ketentuan': {
    title: 'Syarat & Ketentuan',
    subtitle: 'Ketentuan partisipasi kegiatan aktivasi dan penggunaan platform komunitas Jalan Bareng demi kenyamanan bersama.',
    badgeLabel: 'Regulasi & Ketentuan',
    icon: 'mdi-file-document-check',
    badgeColor: '#F59E0B',
    badgeTextColor: '#FBBF24',
    readTime: '4 menit'
  },
  'kebijakan-privasi': {
    title: 'Kebijakan Privasi',
    subtitle: 'Komitmen kami dalam menjaga dan melindungi data pribadi pejalan kaki saat mendaftar aktivasi dan menggunakan web.',
    badgeLabel: 'Perlindungan & Privasi',
    icon: 'mdi-lock-outline',
    badgeColor: '#818CF8',
    badgeTextColor: '#A5B4FC',
    readTime: '3 menit'
  }
}

const pageMeta = computed(() => {
  return pageMetaMap[currentSlug.value] || {
    title: page.value?.title || 'Informasi Komunitas',
    subtitle: 'Informasi dan catatan panduan seputar inisiatif Jalan Bareng.',
    badgeLabel: 'Informasi Komunitas',
    icon: 'mdi-information-outline',
    badgeColor: '#EF4444',
    badgeTextColor: '#F87171',
    readTime: '3 menit'
  }
})

const lastUpdatedText = computed(() => {
  if (page.value?.updated_at) {
    return new Date(page.value.updated_at).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    })
  }
  return 'September 2026'
})

// === TENTANG KAMI DATA ===
const aboutPillars = [
  {
    title: 'Inklusif untuk Semua Warga',
    desc: 'Terbuka untuk semua usia, latar belakang, dan ramah bagi kawan perantau, lansia, maupun pengguna kursi roda/difabel.',
    icon: 'mdi-account-heart',
    bg: '#FEE2E2',
    color: '#DC2626'
  },
  {
    title: 'Merayakan Ruang Publik',
    desc: 'Menghidupkan trotoar, lorong heritage, dan taman kota yang kerap terlupakan agar menjadi ruang perjumpaan yang aman.',
    icon: 'mdi-tree-outline',
    bg: '#DCFCE7',
    color: '#16A34A'
  },
  {
    title: 'Solidaritas & Kepekaan Sosial',
    desc: 'Menghargai ketenangan warga kampung di gang sempit, ramah menyapa, dan tidak meninggalkan kawan yang berjalan lambat.',
    icon: 'mdi-handshake-outline',
    bg: '#FEF3C7',
    color: '#B45309'
  },
  {
    title: 'Melangkah Santai & Bahagia',
    desc: 'Bukan ajang olahraga kompetitif. Kami jalan santai untuk melepas penat, bertukar cerita, dan menyegarkan pikiran.',
    icon: 'mdi-emoticon-happy-outline',
    bg: '#E0F2FE',
    color: '#0284C7'
  }
]

const aboutActivities = [
  {
    title: 'Jalan Bareng Makassar (City Chapter)',
    desc: 'Agenda jalan santai rutin akhir pekan menyusuri trotoar pesisir, pusat kota, dan gang-gang legendaris.',
    icon: 'mdi-walk',
    color: '#DC2626'
  },
  {
    title: 'Diskusi Buku Bareng',
    desc: 'Membawa buku favorit, berjalan santai sore hari, dan duduk melingkar di gazebo taman kota untuk saling berbagi refleksi.',
    icon: 'mdi-book-open-page-variant',
    color: '#0284C7'
  },
  {
    title: 'Makan Bareng (Jelajah Kuliner Lokal)',
    desc: 'Menyusuri gang kuliner tersembunyi sembari mencicipi hidangan otentik dan mendengarkan cerita para pedagang lokal.',
    icon: 'mdi-silverware-fork-knife',
    color: '#B45309'
  },
  {
    title: 'Explore Bareng (Heritage & Sudut Tersembunyi)',
    desc: 'Rute khusus dengan narasi sejarah, cagar budaya, dan eksplorasi sudut kota yang jarang diperhatikan.',
    icon: 'mdi-compass-outline',
    color: '#16A34A'
  }
]

// === PANDUAN KOMUNITAS DATA ===
const guideRules = [
  {
    title: 'Perlengkapan Nyaman & Aman',
    desc: 'Gunakan alas kaki yang nyaman (sneakers/sepatu jalan), kenakan pakaian longgar yang menyerap keringat, dan bawa pelindung cuaca seperti topi atau payung.',
    icon: 'mdi-shoe-sneaker',
    bg: '#FEE2E2',
    color: '#DC2626'
  },
  {
    title: 'Zero Waste: Bawa Tumbler Sendiri',
    desc: 'Kami menerapkan prinsip Leave No Trace. Setiap pejalan wajib membawa botol minum pribadi dan membawa pulang sampahnya masing-masing.',
    icon: 'mdi-bottle-tonic-outline',
    bg: '#DCFCE7',
    color: '#16A34A'
  },
  {
    title: 'Santun di Lorong & Pemukiman Warga',
    desc: 'Saat melewati gang perkampungan sempit, hormati ketenangan warga, kecilkan suara, tidak membuang puntung rokok, dan saling bertegur sapa.',
    icon: 'mdi-home-heart',
    bg: '#FEF3C7',
    color: '#B45309'
  },
  {
    title: 'Solidaritas: Tidak Ada yang Ditinggal',
    desc: 'Rombongan berjalan dengan ritme santai. Saling memperhatikan kawan di sebelah, membantu saat menyeberang jalan raya, dan menjaga keselamatan.',
    icon: 'mdi-account-group-outline',
    bg: '#E0F2FE',
    color: '#0284C7'
  }
]

const dosList = [
  'Membawa tumbler / botol air minum pribadi dari rumah.',
  'Datang tepat waktu di titik kumpul yang telah diumumkan.',
  'Memberi senyum dan sapaan ramah kepada sesama pejalan dan warga sekitar.',
  'Berjalan dalam barisan wajar dan memberi ruang bagi pejalan kaki lain di trotoar.',
  'Membantu kawan yang kelelahan atau membutuhkan bantuan saat menyeberang jalan.',
  'Mendengarkan arahan dari rute guide / relawan pemandu jalan.'
]

const dontsList = [
  'Meninggalkan sampah botol plastik, tisu, atau bungkus makanan di ruang publik.',
  'Menutup seluruh lebar trotoar sehingga pejalan lain terpaksa turun ke jalan raya.',
  'Membuat kegaduhan berlebihan saat melintasi perkampungan warga atau rumah ibadah.',
  'Melakukan ujaran diskriminatif, pelecehan verbal, atau tindakan intoleran.',
  'Berjalan terburu-buru melompati rute tanpa memperhatikan keselamatan penyeberangan.',
  'Menggunakan pelantang suara (speaker) keras tanpa koordinasi tim aktivasi.'
]

// === FAQ DATA ===
const faqSearchQuery = ref('')
const selectedFaqCategory = ref('all')

const faqCategories = [
  { value: 'all', label: 'Semua Pertanyaan' },
  { value: 'registration', label: 'Pendaftaran & Jadwal' },
  { value: 'activity', label: 'Saat Kegiatan' },
  { value: 'fee', label: 'Biaya & Perlengkapan' },
  { value: 'collab', label: 'Kolaborasi Brand' }
]

const faqList = [
  {
    category: 'registration',
    categoryLabel: 'Pendaftaran',
    tagBg: '#FEE2E2',
    tagColor: '#DC2626',
    q: 'Bagaimana cara mendaftar untuk mengikuti jalan santai pekan ini?',
    a: 'Pendaftaran dibuka secara berkala setiap hari Rabu hingga Jumat malam melalui halaman Registrasi Pekan Ini di web ini. Anda cukup memilih aktivasi yang diinginkan dan mengisi formulir Google Form pendaftaran.'
  },
  {
    category: 'fee',
    categoryLabel: 'Biaya',
    tagBg: '#DCFCE7',
    tagColor: '#16A34A',
    q: 'Apakah kegiatan Jalan Bareng dipungut biaya (berbayar)?',
    a: 'Seluruh kegiatan jalan santai rutin mingguan (Jalan Bareng Makassar dan Diskusi Buku Bareng) bersifat 100% GRATIS dan terbuka untuk umum. Khusus aktivasi Makan Bareng, peserta membayar makanan masing-masing (split bill / mandiri).'
  },
  {
    category: 'activity',
    categoryLabel: 'Kegiatan',
    tagBg: '#E0F2FE',
    tagColor: '#0284C7',
    q: 'Apakah saya boleh mengajak anak-anak atau orang tua?',
    a: 'Sangat boleh! Aktivasi kami dirancang inklusif dan ramah keluarga. Rute jalan santai dipilih dengan trotoar yang aman dan kecepatan langkah yang santai.'
  },
  {
    category: 'registration',
    categoryLabel: 'Pendaftaran',
    tagBg: '#FEE2E2',
    tagColor: '#DC2626',
    q: 'Bolehkah saya mendaftar di lebih dari satu aktivasi dalam satu pekan?',
    a: 'Tentu boleh, selama jadwal pelaksanaannya tidak bentrok. Misalnya Anda mengikuti Jalan Bareng Makassar di hari Sabtu pagi, lalu ikut Diskusi Buku Bareng di hari Minggu sore.'
  },
  {
    category: 'fee',
    categoryLabel: 'Perlengkapan',
    tagBg: '#DCFCE7',
    tagColor: '#16A34A',
    q: 'Apa saja perlengkapan yang wajib saya bawa?',
    a: 'Cukup kenakan sepatu santai yang nyaman, pakaian longgar menyerap keringat, dan bawa botol minum / tumbler pribadi untuk mendukung gerakan bebas sampah plastik (Leave No Trace).'
  },
  {
    category: 'activity',
    categoryLabel: 'Kegiatan',
    tagBg: '#E0F2FE',
    tagColor: '#0284C7',
    q: 'Apa yang harus dilakukan jika saya terlambat sampai di titik kumpul?',
    a: 'Rombongan biasanya mulai berjalan sekitar 15-20 menit setelah jam kumpul. Jika terlambat, Anda dapat memantau rute jalan santai yang telah dibagikan di grup atau melalui update live story Instagram @jalanbarengind.'
  },
  {
    category: 'collab',
    categoryLabel: 'Kolaborasi',
    tagBg: '#FEF3C7',
    tagColor: '#B45309',
    q: 'Bagaimana cara brand atau komunitas kami berkolaborasi dengan Jalan Bareng?',
    a: 'Kami sangat terbuka terhadap kolaborasi brand, komunitas literasi, pelaku F&B lokal, maupun inisiatif keberlanjutan. Silakan hubungi kami melalui DM Instagram @jalanbarengind untuk mendiskusikan konsep aktivasi bersama.'
  },
  {
    category: 'activity',
    categoryLabel: 'Buku',
    tagBg: '#E0F2FE',
    tagColor: '#0284C7',
    q: 'Pada aktivasi Diskusi Buku Bareng, buku apa yang harus dibawa?',
    a: 'Bebas! Anda boleh membawa buku fiksi, non-fiksi, puisi, sejarah, komik, atau artikel apapun yang sedang Anda baca. Sesi diskusi berfokus pada saling bertukar refleksi ringan tanpa pretensi akademis yang kaku.'
  }
]

const filteredFaqs = computed(() => {
  let list = faqList

  // Category filter
  if (selectedFaqCategory.value !== 'all') {
    list = list.filter(item => item.category === selectedFaqCategory.value)
  }

  // Search filter
  if (faqSearchQuery.value && faqSearchQuery.value.trim() !== '') {
    const q = faqSearchQuery.value.toLowerCase().trim()
    list = list.filter(item =>
      item.q.toLowerCase().includes(q) ||
      item.a.toLowerCase().includes(q) ||
      item.categoryLabel.toLowerCase().includes(q)
    )
  }

  return list
})

// Fetch page data from backend
const fetchPage = async () => {
  loading.value = true
  try {
    const response = await api.get(`/pages/${currentSlug.value}`)
    page.value = response.data.page

    if (page.value) {
      useHead({
        title: `${pageMeta.value.title} - Jalan Bareng`,
        meta: [
          {
            name: 'description',
            content: pageMeta.value.subtitle
          }
        ]
      })
    }
  } catch (error: any) {
    // Graceful fallback: Still show the rich custom component layout even if DB is empty
    console.warn('Using client-side rich community template for:', currentSlug.value)
    page.value = {
      title: pageMeta.value.title
    }
  } finally {
    loading.value = false
  }
}

watch(() => route.params.slug, () => {
  fetchPage()
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
})

onMounted(() => {
  fetchPage()
})
</script>

<style scoped>
.community-page-wrapper {
  background: #F8FAFC;
  min-height: 100vh;
}

/* 1. Hero Header */
.community-hero-section {
  background: radial-gradient(circle at 85% 20%, rgba(37, 99, 235, 0.25) 0%, transparent 45%),
              radial-gradient(circle at 15% 85%, rgba(220, 38, 38, 0.18) 0%, transparent 40%),
              #0F172A;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
}

.breadcrumb-link {
  color: #94A3B8;
  text-decoration: none;
  transition: color 0.2s ease;
}

.breadcrumb-link:hover {
  color: #FFFFFF;
}

.hero-topic-badge {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(8px);
}

.page-main-title {
  letter-spacing: -0.025em;
  line-height: 1.15;
}

/* 2. Sidebar Navigation */
.sticky-sidebar {
  position: sticky;
  top: 90px;
}

.nav-page-item {
  color: #475569;
  transition: all 0.2s ease;
}

.nav-page-item:hover {
  background: #F1F5F9;
  color: #0F172A;
}

.nav-item-active {
  background: #FEF2F2 !important;
  color: #DC2626 !important;
  font-weight: 700;
}

.active-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #DC2626;
  display: inline-block;
}

.cta-mini-box {
  background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%);
  box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3);
}

/* 3. Custom Page Content Cards */
.story-highlight-box {
  background: #FFF7ED;
  border: 1px solid #FFEDD5;
}

.pillar-card {
  background: #FFFFFF;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.pillar-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.05);
}

.pillar-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.collab-callout-box {
  background: #FFFBEB;
  border-color: #FDE68A !important;
}

.guide-intro-banner {
  background: #F0FDF4;
  border: 1px solid #DCFCE7;
}

.rule-card {
  background: #FFFFFF;
}

.rule-icon-box {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dos-box {
  background: #F0FDF4;
  border-color: #BBF7D0 !important;
}

.donts-box {
  background: #FEF2F2;
  border-color: #FECACA !important;
}

.clean-checklist {
  list-style: none;
  padding-left: 0;
  margin-bottom: 0;
}

.legal-summary-box {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
}

/* FAQ Accordion Styling */
.faq-category-tag {
  font-size: 0.7rem;
  font-weight: 700;
  padding: 2px 8px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  flex-shrink: 0;
}

.faq-panel {
  background: #FFFFFF !important;
  border-color: #E2E8F0 !important;
  transition: all 0.2s ease;
}

.faq-panel:hover {
  border-color: #CBD5E1 !important;
}

/* Rich Prose / HTML Content */
.page-content :deep(h2) {
  font-size: 1.5rem;
  font-weight: 700;
  margin-top: 1.75rem;
  margin-bottom: 0.75rem;
  color: #0F172A;
}

.page-content :deep(h3) {
  font-size: 1.25rem;
  font-weight: 700;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
  color: #1E293B;
}

.page-content :deep(p) {
  margin-bottom: 1rem;
  line-height: 1.75;
  color: #334155;
}

.page-content :deep(ul),
.page-content :deep(ol) {
  margin-bottom: 1rem;
  padding-left: 1.5rem;
  color: #334155;
}

.page-content :deep(li) {
  margin-bottom: 0.4rem;
  line-height: 1.6;
}

.page-content :deep(a) {
  color: #DC2626;
  text-decoration: underline;
}

@media (max-width: 600px) {
  .faq-header-box .v-chip {
    font-size: 0.72rem !important;
    min-height: 26px !important;
    height: 26px !important;
    padding: 0 8px !important;
  }
}
</style>
