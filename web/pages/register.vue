<template>
  <div class="register-page">
    <div class="register-split-layout">
      <!-- Left Side - Editorial Community Collage (Tablet & Desktop, White/Light Theme) -->
      <div class="collage-side d-none d-sm-flex flex-column justify-space-between">
        <!-- Top Branding -->
        <div class="collage-header d-flex align-center justify-space-between">
          <NuxtLink to="/" class="brand-link" title="Kembali ke Beranda">
            <img
              src="/images/logo-jalan-bareng.png"
              alt="Jalan Bareng"
              class="brand-logo-desktop"
            />
          </NuxtLink>
          <div class="hero-badge-pill">
            <v-icon size="14" color="#DC2626" class="mr-1">mdi-foot-print</v-icon>
            <span>#menelusurikotalebihlambat</span>
          </div>
        </div>

        <!-- Center Stage: Editorial Headline & Authentic Photo Collage -->
        <div class="collage-center-content my-auto py-2">
          <div class="collage-headline-block mb-3 mb-md-4">
            <h2 class="editorial-headline text-grey-darken-4 mb-2 mb-md-2.5">
              Langkah awal dimulai,<br>
              <span class="text-primary-red">jelajahi kota</span> bersama kami.
            </h2>
            <p class="editorial-sublead text-grey-darken-2 mb-0" style="line-height: 1.6;">
              Bergabung dengan ribuan warga Makassar untuk jalan santai mingguan, eksplorasi sudut kota, dan merawat ruang bersama.
            </p>
          </div>

          <!-- Dynamic 3-Card Collage Composition -->
          <div class="collage-composition-wrapper">
            <!-- Center Main Hero Photo (Tall, Rounded) -->
            <div class="photo-card photo-card-main">
              <div class="photo-img-wrapper">
                <img
                  src="/images/hero/walk_4.jpg"
                  alt="Komunitas Jalan Bareng Makassar"
                  class="photo-img"
                />
                <div class="photo-caption-pill">
                  <span class="pulse-dot mr-1"></span>
                  <span>100% Terbuka & Gratis</span>
                </div>
              </div>
            </div>

            <!-- Side Column for Stacked Secondary Photos -->
            <div class="photo-side-column">
              <!-- Top Secondary Photo -->
              <div class="photo-card photo-card-secondary-top">
                <div class="photo-img-wrapper">
                  <img
                    src="/images/hero/walk_2.jpg"
                    alt="Kawan Baru Jalan Bareng"
                    class="photo-img"
                  />
                  <div class="photo-badge-chip">
                    <span>👟 Kawan Baru</span>
                  </div>
                </div>
              </div>

              <!-- Bottom Secondary Photo -->
              <div class="photo-card photo-card-secondary-bottom">
                <div class="photo-img-wrapper">
                  <img
                    src="/images/hero/walk_6.jpg"
                    alt="Eksplorasi Sudut Kota"
                    class="photo-img"
                  />
                  <div class="photo-badge-chip">
                    <v-icon size="12" color="#DC2626" class="mr-0.5">mdi-map-marker</v-icon>
                    <span>Ruang Publik</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Playful Editorial Stickers -->
            <div class="playful-sticker sticker-move">
              <span class="sticker-icon">✨</span>
              <span class="sticker-text">Warga Pejalan</span>
            </div>
            <div class="playful-sticker sticker-flower">
              <span>🌿</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Register Form -->
      <div class="form-side d-flex flex-column justify-center align-center">
        <!-- Top Back Nav -->
        <div class="top-nav-bar w-100 d-flex justify-space-between align-center">
          <NuxtLink to="/" class="back-link">
            <v-icon size="18" class="mr-1">mdi-arrow-left</v-icon>
            <span>Kembali ke Beranda</span>
          </NuxtLink>
        </div>

        <div class="form-inner-container w-100 my-auto">
          <!-- Mobile Brand Logo (Only shown on mobile <600px) -->
          <div class="text-center d-block d-sm-none mb-3">
            <NuxtLink to="/" class="d-inline-block">
              <img
                src="/images/logo-jalan-bareng.png"
                alt="Jalan Bareng"
                class="brand-logo-mobile"
              />
            </NuxtLink>
          </div>

          <!-- Form Header -->
          <div class="mb-3 mb-sm-4 text-center text-sm-left">
            <h1 class="form-title font-weight-black text-grey-darken-4 mb-0.5">
              Daftar Sekarang
            </h1>
            <p class="form-subtitle text-grey-darken-1 mb-0">
              Buat akun gratis untuk bergabung dengan komunitas jalan kaki
            </p>
          </div>

          <!-- Main Register Card -->
          <div class="register-card pa-5 pa-md-6">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-6">
              <v-progress-circular indeterminate color="#DC2626" size="44" width="3.5"></v-progress-circular>
              <p class="mt-3 text-body-2 font-weight-medium text-grey-darken-2">Sedang memproses pendaftaran...</p>
            </div>

            <!-- Register Form -->
            <div v-else>
              <!-- Google Sign In Button -->
              <button
                type="button"
                class="google-oauth-btn w-100"
                @click="handleGoogleRegister"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="mr-2">
                  <path
                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    fill="#4285F4"
                  />
                  <path
                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    fill="#34A853"
                  />
                  <path
                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    fill="#FBBC05"
                  />
                  <path
                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    fill="#EA4335"
                  />
                </svg>
                <span>Daftar dengan Google</span>
              </button>

              <!-- Divider -->
              <div class="divider-line my-3">
                <span class="divider-text">atau daftar dengan email</span>
              </div>

              <v-form ref="registerForm" v-model="formValid" @submit.prevent="handleRegister">
                <!-- Nama Lengkap -->
                <div class="input-group mb-2.5">
                  <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                    Nama Lengkap
                  </label>
                  <v-text-field
                    v-model="name"
                    type="text"
                    placeholder="Contoh: Rahmat Hidayat"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-account-outline"
                    :rules="[rules.required]"
                    color="#DC2626"
                    hide-details="auto"
                    class="clean-field"
                  ></v-text-field>
                </div>

                <!-- Email Input -->
                <div class="input-group mb-2.5">
                  <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                    Email
                  </label>
                  <v-text-field
                    v-model="email"
                    type="email"
                    placeholder="nama@email.com"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-email-outline"
                    :rules="[rules.required, rules.email]"
                    color="#DC2626"
                    hide-details="auto"
                    class="clean-field"
                  ></v-text-field>
                </div>

                <!-- No. WhatsApp / HP Input (Opsional) -->
                <div class="input-group mb-2.5">
                  <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                    No. WhatsApp / HP <span class="text-grey-darken-1 font-weight-regular text-caption">(Opsional)</span>
                  </label>
                  <v-text-field
                    v-model="phone"
                    type="tel"
                    placeholder="Contoh: 081234567890"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-whatsapp"
                    color="#DC2626"
                    hide-details="auto"
                    class="clean-field"
                  ></v-text-field>
                </div>

                <!-- Password Input -->
                <div class="input-group mb-2.5">
                  <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                    Kata Sandi
                  </label>
                  <v-text-field
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                    placeholder="Minimal 8 karakter"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-lock-outline"
                    :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    @click:append-inner="showPassword = !showPassword"
                    :rules="[rules.required, rules.minLength]"
                    color="#DC2626"
                    hide-details="auto"
                    class="clean-field"
                  ></v-text-field>
                </div>

                <!-- Konfirmasi Password Input -->
                <div class="input-group mb-3">
                  <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                    Konfirmasi Kata Sandi
                  </label>
                  <v-text-field
                    v-model="passwordConfirmation"
                    :type="showPasswordConfirmation ? 'text' : 'password'"
                    placeholder="Ulangi kata sandi"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-lock-check-outline"
                    :append-inner-icon="showPasswordConfirmation ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                    @click:append-inner="showPasswordConfirmation = !showPasswordConfirmation"
                    :rules="[rules.required, rules.passwordMatch]"
                    color="#DC2626"
                    hide-details="auto"
                    class="clean-field"
                  ></v-text-field>
                </div>

                <!-- Submit Button (Solid Primary Red) -->
                <v-btn
                  type="submit"
                  block
                  class="register-btn-primary mb-2"
                  :loading="loading"
                >
                  Daftar Akun Baru
                </v-btn>

                <!-- Terms Notice -->
                <p class="text-caption text-center text-grey-darken-1 mb-0" style="line-height: 1.4;">
                  Dengan mendaftar, Anda menyetujui
                  <span class="text-primary-red font-weight-medium">Syarat Komunitas</span>
                  dan
                  <span class="text-primary-red font-weight-medium">Kebijakan Privasi</span>.
                </p>

                <!-- Footer CTA Link -->
                <div class="text-center mt-3 pt-2.5 border-top-subtle">
                  <p class="footer-cta-text text-grey-darken-1 mb-0">
                    Sudah punya akun?
                    <NuxtLink to="/login" class="login-link font-weight-bold ml-1">
                      Masuk sekarang
                    </NuxtLink>
                  </p>
                </div>
              </v-form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Snackbar -->
    <v-snackbar v-model="showError" color="#DC2626" :timeout="6000" location="top" rounded="pill">
      <div class="d-flex align-center">
        <v-icon start size="18">mdi-alert-circle</v-icon>
        <span>{{ errorMessage }}</span>
      </div>
      <template v-slot:actions>
        <v-btn variant="text" color="white" size="small" @click="showError = false">
          Tutup
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { definePageMeta, useSeoMeta, useApi, useAuthStore, useRouter, useCookie } from '#imports'

definePageMeta({
  layout: false,
  middleware: 'guest'
})

useSeoMeta({
  title: 'Daftar - Jalan Bareng',
  ogTitle: 'Daftar - Jalan Bareng',
  description: 'Daftar sekarang untuk bergabung dengan komunitas Jalan Bareng Makassar, berbagi destinasi, dan mengikuti event seru.',
  ogDescription: 'Daftar sekarang untuk bergabung dengan komunitas Jalan Bareng Makassar, berbagi destinasi, dan mengikuti event seru.',
  ogImage: '/images/hero/walk_3.jpg',
  twitterCard: 'summary_large_image',
})

const { api } = useApi()
const authStore = useAuthStore()
const router = useRouter()

const registerForm = ref<any>(null)
const formValid = ref(false)
const loading = ref(false)
const showError = ref(false)
const errorMessage = ref('')

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const rules = {
  required: (value: any) => !!value || 'Field ini wajib diisi',
  email: (value: string) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Format email tidak valid'
  },
  minLength: (value: string) => (value && value.length >= 8) || 'Kata sandi minimal 8 karakter',
  passwordMatch: (value: string) => value === password.value || 'Konfirmasi kata sandi tidak cocok'
}

const handleRegister = async () => {
  if (registerForm.value) {
    const { valid } = await registerForm.value.validate()
    if (!valid) return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const payload: Record<string, any> = {
      name: name.value.trim(),
      email: email.value.trim(),
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    }
    if (phone.value && phone.value.trim()) {
      payload.phone = phone.value.trim()
    }

    const response = await api.post('/auth/register', payload)

    // Store token if returned by backend
    const token = response.data?.token || response.data?.access_token
    if (token) {
      const tokenCookie = useCookie('auth_token', {
        maxAge: 60 * 60 * 24 * 7, // 7 days
        path: '/',
        sameSite: 'lax'
      })
      tokenCookie.value = token
      await authStore.fetchUser()
      router.push('/')
    } else {
      router.push('/login?registered=1')
    }
  } catch (error: any) {
    console.error('Registration error:', error)
    const res = error.response?.data
    if (res?.errors) {
      const firstKey = Object.keys(res.errors)[0]
      const firstVal = res.errors[firstKey]
      errorMessage.value = Array.isArray(firstVal) ? firstVal[0] : String(firstVal)
    } else {
      errorMessage.value = res?.message || 'Gagal mendaftar. Silakan periksa kembali data Anda.'
    }
    showError.value = true
  } finally {
    loading.value = false
  }
}

const handleGoogleRegister = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const origin = typeof window !== 'undefined' ? window.location.origin : ''
    const response = await api.get('/auth/google/url', { params: { origin } })
    if (response.data?.url) {
      window.location.href = response.data.url
    } else {
      throw new Error('URL otorisasi Google tidak tersedia.')
    }
  } catch (error: any) {
    console.error('Google register error:', error)
    errorMessage.value =
      error.response?.data?.message ||
      'Layanan Google Sign-In sedang disiapkan di server. Anda dapat mendaftar langsung dengan mengisi form di bawah.'
    showError.value = true
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* ============================================================
   REGISTER PAGE: EDITORIAL LIGHT THEME SPLIT LAYOUT
   ============================================================ */
.register-page {
  height: 100dvh;
  max-height: 100dvh;
  overflow: hidden;
  background-color: #FAFAF9;
  font-family: inherit;
  display: flex;
  flex-direction: column;
}

.register-split-layout {
  display: flex;
  height: 100%;
  width: 100%;
  overflow: hidden;
}

/* ============================================================
   LEFT SIDE: EDITORIAL COMMUNITY COLLAGE (Desktop, Light Background)
   ============================================================ */
.collage-side {
  flex: 1.15;
  height: 100%;
  background-color: #FFFFFF;
  border-right: 1px solid #E5E7EB;
  padding: clamp(20px, 3.5vh, 40px) clamp(24px, 3.5vw, 48px);
  overflow: hidden;
  position: relative;
  box-sizing: border-box;
}

.collage-header {
  position: relative;
  z-index: 2;
  flex-shrink: 0;
}

.brand-logo-desktop {
  height: clamp(38px, 4.5vh, 48px);
  width: auto;
  object-fit: contain;
  transition: transform 0.2s ease;
}

.brand-logo-desktop:hover {
  transform: scale(1.02);
}

.hero-badge-pill {
  display: inline-flex;
  align-items: center;
  padding: 6px 14px;
  border-radius: 9999px;
  background: #FEF2F2;
  border: 1px solid #FEE2E2;
  color: #DC2626;
  font-size: 0.8rem;
  font-weight: 800;
  letter-spacing: 0.04em;
}

/* Center Editorial Content */
.collage-center-content {
  position: relative;
  z-index: 2;
  margin: auto 0;
}

.editorial-headline {
  font-size: clamp(1.5rem, 2.2vw, 2.1rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.25;
}

.text-primary-red {
  color: #DC2626;
}

.editorial-sublead {
  font-size: clamp(0.9rem, 1.1vw, 1rem);
  line-height: 1.6;
  max-width: 500px;
}

/* 3-Card Collage Composition */
.collage-composition-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  margin-top: clamp(16px, 2.5vh, 28px);
  max-width: 520px;
}

.photo-card {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  background: #FFFFFF;
  border: 4px solid #FFFFFF;
  box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.12), 0 4px 12px rgba(0, 0, 0, 0.04);
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
  cursor: pointer;
}

.photo-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 22px 44px -10px rgba(0, 0, 0, 0.16);
}

.photo-card-main {
  width: clamp(220px, 19vw, 260px);
  height: clamp(250px, 36vh, 330px);
  flex-shrink: 0;
  border-radius: 24px;
}

.photo-side-column {
  display: flex;
  flex-direction: column;
  gap: 12px;
  flex-grow: 1;
}

.photo-card-secondary-top {
  height: clamp(120px, 17vh, 158px);
  border-radius: 18px;
  transform: rotate(2deg);
}

.photo-card-secondary-bottom {
  height: clamp(120px, 17vh, 160px);
  border-radius: 18px;
  transform: rotate(-2deg);
}

.photo-img-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: inherit;
}

.photo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.photo-caption-pill {
  position: absolute;
  bottom: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(6px);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.78rem;
  font-weight: 700;
}

.pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #10B981;
}

.photo-badge-chip {
  position: absolute;
  bottom: 10px;
  left: 10px;
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(4px);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.74rem;
  font-weight: 700;
}

/* Playful Editorial Stickers */
.playful-sticker {
  position: absolute;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #FFFFFF;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  border: 1px solid #F1F5F9;
  z-index: 4;
}

.sticker-move {
  top: -14px;
  right: 20px;
  padding: 6px 14px;
  border-radius: 9999px;
  font-size: 0.82rem;
  font-weight: 800;
  color: #111827;
  transform: rotate(4deg);
}

.sticker-flower {
  bottom: -14px;
  left: 220px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  justify-content: center;
  font-size: 1.15rem;
  transform: rotate(-6deg);
}

/* ============================================================
   RIGHT SIDE: REGISTER FORM (Desktop & Mobile)
   ============================================================ */
.form-side {
  flex: 1;
  height: 100%;
  background-color: #FAFAF9;
  padding: clamp(16px, 3vh, 36px) clamp(20px, 3vw, 44px);
  position: relative;
  overflow-y: auto;
  box-sizing: border-box;
}

.top-nav-bar {
  max-width: 440px;
  margin: 0 auto clamp(6px, 1.2vh, 12px);
}

.back-link {
  display: inline-flex;
  align-items: center;
  color: #64748B;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  transition: color 0.15s ease;
}

.back-link:hover {
  color: #DC2626;
}

.form-inner-container {
  max-width: 440px;
  margin: auto auto;
}

.brand-logo-mobile {
  height: 38px;
  width: auto;
  object-fit: contain;
}

.form-title {
  font-size: clamp(1.4rem, 2vw, 1.75rem);
  letter-spacing: -0.025em;
  line-height: 1.2;
}

.form-subtitle {
  font-size: 0.88rem;
  line-height: 1.45;
}

/* Clean Register Card */
.register-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 20px;
  box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
}

.input-label {
  letter-spacing: 0.01em;
  font-size: 0.84rem !important;
}

:deep(.clean-field .v-field) {
  border-radius: 12px !important;
  background-color: #FAFAFA !important;
}

:deep(.clean-field .v-field--focused) {
  background-color: #FFFFFF !important;
}

:deep(.clean-field input) {
  font-size: 16px !important;
  min-height: 42px !important;
  padding-top: 6px !important;
  padding-bottom: 6px !important;
}

@media screen and (max-width: 768px) {
  :deep(input),
  :deep(.v-field input),
  :deep(.clean-field input) {
    font-size: 16px !important;
  }
}

:deep(.clean-field .v-icon) {
  font-size: 19px !important;
}

/* Action Buttons (High Contrast Solid Primary Red) */
.register-btn-primary {
  background-color: #DC2626 !important;
  color: #FFFFFF !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
  letter-spacing: 0.02em !important;
  height: 46px !important;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.28) !important;
  text-transform: none !important;
  font-size: 0.95rem !important;
  opacity: 1 !important;
  transition: all 0.2s ease !important;
}

.register-btn-primary:hover {
  background-color: #B91C1C !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(220, 38, 38, 0.35) !important;
}

.divider-line {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1rem 0;
}

.divider-line::before,
.divider-line::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #E2E8F0;
}

.divider-text {
  padding: 0 12px;
  font-size: 0.74rem;
  color: #64748B;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.google-oauth-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 46px;
  border: 1px solid #E2E8F0;
  border-radius: 9999px;
  background: #FFFFFF;
  color: #334155;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.google-oauth-btn:hover {
  background: #F8FAFC;
  border-color: #CBD5E1;
  color: #0F172A;
  transform: translateY(-1px);
}

.border-top-subtle {
  border-top: 1px solid #F1F5F9;
}

.footer-cta-text {
  font-size: 0.88rem;
}

.login-link {
  color: #DC2626;
  text-decoration: none;
  font-size: 0.88rem;
}

.login-link:hover {
  text-decoration: underline;
}

/* ============================================================
   TABLET RESPONSIVE (600px - 959px)
   ============================================================ */
@media (min-width: 600px) and (max-width: 959px) {
  .collage-side {
    flex: 1 1 45%;
    padding: 20px;
  }

  .editorial-headline {
    font-size: 1.35rem;
  }

  .editorial-sublead {
    font-size: 0.82rem;
  }

  .photo-card-main {
    width: 155px;
    height: 215px;
  }

  .photo-card-secondary-top,
  .photo-card-secondary-bottom {
    height: 98px;
  }

  .playful-sticker {
    display: none !important;
  }

  .form-side {
    flex: 1 1 55%;
    padding: 16px 20px;
  }

  .form-inner-container {
    max-width: 340px;
  }
}

/* ============================================================
   MOBILE RESPONSIVE (<600px)
   ============================================================ */
@media (max-width: 599.98px) {
  .register-page {
    height: auto;
    min-height: 100dvh;
    overflow-y: auto;
  }

  .register-split-layout {
    height: auto;
    min-height: 100dvh;
    overflow-y: auto;
  }

  .form-side {
    height: auto;
    min-height: 100dvh;
    padding: 16px 14px 24px;
    justify-content: center;
  }

  .form-inner-container {
    max-width: 360px;
  }

  .top-nav-bar {
    margin-bottom: 8px;
  }

  .register-card {
    padding: 18px 14px !important;
  }
}
</style>
