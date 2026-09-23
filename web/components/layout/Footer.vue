<template>
  <v-footer class="modern-footer pa-0">
    <div class="footer-wrapper w-100">
      <v-container class="py-12 py-md-16">
        <!-- 2. MAIN 4-COLUMN NAVIGATION -->
        <v-row justify="space-between" class="footer-main-nav">
          <!-- Col 1: Brand & Bio -->
          <v-col cols="12" md="4" class="mb-8 mb-md-0">
            <NuxtLink to="/" class="text-decoration-none d-inline-block mb-4">
              <img
                src="/images/logo-jalan-bareng-white.png"
                alt="Jalan Bareng"
                style="height: 46px; width: auto; object-fit: contain;"
              />
            </NuxtLink>

            <p class="text-grey-lighten-1 mb-4" style="line-height: 1.7; font-size: 14.5px; max-width: 320px;">
              {{ brandDescription }}
            </p>

            <div class="city-origin-badge d-inline-flex align-center ga-2 px-3 py-1-5 rounded-lg mb-6">
              <span class="pulse-origin-dot"></span>
              <span class="text-caption text-grey-lighten-2 font-weight-medium">
                Berakar di Makassar • Tumbuh untuk Indonesia
              </span>
            </div>

            <!-- Social Media Handles -->
            <div class="d-flex align-center ga-2">
              <v-btn
                v-if="settings.footer_social_instagram || true"
                icon
                variant="outlined"
                color="grey-lighten-2"
                class="social-btn"
                size="small"
                href="https://instagram.com/jalanbarengind"
                target="_blank"
                rel="noopener noreferrer"
                title="Instagram @jalanbarengind"
              >
                <v-icon size="18">mdi-instagram</v-icon>
              </v-btn>

              <v-btn
                v-if="settings.footer_social_facebook"
                icon
                variant="outlined"
                color="grey-lighten-2"
                class="social-btn"
                size="small"
                :href="settings.footer_social_facebook"
                target="_blank"
                rel="noopener noreferrer"
                title="Facebook"
              >
                <v-icon size="18">mdi-facebook</v-icon>
              </v-btn>

              <v-btn
                v-if="settings.footer_social_twitter"
                icon
                variant="outlined"
                color="grey-lighten-2"
                class="social-btn"
                size="small"
                :href="settings.footer_social_twitter"
                target="_blank"
                rel="noopener noreferrer"
                title="Twitter / X"
              >
                <v-icon size="18">mdi-twitter</v-icon>
              </v-btn>

              <v-btn
                icon
                variant="outlined"
                color="grey-lighten-2"
                class="social-btn"
                size="small"
                href="https://tiktok.com"
                target="_blank"
                rel="noopener noreferrer"
                title="TikTok"
              >
                <v-icon size="18">mdi-music-note</v-icon>
              </v-btn>
            </div>
          </v-col>

          <!-- Col 2: Aktivasi & Agenda -->
          <v-col cols="6" sm="4" md="2" class="mb-6 mb-md-0">
            <h4 class="nav-section-title text-white font-weight-bold mb-4">
              Aktivasi
            </h4>
            <div class="footer-links">
              <NuxtLink to="/aktivasi/jalan-bareng-makassar" class="footer-link">
                Jalan Bareng Makassar
              </NuxtLink>
              <NuxtLink to="/aktivasi/diskusi-buku-bareng" class="footer-link">
                Diskusi Buku Bareng
              </NuxtLink>
              <NuxtLink to="/aktivasi/makan-bareng" class="footer-link">
                Makan Bareng
              </NuxtLink>
              <NuxtLink to="/aktivasi/explore-bareng" class="footer-link">
                Explore Bareng
              </NuxtLink>
              <NuxtLink to="/#registrasi" class="footer-link highlighted-link">
                <v-icon size="14" class="mr-1" color="#FACC15">mdi-ticket-confirmation-outline</v-icon>
                Daftar Pekan Ini
              </NuxtLink>
            </div>
          </v-col>

          <!-- Col 3: Eksplorasi & Publikasi -->
          <v-col cols="6" sm="4" md="3" class="mb-6 mb-md-0">
            <h4 class="nav-section-title text-white font-weight-bold mb-4">
              Eksplorasi
            </h4>
            <div class="footer-links">
              <NuxtLink to="/cerita" class="footer-link">
                Cerita Jalan Bareng
              </NuxtLink>
              <NuxtLink to="/destinations" class="footer-link">
                Direktori Destinasi
              </NuxtLink>
              <NuxtLink to="/events" class="footer-link">
                Agenda & Event
              </NuxtLink>
              <NuxtLink to="/destinations?view=map" class="footer-link">
                Peta Destinasi Interaktif
              </NuxtLink>
              <NuxtLink to="/cerita" class="footer-link">
                Kirim Tulisan Cerita
              </NuxtLink>
            </div>
          </v-col>

          <!-- Col 4: Komunitas & Regulasi (Halaman yang diminta diperbagus) -->
          <v-col cols="12" sm="4" md="3">
            <h4 class="nav-section-title text-white font-weight-bold mb-4">
              Komunitas
            </h4>
            <div class="footer-links">
              <NuxtLink to="/profile" class="footer-link">
                <v-icon size="14" class="mr-1">mdi-account-circle-outline</v-icon>
                Profil Pejalan
              </NuxtLink>
              <NuxtLink to="/pages/tentang-kami" class="footer-link">
                Tentang Kami
              </NuxtLink>
              <NuxtLink to="/mitra" class="footer-link">
                <v-icon size="14" class="mr-1">mdi-handshake-outline</v-icon>
                Mitra &amp; Kolaborator
              </NuxtLink>
              <NuxtLink to="/pages/panduan-komunitas" class="footer-link">
                Panduan & Etika Komunitas
              </NuxtLink>
              <NuxtLink to="/pages/faq" class="footer-link">
                Tanya Jawab (FAQ)
              </NuxtLink>
              <NuxtLink to="/pages/syarat-ketentuan" class="footer-link">
                Syarat & Ketentuan
              </NuxtLink>
              <NuxtLink to="/pages/kebijakan-privasi" class="footer-link">
                Kebijakan Privasi
              </NuxtLink>
            </div>
          </v-col>
        </v-row>

        <v-divider class="my-8 my-md-10" style="border-color: rgba(255, 255, 255, 0.1);"></v-divider>

        <!-- 3. BOTTOM COPYRIGHT & ATTRIBUTION -->
        <div class="d-flex flex-column flex-md-row justify-space-between align-center ga-4 text-caption text-grey-lighten-1">
          <div class="text-center text-md-left">
            <span>© {{ new Date().getFullYear() }} <strong>Jalan Bareng</strong>. Ruang bersama pejalan kaki.</span>
          </div>

          <div class="text-center">
            <span>Dibuat dengan <span style="color: #EF4444;">❤</span> oleh teman-teman pejalan untuk ruang kota yang lebih ramah & setara.</span>
          </div>

          <div class="text-center text-md-right">
            <button class="back-to-top-btn d-inline-flex align-center ga-1" @click="scrollToTop">
              <span>Kembali ke atas</span>
              <v-icon size="14">mdi-arrow-up</v-icon>
            </button>
          </div>
        </div>
      </v-container>
    </div>
  </v-footer>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const api = useApi()

const settings = ref<any>({})
const pages = ref<any[]>([])

const fetchFooterData = async () => {
  try {
    const [settingsRes, pagesRes] = await Promise.all([
      api.get('/settings/footer'),
      api.get('/pages/footer')
    ])

    settings.value = settingsRes.data?.settings || {}
    pages.value = pagesRes.data?.pages || []
  } catch (error) {
    console.warn('Using default footer configuration')
  }
}

const brandDescription = computed(() => {
  return settings.value.footer_brand_description ||
    'Inisiatif komunitas terbuka pejalan kaki untuk melambatkan langkah, mengamati tata kota, dan menghubungkan kawan baru di setiap trotoar.'
})

const scrollToTop = () => {
  if (typeof window !== 'undefined') {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(() => {
  fetchFooterData()
})
</script>

<style scoped>
.modern-footer {
  position: relative;
  overflow: hidden;
  border: none !important;
  background: #0B0F19;
}

.footer-wrapper {
  background: radial-gradient(circle at 85% 15%, rgba(37, 99, 235, 0.12) 0%, transparent 50%),
              radial-gradient(circle at 15% 90%, rgba(220, 38, 38, 0.08) 0%, transparent 45%),
              #0B0F19;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  position: relative;
}

/* Top Community Callout Banner */
.footer-community-banner {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%);
  border: 1px solid rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  box-shadow: 0 16px 32px -8px rgba(0, 0, 0, 0.35);
}

.footer-banner-title {
  line-height: 1.25 !important;
  word-break: break-word;
}

.footer-banner-btn {
  white-space: nowrap !important;
}

.community-tag {
  background: rgba(220, 38, 38, 0.2);
  color: #F87171;
  border: 1px solid rgba(239, 68, 68, 0.3);
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.instagram-cta-btn {
  letter-spacing: 0.02em;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.instagram-cta-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
}

.city-origin-badge {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.pulse-origin-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #10B981;
  box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
  animation: pulse-green 2s infinite;
}

@keyframes pulse-green {
  0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
  70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
  100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

.nav-section-title {
  font-size: 0.95rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #F1F5F9 !important;
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.footer-link {
  color: #94A3B8;
  text-decoration: none;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
}

.footer-link:hover {
  color: #FFFFFF;
  transform: translateX(4px);
}

.highlighted-link {
  color: #FACC15;
  font-weight: 600;
}

.highlighted-link:hover {
  color: #FEF08A;
}

.social-btn {
  border-color: rgba(255, 255, 255, 0.15) !important;
  background: rgba(255, 255, 255, 0.03);
  transition: all 0.25s ease;
}

.social-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 0.4) !important;
  transform: translateY(-2px);
  color: #FFFFFF !important;
}

.back-to-top-btn {
  background: none;
  border: none;
  color: #94A3B8;
  cursor: pointer;
  transition: color 0.2s ease;
  padding: 4px 8px;
  border-radius: 6px;
}

.back-to-top-btn:hover {
  color: #FFFFFF;
}
</style>
