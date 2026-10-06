<template>
  <div class="admin-destinations-page">
    <v-container class="py-8">
      <!-- Header - Mobile Responsive -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex flex-column flex-sm-row align-start align-sm-center px-4 px-sm-6 py-4 ga-3">
          <div class="d-flex align-center flex-grow-1">
            <v-icon class="mr-3" size="32" color="primary">mdi-map-marker-multiple</v-icon>
            <div>
              <h1 class="text-h6 text-sm-h5 font-weight-bold">Kelola Destinasi</h1>
              <p class="text-caption text-grey mb-0 d-none d-sm-block">Manage semua destinasi dari semua user</p>
            </div>
          </div>
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            to="/destinations/create"
            :size="$vuetify.display.smAndDown ? 'small' : 'large'"
            block
            class="d-sm-inline-flex"
          >
            <v-icon start :size="$vuetify.display.smAndDown ? 16 : 20">mdi-plus</v-icon>
            <span :class="$vuetify.display.smAndDown ? 'text-caption' : ''">Tambah Destinasi</span>
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Stats Cards - Mobile Optimized -->
      <v-row class="mb-6" dense>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="lg" class="stat-card-compact" color="blue-lighten-5">
            <div class="stat-icon-wrapper">
              <v-icon size="28" color="blue">mdi-map-marker-multiple</v-icon>
            </div>
            <div class="stat-number text-blue">{{ stats.total }}</div>
            <div class="stat-label">Total Destinasi</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="lg" class="stat-card-compact" color="green-lighten-5">
            <div class="stat-icon-wrapper">
              <v-icon size="28" color="green">mdi-check-circle</v-icon>
            </div>
            <div class="stat-number text-green">{{ stats.published }}</div>
            <div class="stat-label">Published</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="lg" class="stat-card-compact" color="red-lighten-5">
            <div class="stat-icon-wrapper">
              <v-icon size="28" color="red">mdi-heart</v-icon>
            </div>
            <div class="stat-number text-red">{{ stats.totalLikes }}</div>
            <div class="stat-label">Total Likes</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="lg" class="stat-card-compact" color="orange-lighten-5">
            <div class="stat-icon-wrapper">
              <v-icon size="28" color="orange">mdi-comment-multiple</v-icon>
            </div>
            <div class="stat-number text-orange">{{ stats.totalComments }}</div>
            <div class="stat-label">Total Komentar</div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
        <!-- Mobile: search + filter toggle -->
        <div v-if="$vuetify.display.xs" class="d-flex align-center ga-2">
          <v-text-field
            v-model="search"
            placeholder="Cari destinasi..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            rounded="lg"
            density="compact"
            clearable
            hide-details
            class="flex-grow-1"
            @update:model-value="fetchDestinations"
          ></v-text-field>
          <v-btn
            :variant="activeFilterCount > 0 ? 'flat' : 'outlined'"
            :color="activeFilterCount > 0 ? 'primary' : 'grey-darken-1'"
            icon
            rounded="lg"
            size="40"
            @click="showFilters = !showFilters"
            :aria-label="showFilters ? 'Tutup filter' : 'Buka filter'"
          >
            <v-badge v-if="activeFilterCount > 0" :content="activeFilterCount" color="primary" floating>
              <v-icon size="20">mdi-filter-variant</v-icon>
            </v-badge>
            <v-icon v-else size="20">mdi-filter-variant</v-icon>
          </v-btn>
        </div>

        <!-- Mobile: collapsible filter panel -->
        <v-expand-transition>
          <div v-if="$vuetify.display.xs && showFilters" class="mt-3 d-flex flex-column ga-2">
            <v-select v-model="filterActivation" :items="activations" item-title="name" item-value="id"
              placeholder="Semua Aktivasi" variant="outlined" rounded="lg" density="compact" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner><v-icon size="18">mdi-star-circle</v-icon></template>
            </v-select>
            <v-select v-model="filterCategory" :items="categories" item-title="name" item-value="id"
              placeholder="Semua Kategori" variant="outlined" rounded="lg" density="compact" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner><v-icon size="18">mdi-filter</v-icon></template>
            </v-select>
            <v-select v-model="filterUser" :items="users" item-title="name" item-value="id"
              placeholder="Semua User" variant="outlined" rounded="lg" density="compact" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner><v-icon size="18">mdi-account</v-icon></template>
            </v-select>
            <v-select v-model="sortBy" :items="sortOptions" item-title="label" item-value="value"
              variant="outlined" rounded="lg" density="compact" hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner><v-icon size="18">mdi-sort</v-icon></template>
            </v-select>
            <v-btn v-if="activeFilterCount > 0" variant="text" color="error" size="small" @click="clearFilters" prepend-icon="mdi-close-circle">
              Reset Filter
            </v-btn>
          </div>
        </v-expand-transition>

        <!-- Desktop: semua filter tampil biasa -->
        <v-row v-if="!$vuetify.display.xs">
          <v-col cols="12" md="3">
            <v-text-field v-model="search" placeholder="Cari destinasi..." prepend-inner-icon="mdi-magnify"
              variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchDestinations"></v-text-field>
          </v-col>
          <v-col cols="12" md="2">
            <v-select v-model="filterActivation" :items="activations" item-title="name" item-value="id"
              placeholder="Semua Aktivasi" variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner>
                <v-icon>mdi-star-circle</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" md="2">
            <v-select v-model="filterCategory" :items="categories" item-title="name" item-value="id"
              placeholder="Semua Kategori" variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner>
                <v-icon>mdi-filter</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" md="2">
            <v-select v-model="filterUser" :items="users" item-title="name" item-value="id" placeholder="Semua User"
              variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner>
                <v-icon>mdi-account</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select v-model="sortBy" :items="sortOptions" item-title="label" item-value="value" variant="outlined"
              rounded="lg" density="comfortable" hide-details @update:model-value="fetchDestinations">
              <template v-slot:prepend-inner>
                <v-icon>mdi-sort</v-icon>
              </template>
            </v-select>
          </v-col>
        </v-row>
      </v-card>

      <!-- Mobile Card View (xs / 390px) -->
      <div v-if="$vuetify.display.xs">
        <div v-if="loading" class="py-2">
          <v-skeleton-loader v-for="i in 4" :key="i" type="card" height="120" rounded="xl" class="mb-3" />
        </div>
        <div v-else-if="destinations.length === 0" class="text-center py-10">
          <v-icon size="64" color="grey-lighten-1">mdi-map-marker-off</v-icon>
          <p class="text-body-2 text-grey mt-2">Tidak ada destinasi ditemukan</p>
        </div>
        <div v-else>
          <v-card
            v-for="item in destinations"
            :key="item.id"
            elevation="0"
            rounded="xl"
            class="pa-4 mb-3 mobile-dest-card"
          >
            <div class="d-flex align-start ga-3">
              <!-- Photo -->
              <v-avatar size="60" rounded="lg" class="flex-shrink-0">
                <v-img :src="getImageUrl(item.primary_photo)" :alt="item.name" cover></v-img>
              </v-avatar>
              <!-- Info -->
              <div class="flex-grow-1 min-w-0">
                <div class="font-weight-bold text-grey-darken-4 text-truncate mb-1">{{ item.name }}</div>
                <div class="d-flex align-center ga-2 flex-wrap mb-1">
                  <v-chip v-if="item.category" size="x-small" color="primary" variant="tonal">
                    {{ item.category.name }}
                  </v-chip>
                </div>
                <div class="text-caption text-grey-darken-1 d-flex align-center ga-2">
                  <span><v-icon size="11" color="red">mdi-heart</v-icon> {{ item.likes_count || 0 }}</span>
                  <span><v-icon size="11" color="blue">mdi-comment</v-icon> {{ item.comments_count || 0 }}</span>
                  <span class="text-truncate">{{ item.user?.name || '-' }}</span>
                </div>
              </div>
            </div>
            <!-- Actions -->
            <div class="d-flex align-center justify-end ga-1 mt-3 pt-3 border-t">
              <v-btn size="small" variant="tonal" color="primary" rounded="pill" prepend-icon="mdi-eye" @click="viewDestination(item.id)">
                Lihat
              </v-btn>
              <v-btn size="small" variant="tonal" color="warning" rounded="pill" icon="mdi-pencil" @click="editDestination(item.id)" />
              <v-btn size="small" variant="text" color="error" rounded="pill" icon="mdi-delete" @click="confirmDelete(item)" />
            </div>
          </v-card>
        </div>

        <!-- Pagination Mobile -->
        <div class="d-flex justify-center mt-2 mb-4">
          <v-pagination
            v-model="pagination.current_page"
            :length="Math.ceil(pagination.total / pagination.per_page)"
            :total-visible="3"
            density="comfortable"
            @update:model-value="fetchDestinations"
          ></v-pagination>
        </div>
      </div>

      <!-- Desktop Table (smAndUp) -->
      <v-card v-else elevation="0" rounded="lg">
        <v-data-table :headers="headers" :items="destinations" :loading="loading" :items-per-page="pagination.per_page"
          hide-default-footer class="elevation-0">
          <template v-slot:item.primary_photo="{ item }">
            <v-avatar size="60" rounded="lg" class="my-2">
              <v-img :src="getImageUrl(item.primary_photo)" :alt="item.name"></v-img>
            </v-avatar>
          </template>

          <template v-slot:item.name="{ item }">
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-grey">
                <v-icon size="x-small">mdi-map-marker</v-icon>
                {{ item.latitude?.toFixed(4) }}, {{ item.longitude?.toFixed(4) }}
              </div>
            </div>
          </template>

          <template v-slot:item.category="{ item }">
            <v-chip size="small" color="primary" variant="tonal">
              {{ item.category?.name || '-' }}
            </v-chip>
          </template>

          <template v-slot:item.user="{ item }">
            <div class="d-flex align-center">
              <v-avatar size="32" class="mr-2">
                <v-img v-if="item.user?.photo" :src="getImageUrl(item.user.photo)"></v-img>
                <v-icon v-else size="small">mdi-account-circle</v-icon>
              </v-avatar>
              <div>
                <div class="text-body-2">{{ item.user?.name || '-' }}</div>
              </div>
            </div>
          </template>

          <template v-slot:item.stats="{ item }">
            <div class="text-caption">
              <div><v-icon size="x-small" color="red">mdi-heart</v-icon> {{ item.likes_count || 0 }}</div>
              <div><v-icon size="x-small" color="blue">mdi-comment</v-icon> {{ item.comments_count || 0 }}</div>
            </div>
          </template>

          <template v-slot:item.created_at="{ item }">
            <div class="text-caption">{{ formatDate(item.created_at) }}</div>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn icon size="small" variant="text" color="primary" @click="viewDestination(item.id)">
                <v-icon size="small">mdi-eye</v-icon>
                <v-tooltip activator="parent" location="top">Lihat Detail</v-tooltip>
              </v-btn>
              <v-btn icon size="small" variant="text" color="warning" @click="editDestination(item.id)">
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

          <template v-slot:no-data>
            <div class="text-center py-8">
              <v-icon size="64" color="grey-lighten-1">mdi-map-marker-off</v-icon>
              <p class="text-body-1 text-grey mt-4">Tidak ada destinasi ditemukan</p>
            </div>
          </template>
        </v-data-table>

        <!-- Pagination -->
        <v-divider></v-divider>
        <div class="pa-4 d-flex align-center">
          <div class="text-caption text-grey">
            Menampilkan {{ destinations.length }} dari {{ pagination.total }} destinasi
          </div>
          <v-spacer></v-spacer>
          <v-pagination v-model="pagination.current_page" :length="Math.ceil(pagination.total / pagination.per_page)"
            :total-visible="5" density="comfortable" @update:model-value="fetchDestinations"></v-pagination>
        </div>
      </v-card>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus Destinasi?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus destinasi "<strong>{{ destinationToDelete?.name }}</strong>"?
            Tindakan ini tidak dapat dibatalkan.
          </p>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" :loading="deleting" @click="deleteDestination">
            Hapus
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'solid'
})

console.log('Admin destinations page loaded')

const router = useRouter()
const { api } = useApi()
const config = useRuntimeConfig()
const authStore = useAuthStore()

console.log('Auth store:', authStore.isAdmin)

const loading = ref(false)
const deleting = ref(false)
const destinations = ref([])
const categories = ref([])
const activations = ref([])
const users = ref([])
const search = ref('')
const sortBy = ref('newest')
const filterCategory = ref(null)
const filterActivation = ref(null)
const filterUser = ref(null)
const deleteDialog = ref(false)
const destinationToDelete = ref(null)

// Mobile filter panel state
const showFilters = ref(false)
const activeFilterCount = computed(() =>
  [filterActivation.value, filterCategory.value, filterUser.value].filter(Boolean).length
)
const clearFilters = () => {
  filterActivation.value = null
  filterCategory.value = null
  filterUser.value = null
  sortBy.value = 'newest'
  fetchDestinations()
}

const stats = ref({
  total: 0,
  published: 0,
  totalLikes: 0,
  totalComments: 0
})

const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0
})

const headers = [
  { title: 'Foto', key: 'primary_photo', sortable: false, width: '80px' },
  { title: 'Nama Destinasi', key: 'name', sortable: true },
  { title: 'Kategori', key: 'category', sortable: false },
  { title: 'User', key: 'user', sortable: false },
  { title: 'Stats', key: 'stats', sortable: false },
  { title: 'Tanggal', key: 'created_at', sortable: true },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'center', width: '140px' }
]

const sortOptions = [
  { label: 'Terbaru', value: 'newest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Paling Banyak Likes', value: 'most_liked' },
  { label: 'Paling Banyak Komentar', value: 'most_commented' }
]

const getImageUrl = (path: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&h=600&fit=crop'
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

const fetchDestinations = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
      admin_view: true // Flag untuk admin
    }

    if (search.value) {
      params.search = search.value
    }

    if (filterCategory.value) {
      params.category_id = filterCategory.value
    }

    if (filterActivation.value) {
      params.activation_id = filterActivation.value
    }

    if (filterUser.value) {
      params.user_id = filterUser.value
    }

    if (sortBy.value) {
      params.sort = sortBy.value
    }

    const response = await api.get('/destinations', { params })
    destinations.value = response.data.data || []

    pagination.value = {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total
    }

    // Calculate stats
    stats.value.total = response.data.total
    stats.value.published = destinations.value.length
    stats.value.totalLikes = destinations.value.reduce((sum, post) => sum + (post.likes_count || 0), 0)
    stats.value.totalComments = destinations.value.reduce((sum, post) => sum + (post.comments_count || 0), 0)
  } catch (error) {
    console.error('Error fetching destinations:', error)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = [
      { id: null, name: 'Semua Kategori' },
      ...(response.data.categories || [])
    ]
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const fetchUsers = async () => {
  try {
    const response = await api.get('/users')
    users.value = [
      { id: null, name: 'Semua User' },
      ...(response.data.users || [])
    ]
  } catch (error) {
    console.error('Error fetching users:', error)
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

const viewDestination = (id: number) => {
  router.push(`/destinations/${id}`)
}

const editDestination = (id: number) => {
  router.push(`/destinations/${id}/edit`)
}

const confirmDelete = (destination: any) => {
  destinationToDelete.value = destination
  deleteDialog.value = true
}

const deleteDestination = async () => {
  if (!destinationToDelete.value) return

  deleting.value = true
  try {
    await api.delete(`/destinations/${destinationToDelete.value.id}`)
    deleteDialog.value = false
    destinationToDelete.value = null
    fetchDestinations()
  } catch (error: any) {
    console.error('Error deleting destination:', error)
    alert(error.response?.data?.message || 'Gagal menghapus destinasi')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchDestinations()
  fetchCategories()
  fetchActivations()
  fetchUsers()
})
</script>

<style scoped>
.admin-destinations-page {
  background: #fafafa;
  min-height: 100vh;
}

/* Mobile Destination Card */
.mobile-dest-card {
  background: #FFFFFF;
  border: 1px solid #F1F5F9;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.mobile-dest-card .border-t {
  border-top: 1px solid #F1F5F9;
}

/* Mobile-Optimized Compact Stat Cards */
.stat-card-compact {
  padding: 16px 12px;
  text-align: center;
  min-height: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.stat-icon-wrapper {
  margin-bottom: 8px;
}

.stat-number {
  font-size: 1.75rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.75rem;
  color: #666;
  font-weight: 500;
  line-height: 1.2;
}

@media (max-width: 600px) {
  .stat-card-compact {
    padding: 12px 8px;
    min-height: 100px;
  }

  .stat-number {
    font-size: 1.5rem;
  }

  .stat-label {
    font-size: 0.7rem;
  }
}
</style>