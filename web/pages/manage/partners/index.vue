<template>
  <div class="admin-partners-page">
    <v-container class="py-8">

      <!-- Page Header -->
      <v-card elevation="0" rounded="xl" class="mb-6 border">
        <v-card-title class="admin-page-header px-6 py-4">
          <div class="header-content">
            <div class="header-main">
              <v-avatar color="primary" variant="tonal" size="48" class="header-icon flex-shrink-0">
                <v-icon size="26" color="primary">mdi-handshake-outline</v-icon>
              </v-avatar>
              <div class="header-text">
                <h1 class="text-h5 font-weight-bold text-grey-darken-4 mb-0">Kelola Mitra & Kolaborator</h1>
                <p class="text-caption text-grey-darken-1 mb-0">Brand, instansi pemerintah, komunitas, dan media partner Jalan Bareng</p>
              </div>
            </div>
            <div class="header-actions">
              <v-btn
                color="primary"
                variant="flat"
                rounded="pill"
                prepend-icon="mdi-plus"
                @click="openForm()"
              >
                Tambah Mitra
              </v-btn>
            </div>
          </div>
        </v-card-title>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6" dense>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="xl" class="stat-card-compact border bg-blue-lighten-5">
            <v-avatar color="blue" size="44"><v-icon color="white">mdi-handshake-outline</v-icon></v-avatar>
            <div class="stat-number text-blue-darken-4">{{ counts.total }}</div>
            <div class="stat-label text-blue-darken-3">Total Mitra</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="xl" class="stat-card-compact border bg-green-lighten-5">
            <v-avatar color="green-darken-1" size="44"><v-icon color="white">mdi-check-circle-outline</v-icon></v-avatar>
            <div class="stat-number text-green-darken-4">{{ counts.active }}</div>
            <div class="stat-label text-green-darken-3">Aktif Tampil</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="xl" class="stat-card-compact border bg-purple-lighten-5">
            <v-avatar color="purple" size="44"><v-icon color="white">mdi-store-outline</v-icon></v-avatar>
            <div class="stat-number text-purple-darken-4">{{ counts.brand }}</div>
            <div class="stat-label text-purple-darken-3">Brand / Korporat</div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="3">
          <v-card elevation="0" rounded="xl" class="stat-card-compact border bg-amber-lighten-5">
            <v-avatar color="amber-darken-2" size="44"><v-icon color="white">mdi-account-group-outline</v-icon></v-avatar>
            <div class="stat-number text-amber-darken-4">{{ counts.community }}</div>
            <div class="stat-label text-amber-darken-3">Komunitas</div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="xl" class="mb-6 pa-4 border">
        <div class="d-flex align-center ga-3 flex-wrap">
          <v-text-field
            v-model="search"
            placeholder="Cari nama mitra..."
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            rounded="lg"
            density="compact"
            hide-details
            clearable
            class="flex-grow-1"
            style="min-width: 200px;"
            @update:model-value="debounceSearch"
          />
          <v-select
            v-model="filterCategory"
            :items="categoryOptions"
            item-title="label"
            item-value="value"
            placeholder="Semua Kategori"
            variant="outlined"
            rounded="lg"
            density="compact"
            hide-details
            clearable
            style="max-width: 200px;"
            @update:model-value="fetchPartners"
          >
            <template v-slot:prepend-inner><v-icon size="18">mdi-filter-variant</v-icon></template>
          </v-select>
          <v-select
            v-model="filterActive"
            :items="activeOptions"
            item-title="label"
            item-value="value"
            placeholder="Semua Status"
            variant="outlined"
            rounded="lg"
            density="compact"
            hide-details
            clearable
            style="max-width: 160px;"
            @update:model-value="fetchPartners"
          />
        </div>
      </v-card>

      <!-- Loading -->
      <div v-if="loading" class="py-2">
        <v-skeleton-loader v-for="i in 4" :key="i" type="list-item-avatar-two-line" rounded="xl" class="mb-3 border" />
      </div>

      <!-- Empty State -->
      <v-card v-else-if="partners.length === 0" elevation="0" rounded="xl" class="pa-12 text-center border">
        <v-icon size="64" color="grey-lighten-1" class="mb-3">mdi-handshake-outline</v-icon>
        <h3 class="text-h6 font-weight-bold text-grey-darken-3 mb-1">Belum ada mitra</h3>
        <p class="text-caption text-grey mb-4">Tambahkan mitra pertama Jalan Bareng untuk ditampilkan di halaman publik.</p>
        <v-btn color="primary" rounded="pill" prepend-icon="mdi-plus" @click="openForm()">Tambah Mitra</v-btn>
      </v-card>

      <!-- Partners List -->
      <div v-else>
        <!-- Mobile: card view -->
        <div v-if="$vuetify.display.xs">
          <v-card
            v-for="item in partners"
            :key="item.id"
            elevation="0"
            rounded="xl"
            class="pa-4 mb-3 border"
          >
            <div class="d-flex align-center ga-3 mb-3">
              <!-- Logo / Initial avatar -->
              <v-avatar size="52" rounded="lg" :color="item.bg_color || '#F3F4F6'" class="flex-shrink-0 border">
                <v-img v-if="item.logo_url" :src="item.logo_url" :alt="item.name" cover />
                <span v-else class="font-weight-black text-body-1" :style="{ color: item.text_color || '#111827' }">
                  {{ item.initial || item.name.charAt(0) }}
                </span>
              </v-avatar>
              <div class="flex-grow-1 min-w-0">
                <div class="font-weight-bold text-grey-darken-4 text-truncate">{{ item.name }}</div>
                <div class="d-flex align-center ga-2 flex-wrap mt-1">
                  <v-chip size="x-small" :color="getCategoryColor(item.category)" variant="tonal" class="font-weight-bold">
                    {{ getCategoryLabel(item.category) }}
                  </v-chip>
                  <v-chip size="x-small" :color="item.is_active ? 'success' : 'grey'" variant="flat" class="text-white font-weight-bold">
                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                  </v-chip>
                </div>
                <div v-if="item.role" class="text-caption text-grey-darken-1 mt-1 text-truncate">{{ item.role }}</div>
              </div>
            </div>
            <div class="d-flex justify-end ga-2 pt-3 border-t">
              <v-btn size="small" variant="tonal" color="primary" rounded="pill" prepend-icon="mdi-pencil" @click="openForm(item)">Edit</v-btn>
              <v-btn size="small" variant="text" color="error" rounded="pill" icon="mdi-delete-outline" @click="confirmDelete(item)" />
            </div>
          </v-card>
        </div>

        <!-- Desktop: table -->
        <v-card v-else elevation="0" rounded="xl" class="border">
          <v-table hover>
            <thead>
              <tr>
                <th class="text-left py-3 font-weight-bold">Mitra</th>
                <th class="text-left py-3 font-weight-bold">Kategori</th>
                <th class="text-left py-3 font-weight-bold">Peran / Tipe Kolaborasi</th>
                <th class="text-left py-3 font-weight-bold">Status</th>
                <th class="text-left py-3 font-weight-bold">Urutan</th>
                <th class="text-right py-3 font-weight-bold pr-6">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in partners" :key="item.id">
                <!-- Logo + Nama -->
                <td class="py-3">
                  <div class="d-flex align-center ga-3">
                    <v-avatar size="44" rounded="lg" :color="item.bg_color || '#F3F4F6'" class="border flex-shrink-0">
                      <v-img v-if="item.logo_url" :src="item.logo_url" :alt="item.name" cover />
                      <span v-else class="font-weight-black text-caption" :style="{ color: item.text_color || '#111827' }">
                        {{ item.initial || item.name.charAt(0) }}
                      </span>
                    </v-avatar>
                    <div>
                      <div class="font-weight-bold text-grey-darken-4">{{ item.name }}</div>
                      <a v-if="item.website_url" :href="item.website_url" target="_blank" rel="noopener noreferrer"
                        class="text-caption text-primary text-decoration-none">
                        {{ item.website_url.replace(/^https?:\/\//, '') }}
                      </a>
                    </div>
                  </div>
                </td>
                <!-- Kategori -->
                <td>
                  <v-chip size="small" :color="getCategoryColor(item.category)" variant="tonal" class="font-weight-bold">
                    {{ getCategoryLabel(item.category) }}
                  </v-chip>
                </td>
                <!-- Peran -->
                <td class="text-caption text-grey-darken-2">
                  <div v-if="item.role">{{ item.role }}</div>
                  <div v-if="item.collab_type" class="text-grey">{{ item.collab_type }}</div>
                </td>
                <!-- Status -->
                <td>
                  <v-chip
                    size="small"
                    :color="item.is_active ? 'success' : 'grey'"
                    variant="flat"
                    class="text-white font-weight-bold"
                    style="cursor: pointer"
                    @click="toggleActive(item)"
                  >
                    {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                  </v-chip>
                </td>
                <!-- Urutan -->
                <td class="text-caption text-grey-darken-2">{{ item.sort_order }}</td>
                <!-- Aksi -->
                <td class="text-right pr-6">
                  <div class="d-flex justify-end ga-1">
                    <v-btn icon size="small" variant="text" color="primary" @click="openForm(item)">
                      <v-icon size="18">mdi-pencil-outline</v-icon>
                      <v-tooltip activator="parent" location="top">Edit</v-tooltip>
                    </v-btn>
                    <v-btn icon size="small" variant="text" color="error" @click="confirmDelete(item)">
                      <v-icon size="18">mdi-delete-outline</v-icon>
                      <v-tooltip activator="parent" location="top">Hapus</v-tooltip>
                    </v-btn>
                  </div>
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </div>
    </v-container>

    <!-- Form Dialog (Add / Edit) -->
    <v-dialog v-model="formDialog" max-width="560" scrollable>
      <v-card rounded="xl">
        <v-card-title class="d-flex align-center justify-space-between pa-5 border-b">
          <span class="text-subtitle-1 font-weight-bold text-grey-darken-4">
            {{ editingPartner ? 'Edit Mitra' : 'Tambah Mitra Baru' }}
          </span>
          <v-btn icon="mdi-close" variant="text" density="comfortable" @click="formDialog = false" />
        </v-card-title>

        <v-card-text class="pa-5">
          <v-form ref="formRef" v-model="formValid">
            <div class="d-flex flex-column ga-4">
              <!-- Nama -->
              <v-text-field
                v-model="form.name"
                label="Nama Mitra *"
                variant="outlined"
                rounded="lg"
                density="comfortable"
                hide-details="auto"
                :rules="[v => !!v || 'Nama wajib diisi']"
              />

              <!-- Kategori + Initial -->
              <v-row dense>
                <v-col cols="8">
                  <v-select
                    v-model="form.category"
                    :items="categoryOptions"
                    item-title="label"
                    item-value="value"
                    label="Kategori *"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details="auto"
                    :rules="[v => !!v || 'Kategori wajib dipilih']"
                  />
                </v-col>
                <v-col cols="4">
                  <v-text-field
                    v-model="form.initial"
                    label="Singkatan"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    placeholder="BRI"
                    maxlength="10"
                  />
                </v-col>
              </v-row>

              <!-- Peran + Tipe Kolaborasi -->
              <v-row dense>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.role"
                    label="Peran"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    placeholder="Sponsor Utama"
                  />
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.collab_type"
                    label="Tipe Kolaborasi"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    placeholder="Sponsorship"
                  />
                </v-col>
              </v-row>

              <!-- URL Logo -->
              <v-text-field
                v-model="form.logo_url"
                label="URL Logo"
                variant="outlined"
                rounded="lg"
                density="comfortable"
                hide-details
                placeholder="https://..."
                prepend-inner-icon="mdi-image-outline"
              />

              <!-- Website -->
              <v-text-field
                v-model="form.website_url"
                label="Website"
                variant="outlined"
                rounded="lg"
                density="comfortable"
                hide-details
                placeholder="https://..."
                prepend-inner-icon="mdi-web"
              />

              <!-- Warna BG + Text -->
              <v-row dense>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.bg_color"
                    label="Warna Background"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    placeholder="#F3F4F6"
                    maxlength="20"
                  >
                    <template v-slot:prepend-inner>
                      <div class="color-swatch mr-2" :style="{ background: form.bg_color || '#F3F4F6' }"></div>
                    </template>
                  </v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-text-field
                    v-model="form.text_color"
                    label="Warna Teks Fallback"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    placeholder="#111827"
                    maxlength="20"
                  >
                    <template v-slot:prepend-inner>
                      <div class="color-swatch mr-2" :style="{ background: form.text_color || '#111827' }"></div>
                    </template>
                  </v-text-field>
                </v-col>
              </v-row>

              <!-- Urutan + Status -->
              <v-row dense>
                <v-col cols="6">
                  <v-text-field
                    v-model.number="form.sort_order"
                    label="Urutan Tampil"
                    variant="outlined"
                    rounded="lg"
                    density="comfortable"
                    hide-details
                    type="number"
                    min="0"
                  />
                </v-col>
                <v-col cols="6" class="d-flex align-center">
                  <v-switch
                    v-model="form.is_active"
                    label="Tampilkan di halaman publik"
                    color="primary"
                    density="comfortable"
                    hide-details
                    inset
                  />
                </v-col>
              </v-row>

              <!-- Preview -->
              <div class="preview-box pa-4 rounded-xl border bg-grey-lighten-5">
                <div class="text-caption font-weight-bold text-grey-darken-1 mb-2">PREVIEW KARTU</div>
                <div class="d-flex align-center ga-3">
                  <v-avatar size="52" rounded="lg" :color="form.bg_color || '#F3F4F6'" class="border flex-shrink-0">
                    <v-img v-if="form.logo_url" :src="form.logo_url" cover />
                    <span v-else class="font-weight-black text-body-1" :style="{ color: form.text_color || '#111827' }">
                      {{ form.initial || (form.name ? form.name.charAt(0) : '?') }}
                    </span>
                  </v-avatar>
                  <div>
                    <div class="font-weight-bold text-grey-darken-4">{{ form.name || 'Nama Mitra' }}</div>
                    <div class="text-caption text-grey-darken-1">{{ form.role || 'Peran belum diisi' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-5 border-t">
          <v-btn variant="text" rounded="pill" @click="formDialog = false">Batal</v-btn>
          <v-spacer />
          <v-btn
            color="primary"
            variant="flat"
            rounded="pill"
            :loading="saving"
            :disabled="!formValid"
            @click="submitForm"
          >
            {{ editingPartner ? 'Simpan Perubahan' : 'Tambah Mitra' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Delete Confirm Dialog -->
    <v-dialog v-model="deleteDialog" max-width="420">
      <v-card rounded="xl" class="pa-6 text-center">
        <v-avatar color="red-lighten-5" size="56" class="mb-3 mx-auto">
          <v-icon color="red" size="28">mdi-delete-alert-outline</v-icon>
        </v-avatar>
        <h3 class="text-h6 font-weight-bold mb-2">Hapus Mitra Ini?</h3>
        <p class="text-body-2 text-grey-darken-1 mb-6">
          "<strong>{{ partnerToDelete?.name }}</strong>" akan dihapus permanen dari daftar mitra.
        </p>
        <div class="d-flex justify-center ga-3">
          <v-btn variant="text" rounded="pill" @click="deleteDialog = false">Batal</v-btn>
          <v-btn color="error" variant="flat" rounded="pill" :loading="deleting" @click="executeDelete">
            Ya, Hapus
          </v-btn>
        </div>
      </v-card>
    </v-dialog>

    <!-- Snackbar -->
    <v-snackbar v-model="snackbar" :color="snackbarColor" rounded="pill" location="bottom" :timeout="3000">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'

definePageMeta({
  layout: 'solid',
  middleware: ['auth', 'admin'],
})

const { api } = useApi()

// State
const partners = ref<any[]>([])
const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const search = ref('')
const filterCategory = ref<string | null>(null)
const filterActive = ref<boolean | null>(null)
const counts = reactive({ total: 0, active: 0, inactive: 0, brand: 0, government: 0, community: 0 })

// Dialogs
const formDialog = ref(false)
const deleteDialog = ref(false)
const editingPartner = ref<any>(null)
const partnerToDelete = ref<any>(null)
const formRef = ref<any>(null)
const formValid = ref(false)

// Snackbar
const snackbar = ref(false)
const snackbarText = ref('')
const snackbarColor = ref('success')

// Form
const defaultForm = () => ({
  name: '',
  category: 'brand',
  role: '',
  collab_type: '',
  initial: '',
  logo_url: '',
  bg_color: '#F3F4F6',
  text_color: '#111827',
  website_url: '',
  sort_order: 0,
  is_active: true,
})
const form = reactive(defaultForm())

let searchTimeout: any = null

// Options
const categoryOptions = [
  { label: 'Brand / Korporat', value: 'brand' },
  { label: 'Pemerintah', value: 'government' },
  { label: 'BUMN', value: 'bumn' },
  { label: 'Komunitas', value: 'community' },
  { label: 'Media', value: 'media' },
]

const activeOptions = [
  { label: 'Aktif', value: true },
  { label: 'Nonaktif', value: false },
]

const getCategoryLabel = (cat: string) =>
  categoryOptions.find(o => o.value === cat)?.label ?? cat

const getCategoryColor = (cat: string) => {
  const map: Record<string, string> = {
    brand: 'purple', government: 'blue', bumn: 'teal', community: 'orange', media: 'pink',
  }
  return map[cat] ?? 'grey'
}

// Fetch
const fetchPartners = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/partners', {
      params: {
        q: search.value || undefined,
        category: filterCategory.value || undefined,
        is_active: filterActive.value !== null ? filterActive.value : undefined,
      },
    })
    partners.value = res.data.data || []
    if (res.data.counts) Object.assign(counts, res.data.counts)
  } catch (err) {
    showSnackbar('Gagal memuat data mitra', 'error')
  } finally {
    loading.value = false
  }
}

const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => fetchPartners(), 350)
}

// Form open/close
const openForm = (partner?: any) => {
  editingPartner.value = partner ?? null
  if (partner) {
    Object.assign(form, {
      name: partner.name,
      category: partner.category,
      role: partner.role ?? '',
      collab_type: partner.collab_type ?? '',
      initial: partner.initial ?? '',
      logo_url: partner.logo_url ?? '',
      bg_color: partner.bg_color ?? '#F3F4F6',
      text_color: partner.text_color ?? '#111827',
      website_url: partner.website_url ?? '',
      sort_order: partner.sort_order ?? 0,
      is_active: partner.is_active,
    })
  } else {
    Object.assign(form, defaultForm())
  }
  formDialog.value = true
}

// Submit
const submitForm = async () => {
  const { valid } = await formRef.value?.validate()
  if (!valid) return
  saving.value = true
  try {
    if (editingPartner.value) {
      await api.put(`/admin/partners/${editingPartner.value.id}`, form)
      showSnackbar('Mitra berhasil diperbarui')
    } else {
      await api.post('/admin/partners', form)
      showSnackbar('Mitra berhasil ditambahkan')
    }
    formDialog.value = false
    await fetchPartners()
  } catch (err: any) {
    showSnackbar(err?.response?.data?.message ?? 'Terjadi kesalahan', 'error')
  } finally {
    saving.value = false
  }
}

// Toggle active
const toggleActive = async (item: any) => {
  try {
    await api.put(`/admin/partners/${item.id}`, { is_active: !item.is_active })
    item.is_active = !item.is_active
    showSnackbar(`Mitra ${item.is_active ? 'diaktifkan' : 'dinonaktifkan'}`)
  } catch {
    showSnackbar('Gagal mengubah status', 'error')
  }
}

// Delete
const confirmDelete = (item: any) => {
  partnerToDelete.value = item
  deleteDialog.value = true
}

const executeDelete = async () => {
  if (!partnerToDelete.value) return
  deleting.value = true
  try {
    await api.delete(`/admin/partners/${partnerToDelete.value.id}`)
    deleteDialog.value = false
    showSnackbar('Mitra berhasil dihapus')
    await fetchPartners()
  } catch {
    showSnackbar('Gagal menghapus mitra', 'error')
  } finally {
    deleting.value = false
  }
}

// Snackbar helper
const showSnackbar = (text: string, color = 'success') => {
  snackbarText.value = text
  snackbarColor.value = color
  snackbar.value = true
}

onMounted(() => fetchPartners())
</script>

<style scoped>
/* Responsive header */
.admin-page-header .header-content {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  width: 100%;
}
.admin-page-header .header-main {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex: 1;
  min-width: 0;
}
.admin-page-header .header-text { flex: 1; min-width: 0; }
.admin-page-header .header-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
@media (max-width: 600px) {
  .admin-page-header .header-content { flex-direction: column; align-items: stretch; }
  .admin-page-header .header-main { width: 100%; }
  .admin-page-header .header-icon { width: 40px !important; height: 40px !important; }
  .admin-page-header .header-text h1 { font-size: 1.1rem !important; }
  .admin-page-header .header-actions { width: 100%; }
  .admin-page-header .header-actions .v-btn { width: 100%; }
}

/* Compact stat cards */
.stat-card-compact {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  text-align: center;
  gap: 0.5rem;
}
.stat-card-compact .stat-number {
  font-size: 2rem;
  font-weight: 700;
  line-height: 1;
}
.stat-card-compact .stat-label {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
@media (max-width: 600px) {
  .stat-card-compact { padding: 0.75rem 0.5rem; }
  .stat-card-compact .v-avatar { width: 36px !important; height: 36px !important; }
  .stat-card-compact .stat-number { font-size: 1.5rem; }
  .stat-card-compact .stat-label { font-size: 0.65rem; }
}

/* Table header */
:deep(.v-table thead tr th) {
  background-color: #f8fafc;
  color: #475569;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Color swatch in form */
.color-swatch {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: 1px solid rgba(0,0,0,0.12);
  flex-shrink: 0;
}

/* Mobile card border */
.border-t {
  border-top: 1px solid #F1F5F9;
}
</style>
