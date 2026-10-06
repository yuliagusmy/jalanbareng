<template>
  <div class="mitra-page">
    <!-- Hero Section -->
    <section class="mitra-hero">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" md="8" class="text-center">
            <div class="hero-badge-pill mb-4 d-inline-flex align-center ga-2">
              <v-icon size="14" color="#DC2626">mdi-handshake-outline</v-icon>
              <span>EKOSISTEM KOLABORASI</span>
            </div>
            <h1 class="hero-title font-weight-black text-grey-darken-4 mb-4">
              Mitra &amp; Kolaborator<br>
              <span class="text-primary">Jalan Bareng</span>
            </h1>
            <p class="hero-subtitle text-grey-darken-1 mx-auto">
              Bersama brand, instansi pemerintah, komunitas, dan media partner yang mendukung gerakan pejalan kaki dan ruang publik kota Indonesia.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- Main Content -->
    <v-container class="py-8 py-md-14">

      <!-- Loading State -->
      <div v-if="loading">
        <v-row dense class="mb-8">
          <v-col v-for="i in 8" :key="i" cols="6" sm="4" md="3">
            <v-skeleton-loader type="card" height="120" rounded="xl" />
          </v-col>
        </v-row>
      </div>

      <!-- Empty State -->
      <div v-else-if="allPartners.length === 0" class="text-center py-16">
        <v-icon size="64" color="grey-lighten-1" class="mb-3">mdi-handshake-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-1">Belum ada mitra yang ditampilkan</h3>
        <p class="text-caption text-grey">Data mitra sedang dalam proses pembaruan.</p>
      </div>

      <!-- Partner Groups by Category -->
      <template v-else>
        <div
          v-for="group in partnerGroups"
          :key="group.key"
          class="partner-group mb-10 mb-md-16"
        >
          <!-- Group Header -->
          <div class="d-flex align-center ga-3 mb-6">
            <div class="group-line" aria-hidden="true"></div>
            <div class="d-flex align-center ga-2">
              <v-avatar :color="group.color" size="32" class="flex-shrink-0">
                <v-icon size="16" color="white">{{ group.icon }}</v-icon>
              </v-avatar>
              <h2 class="text-h6 text-md-h5 font-weight-bold text-grey-darken-4 mb-0">{{ group.label }}</h2>
              <v-chip size="x-small" :color="group.color" variant="tonal" class="font-weight-bold">
                {{ group.partners.length }}
              </v-chip>
            </div>
          </div>

          <!-- Partner Cards Grid -->
          <v-row dense>
            <v-col
              v-for="partner in group.partners"
              :key="partner.id"
              cols="6"
              sm="4"
              md="3"
              lg="2"
            >
              <component
                :is="partner.website_url ? 'a' : 'div'"
                :href="partner.website_url || undefined"
                target="_blank"
                rel="noopener noreferrer"
                class="partner-card text-decoration-none"
                :class="{ 'has-link': !!partner.website_url }"
                :aria-label="partner.website_url ? `Kunjungi website ${partner.name}` : partner.name"
              >
                <!-- Logo / Initial -->
                <div
                  class="partner-logo-wrap"
                  :style="{ backgroundColor: partner.bg_color || '#F3F4F6' }"
                >
                  <img
                    v-if="partner.logo_url"
                    :src="partner.logo_url"
                    :alt="partner.name"
                    class="partner-logo-img"
                    loading="lazy"
                  />
                  <span
                    v-else
                    class="partner-initial font-weight-black"
                    :style="{ color: partner.text_color || '#111827' }"
                    aria-hidden="true"
                  >
                    {{ partner.initial || partner.name.charAt(0) }}
                  </span>
                </div>

                <!-- Name + Role -->
                <div class="partner-info">
                  <div class="partner-name">{{ partner.name }}</div>
                  <div v-if="partner.role" class="partner-role">{{ partner.role }}</div>
                </div>

                <!-- External link indicator -->
                <v-icon
                  v-if="partner.website_url"
                  size="12"
                  color="grey-lighten-1"
                  class="partner-ext-icon"
                  aria-hidden="true"
                >
                  mdi-open-in-new
                </v-icon>
              </component>
            </v-col>
          </v-row>
        </div>
      </template>

      <!-- CTA Section — Become a Partner -->
      <div class="become-partner-cta mt-4 mt-md-8 pa-6 pa-sm-8 pa-md-12 text-center rounded-2xl">
        <v-icon size="40" color="#DC2626" class="mb-3">mdi-handshake-outline</v-icon>
        <h2 class="text-h5 text-md-h4 font-weight-bold text-grey-darken-4 mb-3">
          Ingin Berkolaborasi dengan Kami?
        </h2>
        <p class="text-body-2 text-md-body-1 text-grey-darken-1 mx-auto mb-6" style="max-width: 560px; line-height: 1.7;">
          Jalan Bareng terbuka untuk kolaborasi dengan brand, instansi pemerintah, komunitas, dan media yang sejalan dengan semangat gerakan pejalan kaki dan ruang publik kota Indonesia.
        </p>
        <v-btn
          href="https://instagram.com/jalanbarengind"
          target="_blank"
          rel="noopener noreferrer"
          color="#DC2626"
          size="large"
          rounded="pill"
          elevation="0"
          class="font-weight-bold text-white px-8"
        >
          <v-icon start size="18">mdi-instagram</v-icon>
          Hubungi Kami via Instagram
        </v-btn>
      </div>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'

useSeoMeta({
  title: 'Mitra & Kolaborator — Jalan Bareng',
  description: 'Brand, instansi pemerintah, komunitas, dan media partner yang mendukung gerakan pejalan kaki bersama Jalan Bareng.',
})

definePageMeta({ layout: 'default' })

const { api } = useApi()
const allPartners = ref<any[]>([])
const loading = ref(true)

const categoryMeta: Record<string, { label: string; icon: string; color: string; order: number }> = {
  brand:      { label: 'Brand & Korporat',         icon: 'mdi-store-outline',           color: 'purple',  order: 1 },
  government: { label: 'Instansi Pemerintah',       icon: 'mdi-bank-outline',            color: 'blue',    order: 2 },
  bumn:       { label: 'BUMN',                      icon: 'mdi-domain',                  color: 'teal',    order: 3 },
  community:  { label: 'Komunitas & Organisasi',    icon: 'mdi-account-group-outline',   color: 'orange',  order: 4 },
  media:      { label: 'Media Partner',             icon: 'mdi-newspaper-variant-outline', color: 'pink',  order: 5 },
}

// Group partners by category, only show groups that have data
const partnerGroups = computed(() => {
  const groups: Record<string, any[]> = {}
  allPartners.value.forEach(p => {
    if (!groups[p.category]) groups[p.category] = []
    groups[p.category].push(p)
  })

  return Object.entries(groups)
    .map(([key, partners]) => ({
      key,
      partners,
      ...(categoryMeta[key] ?? { label: key, icon: 'mdi-handshake-outline', color: 'grey', order: 99 }),
    }))
    .sort((a, b) => a.order - b.order)
})

const fetchPartners = async () => {
  loading.value = true
  try {
    const res = await api.get('/partners')
    allPartners.value = res.data.data || []
  } catch {
    allPartners.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchPartners())
</script>

<style scoped>
/* Hero */
.mitra-hero {
  background: #FAFAF9;
  border-bottom: 1px solid #F1F5F9;
  padding: 56px 0 48px;
}

.hero-badge-pill {
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
  font-size: clamp(2.2rem, 4vw, 3.2rem);
  letter-spacing: -0.03em;
  line-height: 1.15;
}

.hero-subtitle {
  font-size: 1.05rem;
  line-height: 1.65;
  max-width: 600px;
}

/* Group header */
.group-line {
  width: 40px;
  height: 3px;
  background: #DC2626;
  border-radius: 9999px;
  flex-shrink: 0;
}

/* Partner card */
.partner-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 16px 12px;
  border-radius: 16px;
  border: 1px solid #F1F5F9;
  background: #FFFFFF;
  transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
  cursor: default;
  height: 100%;
  position: relative;
}

.partner-card.has-link {
  cursor: pointer;
}

.partner-card.has-link:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.08);
  border-color: #FECACA;
}

.partner-card.has-link:focus-visible {
  outline: 2px solid #DC2626;
  outline-offset: 2px;
  border-radius: 16px;
}

.partner-logo-wrap {
  width: 64px;
  height: 64px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin-bottom: 10px;
  flex-shrink: 0;
}

.partner-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 6px;
}

.partner-initial {
  font-size: 1.4rem;
  line-height: 1;
  user-select: none;
}

.partner-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
}

.partner-name {
  font-size: 0.8rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.3;
  word-break: break-word;
}

.partner-role {
  font-size: 0.68rem;
  color: #6B7280;
  font-weight: 500;
  line-height: 1.3;
}

.partner-ext-icon {
  position: absolute;
  top: 8px;
  right: 8px;
}

/* CTA Section */
.become-partner-cta {
  background: linear-gradient(135deg, #FEF2F2 0%, #FAFAF9 60%, #F0FDF4 100%);
  border: 1px solid #FECACA;
}

.rounded-2xl {
  border-radius: 24px;
}

/* Mobile tweaks */
@media (max-width: 600px) {
  .mitra-hero {
    padding: 40px 0 32px;
  }

  .partner-logo-wrap {
    width: 52px;
    height: 52px;
    border-radius: 12px;
  }

  .partner-initial {
    font-size: 1.1rem;
  }

  .partner-card {
    padding: 12px 8px;
  }

  .partner-name {
    font-size: 0.72rem;
  }

  .partner-role {
    font-size: 0.62rem;
  }
}

@media (prefers-reduced-motion: reduce) {
  .partner-card {
    transition: none;
  }
}
</style>
