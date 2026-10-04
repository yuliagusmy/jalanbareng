<template>
  <div class="contributor-points-wallet">
    <!-- Wallet Overview Card -->
    <v-card elevation="0" rounded="xl" class="wallet-hero-card pa-6 pa-md-8 mb-6 border">
      <div class="d-flex flex-column flex-md-row align-start align-md-center justify-space-between ga-6">
        <div>
          <div class="d-inline-flex align-center wallet-badge mb-2">
            <v-icon size="14" color="#DC2626" class="mr-1.5">mdi-shield-check</v-icon>
            <span>KONTRIBUTOR JALAN BARENG</span>
          </div>
          <div class="text-caption text-grey-darken-1 font-weight-medium mb-1">Saldo Poin Aktif Anda</div>
          <div class="d-flex align-baseline ga-2 mb-1">
            <h2 class="text-h3 text-md-h2 font-weight-black text-grey-darken-4 mb-0">
              {{ walletData?.balance ?? 0 }}
            </h2>
            <span class="text-h6 font-weight-bold text-primary-red">Poin</span>
          </div>
          <p class="text-body-2 text-grey-darken-2 mb-0">
            Estimasi Nilai Tunai:
            <strong class="text-grey-darken-4 font-weight-bold">
              Rp {{ formatRupiah((walletData?.balance ?? 0) * (walletData?.rupiah_per_point ?? 100)) }}
            </strong>
            <span class="text-caption text-grey ml-1">(1 Poin = Rp 100)</span>
          </p>
        </div>

        <div class="d-flex flex-column align-start align-md-end ga-3 w-100 w-md-auto">
          <v-btn
            color="#DC2626"
            size="large"
            rounded="pill"
            elevation="0"
            class="font-weight-bold px-8 text-white w-100 w-md-auto"
            :disabled="!walletData || walletData.balance < (walletData.min_cashout_points || 500)"
            @click="openCashoutModal"
          >
            <v-icon start size="20">mdi-cash-multiple</v-icon>
            Tarik Poin ke Rupiah
          </v-btn>
          <div class="text-caption text-grey-darken-1 text-md-right">
            Minimal penarikan {{ walletData?.min_cashout_points || 500 }} Poin (Rp {{ formatRupiah((walletData?.min_cashout_points || 500) * 100) }})
          </div>
        </div>
      </div>

      <!-- Quick Metrics Strip -->
      <v-divider class="my-5"></v-divider>
      <v-row dense>
        <v-col cols="6" sm="4">
          <div class="metric-item">
            <span class="metric-label">TOTAL DIPEROLEH</span>
            <span class="metric-val text-emerald-700">+{{ walletData?.total_earned ?? 0 }} Poin</span>
          </div>
        </v-col>
        <v-col cols="6" sm="4">
          <div class="metric-item">
            <span class="metric-label">TOTAL SUDAH DICAIRKAN</span>
            <span class="metric-val text-grey-darken-3">{{ walletData?.total_withdrawn ?? 0 }} Poin</span>
          </div>
        </v-col>
        <v-col cols="12" sm="4" class="mt-2 mt-sm-0">
          <div class="metric-item">
            <span class="metric-label">LEVEL KONTRIBUTOR</span>
            <span class="metric-val text-primary-red font-weight-black">
              {{ getTierLabel(walletData?.total_earned ?? 0) }}
            </span>
          </div>
        </v-col>
      </v-row>
    </v-card>

    <!-- Ways to Earn Points Banner -->
    <v-card elevation="0" rounded="xl" class="pa-5 mb-6 border bg-stone-50">
      <div class="d-flex align-center justify-space-between flex-wrap ga-3 mb-3">
        <h3 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-0 d-flex align-center">
          <v-icon color="#DC2626" class="mr-2" size="20">mdi-lightbulb-on-outline</v-icon>
          Cara Mendapatkan Poin Tambahan
        </h3>
      </div>
      <v-row dense>
        <v-col cols="12" sm="6" md="3">
          <div class="earn-action-card pa-3 rounded-lg border bg-white h-100">
            <div class="d-flex align-center justify-space-between mb-1">
              <span class="earn-points-badge">+100 Poin</span>
              <v-icon size="18" color="#DC2626">mdi-feather</v-icon>
            </div>
            <div class="text-body-2 font-weight-bold text-grey-darken-4">Tulis Cerita Perjalanan</div>
            <p class="text-caption text-grey-darken-1 mb-0">Bagikan kisah sudut kota. Poin cair setelah disetujui kurator.</p>
          </div>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <div class="earn-action-card pa-3 rounded-lg border bg-white h-100">
            <div class="d-flex align-center justify-space-between mb-1">
              <span class="earn-points-badge">+75 Poin</span>
              <v-icon size="18" color="#D97706">mdi-map-marker-plus</v-icon>
            </div>
            <div class="text-body-2 font-weight-bold text-grey-darken-4">Tambah Landmark Baru</div>
            <p class="text-caption text-grey-darken-1 mb-0">Tandai titik kumpul atau cagar budaya ramah pejalan.</p>
          </div>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <div class="earn-action-card pa-3 rounded-lg border bg-white h-100">
            <div class="d-flex align-center justify-space-between mb-1">
              <span class="earn-points-badge">+50 Poin</span>
              <v-icon size="18" color="#16A34A">mdi-calendar-check</v-icon>
            </div>
            <div class="text-body-2 font-weight-bold text-grey-darken-4">Hadir di Walk Event</div>
            <p class="text-caption text-grey-darken-1 mb-0">Scan QR check-in saat ikut jalan bareng komunitas mingguan.</p>
          </div>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <div class="earn-action-card pa-3 rounded-lg border bg-white h-100">
            <div class="d-flex align-center justify-space-between mb-1">
              <span class="earn-points-badge">+25 Poin</span>
              <v-icon size="18" color="#2563EB">mdi-tooltip-check-outline</v-icon>
            </div>
            <div class="text-body-2 font-weight-bold text-grey-darken-4">Lengkapi Data Fasilitas</div>
            <p class="text-caption text-grey-darken-1 mb-0">Perbarui info trotoar, akses difabel, atau kran air minum.</p>
          </div>
        </v-col>
      </v-row>
    </v-card>

    <!-- Sub-Tabs: Riwayat Mutasi Poin & Riwayat Penarikan -->
    <v-card elevation="0" rounded="xl" class="border overflow-hidden">
      <div class="px-5 pt-4 border-b">
        <div class="d-flex align-center ga-3">
          <button
            type="button"
            :class="['subtab-btn', { active: activeSubTab === 'transactions' }]"
            @click="activeSubTab = 'transactions'"
          >
            <v-icon start size="16">mdi-history</v-icon>
            Riwayat Mutasi Poin
          </button>
          <button
            type="button"
            :class="['subtab-btn', { active: activeSubTab === 'cashouts' }]"
            @click="activeSubTab = 'cashouts'"
          >
            <v-icon start size="16">mdi-cash-fast</v-icon>
            Permohonan Pencairan Dana
            <v-badge
              v-if="pendingCashoutsCount > 0"
              color="#DC2626"
              :content="pendingCashoutsCount"
              inline
            ></v-badge>
          </button>
        </div>
      </div>

      <!-- Tab Content 1: Riwayat Transaksi -->
      <div v-if="activeSubTab === 'transactions'" class="pa-4 pa-md-6">
        <div v-if="loading" class="text-center py-8">
          <v-progress-circular indeterminate color="#DC2626"></v-progress-circular>
        </div>
        <div v-else-if="!walletData?.transactions?.data?.length" class="text-center py-10">
          <v-icon size="48" color="grey-lighten-1">mdi-clipboard-text-off-outline</v-icon>
          <div class="text-body-1 font-weight-bold text-grey-darken-3 mt-2">Belum ada mutasi poin</div>
          <p class="text-caption text-grey-darken-1 mb-0">Mulai berkontribusi cerita atau landmark untuk mendapatkan poin pertama Anda!</p>
        </div>
        <div v-else class="transaction-list">
          <div
            v-for="tx in walletData.transactions.data"
            :key="tx.id"
            class="tx-item d-flex align-center justify-space-between py-3 border-b-subtle"
          >
            <div class="d-flex align-center ga-3">
              <v-avatar
                :color="tx.type === 'credit' ? '#DCFCE7' : '#FEE2E2'"
                size="40"
                rounded="circle"
              >
                <v-icon
                  size="20"
                  :color="tx.type === 'credit' ? '#16A34A' : '#DC2626'"
                >
                  {{ tx.type === 'credit' ? 'mdi-arrow-down-left' : 'mdi-arrow-up-right' }}
                </v-icon>
              </v-avatar>
              <div>
                <div class="text-body-2 font-weight-bold text-grey-darken-4">{{ tx.description }}</div>
                <div class="text-caption text-grey">{{ formatDate(tx.created_at) }}</div>
              </div>
            </div>
            <div
              :class="['text-subtitle-2 font-weight-black', tx.type === 'credit' ? 'text-emerald-700' : 'text-primary-red']"
            >
              {{ tx.type === 'credit' ? '+' : '-' }}{{ tx.amount }} Pts
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Content 2: Riwayat Penarikan Dana (Cashouts) -->
      <div v-else class="pa-4 pa-md-6">
        <div v-if="!walletData?.recent_cashouts?.length" class="text-center py-10">
          <v-icon size="48" color="grey-lighten-1">mdi-cash-remove</v-icon>
          <div class="text-body-1 font-weight-bold text-grey-darken-3 mt-2">Belum ada pengajuan pencairan</div>
          <p class="text-caption text-grey-darken-1 mb-0">Jika saldo poin Anda sudah mencapai 500 Poin, Anda bisa menariknya ke rekening/e-wallet.</p>
        </div>
        <div v-else class="cashout-list">
          <div
            v-for="c in walletData.recent_cashouts"
            :key="c.id"
            class="cashout-item pa-4 mb-3 border rounded-xl bg-stone-50"
          >
            <div class="d-flex align-center justify-space-between flex-wrap ga-2 mb-2">
              <div class="d-flex align-center ga-2">
                <span class="text-subtitle-2 font-weight-black text-grey-darken-4">
                  Rp {{ formatRupiah(c.rupiah_amount) }}
                </span>
                <span class="text-caption text-grey">({{ c.points_requested }} Poin)</span>
              </div>
              <v-chip
                size="small"
                :color="getCashoutStatusColor(c.status)"
                class="font-weight-bold text-capitalize"
                variant="flat"
              >
                {{ getCashoutStatusLabel(c.status) }}
              </v-chip>
            </div>
            <div class="text-caption text-grey-darken-2 d-flex flex-wrap ga-4">
              <span><strong>Metode:</strong> {{ formatPaymentMethod(c.payment_method) }}</span>
              <span><strong>Rekening/HP:</strong> {{ c.account_number }} (a.n {{ c.account_name }})</span>
              <span><strong>Diajukan:</strong> {{ formatDate(c.created_at) }}</span>
            </div>
            <div v-if="c.admin_notes" class="mt-2 text-caption text-amber-900 bg-amber-50 pa-2 rounded-lg border">
              <strong>Catatan Admin:</strong> {{ c.admin_notes }}
            </div>
            <div v-if="c.receipt_image_url" class="mt-2">
              <v-btn
                size="x-small"
                variant="outlined"
                color="primary"
                rounded="pill"
                :href="c.receipt_image_url"
                target="_blank"
              >
                <v-icon start size="14">mdi-receipt</v-icon>
                Lihat Bukti Transfer Admin
              </v-btn>
            </div>
          </div>
        </div>
      </div>
    </v-card>

    <!-- Cashout Request Modal -->
    <v-dialog v-model="cashoutDialog" max-width="500" persistent>
      <v-card rounded="xl" class="pa-4 pa-sm-6">
        <div class="d-flex align-center justify-space-between mb-4">
          <div class="d-flex align-center">
            <v-avatar color="#FEE2E2" size="40" class="mr-3">
              <v-icon color="#DC2626">mdi-cash-multiple</v-icon>
            </v-avatar>
            <div>
              <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-0">Tarik Poin ke Rupiah</h3>
              <p class="text-caption text-grey mb-0">Saldo Tersedia: {{ walletData?.balance || 0 }} Pts</p>
            </div>
          </div>
          <v-btn icon="mdi-close" variant="text" size="small" @click="cashoutDialog = false"></v-btn>
        </div>

        <v-form @submit.prevent="submitCashout">
          <!-- Points Amount Input -->
          <div class="mb-3">
            <v-text-field
              v-model.number="form.points_requested"
              label="Jumlah Poin yang Ditarik"
              type="number"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              :min="walletData?.min_cashout_points || 500"
              :max="walletData?.balance || 0"
              prepend-inner-icon="mdi-hand-coin"
              hint="Minimal 500 Poin (1 Poin = Rp 100)"
              persistent-hint
              required
            ></v-text-field>
          </div>

          <!-- Conversion Preview Box -->
          <div class="pa-3 mb-4 rounded-lg bg-red-lighten-5 border border-red-lighten-4 d-flex align-center justify-space-between">
            <span class="text-caption text-grey-darken-3 font-weight-medium">Nominal yang akan Anda terima:</span>
            <span class="text-subtitle-1 font-weight-black text-primary-red">
              Rp {{ formatRupiah((form.points_requested || 0) * 100) }}
            </span>
          </div>

          <!-- Payment Method Select -->
          <div class="mb-3">
            <v-select
              v-model="form.payment_method"
              label="Tujuan Rekening / E-Wallet"
              :items="paymentOptions"
              item-title="title"
              item-value="value"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              prepend-inner-icon="mdi-bank"
              required
            ></v-select>
          </div>

          <!-- Account Number / Phone -->
          <div class="mb-3">
            <v-text-field
              v-model="form.account_number"
              label="Nomor Rekening / Nomor HP E-Wallet"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              placeholder="Contoh: 1234567890 / 081234567890"
              required
            ></v-text-field>
          </div>

          <!-- Account Holder Name -->
          <div class="mb-4">
            <v-text-field
              v-model="form.account_name"
              label="Nama Pemilik Rekening / Akun E-Wallet"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              placeholder="Harus sesuai buku tabungan / akun e-wallet"
              required
            ></v-text-field>
          </div>

          <v-alert
            v-if="modalError"
            type="error"
            variant="tonal"
            density="compact"
            class="mb-4"
          >
            {{ modalError }}
          </v-alert>

          <div class="d-flex ga-3 justify-end pt-2">
            <v-btn variant="text" rounded="pill" @click="cashoutDialog = false">
              Batal
            </v-btn>
            <v-btn
              color="#DC2626"
              rounded="pill"
              class="text-white px-6 font-weight-bold"
              type="submit"
              :loading="submitting"
              :disabled="!isFormValid"
            >
              Kirim Permohonan
            </v-btn>
          </div>
        </v-form>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePoints } from '~/composables/usePoints'

const { loading, walletData, fetchMyPoints, requestCashout } = usePoints()

const activeSubTab = ref<'transactions' | 'cashouts'>('transactions')
const cashoutDialog = ref(false)
const submitting = ref(false)
const modalError = ref<string | null>(null)

const form = ref({
  points_requested: 500,
  payment_method: 'bank_bca',
  account_number: '',
  account_name: '',
})

const paymentOptions = [
  { title: 'Bank BCA', value: 'bank_bca' },
  { title: 'Bank Mandiri', value: 'bank_mandiri' },
  { title: 'Bank BRI', value: 'bank_bri' },
  { title: 'Bank BNI', value: 'bank_bni' },
  { title: 'GoPay', value: 'gopay' },
  { title: 'OVO', value: 'ovo' },
  { title: 'DANA', value: 'dana' },
]

const pendingCashoutsCount = computed(() => {
  if (!walletData.value?.recent_cashouts) return 0
  return walletData.value.recent_cashouts.filter(c => c.status === 'pending').length
})

const isFormValid = computed(() => {
  const pts = form.value.points_requested
  const min = walletData.value?.min_cashout_points || 500
  const max = walletData.value?.balance || 0
  return pts >= min && pts <= max && !!form.value.account_number && !!form.value.account_name
})

const formatRupiah = (val: number) => {
  return new Intl.NumberFormat('id-ID').format(val || 0)
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getTierLabel = (totalEarned: number) => {
  if (totalEarned >= 2000) return '👑 Duta Pejalan (Level 4)'
  if (totalEarned >= 751) return '🧭 Pemandu Kota (Level 3)'
  if (totalEarned >= 251) return '🗺️ Penjelajah Lorong (Level 2)'
  return '🚶 Pejalan Pemula (Level 1)'
}

const getCashoutStatusColor = (status: string) => {
  if (status === 'approved') return '#16A34A'
  if (status === 'rejected') return '#DC2626'
  return '#D97706'
}

const getCashoutStatusLabel = (status: string) => {
  if (status === 'approved') return 'Berhasil Ditransfer'
  if (status === 'rejected') return 'Ditolak'
  return 'Menunggu Verifikasi Admin'
}

const formatPaymentMethod = (method: string) => {
  const found = paymentOptions.find(p => p.value === method)
  return found ? found.title : method
}

const openCashoutModal = () => {
  modalError.value = null
  const min = walletData.value?.min_cashout_points || 500
  form.value.points_requested = Math.max(min, walletData.value?.balance || min)
  cashoutDialog.value = true
}

const submitCashout = async () => {
  submitting.value = true
  modalError.value = null
  const res = await requestCashout(form.value)
  submitting.value = false
  if (res.success) {
    cashoutDialog.value = false
    activeSubTab.value = 'cashouts'
  } else {
    modalError.value = res.message
  }
}

onMounted(() => {
  fetchMyPoints()
})
</script>

<style scoped>
.wallet-hero-card {
  background: linear-gradient(135deg, #FFFFFF 0%, #FFF5F5 100%);
}

.wallet-badge {
  padding: 4px 12px;
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 9999px;
  color: #DC2626;
  font-size: 0.74rem;
  font-weight: 800;
  letter-spacing: 0.04em;
}

.text-primary-red {
  color: #DC2626;
}

.metric-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: #64748B;
  display: block;
  letter-spacing: 0.03em;
}

.metric-val {
  font-size: 1.05rem;
  font-weight: 800;
  display: block;
}

.earn-points-badge {
  background: #FEF2F2;
  color: #DC2626;
  font-weight: 800;
  font-size: 0.72rem;
  padding: 2px 8px;
  border-radius: 9999px;
  border: 1px solid #FECACA;
}

.subtab-btn {
  display: inline-flex;
  align-items: center;
  padding: 10px 16px;
  font-size: 0.88rem;
  font-weight: 700;
  color: #64748B;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.subtab-btn:hover {
  color: #111827;
}

.subtab-btn.active {
  color: #DC2626;
  border-bottom-color: #DC2626;
}

.border-b-subtle {
  border-bottom: 1px solid #F1F5F9;
}

.border-b-subtle:last-child {
  border-bottom: none;
}

.bg-stone-50 {
  background-color: #FAFAF9;
}
</style>
