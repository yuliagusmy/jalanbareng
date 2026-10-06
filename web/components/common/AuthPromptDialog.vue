<template>
  <v-dialog
    v-model="isAuthPromptOpen"
    max-width="460"
    :transition="$vuetify.display.smAndDown ? 'dialog-bottom-transition' : 'dialog-transition'"
    class="auth-prompt-dialog"
  >
    <v-card :rounded="$vuetify.display.smAndDown ? 't-xl b-0' : 'xl'" class="pa-5 pa-sm-6 text-center auth-prompt-card">
      <!-- Icon Avatar -->
      <div class="auth-icon-circle mx-auto mb-4">
        <v-icon size="32" color="#DC2626">mdi-account-heart-outline</v-icon>
      </div>

      <!-- Title & Headline -->
      <h3 class="text-h6 font-weight-black text-grey-darken-4 mb-2">
        {{ authPromptTitle }}
      </h3>

      <p class="text-body-2 text-grey-darken-1 mb-4" style="line-height: 1.55;">
        {{ authPromptMessage }}
      </p>

      <!-- Benefits Box -->
      <div class="benefits-box pa-3.5 rounded-xl text-left mb-5">
        <div class="d-flex align-center mb-2">
          <v-icon size="16" color="#DC2626" class="mr-2.5 flex-shrink-0">mdi-feather</v-icon>
          <span class="text-caption font-weight-medium text-grey-darken-3">Kirim cerita, riset, dan foto jelajahmu</span>
        </div>
        <div class="d-flex align-center mb-2">
          <v-icon size="16" color="#DC2626" class="mr-2.5 flex-shrink-0">mdi-heart-outline</v-icon>
          <span class="text-caption font-weight-medium text-grey-darken-3">Apresiasi tulisan dan tanggapi kawan pejalan</span>
        </div>
        <div class="d-flex align-center">
          <v-icon size="16" color="#DC2626" class="mr-2.5 flex-shrink-0">mdi-map-marker-plus-outline</v-icon>
          <span class="text-caption font-weight-medium text-grey-darken-3">Bagikan rekomendasi rute & landmark lorong</span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="d-flex flex-column ga-2.5">
        <v-btn
          color="#DC2626"
          size="large"
          rounded="pill"
          elevation="0"
          class="font-weight-bold text-white text-none"
          @click="goToLogin"
        >
          <v-icon start size="18">mdi-login</v-icon>
          Masuk ke Akun
        </v-btn>

        <v-btn
          variant="outlined"
          color="grey-darken-2"
          size="large"
          rounded="pill"
          class="font-weight-bold text-none"
          @click="goToRegister"
        >
          Daftar Akun Baru
        </v-btn>

        <v-btn
          variant="text"
          density="comfortable"
          class="text-grey-darken-1 text-caption text-none mt-1"
          @click="closeAuthPrompt"
        >
          Nanti Saja, Lanjut Melihat-lihat
        </v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { useAuthGuard } from '~/composables/useAuthGuard'

const {
  isAuthPromptOpen,
  authPromptTitle,
  authPromptMessage,
  closeAuthPrompt,
  goToLogin,
  goToRegister,
} = useAuthGuard()
</script>

<style scoped>
.auth-prompt-card {
  position: relative;
  border: 1px solid #f1f5f9;
  box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.12) !important;
}

.auth-icon-circle {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #fef2f2;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #fee2e2;
}

.benefits-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
}
</style>
