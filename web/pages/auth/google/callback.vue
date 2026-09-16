<template>
  <div class="callback-page d-flex align-center justify-center" style="min-height: 100vh; background: #f5f5f5;">
    <v-container>
      <v-row justify="center">
        <v-col cols="12" sm="8" md="6" lg="4">
          <v-card elevation="8" rounded="xl" class="pa-8 text-center">
            <v-progress-circular
              v-if="!error"
              indeterminate
              color="primary"
              size="64"
              class="mb-4"
            ></v-progress-circular>

            <v-icon v-else size="64" color="error" class="mb-4">
              mdi-alert-circle
            </v-icon>

            <h2 class="text-h5 font-weight-bold mb-3">
              {{ error ? 'Login Gagal' : 'Sedang Memproses Login' }}
            </h2>

            <p class="text-body-1 text-grey-darken-1 mb-4">
              {{ error ? errorMessage : 'Mohon tunggu, kami sedang menghubungkan akun Google Anda...' }}
            </p>

            <v-btn
              v-if="error"
              color="primary"
              size="large"
              to="/login"
              rounded="pill"
            >
              Coba Lagi
            </v-btn>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false
})

const { api } = useApi()
const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const error = ref(false)
const errorMessage = ref('')

onMounted(async () => {
  // Jika sudah login, redirect ke home
  if (authStore.isLoggedIn) {
    await router.push('/')
    return
  }

  // Get token or error from query params (dari backend redirect)
  const token = route.query.token
  const errorParam = route.query.error

  // Check for error
  if (errorParam) {
    error.value = true
    errorMessage.value = errorParam as string
    return
  }

  // Check for token
  if (!token) {
    error.value = true
    errorMessage.value = 'Token autentikasi tidak ditemukan. Silakan coba login lagi.'
    return
  }

  try {
    // Save token
    authStore.setToken(token as string)

    // Fetch user data
    await authStore.fetchUser()

    // Redirect to home
    setTimeout(() => {
      router.push('/')
    }, 500)

  } catch (err: any) {
    console.error('OAuth callback error:', err)
    error.value = true
    errorMessage.value = err.response?.data?.message || err.message || 'Terjadi kesalahan saat login. Silakan coba lagi.'
  }
})
</script>

<style scoped>
.callback-page {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
