<template>
  <div class="edit-post-page">
    <v-container class="py-8" style="max-width: 935px;">
      <!-- Header Instagram-style -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex align-center px-6 py-4" style="border-bottom: 1px solid #dbdbdb;">
          <v-btn icon size="small" variant="text" @click="$router.back()" class="mr-2">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
          <span class="text-h6 font-weight-bold">Edit Postingan</span>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            :loading="loading"
            :disabled="!canSubmit"
            @click="submitUpdate"
          >
            <v-icon start>mdi-content-save</v-icon>
            Simpan Perubahan
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Loading State -->
      <div v-if="fetchingData" class="text-center py-12">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
        <p class="text-body-1 text-grey mt-4">Memuat data...</p>
      </div>

      <!-- Main Content -->
      <v-row v-else>
        <!-- Left Side - Image Upload -->
        <v-col cols="12" md="7">
          <v-card elevation="0" rounded="lg" class="overflow-hidden">
            <div class="image-upload-section">
              <!-- Image Preview Area -->
              <div v-if="previewImages.length > 0" class="preview-container">
                <v-carousel
                  v-model="currentImageIndex"
                  height="500"
                  hide-delimiters
                  show-arrows="hover"
                  class="instagram-carousel"
                >
                  <v-carousel-item
                    v-for="(image, index) in previewImages"
                    :key="index"
                  >
                    <div class="image-preview-wrapper">
                      <img
                        :src="image.url"
                        :alt="`Preview ${index + 1}`"
                        class="preview-image"
                        @error="handleImageError"
                      />
                      <v-btn
                        icon
                        size="small"
                        color="error"
                        class="delete-image-btn"
                        @click="removeImage(index)"
                      >
                        <v-icon>mdi-close</v-icon>
                      </v-btn>
                      <v-chip
                        v-if="image.isPrimary"
                        color="primary"
                        size="small"
                        class="primary-badge"
                      >
                        <v-icon start size="small">mdi-star</v-icon>
                        Foto Utama
                      </v-chip>
                      <v-btn
                        v-else
                        variant="tonal"
                        size="small"
                        class="set-primary-btn"
                        @click="setPrimaryImage(index)"
                      >
                        <v-icon start size="small">mdi-star-outline</v-icon>
                        Jadikan Utama
                      </v-btn>
                    </div>
                  </v-carousel-item>
                </v-carousel>

                <!-- Image Counter -->
                <div class="image-counter">
                  {{ currentImageIndex + 1 }} / {{ previewImages.length }}
                </div>
              </div>

              <!-- Upload Placeholder -->
              <div v-else class="upload-placeholder" @click="triggerFileInput">
                <v-icon size="80" color="grey-lighten-1">mdi-image-plus</v-icon>
                <h3 class="text-h5 mt-4 mb-2">Tambahkan Foto</h3>
                <p class="text-body-2 text-grey-darken-1">Upload hingga 5 foto lokasi</p>
                <v-btn color="primary" variant="flat" rounded="lg" class="mt-4" size="large">
                  <v-icon start>mdi-upload</v-icon>
                  Pilih dari Perangkat
                </v-btn>
              </div>

              <!-- Hidden File Input -->
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                multiple
                style="display: none"
                @change="handleFileSelect"
              />
            </div>

            <!-- Add More Photos Button -->
            <div v-if="previewImages.length > 0 && previewImages.length < 5" class="pa-4 text-center" style="border-top: 1px solid #dbdbdb;">
              <v-btn
                variant="outlined"
                color="primary"
                rounded="lg"
                @click="triggerFileInput"
              >
                <v-icon start>mdi-image-plus</v-icon>
                Tambah Foto ({{ previewImages.length }}/5)
              </v-btn>
            </div>
          </v-card>
        </v-col>

        <!-- Right Side - Form Details -->
        <v-col cols="12" md="5">
          <v-card elevation="0" rounded="lg" class="pa-6">
            <v-form ref="formRef" v-model="formValid">
              <!-- Pilih Kota -->
              <div class="mb-6">
                <label class="form-label">Pilih Kota</label>
                <v-select
                  v-model="form.activation_id"
                  :items="cityOptions"
                  item-title="city"
                  item-value="id"
                  placeholder="Pilih kota"
                  variant="outlined"
                  rounded="lg"
                  density="comfortable"
                  clearable
                  hide-details="auto"
                  class="mb-2"
                >
                  <template v-slot:prepend-inner>
                    <v-icon>mdi-map-marker</v-icon>
                  </template>
                </v-select>
              </div>

              <!-- Nama Destinasi -->
              <div class="mb-6">
                <label class="form-label">Nama Lokasi</label>
                <v-text-field
                  v-model="form.name"
                  placeholder="Contoh: Pantai Losari"
                  variant="outlined"
                  rounded="lg"
                  density="comfortable"
                  :rules="[rules.required]"
                  hide-details="auto"
                  class="mb-2"
                ></v-text-field>
              </div>

              <!-- Kategori -->
              <div class="mb-6">
                <label class="form-label">Kategori</label>
                <v-select
                  v-model="form.category_id"
                  :items="categories"
                  item-title="name"
                  item-value="id"
                  placeholder="Pilih kategori"
                  variant="outlined"
                  rounded="lg"
                  density="comfortable"
                  :rules="[rules.required]"
                  hide-details="auto"
                  class="mb-2"
                >
                  <template v-slot:prepend-inner>
                    <v-icon>mdi-tag</v-icon>
                  </template>
                </v-select>
              </div>

              <!-- Deskripsi -->
              <div class="mb-6">
                <label class="form-label">Ceritakan Pengalamanmu</label>
                <v-textarea
                  v-model="form.description"
                  placeholder="Bagikan cerita menarik tentang lokasi ini..."
                  variant="outlined"
                  rounded="lg"
                  rows="4"
                  :rules="[rules.required]"
                  hide-details="auto"
                  counter="500"
                  maxlength="500"
                  class="mb-2"
                ></v-textarea>
                <div class="text-caption text-grey mt-1">
                  {{ form.description?.length || 0 }}/500 karakter
                </div>
              </div>

              <!-- Location Picker -->
              <div class="mb-6">
                <label class="form-label d-flex align-center">
                  <v-icon class="mr-2">mdi-map-marker</v-icon>
                  Lokasi
                </label>

                <v-card variant="outlined" rounded="lg" class="pa-4 mb-3">
                  <div v-if="form.latitude && form.longitude" class="d-flex align-start">
                    <v-icon color="success" class="mr-2 mt-1">mdi-map-marker-check</v-icon>
                    <div class="flex-grow-1">
                      <div class="text-body-2 font-weight-medium">Lokasi Dipilih</div>
                      <div class="text-caption text-grey">
                        {{ form.latitude?.toFixed(6) }}, {{ form.longitude?.toFixed(6) }}
                      </div>
                      <div v-if="selectedAddress" class="text-caption text-primary mt-1">
                        <v-icon size="x-small" class="mr-1">mdi-map-marker-radius</v-icon>
                        {{ selectedAddress }}
                      </div>
                    </div>
                    <v-btn
                      icon
                      size="small"
                      variant="text"
                      @click="clearLocation"
                    >
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                  </div>
                  <div v-else class="text-center text-grey">
                    <v-icon size="32" class="mb-2">mdi-map-marker-outline</v-icon>
                    <div class="text-caption">Belum ada lokasi dipilih</div>
                  </div>
                </v-card>

                <v-btn
                  block
                  variant="outlined"
                  color="primary"
                  rounded="lg"
                  @click="openMapPicker"
                >
                  <v-icon start>mdi-map-search</v-icon>
                  {{ form.latitude ? 'Ubah Lokasi' : 'Pilih Lokasi di Peta' }}
                </v-btn>
              </div>

              <!-- Info Card -->
              <v-card color="blue-lighten-5" variant="flat" rounded="lg" class="pa-4">
                <div class="d-flex align-start">
                  <v-icon color="blue" class="mr-3">mdi-information-outline</v-icon>
                  <div>
                    <div class="text-subtitle-2 font-weight-bold mb-1">Informasi</div>
                    <ul class="text-caption text-grey-darken-2 pl-4">
                      <li>Foto baru akan menggantikan foto lama</li>
                      <li>Maksimal ukuran foto 2MB per file</li>
                      <li>Minimal 1 foto wajib ada</li>
                      <li>Pastikan semua data sudah benar</li>
                    </ul>
                  </div>
                </div>
              </v-card>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Map Picker Dialog -->
    <v-dialog v-model="mapDialog" max-width="900" scrollable>
      <v-card rounded="xl">
        <v-card-title class="d-flex align-center pa-4" style="border-bottom: 1px solid #dbdbdb;">
          <span class="text-h6 font-weight-bold">Pilih Lokasi di Peta</span>
          <v-spacer></v-spacer>
          <v-btn icon variant="text" @click="mapDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class="pa-0" style="height: 600px;">
          <ClientOnly>
            <MapPicker
              v-model="selectedMapLocation"
              @location-selected="handleLocationSelected"
            />
            <template #fallback>
              <div class="d-flex align-center justify-center" style="height: 100%;">
                <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
              </div>
            </template>
          </ClientOnly>
        </v-card-text>

        <v-card-actions class="pa-4" style="border-top: 1px solid #dbdbdb;">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="mapDialog = false">Batal</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :disabled="!selectedMapLocation"
            @click="confirmLocation"
          >
            Konfirmasi Lokasi
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Success Dialog -->
    <v-dialog v-model="successDialog" max-width="400" persistent>
      <v-card rounded="xl" class="pa-6">
        <div class="text-center">
          <v-icon size="80" color="success" class="mb-4">mdi-check-circle</v-icon>
          <h2 class="text-h5 font-weight-bold mb-3">Berhasil Diperbarui!</h2>
          <p class="text-body-1 text-grey-darken-1 mb-6">
            Perubahan destinasi berhasil disimpan
          </p>
          <v-btn color="primary" block variant="flat" rounded="lg" @click="goToMyPosts">
            {{ authStore.isAdmin || authStore.isCommunityAdmin ? 'Kelola Destinasi' : 'Kembali ke Postingan Saya' }}
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig, useHead } from '#app'
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: 'auth',
  layout: 'solid'
})

const router = useRouter()
const route = useRoute()
const { api } = useApi()
const config = useRuntimeConfig()
const authStore = useAuthStore()

const destinationId = computed(() => route.params.id)

// Form state
const formRef = ref(null)
const formValid = ref(false)
const loading = ref(false)
const fetchingData = ref(true)
const fileInput = ref(null)
const currentImageIndex = ref(0)
const mapDialog = ref(false)
const successDialog = ref(false)

const form = ref({
  name: '',
  category_id: null,
  activation_id: null,
  description: '',
  latitude: null,
  longitude: null,
})

useHead(() => ({
  title: `Edit: ${form.value.name || 'Destinasi'}`
}))

const previewImages = ref<Array<{ file?: File; url: string; isPrimary: boolean; id?: number; isExisting?: boolean }>>([])
const categories = ref([])
const activations = ref([])
const imagesToDelete = ref<number[]>([])

// Map picker state
const selectedMapLocation = ref<{ lat: number; lng: number; address?: string } | null>(null)
const selectedAddress = ref('')

// Validation rules
const rules = {
  required: (v: any) => !!v || 'Field ini wajib diisi',
}

// Computed - City options from activations
const cityOptions = computed(() => {
  if (!activations.value || activations.value.length === 0) return []
  
  // Get unique cities from activations
  const cities = activations.value
    .filter((a: any) => a.city)
    .map((a: any) => ({
      id: a.id,
      city: a.city,
      name: a.name
    }))
  
  // Remove duplicates based on city name, keep first occurrence
  const uniqueCities = cities.filter((city: any, index: number, self: any[]) =>
    index === self.findIndex((c: any) => c.city === city.city)
  )
  
  return uniqueCities
})

// Computed
const canSubmit = computed(() => {
  return (
    formValid.value &&
    previewImages.value.length > 0 &&
    form.value.latitude &&
    form.value.longitude
  )
})

// Methods
const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const fetchDestination = async () => {
  fetchingData.value = true
  try {
    const response = await api.get(`/destinations/${destinationId.value}`)
    const data = response.data.destination

    // Fill form
    form.value.name = data.name
    form.value.category_id = data.category_id
    form.value.activation_id = data.activation_id
    form.value.description = data.description
    form.value.latitude = parseFloat(data.latitude)
    form.value.longitude = parseFloat(data.longitude)

    // Load existing images
    if (data.photos && data.photos.length > 0) {
      console.log('Loading existing photos:', data.photos)
      previewImages.value = data.photos.map((photo: any) => {
        // Try different possible field names
        const photoPath = photo.path || photo.photo_path || photo.url
        const imageUrl = getImageUrl(photoPath)
        console.log('Photo URL:', imageUrl)
        return {
          id: photo.id,
          url: imageUrl,
          isPrimary: photo.is_primary || false,
          isExisting: true
        }
      })
    }
  } catch (error: any) {
    console.error('Error fetching destination:', error)
    alert('Gagal memuat data destinasi')
    router.back()
  } finally {
    fetchingData.value = false
  }
}

const handleImageError = (event: Event) => {
  console.error('Image failed to load:', (event.target as HTMLImageElement).src)
  // Set a placeholder image
  (event.target as HTMLImageElement).src = 'https://via.placeholder.com/500x500?text=Image+Not+Found'
}

const triggerFileInput = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const files = Array.from(target.files || [])

  const remainingSlots = 5 - previewImages.value.length
  const filesToAdd = files.slice(0, remainingSlots)

  // Validate file size (2MB max to match backend)
  const maxSize = 2 * 1024 * 1024 // 2MB in bytes (2048 KB)
  const invalidFiles: string[] = []

  filesToAdd.forEach((file, index) => {
    // Check file size
    if (file.size > maxSize) {
      invalidFiles.push(file.name)
      return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
      const isPrimary = previewImages.value.length === 0 && index === 0
      previewImages.value.push({
        file,
        url: e.target?.result as string,
        isPrimary,
        isExisting: false
      })
    }
    reader.readAsDataURL(file)
  })

  // Show error message if there are invalid files
  if (invalidFiles.length > 0) {
    const fileList = invalidFiles.join(', ')
    alert(`File berikut melebihi ukuran maksimal 2MB:\n${fileList}\n\nSilakan kompres foto terlebih dahulu atau gunakan foto dengan resolusi lebih kecil.`)
  }

  // Reset input
  if (target) target.value = ''
}

const removeImage = (index: number) => {
  const image = previewImages.value[index]

  // Track existing images to delete
  if (image.isExisting && image.id) {
    imagesToDelete.value.push(image.id)
  }

  const wasRemovingPrimary = image.isPrimary
  previewImages.value.splice(index, 1)

  // If we removed the primary image, set the first image as primary
  if (wasRemovingPrimary && previewImages.value.length > 0) {
    previewImages.value[0].isPrimary = true
  }

  // Adjust current index if needed
  if (currentImageIndex.value >= previewImages.value.length && currentImageIndex.value > 0) {
    currentImageIndex.value = previewImages.value.length - 1
  }
}

const setPrimaryImage = (index: number) => {
  previewImages.value.forEach((img, i) => {
    img.isPrimary = i === index
  })
}

const openMapPicker = () => {
  // Set initial location if already selected
  if (form.value.latitude && form.value.longitude) {
    selectedMapLocation.value = {
      lat: form.value.latitude,
      lng: form.value.longitude
    }
  }
  mapDialog.value = true
}

const handleLocationSelected = (location: { lat: number; lng: number; address?: string }) => {
  selectedMapLocation.value = location
  if (location.address) {
    selectedAddress.value = location.address
  }
}

const confirmLocation = () => {
  if (selectedMapLocation.value) {
    form.value.latitude = selectedMapLocation.value.lat
    form.value.longitude = selectedMapLocation.value.lng
  }
  mapDialog.value = false
}

const clearLocation = () => {
  form.value.latitude = null
  form.value.longitude = null
  selectedMapLocation.value = null
  selectedAddress.value = ''
}

const submitUpdate = async () => {
  if (!canSubmit.value) return

  loading.value = true

  try {
    const formData = new FormData()
    formData.append('_method', 'PUT') // Laravel method spoofing for multipart
    formData.append('name', form.value.name)
    formData.append('category_id', form.value.category_id?.toString() || '')
    if (form.value.activation_id) {
      formData.append('activation_id', form.value.activation_id.toString())
    }
    formData.append('description', form.value.description)
    formData.append('latitude', form.value.latitude?.toString() || '')
    formData.append('longitude', form.value.longitude?.toString() || '')

    // Add new images
    const newImages = previewImages.value.filter(img => !img.isExisting && img.file)
    newImages.forEach((image) => {
      if (image.file) {
        formData.append('photos[]', image.file)
      }
    })

    // Find primary image
    const primaryIndex = previewImages.value.findIndex(img => img.isPrimary)
    if (primaryIndex !== -1) {
      // If primary is existing image, send its ID, otherwise send the index of new images
      const primaryImage = previewImages.value[primaryIndex]
      if (primaryImage.isExisting && primaryImage.id) {
        formData.append('primary_photo_id', primaryImage.id.toString())
      } else {
        // Calculate index in new images array
        const newImageIndex = previewImages.value
          .slice(0, primaryIndex + 1)
          .filter(img => !img.isExisting).length - 1
        if (newImageIndex >= 0) {
          formData.append('primary_photo_index', newImageIndex.toString())
        }
      }
    }

    // Add images to delete
    if (imagesToDelete.value.length > 0) {
      formData.append('delete_photos', JSON.stringify(imagesToDelete.value))
    }

    // Log FormData for debugging
    console.log('Submitting update form data:')
    for (let pair of formData.entries()) {
      console.log(pair[0], pair[1])
    }

    await api.post(`/destinations/${destinationId.value}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    successDialog.value = true
  } catch (error: any) {
    console.error('Error updating destination:', error)
    console.error('Error response:', error.response?.data)

    // Show detailed error messages
    let errorMessage = 'Gagal memperbarui postingan. Silakan coba lagi.'

    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }

    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      const errorList = Object.entries(errors)
        .map(([field, messages]: [string, any]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
        .join('\n')
      errorMessage = `Validation Error:\n${errorList}`
    }

    alert(errorMessage)
  } finally {
    loading.value = false
  }
}

const goToMyPosts = () => {
  const authStore = useAuthStore()
  // Redirect admin and community admin to manage page, regular users to my-posts
  if (authStore.isAdmin || authStore.isCommunityAdmin) {
    router.push('/manage/destinations')
  } else {
    router.push('/destinations/my-posts')
  }
}

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.categories || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Fetch activations
const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

onMounted(() => {
  fetchDestination()
  fetchCategories()
  fetchActivations()
})
</script>

<style scoped>
.edit-post-page {
  background: #fafafa;
  min-height: 100vh;
  padding-bottom: 40px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 8px;
  color: #262626;
}

/* Image Upload Section */
.image-upload-section {
  background: #ffffff;
  border: 1px solid #dbdbdb;
  border-radius: 8px;
  overflow: hidden;
}

.preview-container {
  position: relative;
}

.instagram-carousel {
  border-radius: 0;
}

.image-preview-wrapper {
  position: relative;
  width: 100%;
  height: 500px;
  background: #000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.delete-image-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 10;
  background: rgba(0, 0, 0, 0.6) !important;
}

.primary-badge {
  position: absolute;
  top: 16px;
  left: 16px;
  z-index: 10;
}

.set-primary-btn {
  position: absolute;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}

.image-counter {
  position: absolute;
  bottom: 16px;
  right: 16px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  z-index: 10;
}

.upload-placeholder {
  height: 500px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: #fafafa;
  cursor: pointer;
  transition: background 0.3s ease;
}

.upload-placeholder:hover {
  background: #f0f0f0;
}

/* Responsive */
@media (max-width: 960px) {
  .image-preview-wrapper {
    height: 350px;
  }

  .upload-placeholder {
    height: 350px;
  }
}

@media (max-width: 600px) {
  .image-preview-wrapper {
    height: 300px;
  }

  .upload-placeholder {
    height: 300px;
  }
}
</style>
