<template>
  <div class="partners-page">
    <!-- Editorial Hero Section -->
    <section class="partners-hero">
      <v-container class="hero-content">
        <v-row align="center" justify="center">
          <v-col cols="12" md="10" lg="8" class="text-center">
            <!-- Eyebrow Badge -->
            <div class="hero-badge-pill mb-4">
              <v-icon start size="16" color="#DC2626">mdi-handshake-outline</v-icon>
              <span>KOLABORASI &amp; SINERGI STRATEGIS</span>
            </div>

            <!-- Main Headline -->
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              Melangkah Bersama Para
              <span class="text-primary-red">Mitra &amp; Kolaborator</span>
            </h1>

            <!-- Subtitle -->
            <p class="hero-subtitle text-grey-darken-1 mx-auto mb-8">
              Apresiasi mendalam untuk 80+ mitra lintas sektor—mulai dari brand global, kementerian, pemerintah daerah, institusi pendidikan, BUMN, hingga jejaring komunitas yang telah mempercayai dan berjalan bersama Jalan Bareng merawat ruang kota ramah pejalan.
            </p>

            <!-- Quick Stats Row -->
            <div class="d-flex align-center justify-center flex-wrap ga-3 ga-md-6 mb-2">
              <div class="stat-pill">
                <span class="stat-num text-primary-red">80+</span>
                <span class="stat-lbl">Mitra Strategis</span>
              </div>
              <div class="stat-pill">
                <span class="stat-num text-grey-darken-4">10.000+</span>
                <span class="stat-lbl">Pendaftar Database</span>
              </div>
              <div class="stat-pill">
                <span class="stat-num text-grey-darken-4">5</span>
                <span class="stat-lbl">Wilayah Aktivasi</span>
              </div>
              <div class="stat-pill">
                <span class="stat-num text-primary-red">2+</span>
                <span class="stat-lbl">Tahun Melangkah</span>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Container -->
    <v-container class="py-10">
      <!-- 1. Featured Spotlight Partnership: Infinix x Jalan Bareng -->
      <section class="mb-14">
        <div class="spotlight-card pa-6 pa-md-10">
          <v-row align="center">
            <v-col cols="12" md="7">
              <div class="d-inline-flex align-center ga-1 text-caption font-weight-bold text-primary-red text-uppercase tracking-wider mb-2">
                <v-icon size="14" color="#DC2626">mdi-star-four-points</v-icon>
                <span>Sorotan Kolaborasi Unggulan</span>
              </div>
              <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-3">
                Infinix x Jalan Bareng
              </h2>
              <p class="text-body-1 text-grey-darken-1 mb-6" style="line-height: 1.7;">
                Kolaborasi integratif antara inovasi teknologi dan gerakan eksplorasi kota. Menghubungkan eksplorasi visual fotografi perkotaan, dokumentasi sudut bersejarah Makassar, serta aktivasi anak muda melalui rute walking tour bertema eksplorasi kreatif.
              </p>

              <div class="d-flex flex-wrap ga-2 mb-6">
                <span class="tag-chip">
                  <v-icon start size="14" color="#DC2626">mdi-check-decagram</v-icon>
                  Brand Sponsor &amp; Co-Creator
                </span>
                <span class="tag-chip">
                  <v-icon start size="14" color="#DC2626">mdi-camera-outline</v-icon>
                  Street Photography Walk
                </span>
                <span class="tag-chip">
                  <v-icon start size="14" color="#DC2626">mdi-account-group-outline</v-icon>
                  Aktivasi Komunitas Muda
                </span>
              </div>

              <div class="d-flex align-center ga-3">
                <a
                  href="#kolaborasi"
                  class="btn-primary-pill text-decoration-none"
                >
                  <v-icon start size="18">mdi-handshake</v-icon>
                  Ajukan Kolaborasi Brand
                </a>
              </div>
            </v-col>

            <v-col cols="12" md="5" class="mt-6 mt-md-0">
              <div class="spotlight-logo-box text-center pa-8">
                <div class="brand-duo-badge mb-4">
                  <span class="brand-name-infinix">INFINIX</span>
                  <span class="brand-divider">✕</span>
                  <span class="brand-name-jb">JALAN BARENG</span>
                </div>
                <div class="text-caption text-grey-darken-1 font-weight-medium">
                  Official Technology &amp; Lifestyle Walking Partner
                </div>
              </div>
            </v-col>
          </v-row>
        </div>
      </section>

      <!-- 2. Category Filter & Search Bar -->
      <section class="mb-10">
        <div class="d-flex flex-column flex-md-row align-md-center justify-space-between ga-4 mb-6">
          <div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-1">Direktori Mitra</h2>
            <p class="text-body-2 text-grey-darken-1 mb-0">
              Menampilkan {{ filteredPartners.length }} mitra yang telah melangkah dan berkontribusi bersama kami
            </p>
          </div>

          <!-- Search Input -->
          <div style="max-width: 320px; width: 100%;">
            <v-text-field
              v-model="searchQuery"
              label="Cari nama mitra..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              rounded="lg"
              density="compact"
              hide-details
              clearable
            ></v-text-field>
          </div>
        </div>

        <!-- Filter Chips -->
        <div class="category-filters-wrapper d-flex align-center flex-wrap ga-2 mb-8">
          <button
            type="button"
            :class="['filter-btn', { active: selectedCategory === 'all' }]"
            @click="selectedCategory = 'all'"
          >
            Semua Sektor ({{ partnersList.length }})
          </button>
          <button
            type="button"
            :class="['filter-btn', { active: selectedCategory === 'brand' }]"
            @click="selectedCategory = 'brand'"
          >
            <v-icon start size="16">mdi-tag-outline</v-icon>
            Brand &amp; Sponsor
          </button>
          <button
            type="button"
            :class="['filter-btn', { active: selectedCategory === 'government' }]"
            @click="selectedCategory = 'government'"
          >
            <v-icon start size="16">mdi-bank-outline</v-icon>
            Pemerintah &amp; Kementerian
          </button>
          <button
            type="button"
            :class="['filter-btn', { active: selectedCategory === 'bumn' }]"
            @click="selectedCategory = 'bumn'"
          >
            <v-icon start size="16">mdi-domain</v-icon>
            BUMN &amp; Institusi
          </button>
          <button
            type="button"
            :class="['filter-btn', { active: selectedCategory === 'community' }]"
            @click="selectedCategory = 'community'"
          >
            <v-icon start size="16">mdi-account-group-outline</v-icon>
            Komunitas &amp; Media
          </button>
        </div>

        <!-- Partners Grid -->
        <v-row v-if="filteredPartners.length > 0">
          <v-col
            v-for="partner in filteredPartners"
            :key="partner.id"
            cols="12"
            sm="6"
            md="4"
            lg="3"
          >
            <div class="partner-card h-100 pa-5 d-flex flex-column justify-space-between">
              <div>
                <!-- Top Pill / Role -->
                <div class="d-flex align-center justify-space-between mb-3">
                  <span :class="['role-pill', partner.category]">
                    {{ getCategoryLabel(partner.category) }}
                  </span>
                  <v-icon size="16" color="#94A3B8">mdi-handshake</v-icon>
                </div>

                <!-- Partner Logo or Emblem -->
                <div class="partner-logo-wrapper mb-3">
                  <div v-if="partner.logo && !failedLogos[partner.id]" class="partner-logo-box">
                    <img
                      :src="partner.logo"
                      :alt="partner.name + ' Logo'"
                      class="partner-logo-img"
                      loading="lazy"
                      @error="failedLogos[partner.id] = true"
                    />
                  </div>
                  <div v-else class="partner-emblem" :style="{ backgroundColor: partner.bgColor || '#F1F5F9' }">
                    <span class="emblem-text" :style="{ color: partner.textColor || '#0F172A' }">
                      {{ partner.initial }}
                    </span>
                  </div>
                </div>

                <!-- Partner Name & Detail -->
                <h3 class="partner-name text-subtitle-1 font-weight-bold text-grey-darken-4 mb-1">
                  {{ partner.name }}
                </h3>
                <p class="partner-desc text-caption text-grey-darken-1 mb-0">
                  {{ partner.role }}
                </p>
              </div>

              <!-- Bottom Tag -->
              <div class="pt-4 mt-4 border-t d-flex align-center justify-space-between">
                <span class="text-caption text-grey font-weight-medium">Bentuk Kolaborasi</span>
                <span class="collab-type-text">{{ partner.collabType }}</span>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- Empty Search State -->
        <div v-else class="empty-state-box text-center pa-12">
          <v-icon size="56" color="grey-lighten-1">mdi-domain-off</v-icon>
          <h3 class="text-h6 font-weight-bold text-grey-darken-3 mt-4 mb-2">Mitra tidak ditemukan</h3>
          <p class="text-body-2 text-grey-darken-1 mb-0">Coba ubah kata kunci pencarian Anda</p>
        </div>
      </section>

      <!-- 3. Partnership Opportunities / Cara Berkolaborasi -->
      <section id="kolaborasi" class="mb-14">
        <div class="kolaborasi-card pa-8 pa-md-12">
          <div class="text-center max-w-700 mx-auto mb-10">
            <div class="hero-badge-pill mb-3 d-inline-flex">
              <v-icon start size="16" color="#DC2626">mdi-briefcase-outline</v-icon>
              <span>RUANG KEMITRAAN</span>
            </div>
            <h2 class="text-h4 font-weight-black text-grey-darken-4 mb-3">
              Mari Berjalan &amp; Bertumbuh Bersama
            </h2>
            <p class="text-body-1 text-grey-darken-1 mb-0" style="line-height: 1.7;">
              Kami membuka peluang kolaborasi seluas-luasnya bagi perusahaan, institusi, instansi pemerintah, dan gerakan sosial yang memiliki kepedulian terhadap tata kota yang inklusif, ramah iklim, dan pemberdayaan anak muda.
            </p>
          </div>

          <v-row>
            <v-col cols="12" md="4">
              <div class="collab-model-card pa-6 h-100">
                <div class="model-icon-box mb-4">
                  <v-icon size="28" color="#DC2626">mdi-bullhorn-outline</v-icon>
                </div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  Brand Activation &amp; Sponsor
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-0" style="line-height: 1.6;">
                  Tap-in kampanye brand Anda secara organik dalam rute walking tour tematik, festival jalan santai, dokumentasi kreatif, dan interaksi langsung dengan ribuan peserta aktif.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="collab-model-card pa-6 h-100">
                <div class="model-icon-box mb-4">
                  <v-icon size="28" color="#EA580C">mdi-leaf</v-icon>
                </div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  CSR &amp; Program Ramah Iklim
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-0" style="line-height: 1.6;">
                  Program aksi sosial penanaman pohon, audit trotoar kota, pemenuhan hak bermain anak bersama Lanjut Bergerak, serta kampanye gaya hidup sehat nol emisi.
                </p>
              </div>
            </v-col>

            <v-col cols="12" md="4">
              <div class="collab-model-card pa-6 h-100">
                <div class="model-icon-box mb-4">
                  <v-icon size="28" color="#0284C7">mdi-storefront-outline</v-icon>
                </div>
                <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-2">
                  Aktivasi Ruang &amp; Kreativitas
                </h3>
                <p class="text-body-2 text-grey-darken-1 mb-0" style="line-height: 1.6;">
                  Menghidupkan ruang publik, spot kreatif, galeri seni, atau ruang usaha melalui pameran instalasi seni, lokakarya kriya Djari Djemari, diskusi buku, dan sharing session.
                </p>
              </div>
            </v-col>
          </v-row>

          <!-- Contact Action Bar -->
          <div class="mt-10 pt-8 border-t text-center">
            <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-3">
              Tertarik Berkolaborasi dengan Komunitas Jalan Bareng?
            </h4>
            <div class="d-flex align-center justify-center flex-wrap ga-3">
              <a
                href="https://wa.me/6281234567890?text=Halo%20Tim%20Jalan%20Bareng,%20kami%20tertarik%20untuk%20mengajukan%20kolaborasi/kemitraan."
                target="_blank"
                rel="noopener noreferrer"
                class="btn-primary-pill text-decoration-none"
              >
                <v-icon start size="18">mdi-whatsapp</v-icon>
                Hubungi Tim Kemitraan
              </a>
              <a
                href="mailto:kerjasama@jalanbareng.id"
                class="btn-outline-pill text-decoration-none"
              >
                <v-icon start size="18">mdi-email-outline</v-icon>
                Kirim Email Proposal
              </a>
            </div>
          </div>
        </div>
      </section>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useSeoMeta } from '#app'

definePageMeta({
  layout: 'default'
})

useSeoMeta({
  title: 'Mitra & Kolaborator - Jalan Bareng',
  ogTitle: 'Mitra & Kolaborator - Jalan Bareng',
  description: 'Daftar mitra strategis dari pemerintah, kementerian, brand global, BUMN, institusi, dan komunitas yang melangkah bersama Jalan Bareng.',
  ogDescription: 'Daftar mitra strategis dari pemerintah, kementerian, brand global, BUMN, institusi, dan komunitas yang melangkah bersama Jalan Bareng.',
  ogImage: '/images/hero/walk_1.jpg',
  twitterCard: 'summary_large_image',
})

const selectedCategory = ref<'all' | 'brand' | 'government' | 'bumn' | 'community'>('all')
const searchQuery = ref('')
const failedLogos = ref<Record<number, boolean>>({})

interface Partner {
  id: number
  name: string
  category: 'brand' | 'government' | 'bumn' | 'community'
  role: string
  collabType: string
  initial: string
  logo?: string
  bgColor?: string
  textColor?: string
}

// 80+ Strategic Partners Directory
const partnersList = ref<Partner[]>([
  // Brands & Sponsors
  { 
    id: 1, 
    name: 'Infinix Indonesia', 
    category: 'brand', 
    role: 'Official Technology & Visual Storytelling Partner', 
    collabType: 'Brand Sponsor & Co-Creator', 
    initial: 'IFX', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Infinix_logo.svg/320px-Infinix_logo.svg.png',
    bgColor: '#FEF2F2', 
    textColor: '#DC2626' 
  },
  { 
    id: 2, 
    name: 'Eiger Adventure', 
    category: 'brand', 
    role: 'Outdoor & Walking Apparel Support', 
    collabType: 'Apparel Partner', 
    initial: 'EGR', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Logo_Eiger.png/320px-Logo_Eiger.png',
    bgColor: '#FEF3C7', 
    textColor: '#D97706' 
  },
  { 
    id: 3, 
    name: 'Hydro Coco', 
    category: 'brand', 
    role: 'Official Hydration Partner', 
    collabType: 'Event Sponsor', 
    initial: 'HYD', 
    logo: 'https://upload.wikimedia.org/wikipedia/id/thumb/7/70/Hydro_Coco.svg/320px-Hydro_Coco.svg.png',
    bgColor: '#ECFDF5', 
    textColor: '#059669' 
  },
  { 
    id: 4, 
    name: 'Kopi Kenangan', 
    category: 'brand', 
    role: 'Community Coffee Break Partner', 
    collabType: 'F&B Partner', 
    initial: 'KK', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Kopi_Kenangan_logo.svg/320px-Kopi_Kenangan_logo.svg.png',
    bgColor: '#FFFBEB', 
    textColor: '#B45309' 
  },
  { 
    id: 5, 
    name: 'Sunpride Indonesia', 
    category: 'brand', 
    role: 'Pemberian Buah Segar Pejalan', 
    collabType: 'Nutrition Partner', 
    initial: 'SP', 
    bgColor: '#FEF9C3', 
    textColor: '#A16207' 
  },
  { 
    id: 6, 
    name: 'Grab Indonesia', 
    category: 'brand', 
    role: 'Dukungan Transportasi Titik Kumpul', 
    collabType: 'Mobility Partner', 
    initial: 'GRB', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Grab_Logo.svg/320px-Grab_Logo.svg.png',
    bgColor: '#F0FDF4', 
    textColor: '#16A34A' 
  },
  { 
    id: 7, 
    name: 'Wardah', 
    category: 'brand', 
    role: 'Skincare & Sun Protection Walk Partner', 
    collabType: 'Brand Sponsor', 
    initial: 'WRD', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Wardah_logo.svg/320px-Wardah_logo.svg.png',
    bgColor: '#FDF2F8', 
    textColor: '#DB2777' 
  },
  { 
    id: 8, 
    name: 'Decathlon Indonesia', 
    category: 'brand', 
    role: 'Perlengkapan Olahraga & Jalan Santai', 
    collabType: 'Equipment Partner', 
    initial: 'DCT', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Decathlon_Logo.svg/320px-Decathlon_Logo.svg.png',
    bgColor: '#EFF6FF', 
    textColor: '#2563EB' 
  },

  // Pemerintah & Kementerian
  { 
    id: 9, 
    name: 'Kemenparekraf RI', 
    category: 'government', 
    role: 'Dukungan Pengembangan Wisata Jalan Kaki & Budaya', 
    collabType: 'Institutional Partner', 
    initial: 'KPK', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Kemenparekraf_Logo.png/320px-Kemenparekraf_Logo.png',
    bgColor: '#FEF2F2', 
    textColor: '#DC2626' 
  },
  { 
    id: 10, 
    name: 'Pemerintah Kota Makassar', 
    category: 'government', 
    role: 'Dukungan Ruang Terbuka Hijau & Pejalan Kaki', 
    collabType: 'Government Partner', 
    initial: 'MKS', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Lambang_Kota_Makassar.png/240px-Lambang_Kota_Makassar.png',
    bgColor: '#F8FAFC', 
    textColor: '#334155' 
  },
  { 
    id: 11, 
    name: 'Dinas Pariwisata Kota Makassar', 
    category: 'government', 
    role: 'Eksplorasi Warisan Sejarah & Rute Wisata Kota', 
    collabType: 'Tourism Partner', 
    initial: 'DP', 
    bgColor: '#F1F5F9', 
    textColor: '#475569' 
  },
  { 
    id: 12, 
    name: 'Dinas Pemuda & Olahraga (Dispora)', 
    category: 'government', 
    role: 'Gerakan Kebugaran Fisik Masyarakat Produktif', 
    collabType: 'Sports Partner', 
    initial: 'DSP', 
    bgColor: '#F0FDF4', 
    textColor: '#15803D' 
  },
  { 
    id: 13, 
    name: 'Dinas Tata Ruang & Pemukiman', 
    category: 'government', 
    role: 'Advokasi Trotoar & Ramah Difabel', 
    collabType: 'Urban Planning', 
    initial: 'DTR', 
    bgColor: '#EFF6FF', 
    textColor: '#1D4ED8' 
  },
  { 
    id: 14, 
    name: 'Pemerintah Kota Palopo', 
    category: 'government', 
    role: 'Fasilitasi Ruang Aktivasi Pejalan di Palopo', 
    collabType: 'Regional Partner', 
    initial: 'PLP', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Lambang_Kota_Palopo.png/240px-Lambang_Kota_Palopo.png',
    bgColor: '#F8FAFC', 
    textColor: '#1E293B' 
  },
  { 
    id: 15, 
    name: 'Pemerintah Kabupaten Gowa', 
    category: 'government', 
    role: 'Dukungan Rute Sejarah Somba Opu & Benteng', 
    collabType: 'Regional Partner', 
    initial: 'GWA', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Lambang_Kabupaten_Gowa.png/240px-Lambang_Kabupaten_Gowa.png',
    bgColor: '#FEF3C7', 
    textColor: '#B45309' 
  },
  { 
    id: 16, 
    name: 'Pemerintah Kabupaten Bone', 
    category: 'government', 
    role: 'Eksplorasi Budaya Bugis & Ruang Terbuka Hijau', 
    collabType: 'Regional Partner', 
    initial: 'BNE', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Lambang_Kabupaten_Bone.png/240px-Lambang_Kabupaten_Bone.png',
    bgColor: '#FDF2F8', 
    textColor: '#BE185D' 
  },

  // BUMN & Institusi
  { 
    id: 17, 
    name: 'PT Bank Mandiri (Persero) Tbk', 
    category: 'bumn', 
    role: 'Dukungan Transaksi UMKM Digital & Kemitraan', 
    collabType: 'BUMN Partner', 
    initial: 'BM', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/320px-Bank_Mandiri_logo_2016.svg.png',
    bgColor: '#EFF6FF', 
    textColor: '#1E40AF' 
  },
  { 
    id: 18, 
    name: 'PT Telkom Indonesia Tbk', 
    category: 'bumn', 
    role: 'Konektivitas & Ruang Diskusi Digital', 
    collabType: 'Digital Partner', 
    initial: 'TLK', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Telkom_Indonesia_2013.svg/320px-Telkom_Indonesia_2013.svg.png',
    bgColor: '#FEF2F2', 
    textColor: '#DC2626' 
  },
  { 
    id: 19, 
    name: 'PT Pos Indonesia (Pos Bloc Makassar)', 
    category: 'bumn', 
    role: 'Titik Kumpul & Creative Hub Pejalan Kaki', 
    collabType: 'Venue & Creative Hub', 
    initial: 'POS', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Logo_Pos_Indonesia_%282023%29.svg/320px-Logo_Pos_Indonesia_%282023%29.svg.png',
    bgColor: '#FFF7ED', 
    textColor: '#EA580C' 
  },
  { 
    id: 20, 
    name: 'PT PLN (Persero)', 
    category: 'bumn', 
    role: 'Dukungan Program Ramah Iklim & Nol Emisi', 
    collabType: 'CSR & Eco Partner', 
    initial: 'PLN', 
    logo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Logo_PLN.svg/320px-Logo_PLN.svg.png',
    bgColor: '#F0FDF4', 
    textColor: '#16A34A' 
  },
  { 
    id: 21, 
    name: 'Universitas Hasanuddin (UNHAS)', 
    category: 'bumn', 
    role: 'Riset Tata Ruang, Inklusi & Relawan Mahasiswa', 
    collabType: 'Academic Partner', 
    initial: 'UNH', 
    logo: 'https://upload.wikimedia.org/wikipedia/id/thumb/7/72/Logo_Universitas_Hasanuddin.svg/320px-Logo_Universitas_Hasanuddin.svg.png',
    bgColor: '#FEF2F2', 
    textColor: '#B91C1C' 
  },
  { 
    id: 22, 
    name: 'Universitas Negeri Makassar (UNM)', 
    category: 'bumn', 
    role: 'Pemberdayaan Literasi Ruang Publik & Kriya', 
    collabType: 'Academic Partner', 
    initial: 'UNM', 
    logo: 'https://upload.wikimedia.org/wikipedia/id/thumb/1/15/Logo_UNM.png/240px-Logo_UNM.png',
    bgColor: '#FFFBEB', 
    textColor: '#D97706' 
  },
  { 
    id: 23, 
    name: 'Institut Kesenian Makassar', 
    category: 'bumn', 
    role: 'Aktivasi Budaya, Instalasi Seni & Musik Lorong', 
    collabType: 'Arts Partner', 
    initial: 'IKM', 
    bgColor: '#FAF5FF', 
    textColor: '#7E22CE' 
  },

  // Komunitas & Media Partner
  { 
    id: 24, 
    name: 'Koalisi Pejalan Kaki', 
    category: 'community', 
    role: 'Solidaritas Advokasi Hak Pejalan Kaki Nasional', 
    collabType: 'Advocacy Network', 
    initial: 'KPK', 
    bgColor: '#F8FAFC', 
    textColor: '#0F172A' 
  },
  { 
    id: 25, 
    name: 'Makassar Berkebun', 
    category: 'community', 
    role: 'Eksplorasi Urban Farming & Kelestarian Ruang', 
    collabType: 'Green Community', 
    initial: 'MB', 
    bgColor: '#ECFDF5', 
    textColor: '#047857' 
  },
  { 
    id: 26, 
    name: 'Rumah Buku Makassar', 
    category: 'community', 
    role: 'Kolaborator Diskusi Buku Bareng di Taman Kota', 
    collabType: 'Literacy Partner', 
    initial: 'RBM', 
    bgColor: '#FFF7ED', 
    textColor: '#C2410C' 
  },
  { 
    id: 27, 
    name: 'Tribun Timur', 
    category: 'community', 
    role: 'Publikasi & Liputan Kegiatan Rutin Pejalan', 
    collabType: 'Media Partner', 
    initial: 'TRB', 
    bgColor: '#EFF6FF', 
    textColor: '#1D4ED8' 
  },
  { 
    id: 28, 
    name: 'Fajar Makassar', 
    category: 'community', 
    role: 'Dokumentasi & Kanal Narasi Sudut Kota', 
    collabType: 'Media Partner', 
    initial: 'FJR', 
    bgColor: '#FEF2F2', 
    textColor: '#BE123C' 
  },
  { 
    id: 29, 
    name: 'Makassar Terkini', 
    category: 'community', 
    role: 'Sosialisasi Agenda Pekanan & Panduan Acara', 
    collabType: 'Media Partner', 
    initial: 'MT', 
    bgColor: '#F0FDF4', 
    textColor: '#15803D' 
  },
  { 
    id: 30, 
    name: 'Komunitas Tuli Makassar', 
    category: 'community', 
    role: 'Inklusi Bahasa Isyarat dalam Walking Tour', 
    collabType: 'Inclusion Partner', 
    initial: 'KTM', 
    bgColor: '#FEF3C7', 
    textColor: '#B45309' 
  },
  { 
    id: 31, 
    name: 'Earth Hour Makassar', 
    category: 'community', 
    role: 'Aksi Lingkungan & Berjalan Kaki Minim Emisi', 
    collabType: 'Eco Partner', 
    initial: 'EHM', 
    bgColor: '#ECFDF5', 
    textColor: '#059669' 
  },
  { 
    id: 32, 
    name: 'Save the Children ID', 
    category: 'community', 
    role: 'Kolaborasi Ramah Anak bersama Lanjut Bergerak', 
    collabType: 'Social Impact', 
    initial: 'STC', 
    bgColor: '#FEF2F2', 
    textColor: '#DC2626' 
  },
])

// Filter logic
const filteredPartners = computed(() => {
  return partnersList.value.filter(partner => {
    const matchCategory = selectedCategory.value === 'all' || partner.category === selectedCategory.value
    const matchSearch = !searchQuery.value || 
      partner.name.toLowerCase().includes(searchQuery.value.toLowerCase().trim()) ||
      partner.role.toLowerCase().includes(searchQuery.value.toLowerCase().trim())
    return matchCategory && matchSearch
  })
})

const getCategoryLabel = (category: string) => {
  const map: Record<string, string> = {
    brand: 'Brand & Sponsor',
    government: 'Pemerintah',
    bumn: 'BUMN & Institusi',
    community: 'Komunitas & Media'
  }
  return map[category] || 'Mitra'
}
</script>

<style scoped>
/* Hero Section - Editorial Light */
.partners-hero {
  background: #FAFAFA;
  padding-top: 48px;
  padding-bottom: 56px;
  border-bottom: 1px solid #F1F5F9;
}

.hero-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 16px;
  border-radius: 9999px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  font-size: 0.74rem;
  font-weight: 700;
  color: #DC2626;
  letter-spacing: 0.06em;
}

.hero-title {
  font-size: clamp(2.2rem, 4vw, 3.4rem);
  letter-spacing: -0.035em;
  line-height: 1.15;
  color: #111827;
}

.text-primary-red {
  color: #DC2626;
}

.hero-subtitle {
  font-size: 1.1rem;
  line-height: 1.65;
  max-width: 720px;
}

/* Stat Pill */
.stat-pill {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  padding: 8px 18px;
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 16px;
  min-width: 130px;
}

.stat-num {
  font-size: 1.4rem;
  font-weight: 900;
  line-height: 1.1;
}

.stat-lbl {
  font-size: 0.76rem;
  color: #64748B;
  font-weight: 600;
}

/* Spotlight Card (Infinix x Jalan Bareng) */
.spotlight-card {
  background: #FFFFFF;
  border: 1.5px solid #E2E8F0;
  border-radius: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.spotlight-logo-box {
  background: #FAFAFA;
  border: 1.5px dashed #CBD5E1;
  border-radius: 20px;
}

.brand-duo-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
}

.brand-name-infinix {
  font-size: 1.4rem;
  font-weight: 900;
  letter-spacing: 0.1em;
  color: #111827;
}

.brand-divider {
  font-size: 1rem;
  font-weight: 700;
  color: #DC2626;
}

.brand-name-jb {
  font-size: 1.2rem;
  font-weight: 800;
  color: #DC2626;
}

.tag-chip {
  display: inline-flex;
  align-items: center;
  padding: 5px 12px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 9999px;
  font-size: 0.78rem;
  font-weight: 600;
  color: #334155;
}

/* Filter Buttons */
.category-filters-wrapper {
  margin-top: 4px;
}

.filter-btn {
  display: inline-flex;
  align-items: center;
  padding: 8px 18px;
  border-radius: 9999px;
  border: 1px solid #E2E8F0;
  background: #FFFFFF;
  color: #4B5563;
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #111827;
}

.filter-btn.active {
  background: #DC2626;
  border-color: #DC2626;
  color: #FFFFFF;
  box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
}

/* Partner Card */
.partner-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.partner-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.08);
  border-color: #CBD5E1;
}

.partner-logo-wrapper {
  height: 54px;
  display: flex;
  align-items: center;
}

.partner-logo-box {
  height: 48px;
  max-width: 140px;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.partner-logo-img {
  max-height: 42px;
  max-width: 130px;
  width: auto;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.04));
}

.partner-emblem {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.emblem-text {
  font-size: 1.1rem;
  font-weight: 900;
  letter-spacing: -0.02em;
}

.partner-name {
  line-height: 1.3;
}

.partner-desc {
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.role-pill {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.role-pill.brand {
  background: #FEF2F2;
  color: #DC2626;
}

.role-pill.government {
  background: #F1F5F9;
  color: #334155;
}

.role-pill.bumn {
  background: #EFF6FF;
  color: #2563EB;
}

.role-pill.community {
  background: #ECFDF5;
  color: #059669;
}

.collab-type-text {
  font-size: 0.75rem;
  font-weight: 700;
  color: #DC2626;
}

/* Kolaborasi Models Card */
.kolaborasi-card {
  border-radius: 24px;
  border: 1px solid #E2E8F0;
  background: #FAFAFA;
}

.collab-model-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.collab-model-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
}

.model-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Buttons */
.btn-primary-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 24px;
  border-radius: 9999px;
  background: #DC2626;
  color: #FFFFFF;
  font-weight: 700;
  font-size: 0.92rem;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary-pill:hover {
  background: #B91C1C;
  transform: translateY(-1px);
}

.btn-outline-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 24px;
  border-radius: 9999px;
  background: transparent;
  border: 1.5px solid #CBD5E1;
  color: #374151;
  font-weight: 700;
  font-size: 0.92rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-outline-pill:hover {
  border-color: #DC2626;
  color: #DC2626;
  background: #FEF2F2;
}

.empty-state-box {
  background: #FFFFFF;
  border: 1px dashed #CBD5E1;
  border-radius: 20px;
}

.max-w-700 {
  max-width: 700px;
}

/* Mobile Responsiveness */
@media (max-width: 600px) {
  .partners-hero {
    padding-top: 32px;
    padding-bottom: 35px;
  }

  .hero-title {
    font-size: 1.95rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }

  .filter-btn {
    font-size: 0.8rem;
    padding: 6px 14px;
  }
}
</style>
