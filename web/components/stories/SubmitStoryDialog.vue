<template>
  <v-dialog v-model="dialog" max-width="720" persistent scrollable>
    <v-card rounded="xl" class="story-dialog">
      <!-- Header -->
      <v-card-title class="d-flex align-center justify-space-between pa-5 pa-md-6 border-b">
        <div class="d-flex align-center">
          <v-avatar color="primary" variant="tonal" size="44" class="mr-3">
            <v-icon color="primary" size="24">mdi-feather</v-icon>
          </v-avatar>
          <div>
            <h3 class="text-h6 font-weight-bold text-grey-darken-4 mb-0">Kirim Tulisan Jalan Bareng</h3>
            <span class="text-caption text-grey-darken-1">Bagikan cerita, riset, atau pengalaman jelajahmu</span>
          </div>
        </div>
        <v-btn icon="mdi-close" variant="text" density="comfortable" @click="closeDialog" :disabled="submitting"></v-btn>
      </v-card-title>

      <!-- Form Content -->
      <v-card-text class="pa-5 pa-md-6" style="max-height: 70vh;">
        <!-- Success State Alert -->
        <v-alert
          v-if="submittedSuccess"
          type="success"
          variant="tonal"
          rounded="lg"
          class="mb-6"
          icon="mdi-check-decagram"
        >
          <div class="text-subtitle-1 font-weight-bold">Tulisan Berhasil Dikirim!</div>
          <div class="text-body-2">
            Terima kasih telah berbagi cerita. Tulisan Anda kini masuk ke antrean kurasi admin Jalan Bareng dan akan segera ditinjau sebelum dipublikasikan.
          </div>
          <div class="mt-4">
            <v-btn color="success" variant="flat" rounded="pill" @click="resetAndClose">
              Selesai
            </v-btn>
          </div>
        </v-alert>

        <v-form v-else ref="formRef" @submit.prevent="submitStory">
          <!-- Intro Info Box -->
          <v-alert
            type="info"
            variant="tonal"
            density="compact"
            rounded="lg"
            class="mb-6"
            icon="mdi-information-outline"
          >
            <span class="text-caption">
              Semua tulisan yang masuk akan melewati proses kurasi oleh tim kurator Jalan Bareng demi menjaga kenyamanan dan kualitas bacaan komunitas.
            </span>
          </v-alert>

          <!-- Judul Tulisan -->
          <div class="mb-4">
            <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
              Judul Tulisan <span class="text-error">*</span>
            </label>
            <v-text-field
              v-model="form.title"
              placeholder="Contoh: Menelusuri Sudut Teduh dan Cerita Lorong di Kota Tua"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            ></v-text-field>
          </div>

          <!-- Ringkasan / Excerpt -->
          <div class="mb-4">
            <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
              Ringkasan Cuplikan (Excerpt)
            </label>
            <v-textarea
              v-model="form.excerpt"
              rows="2"
              placeholder="Tuliskan 1-2 kalimat pengantar yang menarik untuk ditampilkan pada kartu pratinjau..."
              variant="outlined"
              density="comfortable"
              rounded="lg"
              counter="300"
              maxlength="300"
              hide-details="auto"
            ></v-textarea>
          </div>

          <!-- Isi Tulisan Lengkap -->
          <div class="mb-5">
            <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
              Isi Cerita / Tulisan Lengkap <span class="text-error">*</span>
            </label>
            <v-textarea
              v-model="form.content"
              rows="6"
              placeholder="Tuliskan seluruh ceritamu di sini. Boleh berupa pengalaman jalan santai, laporan riset mini rute jalan kaki, rekomendasi sudut singgah, atau catatan sosial perkotaan..."
              variant="outlined"
              density="comfortable"
              rounded="lg"
              :rules="[rules.required]"
              hide-details="auto"
            ></v-textarea>
            <span class="text-caption text-grey">Format paragraf dapat menggunakan teks biasa atau HTML sederhana.</span>
          </div>

          <!-- Pilihan Warna & Gaya Kartu -->
          <div class="mb-5">
            <label class="text-subtitle-2 font-weight-bold mb-2 d-block text-grey-darken-3">
              Pilihan Nuansa Warna Kartu (Inspirasi Publikasi)
            </label>
            <v-row dense>
              <v-col cols="6" sm="3" v-for="style in cardStyleOptions" :key="style.value">
                <v-card
                  variant="outlined"
                  rounded="lg"
                  class="pa-3 text-center cursor-pointer style-picker-card"
                  :class="{ 'active-style': form.card_style === style.value }"
                  @click="form.card_style = style.value"
                >
                  <div
                    class="style-color-dot mx-auto mb-2"
                    :style="{ background: style.color }"
                  ></div>
                  <div class="text-caption font-weight-bold">{{ style.label }}</div>
                </v-card>
              </v-col>
            </v-row>
          </div>

          <!-- Upload Foto / Cover -->
          <div class="mb-5">
            <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
              Foto Sampul / Ilustrasi (Cover)
            </label>
            <v-file-input
              v-model="coverFile"
              accept="image/*"
              label="Pilih foto sampul (JPG, PNG, WebP maks 4MB)"
              prepend-icon=""
              prepend-inner-icon="mdi-camera"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              show-size
              hide-details="auto"
              @update:model-value="onCoverSelected"
            ></v-file-input>
            <div v-if="coverPreview" class="mt-3">
              <v-img
                :src="coverPreview"
                height="160"
                cover
                rounded="lg"
                class="border"
              ></v-img>
            </div>
          </div>

          <v-divider class="my-5"></v-divider>

          <!-- Profil Penulis -->
          <h4 class="text-subtitle-1 font-weight-bold text-grey-darken-4 mb-3">
            Identitas Kontributor / Penulis
          </h4>

          <v-row dense>
            <v-col cols="12" sm="6" class="mb-3">
              <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
                Nama Lengkap / Samaran <span class="text-error">*</span>
              </label>
              <v-text-field
                v-model="form.author_name"
                placeholder="Nama Anda"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                :rules="[rules.required]"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" class="mb-3">
              <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
                Email Kontak
              </label>
              <v-text-field
                v-model="form.author_email"
                type="email"
                placeholder="email@example.com"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" class="mb-3">
              <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
                Instagram (Opsional)
              </label>
              <v-text-field
                v-model="form.author_instagram"
                placeholder="@username"
                prefix="@"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                hide-details="auto"
              ></v-text-field>
            </v-col>

            <v-col cols="12" sm="6" class="mb-3">
              <label class="text-subtitle-2 font-weight-bold mb-1 d-block text-grey-darken-3">
                Bio Singkat (Opsional)
              </label>
              <v-text-field
                v-model="form.author_bio"
                placeholder="Contoh: Penggiat pejalan kaki kota Bandung"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                hide-details="auto"
              ></v-text-field>
            </v-col>
          </v-row>

          <div v-if="errorMessage" class="text-error text-caption mt-2">
            {{ errorMessage }}
          </div>
        </v-form>
      </v-card-text>

      <!-- Footer Buttons -->
      <v-card-actions v-if="!submittedSuccess" class="pa-5 pa-md-6 border-t d-flex justify-end ga-3">
        <v-btn variant="text" rounded="pill" @click="closeDialog" :disabled="submitting">
          Batal
        </v-btn>
        <v-btn
          color="primary"
          variant="flat"
          rounded="pill"
          class="px-6"
          :loading="submitting"
          @click="submitStory"
        >
          <v-icon start>mdi-send</v-icon>
          Kirim Tulisan
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'submitted'): void
}>()

const dialog = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const authStore = useAuthStore()
const { api } = useApi()

const formRef = ref<any>(null)
const submitting = ref(false)
const submittedSuccess = ref(false)
const errorMessage = ref('')
const coverFile = ref<any>(null)
const coverPreview = ref<string | null>(null)

const cardStyleOptions = [
  { value: 'coral', label: 'Coral Merah', color: '#E53935' },
  { value: 'magenta', label: 'Magenta Crimson', color: '#D81B60' },
  { value: 'amber', label: 'Amber Kuning', color: '#F59E0B' },
  { value: 'photo', label: 'Foto Cerita', color: '#374151' },
]

const form = reactive({
  title: '',
  excerpt: '',
  content: '',
  card_style: 'coral',
  author_name: '',
  author_email: '',
  author_instagram: '',
  author_bio: '',
})

const rules = {
  required: (v: any) => !!v || 'Wajib diisi',
}

// Prefill with logged in user
watch(dialog, (isOpen) => {
  if (isOpen && authStore.isLoggedIn && authStore.user) {
    if (!form.author_name) form.author_name = authStore.user.name || ''
    if (!form.author_email) form.author_email = authStore.user.email || ''
  }
})

const onCoverSelected = (file: any) => {
  const actualFile = Array.isArray(file) ? file[0] : file
  if (actualFile && actualFile instanceof File) {
    coverPreview.value = URL.createObjectURL(actualFile)
  } else {
    coverPreview.value = null
  }
}

const closeDialog = () => {
  if (!submitting.value) {
    dialog.value = false
  }
}

const resetAndClose = () => {
  submittedSuccess.value = false
  form.title = ''
  form.excerpt = ''
  form.content = ''
  coverFile.value = null
  coverPreview.value = null
  errorMessage.value = ''
  dialog.value = false
  emit('submitted')
}

const submitStory = async () => {
  if (!formRef.value) return
  const { valid } = await formRef.value.validate()
  if (!valid) return

  submitting.value = true
  errorMessage.value = ''

  try {
    const formData = new FormData()
    formData.append('title', form.title)
    if (form.excerpt) formData.append('excerpt', form.excerpt)
    formData.append('content', form.content)
    formData.append('card_style', form.card_style)
    formData.append('author_name', form.author_name)
    if (form.author_email) formData.append('author_email', form.author_email)
    if (form.author_instagram) formData.append('author_instagram', form.author_instagram.replace(/^@/, ''))
    if (form.author_bio) formData.append('author_bio', form.author_bio)

    const actualFile = Array.isArray(coverFile.value) ? coverFile.value[0] : coverFile.value
    if (actualFile instanceof File) {
      formData.append('cover_image', actualFile)
    }

    await api.post('/stories', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    submittedSuccess.value = true
  } catch (err: any) {
    console.error('Failed to submit story:', err)
    errorMessage.value = err.response?.data?.message || 'Gagal mengirim tulisan. Silakan periksa kembali input Anda.'
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.story-dialog {
  overflow: hidden;
}

.style-picker-card {
  transition: all 0.2s ease;
  border: 2px solid transparent !important;
  background-color: #f8fafc;
}

.style-picker-card:hover {
  transform: translateY(-2px);
  background-color: #f1f5f9;
}

.style-picker-card.active-style {
  border-color: #1976D2 !important;
  background-color: #eff6ff;
}

.style-color-dot {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}
</style>
