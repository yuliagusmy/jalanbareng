<template>
  <div class="profile-page">
    <!-- Hero Section with Cover -->
    <div class="profile-hero">
      <div class="hero-gradient"></div>
      <v-container class="hero-content">
        <div class="d-flex align-center flex-column flex-md-row">
          <!-- Avatar -->
          <v-avatar :size="isMobile ? 120 : 140" class="profile-avatar elevation-8" :key="photoUpdateKey">
            <v-img v-if="getPhotoUrl(authStore.user?.photo)" :src="getPhotoUrlWithCache(authStore.user?.photo)"
              :alt="authStore.user?.name" cover>
              <template v-slot:error>
                <v-icon size="x-large">mdi-account-circle</v-icon>
              </template>
            </v-img>
            <v-icon v-else size="x-large">mdi-account-circle</v-icon>
          </v-avatar>

          <!-- User Info -->
          <div class="user-info-section">
            <h1 class="user-name">{{ authStore.user?.name }}</h1>
            <p class="user-email">
              <v-icon size="small" class="mr-1">mdi-email-outline</v-icon>
              {{ authStore.user?.email }}
            </p>
            <div class="user-stats">
              <div class="stat-item">
                <v-icon size="small" color="primary" class="mr-1">mdi-map-marker</v-icon>
                <span class="stat-value">{{ profileData?.destinations_count || 0 }}</span>
                <span class="stat-label">Destinasi</span>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <v-icon size="small" color="success" class="mr-1">mdi-calendar-check</v-icon>
                <span class="stat-value">{{ profileData?.events_count || 0 }}</span>
                <span class="stat-label">Event</span>
              </div>
            </div>
          </div>
        </div>
      </v-container>
    </div>

    <!-- Main Content -->
    <v-container class="content-section">
      <v-row>
        <!-- Main Content Card -->
        <v-col cols="12" lg="8">
          <v-card elevation="0" rounded="xl" class="main-card">
            <!-- Tabs -->
            <v-tabs v-model="tab" color="primary" align-tabs="start" class="tabs-header" hide-slider>
              <v-tab value="profile" class="custom-tab">
                <v-icon start size="small">mdi-account</v-icon>
                Profil
              </v-tab>
              <v-tab value="destinations" class="custom-tab">
                <v-icon start size="small">mdi-map-marker-multiple</v-icon>
                Destinasi
                <v-chip v-if="profileData?.destinations_count" size="x-small" class="ml-2" color="primary">
                  {{ profileData.destinations_count }}
                </v-chip>
              </v-tab>
              <v-tab value="events" class="custom-tab">
                <v-icon start size="small">mdi-calendar-star</v-icon>
                Event
                <v-chip v-if="profileData?.events_count" size="x-small" class="ml-2" color="success">
                  {{ profileData.events_count }}
                </v-chip>
              </v-tab>
            </v-tabs>

            <v-divider></v-divider>

            <!-- Tab Content -->
            <v-window v-model="tab">
              <!-- Profile Tab -->
              <v-window-item value="profile">
                <div class="tab-content">
                  <div class="section-header">
                    <div>
                      <h3 class="section-title">Informasi Profil</h3>
                      <p class="section-subtitle">Detail informasi pribadi Anda</p>
                    </div>
                    <v-btn color="primary" variant="flat" @click="startEdit" rounded="lg">
                      <v-icon start>mdi-pencil</v-icon>
                      Edit Profil
                    </v-btn>
                  </div>

                  <v-card elevation="0" class="info-card">
                    <v-row>
                      <!-- Contact Info -->
                      <v-col cols="12" md="6">
                        <div class="info-item">
                          <div class="info-icon">
                            <v-icon color="primary">mdi-phone</v-icon>
                          </div>
                          <div class="info-content">
                            <p class="info-label">Nomor Telepon</p>
                            <p class="info-value">{{ authStore.user?.phone || 'Belum diisi' }}</p>
                          </div>
                        </div>
                      </v-col>

                      <!-- Social Media -->
                      <v-col cols="12" md="6">
                        <div class="info-item">
                          <div class="info-icon">
                            <v-icon color="pink">mdi-instagram</v-icon>
                          </div>
                          <div class="info-content">
                            <p class="info-label">Instagram</p>
                            <p class="info-value">
                              <a v-if="authStore.user?.instagram" :href="authStore.user.instagram" target="_blank"
                                class="social-link">
                                Lihat Profil
                                <v-icon size="x-small" class="ml-1">mdi-open-in-new</v-icon>
                              </a>
                              <span v-else>Belum diisi</span>
                            </p>
                          </div>
                        </div>
                      </v-col>

                      <v-col cols="12" md="6">
                        <div class="info-item">
                          <div class="info-icon">
                            <v-icon color="blue">mdi-facebook</v-icon>
                          </div>
                          <div class="info-content">
                            <p class="info-label">Facebook</p>
                            <p class="info-value">
                              <a v-if="authStore.user?.facebook" :href="authStore.user.facebook" target="_blank"
                                class="social-link">
                                Lihat Profil
                                <v-icon size="x-small" class="ml-1">mdi-open-in-new</v-icon>
                              </a>
                              <span v-else>Belum diisi</span>
                            </p>
                          </div>
                        </div>
                      </v-col>

                      <v-col cols="12" md="6">
                        <div class="info-item">
                          <div class="info-icon">
                            <v-icon color="light-blue">mdi-twitter</v-icon>
                          </div>
                          <div class="info-content">
                            <p class="info-label">Twitter / X</p>
                            <p class="info-value">
                              <a v-if="authStore.user?.twitter" :href="authStore.user.twitter" target="_blank"
                                class="social-link">
                                Lihat Profil
                                <v-icon size="x-small" class="ml-1">mdi-open-in-new</v-icon>
                              </a>
                              <span v-else>Belum diisi</span>
                            </p>
                          </div>
                        </div>
                      </v-col>
                    </v-row>
                  </v-card>
                </div>
              </v-window-item>

              <!-- Destinations Tab -->
              <v-window-item value="destinations">
                <div class="tab-content">
                  <!-- Loading -->
                  <div v-if="loading" class="loading-state">
                    <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
                    <p class="mt-4 text-grey">Memuat destinasi...</p>
                  </div>

                  <!-- Has Destinations -->
                  <div v-else-if="userDestinations.length > 0">
                    <v-row>
                      <v-col v-for="destination in userDestinations" :key="destination.id" cols="12" sm="6">
                        <v-card hover rounded="xl" class="destination-card" :to="`/destinations/${destination.id}`">
                          <v-img :src="getImageUrl(destination.primary_photo)" height="200" cover
                            class="destination-image">
                            <template v-slot:placeholder>
                              <div class="d-flex align-center justify-center fill-height">
                                <v-progress-circular indeterminate color="white"></v-progress-circular>
                              </div>
                            </template>
                          </v-img>
                          <v-card-text class="pa-4">
                            <h4 class="destination-name">{{ destination.name }}</h4>
                            <p class="destination-category">
                              <v-icon size="small" class="mr-1">mdi-folder-outline</v-icon>
                              {{ destination.category?.name || 'Tanpa Kategori' }}
                            </p>
                            <div class="destination-meta">
                              <div class="meta-item">
                                <v-icon size="small" color="red">mdi-heart</v-icon>
                                <span>{{ destination.likes_count || 0 }}</span>
                              </div>
                              <div class="meta-item">
                                <v-icon size="small" color="primary">mdi-comment</v-icon>
                                <span>{{ destination.comments_count || 0 }}</span>
                              </div>
                            </div>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <!-- Empty State -->
                  <div v-else class="empty-state">
                    <v-icon size="80" color="grey-lighten-2">mdi-map-marker-off-outline</v-icon>
                    <h3 class="empty-title">Belum Ada Destinasi</h3>
                    <p class="empty-text">Anda belum menambahkan destinasi apapun</p>
                    <v-btn color="primary" variant="flat" to="/destinations/create" rounded="lg" size="large">
                      <v-icon start>mdi-plus</v-icon>
                      Tambah Destinasi
                    </v-btn>
                  </div>
                </div>
              </v-window-item>

              <!-- Events Tab -->
              <v-window-item value="events">
                <div class="tab-content">
                  <!-- Loading -->
                  <div v-if="loading" class="loading-state">
                    <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
                    <p class="mt-4 text-grey">Memuat event...</p>
                  </div>

                  <!-- Has Events -->
                  <div v-else-if="userEvents.length > 0">
                    <v-row>
                      <v-col v-for="event in userEvents" :key="event.id" cols="12" sm="6">
                        <v-card hover rounded="xl" class="event-card" :to="`/events/${event.id}`">
                          <v-img :src="getImageUrl(event.poster)" height="200" cover class="event-image">
                            <template v-slot:placeholder>
                              <div class="d-flex align-center justify-center fill-height">
                                <v-progress-circular indeterminate color="white"></v-progress-circular>
                              </div>
                            </template>
                            <div v-if="event.type === 'walking'" class="event-badge">
                              <v-icon size="small" class="mr-1">mdi-walk</v-icon>
                              Jalan Kaki
                            </div>
                          </v-img>
                          <v-card-text class="pa-4">
                            <h4 class="event-name">{{ event.name }}</h4>
                            <p class="event-date">
                              <v-icon size="small" class="mr-1">mdi-calendar</v-icon>
                              {{ formatDate(event.date) }}
                            </p>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>

                  <!-- Empty State -->
                  <div v-else class="empty-state">
                    <v-icon size="80" color="grey-lighten-2">mdi-calendar-blank-outline</v-icon>
                    <h3 class="empty-title">Belum Ada Event</h3>
                    <p class="empty-text">Anda belum mengikuti event apapun</p>
                    <v-btn color="primary" variant="flat" to="/events" rounded="lg" size="large">
                      <v-icon start>mdi-calendar-star</v-icon>
                      Lihat Event
                    </v-btn>
                  </div>
                </div>
              </v-window-item>
            </v-window>
          </v-card>
        </v-col>

        <!-- Sidebar -->
        <v-col cols="12" lg="4">
          <!-- Admin Panel Quick Access (Administrator & Community Admin) -->
          <v-card
            v-if="authStore.isAdmin || authStore.isCommunityAdmin"
            elevation="0"
            rounded="xl"
            class="sidebar-card mb-4"
            style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;"
          >
            <v-card-text class="pa-6">
              <div class="d-flex align-center ga-3 mb-3">
                <v-avatar color="rgba(255,255,255,0.12)" size="42">
                  <v-icon color="#F59E0B" size="24">mdi-shield-crown</v-icon>
                </v-avatar>
                <div>
                  <h3 class="text-subtitle-1 font-weight-bold text-white mb-0">
                    {{ authStore.isAdmin ? 'Panel Administrator' : 'Panel Manajemen Komunitas' }}
                  </h3>
                  <p class="text-caption text-grey-lighten-2 mb-0">
                    {{ authStore.isAdmin ? 'Kelola sistem, event, & aktivasi' : 'Kelola event, destinasi, & aktivasi' }}
                  </p>
                </div>
              </div>
              <v-btn
                block
                color="#DC2626"
                variant="flat"
                size="large"
                rounded="lg"
                to="/manage/activations"
                class="font-weight-bold"
              >
                <v-icon start>mdi-view-dashboard-outline</v-icon>
                {{ authStore.isAdmin ? 'Masuk ke Panel Admin' : 'Masuk ke Panel Manajemen' }}
              </v-btn>
            </v-card-text>
          </v-card>

          <!-- Quick Actions -->
          <v-card elevation="0" rounded="xl" class="sidebar-card mb-4">
            <v-card-text class="pa-6">
              <h3 class="sidebar-title">Aksi Cepat</h3>
              <div class="quick-actions">
                <v-btn block variant="tonal" color="primary" size="large" rounded="lg" class="mb-3" @click="startEdit">
                  <v-icon start>mdi-account-edit</v-icon>
                  Edit Profil
                </v-btn>
                <v-btn block variant="tonal" color="success" size="large" rounded="lg" class="mb-3"
                  to="/destinations/create">
                  <v-icon start>mdi-map-marker-plus</v-icon>
                  Tambah Destinasi
                </v-btn>
                <v-btn block variant="tonal" color="warning" size="large" rounded="lg" @click="authStore.logout">
                  <v-icon start>mdi-logout</v-icon>
                  Keluar
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Account Info -->
          <v-card elevation="0" rounded="xl" class="sidebar-card">
            <v-card-text class="pa-6">
              <h3 class="sidebar-title">Informasi Akun</h3>
              <div class="account-info">
                <div class="account-item">
                  <v-icon size="small" color="primary" class="mr-2">mdi-shield-check</v-icon>
                  <span class="account-label">Status:</span>
                  <v-chip size="small"
                    :color="authStore.isAdmin ? 'error' : (authStore.isCommunityAdmin ? 'warning' : 'success')"
                    variant="flat">
                    {{ authStore.isAdmin ? 'Admin' : (authStore.isCommunityAdmin ? 'Community Admin' : 'Member') }}
                  </v-chip>
                </div>
                <div class="account-item">
                  <v-icon size="small" color="primary" class="mr-2">mdi-calendar-clock</v-icon>
                  <span class="account-label">Bergabung:</span>
                  <span class="account-value">{{ formatJoinDate(authStore.user?.created_at) }}</span>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <!-- Edit Profile Dialog -->
    <v-dialog v-model="isEditing" max-width="900" persistent scrollable>
      <v-card rounded="xl">
        <v-card-title class="pa-6 bg-gradient">
          <div class="d-flex align-center">
            <v-icon class="mr-3" color="primary" size="32">mdi-account-edit</v-icon>
            <div>
              <h2 class="text-h5 font-weight-bold">Edit Profil</h2>
              <p class="text-caption text-grey mb-0">Perbarui informasi profil Anda</p>
            </div>
          </div>
        </v-card-title>

        <v-divider></v-divider>

        <v-card-text class="pa-6">
          <v-form ref="formRef">
            <!-- Photo Section - DISABLED -->
            <div class="photo-section" v-if="false">
              <h3 class="section-title-sm mb-4">Foto Profil</h3>
              <div class="photo-upload-container">
                <div class="photo-preview-wrapper">
                  <v-avatar size="160" class="photo-preview-avatar elevation-4" :key="photoUpdateKey">
                    <v-img :src="photoPreview || getPhotoUrlWithCache(authStore.user?.photo)" cover>
                      <template v-slot:error>
                        <v-icon size="x-large">mdi-account-circle</v-icon>
                      </template>
                    </v-img>
                  </v-avatar>
                </div>
                <div class="photo-upload-input">
                  <v-file-input v-model="formData.photo" label="Pilih foto baru" accept="image/*" variant="outlined"
                    density="comfortable" rounded="lg" prepend-icon="" prepend-inner-icon="mdi-camera"
                    @update:model-value="handlePhotoSelect" hide-details="auto"></v-file-input>
                  <p class="text-caption text-grey mt-2">
                    <v-icon size="x-small" class="mr-1">mdi-information</v-icon>
                    Format: JPG, PNG • Max: 2MB
                  </p>
                </div>
              </div>
            </div>

            <!-- Divider removed when photo section is hidden -->
            <v-divider class="my-6" v-if="false"></v-divider>

            <!-- Basic Info -->
            <div class="form-section">
              <h3 class="section-title-sm mb-4">Informasi Dasar</h3>
              <v-text-field v-model="formData.name" label="Nama Lengkap *" variant="outlined" rounded="lg"
                density="comfortable" :rules="[rules.required]" prepend-inner-icon="mdi-account" hide-details="auto"
                class="mb-4"></v-text-field>

              <v-text-field v-model="formData.phone" label="Nomor Telepon" variant="outlined" rounded="lg"
                density="comfortable" prepend-inner-icon="mdi-phone" placeholder="+62 812 3456 7890"
                hide-details="auto"></v-text-field>
            </div>

            <v-divider class="my-6"></v-divider>

            <!-- Social Media -->
            <div class="form-section">
              <h3 class="section-title-sm mb-4">Media Sosial</h3>
              <v-text-field v-model="formData.instagram" label="Instagram URL" variant="outlined" rounded="lg"
                density="comfortable" prepend-inner-icon="mdi-instagram" placeholder="https://instagram.com/username"
                hide-details="auto" class="mb-4"></v-text-field>

              <v-text-field v-model="formData.facebook" label="Facebook URL" variant="outlined" rounded="lg"
                density="comfortable" prepend-inner-icon="mdi-facebook" placeholder="https://facebook.com/username"
                hide-details="auto" class="mb-4"></v-text-field>

              <v-text-field v-model="formData.twitter" label="Twitter / X URL" variant="outlined" rounded="lg"
                density="comfortable" prepend-inner-icon="mdi-twitter" placeholder="https://twitter.com/username"
                hide-details="auto"></v-text-field>
            </div>
          </v-form>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions class="pa-6">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="cancelEdit" :disabled="saving" size="large" rounded="lg">
            Batal
          </v-btn>
          <v-btn color="primary" variant="flat" size="large" rounded="lg" :loading="saving" @click="updateProfile">
            <v-icon start>mdi-content-save</v-icon>
            Simpan Perubahan
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" location="top">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useImageCompressor } from '~/composables/useImageCompressor'
import { useRuntimeConfig } from '#app'
import { useDisplay } from 'vuetify'

definePageMeta({
  layout: 'default',
  middleware: 'auth'
})

const authStore = useAuthStore()
const { api } = useApi()
const config = useRuntimeConfig()
const { mobile } = useDisplay()

const isMobile = computed(() => mobile.value)

const tab = ref('profile')
const isEditing = ref(false)
const saving = ref(false)
const loading = ref(false)
const profileData = ref<any>(null)
const userDestinations = ref<any[]>([])
const userEvents = ref<any[]>([])
const formRef = ref<any>(null)
const { compressImage } = useImageCompressor()
const photoPreview = ref<string | null>(null)
const photoUpdateKey = ref(0)

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const formData = ref({
  name: '',
  phone: '',
  instagram: '',
  facebook: '',
  twitter: '',
  photo: null as File[] | null,
  _method: 'PUT'
})

const rules = {
  required: (v: string) => !!v || 'Field ini wajib diisi'
}

const showSnackbar = (text: string, color: string = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

const getPhotoUrl = (path: string | undefined) => {
  if (!path) return null
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const getImageUrl = (path: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800&h=600&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const getPhotoUrlWithCache = (path: string | undefined) => {
  const baseUrl = getPhotoUrl(path)
  if (!baseUrl) return null
  // Add timestamp to prevent caching
  return `${baseUrl}?t=${photoUpdateKey.value}`
}

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const formatJoinDate = (date: string | undefined) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    month: 'long',
    year: 'numeric'
  })
}

const setFormData = () => {
  const user = authStore.user
  formData.value = {
    name: user?.name || '',
    phone: user?.phone || '',
    instagram: user?.instagram || '',
    facebook: user?.facebook || '',
    twitter: user?.twitter || '',
    photo: null,
    _method: 'PUT'
  }
  photoPreview.value = null
}

const startEdit = () => {
  setFormData()
  isEditing.value = true
}

const cancelEdit = () => {
  isEditing.value = false
  photoPreview.value = null
}

const handlePhotoSelect = async (files: File[]) => {
  const file = files?.[0]
  if (file) {
    try {
      // Compress avatar to WebP (max 800x800)
      const res = await compressImage(file, 800, 800, 0.85)
      formData.value.photo = [res.file]
      photoPreview.value = res.previewUrl
    } catch (e) {
      formData.value.photo = [file]
      const reader = new FileReader()
      reader.onload = (e) => {
        photoPreview.value = e.target?.result as string
      }
      reader.readAsDataURL(file)
    }
  }
}

const updateProfile = async () => {
  if (!formRef.value) return

  const { valid } = await formRef.value.validate()
  if (!valid) {
    showSnackbar('Mohon lengkapi form dengan benar', 'error')
    return
  }

  saving.value = true
  try {
    const data = new FormData()

    data.append('name', formData.value.name)
    data.append('_method', 'PUT')

    if (formData.value.phone) data.append('phone', formData.value.phone)
    if (formData.value.instagram) data.append('instagram', formData.value.instagram)
    if (formData.value.facebook) data.append('facebook', formData.value.facebook)
    if (formData.value.twitter) data.append('twitter', formData.value.twitter)

    if (formData.value.photo && formData.value.photo.length > 0) {
      data.append('photo', formData.value.photo[0])
    }

    const response = await api.post('/profile', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data.user) {
      authStore.setUser(response.data.user)
      // Force photo refresh by updating key
      photoUpdateKey.value = Date.now()
    }

    showSnackbar('Profil berhasil diperbarui!', 'success')
    cancelEdit()
    await fetchProfileData()
  } catch (error: any) {
    console.error('Error updating profile:', error)
    showSnackbar(error.response?.data?.message || 'Gagal memperbarui profil', 'error')
  } finally {
    saving.value = false
  }
}

const fetchProfileData = async () => {
  if (!authStore.user?.id) return

  loading.value = true
  try {
    const response = await api.get(`/profile/${authStore.user.id}`)
    profileData.value = response.data.user
    userDestinations.value = response.data.user.destinations || []
    userEvents.value = response.data.user.participated_events || []
  } catch (error) {
    console.error('Error fetching profile data:', error)
    showSnackbar('Gagal memuat data profil', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProfileData()
})
</script>

<style scoped>
/* ============================================
   CLEAN PROFILE PAGE DESIGN
   ============================================ */

.profile-page {
  background: #fafafa;
  min-height: 100vh;
}

/* Hero Section */
.profile-hero {
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 48px 0 40px;
  overflow: hidden;
}

.hero-gradient {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background:
    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
}

.hero-content {
  position: relative;
  z-index: 2;
  padding-top: 90px;
}

.profile-avatar {
  border: 5px solid rgba(255, 255, 255, 0.3);
  background: white;
  margin-bottom: 24px;
}

@media (min-width: 960px) {
  .profile-avatar {
    margin-bottom: 0;
    margin-right: 32px;
  }
}

/* User Info */
.user-info-section {
  text-align: center;
  color: white;
}

@media (min-width: 960px) {
  .user-info-section {
    text-align: left;
  }
}

.user-name {
  font-size: 32px;
  font-weight: 700;
  color: white;
  margin: 0 0 8px;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.user-email {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0 0 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (min-width: 960px) {
  .user-email {
    justify-content: flex-start;
  }
}

.user-stats {
  display: flex;
  gap: 24px;
  align-items: center;
  justify-content: center;
}

@media (min-width: 960px) {
  .user-stats {
    justify-content: flex-start;
  }
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 6px;
}

.stat-value {
  font-size: 20px;
  font-weight: 700;
  color: white;
}

.stat-label {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.9);
}

.stat-divider {
  width: 1px;
  height: 24px;
  background: rgba(255, 255, 255, 0.3);
}

/* Content Section */
.content-section {
  margin-top: -40px;
  padding-bottom: 80px;
}

/* Main Card */
.main-card {
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

/* Tabs */
.tabs-header {
  background: white;
}

.custom-tab {
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0;
}

/* Tab Content */
.tab-content {
  padding: 32px;
  min-height: 400px;
}

/* Section Header */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

.section-subtitle {
  font-size: 14px;
  color: #666;
  margin: 4px 0 0;
}

.section-title-sm {
  font-size: 18px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}

/* Info Card */
.info-card {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 24px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  margin-bottom: 16px;
  transition: all 0.2s;
}

.info-item:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.info-icon {
  flex-shrink: 0;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  border-radius: 10px;
}

.info-content {
  flex: 1;
}

.info-label {
  font-size: 13px;
  color: #666;
  margin: 0 0 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
}

.info-value {
  font-size: 15px;
  color: #1a1a1a;
  font-weight: 500;
  margin: 0;
}

.social-link {
  color: #1976d2;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  transition: color 0.2s;
}

.social-link:hover {
  color: #1565c0;
  text-decoration: underline;
}

/* Destination Card */
.destination-card,
.event-card {
  transition: all 0.3s;
  overflow: hidden;
}

.destination-card:hover,
.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.destination-image,
.event-image {
  position: relative;
}

.destination-name,
.event-name {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 8px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.destination-category,
.event-date {
  font-size: 13px;
  color: #666;
  margin: 0 0 12px;
  display: flex;
  align-items: center;
}

.destination-meta {
  display: flex;
  gap: 16px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  color: #666;
}

.event-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: rgba(76, 175, 80, 0.95);
  color: white;
  padding: 6px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  backdrop-filter: blur(4px);
}

/* Empty State */
.empty-state,
.loading-state {
  text-align: center;
  padding: 80px 24px;
}

.empty-title {
  font-size: 20px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 16px 0 8px;
}

.empty-text {
  font-size: 14px;
  color: #666;
  margin: 0 0 24px;
}

/* Sidebar */
.sidebar-card {
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.sidebar-title {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px;
}

.quick-actions {
  display: flex;
  flex-direction: column;
}

.account-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.account-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
}

.account-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
  margin-right: 8px;
}

.account-value {
  font-size: 14px;
  color: #1a1a1a;
  font-weight: 600;
}

/* Edit Dialog */
.bg-gradient {
  background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
}

.photo-section {
  margin-bottom: 24px;
}

.photo-upload-container {
  display: flex;
  gap: 24px;
  align-items: center;
  flex-wrap: wrap;
}

.photo-preview-wrapper {
  flex-shrink: 0;
}

.photo-preview-avatar {
  border: 3px dashed #e0e0e0;
  background: #fafafa;
}

.photo-upload-input {
  flex: 1;
  min-width: 250px;
}

.form-section {
  margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 960px) {
  .user-name {
    font-size: 24px;
  }

  .tab-content {
    padding: 20px;
  }

  .section-header {
    flex-direction: column;
  }
}

@media (max-width: 600px) {
  .profile-hero {
    padding: 32px 0 32px;
  }

  .user-stats {
    flex-direction: column;
    gap: 12px;
  }

  .stat-divider {
    display: none;
  }
}
</style>
