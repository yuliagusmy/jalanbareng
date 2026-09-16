<template>
  <div class="my-posts-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex align-center px-6 py-4">
          <v-icon class="mr-3" size="32" color="primary">mdi-post</v-icon>
          <div>
            <h1 class="text-h5 font-weight-bold">Postingan Saya</h1>
            <p class="text-caption text-grey mb-0">Kelola destinasi yang telah Anda bagikan</p>
          </div>
          <v-spacer></v-spacer>
          <v-btn color="primary" variant="flat" rounded="lg" to="/destinations/create" size="large">
            <v-icon start>mdi-plus</v-icon>
            Buat Postingan
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="4">
          <v-card elevation="0" rounded="lg" class="pa-4" color="blue-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="blue" class="mr-4">mdi-map-marker-multiple</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-blue">{{ stats.total }}</div>
                <div class="text-caption text-grey-darken-1">Total Postingan</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="4">
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
        <v-col cols="12" sm="4">
          <v-card elevation="0" rounded="lg" class="pa-4" color="green-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="green" class="mr-4">mdi-comment-multiple</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-green">{{ stats.totalComments }}</div>
                <div class="text-caption text-grey-darken-1">Total Komentar</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
        <v-row>
          <v-col cols="12" md="6">
            <v-text-field v-model="search" placeholder="Cari postingan Anda..." prepend-inner-icon="mdi-magnify"
              variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchMyPosts"></v-text-field>
          </v-col>
          <v-col cols="12" md="3">
            <v-select v-model="sortBy" :items="sortOptions" item-title="label" item-value="value" variant="outlined"
              rounded="lg" density="comfortable" hide-details @update:model-value="fetchMyPosts">
              <template v-slot:prepend-inner>
                <v-icon>mdi-sort</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" md="3">
            <v-select v-model="filterCategory" :items="categories" item-title="name" item-value="id"
              placeholder="Semua Kategori" variant="outlined" rounded="lg" density="comfortable" clearable hide-details
              @update:model-value="fetchMyPosts">
              <template v-slot:prepend-inner>
                <v-icon>mdi-filter</v-icon>
              </template>
            </v-select>
          </v-col>
        </v-row>
      </v-card>

      <!-- Posts Grid -->
      <v-row v-if="!loading">
        <v-col v-for="destination in myPosts" :key="destination.id" cols="6" sm="6" md="4" lg="3">
          <v-card elevation="0" rounded="lg" class="post-card h-100">
            <!-- Image -->
            <div class="post-image-wrapper" @click="viewDestination(destination.id)">
              <img :src="getImageUrl(destination.primary_photo)" :alt="destination.name" class="post-image" />
              <div class="post-overlay">
                <div class="post-stats">
                  <div class="stat-item">
                    <v-icon size="small" color="white">mdi-heart</v-icon>
                    <span>{{ destination.likes_count || 0 }}</span>
                  </div>
                  <div class="stat-item">
                    <v-icon size="small" color="white">mdi-comment</v-icon>
                    <span>{{ destination.comments_count || 0 }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Content -->
            <v-card-text class="pa-4">
              <div class="d-flex align-center mb-2">
                <v-chip v-if="destination.category" size="x-small" color="primary" variant="flat" class="mr-2">
                  {{ destination.category.name }}
                </v-chip>
                <v-spacer></v-spacer>
                <v-menu>
                  <template v-slot:activator="{ props }">
                    <v-btn icon="mdi-dots-vertical" size="x-small" variant="text" v-bind="props"></v-btn>
                  </template>
                  <v-list density="compact" rounded="lg">
                    <v-list-item @click="editDestination(destination.id)">
                      <template v-slot:prepend>
                        <v-icon>mdi-pencil</v-icon>
                      </template>
                      <v-list-item-title>Edit</v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="confirmDelete(destination)">
                      <template v-slot:prepend>
                        <v-icon color="error">mdi-delete</v-icon>
                      </template>
                      <v-list-item-title class="text-error">Hapus</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
              </div>

              <h4 class="text-subtitle-1 font-weight-bold mb-1">
                {{ destination.name }}
              </h4>

              <p class="text-caption text-grey-darken-1 mb-2 line-clamp-2">
                {{ destination.description }}
              </p>

              <div class="text-caption text-grey">
                <v-icon size="x-small" class="mr-1">mdi-calendar</v-icon>
                {{ formatDate(destination.created_at) }}
              </div>
            </v-card-text>
          </v-card>
        </v-col>

        <!-- Empty State -->
        <v-col v-if="myPosts.length === 0" cols="12">
          <v-card elevation="0" rounded="xl" class="text-center pa-12">
            <v-icon size="100" color="grey-lighten-1">mdi-post-outline</v-icon>
            <h3 class="text-h5 mt-6 mb-2">Belum Ada Postingan</h3>
            <p class="text-body-1 text-grey-darken-1 mb-6">
              Mulai bagikan destinasi menarik yang Anda kunjungi
            </p>
            <v-btn color="primary" size="large" rounded="lg" to="/destinations/create">
              <v-icon start>mdi-plus-circle</v-icon>
              Buat Postingan Pertama
            </v-btn>
          </v-card>
        </v-col>
      </v-row>

      <!-- Loading State -->
      <v-row v-else>
        <v-col v-for="n in 8" :key="n" cols="6" sm="6" md="4" lg="3">
          <v-skeleton-loader type="card"></v-skeleton-loader>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row v-if="pagination.total > pagination.per_page" class="mt-6">
        <v-col cols="12" class="d-flex justify-center">
          <v-pagination v-model="pagination.current_page" :length="Math.ceil(pagination.total / pagination.per_page)"
            color="primary" rounded="circle" @update:model-value="fetchMyPosts"></v-pagination>
        </v-col>
      </v-row>
    </v-container>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus Postingan?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus "<strong>{{ destinationToDelete?.name }}</strong>"?
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

definePageMeta({
  middleware: 'auth',
  layout: 'solid'
})

const router = useRouter()
const { api } = useApi()
const config = useRuntimeConfig()

const loading = ref(false)
const deleting = ref(false)
const myPosts = ref([])
const categories = ref([])
const search = ref('')
const sortBy = ref('newest')
const filterCategory = ref(null)
const deleteDialog = ref(false)
const destinationToDelete = ref(null)

const stats = ref({
  total: 0,
  totalLikes: 0,
  totalComments: 0
})

const pagination = ref({
  current_page: 1,
  per_page: 12,
  total: 0
})

const sortOptions = [
  { label: 'Terbaru', value: 'newest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Paling Banyak Likes', value: 'most_liked' },
  { label: 'Paling Banyak Komentar', value: 'most_commented' }
]

const { getImageUrl } = useImageUrl()

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const fetchMyPosts = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    }

    if (search.value) {
      params.search = search.value
    }

    if (filterCategory.value) {
      params.category_id = filterCategory.value
    }

    if (sortBy.value) {
      params.sort = sortBy.value
    }

    const response = await api.get('/destinations/my-posts', { params })
    myPosts.value = response.data.data || []

    pagination.value = {
      current_page: response.data.current_page,
      per_page: response.data.per_page,
      total: response.data.total
    }

    // Calculate stats
    stats.value.total = response.data.total
    stats.value.totalLikes = myPosts.value.reduce((sum, post) => sum + (post.likes_count || 0), 0)
    stats.value.totalComments = myPosts.value.reduce((sum, post) => sum + (post.comments_count || 0), 0)
  } catch (error) {
    console.error('Error fetching my posts:', error)
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
    fetchMyPosts()
  } catch (error: any) {
    console.error('Error deleting destination:', error)
    alert(error.response?.data?.message || 'Gagal menghapus postingan')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchMyPosts()
  fetchCategories()
})
</script>

<style scoped>
.my-posts-page {
  background: #fafafa;
  min-height: 100vh;
}

/* Post Card */
.post-card {
  border: 1px solid #dbdbdb;
  transition: all 0.3s ease;
  overflow: hidden;
}

.post-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.post-image-wrapper {
  position: relative;
  width: 100%;
  padding-top: 100%;
  /* 1:1 Aspect Ratio like Instagram */
  overflow: hidden;
  cursor: pointer;
}

.post-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.post-card:hover .post-image {
  transform: scale(1.05);
}

.post-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.post-card:hover .post-overlay {
  opacity: 1;
}

.post-stats {
  display: flex;
  gap: 24px;
  color: white;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  font-size: 16px;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
