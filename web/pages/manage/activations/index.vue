<template>
  <div class="admin-activations-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <div class="d-flex align-center justify-space-between flex-wrap ga-3 px-4 px-sm-6 py-4">
          <div class="d-flex align-center">
            <v-icon class="mr-3" size="32" color="primary">mdi-star-four-points</v-icon>
            <div>
              <h1 class="text-h6 text-sm-h5 font-weight-bold">Kelola Aktivasi</h1>
              <p class="text-caption text-grey mb-0">Manajemen aktivasi komunitas</p>
            </div>
          </div>
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            to="/manage/activations/create"
            :size="$vuetify.display.xs ? 'default' : 'large'"
          >
            <v-icon start>mdi-plus</v-icon>
            <span v-if="!$vuetify.display.xs">Buat Aktivasi</span>
            <span v-else>Aktivasi</span>
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
          <v-col cols="12" sm="6" md="4">
            <v-text-field
              v-model="search"
              placeholder="Cari aktivasi..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              clearable
              hide-details
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="filterCategory"
              :items="categoryOptions"
              placeholder="Semua Kategori"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              clearable
              hide-details
            >
              <template v-slot:prepend-inner>
                <v-icon>mdi-tag</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="filterStatus"
              :items="statusOptions"
              placeholder="Semua Status"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              clearable
              hide-details
            >
              <template v-slot:prepend-inner>
                <v-icon>mdi-check-circle</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="6" md="2">
            <v-select
              v-model="sortBy"
              :items="sortOptions"
              item-title="label"
              item-value="value"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
            >
              <template v-slot:prepend-inner>
                <v-icon>mdi-sort</v-icon>
              </template>
            </v-select>
          </v-col>
        </v-row>
      </v-card>

      <!-- Activations Table -->
      <v-card elevation="0" rounded="lg" style="overflow-x: auto;">
        <v-data-table
          :headers="headers"
          :items="filteredActivations"
          :loading="loading"
          :search="search"
          hide-default-footer
          class="elevation-0"
          style="min-width: 640px;"
        >
          <!-- Name Column -->
          <template v-slot:item.name="{ item }">
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-grey">{{ item.city }}</div>
            </div>
          </template>

          <!-- Category Column -->
          <template v-slot:item.category="{ item }">
            <v-chip size="small" :color="getCategoryColor(item.category)" variant="tonal">
              {{ getCategoryLabel(item.category) }}
            </v-chip>
          </template>

          <!-- Status Column -->
          <template v-slot:item.is_active="{ item }">
            <v-chip
              :color="item.is_active ? 'success' : 'grey'"
              size="small"
              variant="flat"
            >
              {{ item.is_active ? 'Aktif' : 'Non-aktif' }}
            </v-chip>
          </template>

          <!-- Featured Column -->
          <template v-slot:item.is_featured="{ item }">
            <v-icon
              :color="item.is_featured ? 'warning' : 'grey-lighten-2'"
            >
              {{ item.is_featured ? 'mdi-star' : 'mdi-star-outline' }}
            </v-icon>
          </template>

          <!-- Actions Column -->
          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1 justify-end">
              <v-btn
                icon
                size="small"
                variant="text"
                color="primary"
                :to="`/aktivasi/${item.slug}`"
                target="_blank"
              >
                <v-icon size="small">mdi-open-in-new</v-icon>
                <v-tooltip activator="parent" location="top">Lihat Halaman</v-tooltip>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                color="warning"
                :to="`/manage/activations/edit/${item.id}`"
              >
                <v-icon size="small">mdi-pencil</v-icon>
                <v-tooltip activator="parent" location="top">Edit</v-tooltip>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
              >
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
              <v-icon size="64" color="grey-lighten-1">mdi-star-off</v-icon>
              <p class="text-body-1 text-grey mt-4">Belum ada aktivasi</p>
            </div>
          </template>
        </v-data-table>

        <!-- Pagination -->
        <v-divider></v-divider>
        <div class="pa-4 d-flex align-center">
          <div class="text-caption text-grey">
            Menampilkan {{ filteredActivations.length }} aktivasi
          </div>
          <v-spacer></v-spacer>
        </div>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus Aktivasi?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus aktivasi "<strong>{{ itemToDelete?.name }}</strong>"?
            Tindakan ini tidak dapat dibatalkan.
          </p>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn
            color="error"
            variant="flat"
            :loading="deleteLoading"
            @click="deleteActivation"
          >
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

const api = useApi()
const authStore = useAuthStore()

const loading = ref(true)
const activations = ref([])
const search = ref('')
const filterCategory = ref(null)
const filterStatus = ref(null)
const sortBy = ref('newest')
const deleteDialog = ref(false)
const deleteLoading = ref(false)
const itemToDelete = ref<any>(null)

const categoryOptions = [
  { title: 'Kota', value: 'city' },
  { title: 'Tematik', value: 'theme' },
  { title: 'Ruang Kreatif', value: 'space' },
  { title: 'Lainnya', value: 'other' }
]

const statusOptions = [
  { title: 'Aktif', value: 'active' },
  { title: 'Non-aktif', value: 'inactive' }
]

const sortOptions = [
  { label: 'Terbaru', value: 'newest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Nama A-Z', value: 'name_asc' },
  { label: 'Nama Z-A', value: 'name_desc' }
]

const headers = [
  { title: 'Nama Aktivasi', key: 'name', sortable: true },
  { title: 'Kategori', key: 'category', sortable: true },
  { title: 'Status', key: 'is_active', sortable: true },
  { title: 'Unggulan', key: 'is_featured', sortable: true },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'end', width: '140px' }
]

const stats = computed(() => {
  const active = activations.value.filter(a => a.is_active).length
  const featured = activations.value.filter(a => a.is_featured).length
  const city = activations.value.filter(a => a.category === 'city').length

  return [
    {
      icon: 'mdi-star-four-points',
      value: activations.value.length,
      label: 'Total Aktivasi',
      color: 'blue',
      bgColor: 'blue-lighten-5'
    },
    {
      icon: 'mdi-check-circle',
      value: active,
      label: 'Aktif',
      color: 'green',
      bgColor: 'green-lighten-5'
    },
    {
      icon: 'mdi-star',
      value: featured,
      label: 'Unggulan',
      color: 'orange',
      bgColor: 'orange-lighten-5'
    },
    {
      icon: 'mdi-city',
      value: city,
      label: 'Kota',
      color: 'purple',
      bgColor: 'purple-lighten-5'
    }
  ]
})

const filteredActivations = computed(() => {
  let filtered = activations.value

  // Filter by category
  if (filterCategory.value) {
    filtered = filtered.filter(a => a.category === filterCategory.value)
  }

  // Filter by status
  if (filterStatus.value) {
    const isActive = filterStatus.value === 'active'
    filtered = filtered.filter(a => Boolean(a.is_active) === isActive)
  }

  // Sort
  return filtered.sort((a, b) => {
    if (sortBy.value === 'newest') return new Date(b.created_at).getTime() - new Date(a.created_at).getTime()
    if (sortBy.value === 'oldest') return new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
    if (sortBy.value === 'name_asc') return a.name.localeCompare(b.name)
    if (sortBy.value === 'name_desc') return b.name.localeCompare(a.name)
    return 0
  })
})

const fetchActivations = async () => {
  loading.value = true
  try {
    const response = await api.get('/activations?include_inactive=1')
    activations.value = response.data
  } catch (error) {
    console.error('Error fetching activations:', error)
  } finally {
    loading.value = false
  }
}

const getCategoryColor = (category: string) => {
  const colors: Record<string, string> = {
    city: 'blue',
    theme: 'purple',
    space: 'pink',
    other: 'grey'
  }
  return colors[category] || 'grey'
}

const getCategoryLabel = (category: string) => {
  const labels: Record<string, string> = {
    city: 'Kota',
    theme: 'Tematik',
    space: 'Ruang Kreatif',
    other: 'Lainnya'
  }
  return labels[category] || 'Lainnya'
}

const confirmDelete = (item: any) => {
  itemToDelete.value = item
  deleteDialog.value = true
}

const deleteActivation = async () => {
  if (!itemToDelete.value) return
  
  deleteLoading.value = true
  try {
    await api.delete(`/activations/${itemToDelete.value.id}`)
    deleteDialog.value = false
    fetchActivations()
  } catch (error) {
    console.error('Error deleting activation:', error)
  } finally {
    deleteLoading.value = false
  }
}

// Check if user has permission
onMounted(() => {
  if (!authStore.isAdmin && !authStore.isCommunityAdmin) {
    navigateTo('/activations')
    return
  }

  fetchActivations()
})
</script>

<style scoped>
.admin-activations-page {
  background: #fafafa;
  min-height: 100vh;
}
</style>
