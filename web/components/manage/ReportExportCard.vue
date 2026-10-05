<template>
  <div class="report-export-section">
    <v-row>
      <!-- Export Member Card -->
      <v-col cols="12" md="6">
        <v-card class="export-card pa-6 h-100" rounded="xl" elevation="0">
          <div class="d-flex align-center justify-space-between mb-3">
            <div class="card-icon-box bg-red-subtle">
              <v-icon color="#DC2626" size="24">mdi-account-group-outline</v-icon>
            </div>
            <v-chip size="small" color="#DC2626" variant="tonal" rounded="pill" class="font-weight-bold">
              CSV Format
            </v-chip>
          </div>

          <h3 class="text-h6 font-weight-black text-grey-darken-4 mb-2">
            Ekspor Data Member
          </h3>
          <p class="text-body-2 text-grey-darken-1 mb-5">
            Unduh seluruh direktori anggota terdaftar, role hak akses, kontak telepon, dan tanggal bergabung dalam format CSV terstruktur.
          </p>

          <div class="export-features mb-6">
            <div class="feature-item">
              <v-icon size="16" color="#16A34A">mdi-check-circle-outline</v-icon>
              <span>Termasuk nama, email, nomor HP, & status akun</span>
            </div>
            <div class="feature-item">
              <v-icon size="16" color="#16A34A">mdi-check-circle-outline</v-icon>
              <span>Encoding UTF-8 BOM ramah Microsoft Excel</span>
            </div>
          </div>

          <v-btn
            color="#DC2626"
            rounded="pill"
            block
            size="large"
            class="export-btn font-weight-bold text-white elevation-0"
            :loading="downloadingMember"
            @click="handleExportMembers"
          >
            <v-icon start size="18">mdi-download</v-icon>
            Unduh CSV Member
          </v-btn>
        </v-card>
      </v-col>

      <!-- Export Participants Card -->
      <v-col cols="12" md="6">
        <v-card class="export-card pa-6 h-100" rounded="xl" elevation="0">
          <div class="d-flex align-center justify-space-between mb-3">
            <div class="card-icon-box bg-blue-subtle">
              <v-icon color="#0284C7" size="24">mdi-clipboard-account-outline</v-icon>
            </div>
            <v-chip size="small" color="#0284C7" variant="tonal" rounded="pill" class="font-weight-bold">
              Excel / CSV
            </v-chip>
          </div>

          <h3 class="text-h6 font-weight-black text-grey-darken-4 mb-2">
            Ekspor Pendaftar Aktivasi
          </h3>
          <p class="text-body-2 text-grey-darken-1 mb-3">
            Unduh daftar pejalan yang telah mendaftar ke kegiatan aktivasi kota atau walking tour.
          </p>

          <!-- Filter Event Opsional -->
          <div class="mb-4">
            <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">
              Filter Berdasarkan Event (Opsional):
            </label>
            <v-select
              v-model="selectedEventId"
              :items="eventOptions"
              item-title="title"
              item-value="id"
              placeholder="Pilih Event atau Semua"
              variant="outlined"
              density="compact"
              rounded="lg"
              hide-details
              class="event-selector"
            />
          </div>

          <v-btn
            color="#0284C7"
            rounded="pill"
            block
            size="large"
            class="export-btn font-weight-bold text-white elevation-0"
            :loading="downloadingParticipant"
            @click="handleExportParticipants"
          >
            <v-icon start size="18">mdi-file-excel-outline</v-icon>
            Unduh Daftar Peserta
          </v-btn>
        </v-card>
      </v-col>
    </v-row>

    <!-- Snackbar Notifikasi -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3500" rounded="pill" location="bottom right">
      <div class="d-flex align-center ga-2">
        <v-icon size="18">{{ snackbar.icon }}</v-icon>
        <span>{{ snackbar.text }}</span>
      </div>
    </v-snackbar>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'

interface EventItem {
  id: number
  name: string
  date?: string
}

const { api } = useApi()

const downloadingMember = ref(false)
const downloadingParticipant = ref(false)
const selectedEventId = ref<number | null>(null)
const rawEvents = ref<EventItem[]>([])

const snackbar = ref({
  show: false,
  text: '',
  color: 'success',
  icon: 'mdi-check-circle'
})

const showNotice = (text: string, color = 'success', icon = 'mdi-check-circle') => {
  snackbar.value = { show: true, text, color, icon }
}

// Fetch events list for filter dropdown
const fetchEvents = async () => {
  try {
    const res = await api.get('/events')
    const list = res.data?.events || res.data?.data || []
    rawEvents.value = Array.isArray(list) ? list : []
  } catch (err) {
    console.warn('Gagal memuat list event untuk filter ekspor', err)
  }
}

const eventOptions = computed(() => {
  const options = [{ id: 0, title: 'Semua Event & Aktivasi' }]
  rawEvents.value.forEach(e => {
    options.push({
      id: e.id,
      title: e.name + (e.date ? ` (${new Date(e.date).toLocaleDateString('id-ID')})` : '')
    })
  })
  return options
})

// Trigger browser file download via Blob
const triggerDownload = (data: any, defaultFilename: string) => {
  const blob = new Blob([data], { type: 'text/csv;charset=utf-8;' })
  const url = window.URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.setAttribute('download', defaultFilename)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(url)
}

const handleExportMembers = async () => {
  downloadingMember.value = true
  try {
    const res = await api.get('/admin/reports/members', {
      responseType: 'blob'
    })
    const dateStr = new Date().toISOString().slice(0, 10)
    triggerDownload(res.data, `member_jalanbareng_${dateStr}.csv`)
    showNotice('Laporan CSV Member berhasil diunduh!')
  } catch (err: any) {
    showNotice('Gagal mengunduh data member. Silakan coba lagi.', 'error', 'mdi-alert-circle')
  } finally {
    downloadingMember.value = false
  }
}

const handleExportParticipants = async () => {
  downloadingParticipant.value = true
  try {
    const params: Record<string, any> = {}
    if (selectedEventId.value && selectedEventId.value > 0) {
      params.event_id = selectedEventId.value
    }
    const res = await api.get('/admin/reports/participants', {
      params,
      responseType: 'blob'
    })
    const dateStr = new Date().toISOString().slice(0, 10)
    const suffix = selectedEventId.value ? `event_${selectedEventId.value}` : 'semua_event'
    triggerDownload(res.data, `peserta_aktivasi_${suffix}_${dateStr}.csv`)
    showNotice('Daftar peserta aktivasi berhasil diunduh!')
  } catch (err: any) {
    showNotice('Gagal mengunduh data peserta. Silakan coba lagi.', 'error', 'mdi-alert-circle')
  } finally {
    downloadingParticipant.value = false
  }
}

onMounted(() => {
  fetchEvents()
})
</script>

<style scoped>
.export-card {
  background: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
  display: flex;
  flex-direction: column;
}

.card-icon-box {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.bg-red-subtle {
  background-color: #FEF2F2;
}

.bg-blue-subtle {
  background-color: #F0F9FF;
}

.export-features {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.825rem;
  color: #4B5563;
}

.export-btn {
  letter-spacing: 0.02em;
  margin-top: auto;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.export-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
}
</style>
