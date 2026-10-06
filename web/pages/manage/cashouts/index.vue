<template>
  <div class="admin-cashouts-page">
    <v-container class="py-8">
      <!-- Header Card -->
      <v-card elevation="0" rounded="xl" class="mb-6 border">
        <v-card-title class="d-flex flex-wrap align-center justify-space-between px-6 py-4 ga-3">
          <div class="d-flex align-center">
            <v-avatar color="#FEE2E2" size="48" class="mr-4">
              <v-icon size="28" color="#DC2626">mdi-hand-coin</v-icon>
            </v-avatar>
            <div>
              <h1 class="text-h5 font-weight-bold text-grey-darken-4 mb-0">Kelola Pencairan Poin</h1>
              <p class="text-caption text-grey-darken-1 mb-0">Verifikasi dan proses penarikan uang kontributor Jalan Bareng</p>
            </div>
          </div>

          <div class="d-flex align-center ga-2">
            <v-btn
              variant="outlined"
              color="#DC2626"
              rounded="pill"
              prepend-icon="mdi-refresh"
              :loading="loading"
              @click="fetchCashouts"
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
                <div class="text-caption font-weight-bold text-blue-darken-3">TOTAL PENGAJUAN</div>
                <div class="text-h4 font-weight-bold text-blue-darken-4">{{ counts.all }}</div>
              </div>
              <v-avatar color="blue" size="44">
                <v-icon color="white">mdi-format-list-bulleted</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="0" rounded="xl" class="pa-4 border bg-amber-lighten-5">
            <div class="d-flex align-center justify-space-between">
              <div>
                <div class="text-caption font-weight-bold text-amber-darken-3">BUTUH TINDAKAN</div>
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
                <div class="text-caption font-weight-bold text-green-darken-3">BERHASIL DITRANSFER</div>
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
                <div class="text-caption font-weight-bold text-red-darken-3">DITOLAK / REFUND</div>
                <div class="text-h4 font-weight-bold text-red-darken-4">{{ counts.rejected }}</div>
              </div>
              <v-avatar color="red-darken-1" size="44">
                <v-icon color="white">mdi-close-circle-outline</v-icon>
              </v-avatar>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter Tabs & Main Table -->
      <v-card elevation="0" rounded="xl" class="border">
        <div class="px-6 pt-4 border-b">
          <v-tabs v-model="statusFilter" color="#DC2626" @update:model-value="fetchCashouts">
            <v-tab value="all">Semua ({{ counts.all }})</v-tab>
            <v-tab value="pending">
              Menunggu ({{ counts.pending }})
              <v-badge v-if="counts.pending > 0" color="#DC2626" content="!" inline class="ml-1"></v-badge>
            </v-tab>
            <v-tab value="approved">Ditransfer ({{ counts.approved }})</v-tab>
            <v-tab value="rejected">Ditolak ({{ counts.rejected }})</v-tab>
          </v-tabs>
        </div>

        <!-- Table -->
        <v-table hover>
          <thead>
            <tr>
              <th class="font-weight-bold">Pengguna</th>
              <th class="font-weight-bold">Jumlah Poin</th>
              <th class="font-weight-bold">Nominal Transfer</th>
              <th class="font-weight-bold">Tujuan &amp; Atas Nama</th>
              <th class="font-weight-bold">Tanggal</th>
              <th class="font-weight-bold">Status</th>
              <th class="font-weight-bold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-8">
                <v-progress-circular indeterminate color="#DC2626"></v-progress-circular>
              </td>
            </tr>
            <tr v-else-if="!cashouts.length">
              <td colspan="7" class="text-center py-8 text-grey">
                Tidak ada data pengajuan pencairan
              </td>
            </tr>
            <tr v-for="item in cashouts" :key="item.id">
              <td>
                <div class="d-flex align-center ga-3 py-2">
                  <v-avatar size="36" color="grey-lighten-3">
                    <v-img v-if="item.user?.photo" :src="item.user.photo"></v-img>
                    <v-icon v-else size="20">mdi-account</v-icon>
                  </v-avatar>
                  <div>
                    <div class="font-weight-bold text-grey-darken-4">{{ item.user?.name || 'Anonim' }}</div>
                    <div class="text-caption text-grey">{{ item.user?.email }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="font-weight-bold text-primary-red">{{ item.points_requested }} Pts</span>
              </td>
              <td>
                <span class="font-weight-bold text-grey-darken-4">Rp {{ formatRupiah(item.rupiah_amount) }}</span>
              </td>
              <td>
                <div class="font-weight-medium text-capitalize">{{ formatPaymentMethod(item.payment_method) }}</div>
                <div class="text-caption text-grey-darken-1">{{ item.account_number }} (a.n {{ item.account_name }})</div>
              </td>
              <td class="text-caption text-grey">
                {{ formatDate(item.created_at) }}
              </td>
              <td>
                <v-chip
                  size="small"
                  :color="getStatusColor(item.status)"
                  class="font-weight-bold text-capitalize"
                >
                  {{ getStatusLabel(item.status) }}
                </v-chip>
              </td>
              <td class="text-right">
                <v-btn
                  v-if="item.status === 'pending'"
                  color="#DC2626"
                  size="small"
                  rounded="pill"
                  class="text-white font-weight-bold"
                  @click="openProcessModal(item)"
                >
                  Proses Transfer
                </v-btn>
                <v-btn
                  v-else-if="item.receipt_image_url"
                  variant="outlined"
                  size="small"
                  rounded="pill"
                  color="primary"
                  :href="item.receipt_image_url"
                  target="_blank"
                >
                  Lihat Struk
                </v-btn>
                <span v-else class="text-caption text-grey">Selesai</span>
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>
    </v-container>

    <!-- Modal Review & Process Cashout -->
    <v-dialog v-model="processDialog" max-width="520" persistent>
      <v-card rounded="xl" class="pa-4 pa-sm-6" v-if="selectedCashout">
        <div class="d-flex align-center justify-space-between mb-4">
          <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-0">Verifikasi Pengajuan Penarikan</h3>
          <v-btn icon="mdi-close" variant="text" size="small" @click="processDialog = false"></v-btn>
        </div>

        <div class="pa-4 mb-4 rounded-xl bg-grey-lighten-4 border">
          <div class="d-flex justify-space-between mb-1">
            <span class="text-caption text-grey-darken-1">Nama Kontributor:</span>
            <span class="font-weight-bold">{{ selectedCashout.user?.name }}</span>
          </div>
          <div class="d-flex justify-space-between mb-1">
            <span class="text-caption text-grey-darken-1">Jumlah Nominal:</span>
            <span class="font-weight-black text-primary-red text-subtitle-1">
              Rp {{ formatRupiah(selectedCashout.rupiah_amount) }}
            </span>
          </div>
          <div class="d-flex justify-space-between mb-1">
            <span class="text-caption text-grey-darken-1">Metode &amp; No. Rekening:</span>
            <span class="font-weight-bold text-right">
              {{ formatPaymentMethod(selectedCashout.payment_method) }} - {{ selectedCashout.account_number }}
            </span>
          </div>
          <div class="d-flex justify-space-between">
            <span class="text-caption text-grey-darken-1">Atas Nama Rekening:</span>
            <span class="font-weight-bold">{{ selectedCashout.account_name }}</span>
          </div>
        </div>

        <!-- Action Selection -->
        <div class="mb-4">
          <label class="text-subtitle-2 font-weight-bold text-grey-darken-3 d-block mb-2">Keputusan Admin:</label>
          <v-btn-toggle v-model="processForm.status" mandatory color="#DC2626" rounded="lg" class="w-100">
            <v-btn value="approved" class="flex-grow-1">
              <v-icon start color="green">mdi-check-circle</v-icon>
              Setujui &amp; Ditransfer
            </v-btn>
            <v-btn value="rejected" class="flex-grow-1">
              <v-icon start color="red">mdi-close-circle</v-icon>
              Tolak &amp; Refund Saldo
            </v-btn>
          </v-btn-toggle>
        </div>

        <!-- Upload Struk if Approved -->
        <div v-if="processForm.status === 'approved'" class="mb-3">
          <v-file-input
            v-model="processForm.receipt_file"
            label="Upload Bukti Struk Transfer (Opsional)"
            accept="image/*"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            prepend-icon="mdi-camera"
            show-size
          ></v-file-input>
        </div>

        <!-- Notes (Alasan jika ditolak / catatan referensi) -->
        <div class="mb-4">
          <v-textarea
            v-model="processForm.admin_notes"
            :label="processForm.status === 'rejected' ? 'Alasan Penolakan (Wajib Diisi)' : 'Nomor Referensi / Catatan Tambahan (Opsional)'"
            variant="outlined"
            rounded="lg"
            rows="3"
            density="comfortable"
            :required="processForm.status === 'rejected'"
          ></v-textarea>
        </div>

        <div class="d-flex justify-end ga-3">
          <v-btn variant="text" rounded="pill" @click="processDialog = false">Batal</v-btn>
          <v-btn
            color="#DC2626"
            rounded="pill"
            class="text-white px-6 font-weight-bold"
            :loading="processing"
            :disabled="processForm.status === 'rejected' && !processForm.admin_notes"
            @click="submitProcess"
          >
            Konfirmasi Keputusan
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'

definePageMeta({
  layout: 'solid',
  middleware: 'auth'
})

const { api } = useApi()

const loading = ref(false)
const processing = ref(false)
const statusFilter = ref<'all' | 'pending' | 'approved' | 'rejected'>('all')
const cashouts = ref<any[]>([])
const counts = ref({ all: 0, pending: 0, approved: 0, rejected: 0 })

const processDialog = ref(false)
const selectedCashout = ref<any>(null)
const processForm = ref({
  status: 'approved',
  admin_notes: '',
  receipt_file: null as File[] | null,
})

const formatRupiah = (val: number) => new Intl.NumberFormat('id-ID').format(val || 0)

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

const getStatusColor = (status: string) => {
  if (status === 'approved') return 'green'
  if (status === 'rejected') return 'red'
  return 'amber-darken-2'
}

const getStatusLabel = (status: string) => {
  if (status === 'approved') return 'Ditransfer'
  if (status === 'rejected') return 'Ditolak'
  return 'Menunggu'
}

const formatPaymentMethod = (method: string) => {
  const map: Record<string, string> = {
    bank_bca: 'Bank BCA', bank_mandiri: 'Bank Mandiri', bank_bri: 'Bank BRI',
    bank_bni: 'Bank BNI', gopay: 'GoPay', ovo: 'OVO', dana: 'DANA'
  }
  return map[method] || method
}

const fetchCashouts = async () => {
  loading.value = true
  try {
    const params: any = {}
    if (statusFilter.value !== 'all') params.status = statusFilter.value
    const res = await api.get('/admin/cashouts', { params })
    cashouts.value = res.data.data.cashouts.data || []
    counts.value = res.data.data.counts
  } catch (err) {
    console.error('Error fetching cashouts:', err)
  } finally {
    loading.value = false
  }
}

const openProcessModal = (item: any) => {
  selectedCashout.value = item
  processForm.value = {
    status: 'approved',
    admin_notes: '',
    receipt_file: null,
  }
  processDialog.value = true
}

const submitProcess = async () => {
  if (!selectedCashout.value) return
  processing.value = true
  try {
    const fd = new FormData()
    fd.append('status', processForm.value.status)
    if (processForm.value.admin_notes) fd.append('admin_notes', processForm.value.admin_notes)
    if (processForm.value.receipt_file && processForm.value.receipt_file.length > 0) {
      fd.append('receipt_image', processForm.value.receipt_file[0])
    }

    await api.post(`/admin/cashouts/${selectedCashout.value.id}/status`, fd)
    processDialog.value = false
    await fetchCashouts()
  } catch (err) {
    console.error('Error processing cashout:', err)
  } finally {
    processing.value = false
  }
}

onMounted(() => {
  fetchCashouts()
})
</script>

<style scoped>
.text-primary-red {
  color: #DC2626;
}
</style>
