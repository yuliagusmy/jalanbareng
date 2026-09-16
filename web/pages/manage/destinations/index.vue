<template>
  <div class="admin-destinations-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex align-center px-6 py-4">
          <v-icon class="mr-3" size="32" color="primary">mdi-map-marker-multiple</v-icon>
          <div>
            <h1 class="text-h5 font-weight-bold">Kelola Destinasi</h1>
            <p class="text-caption text-grey mb-0">Manage semua destinasi dari semua user</p>
          </div>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="flat" rounded="lg" to="/destinations/create" size="large">
            <v-icon start>mdi-plus</v-icon>
            Tambah Destinasi
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="3">
          <v-card elevation="0" rounded="lg" class="pa-4" color="blue-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="blue" class="mr-4">mdi-map-marker-multiple</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-blue">{{ stats.total }}</div>
                <div class="text-caption text-grey-darken-1">Total Destinasi</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="3">
          <v-card elevation="0" rounded="lg" class="pa-4" color="green-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="green" class="mr-4">mdi-check-circle</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-green">{{ stats.published }}</div>
                <div class="text-caption text-grey-darken-1">Published</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="3">
          <v-card elevation="0" rounded="lg" class="pa-4" color="red-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="red" class="mr-4">mdi-heart</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-red">{{ stats.totalLikes }}</div>
                <div class="text-caption text-grey-darken-1">Total Likes</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="3">
          <v-card elevation="0" rounded="lg" class="pa-4" color="orange-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="orange" class="mr-4">mdi-comment-multiple</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-orange">{{ stats.totalComments }}</div>
                <div class="text-caption text-grey-darken-1">Total Komentar</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
        <v-row>
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

      <!-- Destinations Table -->
      <v-card elevation="0" rounded="lg">
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
</style>
