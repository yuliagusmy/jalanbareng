<template>
  <template v-if="!$vuetify.display.mobile">
    <v-btn to="/" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg">
      Home
    </v-btn>

    <v-menu offset-y transition="slide-y-transition">
      <template v-slot:activator="{ props }">
        <v-btn v-bind="props" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg"
          append-icon="mdi-chevron-down">
          Aktivasi
        </v-btn>
      </template>
      <v-card min-width="320" max-width="400" class="py-2" rounded="lg" elevation="4">
        <v-list density="compact">
          <!-- City Activations -->
          <div v-if="groupedActivations.city.length > 0">
            <v-list-subheader class="font-weight-bold text-primary text-caption text-uppercase">Aktivasi
              Kota</v-list-subheader>
            <v-list-item v-for="activation in groupedActivations.city" :key="activation.id"
              :to="`/aktivasi/${activation.slug}`" :title="activation.name" :subtitle="activation.tagline" lines="two"
              rounded="lg" class="mx-2 mb-1">
              <template v-slot:prepend>
                <v-icon size="small" color="primary" class="mr-2">mdi-city</v-icon>
              </template>
            </v-list-item>
          </div>

          <!-- Theme Activations -->
          <div v-if="groupedActivations.theme.length > 0">
            <v-divider class="my-2" v-if="groupedActivations.city.length > 0"></v-divider>
            <v-list-subheader class="font-weight-bold text-purple text-caption text-uppercase">Aktivasi
              Tematik</v-list-subheader>
            <v-list-item v-for="activation in groupedActivations.theme" :key="activation.id"
              :to="`/aktivasi/${activation.slug}`" :title="activation.name" :subtitle="activation.tagline" lines="two"
              rounded="lg" class="mx-2 mb-1">
              <template v-slot:prepend>
                <v-icon size="small" color="purple" class="mr-2">mdi-shape</v-icon>
              </template>
            </v-list-item>
          </div>

          <!-- Space Activations -->
          <div v-if="groupedActivations.space.length > 0">
            <v-divider class="my-2"
              v-if="groupedActivations.theme.length > 0 || groupedActivations.city.length > 0"></v-divider>
            <v-list-subheader class="font-weight-bold text-pink text-caption text-uppercase">Ruang & Program
              Khusus</v-list-subheader>
            <v-list-item v-for="activation in groupedActivations.space" :key="activation.id"
              :to="`/aktivasi/${activation.slug}`" :title="activation.name" :subtitle="activation.tagline" lines="two"
              rounded="lg" class="mx-2 mb-1">
              <template v-slot:prepend>
                <v-icon size="small" color="pink" class="mr-2">mdi-store</v-icon>
              </template>
            </v-list-item>
          </div>

          <v-divider class="my-2"></v-divider>
          <v-list-item to="/aktivasi" class="mx-2" rounded="lg">
            <template v-slot:prepend>
              <v-icon>mdi-arrow-right</v-icon>
            </template>
            <v-list-item-title>Lihat Semua Aktivasi</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-card>
    </v-menu>

    <v-btn to="/events" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg">
      Event
    </v-btn>
    <v-btn to="/destinations" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg">
      Destinasi
    </v-btn>
    <v-btn to="/cerita" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg">
      Cerita
    </v-btn>
    <v-btn to="/map" variant="text" :color="textColor" class="mx-1 nav-link" rounded="lg">
      Peta
    </v-btn>

    <!-- Admin Menu -->
    <v-menu v-if="authStore.isAdmin" offset-y>
      <template v-slot:activator="{ props }">
        <v-btn v-bind="props" variant="text" :color="adminColor" class="mx-1 nav-link" rounded="lg">
          <v-icon start>mdi-shield-crown</v-icon>
          Admin
          <v-icon end>mdi-chevron-down</v-icon>
        </v-btn>
      </template>
      <v-list min-width="220" rounded="xl" class="py-2">
        <v-list-item to="/manage/activations" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="purple">mdi-star-four-points</v-icon>
          </template>
          <v-list-item-title>Kelola Aktivasi</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua aktivasi</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/events" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="secondary">mdi-calendar-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Event</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua event</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/destinations" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="primary">mdi-map-marker-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Destinasi</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua destinasi</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/users" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="info">mdi-account-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola User</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage semua user</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/categories" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="warning">mdi-tag-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Kategori</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage kategori</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/pages" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="teal">mdi-file-document-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Halaman</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Manage halaman statis</v-list-item-subtitle>
        </v-list-item>

        <v-list-item to="/manage/stories" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="pink">mdi-feather</v-icon>
          </template>
          <v-list-item-title>Kurasi Tulisan</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Moderasi cerita teman jalan</v-list-item-subtitle>
        </v-list-item>

        <v-divider class="my-2"></v-divider>

        <v-list-item to="/manage/settings" rounded="lg" class="mx-2">
          <template v-slot:prepend>
            <v-icon color="grey-darken-1">mdi-cog</v-icon>
          </template>
          <v-list-item-title>Pengaturan</v-list-item-title>
          <v-list-item-subtitle class="text-caption">Pengaturan website</v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-menu>
  </template>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'

interface Props {
  textColor?: string
  adminColor?: string
}

const props = withDefaults(defineProps<Props>(), {
  textColor: 'primary',
  adminColor: 'secondary'
})

const authStore = useAuthStore()
const api = useApi()
const activations = ref<any[]>([])

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

const groupedActivations = computed(() => {
  const grouped: Record<string, any[]> = {
    city: [],
    theme: [],
    space: [],
    other: []
  }

  activations.value.forEach(a => {
    if (a.is_active) {
      if (grouped[a.category]) {
        grouped[a.category].push(a)
      } else {
        grouped.other.push(a)
      }
    }
  })

  return grouped
})

onMounted(() => {
  fetchActivations()
})
</script>

<style scoped>
.nav-link {
  text-transform: none !important;
  font-weight: 500;
  letter-spacing: 0.3px;
  transition: all 0.2s ease;
}
</style>
