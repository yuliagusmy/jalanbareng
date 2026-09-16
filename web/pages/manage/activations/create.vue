<template>
  <div class="admin-page">
    <v-container class="py-8">
      <v-row class="mb-6">
        <v-col cols="12" class="d-flex align-center">
          <v-btn
            icon
            variant="text"
            to="/manage/activations"
            class="mr-4"
            color="primary"
          >
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
          <div>
            <h1 class="text-h4 font-weight-bold">Buat Aktivasi Baru</h1>
            <p class="text-body-2 text-grey mb-0">Isi formulir di bawah untuk membuat aktivasi baru</p>
          </div>
        </v-col>
      </v-row>

      <v-form ref="form" v-model="valid" @submit.prevent="submit">
        <v-card elevation="0" rounded="lg">
          <v-tabs v-model="tab" color="primary" bg-color="grey-lighten-4">
            <v-tab value="basic" class="text-capitalize">Info Dasar</v-tab>
            <v-tab value="hero" class="text-capitalize">Hero Section</v-tab>
            <v-tab value="content" class="text-capitalize">Konten</v-tab>
            <v-tab value="cta" class="text-capitalize">CTA & Kontak</v-tab>
            <v-tab value="settings" class="text-capitalize">Pengaturan</v-tab>
          </v-tabs>
          <v-divider></v-divider>

          <v-card-text class="pa-6">
            <v-window v-model="tab">
              <!-- Basic Info -->
              <v-window-item value="basic">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.name"
                      label="Nama Aktivasi"
                      :rules="[v => !!v || 'Nama wajib diisi']"
                      required
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.slug"
                      label="Slug (Opsional)"
                      hint="Akan digenerate otomatis jika kosong"
                      persistent-hint
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="formData.category"
                      label="Kategori"
                      :items="categories"
                      item-title="text"
                      item-value="value"
                      required
                      variant="outlined"
                      density="comfortable"
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6" v-if="formData.category === 'city'">
                    <v-text-field
                      v-model="formData.city"
                      label="Kota"
                      variant="outlined"
                      density="comfortable"
                      :loading="geocoding"
                      :hint="cityCoordinates ? `Koordinat: ${Number(cityCoordinates.lat).toFixed(4)}, ${Number(cityCoordinates.lng).toFixed(4)}` : ''"
                      persistent-hint
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.short_title"
                      label="Judul Singkat"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.tagline"
                      label="Tagline"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Hero Section -->
              <v-window-item value="hero">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="formData.hero_title"
                      label="Judul Hero"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.hero_subtitle"
                      label="Subjudul Hero"
                      rows="3"
                      variant="outlined"
                      density="comfortable"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-file-input
                      v-model="heroImageFile"
                      label="Gambar Hero"
                      accept="image/*"
                      prepend-icon="mdi-image"
                      variant="outlined"
                      density="comfortable"
                    ></v-file-input>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-file-input
                      v-model="iconFile"
                      label="Icon"
                      accept="image/*"
                      prepend-icon="mdi-emoticon"
                      variant="outlined"
                      density="comfortable"
                    ></v-file-input>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-color-picker
                      v-model="formData.color_theme"
                      mode="hex"
                      hide-inputs
                      show-swatches
                      elevation="0"
                      border
                      rounded="lg"
                      class="mb-4"
                    ></v-color-picker>
                    <v-text-field
                      v-model="formData.color_theme"
                      label="Tema Warna (Hex)"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Content -->
              <v-window-item value="content">
                <v-row>
                  <v-col cols="12">
                    <v-textarea
                      v-model="formData.description"
                      label="Deskripsi Lengkap"
                      rows="10"
                      variant="outlined"
                      density="comfortable"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- CTA & Contact -->
              <v-window-item value="cta">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.cta_primary_label"
                      label="Label CTA Utama"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.cta_primary_url"
                      label="URL CTA Utama"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.cta_secondary_label"
                      label="Label CTA Sekunder"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.cta_secondary_url"
                      label="URL CTA Sekunder"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-divider class="my-4"></v-divider>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.contact_person"
                      label="Contact Person"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="formData.contact_phone"
                      label="Nomor Telepon"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="formData.social_instagram"
                      label="Instagram Username"
                      prepend-inner-icon="mdi-instagram"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="formData.social_twitter"
                      label="Twitter Username"
                      prepend-inner-icon="mdi-twitter"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="formData.social_linktree"
                      label="Linktree URL"
                      prepend-inner-icon="mdi-link-variant"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Settings -->
              <v-window-item value="settings">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-switch
                      v-model="formData.is_active"
                      label="Aktifkan Aktivasi"
                      color="success"
                      inset
                    ></v-switch>
                    <v-switch
                      v-model="formData.is_featured"
                      label="Jadikan Unggulan"
                      color="warning"
                      inset
                    ></v-switch>
                  </v-col>
                  <v-col cols="12" md="6">
                    <h3 class="text-subtitle-1 font-weight-bold mb-2">Tampilkan Section</h3>
                    <v-checkbox
                      v-model="formData.show_gallery"
                      label="Gallery"
                      density="compact"
                      hide-details
                    ></v-checkbox>
                    <v-checkbox
                      v-model="formData.show_testimonials"
                      label="Testimonial"
                      density="compact"
                      hide-details
                    ></v-checkbox>
                    <v-checkbox
                      v-model="formData.show_faq"
                      label="FAQ"
                      density="compact"
                      hide-details
                    ></v-checkbox>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model.number="formData.sort_order"
                      label="Urutan Tampilan"
                      type="number"
                      variant="outlined"
                      density="comfortable"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>
            </v-window>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn variant="text" to="/manage/activations" size="large">Batal</v-btn>
            <v-btn color="primary" type="submit" :loading="loading" variant="flat" size="large">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>

      <!-- Error Snackbar -->
      <v-snackbar
        v-model="errorSnackbar"
        :timeout="6000"
        color="error"
        location="top"
        multi-line
      >
        <div class="d-flex align-start">
          <v-icon class="mr-2">mdi-alert-circle</v-icon>
          <div>
            <div class="font-weight-bold mb-1">{{ errorTitle }}</div>
            <div class="text-body-2" style="white-space: pre-line">{{ errorMessage }}</div>
          </div>
        </div>
        <template v-slot:actions>
          <v-btn
            variant="text"
            @click="errorSnackbar = false"
          >
            Tutup
          </v-btn>
        </template>
      </v-snackbar>
    </v-container>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'solid',
  middleware: ['auth', 'admin']
})

const api = useApi()
const router = useRouter()

const tab = ref('basic')
const valid = ref(false)
const loading = ref(false)
const heroImageFile = ref<File | null>(null)
const iconFile = ref<File | null>(null)

// Error snackbar
const errorSnackbar = ref(false)
const errorTitle = ref('')
const errorMessage = ref('')

const showError = (title: string, message: string) => {
  errorTitle.value = title
  errorMessage.value = message
  errorSnackbar.value = true
}

const categories = [
  { text: 'Aktivasi Kota', value: 'city' },
  { text: 'Aktivasi Tematik', value: 'theme' },
  { text: 'Ruang Kreatif', value: 'space' },
  { text: 'Lainnya', value: 'other' },
]

const formData = reactive({
  name: '',
  slug: '',
  category: 'city',
  city: '',
  city_latitude: null as number | null,
  city_longitude: null as number | null,
  short_title: '',
  tagline: '',
  hero_title: '',
  hero_subtitle: '',
  color_theme: '#1976D2',
  description: '',
  cta_primary_label: '',
  cta_primary_url: '',
  cta_secondary_label: '',
  cta_secondary_url: '',
  contact_person: '',
  contact_phone: '',
  social_instagram: '',
  social_twitter: '',
  social_linktree: '',
  is_active: true,
  is_featured: false,
  show_gallery: true,
  show_testimonials: true,
  show_faq: true,
  sort_order: 0,
})

const geocoding = ref(false)
const cityCoordinates = computed(() => {
  if (formData.city_latitude && formData.city_longitude) {
    return { lat: formData.city_latitude, lng: formData.city_longitude }
  }
  return null
})

// Watch category changes to clear city data when not 'city'
watch(() => formData.category, (newCategory) => {
  if (newCategory !== 'city') {
    formData.city = ''
    formData.city_latitude = null
    formData.city_longitude = null
  }
})

// Geocode city when it changes
let geocodeTimeout: NodeJS.Timeout | null = null
watch(() => formData.city, async (newCity) => {
  // Clear previous timeout
  if (geocodeTimeout) {
    clearTimeout(geocodeTimeout)
  }
  
  if (!newCity || newCity.length < 3) {
    formData.city_latitude = null
    formData.city_longitude = null
    return
  }

  // Debounce geocoding
  geocodeTimeout = setTimeout(async () => {
    geocoding.value = true
    try {
      const config = useRuntimeConfig()
      const apiKey = config.public.googleMapsApiKey
      
      const searchAddress = newCity.includes('Indonesia') ? newCity : `${newCity}, Indonesia`
      const response = await fetch(
        `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(searchAddress)}&key=${apiKey}`
      )
      const data = await response.json()
      
      if (data.results && data.results[0]) {
        formData.city_latitude = data.results[0].geometry.location.lat
        formData.city_longitude = data.results[0].geometry.location.lng
      } else {
        formData.city_latitude = null
        formData.city_longitude = null
      }
    } catch (error) {
      console.error('Geocoding error:', error)
      formData.city_latitude = null
      formData.city_longitude = null
    } finally {
      geocoding.value = false
    }
  }, 500)
})

const submit = async () => {
  if (!valid.value) return
  
  loading.value = true
  try {
    const data = new FormData()
    
    // Append all fields
    Object.keys(formData).forEach(key => {
      const value = formData[key as keyof typeof formData]
      if (value !== null && value !== undefined) {
        // Handle boolean values
        if (typeof value === 'boolean') {
          data.append(key, value ? '1' : '0')
        } else {
          data.append(key, String(value))
        }
      }
    })
    
    // Append files
    if (heroImageFile.value) {
      data.append('hero_image', heroImageFile.value)
    }
    if (iconFile.value) {
      data.append('icon', iconFile.value)
    }
    
    await api.post('/activations', data, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    router.push('/manage/activations')
  } catch (error: any) {
    console.error('Error creating activation:', error)
    
    // Show user-friendly error message
    let title = 'Gagal Membuat Aktivasi'
    let message = 'Terjadi kesalahan saat membuat aktivasi. Silakan coba lagi.'
    
    if (error.response?.data?.message) {
      message = error.response.data.message
    }
    
    // Handle validation errors
    if (error.response?.data?.errors) {
      title = 'Validasi Error'
      const errors = error.response.data.errors
      const errorList = Object.entries(errors)
        .map(([field, messages]: [string, any]) => {
          const fieldName = field.replace('_', ' ').replace(/\b\w/g, (l: string) => l.toUpperCase())
          const errorMessages = Array.isArray(messages) ? messages.join(', ') : messages
          return `• ${fieldName}: ${errorMessages}`
        })
        .join('\n')
      message = errorList
    }
    
    showError(title, message)
  } finally {
    loading.value = false
  }
}
</script>
