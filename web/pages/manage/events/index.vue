<template>
  <div class="admin-events-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <div class="d-flex align-center justify-space-between flex-wrap ga-3 px-4 px-sm-6 py-4">
          <div class="d-flex align-center">
            <v-icon class="mr-3" size="32" color="primary">mdi-calendar-multiple</v-icon>
            <div>
              <h1 class="text-h6 text-sm-h5 font-weight-bold">Kelola Event</h1>
              <p class="text-caption text-grey mb-0">Manajemen event komunitas</p>
            </div>
          </div>
          <v-btn color="primary" variant="flat" rounded="lg" to="/events/create" :size="$vuetify.display.xs ? 'default' : 'large'">
            <v-icon start>mdi-plus</v-icon>
            <span v-if="!$vuetify.display.xs">Buat Event Baru</span>
            <span v-else>Buat Event</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="6" sm="6" md="3" v-for="stat in stats" :key="stat.label">
          <v-card elevation="0" rounded="lg" class="pa-3 pa-sm-4" :color="stat.bgColor">
            <div class="d-flex align-center">
              <v-icon :size="$vuetify.display.xs ? 28 : 40" :color="stat.color" class="mr-3 mr-sm-4">{{ stat.icon }}</v-icon>
              <div>
                <div class="text-h5 text-sm-h4 font-weight-bold" :class="`text-${stat.color}`">{{ stat.value }}</div>
                <div class="text-caption text-grey-darken-1">{{ stat.label }}</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
        <v-row>
          <v-col cols="12" sm="6" md="3">
            <v-text-field v-model="search" placeholder="Cari event..." prepend-inner-icon="mdi-magnify"
              variant="outlined" rounded="lg" density="comfortable" clearable hide-details></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-select v-model="filterActivation" :items="activations" item-title="name" item-value="id"
              placeholder="Semua Aktivasi" variant="outlined" rounded="lg" density="comfortable" clearable hide-details>
              <template v-slot:prepend-inner>
                <v-icon>mdi-star-circle</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-select v-model="filterType" :items="eventTypes" placeholder="Semua Jenis" variant="outlined" rounded="lg"
              density="comfortable" clearable hide-details>
              <template v-slot:prepend-inner>
                <v-icon>mdi-filter</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-select v-model="filterStatus" :items="statusOptions" placeholder="Semua Status" variant="outlined"
              rounded="lg" density="comfortable" clearable hide-details>
              <template v-slot:prepend-inner>
                <v-icon>mdi-calendar-clock</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="12" md="3">
            <v-select v-model="sortBy" :items="sortOptions" item-title="label" item-value="value" variant="outlined"
              rounded="lg" density="comfortable" hide-details>
              <template v-slot:prepend-inner>
                <v-icon>mdi-sort</v-icon>
              </template>
            </v-select>
          </v-col>
        </v-row>
      </v-card>

      <!-- Events Table -->
      <v-card elevation="0" rounded="lg" style="overflow-x: auto;">
        <v-data-table :headers="headers" :items="filteredEvents" :loading="loading" :search="search" hide-default-footer
          class="elevation-0" style="min-width: 640px;">
          <!-- Poster Column -->
          <template v-slot:item.poster="{ item }">
            <v-avatar size="60" rounded="lg" class="my-2">
              <v-img :src="getImageUrl(item.poster)" cover></v-img>
            </v-avatar>
          </template>

          <!-- Name Column -->
          <template v-slot:item.name="{ item }">
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <v-chip :color="item.type === 'walking' ? 'success' : 'primary'" size="x-small" variant="tonal"
                class="mt-1">
                {{ item.type === 'walking' ? 'Jalan Kaki' : 'Regular' }}
              </v-chip>
            </div>
          </template>

          <!-- Date Column -->
          <template v-slot:item.date="{ item }">
            <div class="text-caption">{{ formatDate(item.date) }}</div>
          </template>

          <!-- Status Column -->
          <template v-slot:item.status="{ item }">
            <v-chip :color="getStatusColor(item.date)" size="small">
              {{ getStatusLabel(item.date) }}
            </v-chip>
          </template>

          <!-- Actions Column -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn icon size="small" variant="text" color="primary" :to="`/events/${item.id}`">
                <v-icon size="small">mdi-eye</v-icon>
                <v-tooltip activator="parent" location="top">Lihat Detail</v-tooltip>
              </v-btn>
              <v-btn icon size="small" variant="text" color="warning" :to="`/events/${item.id}/edit`">
                <v-icon size="small">mdi-pencil</v-icon>
                <v-tooltip activator="parent" location="top">Edit</v-tooltip>
              </v-btn>
              <v-btn icon size="small" variant="text" color="error" @click="confirmDelete(item)">
                <v-icon size="small">mdi-delete</v-icon>
                <v-tooltip activator="parent" location="top">Hapus</v-tooltip>
              </v-btn>
            </div>
          </template>

          <template v-slot:loading>
            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
          </template>

          <!-- No Data -->
          <template v-slot:no-data>
            <div class="text-center py-8">
              <v-icon size="48" color="grey-lighten-1">mdi-calendar-blank</v-icon>
              <p class="text-body-1 font-weight-medium text-grey-darken-1 mt-2 mb-1">Belum ada data event</p>
              <p class="text-caption text-grey">Event yang dibuat atau hasil filter akan muncul di sini</p>
            </div>
          </template>
        </v-data-table>

        <!-- Pagination -->
        <v-divider></v-divider>
        <div class="pa-4 d-flex align-center">
          <div class="text-caption text-grey">
            Menampilkan {{ filteredEvents.length }} event
          </div>
          <v-spacer></v-spacer>
        </div>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus Event?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus event "<strong>{{ eventToDelete?.name }}</strong>"?
            Tindakan ini tidak dapat dibatalkan.
          </p>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" :loading="deleting" @click="deleteEvent">
            Hapus
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'solid'
})

const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

const loading = ref(true)
const events = ref([])
const activations = ref([])
const search = ref('')
const filterType = ref(null)
const filterActivation = ref(null)
const filterStatus = ref(null)
const sortBy = ref('newest')
const deleteDialog = ref(false)
const deleting = ref(false)
const eventToDelete = ref(null)

const eventTypes = [
  { title: 'Regular Event', value: 'regular' },
  { title: 'Jalan Kaki', value: 'walking' }
]

const statusOptions = [
  { title: 'Mendatang', value: 'upcoming' },
  { title: 'Berlangsung', value: 'ongoing' },
  { title: 'Selesai', value: 'past' }
]

const sortOptions = [
  { label: 'Terbaru', value: 'newest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Nama A-Z', value: 'name_asc' },
  { label: 'Nama Z-A', value: 'name_desc' }
]

const headers = [
  { title: 'Poster', key: 'poster', sortable: false, width: '80px' },
  { title: 'Nama Event', key: 'name', sortable: true },
  { title: 'Tanggal', key: 'date', sortable: true },
  { title: 'Status', key: 'status', sortable: false },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'center', width: '140px' }
]

const stats = computed(() => {
  const upcoming = events.value.filter(e => new Date(e.date) > new Date()).length
  const walking = events.value.filter(e => e.type === 'walking').length
  const regular = events.value.filter(e => e.type === 'regular').length

  return [
    {
      icon: 'mdi-calendar-multiple',
      value: events.value.length,
      label: 'Total Event',
      color: 'blue',
      bgColor: 'blue-lighten-5'
    },
    {
      icon: 'mdi-calendar-clock',
      value: upcoming,
      label: 'Mendatang',
      color: 'green',
      bgColor: 'green-lighten-5'
    },
    {
      icon: 'mdi-walk',
      value: walking,
      label: 'Jalan Kaki',
      color: 'teal',
      bgColor: 'teal-lighten-5'
    },
    {
      icon: 'mdi-calendar-star',
      value: regular,
      label: 'Regular',
      color: 'orange',
      bgColor: 'orange-lighten-5'
    }
  ]
})

const filteredEvents = computed(() => {
  let filtered = events.value

  // Filter by type
  if (filterType.value) {
    filtered = filtered.filter(e => e.type === filterType.value)
  }

  // Filter by activation
  if (filterActivation.value) {
    filtered = filtered.filter(e => e.activation_id === filterActivation.value)
  }

  // Filter by status
  if (filterStatus.value) {
    const now = new Date()
    filtered = filtered.filter(e => {
      const eventDate = new Date(e.date)
      if (filterStatus.value === 'upcoming') {
        return eventDate > now
      } else if (filterStatus.value === 'past') {
        return eventDate < now
      } else if (filterStatus.value === 'ongoing') {
        const today = new Date()
        today.setHours(0, 0, 0, 0)
        const tomorrow = new Date(today)
        tomorrow.setDate(tomorrow.getDate() + 1)
        return eventDate >= today && eventDate < tomorrow
      }
      return true
    })
  }

  return filtered
})

const getImageUrl = (path: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=400&h=300&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getStatusColor = (date: string) => {
  const eventDate = new Date(date)
  const now = new Date()

  if (eventDate > now) return 'success'
  if (eventDate < now) return 'grey'
  return 'warning'
}

const getStatusLabel = (date: string) => {
  const eventDate = new Date(date)
  const now = new Date()

  if (eventDate > now) return 'Mendatang'
  if (eventDate < now) return 'Selesai'
  return 'Berlangsung'
}

const fetchEvents = async () => {
  loading.value = true
  try {
    const response = await api.get('/events')
    events.value = response.data.data || response.data || []
  } catch (error) {
    console.error('Error fetching events:', error)
  } finally {
    loading.value = false
  }
}

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = [
      { id: null, name: 'Semua Aktivasi' },
      ...(response.data || [])
    ]
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}


const confirmDelete = (event: any) => {
  eventToDelete.value = event
  deleteDialog.value = true
}

const deleteEvent = async () => {
  if (!eventToDelete.value) return

  deleting.value = true
  try {
    await api.delete(`/events/${eventToDelete.value.id}`)

    // Remove from local array
    events.value = events.value.filter(e => e.id !== eventToDelete.value.id)

    deleteDialog.value = false
    eventToDelete.value = null
  } catch (error: any) {
    console.error('Error deleting event:', error)
    alert(error.response?.data?.message || 'Gagal menghapus event')
  } finally {
    deleting.value = false
  }
}

// Check if user has permission
onMounted(() => {
  if (!authStore.isAdmin && !authStore.isCommunityAdmin) {
    navigateTo('/events')
    return
  }

  fetchEvents()
  fetchActivations()
})
</script>

<style scoped>
.admin-events-page {
  background: #fafafa;
  min-height: 100vh;
}
</style>
