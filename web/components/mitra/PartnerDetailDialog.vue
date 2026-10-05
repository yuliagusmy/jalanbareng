<template>
  <v-dialog
    :model-value="modelValue"
    max-width="460"
    rounded="24"
    transition="dialog-bottom-transition"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <v-card v-if="partner" class="partner-modal-card pa-5 pa-sm-6" rounded="24" elevation="10">
      <!-- Top Action Bar: Category Pill & Close Icon -->
      <div class="d-flex align-center justify-space-between mb-4">
        <div class="partner-category-chip d-inline-flex align-center ga-1-5 px-3 py-1 rounded-pill" :class="partner.category">
          <v-icon size="14">{{ getCategoryIcon(partner.category) }}</v-icon>
          <span class="text-caption font-weight-bold">{{ getCategoryLabel(partner.category) }}</span>
        </div>

        <v-btn
          icon
          size="small"
          variant="text"
          color="grey-darken-1"
          class="modal-close-icon-btn"
          aria-label="Tutup Dialog Mitra"
          @click="$emit('update:modelValue', false)"
        >
          <v-icon size="18">mdi-close</v-icon>
        </v-btn>
      </div>

      <!-- Hero Logo Showcase Frame (Wider, prominent, matching full width of close button) -->
      <div class="partner-modal-logo-stage mb-4">
        <img
          v-if="partner.logo && !hasLogoError"
          :src="partner.logo"
          :alt="partner.name"
          class="modal-hero-logo"
          @error="hasLogoError = true"
        />
        <div
          v-else
          class="modal-hero-emblem"
          :style="{ backgroundColor: partner.bgColor || '#F1F5F9', color: partner.textColor || '#0F172A' }"
        >
          <span class="modal-emblem-initials">{{ partner.initial }}</span>
          <span class="modal-emblem-name text-truncate">{{ partner.name }}</span>
        </div>
      </div>

      <!-- Partner Identity & Role Info -->
      <div class="text-center mb-4">
        <h2 class="partner-title-heading mb-1 text-grey-darken-4">
          {{ partner.name }}
        </h2>
        <div class="partner-collab-type-tag mb-2 d-inline-block px-2.5 py-0.5 rounded-pill text-caption font-weight-bold">
          {{ partner.collabType || 'Mitra Kolaborasi' }}
        </div>
        <p class="text-body-2 text-grey-darken-2 mb-0 px-2 line-height-relaxed">
          {{ partner.role }}
        </p>
      </div>

      <!-- Collaboration Highlight Box (Connects to chapter & edition) -->
      <div v-if="collabInfo" class="collab-connection-card pa-3-5 mb-5">
        <div class="d-flex align-center justify-space-between mb-2">
          <div class="collab-eyebrow d-flex align-center ga-1-5">
            <v-icon size="14" color="#DC2626">mdi-handshake</v-icon>
            <span class="font-weight-black tracking-wider text-uppercase" style="font-size: 0.68rem; color: #DC2626;">
              RIWAYAT KOLABORASI
            </span>
          </div>
          <span class="collab-edition-pill">{{ collabInfo.edition }}</span>
        </div>

        <div class="collab-chapter-title mb-1 font-weight-bold text-grey-darken-4">
          {{ collabInfo.chapterName }}
        </div>
        <div class="collab-activity-desc text-caption text-grey-darken-2 mb-3">
          {{ collabInfo.title }}
        </div>

        <v-btn
          :to="collabInfo.url"
          block
          variant="flat"
          color="#FEF2F2"
          rounded="pill"
          class="collab-explore-btn font-weight-bold text-none"
          size="default"
          @click="$emit('update:modelValue', false)"
        >
          <v-icon start size="16" color="#DC2626">mdi-map-marker-path</v-icon>
          <span style="color: #DC2626; font-size: 0.84rem;">Buka Chapter &amp; Rute Terkait</span>
          <v-icon end size="14" color="#DC2626">mdi-arrow-right</v-icon>
        </v-btn>
      </div>

      <!-- Primary Close Button (Pill shape, full width) -->
      <v-btn
        block
        color="#DC2626"
        rounded="pill"
        variant="flat"
        size="large"
        class="font-weight-bold modal-main-close-btn text-none"
        @click="$emit('update:modelValue', false)"
      >
        Tutup
      </v-btn>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'

interface Partner {
  id: number
  name: string
  category: 'brand' | 'government' | 'bumn' | 'community'
  tier?: 'large' | 'medium' | 'small'
  role: string
  collabType: string
  initial: string
  logo?: string
  bgColor?: string
  textColor?: string
}

interface CollabInfo {
  chapterName: string
  edition: string
  title: string
  url: string
}

const props = defineProps<{
  modelValue: boolean
  partner: Partner | null
}>()

defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

const hasLogoError = ref(false)

watch(() => props.partner, () => {
  hasLogoError.value = false
})

const getCategoryLabel = (category: string) => {
  switch (category) {
    case 'brand': return 'Brand & Sponsor'
    case 'government': return 'Pemerintah & Kementerian'
    case 'bumn': return 'BUMN & Institusi'
    case 'community': return 'Komunitas & Media'
    default: return 'Mitra Kolaborasi'
  }
}

const getCategoryIcon = (category: string) => {
  switch (category) {
    case 'brand': return 'mdi-tag-outline'
    case 'government': return 'mdi-bank-outline'
    case 'bumn': return 'mdi-domain'
    case 'community': return 'mdi-account-group-outline'
    default: return 'mdi-handshake-outline'
  }
}

const collabInfo = computed<CollabInfo | null>(() => {
  if (!props.partner) return null
  const name = props.partner.name.toLowerCase()
  const role = props.partner.role.toLowerCase()

  // 1. Infinix Indonesia (Official co-creator for Edition #56)
  if (name.includes('infinix')) {
    return {
      chapterName: 'Jalan Bareng Makassar',
      edition: 'Edisi Kolaborasi #56',
      title: 'Mobile Photography Walk & Storytelling Tour Benteng Rotterdam',
      url: '/aktivasi/jalan-bareng-makassar'
    }
  }

  // 2. Palopo & Pemerintah Kota Palopo
  if (name.includes('palopo')) {
    return {
      chapterName: 'Jalan Bareng Palopo',
      edition: 'Edisi #12',
      title: 'Jelajah Pesisir Tanjung Ringgit & Ruang Terbuka Hijau Kota Idaman',
      url: '/aktivasi/jalan-bareng-palopo'
    }
  }

  // 3. Gowa & Benteng Somba Opu
  if (name.includes('gowa')) {
    return {
      chapterName: 'Jalan Bareng Kabupaten Gowa',
      edition: 'Edisi #08',
      title: 'Heritage Trail Cagar Budaya Benteng Somba Opu & Makam Katangka',
      url: '/aktivasi/jalan-bareng-gowa'
    }
  }

  // 4. Bone & Watampone
  if (name.includes('bone') || name.includes('watampone')) {
    return {
      chapterName: 'Jalan Bareng Kabupaten Bone',
      edition: 'Edisi #05',
      title: 'Jelajah Arsitektur Tradisional Bola Soba & Lapangan Merdeka',
      url: '/aktivasi/jalan-bareng-bone'
    }
  }

  // 5. Jakarta Selatan / Transit / Mobility
  if (name.includes('jakarta') || name.includes('grab') || name.includes('decathlon') || name.includes('mrt')) {
    return {
      chapterName: 'Jalan Bareng Jakarta Selatan',
      edition: 'Edisi #24',
      title: 'Urban Transit Walk & Jalur Pedestrian Terintegrasi Blok M',
      url: '/aktivasi/jalan-bareng-jaksel'
    }
  }

  // 6. Kuliner, F&B, Minuman, Buah (Makan Bareng)
  if (name.includes('kopi') || name.includes('hydro') || name.includes('sunpride') || name.includes('kuliner') || role.includes('hidrasi') || role.includes('buah')) {
    return {
      chapterName: 'Makan Bareng (Aktivasi Tematik)',
      edition: 'Aktivasi Tematik',
      title: 'Jalan Santai Pagi & Menjelajah Kuliner Khas Rasa Lokal',
      url: '/aktivasi/makan-bareng'
    }
  }

  // 7. Seni, Kriya, Desain (Djari Djemari & Creative Space)
  if (name.includes('seni') || name.includes('kriya') || name.includes('ikm') || name.includes('culture') || role.includes('kreatif')) {
    return {
      chapterName: 'Jalan Bareng Creative Space',
      edition: 'Ruang Kolaborasi',
      title: 'Lokakarya Kriya, Pameran Komunitas, & Diskusi Tata Kota',
      url: '/aktivasi/jalan-bareng-creative-space'
    }
  }

  // 8. Makassar Local Government, Dispar, Dispora, Wardah, Eiger
  if (name.includes('makassar') || name.includes('dispar') || name.includes('dispora') || name.includes('eiger') || name.includes('wardah') || name.includes('unhas') || name.includes('unm')) {
    return {
      chapterName: 'Jalan Bareng Makassar',
      edition: 'Edisi Rutin #111',
      title: 'Langkah Pagi Menyusuri Koridor Heritage & Anjungan Losari',
      url: '/aktivasi/jalan-bareng-makassar'
    }
  }

  // Default Fallback: Hub Utama Jalan Bareng
  return {
    chapterName: 'Jalan Bareng Makassar',
    edition: 'Edisi Komunitas',
    title: 'Aktivasi Jalan Kaki Tematik & Edukasi Ruang Publik Ramah Warga',
    url: '/aktivasi/jalan-bareng-makassar'
  }
})
</script>

<style scoped>
.partner-modal-card {
  background: #FFFFFF !important;
  border: 1px solid #F1F5F9;
  box-shadow: 0 24px 48px -12px rgba(15, 23, 42, 0.18), 0 8px 16px rgba(0, 0, 0, 0.06) !important;
}

/* Category Badge Chips */
.partner-category-chip {
  font-size: 0.74rem;
  letter-spacing: 0.02em;
}

.partner-category-chip.brand {
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FEE2E2;
}

.partner-category-chip.government {
  background: #F8FAFC;
  color: #334155;
  border: 1px solid #E2E8F0;
}

.partner-category-chip.bumn {
  background: #EFF6FF;
  color: #1D4ED8;
  border: 1px solid #DBEAFE;
}

.partner-category-chip.community {
  background: #F0FDF4;
  color: #15803D;
  border: 1px solid #DCFCE7;
}

.modal-close-icon-btn {
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  transition: all 0.2s ease;
}

.modal-close-icon-btn:hover {
  background: #F1F5F9;
  color: #0F172A !important;
}

/* Hero Logo Showcase Frame - Maximized width to match action button edges */
.partner-modal-logo-stage {
  width: 100%;
  height: 140px;
  background: #F8FAFC;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px 20px;
  box-sizing: border-box;
  overflow: hidden;
  transition: all 0.25s ease;
}

.modal-hero-logo {
  max-height: 105px;
  max-width: 95%;
  width: auto;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.05));
  transition: transform 0.25s ease;
}

.modal-hero-logo:hover {
  transform: scale(1.02);
}

.modal-hero-emblem {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border-radius: 14px;
}

.modal-emblem-initials {
  font-size: 2.2rem;
  font-weight: 900;
  letter-spacing: -0.02em;
  line-height: 1;
}

.modal-emblem-name {
  font-size: 0.85rem;
  font-weight: 700;
  max-width: 85%;
  opacity: 0.85;
}

/* Title & Info */
.partner-title-heading {
  font-size: 1.28rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  line-height: 1.25;
}

.partner-collab-type-tag {
  background: #F1F5F9;
  color: #475569;
  font-size: 0.72rem;
  letter-spacing: 0.02em;
}

.line-height-relaxed {
  line-height: 1.6;
}

/* Collab Connection Box */
.collab-connection-card {
  background: #FAFAF9;
  border: 1px solid #E7E5E4;
  border-radius: 18px;
  text-align: left;
}

.collab-edition-pill {
  font-size: 0.7rem;
  font-weight: 800;
  padding: 2px 10px;
  border-radius: 9999px;
  background: #FEF2F2;
  color: #DC2626;
  border: 1px solid #FECACA;
}

.collab-chapter-title {
  font-size: 0.95rem;
  line-height: 1.3;
}

.collab-activity-desc {
  line-height: 1.5;
}

.collab-explore-btn {
  border: 1px solid #FEE2E2 !important;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  height: 42px !important;
}

.collab-explore-btn:hover {
  background: #FEE2E2 !important;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.12) !important;
}

/* Close Button */
.modal-main-close-btn {
  height: 46px !important;
  letter-spacing: 0.01em;
  font-size: 0.95rem;
  transition: all 0.25s ease;
}

.modal-main-close-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.25) !important;
}
</style>
