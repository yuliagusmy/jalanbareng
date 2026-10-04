<template>
  <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="700" persistent scrollable>
    <v-card rounded="xl" class="profile-edit-card">
      <div class="dialog-header d-flex align-center justify-space-between px-6 py-4">
        <div class="d-flex align-center ga-3">
          <div class="header-icon-box">
            <v-icon color="#DC2626" size="24">mdi-account-edit-outline</v-icon>
          </div>
          <div>
            <h2 class="text-h6 font-weight-bold text-grey-darken-4 mb-0">Edit Profil</h2>
            <p class="text-caption text-grey mb-0">Perbarui identitas dan media sosial Anda</p>
          </div>
        </div>
        <v-btn icon variant="text" size="small" @click="$emit('update:modelValue', false)" :disabled="saving">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>

      <v-divider></v-divider>

      <v-card-text class="pa-6 dialog-body">
        <v-form ref="formRef" @submit.prevent="save">
          <!-- Avatar Upload Section -->
          <div class="photo-upload-section mb-6">
            <div class="d-flex align-center ga-4">
              <v-avatar size="84" class="avatar-preview-box">
                <v-img v-if="photoPreview || userPhotoUrl" :src="photoPreview || userPhotoUrl" cover></v-img>
                <v-icon v-else size="44" color="#DC2626">mdi-account</v-icon>
              </v-avatar>
              <div class="flex-grow-1">
                <div class="text-subtitle-2 font-weight-bold mb-1">Foto Profil</div>
                <div class="text-caption text-grey mb-2">Unggah foto format JPG, PNG, atau WebP (maks. 2MB)</div>
                <v-file-input
                  v-model="photoFile"
                  label="Pilih foto profil baru"
                  accept="image/*"
                  variant="outlined"
                  density="compact"
                  rounded="lg"
                  prepend-icon=""
                  prepend-inner-icon="mdi-camera"
                  hide-details="auto"
                  @update:model-value="handlePhotoSelect"
                ></v-file-input>
              </div>
            </div>
          </div>

          <v-divider class="mb-5"></v-divider>

          <!-- Basic Info -->
          <div class="form-group mb-5">
            <h3 class="text-subtitle-2 font-weight-bold text-grey-darken-3 mb-3">
              <v-icon size="18" class="mr-1" color="#DC2626">mdi-badge-account-outline</v-icon>
              Informasi Pribadi
            </h3>
            <v-text-field
              v-model="form.name"
              label="Nama Lengkap *"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              :rules="[rules.required]"
              prepend-inner-icon="mdi-account"
              hide-details="auto"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="form.phone"
              label="Nomor WhatsApp / Telepon"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              prepend-inner-icon="mdi-phone"
              placeholder="+62 812 3456 7890"
              hide-details="auto"
            ></v-text-field>
          </div>

          <v-divider class="mb-5"></v-divider>

          <!-- Social Links -->
          <div class="form-group">
            <h3 class="text-subtitle-2 font-weight-bold text-grey-darken-3 mb-3">
              <v-icon size="18" class="mr-1" color="#DC2626">mdi-share-variant-outline</v-icon>
              Jejaring Media Sosial
            </h3>
            <v-text-field
              v-model="form.instagram"
              label="Instagram URL"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              prepend-inner-icon="mdi-instagram"
              placeholder="https://instagram.com/username"
              hide-details="auto"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="form.facebook"
              label="Facebook URL"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              prepend-inner-icon="mdi-facebook"
              placeholder="https://facebook.com/username"
              hide-details="auto"
              class="mb-3"
            ></v-text-field>

            <v-text-field
              v-model="form.twitter"
              label="Twitter / X URL"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              prepend-inner-icon="mdi-twitter"
              placeholder="https://twitter.com/username"
              hide-details="auto"
            ></v-text-field>
          </div>
        </v-form>
      </v-card-text>

      <v-divider></v-divider>

      <div class="dialog-actions d-flex align-center justify-end ga-3 px-6 py-4 bg-grey-lighten-5">
        <v-btn
          variant="text"
          rounded="pill"
          size="large"
          @click="$emit('update:modelValue', false)"
          :disabled="saving"
          class="font-weight-medium"
        >
          Batal
        </v-btn>
        <v-btn
          color="#DC2626"
          variant="flat"
          rounded="pill"
          size="large"
          :loading="saving"
          @click="save"
          class="font-weight-bold px-6 text-white"
        >
          <v-icon start size="18">mdi-check</v-icon>
          Simpan Perubahan
        </v-btn>
      </div>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useApi } from '~/composables/useApi'
import { useImageUrl } from '~/composables/useImageUrl'
import { useImageCompressor } from '~/composables/useImageCompressor'

const props = defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'saved'): void
}>()

const authStore = useAuthStore()
const { api } = useApi()
const { getImageUrl } = useImageUrl()
const { compressImage } = useImageCompressor()

const formRef = ref<any>(null)
const saving = ref(false)
const photoFile = ref<File[] | null>(null)
const photoPreview = ref<string | null>(null)

const form = ref({
  name: '',
  phone: '',
  instagram: '',
  facebook: '',
  twitter: ''
})

const rules = {
  required: (v: string) => !!v || 'Field ini wajib diisi'
}

const userPhotoUrl = computed(() => {
  return authStore.user?.photo ? getImageUrl(authStore.user.photo) : null
})

const initForm = () => {
  const user = authStore.user
  form.value = {
    name: user?.name || '',
    phone: user?.phone || '',
    instagram: user?.instagram || '',
    facebook: user?.facebook || '',
    twitter: user?.twitter || ''
  }
  photoFile.value = null
  photoPreview.value = null
}

watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    initForm()
  }
})

const handlePhotoSelect = async (files: File[]) => {
  const file = files?.[0]
  if (file) {
    try {
      const res = await compressImage(file, 800, 800, 0.85)
      photoFile.value = [res.file]
      photoPreview.value = res.previewUrl
    } catch {
      photoFile.value = [file]
      const reader = new FileReader()
      reader.onload = (e) => {
        photoPreview.value = e.target?.result as string
      }
      reader.readAsDataURL(file)
    }
  }
}

const save = async () => {
  if (!formRef.value) return
  const { valid } = await formRef.value.validate()
  if (!valid) return

  saving.value = true
  try {
    const data = new FormData()
    data.append('name', form.value.name)
    data.append('_method', 'PUT')

    if (form.value.phone) data.append('phone', form.value.phone)
    if (form.value.instagram) data.append('instagram', form.value.instagram)
    if (form.value.facebook) data.append('facebook', form.value.facebook)
    if (form.value.twitter) data.append('twitter', form.value.twitter)

    if (photoFile.value && photoFile.value.length > 0) {
      data.append('photo', photoFile.value[0])
    }

    const response = await api.post('/profile', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response.data.user) {
      authStore.setUser(response.data.user)
    }

    emit('saved')
    emit('update:modelValue', false)
  } catch (error: any) {
    console.error('Error updating profile:', error)
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.profile-edit-card {
  box-shadow: 0 20px 48px rgba(0, 0, 0, 0.16) !important;
  border: 1px solid #E5E7EB;
}

.dialog-header {
  background: #FFFFFF;
}

.header-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: #FEF2F2;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-preview-box {
  border: 2px solid #E5E7EB;
  background: #FEF2F2;
}
</style>
