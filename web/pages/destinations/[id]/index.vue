<template>
  <div class="destination-detail">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
    </div>

    <!-- Error State -->
    <v-container v-else-if="!destination" class="text-center py-16">
      <v-icon size="64" color="grey">mdi-alert-circle-outline</v-icon>
      <h2 class="text-h5 mt-4 mb-2">Destinasi tidak ditemukan</h2>
      <p class="text-body-2 text-grey mb-6">Destinasi yang Anda cari tidak tersedia.</p>
      <v-btn color="primary" to="/destinations" variant="flat">
        Kembali ke Destinasi
      </v-btn>
    </v-container>

    <!-- Destination Detail Content -->
    <div v-else>
      <!-- Hero Section with Background Image -->
      <div class="hero-section" :style="{ backgroundImage: `url(${getImageUrl(destination.primary_photo)})` }">
        <div class="hero-overlay"></div>
        <v-container class="hero-container">
          <div class="hero-content">
            <!-- Breadcrumbs -->
            <div class="breadcrumbs mb-6">
              <nuxt-link to="/" class="breadcrumb-link">Beranda</nuxt-link>
              <span class="breadcrumb-separator">/</span>
              <nuxt-link to="/destinations" class="breadcrumb-link">Destinasi</nuxt-link>
              <span class="breadcrumb-separator">/</span>
              <span class="breadcrumb-current">{{ destination.name }}</span>
            </div>

            <!-- Title & Meta -->
            <h1 class="hero-title">{{ destination.name }}</h1>

            <div class="hero-meta">
              <div class="meta-item">
                <v-icon size="18" color="white">mdi-folder-outline</v-icon>
                <span>{{ destination.category?.name }}</span>
              </div>
              <div class="meta-item">
                <v-icon size="18" color="white">mdi-map-marker-outline</v-icon>
                <span>Makassar, Sulawesi Selatan</span>
              </div>
              <div class="meta-item">
                <v-icon size="18" color="white">mdi-calendar-outline</v-icon>
                <span>{{ formatDate(destination.created_at) }}</span>
              </div>
            </div>

            <!-- User Info -->
            <div v-if="destination.user" class="user-info">
              <v-avatar size="48" class="user-avatar">
                <v-img v-if="destination.user.photo && !heroImageError" :src="getImageUrl(destination.user.photo)"
                  @error="heroImageError = true"></v-img>
                <v-icon v-else color="white">mdi-account-circle</v-icon>
              </v-avatar>
              <div class="user-details">
                <p class="user-label">Dibagikan oleh</p>
                <p class="user-name">{{ destination.user.name }}</p>
              </div>
            </div>
          </div>
        </v-container>
      </div>

      <!-- Content Section -->
      <v-container class="content-section">
        <v-row>
          <!-- Main Content -->
          <v-col cols="12" md="8">
            <!-- Quick Stats -->
            <div class="quick-stats">
              <div class="stat-item">
                <div class="stat-value">{{ destination.likes_count || 0 }}</div>
                <div class="stat-label">Likes</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-value">{{ destination.comments_count || 0 }}</div>
                <div class="stat-label">Komentar</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-value">{{ destination.photos?.length || 0 }}</div>
                <div class="stat-label">Foto</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-value">{{ getRandomViews() }}</div>
                <div class="stat-label">Views</div>
              </div>
            </div>

            <!-- Description -->
            <section class="content-block">
              <h2 class="section-title">Tentang Destinasi</h2>
              <p class="description-text">{{ destination.description }}</p>
            </section>

            <!-- Gallery -->
            <section v-if="destination.photos && destination.photos.length > 0" class="content-block">
              <h2 class="section-title">Galeri Foto</h2>
              <div class="photo-grid">
                <div v-for="(photo, index) in destination.photos" :key="photo.id" class="photo-item"
                  @click="openGallery(index)">
                  <img :src="getImageUrl(photo.photo_path)" :alt="`Photo ${index + 1}`" class="gallery-image" />
                  <div v-if="photo.is_primary" class="primary-label">Utama</div>
                </div>
              </div>
            </section>

            <!-- Map Section -->
            <section class="content-block">
              <h2 class="section-title">Lokasi</h2>
              <div class="map-card">
                <div v-if="destination.latitude && destination.longitude">
                  <div ref="mapContainer" style="height: 400px; border-radius: 12px; overflow: hidden;"></div>
                </div>

                <div class="map-info">
                  <div class="map-info-item">
                    <span class="map-label">Koordinat:</span>
                    <span class="map-value">{{ parseFloat(destination.latitude)?.toFixed(6) }}, {{
                      parseFloat(destination.longitude)?.toFixed(6) }}</span>
                  </div>
                  <v-btn :href="getGoogleMapsUrl()" target="_blank" color="primary" variant="flat" size="default"
                    class="mt-4">
                    <v-icon start size="20">mdi-directions</v-icon>
                    Petunjuk Arah
                  </v-btn>
                </div>
              </div>
            </section>

            <!-- Tips -->
            <section class="content-block">
              <h2 class="section-title">Tips Berkunjung</h2>
              <div class="tips-list">
                <div v-for="(tip, index) in visitTips" :key="index" class="tip-item">
                  <div class="tip-number">{{ index + 1 }}</div>
                  <div class="tip-text">{{ tip.text }}</div>
                </div>
              </div>
            </section>

            <!-- Comments -->
            <section class="content-block">
              <h2 class="section-title">Komentar ({{ destination.comments?.length || 0 }})</h2>

              <!-- Comment Form -->
              <div v-if="authStore.isLoggedIn" class="comment-form">
                <textarea v-model="newComment" placeholder="Tulis komentar Anda..." class="comment-textarea"
                  rows="4"></textarea>
                <div class="comment-actions">
                  <v-btn color="primary" variant="flat" :loading="submittingComment" :disabled="!newComment.trim()"
                    @click="submitComment">
                    Kirim Komentar
                  </v-btn>
                </div>
              </div>

              <div v-else class="login-prompt">
                <p>Silakan login untuk memberikan komentar</p>
                <v-btn to="/login" color="primary" variant="outlined" size="small">
                  Login
                </v-btn>
              </div>

              <!-- Comments List -->
              <div v-if="destination.comments && destination.comments.length > 0" class="comments-list">
                <div v-for="comment in destination.comments" :key="comment.id" class="comment-item">
                  <div class="comment-header">
                    <div class="comment-avatar">
                      <v-avatar size="40">
                        <v-img v-if="comment.user?.photo && !commentImageError[comment.id]"
                          :src="getImageUrl(comment.user.photo)" @error="commentImageError[comment.id] = true"></v-img>
                        <v-icon v-else>mdi-account-circle</v-icon>
                      </v-avatar>
                    </div>
                    <div class="comment-meta">
                      <div class="comment-author">{{ comment.user?.name }}</div>
                      <div class="comment-date">{{ formatDate(comment.created_at) }}</div>
                    </div>
                  </div>
                  <div class="comment-content">{{ comment.content }}</div>
                  <div class="comment-actions">
                    <button class="comment-action-btn" @click="toggleCommentLike(comment.id)">
                      <v-icon size="16" :color="comment.is_liked ? 'red' : ''">{{ comment.is_liked ? 'mdi-heart' :
                        'mdi-heart-outline' }}</v-icon>
                      <span>{{ comment.likes_count || 0 }}</span>
                    </button>
                    <button v-if="authStore.isLoggedIn" class="comment-action-btn" @click="replyTo(comment.id)">
                      <v-icon size="16">mdi-reply</v-icon>
                      <span>Balas</span>
                    </button>
                  </div>

                  <!-- Reply Form -->
                  <div v-if="replyingTo === comment.id" class="reply-form">
                    <textarea v-model="replyComment" placeholder="Tulis balasan..." class="reply-textarea"
                      rows="2"></textarea>
                    <div class="reply-actions">
                      <v-btn size="small" color="primary" variant="flat" :loading="submittingReply"
                        :disabled="!replyComment.trim()" @click="submitReply(comment.id)">
                        Kirim
                      </v-btn>
                      <v-btn size="small" variant="text" @click="cancelReply">
                        Batal
                      </v-btn>
                    </div>
                  </div>

                  <!-- Replies -->
                  <div v-if="comment.replies && comment.replies.length > 0" class="replies-list">
                    <div v-for="reply in comment.replies" :key="reply.id" class="reply-item">
                      <div class="comment-header">
                        <div class="comment-avatar">
                          <v-avatar size="32">
                            <v-img v-if="reply.user?.photo && !commentImageError[reply.id]"
                              :src="getImageUrl(reply.user.photo)" @error="commentImageError[reply.id] = true"></v-img>
                            <v-icon v-else size="small">mdi-account-circle</v-icon>
                          </v-avatar>
                        </div>
                        <div class="comment-meta">
                          <div class="comment-author">{{ reply.user?.name }}</div>
                          <div class="comment-date">{{ formatDate(reply.created_at) }}</div>
                        </div>
                      </div>
                      <div class="comment-content">{{ reply.content }}</div>
                      <div class="comment-actions">
                        <button class="comment-action-btn" @click="toggleCommentLike(reply.id)">
                          <v-icon size="16" :color="reply.is_liked ? 'red' : ''">{{ reply.is_liked ? 'mdi-heart' :
                            'mdi-heart-outline' }}</v-icon>
                          <span>{{ reply.likes_count || 0 }}</span>
                        </button>
                        <!-- No reply button on replies (max 1 level) -->
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-else class="empty-comments">
                <v-icon size="48" color="grey-lighten-1">mdi-comment-outline</v-icon>
                <p>Belum ada komentar</p>
              </div>
            </section>
          </v-col>

          <!-- Sidebar -->
          <v-col cols="12" md="4">
            <div class="sidebar">
              <!-- Like Card -->
              <div class="sidebar-card">
                <v-btn :color="isLiked ? 'error' : 'grey-lighten-3'" :variant="isLiked ? 'flat' : 'flat'" size="large"
                  block class="like-button" @click="toggleLike" :loading="likingDestination">
                  <v-icon start>{{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                  {{ isLiked ? 'Sudah Disukai' : 'Suka Destinasi Ini' }}
                </v-btn>
                <p class="like-count">{{ destination.likes_count || 0 }} orang menyukai ini</p>
              </div>

              <!-- Share Card -->
              <div class="sidebar-card">
                <h3 class="sidebar-title">Bagikan</h3>
                <div class="share-buttons">
                  <button class="share-btn whatsapp" @click="shareDestination('whatsapp')">
                    <v-icon>mdi-whatsapp</v-icon>
                  </button>
                  <button class="share-btn facebook" @click="shareDestination('facebook')">
                    <v-icon>mdi-facebook</v-icon>
                  </button>
                  <button class="share-btn twitter" @click="shareDestination('twitter')">
                    <v-icon>mdi-twitter</v-icon>
                  </button>
                  <button class="share-btn link" @click="copyLink">
                    <v-icon>mdi-link-variant</v-icon>
                  </button>
                </div>
              </div>

              <!-- Info Card -->
              <div class="sidebar-card">
                <h3 class="sidebar-title">Informasi</h3>
                <div class="info-list">
                  <div class="info-item">
                    <span class="info-label">Kategori</span>
                    <span class="info-value">{{ destination.category?.name || '-' }}</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Dibagikan oleh</span>
                    <div class="d-flex align-center">
                      <v-avatar size="24" class="mr-2">
                        <v-img v-if="destination.user.photo && !sidebarImageError"
                          :src="getImageUrl(destination.user.photo)" @error="sidebarImageError = true"></v-img>
                        <v-icon v-else>mdi-account-circle</v-icon>
                      </v-avatar>
                      <span class="info-value">{{ destination.user?.name || '-' }}</span>
                    </div>
                  </div>
                  <div class="info-item">
                    <span class="info-label">Ditambahkan</span>
                    <span class="info-value">{{ formatDate(destination.created_at) }}</span>
                  </div>
                </div>
              </div>

              <!-- Nearby Destinations -->
              <div v-if="nearbyDestinations.length > 0" class="sidebar-card">
                <h3 class="sidebar-title">Destinasi Terdekat</h3>
                <div class="related-list">
                  <nuxt-link v-for="nearby in nearbyDestinations.slice(0, 4)" :key="nearby.id"
                    :to="`/destinations/${nearby.id}`" class="related-item">
                    <img :src="getImageUrl(nearby.primary_photo)" :alt="nearby.name" class="related-image" />
                    <div class="related-info">
                      <div class="related-name">{{ nearby.name }}</div>
                      <div class="related-meta">~{{ Math.floor(Math.random() * 3 + 1) }} km</div>
                    </div>
                  </nuxt-link>
                </div>
              </div>

              <!-- Similar Destinations -->
              <div v-if="similarDestinations.length > 0" class="sidebar-card">
                <h3 class="sidebar-title">Destinasi Serupa</h3>
                <div class="related-list">
                  <nuxt-link v-for="similar in similarDestinations.slice(0, 4)" :key="similar.id"
                    :to="`/destinations/${similar.id}`" class="related-item">
                    <img :src="getImageUrl(similar.primary_photo)" :alt="similar.name" class="related-image" />
                    <div class="related-info">
                      <div class="related-name">{{ similar.name }}</div>
                      <div class="related-meta">{{ similar.likes_count || 0 }} likes</div>
                    </div>
                  </nuxt-link>
                </div>
              </div>

              <!-- Admin Actions -->
              <div v-if="canEdit" class="sidebar-card">
                <h3 class="sidebar-title">Aksi</h3>
                <v-btn color="primary" variant="outlined" block class="mb-2" @click="editDestination">
                  <v-icon start size="20">mdi-pencil</v-icon>
                  Edit
                </v-btn>
                <v-btn color="error" variant="outlined" block @click="confirmDelete">
                  <v-icon start size="20">mdi-delete</v-icon>
                  Hapus
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </div>

    <!-- Delete Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title>Hapus Destinasi</v-card-title>
        <v-card-text>
          Apakah Anda yakin ingin menghapus destinasi <strong>{{ destination?.name }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" @click="deleteDestination" :loading="deleting">Hapus</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Gallery Lightbox -->
    <v-dialog v-model="galleryDialog" max-width="1200">
      <v-card dark>
        <div class="lightbox">
          <button class="lightbox-close" @click="galleryDialog = false">
            <v-icon>mdi-close</v-icon>
          </button>
          <button v-if="currentPhotoIndex > 0" class="lightbox-prev" @click="previousPhoto">
            <v-icon>mdi-chevron-left</v-icon>
          </button>
          <img v-if="destination.photos && destination.photos[currentPhotoIndex]"
            :src="getImageUrl(destination.photos[currentPhotoIndex].photo_path)" class="lightbox-image" />
          <button v-if="currentPhotoIndex < destination.photos.length - 1" class="lightbox-next" @click="nextPhoto">
            <v-icon>mdi-chevron-right</v-icon>
          </button>
          <div class="lightbox-counter">
            {{ currentPhotoIndex + 1 }} / {{ destination.photos?.length }}
          </div>
        </div>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000" location="top">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, nextTick } from 'vue'
import { useRoute, useRouter, useHead } from '#app'
import { useApi } from '~/composables/useApi'
import { useAuthStore } from '~/stores/auth'
import { useRuntimeConfig } from '#app'

definePageMeta({
  layout: 'default'
})

const route = useRoute()
const router = useRouter()
const { api } = useApi()
const authStore = useAuthStore()
const config = useRuntimeConfig()

// Load Google Maps in head with API key from config
useHead({
  script: [
    {
      src: `https://maps.googleapis.com/maps/api/js?key=${config.public.googleMapsApiKey}`,
      async: true,
      defer: true,
    }
  ]
})

// Google Maps
const mapContainer = ref<HTMLElement | null>(null)
let map: any = null
let marker: any = null

const destination = ref<any>(null)

useSeoMeta({
  title: () => destination.value ? `${destination.value.name} - Jalan Bareng` : 'Detail Destinasi - Jalan Bareng',
  ogTitle: () => destination.value ? `${destination.value.name} - Jalan Bareng` : 'Detail Destinasi - Jalan Bareng',
  description: () => destination.value ? `Temukan keindahan ${destination.value.name}. ${destination.value.description?.substring(0, 150)}...` : 'Detail destinasi wisata di Jalan Bareng.',
  ogDescription: () => destination.value ? `Temukan keindahan ${destination.value.name}. ${destination.value.description?.substring(0, 150)}...` : 'Detail destinasi wisata di Jalan Bareng.',
  ogImage: () => destination.value?.primary_photo ? getImageUrl(destination.value.primary_photo) : 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=1200&h=630&fit=crop',
  twitterCard: 'summary_large_image',
})

const loading = ref(true)
const deleteDialog = ref(false)
const deleting = ref(false)
const galleryDialog = ref(false)
const currentPhotoIndex = ref(0)
const isLiked = ref(false)
const likingDestination = ref(false)
const nearbyDestinations = ref([])
const similarDestinations = ref([])
const randomViews = ref(0)
const heroImageError = ref(false)
const sidebarImageError = ref(false)

const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

const newComment = ref('')
const submittingComment = ref(false)
const replyingTo = ref(null)
const replyComment = ref('')
const submittingReply = ref(false)
const commentImageError = ref({})

const visitTips = ref([
  { text: 'Kunjungi di pagi hari untuk pencahayaan terbaik saat berfoto' },
  { text: 'Bawa botol minum dan gunakan sunscreen untuk kenyamanan' },
  { text: 'Hormati lingkungan sekitar dan jaga kebersihan' },
  { text: 'Bagikan pengalaman Anda di komentar untuk membantu pengunjung lain' }
])

const canEdit = computed(() => {
  if (!authStore.isLoggedIn || !destination.value) return false
  return (
    authStore.isAdmin ||
    authStore.isCommunityAdmin ||
    destination.value.user_id === authStore.currentUser?.id
  )
})

const { getImageUrl } = useImageUrl()

const formatDate = (date: string) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getRandomViews = () => {
  if (randomViews.value === 0) {
    randomViews.value = Math.floor(Math.random() * 500 + 100)
  }
  return randomViews.value
}

const getGoogleMapsUrl = () => {
  if (!destination.value?.latitude || !destination.value?.longitude) return '#'
  return `https://www.google.com/maps/dir/?api=1&destination=${destination.value.latitude},${destination.value.longitude}`
}

const shareDestination = (platform: string) => {
  const url = window.location.href
  const text = `Lihat destinasi menarik: ${destination.value.name}`

  let shareUrl = ''
  switch (platform) {
    case 'whatsapp':
      shareUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
      break
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`
      break
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`
      break
  }

  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400')
  }
}

const copyLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href)
    showSnackbar('Link berhasil disalin!', 'success')
  } catch (error) {
    showSnackbar('Gagal menyalin link', 'error')
  }
}

const showSnackbar = (text: string, color: string = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

const waitForGoogleMaps = () => {
  return new Promise<void>((resolve) => {
    if (typeof window !== 'undefined' && (window as any).google?.maps) {
      resolve()
      return
    }

    // Poll for Google Maps to be available
    const checkInterval = setInterval(() => {
      if ((window as any).google?.maps) {
        clearInterval(checkInterval)
        resolve()
      }
    }, 100)

    // Timeout after 10 seconds
    setTimeout(() => {
      clearInterval(checkInterval)
      resolve()
    }, 10000)
  })
}

const initMap = async () => {
  if (!destination.value || !destination.value.latitude || !destination.value.longitude) {
    console.log('No destination data or coordinates')
    return
  }

  try {
    console.log('Waiting for Google Maps...')
    await waitForGoogleMaps()
    console.log('Google Maps ready')

    await nextTick()

    if (!mapContainer.value) {
      console.error('Map container not found')
      return
    }

    const lat = parseFloat(destination.value.latitude)
    const lng = parseFloat(destination.value.longitude)

    console.log('Initializing map at:', lat, lng)

    const google = (window as any).google
    map = new google.maps.Map(mapContainer.value, {
      center: { lat, lng },
      zoom: 15,
      mapTypeControl: true,
      streetViewControl: true,
      fullscreenControl: true,
    })

    console.log('Map initialized')

    marker = new google.maps.Marker({
      position: { lat, lng },
      map: map,
      title: destination.value.name,
    })

    console.log('Marker added')

    const infoWindow = new google.maps.InfoWindow({
      content: `
        <div style="padding: 8px;">
          <div style="font-weight: 600; margin-bottom: 4px; font-size: 14px;">${destination.value.name}</div>
          <div style="font-size: 12px; color: #666;">${destination.value.category?.name || ''}</div>
        </div>
      `
    })

    marker.addListener('click', () => {
      infoWindow.open(map, marker)
    })
  } catch (error) {
    console.error('Error initializing Google Maps:', error)
  }
}

const fetchDestination = async () => {
  loading.value = true
  try {
    const response = await api.get(`/destinations/${route.params.id}`)
    destination.value = response.data.destination
    isLiked.value = destination.value.is_liked

    await fetchNearbyDestinations()
    await fetchSimilarDestinations()
  } catch (error) {
    console.error('Error fetching destination:', error)
    destination.value = null
  } finally {
    loading.value = false

    // Initialize map after loading is complete and DOM is updated
    await nextTick()
    setTimeout(() => {
      initMap()
    }, 300)
  }
}

const fetchNearbyDestinations = async () => {
  try {
    const response = await api.get('/destinations', {
      params: { per_page: 20 }
    })
    nearbyDestinations.value = response.data.data.filter(
      (d: any) => d.id !== parseInt(route.params.id as string)
    )
  } catch (error) {
    console.error('Error fetching nearby destinations:', error)
  }
}

const fetchSimilarDestinations = async () => {
  try {
    if (!destination.value?.category_id) return
    const response = await api.get('/destinations', {
      params: {
        category_id: destination.value.category_id,
        per_page: 20
      }
    })
    similarDestinations.value = response.data.data.filter(
      (d: any) => d.id !== parseInt(route.params.id as string)
    )
  } catch (error) {
    console.error('Error fetching similar destinations:', error)
  }
}



const toggleLike = async () => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  likingDestination.value = true
  try {
    const response = await api.post('/likes/toggle', {
      likeable_type: 'App\\Models\\Destination',
      likeable_id: destination.value.id
    })

    isLiked.value = response.data.liked

    if (response.data.liked) {
      destination.value.likes_count++
      showSnackbar('Destinasi ditambahkan ke favorit!', 'success')
    } else {
      destination.value.likes_count--
      showSnackbar('Destinasi dihapus dari favorit', 'info')
    }
  } catch (error) {
    console.error('Error toggling like:', error)
    showSnackbar('Gagal memproses like', 'error')
  } finally {
    likingDestination.value = false
  }
}

const toggleCommentLike = async (commentId: number) => {
  if (!authStore.isLoggedIn) {
    router.push('/login');
    return;
  }

  let comment = destination.value.comments.find(c => c.id === commentId);
  let isReply = false;

  if (!comment) {
    for (const parentComment of destination.value.comments) {
      if (parentComment.replies) {
        comment = parentComment.replies.find(r => r.id === commentId);
        if (comment) {
          isReply = true;
          break;
        }
      }
    }
  }

  if (!comment) return;

  const originalIsLiked = comment.is_liked;
  const originalLikesCount = comment.likes_count;

  // Optimistic update
  comment.is_liked = !comment.is_liked;
  comment.likes_count = comment.is_liked ? (comment.likes_count || 0) + 1 : (comment.likes_count || 0) - 1;

  try {
    await api.post('/likes/toggle', {
      likeable_type: 'App\\Models\\Comment',
      likeable_id: commentId,
    });
    showSnackbar('Like/Unlike berhasil!', 'success');
  } catch (error) {
    // Revert on error
    comment.is_liked = originalIsLiked;
    comment.likes_count = originalLikesCount;
    console.error('Error toggling comment like:', error);
    showSnackbar('Gagal memproses like', 'error');
  }
};

const submitComment = async () => {
  if (!newComment.value.trim()) return

  submittingComment.value = true
  try {
    const response = await api.post('/comments', {
      commentable_type: 'destination',
      commentable_id: destination.value.id,
      content: newComment.value
    })

    // Append new comment to the list without refreshing
    if (destination.value.comments) {
      destination.value.comments.unshift(response.data.comment)
    } else {
      destination.value.comments = [response.data.comment]
    }

    // Increment comment count
    destination.value.comments_count = (destination.value.comments_count || 0) + 1

    newComment.value = ''
    showSnackbar('Komentar berhasil ditambahkan!', 'success')

    // Reload user to get updated photo URL
    await authStore.fetchUser()
  } catch (error) {
    console.error('Error submitting comment:', error)
    showSnackbar('Gagal menambahkan komentar', 'error')
  } finally {
    submittingComment.value = false
  }
}

const replyTo = (commentId: number) => {
  replyingTo.value = commentId
  replyComment.value = ''
}

const cancelReply = () => {
  replyingTo.value = null
  replyComment.value = ''
}

const submitReply = async (parentId: number) => {
  if (!replyComment.value.trim()) return

  submittingReply.value = true
  try {
    const response = await api.post('/comments', {
      commentable_type: 'destination',
      commentable_id: destination.value.id,
      parent_id: parentId,
      content: replyComment.value
    })

    // Append new reply to the parent comment's replies
    const parentComment = destination.value.comments?.find(c => c.id === parentId)
    if (parentComment) {
      if (!parentComment.replies) {
        parentComment.replies = []
      }
      parentComment.replies.push(response.data.comment)
    }

    // Increment comment count
    destination.value.comments_count = (destination.value.comments_count || 0) + 1

    replyComment.value = ''
    replyingTo.value = null
    showSnackbar('Balasan berhasil ditambahkan!', 'success')

    // Reload user to get updated photo URL
    await authStore.fetchUser()
  } catch (error) {
    console.error('Error submitting reply:', error)
    showSnackbar('Gagal menambahkan balasan', 'error')
  } finally {
    submittingReply.value = false
  }
}

const openGallery = (index: number) => {
  currentPhotoIndex.value = index
  galleryDialog.value = true
}

const previousPhoto = () => {
  if (currentPhotoIndex.value > 0) {
    currentPhotoIndex.value--
  }
}

const nextPhoto = () => {
  if (currentPhotoIndex.value < destination.value.photos.length - 1) {
    currentPhotoIndex.value++
  }
}

const editDestination = () => {
  router.push(`/destinations/${route.params.id}/edit`)
}

const confirmDelete = () => {
  deleteDialog.value = true
}

const deleteDestination = async () => {
  deleting.value = true
  try {
    await api.delete(`/destinations/${route.params.id}`)
    showSnackbar('Destinasi berhasil dihapus', 'success')
    setTimeout(() => {
      router.push('/destinations')
    }, 1500)
  } catch (error) {
    console.error('Error deleting destination:', error)
    showSnackbar('Gagal menghapus destinasi', 'error')
  } finally {
    deleting.value = false
    deleteDialog.value = false
  }
}

onMounted(() => {
  fetchDestination()
})
</script>

<style scoped>
/* ============================================
   CLEAN DESIGN SYSTEM - Travel Website
   ============================================ */

/* Variables */
:root {
  --spacing-unit: 8px;
  --border-radius: 12px;
  --transition: all 0.2s ease;
}

/* Loading */
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 60vh;
}

/* Hero Section with Background Image */
.hero-section {
  position: relative;
  min-height: 480px;
  padding-top: 36px;
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  align-items: flex-end;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.5) 50%, rgba(0, 0, 0, 0.2) 100%);
}

.hero-container {
  position: relative;
  z-index: 2;
  padding-bottom: 48px;
}

.hero-content {
  max-width: 800px;
}

.breadcrumbs {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
}

.breadcrumb-link {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: var(--transition);
}

.breadcrumb-link:hover {
  color: white;
}

.breadcrumb-separator {
  color: rgba(255, 255, 255, 0.5);
}

.breadcrumb-current {
  color: white;
}

.hero-title {
  font-size: 48px;
  font-weight: 700;
  color: white;
  margin: 0 0 24px;
  line-height: 1.2;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.hero-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
  margin-bottom: 24px;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: white;
  font-size: 14px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
}

.user-avatar {
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.user-details {
  color: white;
}

.user-label {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0 0 4px;
}

.user-name {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
}

/* Content Section */
.content-section {
  padding-top: 48px;
  padding-bottom: 80px;
}

/* Quick Stats */
.quick-stats {
  display: flex;
  align-items: center;
  gap: 32px;
  padding: 24px;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  margin-bottom: 48px;
}

.stat-item {
  text-align: center;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #1A1A1A;
  line-height: 1;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 13px;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-divider {
  width: 1px;
  height: 40px;
  background: #E0E0E0;
}

/* Content Blocks */
.content-block {
  margin-bottom: 56px;
}

.section-title {
  font-size: 24px;
  font-weight: 700;
  color: #1A1A1A;
  margin: 0 0 24px;
}

.description-text {
  font-size: 16px;
  line-height: 1.8;
  color: #444;
  margin: 0;
}

/* Photo Grid */
.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

.photo-item {
  position: relative;
  aspect-ratio: 1;
  border-radius: var(--border-radius);
  overflow: hidden;
  cursor: pointer;
  transition: var(--transition);
}

.photo-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.gallery-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.primary-label {
  position: absolute;
  top: 8px;
  right: 8px;
  background: white;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  color: #1976D2;
}

/* Map Card */
.map-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.map-info {
  padding: 24px;
}

.map-info-item {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 16px;
}

.map-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.map-value {
  font-size: 14px;
  color: #333;
  font-family: monospace;
}

/* Tips List */
.tips-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.tip-item {
  display: flex;
  gap: 16px;
  padding: 20px;
  background: #F8F9FA;
  border-radius: var(--border-radius);
}

.tip-number {
  flex-shrink: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #1976D2;
  color: white;
  border-radius: 50%;
  font-weight: 700;
  font-size: 14px;
}

.tip-text {
  flex: 1;
  font-size: 15px;
  line-height: 1.6;
  color: #444;
}

/* Comments */
.comment-form,
.login-prompt {
  padding: 24px;
  background: #F8F9FA;
  border-radius: var(--border-radius);
  margin-bottom: 32px;
}

.comment-textarea,
.reply-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #E0E0E0;
  border-radius: 8px;
  font-size: 15px;
  font-family: inherit;
  resize: vertical;
  transition: var(--transition);
}

.comment-textarea:focus,
.reply-textarea:focus {
  outline: none;
  border-color: #1976D2;
}

.comment-actions,
.reply-actions {
  margin-top: 12px;
  display: flex;
  gap: 8px;
}

.login-prompt {
  text-align: center;
}

.login-prompt p {
  margin: 0 0 16px;
  color: #666;
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.comment-item {
  padding-bottom: 32px;
  border-bottom: 1px solid #E0E0E0;
}

.comment-item:last-child {
  border-bottom: none;
}

.comment-header {
  display: flex;
  gap: 12px;
  margin-bottom: 12px;
}

.comment-avatar {
  flex-shrink: 0;
}

.comment-meta {
  flex: 1;
}

.comment-author {
  font-weight: 600;
  color: #1A1A1A;
  font-size: 15px;
  margin-bottom: 2px;
}

.comment-date {
  font-size: 13px;
  color: #999;
}

.comment-content {
  margin-bottom: 12px;
  font-size: 15px;
  line-height: 1.6;
  color: #444;
}

.comment-actions {
  display: flex;
  gap: 16px;
}

.comment-action-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  padding: 4px 8px;
  color: #666;
  font-size: 13px;
  cursor: pointer;
  transition: var(--transition);
  border-radius: 4px;
}

.comment-action-btn:hover {
  background: #F5F5F5;
  color: #1976D2;
}

.reply-form {
  margin-top: 16px;
  padding: 16px;
  background: #F8F9FA;
  border-radius: 8px;
}

.replies-list {
  margin-top: 24px;
  margin-left: 52px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.reply-item {
  padding-bottom: 24px;
  border-bottom: 1px solid #F0F0F0;
}

.reply-item:last-child {
  border-bottom: none;
}

.empty-comments {
  text-align: center;
  padding: 48px;
  color: #999;
}

.empty-comments p {
  margin-top: 16px;
}

/* Sidebar */
.sidebar {
  position: sticky;
  top: 90px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.sidebar-card {
  padding: 24px;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}

.sidebar-title {
  font-size: 16px;
  font-weight: 700;
  color: #1A1A1A;
  margin: 0 0 16px;
}

/* Like Button */
.like-button {
  border-radius: 8px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  height: 48px !important;
}

.like-count {
  text-align: center;
  margin: 12px 0 0;
  font-size: 13px;
  color: #666;
}

/* Share Buttons */
.share-buttons {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
}

.share-btn {
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #E0E0E0;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
}

.share-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.share-btn.whatsapp:hover {
  background: #25D366;
  border-color: #25D366;
  color: white;
}

.share-btn.facebook:hover {
  background: #1877F2;
  border-color: #1877F2;
  color: white;
}

.share-btn.twitter:hover {
  background: #1DA1F2;
  border-color: #1DA1F2;
  color: white;
}

.share-btn.link:hover {
  background: #666;
  border-color: #666;
  color: white;
}

/* Info List */
.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  padding-bottom: 16px;
  border-bottom: 1px solid #F0F0F0;
}

.info-item:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.info-label {
  font-size: 14px;
  color: #666;
}

.info-value {
  font-size: 14px;
  font-weight: 600;
  color: #1A1A1A;
  text-align: right;
}

/* Related Items */
.related-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.related-item {
  display: flex;
  gap: 12px;
  padding: 8px;
  border-radius: 8px;
  text-decoration: none;
  transition: var(--transition);
}

.related-item:hover {
  background: #F8F9FA;
}

.related-image {
  width: 64px;
  height: 64px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}

.related-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.related-name {
  font-size: 14px;
  font-weight: 600;
  color: #1A1A1A;
  margin-bottom: 4px;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.related-meta {
  font-size: 12px;
  color: #999;
}

/* Lightbox */
.lightbox {
  position: relative;
  background: #000;
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.lightbox-image {
  max-width: 100%;
  max-height: 80vh;
  object-fit: contain;
}

.lightbox-close {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: var(--transition);
  z-index: 10;
}

.lightbox-close:hover {
  background: rgba(255, 255, 255, 0.2);
}

.lightbox-prev,
.lightbox-next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.1);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: var(--transition);
  z-index: 10;
}

.lightbox-prev:hover,
.lightbox-next:hover {
  background: rgba(255, 255, 255, 0.2);
}

.lightbox-prev {
  left: 16px;
}

.lightbox-next {
  right: 16px;
}

.lightbox-counter {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  padding: 8px 16px;
  background: rgba(0, 0, 0, 0.7);
  color: white;
  border-radius: 16px;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 960px) {
  .hero-section {
    min-height: 450px;
    background-attachment: scroll;
  }

  .hero-title {
    font-size: 36px;
  }

  .quick-stats {
    gap: 16px;
    overflow-x: auto;
  }

  .sidebar {
    position: relative;
    top: 0;
  }
}

@media (max-width: 600px) {
  .hero-section {
    min-height: 380px;
    padding-top: 24px;
  }

  .content-section {
    padding-top: 24px;
    padding-bottom: 48px;
  }

  .hero-title {
    font-size: 28px;
  }

  .photo-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .replies-list {
    margin-left: 24px;
  }
}
</style>
