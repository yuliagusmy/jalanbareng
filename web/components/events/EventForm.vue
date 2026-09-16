<template>
  <div>
    <v-container class="py-8" style="max-width: 935px;">
      <!-- New Top Bar -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <v-card-title class="d-flex align-center px-6 py-4" style="border-bottom: 1px solid #dbdbdb;">
          <v-btn icon size="small" variant="text" @click="cancel" class="mr-2">
            <v-icon>mdi-arrow-left</v-icon>
          </v-btn>
          <span class="text-h6 font-weight-bold">{{ isEditMode ? 'Edit Event' : 'Buat Event Baru' }}</span>
          <v-spacer></v-spacer>
          <v-btn v-if="currentStep > 1" @click="previousStep" variant="text" class="mr-2">Sebelumnya</v-btn>
          <v-btn v-if="currentStep < totalSteps" @click="nextStep" color="primary" variant="flat">Selanjutnya</v-btn>
          <v-btn v-else @click="submit" :loading="loading" color="primary" variant="flat">
            {{ isEditMode ? 'Simpan Perubahan' : 'Bagikan' }}
          </v-btn>
        </v-card-title>
      </v-card>

      <!-- Stepper Progress -->
      <v-card elevation="0" rounded="lg" class="mb-8">
        <v-card-text class="pa-6">
          <v-stepper v-model="currentStep" alt-labels :items="steps" hide-actions class="elevation-0 bg-transparent"></v-stepper>
        </v-card-text>
      </v-card>

      <v-form ref="eventForm">
        <v-row justify="center">
          <v-col cols="12" md="10" lg="8">
            <v-card v-show="currentStep === 1" elevation="0" rounded="lg" class="mb-6">
              <v-card-title class="pa-6">
                <h3 class="text-h6 font-weight-bold">1. Informasi Dasar Event</h3>
              </v-card-title>
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
                  label="Bagian dari Aktivasi (Opsional)"
                  :items="activations"
                  item-title="name"
                  item-value="id"
                  variant="outlined"
                  clearable
                  class="mb-4"
                  hint="Pilih jika event ini merupakan bagian dari program aktivasi tertentu"
                  persistent-hint
                ></v-select>

                <v-text-field v-model="formData.name" label="Nama Event" variant="outlined" :rules="[rules.required]"></v-text-field>
                <v-row>
                  <v-col cols="12" sm="6"><v-text-field v-model="formData.date" label="Tanggal Event" type="date" variant="outlined" :rules="[rules.required]"></v-text-field></v-col>
                  <v-col cols="12" sm="6"><v-text-field v-model="formData.time" label="Waktu Mulai" type="time" variant="outlined" :rules="[rules.required]"></v-text-field></v-col>
                </v-row>
                <v-text-field v-model="formData.registration_link" label="Link Pendaftaran" variant="outlined" :rules="[rules.required, rules.url]"></v-text-field>
              </v-card-text>
            </v-card>

            <v-card v-show="currentStep === 2" elevation="0" rounded="lg" class="mb-6">
              <v-card-title class="pa-6">
                <h3 class="text-h6 font-weight-bold">2. Konten & Deskripsi</h3>
              </v-card-title>
              <v-card-text class="pa-6">
                <TiptapEditor v-model="formData.description" />
                <v-text-field v-model="formData.youtube_link" label="Link YouTube (Opsional)" variant="outlined" class="mt-4"></v-text-field>
              </v-card-text>
            </v-card>

            <v-card v-show="currentStep === 3" elevation="0" rounded="lg" class="mb-6">
              <v-card-title class="pa-6">
                <h3 class="text-h6 font-weight-bold">3. Poster Event</h3>
              </v-card-title>
              <v-card-text class="pa-6">
                <v-file-input v-model="formData.poster" label="Upload Poster" variant="outlined" :rules="isEditMode ? [] : [rules.fileRequired]"></v-file-input>
                <v-img v-if="posterPreview" :src="posterPreview" height="200" class="mt-4 rounded-lg"></v-img>
              </v-card-text>
            </v-card>

            <v-card v-show="currentStep === 4 && formData.type === 'walking'" elevation="0" rounded="lg" class="mb-6">
              <v-card-title class="pa-6">
                <h3 class="text-h6 font-weight-bold">4. Rute Jalan Kaki</h3>
              </v-card-title>
              <v-card-text class="pa-6">
                <ClientOnly>
                  <RouteMapEditor v-model="formData.route" @update:distance="updateDistance" :is-visible="currentStep === 4" />
                </ClientOnly>
                <v-text-field v-model="formData.duration" label="Estimasi Durasi (menit)" type="number" variant="outlined" class="mt-4"></v-text-field>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-form>
    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineAsyncComponent, watch } from 'vue'
import { useApi } from '~/composables/useApi'
import TiptapEditor from '~/components/common/TiptapEditor.vue'
import { useHead } from '#app'

const RouteMapEditor = defineAsyncComponent(() => import('~/components/events/RouteMapEditor.vue'))

const props = defineProps<{ eventId?: string }>()
const emit = defineEmits<{ (e: 'success', id: string): void, (e: 'cancel'): void }>()

const { api } = useApi()
const config = useRuntimeConfig()

const eventForm = ref(null)
const currentStep = ref(1)
const loading = ref(false)
const posterPreview = ref('')
const activations = ref([])

const isEditMode = computed(() => !!props.eventId)

const formData = ref({
  type: 'regular',
  activation_id: null,
  name: '',
  date: '',
  time: '',
  registration_link: '',
  description: '',
  youtube_link: '',
  poster: null as File | null,
  route: [],
  start_lat: null,
  start_lng: null,
  finish_lat: null,
  finish_lng: null,
  distance: null,
  duration: null,
  _method: 'POST'
})

useHead(() => ({
  title: isEditMode.value ? `Edit: ${formData.value.name || 'Event'}` : undefined
}))

const steps = computed(() => [
  { title: 'Informasi Dasar', value: 1 },
  { title: 'Konten', value: 2 },
  { title: 'Poster', value: 3 },
  ...(formData.value.type === 'walking' ? [{ title: 'Rute', value: 4 }] : [])
])
const totalSteps = computed(() => steps.value.length)
const rules = {
  required: (v) => !!v || 'Wajib diisi',
  fileRequired: (v) => !!v || 'Poster wajib diisi',
  url: (v) => !v || /^https?:\/\//.test(v) || 'URL tidak valid'
}

const getImageUrl = (path: string) => {
  if (!path) return ''
  const apiBaseUrl = config.public.apiUrl.replace('/api', '')
  return `${apiBaseUrl}/storage/${path}`
}

const fetchActivations = async () => {
  try {
    const response = await api.get('/activations?include_inactive=1')
    activations.value = response.data
  } catch (error) {
    console.error('Error fetching activations:', error)
  }
}

const fetchEvent = async () => {
  if (!props.eventId) return
  try {
    const response = await api.get(`/events/${props.eventId}`)
    const event = response.data.event

    // Parse date and time from the datetime string
    const eventDate = new Date(event.date)
    const date = eventDate.toISOString().split('T')[0]
    const time = eventDate.toTimeString().split(' ')[0].substring(0, 5)

    formData.value = { ...formData.value, ...event, date, time, _method: 'PUT', poster: null }

    if (event.poster) {
      posterPreview.value = getImageUrl(event.poster)
    }
  } catch (error) {
    console.error('Failed to fetch event', error)
  }
}

const submit = async () => {
  const { valid } = await eventForm.value.validate()
  if (!valid) return

  loading.value = true
  const endpoint = isEditMode.value ? `/events/${props.eventId}` : '/events'
  const data = formData.value
  const payload = new FormData()

  // Append simple fields
  payload.append('name', data.name || '')
  payload.append('type', data.type || 'regular')
  if (data.activation_id) {
    payload.append('activation_id', String(data.activation_id))
  }
  payload.append('date', data.date || '')
  payload.append('time', data.time || '')
  payload.append('registration_link', data.registration_link || '')
  payload.append('description', data.description || '')
  if (data.youtube_link) {
    payload.append('youtube_link', data.youtube_link)
  }

  // Append walking-specific fields
  if (data.type === 'walking') {
    payload.append('start_lat', data.start_lat?.toString() || '0')
    payload.append('start_lng', data.start_lng?.toString() || '0')
    payload.append('finish_lat', data.finish_lat?.toString() || '0')
    payload.append('finish_lng', data.finish_lng?.toString() || '0')
    payload.append('distance', data.distance?.toString() || '0')
    if (data.duration) {
      payload.append('duration', data.duration.toString())
    }
    // Correctly stringify the route object
    if (data.route && data.route.length > 0) {
      payload.append('route', JSON.stringify(data.route))
    }
  }

  // Correctly append the poster file
  if (data.poster) {
    payload.append('poster', data.poster)
  }

  // Set method for update
  if (isEditMode.value) {
    payload.append('_method', 'PUT')
  }

  try {
    const response = await api.post(endpoint, payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    emit('success', response.data.event.id)
  } catch (error) {
    console.error('Failed to submit event form', error.response?.data || error)
  } finally {
    loading.value = false
  }
}

const cancel = () => emit('cancel')
const nextStep = () => { if (currentStep.value < totalSteps.value) currentStep.value++ }
const previousStep = () => { if (currentStep.value > 1) currentStep.value-- }

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

onMounted(() => {
  fetchActivations()
  if (isEditMode.value) fetchEvent()
})
</script>
