<template>
  <div class="login-page">
    <v-container fluid class="fill-height pa-0">
      <v-row no-gutters class="fill-height">
        <!-- Left Side - Illustration (Hidden on mobile) -->
        <v-col cols="12" md="6" class="hidden-sm-and-down" style="position: relative;">
          <div class="illustration-side d-flex flex-column justify-center align-center" style="
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              height: 100%;
              position: relative;
              overflow: hidden;
            ">
            <!-- Animated Background Shapes -->
            <div class="bg-shapes">
              <div class="shape shape-1"></div>
              <div class="shape shape-2"></div>
              <div class="shape shape-3"></div>
            </div>

            <div class="text-center pa-8" style="position: relative; z-index: 2;">
              <v-icon size="120" color="white" class="mb-6">mdi-map-marker-multiple</v-icon>
              <h1 class="text-h3 text-white font-weight-bold mb-4">
                Selamat Datang di<br>
                Jalan Bareng Makassar
              </h1>
              <p class="text-h6 text-white" style="opacity: 0.95; max-width: 400px; margin: 0 auto;">
                Jelajahi destinasi menarik dan ikuti event seru bersama komunitas
              </p>
            </div>
          </div>
        </v-col>

        <!-- Right Side - Login Form -->
        <v-col cols="12" md="6" class="d-flex flex-column justify-center">
          <v-container class="py-12">
            <v-row justify="center">
              <v-col cols="12" sm="10" md="9" lg="8">
                <div class="text-center mb-8">
                  <!-- Logo for Mobile -->
                  <v-icon size="60" color="primary" class="mb-3 mobile-logo hidden-md-and-up">
                    mdi-map-marker-multiple
                  </v-icon>

                  <h2 class="text-h5 font-weight-bold mb-1 login-title">Masuk</h2>
                  <p class="text-body-2 text-grey-darken-1">
                    Masuk ke akun Anda untuk melanjutkan
                  </p>
                </div>

                <!-- Login Card -->
                <v-card elevation="2" rounded="xl" class="pa-5 pa-sm-8 login-card-box">
                  <!-- Loading State -->
                  <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                    <p class="mt-4 text-body-1">Sedang masuk...</p>
                  </div>

                  <!-- Login Form -->
                  <v-form v-else ref="loginForm" v-model="formValid" @submit.prevent="handleLogin">
                    <!-- Email/Password Login -->
                    <v-text-field v-model="email" label="Email" type="email" placeholder="nama@email.com"
                      variant="outlined" prepend-inner-icon="mdi-email" :rules="[rules.required, rules.email]"
                      color="primary" class="mb-4"></v-text-field>

                    <v-text-field v-model="password" label="Password" :type="showPassword ? 'text' : 'password'"
                      placeholder="Masukkan password" variant="outlined" prepend-inner-icon="mdi-lock"
                      :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                      @click:append-inner="showPassword = !showPassword" :rules="[rules.required]" color="primary"
                      class="mb-4"></v-text-field>

                    <v-btn type="submit" block size="x-large" color="primary" class="mb-4" :loading="loading"
                      :disabled="!formValid">
                      Masuk
                    </v-btn>

                    <v-divider class="my-6">
                      <span class="text-caption text-grey px-2">ATAU</span>
                    </v-divider>

                    <!-- Google Sign In Button -->
                    <v-btn block size="x-large" variant="outlined" class="google-btn" @click="handleGoogleLogin">
                      <v-icon start>
                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                            fill="#4285F4" />
                          <path
                            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                            fill="#34A853" />
                          <path
                            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                            fill="#FBBC05" />
                          <path
                            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                            fill="#EA4335" />
                        </svg>
                      </v-icon>
                      <span class="font-weight-medium ml-2">Masuk dengan Google</span>
                    </v-btn>

                    <v-divider class="my-6"></v-divider>

                    <p class="text-caption text-center text-grey-darken-1 mb-0">
                      Belum punya akun?
                      <NuxtLink to="/register" class="text-primary text-decoration-none font-weight-bold">
                        Daftar sekarang
                      </NuxtLink>
                    </p>
                  </v-form>
                </v-card>

                <div class="text-center mt-6">
                  <v-btn variant="text" color="primary" to="/" size="large">
                    <v-icon start>mdi-arrow-left</v-icon>
                    Kembali ke Beranda
                  </v-btn>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </v-col>
      </v-row>
    </v-container>

    <!-- Error Snackbar -->
    <v-snackbar v-model="showError" color="error" :timeout="5000" location="top">
      {{ errorMessage }}
      <template v-slot:actions>
        <v-btn variant="text" @click="showError = false">
          Tutup
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false,
  middleware: 'guest'
})

useSeoMeta({
  title: 'Login - Jalan Bareng',
  ogTitle: 'Login - Jalan Bareng',
  description: 'Masuk ke akun Jalan Bareng Anda untuk mulai berbagi destinasi dan mengikuti event.',
  ogDescription: 'Masuk ke akun Jalan Bareng Anda untuk mulai berbagi destinasi dan mengikuti event.',
  ogImage: 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const { api } = useApi()
const authStore = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()

const loginForm = ref(null)
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
    return pattern.test(value) || 'Email tidak valid'
  }
}

const handleLogin = async () => {
  if (!formValid.value) return

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
  min-height: 100vh;
  background: #f5f5f5;
}

.fill-height {
  min-height: 100vh;
}

.bg-shapes {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
}

.shape {
  position: absolute;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.1);
  animation: float 20s infinite ease-in-out;
}

.shape-1 {
  width: 300px;
  height: 300px;
  top: -100px;
  left: -100px;
  animation-delay: 0s;
}

.shape-2 {
  width: 200px;
  height: 200px;
  bottom: -50px;
  right: 100px;
  animation-delay: 5s;
}

.shape-3 {
  width: 150px;
  height: 150px;
  top: 50%;
  right: -50px;
  animation-delay: 10s;
}

@keyframes float {

  0%,
  100% {
    transform: translateY(0) translateX(0);
  }

  33% {
    transform: translateY(-30px) translateX(20px);
  }

  66% {
    transform: translateY(20px) translateX(-20px);
  }
}

.google-btn {
  border-width: 2px;
  text-transform: none;
  letter-spacing: normal;
}

.google-btn:hover {
  background-color: rgba(0, 0, 0, 0.04);
}
</style>
