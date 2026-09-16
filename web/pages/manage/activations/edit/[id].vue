<template>
  <div class="admin-page">
    <v-container class="py-8">
      <v-row class="mb-6">
        <v-col cols="12" class="d-flex align-center">
          <v-btn icon variant="text" to="/manage/activations" class="mr-4" color="primary">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
          <div>
            <h1 class="text-h4 font-weight-bold">Edit Aktivasi</h1>
            <p class="text-body-2 text-grey mb-0">Edit detail dan konten aktivasi</p>
          </div>
          <v-spacer></v-spacer>
          <v-btn v-if="activation" :to="`/aktivasi/${activation.slug}`" target="_blank" variant="outlined"
            prepend-icon="mdi-open-in-new" color="primary" class="mr-2">
            Lihat Halaman
          </v-btn>
        </v-col>
      </v-row>

      <v-form ref="form" v-model="valid" @submit.prevent="submit" v-if="!loading">
        <v-card elevation="0" rounded="lg">
          <v-tabs v-model="tab" color="primary" show-arrows bg-color="grey-lighten-4">
            <v-tab value="basic" class="text-capitalize">Info Dasar</v-tab>
            <v-tab value="hero" class="text-capitalize">Hero Section</v-tab>
            <v-tab value="content" class="text-capitalize">Konten</v-tab>
            <v-tab value="cta" class="text-capitalize">CTA & Kontak</v-tab>
            <v-tab value="settings" class="text-capitalize">Pengaturan</v-tab>
            <v-tab value="gallery" class="text-capitalize">Gallery</v-tab>
            <v-tab value="faq" class="text-capitalize">FAQ</v-tab>
            <v-tab value="testimonials" class="text-capitalize">Testimoni</v-tab>
          </v-tabs>
          <v-divider></v-divider>

          <v-card-text class="pa-6">
            <v-window v-model="tab">
              <!-- Basic Info -->
              <v-window-item value="basic">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.name" label="Nama Aktivasi"
                      :rules="[v => !!v || 'Nama wajib diisi']" required variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.slug" label="Slug"
                      hint="Biarkan kosong untuk auto-generate dari nama" persistent-hint variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select v-model="formData.category" label="Kategori" :items="categories" item-title="text"
                      item-value="value" required variant="outlined" density="comfortable"></v-select>
                  </v-col>
                  <v-col cols="12" md="6" v-if="formData.category === 'city'">
                    <v-text-field v-model="formData.city" label="Kota" variant="outlined"
                      density="comfortable"
                      :loading="geocoding"
                      :hint="cityCoordinates ? `Koordinat: ${Number(cityCoordinates.lat).toFixed(4)}, ${Number(cityCoordinates.lng).toFixed(4)}` : ''"
                      persistent-hint
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="formData.short_title" label="Judul Singkat" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="formData.tagline" label="Tagline" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Hero Section -->
              <v-window-item value="hero">
                <v-row>
                  <v-col cols="12">
                    <v-text-field v-model="formData.hero_title" label="Judul Hero" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea v-model="formData.hero_subtitle" label="Subjudul Hero" rows="3" variant="outlined"
                      density="comfortable"></v-textarea>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-card variant="outlined" class="pa-2 mb-2" v-if="activation?.hero_image">
                      <v-img :src="`${apiBase}/storage/${activation.hero_image}`" height="150" cover
                        class="rounded"></v-img>
                    </v-card>
                    <v-file-input v-model="heroImageFile" label="Ganti Gambar Hero" accept="image/*"
                      prepend-icon="mdi-image" variant="outlined" density="comfortable"></v-file-input>
                  </v-col>
                  <v-col cols="12" md="6">
                    <div class="d-flex align-center mb-4" v-if="activation?.icon">
                      <v-avatar size="64" class="mr-4 border">
                        <v-img :src="`${apiBase}/storage/${activation.icon}`"></v-img>
                      </v-avatar>
                      <div class="text-caption text-grey">Icon saat ini</div>
                    </div>
                    <v-file-input v-model="iconFile" label="Ganti Icon" accept="image/*" prepend-icon="mdi-emoticon"
                      variant="outlined" density="comfortable"></v-file-input>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-color-picker v-model="formData.color_theme" mode="hex" hide-inputs show-swatches elevation="0"
                      border rounded="lg" class="mb-4"></v-color-picker>
                    <v-text-field v-model="formData.color_theme" label="Tema Warna (Hex)" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Content -->
              <v-window-item value="content">
                <v-row>
                  <v-col cols="12">
                    <v-textarea v-model="formData.description" label="Deskripsi Lengkap" rows="10" variant="outlined"
                      density="comfortable"></v-textarea>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- CTA & Contact -->
              <v-window-item value="cta">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.cta_primary_label" label="Label CTA Utama" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.cta_primary_url" label="URL CTA Utama" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.cta_secondary_label" label="Label CTA Sekunder" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.cta_secondary_url" label="URL CTA Sekunder" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-divider class="my-4"></v-divider>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.contact_person" label="Contact Person" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model="formData.contact_phone" label="Nomor Telepon" variant="outlined"
                      density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="formData.social_instagram" label="Instagram Username"
                      prepend-inner-icon="mdi-instagram" variant="outlined" density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="formData.social_twitter" label="Twitter Username"
                      prepend-inner-icon="mdi-twitter" variant="outlined" density="comfortable"></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field v-model="formData.social_linktree" label="Linktree URL"
                      prepend-inner-icon="mdi-link-variant" variant="outlined" density="comfortable"></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Settings -->
              <v-window-item value="settings">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-switch v-model="formData.is_active" label="Aktifkan Aktivasi" color="success" inset></v-switch>
                    <v-switch v-model="formData.is_featured" label="Jadikan Unggulan" color="warning" inset></v-switch>
                  </v-col>
                  <v-col cols="12" md="6">
                    <h3 class="text-subtitle-1 font-weight-bold mb-2">Tampilkan Section</h3>
                    <v-checkbox v-model="formData.show_schedule" label="Jadwal / Agenda" density="compact"
                      hide-details></v-checkbox>
                    <v-checkbox v-model="formData.show_gallery" label="Gallery" density="compact"
                      hide-details></v-checkbox>
                    <v-checkbox v-model="formData.show_testimonials" label="Testimoni" density="compact"
                      hide-details></v-checkbox>
                    <v-checkbox v-model="formData.show_faq" label="FAQ" density="compact" hide-details></v-checkbox>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-text-field v-model.number="formData.sort_order" label="Urutan Tampilan" type="number"
                      variant="outlined" density="comfortable"></v-text-field>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Gallery Management -->
              <v-window-item value="gallery">
                <v-row>
                  <v-col cols="12">
                    <div class="d-flex justify-space-between align-center mb-4">
                      <h3 class="text-h6">Daftar Media</h3>
                      <v-btn color="primary" prepend-icon="mdi-plus" size="small" @click="openMediaDialog">Tambah
                        Media</v-btn>
                    </div>

                    <v-row>
                      <v-col v-for="item in activation?.media" :key="item.id" cols="12" sm="4" md="3">
                        <v-card elevation="0" border rounded="lg">
                          <v-img v-if="item.type === 'photo'" :src="`${apiBase}/storage/${item.file_url}`" height="150"
                            cover class="bg-grey-lighten-4"></v-img>
                          <div v-else class="d-flex align-center justify-center bg-grey-lighten-4"
                            style="height: 150px;">
                            <v-icon size="48" color="red">mdi-youtube</v-icon>
                          </div>
                          <v-card-actions class="pa-2">
                            <v-spacer></v-spacer>
                            <v-btn icon size="small" color="error" variant="text" @click="deleteMedia(item.id)">
                              <v-icon>mdi-delete</v-icon>
                            </v-btn>
                          </v-card-actions>
                        </v-card>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- FAQ Management -->
              <v-window-item value="faq">
                <v-row>
                  <v-col cols="12">
                    <div class="d-flex justify-space-between align-center mb-4">
                      <h3 class="text-h6">Daftar FAQ</h3>
                      <v-btn color="primary" prepend-icon="mdi-plus" size="small" @click="openFaqDialog()">Tambah
                        FAQ</v-btn>
                    </div>

                    <v-expansion-panels variant="outlined" class="rounded-lg">
                      <v-expansion-panel v-for="faq in activation?.faqs" :key="faq.id">
                        <v-expansion-panel-title>
                          {{ faq.question }}
                        </v-expansion-panel-title>
                        <v-expansion-panel-text>
                          <div class="mb-4 text-body-2">{{ faq.answer }}</div>
                          <div class="d-flex justify-end ga-2">
                            <v-btn size="small" color="primary" variant="tonal" @click="openFaqDialog(faq)">Edit</v-btn>
                            <v-btn size="small" color="error" variant="tonal" @click="deleteFaq(faq.id)">Hapus</v-btn>
                          </div>
                        </v-expansion-panel-text>
                      </v-expansion-panel>
                    </v-expansion-panels>
                  </v-col>
                </v-row>
              </v-window-item>

              <!-- Testimonials Management -->
              <v-window-item value="testimonials">
                <v-row>
                  <v-col cols="12">
                    <div class="d-flex justify-space-between align-center mb-4">
                      <h3 class="text-h6">Daftar Testimoni</h3>
                      <v-btn color="primary" prepend-icon="mdi-plus" size="small"
                        @click="openTestimonialDialog()">Tambah Testimoni</v-btn>
                    </div>

                    <v-list class="bg-transparent">
                      <v-list-item v-for="item in activation?.testimonials" :key="item.id"
                        class="mb-3 border rounded-lg bg-white">
                        <template v-slot:prepend>
                          <v-avatar v-if="item.author_photo" size="48">
                            <v-img :src="`${apiBase}/storage/${item.author_photo}`" cover></v-img>
                          </v-avatar>
                          <v-avatar v-else color="primary" size="48">
                            <span class="text-h6 text-white">{{ item.author_name.charAt(0) }}</span>
                          </v-avatar>
                        </template>
                        <v-list-item-title class="font-weight-bold mb-1">{{ item.author_name }}</v-list-item-title>
                        <v-list-item-subtitle class="text-wrap text-body-2 font-italic">"{{ item.quote
                          }}"</v-list-item-subtitle>
                        <template v-slot:append>
                          <div class="d-flex ga-1">
                            <v-btn icon size="small" variant="text" color="primary"
                              @click="openTestimonialDialog(item)">
                              <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon size="small" variant="text" color="error" @click="deleteTestimonial(item.id)">
                              <v-icon>mdi-delete</v-icon>
                            </v-btn>
                          </div>
                        </template>
                      </v-list-item>
                    </v-list>
                  </v-col>
                </v-row>
              </v-window-item>

            </v-window>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="pa-4">
            <v-spacer></v-spacer>
            <v-btn variant="text" to="/manage/activations" size="large">Kembali</v-btn>
            <v-btn color="primary" type="submit" :loading="saving" variant="flat" size="large">Simpan Perubahan</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>

      <v-skeleton-loader v-else type="card, article" class="bg-transparent"></v-skeleton-loader>

      <!-- Dialogs -->
      <!-- Media Dialog -->
      <v-dialog v-model="mediaDialog" max-width="500">
        <v-card rounded="xl">
          <v-card-title class="pa-4">Tambah Media</v-card-title>
          <v-card-text>
            <v-select v-model="mediaForm.type" label="Tipe Media" :items="['photo', 'youtube']" variant="outlined"
              density="comfortable"></v-select>
            <v-file-input v-if="mediaForm.type === 'photo'" v-model="mediaFile" label="File Foto" accept="image/*"
              variant="outlined" density="comfortable"></v-file-input>
            <v-text-field v-if="mediaForm.type === 'youtube'" v-model="mediaForm.external_url" label="URL YouTube"
              variant="outlined" density="comfortable"></v-text-field>
            <v-text-field v-model="mediaForm.description" label="Deskripsi" variant="outlined"
              density="comfortable"></v-text-field>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="mediaDialog = false">Batal</v-btn>
            <v-btn color="primary" variant="flat" @click="submitMedia" :loading="mediaLoading">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- FAQ Dialog -->
      <v-dialog v-model="faqDialog" max-width="500">
        <v-card rounded="xl">
          <v-card-title class="pa-4">{{ editingFaq ? 'Edit' : 'Tambah' }} FAQ</v-card-title>
          <v-card-text>
            <v-text-field v-model="faqForm.question" label="Pertanyaan" variant="outlined"
              density="comfortable"></v-text-field>
            <v-textarea v-model="faqForm.answer" label="Jawaban" variant="outlined" density="comfortable"
              rows="3"></v-textarea>
            <v-text-field v-model.number="faqForm.sort_order" label="Urutan" type="number" variant="outlined"
              density="comfortable"></v-text-field>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="faqDialog = false">Batal</v-btn>
            <v-btn color="primary" variant="flat" @click="submitFaq" :loading="faqLoading">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <!-- Testimonial Dialog -->
      <v-dialog v-model="testimonialDialog" max-width="500">
        <v-card rounded="xl">
          <v-card-title class="pa-4">{{ editingTestimonial ? 'Edit' : 'Tambah' }} Testimoni</v-card-title>
          <v-card-text>
            <v-text-field v-model="testimonialForm.author_name" label="Nama Penulis" variant="outlined"
              density="comfortable"></v-text-field>
            <v-textarea v-model="testimonialForm.quote" label="Kutipan" variant="outlined" density="comfortable"
              rows="3"></v-textarea>
            <v-file-input v-model="testimonialPhotoFile" label="Foto Penulis (Opsional)" accept="image/*"
              variant="outlined" density="comfortable"></v-file-input>
            <v-text-field v-model.number="testimonialForm.sort_order" label="Urutan" type="number" variant="outlined"
              density="comfortable"></v-text-field>
          </v-card-text>
          <v-card-actions class="pa-4 pt-0">
            <v-spacer></v-spacer>
            <v-btn variant="text" @click="testimonialDialog = false">Batal</v-btn>
            <v-btn color="primary" variant="flat" @click="submitTestimonial"
              :loading="testimonialLoading">Simpan</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

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
const route = useRoute()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const activation = ref<any>(null)
const loading = ref(true)
const saving = ref(false)
const tab = ref('basic')
const valid = ref(false)
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
  show_schedule: true,
  show_gallery: true,
  show_testimonials: true,
  show_faq: true,
  sort_order: 0,
  city_latitude: null as number | null,
  city_longitude: null as number | null,
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

// Fetch Data
const fetchActivation = async () => {
  loading.value = true
  try {
    // We need to fetch by ID, but the public API uses slug. 
    // However, for admin edit, we might want to use ID if available or fetch by slug then use ID.
    // The route param is ID.
    // But wait, the public API `show` uses slug.
    // I need to check if I implemented `show` to accept ID or if I need a separate admin endpoint.
    // The `ActivationController::show` uses `$slug`.
    // But for admin editing, usually we use ID.
    // Let's try to fetch by ID using a query param or just assume the controller handles it if I pass ID to a specific endpoint?
    // Actually, I didn't create a specific `show` for ID.
    // I'll fetch the list and find it, or just use the public API if I can get the slug.
    // But I only have ID from the URL.
    // I should probably update the controller to allow finding by ID or Slug, or just use the list endpoint with a filter.
    // For now, let's try to fetch all and find, or assume the ID is passed.
    // Wait, I can use the `index` endpoint with a filter if needed, but that's inefficient.
    // Let's assume I can use the public show endpoint if I had the slug.
    // But I don't.
    // I'll update the `ActivationController` to support finding by ID in `show` or add a new method.
    // Or I can just try to fetch `/activations` and filter in frontend for now (not ideal but works for small data).
    // BETTER: I'll use the `index` endpoint and filter by ID since I didn't make a specific `show($id)` for admin.
    // Actually, `ActivationController::show` expects a slug.
    // I'll fetch the list and find the item.

    const response = await api.get('/activations?include_inactive=1')
    const found = response.data.find((a: any) => a.id == route.params.id)

    if (found) {
      // Now fetch full details using slug
      const detailResponse = await api.get(`/activations/${found.slug}`)
      activation.value = detailResponse.data
      Object.assign(formData, activation.value)
    }
  } catch (error) {
    console.error('Error fetching activation:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchActivation()
})

// Submit Main Form
const submit = async () => {
  if (!valid.value) return

  saving.value = true
  try {
    const data = new FormData()

    // Append all form fields EXCEPT file fields (hero_image, icon)
    // File fields are handled separately below
    Object.keys(formData).forEach(key => {
      // Skip file-related fields as they're handled separately
      if (key === 'hero_image' || key === 'icon') {
        return
      }
      
      const value = formData[key as keyof typeof formData]
      if (value !== null && value !== undefined) {
        if (typeof value === 'boolean') {
          data.append(key, value ? '1' : '0')
        } else {
          data.append(key, String(value))
        }
      }
    })

    // Only append files if they are actual File objects (not empty arrays or null)
    // v-file-input can return [] when cleared, so we need to check for that
    if (heroImageFile.value && 
        heroImageFile.value instanceof File && 
        !Array.isArray(heroImageFile.value)) {
      data.append('hero_image', heroImageFile.value)
    }
    if (iconFile.value && 
        iconFile.value instanceof File && 
        !Array.isArray(iconFile.value)) {
      data.append('icon', iconFile.value)
    }

    // Debug: Log what we're sending
    console.log('Submitting activation update:')
    console.log('heroImageFile.value:', heroImageFile.value)
    console.log('iconFile.value:', iconFile.value)
    for (let pair of data.entries()) {
      console.log(pair[0], pair[1])
    }

    await api.post(`/activations/${route.params.id}`, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    // Refresh data
    fetchActivation()
  } catch (error: any) {
    console.error('Error updating activation:', error)
    
    // Show user-friendly error message
    let title = 'Gagal Update Aktivasi'
    let message = 'Terjadi kesalahan saat mengupdate aktivasi. Silakan coba lagi.'
    
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
    saving.value = false
  }
}

// Media Management
const mediaDialog = ref(false)
const mediaLoading = ref(false)
const mediaFile = ref<File | null>(null)
const mediaForm = reactive({ type: 'photo', external_url: '', description: '' })

const openMediaDialog = () => {
  mediaForm.type = 'photo'
  mediaForm.external_url = ''
  mediaForm.description = ''
  mediaFile.value = null
  mediaDialog.value = true
}

const submitMedia = async () => {
  mediaLoading.value = true
  try {
    const data = new FormData()
    data.append('type', mediaForm.type)
    data.append('description', mediaForm.description)
    if (mediaForm.type === 'photo' && mediaFile.value) {
      data.append('file', mediaFile.value)
    } else if (mediaForm.type === 'youtube') {
      data.append('external_url', mediaForm.external_url)
    }

    await api.post(`/activations/${route.params.id}/media`, data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    mediaDialog.value = false
    fetchActivation()
  } catch (error) {
    console.error('Error uploading media:', error)
  } finally {
    mediaLoading.value = false
  }
}

const deleteMedia = async (mediaId: number) => {
  if (!confirm('Hapus media ini?')) return
  try {
    await api.delete(`/activations/${route.params.id}/media/${mediaId}`)
    fetchActivation()
  } catch (error) {
    console.error('Error deleting media:', error)
  }
}

// FAQ Management
const faqDialog = ref(false)
const faqLoading = ref(false)
const editingFaq = ref<any>(null)
const faqForm = reactive({ question: '', answer: '', sort_order: 0 })

const openFaqDialog = (faq: any = null) => {
  editingFaq.value = faq
  if (faq) {
    faqForm.question = faq.question
    faqForm.answer = faq.answer
    faqForm.sort_order = faq.sort_order
  } else {
    faqForm.question = ''
    faqForm.answer = ''
    faqForm.sort_order = 0
  }
  faqDialog.value = true
}

const submitFaq = async () => {
  faqLoading.value = true
  try {
    if (editingFaq.value) {
      await api.put(`/activations/${route.params.id}/faqs/${editingFaq.value.id}`, faqForm)
    } else {
      await api.post(`/activations/${route.params.id}/faqs`, faqForm)
    }
    faqDialog.value = false
    fetchActivation()
  } catch (error) {
    console.error('Error saving FAQ:', error)
  } finally {
    faqLoading.value = false
  }
}

const deleteFaq = async (faqId: number) => {
  if (!confirm('Hapus FAQ ini?')) return
  try {
    await api.delete(`/activations/${route.params.id}/faqs/${faqId}`)
    fetchActivation()
  } catch (error) {
    console.error('Error deleting FAQ:', error)
  }
}

// Testimonial Management
const testimonialDialog = ref(false)
const testimonialLoading = ref(false)
const editingTestimonial = ref<any>(null)
const testimonialPhotoFile = ref<File | null>(null)
const testimonialForm = reactive({ author_name: '', quote: '', sort_order: 0 })

const openTestimonialDialog = (item: any = null) => {
  editingTestimonial.value = item
  if (item) {
    testimonialForm.author_name = item.author_name
    testimonialForm.quote = item.quote
    testimonialForm.sort_order = item.sort_order
  } else {
    testimonialForm.author_name = ''
    testimonialForm.quote = ''
    testimonialForm.sort_order = 0
  }
  testimonialPhotoFile.value = null
  testimonialDialog.value = true
}

const submitTestimonial = async () => {
  testimonialLoading.value = true
  try {
    const data = new FormData()
    data.append('author_name', testimonialForm.author_name)
    data.append('quote', testimonialForm.quote)
    data.append('sort_order', String(testimonialForm.sort_order))

    if (testimonialPhotoFile.value) {
      data.append('author_photo', testimonialPhotoFile.value)
    }

    if (editingTestimonial.value) {
      // For update, we might need _method=PUT if using POST for file upload, 
      // but here we can use POST to a specific update endpoint that handles it.
      // My routes use POST for updateTestimonial.
      await api.post(`/activations/${route.params.id}/testimonials/${editingTestimonial.value.id}`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await api.post(`/activations/${route.params.id}/testimonials`, data, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    testimonialDialog.value = false
    fetchActivation()
  } catch (error) {
    console.error('Error saving testimonial:', error)
  } finally {
    testimonialLoading.value = false
  }
}

const deleteTestimonial = async (id: number) => {
  if (!confirm('Hapus testimoni ini?')) return
  try {
    await api.delete(`/activations/${route.params.id}/testimonials/${id}`)
    fetchActivation()
  } catch (error) {
    console.error('Error deleting testimonial:', error)
  }
}
</script>
