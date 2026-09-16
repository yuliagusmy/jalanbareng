<template>
  <div>
    <v-container class="pa-6">
      <v-row class="mb-6">
        <v-col cols="12">
          <h1 class="text-h4 font-weight-bold mb-2">{{ props.mode === 'create' ? 'Buat Event Baru' : 'Edit Event' }}</h1>
          <p class="text-body-1 text-grey">{{ props.mode === 'create' ? 'Lengkapi formulir di bawah untuk membuat event komunitas' : 'Perbarui detail event di bawah ini' }}</p>
        </v-col>
      </v-row>
      <v-card elevation="2" rounded="xl" class="mb-8"><v-card-text class="pa-6"><v-stepper v-model="currentStep" alt-labels :items="steps" hide-actions class="elevation-0"></v-stepper></v-card-text></v-card>
      <v-form ref="eventForm">
        <v-row>
          <v-col cols="12" md="8">
            <v-card v-show="currentStep === 1" elevation="3" rounded="xl" class="mb-6">
              <v-card-title class="pa-6 bg-gradient"><v-icon color="primary" class="mr-3">mdi-information</v-icon><span class="text-h5 font-weight-bold">Informasi Dasar Event</span></v-card-title>
              <v-card-text class="pa-6">
                <div class="mb-6">
                  <label class="text-subtitle-1 font-weight-bold mb-3 d-block">Jenis Event <span class="text-error">*</span></label>
                  <v-btn-toggle v-model="formData.type" mandatory color="primary" rounded="lg" class="event-type-toggle">
                    <v-btn value="regular" size="large" class="px-8"><v-icon start>mdi-calendar-star</v-icon>Regular Event</v-btn>
                    <v-btn value="walking" size="large" class="px-8"><v-icon start>mdi-walk</v-icon>Jalan Kaki</v-btn>
                  </v-btn-toggle>
                </div>
                <v-select
                  v-model="formData.activation_id"
                  :items="activations"
                  item-title="name"
                  item-value="id"
                  label="Pilih Aktivasi (Opsional)"
                  variant="outlined"
                  clearable
                  hint="Pilih aktivasi yang terkait dengan event ini"
                  persistent-hint
                  class="mb-4"
                ></v-select>
                <v-text-field v-model="formData.name" label="Nama Event" variant="outlined" :rules="[rules.required]"></v-text-field>
                <v-row>
                  <v-col cols="12" sm="6"><v-text-field v-model="formData.date" label="Tanggal Event" type="date" variant="outlined" :rules="[rules.required]"></v-text-field></v-col>
                  <v-col cols="12" sm="6"><v-text-field v-model="formData.time" label="Waktu Mulai" type="time" variant="outlined" :rules="[rules.required]"></v-text-field></v-col>
                </v-row>
                <v-text-field v-model="formData.registration_link" label="Link Pendaftaran" variant="outlined" :rules="[rules.required, rules.url]"></v-text-field>
              </v-card-text>
            </v-card>
            <v-card v-show="currentStep === 2" elevation="3" rounded="xl" class="mb-6">
              <v-card-title class="pa-6 bg-gradient"><v-icon color="primary" class="mr-3">mdi-text-box</v-icon><span class="text-h5 font-weight-bold">Konten & Deskripsi</span></v-card-title>
              <v-card-text class="pa-6">
                <label class="text-subtitle-1 font-weight-bold mb-3 d-block">Deskripsi Event <span class="text-error">*</span></label>
                <TiptapEditor v-model="formData.description" />
                <v-text-field v-model="formData.youtube_link" label="Link YouTube (Opsional)" variant="outlined" class="mt-6"></v-text-field>
              </v-card-text>
            </v-card>
            <v-card v-show="currentStep === 3" elevation="3" rounded="xl" class="mb-6">
              <v-card-title class="pa-6 bg-gradient"><v-icon color="primary" class="mr-3">mdi-image-multiple</v-icon><span class="text-h5 font-weight-bold">Poster Event</span></v-card-title>
              <v-card-text class="pa-6">
                <v-file-input v-model="formData.poster" @change="onPosterChange" label="Upload Poster" :rules="[rules.required]" accept="image/*"></v-file-input>
                <v-img v-if="posterPreview" :src="posterPreview" max-height="300" contain class="mt-4 rounded-lg elevation-2"></v-img>
              </v-card-text>
            </v-card>
            <v-card v-show="currentStep === 4 && formData.type === 'walking'" elevation="3" rounded="xl" class="mb-6">
              <v-card-title class="pa-6 bg-gradient"><v-icon color="primary" class="mr-3">mdi-map-marker-path</v-icon><span class="text-h5 font-weight-bold">Rute Jalan Kaki</span></v-card-title>
              <v-card-text class="pa-6">
                <ClientOnly>
                  <RouteMapEditor v-model="formData.route" @update:distance="updateDistance" :is-visible="currentStep === 4" />
                </ClientOnly>
                <v-text-field v-model="formData.duration" label="Estimasi Durasi (menit)" type="number" variant="outlined" class="mt-4"></v-text-field>
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12" md="4">
            <v-card elevation="3" rounded="xl" class="sticky-card mb-6">
              <v-card-text class="pa-6">
                <h3 class="text-h6 font-weight-bold mb-4">Aksi</h3>
                <div class="d-flex flex-column ga-3">
                  <v-btn v-if="currentStep > 1" @click="previousStep" size="large" variant="outlined">Sebelumnya</v-btn>
                  <v-btn v-if="currentStep < totalSteps" @click="nextStep" size="large" color="primary">Selanjutnya</v-btn>
                  <v-btn @click="submitEvent" :loading="loading" size="large" color="success">{{ props.mode === 'create' ? 'Buat Event' : 'Simpan Perubahan' }}</v-btn>
                  <v-btn variant="text" @click="cancelAction" size="large">Batal</v-btn>
                </div>
              </v-card-text>
            </v-card>
            <EventHelpCard />
          </v-col>
        </v-row>
      </v-form>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, defineAsyncComponent } from 'vue'
import { useRouter } from 'vue-router'
import { useApi } from '~/composables/useApi'
import TiptapEditor from '~/components/common/TiptapEditor.vue'
import EventHelpCard from '~/components/events/EventHelpCard.vue'

const RouteMapEditor = defineAsyncComponent(() => import('~/components/events/RouteMapEditor.vue'))

const props = defineProps({
  mode: {
    type: String,
    default: 'create' // 'create' or 'edit'
  },
  eventId: {
    type: String,
    default: null
  }
})

const router = useRouter()
const { api } = useApi()
const config = useRuntimeConfig()

const eventForm = ref(null)
const currentStep = ref(1)
const loading = ref(false)
const posterPreview = ref<string | null>(null)
const activations = ref([])
const formData = ref({ type: 'regular', activation_id: null, name: '', date: '', time: '', registration_link: '', description: '', youtube_link: '', poster: [] as File[], route: [], start_lat: null, start_lng: null, finish_lat: null, finish_lng: null, distance: null, duration: null, _method: props.mode === 'edit' ? 'PUT' : null })

const steps = computed(() => {
  const base = [{ title: 'Informasi Dasar' }, { title: 'Konten' }, { title: 'Poster' }]
  if (formData.value.type === 'walking') base.push({ title: 'Rute' })
  return base
})
const totalSteps = computed(() => steps.value.length)

const rules = { required: (v) => !!v || 'Wajib diisi', url: (v) => !v || /^https?:\/\//.test(v) || 'URL tidak valid' }

const onPosterChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files && target.files.length > 0) {
    const reader = new FileReader()
    reader.onload = (e) => {
      posterPreview.value = e.target.result as string
    }
    reader.readAsDataURL(target.files[0])
  } else {
    posterPreview.value = null
  }
}

watch(() => formData.value.route, (newRoute) => {
  if (newRoute && newRoute.length > 0) {
    formData.value.start_lat = newRoute[0].lat
    formData.value.start_lng = newRoute[0].lng
    const lastPoint = newRoute[newRoute.length - 1]
    formData.value.finish_lat = lastPoint.lat
    formData.value.finish_lng = lastPoint.lng
  }
}, { deep: true })

const updateDistance = (newDistance: number) => { formData.value.distance = newDistance }

const fetchEvent = async () => {
  try {
    const response = await api.get(`/events/${props.eventId}`)
    const event = response.data.event
    const eventDate = new Date(event.date)
    const date = eventDate.toISOString().split('T')[0]
    const time = eventDate.toTimeString().split(' ')[0].substring(0, 5)

    // Set initial poster preview if an existing poster is available
    if (event.poster) {
      posterPreview.value = `${config.public.apiUrl.replace('/api', '')}/storage/${event.poster}`
    }

    formData.value = { ...formData.value, ...event, date, time, duration: event.estimated_duration, activation_id: event.activation_id, poster: [], _method: 'PUT' }
  } catch (error) {
    console.error('Failed to fetch event', error)
  }
}

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations')
    activations.value = response.data || []
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

const validateCurrentStep = async (): Promise<{ valid: boolean; message: string }> => {
  const data = formData.value
  
  if (currentStep.value === 1) {
    // Step 1: Informasi Dasar
    if (!data.name?.trim()) return { valid: false, message: 'Nama Event wajib diisi' }
    if (!data.date) return { valid: false, message: 'Tanggal Event wajib diisi' }
    if (!data.time) return { valid: false, message: 'Waktu Mulai wajib diisi' }
    if (!data.registration_link?.trim()) return { valid: false, message: 'Link Pendaftaran wajib diisi' }
    if (data.registration_link && !/^https?:\/\//.test(data.registration_link)) return { valid: false, message: 'Link Pendaftaran harus berupa URL yang valid' }
  }
  
  if (currentStep.value === 2) {
    // Step 2: Konten & Deskripsi
    const plainText = data.description?.replace(/<(.|\\n)*?>/g, '').trim() || ''
    if (!plainText) return { valid: false, message: 'Deskripsi Event wajib diisi' }
  }
  
  if (currentStep.value === 3) {
    // Step 3: Poster - required on create, optional on edit
    if (props.mode === 'create' && (!data.poster || data.poster.length === 0)) {
      return { valid: false, message: 'Poster Event wajib diupload' }
    }
  }
  
  return { valid: true, message: '' }
}

const nextStep = async () => {
  const { valid, message } = await validateCurrentStep()
  if (!valid) {
    alert(message)
    return
  }
  if (currentStep.value < totalSteps.value) currentStep.value++
}
const previousStep = () => { if (currentStep.value > 1) currentStep.value-- }

const submitEvent = async () => {
  const { valid } = await eventForm.value.validate()
  if (!valid || !formData.value.description || formData.value.description.replace(/<(.|\n)*?>/g, '').trim().length === 0) {
    alert('Formulir tidak valid atau deskripsi kosong.')
    return
  }
  loading.value = true
  const payload = new FormData()
  Object.entries(formData.value).forEach(([key, value]) => {
    if (value !== null) {
      if (key === 'route') {
        payload.append(key, JSON.stringify(value))
      } else if (key === 'is_featured') {
        payload.append(key, value ? '1' : '0')
      } else if (key === 'poster') {
        if (Array.isArray(value) && value.length > 0) {
          payload.append(key, value[0])
        }
      } else {
        payload.append(key, Array.isArray(value) ? value[0] : value)
      }
    }
  })

  console.log('Poster field in payload:', payload.get('poster'))

  try {
    const response = await api.post(`/events/${props.mode === 'edit' ? props.eventId : ''}`, payload, { headers: { 'Content-Type': 'multipart/form-data' } })
    if (props.mode === 'create') {
      router.push('/manage/events')
    } else {
      router.push(`/events/${response.data.event.id}`)
    }
  } catch (error) {
    console.error('Failed to submit', error)
    alert('Gagal memperbarui event.')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchActivations()
  if (props.mode === 'edit') {
    fetchEvent()
  }
})

const cancelAction = () => {
  if (props.mode === 'create') {
    router.push('/manage/events')
  } else {
    router.push(`/events/${props.eventId}`)
  }
}
</script>

<style scoped>
.bg-gradient { background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%); }
.event-type-toggle { width: 100%; }
.event-type-toggle .v-btn { flex: 1; }
.sticky-card { position: sticky; top: 90px; }
:deep(.v-stepper) { box-shadow: none !important; }
:deep(.v-stepper-header) { box-shadow: none !important; }
</style>