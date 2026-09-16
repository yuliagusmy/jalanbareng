<template>
  <div class="admin-stories-page">
    <v-container class="py-8">
      <!-- Page Header -->
      <v-card elevation="0" rounded="xl" class="mb-6 border">
        <v-card-title class="d-flex flex-wrap align-center justify-space-between px-6 py-4 ga-3">
          <div class="d-flex align-center">
            <v-avatar color="primary" variant="tonal" size="48" class="mr-4">
              <v-icon size="28" color="primary">mdi-feather</v-icon>
            </v-avatar>
            <div>
              <h1 class="text-h5 font-weight-bold text-grey-darken-4 mb-0">Kurasi & Kelola Tulisan</h1>
              <p class="text-caption text-grey-darken-1 mb-0">Moderasi tulisan komunitas sebelum diterbitkan ke Beranda</p>
            </div>
          </div>

          <div class="d-flex align-center ga-2">
            <v-btn
              variant="outlined"
              color="primary"
              rounded="pill"
              prepend-icon="mdi-refresh"
              :loading="loading"
              @click="fetchStories"
            >
              Segarkan
            </v-btn>
          </div>
        </v-card-title>
      </v-card>

      <!-- Stats Summary Cards -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="0" rounded="xl" class="pa-4 border bg-blue-lighten-5">
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-caption font-weight-bold text-blue-darken-3">TOTAL TULISAN</div>
                <div class="text-h4 font-weight-bold text-blue-darken-4">{{ counts.all }}</div>
              </div>
              <v-avatar color="blue" size="44">
                <v-icon color="white">mdi-book-multiple</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="0" rounded="xl" class="pa-4 border bg-amber-lighten-5">
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-caption font-weight-bold text-amber-darken-3">BUTUH KURASI</div>
                <div class="text-h4 font-weight-bold text-amber-darken-4">{{ counts.pending }}</div>
              </div>
              <v-avatar color="amber-darken-2" size="44">
                <v-icon color="white">mdi-clock-alert-outline</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="0" rounded="xl" class="pa-4 border bg-green-lighten-5">
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-caption font-weight-bold text-green-darken-3">DITERBITKAN</div>
                <div class="text-h4 font-weight-bold text-green-darken-4">{{ counts.approved }}</div>
              </div>
              <v-avatar color="green-darken-1" size="44">
                <v-icon color="white">mdi-check-decagram</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="0" rounded="xl" class="pa-4 border bg-red-lighten-5">
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-caption font-weight-bold text-red-darken-3">DITOLAK</div>
                <div class="text-h4 font-weight-bold text-red-darken-4">{{ counts.rejected }}</div>
              </div>
              <v-avatar color="red-darken-1" size="44">
                <v-icon color="white">mdi-close-circle-outline</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Main Table Card -->
      <v-card elevation="0" rounded="xl" class="border">
        <!-- Tabs & Search Filter -->
        <div class="px-6 pt-4 border-b">
          <v-tabs v-model="activeTab" color="primary" @update:model-value="onTabChange">
            <v-tab value="pending">
              <v-badge
                v-if="counts.pending > 0"
                :content="counts.pending"
                color="amber-darken-2"
                inline
                class="mr-1"
              ></v-badge>
              Menunggu Kurasi
            </v-tab>
            <v-tab value="approved">Diterbitkan</v-tab>
            <v-tab value="rejected">Ditolak</v-tab>
            <v-tab value="all">Semua Tulisan</v-tab>
          </v-tabs>
        </div>

        <!-- Search Input -->
        <div class="px-6 py-4 bg-grey-lighten-5 border-b d-flex align-center">
          <v-text-field
            v-model="searchQuery"
            placeholder="Cari berdasarkan judul atau penulis..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            density="compact"
            rounded="lg"
            bg-color="white"
            hide-details
            clearable
            style="max-width: 400px;"
            @update:model-value="debounceSearch"
          ></v-text-field>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="pa-12 text-center">
          <v-progress-circular indeterminate color="primary" size="48"></v-progress-circular>
          <div class="text-caption text-grey mt-3">Memuat data tulisan...</div>
        </div>

        <!-- Stories Table -->
        <v-table v-else-if="stories.length > 0" hover class="stories-admin-table">
          <thead>
            <tr>
              <th class="text-left py-3 font-weight-bold">Tulisan & Penulis</th>
              <th class="text-left py-3 font-weight-bold">Nuansa Kartu</th>
              <th class="text-left py-3 font-weight-bold">Status</th>
              <th class="text-left py-3 font-weight-bold">Tanggal Kirim</th>
              <th class="text-right py-3 font-weight-bold pr-6">Aksi Kurasi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="story in stories" :key="story.id">
              <!-- Title & Author -->
              <td class="py-4">
                <div class="d-flex align-center">
                  <v-avatar rounded="lg" size="48" class="mr-3 border" color="grey-lighten-4">
                    <v-img v-if="story.cover_image_url" :src="story.cover_image_url" cover></v-img>
                    <v-icon v-else color="grey">mdi-file-document-outline</v-icon>
                  </v-avatar>
                  <div style="max-width: 360px;">
                    <div class="font-weight-bold text-subtitle-2 text-grey-darken-4 text-truncate">
                      {{ story.title }}
                    </div>
                    <div class="text-caption text-grey-darken-1">
                      Oleh: <span class="font-weight-medium">{{ story.author_name }}</span>
                      <span v-if="story.author_email"> ({{ story.author_email }})</span>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Card Style -->
              <td>
                <v-chip size="small" :color="getStyleColor(story.card_style)" variant="flat" class="text-capitalize text-white font-weight-bold">
                  {{ story.card_style || 'coral' }}
                </v-chip>
              </td>

              <!-- Status Badge -->
              <td>
                <v-chip
                  size="small"
                  :color="getStatusColor(story.status)"
                  variant="tonal"
                  class="font-weight-bold text-capitalize"
                >
                  <v-icon start size="x-small">
                    {{ story.status === 'approved' ? 'mdi-check-circle' : (story.status === 'pending' ? 'mdi-clock-outline' : 'mdi-close-circle') }}
                  </v-icon>
                  {{ formatStatusLabel(story.status) }}
                </v-chip>
              </td>

              <!-- Date -->
              <td class="text-caption text-grey-darken-1">
                {{ formatDate(story.created_at) }}
              </td>

              <!-- Action Buttons -->
              <td class="text-right pr-6">
                <div class="d-flex justify-end align-center ga-1">
                  <!-- Review / Preview Button -->
                  <v-btn
                    color="primary"
                    variant="tonal"
                    size="small"
                    rounded="pill"
                    prepend-icon="mdi-eye"
                    class="font-weight-bold"
                    @click="openPreviewDialog(story)"
                  >
                    Tinjau
                  </v-btn>

                  <!-- Direct Approve Button if pending -->
                  <v-btn
                    v-if="story.status === 'pending'"
                    color="success"
                    variant="flat"
                    size="small"
                    rounded="pill"
                    icon="mdi-check"
                    title="Setujui & Publikasikan"
                    @click="quickUpdateStatus(story, 'approved')"
                  ></v-btn>

                  <!-- Delete Button -->
                  <v-btn
                    color="error"
                    variant="text"
                    size="small"
                    icon="mdi-delete-outline"
                    title="Hapus Tulisan"
                    @click="confirmDelete(story)"
                  ></v-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </v-table>

        <!-- Empty State -->
        <div v-else class="pa-12 text-center">
          <v-icon size="64" color="grey" class="mb-3">mdi-inbox-outline</v-icon>
          <div class="text-h6 font-weight-bold text-grey-darken-3 mb-1">Tidak ada tulisan di kategori ini</div>
          <p class="text-caption text-grey">Belum ada data tulisan yang sesuai dengan filter saat ini.</p>
        </div>
      </v-card>
    </v-container>

    <!-- PREVIEW & CURATION MODAL -->
    <v-dialog v-model="previewDialog" max-width="800" scrollable>
      <v-card v-if="selectedStory" rounded="xl">
        <!-- Dialog Header -->
        <v-card-title class="d-flex align-center justify-space-between pa-5 pa-md-6 border-b">
          <div class="d-flex align-center">
            <v-chip
              size="small"
              :color="getStatusColor(selectedStory.status)"
              variant="tonal"
              class="mr-3 font-weight-bold text-capitalize"
            >
              {{ formatStatusLabel(selectedStory.status) }}
            </v-chip>
            <span class="text-subtitle-1 font-weight-bold text-grey-darken-4">
              Kurasi Tulisan Teman Jalan
            </span>
          </div>
          <v-btn icon="mdi-close" variant="text" density="comfortable" @click="previewDialog = false"></v-btn>
        </v-card-title>

        <!-- Dialog Body -->
        <v-card-text class="pa-6" style="max-height: 70vh;">
          <!-- Cover Photo -->
          <div v-if="selectedStory.cover_image_url" class="mb-6">
            <v-img
              :src="selectedStory.cover_image_url"
              height="260"
              cover
              rounded="xl"
              class="elevation-2"
            ></v-img>
          </div>

          <!-- Title & Meta -->
          <h2 class="text-h5 text-md-h4 font-weight-bold text-grey-darken-4 mb-3">
            {{ selectedStory.title }}
          </h2>

          <div class="d-flex flex-wrap align-center ga-3 text-caption text-grey-darken-1 mb-6 pb-4 border-b">
            <div class="font-weight-bold text-grey-darken-3">
              <v-icon size="small" class="mr-1">mdi-account</v-icon>
              {{ selectedStory.author_name }}
            </div>
            <span v-if="selectedStory.author_email">• {{ selectedStory.author_email }}</span>
            <span v-if="selectedStory.author_instagram">• @{{ selectedStory.author_instagram }}</span>
            <span>• Dikirim: {{ formatDate(selectedStory.created_at) }}</span>
          </div>

          <!-- Excerpt -->
          <div v-if="selectedStory.excerpt" class="pa-4 bg-grey-lighten-4 rounded-lg mb-6 border-s-lg border-primary">
            <div class="text-caption font-weight-bold text-primary mb-1">RINGKASAN (EXCERPT)</div>
            <div class="text-body-2 text-grey-darken-3">{{ selectedStory.excerpt }}</div>
          </div>

          <!-- Full Content -->
          <div class="text-caption font-weight-bold text-grey-darken-1 mb-2">ISI TULISAN LENGKAP:</div>
          <div class="story-preview-content pa-5 border rounded-xl bg-white mb-6" v-html="selectedStory.content"></div>

          <!-- Style & Feature Customizer -->
          <v-card variant="outlined" rounded="lg" class="pa-4 mb-4 bg-grey-lighten-5">
            <div class="text-subtitle-2 font-weight-bold mb-3">Pengaturan Tampilan Kartu di Beranda</div>
            <v-row dense align="center">
              <v-col cols="12" sm="6">
                <label class="text-caption font-weight-bold d-block mb-1">Gaya Warna Kartu</label>
                <v-select
                  v-model="curationForm.card_style"
                  :items="cardStyles"
                  item-title="label"
                  item-value="value"
                  density="compact"
                  variant="outlined"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-checkbox
                  v-model="curationForm.is_featured"
                  label="Jadikan Tulisan Unggulan"
                  color="primary"
                  density="compact"
                  hide-details
                ></v-checkbox>
              </v-col>
            </v-row>
          </v-card>

          <!-- Rejection Reason Input (if rejecting) -->
          <div v-if="showRejectInput" class="mt-4">
            <label class="text-subtitle-2 font-weight-bold text-error mb-1 d-block">
              Alasan Penolakan (Catatan Kurasi)
            </label>
            <v-textarea
              v-model="curationForm.rejection_reason"
              rows="3"
              placeholder="Jelaskan alasan tulisan belum dapat diterbitkan..."
              variant="outlined"
              density="comfortable"
              rounded="lg"
              hide-details
            ></v-textarea>
          </div>
        </v-card-text>

        <!-- Dialog Actions -->
        <v-card-actions class="pa-5 pa-md-6 border-t d-flex flex-wrap justify-space-between ga-2">
          <!-- View Live link if approved -->
          <v-btn
            v-if="selectedStory.status === 'approved'"
            :to="`/cerita/${selectedStory.slug}`"
            target="_blank"
            variant="text"
            color="primary"
            prepend-icon="mdi-open-in-new"
          >
            Lihat Halaman Publik
          </v-btn>
          <div v-else></div>

          <div class="d-flex ga-2">
            <!-- Reject Action -->
            <v-btn
              v-if="!showRejectInput && selectedStory.status !== 'rejected'"
              color="error"
              variant="outlined"
              rounded="pill"
              @click="showRejectInput = true"
            >
              <v-icon start>mdi-close</v-icon>
              Tolak Tulisan
            </v-btn>
            <v-btn
              v-else-if="showRejectInput"
              color="error"
              variant="flat"
              rounded="pill"
              :loading="curating"
              @click="submitCuration('rejected')"
            >
              Konfirmasi Tolak
            </v-btn>

            <!-- Approve Action -->
            <v-btn
              color="success"
              variant="flat"
              rounded="pill"
              class="px-6 font-weight-bold"
              :loading="curating"
              @click="submitCuration('approved')"
            >
              <v-icon start>mdi-check-decagram</v-icon>
              {{ selectedStory.status === 'approved' ? 'Simpan Perubahan' : 'Setujui & Publikasikan' }}
            </v-btn>
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- CONFIRM DELETE DIALOG -->
    <v-dialog v-model="deleteDialog" max-width="450">
      <v-card rounded="xl" class="pa-6 text-center">
        <v-avatar color="red-lighten-5" size="56" class="mb-3 mx-auto">
          <v-icon color="red" size="32">mdi-delete-alert-outline</v-icon>
        </v-avatar>
        <h3 class="text-h6 font-weight-bold mb-2">Hapus Tulisan Ini?</h3>
        <p class="text-body-2 text-grey-darken-1 mb-6">
          Tulisan "{{ storyToDelete?.title }}" akan dihapus permanen beserta foto sampulnya.
        </p>
        <div class="d-flex justify-center ga-3">
          <v-btn variant="text" rounded="pill" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" rounded="pill" :loading="deleting" @click="executeDelete">
            Ya, Hapus
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'

definePageMeta({
  layout: 'admin',
  middleware: ['auth', 'admin'],
})

const { api } = useApi()

const stories = ref<any[]>([])
const loading = ref(true)
const curating = ref(false)
const deleting = ref(false)
const activeTab = ref('pending')
const searchQuery = ref('')

const counts = reactive({
  all: 0,
  pending: 0,
  approved: 0,
  rejected: 0,
})

const previewDialog = ref(false)
const selectedStory = ref<any>(null)
const showRejectInput = ref(false)

const deleteDialog = ref(false)
const storyToDelete = ref<any>(null)

const cardStyles = [
  { value: 'coral', label: 'Coral Merah' },
  { value: 'magenta', label: 'Magenta Crimson' },
  { value: 'amber', label: 'Amber Kuning' },
  { value: 'photo', label: 'Foto Cerita' },
]

const curationForm = reactive({
  card_style: 'coral',
  is_featured: false,
  rejection_reason: '',
})

let searchTimeout: any = null

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getStyleColor = (style: string) => {
  switch (style) {
    case 'coral': return '#FA4D56'
    case 'magenta': return '#E02F6B'
    case 'amber': return '#F4A228'
    default: return '#374151'
  }
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'approved': return 'success'
    case 'pending': return 'warning'
    case 'rejected': return 'error'
    default: return 'grey'
  }
}

const formatStatusLabel = (status: string) => {
  switch (status) {
    case 'approved': return 'Diterbitkan'
    case 'pending': return 'Menunggu Kurasi'
    case 'rejected': return 'Ditolak'
    default: return status
  }
}

const fetchStories = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/stories', {
      params: {
        status: activeTab.value,
        q: searchQuery.value || undefined,
      }
    })

    stories.value = res.data.stories.data || res.data.stories || []
    if (res.data.counts) {
      Object.assign(counts, res.data.counts)
    }
  } catch (err) {
    console.error('Failed to fetch admin stories:', err)
  } finally {
    loading.value = false
  }
}

const onTabChange = () => {
  fetchStories()
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchStories()
  }, 400)
}

const openPreviewDialog = (story: any) => {
  selectedStory.value = story
  curationForm.card_style = story.card_style || 'coral'
  curationForm.is_featured = !!story.is_featured
  curationForm.rejection_reason = story.rejection_reason || ''
  showRejectInput.value = false
  previewDialog.value = true
}

const quickUpdateStatus = async (story: any, status: string) => {
  try {
    await api.put(`/admin/stories/${story.id}/status`, {
      status,
      card_style: story.card_style || 'coral',
      is_featured: story.is_featured,
    })
    await fetchStories()
  } catch (err) {
    console.error('Failed to quick update status:', err)
  }
}

const submitCuration = async (status: string) => {
  if (!selectedStory.value) return
  curating.value = true

  try {
    await api.put(`/admin/stories/${selectedStory.value.id}/status`, {
      status,
      card_style: curationForm.card_style,
      is_featured: curationForm.is_featured,
      rejection_reason: status === 'rejected' ? curationForm.rejection_reason : null,
    })

    previewDialog.value = false
    await fetchStories()
  } catch (err) {
    console.error('Failed to curate story:', err)
  } finally {
    curating.value = false
  }
}

const confirmDelete = (story: any) => {
  storyToDelete.value = story
  deleteDialog.value = true
}

const executeDelete = async () => {
  if (!storyToDelete.value) return
  deleting.value = true

  try {
    await api.delete(`/admin/stories/${storyToDelete.value.id}`)
    deleteDialog.value = false
    storyToDelete.value = null
    await fetchStories()
  } catch (err) {
    console.error('Failed to delete story:', err)
  } finally {
    deleting.value = false
  }
}

onMounted(() => {
  fetchStories()
})
</script>

<style scoped>
.stories-admin-table th {
  background-color: #f8fafc;
  color: #475569;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.story-preview-content {
  line-height: 1.75;
  color: #334155;
}
</style>
