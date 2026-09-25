<template>
  <div class="login-page">
    <div class="login-split-layout">
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
            <span>#MENELUSURIKOTA</span>
          </div>
        </div>

        <!-- Center Stage: Editorial Headline & Authentic Photo Collage -->
        <div class="collage-center-content my-auto py-2">
          <div class="collage-headline-block mb-3 mb-md-4">
            <h2 class="editorial-headline text-grey-darken-4 mb-2 mb-md-2.5">
              Menjelajah kota lebih dekat,<br>
              <span class="text-primary-red">berbagi cerita</span> lebih hangat.
            </h2>
            <p class="editorial-sublead text-grey-darken-2 mb-0" style="line-height: 1.6;">
              Ruang bersama untuk berjalan santai, merespon tata kota, dan menghubungkan kawan baru.
            </p>
          </div>

          <!-- Dynamic 3-Card Collage Composition (Inspired by EditorialCollageHero) -->
          <div class="collage-composition-wrapper">
            <!-- Center Main Hero Photo (Tall, Rounded) -->
            <div class="photo-card photo-card-main">
              <div class="photo-img-wrapper">
                <img
                  src="/images/hero/walk_3.jpg"
                  alt="Aktivitas Komunitas Jalan Bareng"
                  class="photo-img"
                />
                <div class="photo-caption-pill">
                  <span class="pulse-dot mr-1"></span>
                  <span>Jalan Santai Mingguan</span>
                </div>
              </div>
            </div>

            <!-- Side Column for Stacked Secondary Photos -->
            <div class="photo-side-column">
              <!-- Top Secondary Photo (Slight Tilt) -->
              <div class="photo-card photo-card-secondary-top">
                <div class="photo-img-wrapper">
                  <img
                    src="/images/hero/walk_1.jpg"
                    alt="Kawan Baru Jalan Bareng"
                    class="photo-img"
                  />
                  <div class="photo-badge-chip">
                    <span>✦ Kawan Baru</span>
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
              <span class="sticker-icon">👟</span>
              <span class="sticker-text">Ruang Bersama</span>
            </div>
            <div class="playful-sticker sticker-flower">
              <span>🌼</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Login Form -->
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
              Selamat Datang Kembali
            </h1>
            <p class="form-subtitle text-grey-darken-1 mb-0">
              Masuk untuk kelola agenda dan berjejaring bersama warga pejalan
            </p>
          </div>

          <!-- Main Login Card -->
          <div class="login-card pa-5 pa-md-7">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-6">
              <v-progress-circular indeterminate color="#DC2626" size="44" width="3.5"></v-progress-circular>
              <p class="mt-3 text-body-2 font-weight-medium text-grey-darken-2">Sedang memproses masuk...</p>
            </div>

            <!-- Login Form -->
            <v-form v-else ref="loginForm" v-model="formValid" @submit.prevent="handleLogin">
              <!-- Email Input -->
              <div class="input-group mb-3">
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

              <!-- Password Input -->
              <div class="input-group mb-4">
                <label class="input-label mb-1 d-block text-body-2 font-weight-bold text-grey-darken-3">
                  Password
                </label>
                <v-text-field
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Masukkan kata sandi"
                  variant="outlined"
                  density="comfortable"
                  prepend-inner-icon="mdi-lock-outline"
                  :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                  @click:append-inner="showPassword = !showPassword"
                  :rules="[rules.required]"
                  color="#DC2626"
                  hide-details="auto"
                  class="clean-field"
                ></v-text-field>
              </div>

              <!-- Submit Button (Solid Primary Red) -->
              <v-btn
                type="submit"
                block
                class="login-btn-primary mb-1"
                :loading="loading"
              >
                Masuk ke Akun
              </v-btn>

              <!-- Divider -->
              <div class="divider-line my-4">
                <span class="divider-text">atau</span>
              </div>

              <!-- Google Sign In Button -->
              <button
                type="button"
                class="google-oauth-btn w-100"
                @click="handleGoogleLogin"
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
                <span>Masuk dengan Google</span>
              </button>

              <!-- Footer CTA Link -->
              <div class="text-center mt-4 pt-3 border-top-subtle">
                <p class="footer-cta-text text-grey-darken-1 mb-0">
                  Belum punya akun?
                  <NuxtLink to="/register" class="register-link font-weight-bold ml-1">
                    Daftar sekarang
                  </NuxtLink>
                </p>
              </div>
            </v-form>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Snackbar -->
    <v-snackbar v-model="showError" color="#DC2626" :timeout="5000" location="top" rounded="pill">
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
import { definePageMeta, useSeoMeta, useApi, useAuthStore, useRouter, useRuntimeConfig, useCookie } from '#imports'

definePageMeta({
  layout: false,
  middleware: 'guest'
})

useSeoMeta({
  title: 'Masuk ke Akun - Jalan Bareng',
  ogTitle: 'Masuk ke Akun - Jalan Bareng',
  description: 'Masuk ke akun Jalan Bareng Anda untuk mulai berbagi rute, destinasi, dan mengikuti agenda jalan komunitas.',
  ogDescription: 'Masuk ke akun Jalan Bareng Anda untuk mulai berbagi rute, destinasi, dan mengikuti agenda jalan komunitas.',
  ogImage: 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const { api } = useApi()
const authStore = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()

const loginForm = ref<any>(null)
const formValid = ref(false)
const loading = ref(false)
const showError = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)

const email = ref('')
const password = ref('')

const rules = {
  required: (value: any) => !!value || 'Field ini wajib diisi',
  email: (value: string) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(value) || 'Format email tidak valid'
  }
}

const handleLogin = async () => {
  if (loginForm.value) {
    const { valid } = await loginForm.value.validate()
    if (!valid) return
  }

  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.post('/auth/login', {
      email: email.value,
      password: password.value
    })

    // Store token
    const token = response.data.token || response.data.access_token
    const tokenCookie = useCookie('auth_token', {
      maxAge: 60 * 60 * 24 * 7, // 7 days
      path: '/',
      sameSite: 'lax'
    })
    tokenCookie.value = token

    // Fetch user data
    await authStore.fetchUser()

    // Redirect to home
    router.push('/')
  } catch (error: any) {
    console.error('Login error:', error)
    errorMessage.value = error.response?.data?.message || 'Email atau password salah'
    showError.value = true
  } finally {
    loading.value = false
  }
}

const handleGoogleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    // Get Google Auth URL from backend
    const response = await api.get('/auth/google/url')

    // Redirect to Google OAuth
    window.location.href = response.data.url
  } catch (error: any) {
    console.error('Google login error:', error)
    errorMessage.value = error.response?.data?.message || 'Gagal menghubungkan dengan Google. Silakan coba lagi.'
    showError.value = true
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  height: 100vh;
  height: 100dvh;
  max-height: 100vh;
  max-height: 100dvh;
  overflow: hidden;
  background-color: #FAFAF9;
  font-family: inherit;
  display: flex;
  flex-direction: column;
}

.login-split-layout {
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
  padding: clamp(24px, 4vh, 44px) clamp(28px, 4vw, 54px);
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
  height: clamp(40px, 5vh, 52px);
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
  font-size: clamp(1.65rem, 2.4vw, 2.25rem);
  font-weight: 800;
  letter-spacing: -0.03em;
  line-height: 1.25;
}

.text-primary-red {
  color: #DC2626;
}

.editorial-sublead {
  font-size: clamp(0.95rem, 1.15vw, 1.05rem);
  line-height: 1.6;
  max-width: 520px;
}

/* 3-Card Collage Composition - Enlarged & Balanced */
.collage-composition-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
  position: relative;
  margin-top: clamp(18px, 3vh, 32px);
  max-width: 540px;
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
  width: clamp(230px, 20vw, 280px);
  height: clamp(270px, 39vh, 360px);
  flex-shrink: 0;
  border-radius: 24px;
}

.photo-side-column {
  display: flex;
  flex-direction: column;
  gap: 14px;
  flex-grow: 1;
}

.photo-card-secondary-top {
  height: clamp(128px, 18.5vh, 172px);
  border-radius: 20px;
  transform: rotate(2.5deg);
}

.photo-card-secondary-bottom {
  height: clamp(130px, 19vh, 174px);
  border-radius: 20px;
  transform: rotate(-2.5deg);
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
  font-size: 0.8rem;
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
  bottom: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  padding: 5px 10px;
  background: rgba(17, 24, 39, 0.85);
  backdrop-filter: blur(4px);
  border-radius: 9999px;
  color: #FFFFFF;
  font-size: 0.76rem;
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
  padding: 7px 16px;
  border-radius: 9999px;
  font-size: 0.84rem;
  font-weight: 800;
  color: #111827;
  transform: rotate(4deg);
}

.sticker-flower {
  bottom: -14px;
  left: 240px;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  justify-content: center;
  font-size: 1.3rem;
  transform: rotate(-10deg);
}

/* ============================================================
   RIGHT SIDE: LOGIN FORM (Desktop & Mobile)
   ============================================================ */
.form-side {
  flex: 1;
  height: 100%;
  background-color: #FAFAF9;
  padding: clamp(24px, 4vh, 48px) clamp(24px, 4vw, 52px);
  position: relative;
  overflow: hidden;
  box-sizing: border-box;
}

.top-nav-bar {
  max-width: 440px;
  margin: 0 auto clamp(8px, 1.8vh, 18px);
  flex-shrink: 0;
}

.back-link {
  display: inline-flex;
  align-items: center;
  color: #64748B;
  font-size: 0.88rem;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s ease;
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
  font-size: clamp(1.45rem, 2.2vw, 1.85rem);
  letter-spacing: -0.025em;
  line-height: 1.25;
}

.form-subtitle {
  font-size: 0.9rem;
  line-height: 1.5;
}

/* Clean Login Card */
.login-card {
  background: #FFFFFF;
  border: 1px solid #E2E8F0;
  border-radius: 22px;
  box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
}

.input-label {
  letter-spacing: 0.01em;
  font-size: 0.86rem !important;
}

:deep(.clean-field .v-field) {
  border-radius: 12px !important;
  background-color: #FAFAFA !important;
}

:deep(.clean-field .v-field--focused) {
  background-color: #FFFFFF !important;
}

:deep(.clean-field input) {
  font-size: 0.95rem !important;
  min-height: 46px !important;
  padding-top: 8px !important;
  padding-bottom: 8px !important;
}

:deep(.clean-field .v-icon) {
  font-size: 20px !important;
}

/* Action Buttons (High Contrast Solid Primary Red) */
.login-btn-primary {
  background-color: #DC2626 !important;
  color: #FFFFFF !important;
  border-radius: 9999px !important;
  font-weight: 700 !important;
  letter-spacing: 0.02em !important;
  height: 48px !important;
  box-shadow: 0 4px 14px rgba(220, 38, 38, 0.28) !important;
  text-transform: none !important;
  font-size: 0.98rem !important;
  opacity: 1 !important;
  transition: all 0.2s ease !important;
}

.login-btn-primary:hover {
  background-color: #B91C1C !important;
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(220, 38, 38, 0.35) !important;
}

.divider-line {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 1.35rem 0;
}

.divider-line::before,
.divider-line::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid #E2E8F0;
}

.divider-text {
  padding: 0 14px;
  font-size: 0.78rem;
  color: #64748B;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.google-oauth-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 48px;
  border: 1px solid #E2E8F0;
  border-radius: 9999px;
  background: #FFFFFF;
  color: #334155;
  font-size: 0.92rem;
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
  font-size: 0.9rem;
}

.register-link {
  color: #DC2626;
  text-decoration: none;
  font-size: 0.9rem;
}

.register-link:hover {
  text-decoration: underline;
}

/* ============================================================
   TABLET / IPAD RESPONSIVE (600px - 959px)
   ============================================================ */
@media (min-width: 600px) and (max-width: 959px) {
  .collage-side {
    flex: 1 1 48%;
    padding: clamp(14px, 2vh, 22px) clamp(16px, 2.5vw, 24px);
  }

  .brand-logo-desktop {
    height: 32px;
  }

  .hero-badge-pill {
    padding: 3px 9px;
    font-size: 0.68rem;
  }

  .editorial-headline {
    font-size: 1.15rem;
    line-height: 1.25;
  }

  .editorial-sublead {
    font-size: 0.74rem;
    line-height: 1.4;
  }

  .collage-composition-wrapper {
    gap: 10px;
    margin-top: 12px;
    max-width: 100%;
  }

  .photo-card-main {
    width: 155px;
    height: 220px;
    border-radius: 18px;
  }

  .photo-side-column {
    gap: 8px;
  }

  .photo-card-secondary-top {
    height: 102px;
    border-radius: 14px;
  }

  .photo-card-secondary-bottom {
    height: 104px;
    border-radius: 14px;
  }

  .sticker-move {
    top: -8px;
    right: 8px;
    font-size: 0.68rem;
    padding: 3px 8px;
  }

  .sticker-flower {
    bottom: -8px;
    left: 135px;
    width: 28px;
    height: 28px;
    font-size: 0.85rem;
  }

  .form-side {
    flex: 1 1 52%;
    padding: clamp(14px, 2vh, 22px) clamp(16px, 2.5vw, 24px);
  }

  .form-inner-container {
    max-width: 325px;
  }

  .form-title {
    font-size: 1.25rem;
  }

  .form-subtitle {
    font-size: 0.75rem;
  }

  .login-card {
    padding: 16px 14px !important;
  }
}

/* ============================================================
   MOBILE 390px COMPACT STANDARDS (< 600px)
   ============================================================ */
@media (max-width: 599px) {
  .login-page {
    height: 100vh;
    height: 100dvh;
    overflow-y: auto;
  }

  .form-side {
    height: auto;
    min-height: 100dvh;
    padding: 16px 14px 20px;
    justify-content: center;
  }

  .form-inner-container {
    max-width: 358px;
  }

  .top-nav-bar {
    margin-bottom: 10px;
  }

  .brand-logo-mobile {
    height: 36px;
  }

  .form-title {
    font-size: 1.25rem;
    line-height: 1.2;
  }

  .form-subtitle {
    font-size: 0.76rem;
    line-height: 1.4;
  }

  .login-card {
    border-radius: 16px;
    padding: 14px 14px !important;
  }
}
</style>


