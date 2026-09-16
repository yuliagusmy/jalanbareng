<template>
  <div class="register-page">
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
              <v-icon size="120" color="white" class="mb-6">mdi-account-group</v-icon>
              <h1 class="text-h3 text-white font-weight-bold mb-4">
                Bergabung dengan<br>
                Komunitas Jalan Bareng
              </h1>
              <p class="text-h6 text-white" style="opacity: 0.95; max-width: 400px; margin: 0 auto;">
                Jelajahi destinasi menarik dan ikuti event seru bersama komunitas Makassar
              </p>
            </div>
          </div>
        </v-col>

        <!-- Right Side - Register Form -->
        <v-col cols="12" md="6" class="d-flex flex-column justify-center">
          <v-container class="py-12">
            <v-row justify="center">
              <v-col cols="12" sm="10" md="9" lg="8">
                <div class="text-center mb-8">
                  <!-- Logo for Mobile -->
                  <v-icon size="80" color="primary" class="mb-4 hidden-md-and-up">
                    mdi-account-group
                  </v-icon>

                  <h2 class="text-h4 font-weight-bold mb-2">Daftar Sekarang</h2>
                  <p class="text-body-1 text-grey-darken-1">
                    Daftar dengan akun Google Anda untuk bergabung dengan komunitas
                  </p>
                </div>

                <!-- Google Sign In Card -->
                <v-card elevation="2" rounded="xl" class="pa-8">
                  <!-- Loading State -->
                  <div v-if="loading" class="text-center py-12">
                    <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                    <p class="mt-4 text-body-1">Menghubungkan dengan Google...</p>
                  </div>

                  <!-- Register Form -->
                  <div v-else>
                    <!-- Benefits -->
                    <div class="mb-6">
                      <h4 class="text-subtitle-1 font-weight-bold mb-3">Keuntungan Bergabung:</h4>
                      <v-list density="compact" class="bg-transparent">
                        <v-list-item prepend-icon="mdi-map-marker-plus" class="px-0">
                          <v-list-item-title>Bagikan destinasi favorit Anda</v-list-item-title>
                        </v-list-item>
                        <v-list-item prepend-icon="mdi-calendar-star" class="px-0">
                          <v-list-item-title>Ikuti event jalan kaki seru</v-list-item-title>
                        </v-list-item>
                        <v-list-item prepend-icon="mdi-account-multiple" class="px-0">
                          <v-list-item-title>Terhubung dengan komunitas lokal</v-list-item-title>
                        </v-list-item>
                        <v-list-item prepend-icon="mdi-camera" class="px-0">
                          <v-list-item-title>Bagikan foto dan pengalaman</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </div>

                    <v-divider class="my-6"></v-divider>

                    <!-- Google Sign In Button -->
                    <v-btn block size="x-large" variant="outlined" class="mb-4 google-btn" @click="handleGoogleRegister"
                      :loading="loading">
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
                      <span class="font-weight-medium ml-2">Daftar dengan Google</span>
                    </v-btn>

                    <v-divider class="my-6"></v-divider>

                    <p class="text-caption text-center text-grey-darken-1">
                      Dengan mendaftar, Anda menyetujui
                      <a href="#" class="text-primary text-decoration-none">Syarat & Ketentuan</a>
                      dan
                      <a href="#" class="text-primary text-decoration-none">Kebijakan Privasi</a>
                    </p>
                  </div>
                </v-card>

                <div class="text-center mt-6">
                  <span class="text-body-2">Sudah punya akun? </span>
                  <v-btn variant="text" color="primary" to="/login" size="large">
                    Masuk sekarang
                  </v-btn>
                </div>

                <div class="text-center mt-4">
                  <v-btn variant="text" color="grey" to="/" size="large">
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
  title: 'Daftar - Jalan Bareng',
  ogTitle: 'Daftar - Jalan Bareng',
  description: 'Daftar sekarang untuk bergabung dengan komunitas Jalan Bareng, berbagi destinasi, dan mengikuti event seru.',
  ogDescription: 'Daftar sekarang untuk bergabung dengan komunitas Jalan Bareng, berbagi destinasi, dan mengikuti event seru.',
  ogImage: 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const { api } = useApi()
const authStore = useAuthStore()
const router = useRouter()

const loading = ref(false)
const showError = ref(false)
const errorMessage = ref('')

const handleGoogleRegister = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    // Get Google Auth URL from backend
    const response = await api.get('/auth/google/url')

    // Redirect to Google OAuth
    window.location.href = response.data.url

  } catch (error: any) {
    console.error('Google register error:', error)
    errorMessage.value = error.response?.data?.message || 'Gagal menghubungkan dengan Google. Silakan coba lagi.'
    showError.value = true
    loading.value = false
  }
}
</script>

<style scoped>
.register-page {
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
