<template>
  <div class="admin-categories-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex align-center px-6 py-4">
          <v-icon class="mr-3" size="32" color="primary">mdi-shape</v-icon>
          <div>
            <h1 class="text-h5 font-weight-bold">Kelola Kategori</h1>
            <p class="text-caption text-grey mb-0">Manage kategori destinasi</p>
          </div>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            size="large"
            @click="openAddDialog"
          >
            <v-icon start>mdi-plus</v-icon>
            Tambah Kategori
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="4">
          <v-card elevation="0" rounded="lg" class="pa-4" color="blue-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="blue" class="mr-4">mdi-shape</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-blue">{{ categories.length }}</div>
                <div class="text-caption text-grey-darken-1">Total Kategori</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="4">
          <v-card elevation="0" rounded="lg" class="pa-4" color="green-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="green" class="mr-4">mdi-image</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-green">{{ categoriesWithPhoto }}</div>
                <div class="text-caption text-grey-darken-1">Dengan Foto</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="12" sm="4">
          <v-card elevation="0" rounded="lg" class="pa-4" color="orange-lighten-5">
            <div class="d-flex align-center">
              <v-icon size="40" color="orange" class="mr-4">mdi-map-marker</v-icon>
              <div>
                <div class="text-h4 font-weight-bold text-orange">{{ totalDestinations }}</div>
                <div class="text-caption text-grey-darken-1">Total Destinasi</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Categories Grid -->
      <v-card elevation="0" rounded="lg">
        <v-card-text class="pa-6">
          <v-row v-if="!loading && categories.length > 0">
            <v-col
              v-for="category in categories"
              :key="category.id"
              cols="12"
              sm="6"
              md="4"
              lg="3"
            >
              <v-card rounded="xl" class="category-card" hover>
                <v-img
                  :src="getCategoryImage(category)"
                  height="200"
                  cover
                  class="category-image"
                >
                  <template v-slot:placeholder>
                    <div class="d-flex align-center justify-center fill-height">
                      <v-progress-circular indeterminate color="white"></v-progress-circular>
                    </div>
                  </template>
                  <template v-slot:error>
                    <div class="d-flex align-center justify-center fill-height bg-grey-lighten-2">
                      <v-icon size="48" color="grey">mdi-image-broken</v-icon>
                    </div>
                  </template>
                  <div class="category-overlay">
                    <v-icon v-if="category.icon" size="48" color="white">
                      {{ category.icon }}
                    </v-icon>
                  </div>
                </v-img>
                <v-card-text class="pa-4">
                  <h3 class="text-h6 font-weight-bold mb-1">{{ category.name }}</h3>
                  <p class="text-caption text-grey mb-2">{{ category.description || 'Tidak ada deskripsi' }}</p>
                  <v-chip size="small" color="primary" variant="tonal">
                    <v-icon start size="small">mdi-map-marker</v-icon>
                    {{ category.destinations_count || 0 }} destinasi
                  </v-chip>
                </v-card-text>
                <v-card-actions class="px-4 pb-4">
                  <v-btn
                    size="small"
                    variant="text"
                    color="warning"
                    @click="openEditDialog(category)"
                  >
                    <v-icon start>mdi-pencil</v-icon>
                    Edit
                  </v-btn>
                  <v-spacer></v-spacer>
                  <v-btn
                    size="small"
                    variant="text"
                    color="error"
                    @click="confirmDelete(category)"
                  >
                    <v-icon start>mdi-delete</v-icon>
                    Hapus
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>

          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
            <p class="text-grey mt-4">Memuat kategori...</p>
          </div>

          <!-- Empty State -->
          <div v-if="!loading && categories.length === 0" class="text-center py-12">
            <v-icon size="64" color="grey-lighten-1">mdi-shape-outline</v-icon>
            <p class="text-body-1 text-grey mt-4">Belum ada kategori</p>
            <v-btn color="primary" class="mt-4" @click="openAddDialog">
              <v-icon start>mdi-plus</v-icon>
              Tambah Kategori Pertama
            </v-btn>
          </div>
        </v-card-text>
      </v-card>
    </v-container>

    <!-- Add/Edit Category Dialog -->
    <v-dialog v-model="dialog" max-width="600" persistent>
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          {{ isEditMode ? 'Edit Kategori' : 'Tambah Kategori' }}
        </v-card-title>
        <v-card-text class="px-6 pb-0">
          <!-- Photo Upload Section -->
          <div class="mb-6">
            <label class="text-subtitle-2 font-weight-medium mb-3 d-block text-grey-darken-2">
              <v-icon size="small" class="mr-1">mdi-image</v-icon>
              Foto Kategori
            </label>
            <div class="photo-upload-container">
              <div class="photo-preview-wrapper">
                <v-card
                  elevation="0"
                  rounded="lg"
                  class="photo-preview-card"
                  :class="{ 'has-image': photoPreview || (isEditMode && form.photo) }"
                >
                  <v-img
                    v-if="photoPreview"
                    :src="photoPreview"
                    height="180"
                    cover
                    class="rounded-lg"
                  ></v-img>
                  <v-img
                    v-else-if="isEditMode && form.photo"
                    :src="getImageUrl(form.photo)"
                    height="180"
                    cover
                    class="rounded-lg"
                  >
                    <template v-slot:placeholder>
                      <div class="d-flex align-center justify-center fill-height">
                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                      </div>
                    </template>
                    <template v-slot:error>
                      <div class="d-flex align-center justify-center fill-height bg-grey-lighten-3">
                        <v-icon size="48" color="grey">mdi-image-broken</v-icon>
                      </div>
                    </template>
                  </v-img>
                  <div v-else class="photo-placeholder">
                    <v-icon size="64" color="grey-lighten-1">mdi-image-plus</v-icon>
                    <p class="text-caption text-grey mt-2">Preview foto akan muncul di sini</p>
                  </div>
                </v-card>
              </div>
              <div class="photo-input-wrapper">
                <!-- Native HTML file input like in destinations -->
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  style="display: none"
                  @change="handlePhotoChange"
                />
                <v-btn
                  color="primary"
                  variant="outlined"
                  rounded="lg"
                  block
                  size="large"
                  @click="triggerFileInput"
                  class="mb-2"
                >
                  <v-icon start>mdi-camera</v-icon>
                  {{ photoPreview ? 'Ganti Foto' : 'Pilih Foto Kategori' }}
                </v-btn>
                <v-btn
                  v-if="photoPreview"
                  color="error"
                  variant="text"
                  rounded="lg"
                  block
                  @click="clearPhoto"
                >
                  <v-icon start>mdi-delete</v-icon>
                  Hapus Foto
                </v-btn>
                <div class="d-flex align-center mt-2">
                  <v-icon size="small" color="grey" class="mr-1">mdi-information</v-icon>
                  <p class="text-caption text-grey mb-0">Format: JPG, PNG • Maksimal: 2MB</p>
                </div>
                <p v-if="errors.photo && errors.photo.length > 0" class="text-caption text-error mt-1">
                  {{ errors.photo[0] }}
                </p>
              </div>
            </div>
          </div>

          <v-divider class="mb-6"></v-divider>

          <!-- Form Fields -->
          <v-text-field
            v-model="form.name"
            label="Nama Kategori *"
            placeholder="Contoh: Kuliner, Wisata Alam, dll"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-4"
            :error-messages="errors.name"
            hide-details="auto"
          >
            <template v-slot:prepend-inner>
              <v-icon color="primary">mdi-tag</v-icon>
            </template>
          </v-text-field>

          <v-textarea
            v-model="form.description"
            label="Deskripsi"
            placeholder="Jelaskan tentang kategori ini..."
            variant="outlined"
            rounded="lg"
            density="comfortable"
            rows="3"
            class="mb-4"
            :error-messages="errors.description"
            hide-details="auto"
          >
            <template v-slot:prepend-inner>
              <v-icon color="primary">mdi-text</v-icon>
            </template>
          </v-textarea>

          <v-select
            v-model="form.icon"
            :items="iconOptions"
            label="Icon Kategori (Opsional)"
            placeholder="Pilih icon yang sesuai"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            clearable
            hide-details="auto"
          >
            <template v-slot:prepend-inner>
              <v-icon color="primary">mdi-shape</v-icon>
            </template>
            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props">
                <template v-slot:prepend>
                  <v-icon>{{ item.value }}</v-icon>
                </template>
              </v-list-item>
            </template>
            <template v-slot:selection="{ item }">
              <v-icon start>{{ item.value }}</v-icon>
              {{ item.title }}
            </template>
          </v-select>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="closeDialog">Batal</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :loading="saving"
            @click="saveCategory"
          >
            {{ isEditMode ? 'Update' : 'Simpan' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus Kategori?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus kategori "<strong>{{ categoryToDelete?.name }}</strong>"?
            <span v-if="categoryToDelete?.destinations_count > 0" class="text-error">
              Kategori ini memiliki {{ categoryToDelete.destinations_count }} destinasi.
            </span>
          </p>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn
            color="error"
            variant="flat"
            :loading="deleting"
            @click="deleteCategory"
          >
            Hapus
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, nextTick } from 'vue'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'

definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'solid'
})

const { api } = useApi()
const config = useRuntimeConfig()

const loading = ref(false)
const saving = ref(false)
const deleting = ref(false)
const dialog = ref(false)
const deleteDialog = ref(false)
const isEditMode = ref(false)
const categories = ref([])
const categoryToDelete = ref(null)
const photoFile = ref([])
const photoPreview = ref(null)
const selectedFile = ref<File | null>(null)  // Store the actual file here
const fileInput = ref<HTMLInputElement | null>(null)  // Ref for file input

const form = ref({
  id: null,
  name: '',
  description: '',
  icon: '',
  photo: ''
})

const errors = ref({
  name: [],
  description: [],
  photo: []
})

const iconOptions = [
  { title: 'Kuliner', value: 'mdi-food' },
  { title: 'Wisata Alam', value: 'mdi-nature' },
  { title: 'Pantai', value: 'mdi-beach' },
  { title: 'Gunung', value: 'mdi-image-filter-hdr' },
  { title: 'Museum', value: 'mdi-bank' },
  { title: 'Taman', value: 'mdi-tree' },
  { title: 'Belanja', value: 'mdi-shopping' },
  { title: 'Olahraga', value: 'mdi-run' },
  { title: 'Hiburan', value: 'mdi-television-play' },
  { title: 'Religi', value: 'mdi-mosque' },
  { title: 'Edukasi', value: 'mdi-school' },
  { title: 'Kafe', value: 'mdi-coffee' }
]

const categoriesWithPhoto = computed(() => {
  return categories.value.filter(c => c.photo).length
})

const totalDestinations = computed(() => {
  return categories.value.reduce((sum, c) => sum + (c.destinations_count || 0), 0)
})

const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }
  // Remove 'public/' prefix if exists, as storage path should directly point to the file
  const cleanPath = path.replace(/^public\//, '')
  const imageUrl = `${config.public.apiUrl.replace('/api', '')}/storage/${cleanPath}`
  return imageUrl
}

const getCategoryImage = (category: any) => {
  // Use photo_url from backend if available (includes full URL)
  if (category.photo_url) {
    return category.photo_url
  }
  // Fallback to manual construction if photo_url not available
  if (category.photo) {
    return getImageUrl(category.photo)
  }
  // Default placeholder image
  return 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=300&fit=crop'
}

// Trigger file input click
const triggerFileInput = () => {
  fileInput.value?.click()
}

// Handle file selection - like in destinations
const handlePhotoChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  console.log('handlePhotoChange called, file:', file)
  
  if (!file) {
    console.warn('No file selected')
    return
  }
  
  // Validate file size (2MB max)
  const maxSize = 2 * 1024 * 1024 // 2MB
  if (file.size > maxSize) {
    errors.value.photo = ['Ukuran file maksimal 2MB']
    return
  }
  
  // Clear previous errors
  errors.value.photo = []
  
  // Store file
  selectedFile.value = file
  console.log('File stored:', file.name, file.size)
  
  // Create preview
  const reader = new FileReader()
  reader.onload = (e) => {
    photoPreview.value = e.target?.result
    console.log('Preview created')
  }
  reader.readAsDataURL(file)
}

const clearPhoto = () => {
  photoFile.value = []
  photoPreview.value = null
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''  // Reset file input
  }
}

const fetchCategories = async () => {
  loading.value = true
  try {
    const response = await api.get('/categories')
    categories.value = response.data.categories || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  } finally {
    loading.value = false
  }
}

const openAddDialog = () => {
  isEditMode.value = false
  form.value = {
    id: null,
    name: '',
    description: '',
    icon: '',
    photo: ''
  }
  photoFile.value = []
  photoPreview.value = null
  selectedFile.value = null
  errors.value = { name: [], description: [], photo: [] }
  dialog.value = true
}

const openEditDialog = (category: any) => {
  isEditMode.value = true
  form.value = {
    id: category.id,
    name: category.name,
    description: category.description || '',
    icon: category.icon || '',
    photo: category.photo || ''
  }
  photoFile.value = []
  photoPreview.value = null
  selectedFile.value = null
  errors.value = { name: [], description: [], photo: [] }
  dialog.value = true
}

const closeDialog = () => {
  dialog.value = false
  form.value = {
    id: null,
    name: '',
    description: '',
    icon: '',
    photo: ''
  }
  photoFile.value = []
  photoPreview.value = null
  errors.value = { name: [], description: [], photo: [] }
}

const saveCategory = async () => {
  errors.value = { name: [], description: [], photo: [] }

  if (!form.value.name) {
    errors.value.name = ['Nama kategori harus diisi']
    return
  }

  saving.value = true
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    if (form.value.description) {
      formData.append('description', form.value.description)
    }
    if (form.value.icon) {
      formData.append('icon', form.value.icon)
    }
    
    // Debug: Check photoFile state
    console.log('photoFile.value:', photoFile.value)
    console.log('photoFile.value?.[0]:', photoFile.value?.[0])
    console.log('selectedFile.value:', selectedFile.value)
    
    // Use selectedFile instead of photoFile
    if (selectedFile.value) {
      console.log('Appending photo to FormData:', selectedFile.value.name, selectedFile.value.size)
      formData.append('photo', selectedFile.value)
    } else {
      console.warn('No photo file to upload')
    }
    
    // Debug: Log FormData contents
    console.log('FormData entries:')
    for (let pair of formData.entries()) {
      console.log(pair[0], pair[1])
    }

    if (isEditMode.value) {
      // Route is already POST /categories/{id}, no need for _method
      await api.post(`/categories/${form.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post('/categories', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    closeDialog()
    await fetchCategories()
  } catch (error: any) {
    console.error('Error saving category:', error)
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      alert(error.response?.data?.message || 'Gagal menyimpan kategori')
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = (category: any) => {
  categoryToDelete.value = category
  deleteDialog.value = true
}

const deleteCategory = async () => {
  if (!categoryToDelete.value) return

  deleting.value = true
  try {
    await api.delete(`/categories/${categoryToDelete.value.id}`)
    deleteDialog.value = false
    categoryToDelete.value = null
    await fetchCategories()
  } catch (error: any) {
    console.error('Error deleting category:', error)
    alert(error.response?.data?.message || 'Gagal menghapus kategori')
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
.admin-categories-page {
  background: #fafafa;
  min-height: 100vh;
}

.category-card {
  transition: transform 0.2s;
}

.category-card:hover {
  transform: translateY(-4px);
}

.category-image {
  position: relative;
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6));
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Photo upload styling */
.photo-upload-container {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}

.photo-preview-wrapper {
  flex-shrink: 0;
  width: 200px;
}

.photo-preview-card {
  border: 2px dashed #e0e0e0;
  background: #fafafa;
  overflow: hidden;
  transition: border-color 0.2s;
}

.photo-preview-card.has-image {
  border: 2px solid #1976d2;
  background: white;
}

.photo-placeholder {
  height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  text-align: center;
}

.photo-input-wrapper {
  flex: 1;
  min-width: 0;
}

@media (max-width: 600px) {
  .photo-upload-container {
    flex-direction: column;
  }

  .photo-preview-wrapper {
    width: 100%;
  }
}
</style>
