<template>
  <v-navigation-drawer v-model="drawerModel" temporary fixed class="mobile-nav-drawer" width="300">
    <v-list class="pa-2">
      <!-- Main Navigation -->
      <v-list-subheader class="font-weight-bold text-uppercase text-caption mb-1">Menu Utama</v-list-subheader>

      <v-list-item to="/" rounded="lg" color="primary" class="mb-1">
        <template v-slot:prepend>
          <v-icon>mdi-home</v-icon>
        </template>
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>

      <v-list-group value="Aktivasi">
        <template v-slot:activator="{ props }">
          <v-list-item v-bind="props" prepend-icon="mdi-star-four-points" title="Aktivasi" rounded="lg"
            class="mb-1"></v-list-item>
        </template>

        <div class="bg-grey-lighten-4 rounded-lg mt-1 mb-2 overflow-hidden">
          <v-list-item v-for="activation in activations" :key="activation.id" :to="`/aktivasi/${activation.slug}`"
            :title="activation.name" class="pl-6" density="compact">
            <template v-slot:prepend>
              <v-icon size="small" :color="getActivationColor(activation.category)">mdi-circle-small</v-icon>
            </template>
          </v-list-item>

          <v-list-item to="/aktivasi" title="Lihat Semua" class="pl-6 text-primary font-weight-medium"
            density="compact">
            <template v-slot:prepend>
              <v-icon size="small" color="primary">mdi-arrow-right</v-icon>
            </template>
          </v-list-item>
        </div>
      </v-list-group>

      <v-list-item to="/events" prepend-icon="mdi-calendar" rounded="lg" class="mb-1">
        <v-list-item-title>Event</v-list-item-title>
      </v-list-item>

      <v-list-item to="/destinations" prepend-icon="mdi-map-marker-radius" rounded="lg" class="mb-1">
        <v-list-item-title>Destinasi &amp; Peta</v-list-item-title>
      </v-list-item>

      <v-list-item to="/cerita" prepend-icon="mdi-feather" rounded="lg" class="mb-1">
        <v-list-item-title>Cerita Jalan Bareng</v-list-item-title>
      </v-list-item>

      <v-list-item to="/mitra" prepend-icon="mdi-handshake-outline" rounded="lg" class="mb-1">
        <v-list-item-title>Mitra &amp; Kolaborator</v-list-item-title>
      </v-list-item>

      <!-- Admin & Management Menu -->
      <template v-if="authStore.isAdmin || authStore.isCommunityAdmin">
        <v-divider class="my-4"></v-divider>
        <v-list-subheader class="font-weight-bold text-uppercase text-caption mb-1 text-secondary">
          {{ authStore.isAdmin ? 'Admin Panel' : 'Panel Manajemen' }}
        </v-list-subheader>

        <v-list-item to="/manage/activations" rounded="lg" class="mb-1" color="secondary">
          <template v-slot:prepend>
            <v-icon color="purple">mdi-star-four-points</v-icon>
          </template>
          <v-list-item-title>Kelola Aktivasi</v-list-item-title>
        </v-list-item>

        <v-list-item to="/manage/events" rounded="lg" class="mb-1" color="secondary">
          <template v-slot:prepend>
            <v-icon color="secondary">mdi-calendar-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Event</v-list-item-title>
        </v-list-item>

        <v-list-item to="/manage/destinations" rounded="lg" class="mb-1" color="secondary">
          <template v-slot:prepend>
            <v-icon color="primary">mdi-map-marker-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Destinasi</v-list-item-title>
        </v-list-item>

        <template v-if="authStore.isAdmin">
          <v-list-item to="/manage/users" rounded="lg" class="mb-1" color="secondary">
            <template v-slot:prepend>
              <v-icon color="info">mdi-account-multiple</v-icon>
            </template>
            <v-list-item-title>Kelola User</v-list-item-title>
          </v-list-item>

          <v-list-item to="/manage/categories" rounded="lg" class="mb-1" color="secondary">
            <template v-slot:prepend>
              <v-icon color="warning">mdi-tag-multiple</v-icon>
            </template>
            <v-list-item-title>Kelola Kategori</v-list-item-title>
          </v-list-item>

          <v-list-item to="/manage/pages" rounded="lg" class="mb-1" color="secondary">
            <template v-slot:prepend>
              <v-icon color="teal">mdi-file-document-multiple</v-icon>
            </template>
            <v-list-item-title>Kelola Halaman</v-list-item-title>
          </v-list-item>

          <v-list-item to="/manage/stories" rounded="lg" class="mb-1" color="secondary">
            <template v-slot:prepend>
              <v-icon color="pink">mdi-feather</v-icon>
            </template>
            <v-list-item-title>Kurasi Tulisan</v-list-item-title>
          </v-list-item>

          <v-list-item to="/manage/settings" rounded="lg" class="mb-1" color="secondary">
            <template v-slot:prepend>
              <v-icon color="grey-darken-1">mdi-cog</v-icon>
            </template>
            <v-list-item-title>Pengaturan</v-list-item-title>
          </v-list-item>
        </template>
      </template>

      <!-- User Actions (if logged in) -->
      <template v-if="authStore.isLoggedIn">
        <v-divider class="my-4"></v-divider>
        <v-list-item to="/profile" rounded="lg" class="mb-1">
          <template v-slot:prepend>
            <v-icon>mdi-account</v-icon>
          </template>
          <v-list-item-title>Profil Saya</v-list-item-title>
        </v-list-item>

        <v-list-item @click="handleLogout" rounded="lg" class="mb-1">
          <template v-slot:prepend>
            <v-icon color="error">mdi-logout</v-icon>
          </template>
          <v-list-item-title class="text-error">Keluar</v-list-item-title>
        </v-list-item>
      </template>

      <!-- Login Button (if guest) -->
      <template v-else>
        <v-divider class="my-4"></v-divider>
        <v-list-item to="/login" variant="flat" color="primary" rounded="lg" class="mb-1 bg-primary text-white">
          <template v-slot:prepend>
            <v-icon color="white">mdi-login</v-icon>
          </template>
          <v-list-item-title class="font-weight-bold">Masuk / Daftar</v-list-item-title>
        </v-list-item>
      </template>

    </v-list>
  </v-navigation-drawer>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from '~/stores/auth'

interface Props {
  modelValue: boolean
}

const props = defineProps<Props>()
const emit = defineEmits(['update:modelValue'])

const drawerModel = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const api = useApi()
const authStore = useAuthStore()
const router = useRouter()

const activations = ref<any[]>([])

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
    drawerModel.value = false
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

const getActivationColor = (category: string) => {
  const colors: Record<string, string> = {
    city: 'primary',
    theme: 'purple',
    space: 'pink',
    other: 'grey'
  }
  return colors[category] || colors.other
}

onMounted(() => {
  fetchActivations()
})
</script>

<style scoped>
.mobile-nav-drawer {
  transition: transform 0.3s ease-in-out;
}
</style>