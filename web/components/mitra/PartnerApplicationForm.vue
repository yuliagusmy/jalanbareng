<template>
  <div class="partner-form-wrapper">
    <div class="form-header text-center mb-6">
      <div class="inline-badge mb-2">
        <v-icon start size="16" color="#DC2626">mdi-store-marker-outline</v-icon>
        <span>FORMULIR KEMITRAAN CHECKPOINT</span>
      </div>
      <h3 class="form-title">Daftarkan Tempat Anda sebagai Titik Temu Pejalan</h3>
      <p class="form-subtitle">
        Buka pintu ruang Anda bagi ribuan pejalan kaki urban. Jadikan tempat Anda sebagai checkpoint istirahat, titik kumpul komunitas, atau mitra aktivasi kota.
      </p>
    </div>

    <v-card class="form-card pa-6 pa-md-8" elevation="0">
      <v-form ref="formRef" v-model="isFormValid" @submit.prevent="handleSubmit">
        <v-row dense>
          <!-- Nama Tempat / Usaha -->
          <v-col cols="12" md="6" class="pb-2">
            <label class="input-label">Nama Tempat / Brand / Ruang <span class="text-error">*</span></label>
            <v-text-field
              v-model="form.name"
              placeholder="Contoh: Kopi Teori / Rumah Budaya Losari"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            />
          </v-col>

          <!-- Kategori Ruang -->
          <v-col cols="12" md="6" class="pb-2">
            <label class="input-label">Kategori Ruang / Usaha <span class="text-error">*</span></label>
            <v-select
              v-model="form.category"
              :items="categories"
              placeholder="Pilih kategori ruang"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            />
          </v-col>

          <!-- Nama Penanggung Jawab -->
          <v-col cols="12" md="6" class="pb-2">
            <label class="input-label">Nama Penanggung Jawab (PIC) <span class="text-error">*</span></label>
            <v-text-field
              v-model="form.pic"
              placeholder="Contoh: Andi Pratama"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            />
          </v-col>

          <!-- Nomor WhatsApp -->
          <v-col cols="12" md="6" class="pb-2">
            <label class="input-label">Nomor WhatsApp Aktif <span class="text-error">*</span></label>
            <v-text-field
              v-model="form.phone"
              placeholder="Contoh: 081234567890"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required, rules.phone]"
              hide-details="auto"
            />
          </v-col>

          <!-- Alamat Lengkap -->
          <v-col cols="12" class="pb-2">
            <label class="input-label">Alamat Lengkap &amp; Patokan Lokasi <span class="text-error">*</span></label>
            <v-textarea
              v-model="form.address"
              rows="2"
              placeholder="Contoh: Jl. Somba Opu No. 45, dekat Pantai Losari, Makassar"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            />
          </v-col>

          <!-- Fasilitas Ramah Pejalan -->
          <v-col cols="12" class="pb-3">
            <label class="input-label mb-2 d-block">
              Fasilitas yang Dapat Disediakan bagi Pejalan Kaki
              <span class="label-hint">(Pilih yang relevan)</span>
            </label>
            <div class="facility-chips">
              <v-chip
                v-for="fac in availableFacilities"
                :key="fac.id"
                filter
                variant="outlined"
                rounded="pill"
                :class="['fac-chip', { 'fac-chip-active': form.facilities.includes(fac.label) }]"
                @click="toggleFacility(fac.label)"
              >
                <v-icon start size="16" :color="form.facilities.includes(fac.label) ? '#DC2626' : undefined">
                  {{ fac.icon }}
                </v-icon>
                {{ fac.label }}
              </v-chip>
            </div>
          </v-col>

          <!-- Catatan / Ide Tambahan -->
          <v-col cols="12" class="pb-4">
            <label class="input-label">Ide atau Harapan Kolaborasi</label>
            <v-textarea
              v-model="form.notes"
              rows="2"
              placeholder="Ceritakan rencana kolaborasi, tawaran diskon bagi pejalan, atau ketersediaan ruang untuk titik kumpul..."
              variant="outlined"
              density="comfortable"
              rounded="lg"
              hide-details="auto"
            />
          </v-col>
        </v-row>

        <div class="form-actions mt-4 text-center">
          <v-btn
            type="submit"
            color="#DC2626"
            size="large"
            rounded="pill"
            class="submit-btn px-8"
            elevation="0"
            :loading="isSubmitting"
          >
            <v-icon start size="20">mdi-send-check-outline</v-icon>
            Ajukan Checkpoint Kemitraan
          </v-btn>
          <div class="secure-note mt-3">
            <v-icon size="14" color="grey">mdi-shield-check-outline</v-icon>
            <span>Data Anda akan langsung diteruskan ke tim kemitraan Jalan Bareng untuk konfirmasi lebih lanjut.</span>
          </div>
        </div>
      </v-form>
    </v-card>

    <!-- Dialog Konfirmasi Sukses -->
    <v-dialog v-model="showSuccessDialog" max-width="500" rounded="xl">
      <v-card class="pa-6 text-center" rounded="24">
        <div class="success-icon-badge mx-auto mb-4">
          <v-icon size="36" color="#16A34A">mdi-check-circle-outline</v-icon>
        </div>
        <h4 class="text-h6 font-weight-black text-grey-darken-4 mb-2">Formulir Siap Dikirim!</h4>
        <p class="text-body-2 text-grey-darken-1 mb-5">
          Terima kasih <strong>{{ form.name }}</strong>! Anda dapat langsung membuka WhatsApp untuk mengirim ringkasan formulir ini ke Tim Kemitraan Jalan Bareng.
        </p>

        <div class="preview-box pa-4 mb-5 text-left">
          <div class="preview-row mb-1">
            <span class="preview-label">Tempat:</span>
            <span class="preview-val font-weight-bold">{{ form.name }} ({{ form.category }})</span>
          </div>
          <div class="preview-row mb-1">
            <span class="preview-label">PIC:</span>
            <span class="preview-val">{{ form.pic }} ({{ form.phone }})</span>
          </div>
          <div v-if="form.facilities.length > 0" class="preview-row mb-1">
            <span class="preview-label">Fasilitas:</span>
            <span class="preview-val text-primary-red">{{ form.facilities.join(', ') }}</span>
          </div>
        </div>

        <div class="d-flex flex-column ga-2">
          <v-btn
            color="#25D366"
            size="large"
            rounded="pill"
            variant="flat"
            class="text-white font-weight-bold"
            @click="openWhatsApp"
          >
            <v-icon start size="20">mdi-whatsapp</v-icon>
            Kirim via WhatsApp Sekarang
          </v-btn>
          <v-btn
            variant="text"
            rounded="pill"
            class="text-grey-darken-1 font-weight-medium"
            @click="showSuccessDialog = false"
          >
            Tutup Dialog
          </v-btn>
        </div>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'

const formRef = ref()
const isFormValid = ref(false)
const isSubmitting = ref(false)
const showSuccessDialog = ref(false)

const categories = [
  'Kafe, Kedai Kopi & F&B',
  'Galeri Seni & Ruang Kreatif',
  'Penginapan, Hostel & Guesthouse',
  'Toko, Kriya & Oleh-Oleh Lokal',
  'Perpustakaan & Ruang Baca Komunal',
  'Komunitas & Kolektif Urban',
  'Lainnya',
]

const availableFacilities = [
  { id: 'water', label: 'Air Minum Gratis / Refill', icon: 'mdi-water-outline' },
  { id: 'power', label: 'Stopkontak / Charging Corner', icon: 'mdi-power-plug-outline' },
  { id: 'toilet', label: 'Akses Toilet Pejalan', icon: 'mdi-toilet' },
  { id: 'discount', label: 'Diskon Pejalan Kaki (Kawan JB)', icon: 'mdi-tag-percent-outline' },
  { id: 'bike', label: 'Parkir Sepeda Aman', icon: 'mdi-bicycle' },
  { id: 'stamp', label: 'Cap / Stempel Paspor Jelajah', icon: 'mdi-stamper' },
]

const form = reactive({
  name: '',
  category: '',
  pic: '',
  phone: '',
  address: '',
  facilities: [] as string[],
  notes: '',
})

const rules = {
  required: (v: string) => !!v || 'Field ini wajib diisi',
  phone: (v: string) => /^[0-9+-\s]{8,18}$/.test(v) || 'Format nomor WhatsApp tidak valid',
}

function toggleFacility(label: string) {
  const index = form.facilities.indexOf(label)
  if (index >= 0) {
    form.facilities.splice(index, 1)
  } else {
    form.facilities.push(label)
  }
}

async function handleSubmit() {
  if (!formRef.value) return
  const { valid } = await formRef.value.validate()
  if (!valid) return

  isSubmitting.value = true
  setTimeout(() => {
    isSubmitting.value = false
    showSuccessDialog.value = true
  }, 400)
}

function openWhatsApp() {
  const facilitiesText = form.facilities.length > 0 ? form.facilities.join(', ') : 'Disesuaikan'
  const message = [
    '*PENDAFTARAN CHECKPOINT KEMITRAAN — JALAN BARENG*',
    '------------------------------------------------',
    `*Nama Usaha/Ruang:* ${form.name}`,
    `*Kategori:* ${form.category}`,
    `*Nama PIC:* ${form.pic}`,
    `*No. WhatsApp:* ${form.phone}`,
    `*Alamat:* ${form.address}`,
    `*Fasilitas Tersedia:* ${facilitiesText}`,
    form.notes ? `*Catatan/Ide:* ${form.notes}` : '',
    '------------------------------------------------',
    'Halo Tim Kemitraan Jalan Bareng, kami mengajukan ruang kami sebagai mitra titik checkpoint bagi pejalan kaki.',
  ].filter(Boolean).join('\n')

  const encoded = encodeURIComponent(message)
  window.open(`https://wa.me/6281234567890?text=${encoded}`, '_blank')
  showSuccessDialog.value = false
}
</script>

<style scoped>
.partner-form-wrapper {
  margin-top: 2rem;
}

.inline-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.35rem 0.85rem;
  background-color: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #DC2626;
  letter-spacing: 0.05em;
}

.form-title {
  font-size: 1.5rem;
  font-weight: 900;
  color: #111827;
  letter-spacing: -0.02em;
}

.form-subtitle {
  font-size: 0.95rem;
  color: #4B5563;
  max-width: 640px;
  margin: 0.5rem auto 0;
  line-height: 1.55;
}

.form-card {
  background: #FFFFFF;
  border-radius: 24px !important;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
}

.input-label {
  display: block;
  font-size: 0.85rem;
  font-weight: 700;
  color: #1F2937;
  margin-bottom: 0.35rem;
}

.label-hint {
  font-weight: 400;
  color: #6B7280;
  font-size: 0.75rem;
}

.text-error {
  color: #DC2626;
}

.facility-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.fac-chip {
  cursor: pointer;
  transition: all 0.2s ease;
  border-color: rgba(0, 0, 0, 0.12) !important;
}

.fac-chip-active {
  background-color: #FEF2F2 !important;
  border-color: #DC2626 !important;
  color: #DC2626 !important;
  font-weight: 600;
}

.submit-btn {
  font-weight: 700;
  letter-spacing: 0.02em;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(220, 38, 38, 0.25);
}

.secure-note {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.35rem;
  font-size: 0.75rem;
  color: #6B7280;
}

.success-icon-badge {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background-color: #DCFCE7;
  display: flex;
  align-items: center;
  justify-content: center;
}

.preview-box {
  background-color: #F8FAFC;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 16px;
  font-size: 0.85rem;
}

.preview-row {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.preview-label {
  color: #64748B;
  flex-shrink: 0;
}

.preview-val {
  color: #0F172A;
  text-align: right;
  word-break: break-word;
}
</style>
