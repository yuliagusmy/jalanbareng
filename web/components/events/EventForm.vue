<template>
  <div class="event-form-page">
    <v-container class="py-6 py-md-10" style="max-width: 920px;">
      <!-- Header Bar -->
      <v-card elevation="0" rounded="xl" class="mb-5 border bg-white">
        <div class="d-flex align-center justify-space-between px-4 px-sm-6 py-3.5">
          <div class="d-flex align-center ga-3 overflow-hidden">
            <v-btn icon="mdi-arrow-left" variant="tonal" size="small" rounded="pill" color="grey-darken-3" @click="cancel" aria-label="Kembali" />
            <div class="text-truncate">
              <h1 class="text-subtitle-1 text-sm-h6 font-weight-bold text-grey-darken-4 mb-0 text-truncate">
                {{ isEditMode ? 'Edit Event' : 'Buat Event Baru' }}
              </h1>
              <span class="text-caption text-grey-darken-1">Langkah {{ currentStep }} dari {{ totalSteps }}: {{ steps[currentStep - 1]?.title }}</span>
            </div>
          </div>

          <!-- Quick action for desktop only -->
          <div class="d-none d-sm-flex align-center ga-2 flex-shrink-0">
            <v-btn v-if="currentStep > 1" @click="previousStep" variant="outlined" rounded="pill" size="small" class="text-none font-weight-medium">
              <v-icon start size="16">mdi-arrow-left</v-icon>Sebelumnya
            </v-btn>
            <v-btn v-if="currentStep < totalSteps" @click="nextStep" color="#DC2626" rounded="pill" size="small" class="text-none font-weight-bold text-white">
              Selanjutnya<v-icon end size="16">mdi-arrow-right</v-icon>
            </v-btn>
            <v-btn v-else @click="submit" :loading="loading" color="#DC2626" rounded="pill" size="small" class="text-none font-weight-bold text-white">
              <v-icon start size="16">mdi-check</v-icon>{{ isEditMode ? 'Simpan' : 'Bagikan' }}
            </v-btn>
          </div>
        </div>
      </v-card>

      <!-- Step Indicator -->
      <v-card elevation="0" rounded="xl" class="mb-6 border bg-white pa-3 pa-sm-4">
        <!-- Desktop Stepper -->
        <div class="d-none d-sm-flex align-center justify-space-around">
          <div 
            v-for="(step, idx) in steps" 
            :key="step.value" 
            class="d-flex align-center cursor-pointer flex-grow-1"
            @click="currentStep = step.value"
          >
            <div class="d-flex align-center">
              <v-avatar size="28" :color="currentStep >= step.value ? '#DC2626' : 'grey-lighten-2'" class="text-white text-caption font-weight-bold">
                <v-icon v-if="currentStep > step.value" size="16">mdi-check</v-icon>
                <span v-else>{{ idx + 1 }}</span>
              </v-avatar>
              <span class="ml-2 text-caption font-weight-bold" :class="currentStep === step.value ? 'text-grey-darken-4' : 'text-grey'">
                {{ step.title }}
              </span>
            </div>
            <v-divider v-if="idx < steps.length - 1" class="mx-3 flex-grow-1" :color="currentStep > step.value ? '#DC2626' : 'grey-lighten-2'"></v-divider>
          </div>
        </div>
        <!-- Mobile Progress Bar -->
        <div class="d-flex d-sm-none flex-column ga-1.5">
          <div class="d-flex align-center justify-space-between text-caption font-weight-bold">
            <span class="text-grey-darken-4">{{ steps[currentStep - 1]?.title }}</span>
            <span class="text-red-darken-2 font-weight-black">{{ currentStep }} / {{ totalSteps }}</span>
          </div>
          <v-progress-linear :model-value="(currentStep / totalSteps) * 100" color="#DC2626" height="6" rounded bg-color="grey-lighten-3"></v-progress-linear>
        </div>
      </v-card>

      <v-form ref="eventForm" @submit.prevent="submit">
        <v-row justify="center">
          <v-col cols="12">
            <!-- Step 1: Informasi Dasar -->
            <v-card v-show="currentStep === 1" elevation="0" rounded="xl" class="border bg-white mb-6">
              <v-card-title class="pa-5 pa-sm-6 border-b">
                <h2 class="text-subtitle-1 text-sm-h6 font-weight-bold text-grey-darken-4 mb-0">1. Informasi Dasar Event</h2>
              </v-card-title>
              <v-card-text class="pa-5 pa-sm-6">
                <div class="mb-5">
                  <label class="text-subtitle-2 font-weight-bold mb-2 d-block text-grey-darken-3">Jenis Kegiatan <span class="text-error">*</span></label>
                  <v-btn-toggle v-model="formData.type" mandatory color="#DC2626" rounded="lg" class="border">
                    <v-btn value="regular" size="default" class="px-4"><v-icon start>mdi-calendar-star</v-icon>Regular Event</v-btn>
                    <v-btn value="walking" size="default" class="px-4"><v-icon start>mdi-walk</v-icon>Jalan Kaki</v-btn>
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
                  class="mb-3"
                  hint="Pilih aktivasi komunitas seperti Jalan Bareng Makassar, Olahraga Bareng, dll."
                  persistent-hint
                ></v-select>

                <v-text-field v-model="formData.name" label="Nama Event" placeholder="Contoh: Bultang Bareng! atau Jalan Sore Benteng Rotterdam" variant="outlined" :rules="[rules.required]" class="mb-3"></v-text-field>

                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="formData.date" label="Tanggal Kegiatan" type="date" variant="outlined" :rules="[rules.required]"></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field v-model="formData.time" label="Waktu Mulai" type="time" variant="outlined" :rules="[rules.required]"></v-text-field>
                  </v-col>
                </v-row>

                <!-- Titik Kumpul (TIKUM) -->
                <div class="pa-4 rounded-xl border bg-grey-lighten-5 mb-4">
                  <div class="d-flex align-center ga-2 mb-2">
                    <v-icon color="#0284C7" size="20">mdi-map-marker-radius</v-icon>
                    <span class="font-weight-bold text-subtitle-2 text-grey-darken-3">Titik Kumpul & Lokasi (Tikum)</span>
                  </div>
                  <v-text-field
                    v-model="formData.meeting_point"
                    label="Nama Tempat / Lokasi Kumpul"
                    placeholder="Contoh: Diamond Badminton Hall Mall Panakkukang Makassar"
                    variant="outlined"
                    hint="Nama gedung, lapangan, cafe, atau titik kumpul peserta"
                    persistent-hint
                    class="mb-2 bg-white"
                  ></v-text-field>

                  <v-switch
                    v-model="formData.is_curated_tikum"
                    color="#DC2626"
                    label="Tikum Rahasia (Sistem Kurasi: hanya dikirim via WhatsApp/DM bagi peserta lolos)"
                    density="comfortable"
                    hide-details
                  ></v-switch>
                </div>

                <!-- Biaya / HTM -->
                <div class="pa-4 rounded-xl border bg-grey-lighten-5 mb-4">
                  <div class="d-flex align-center ga-2 mb-2">
                    <v-icon color="#16A34A" size="20">mdi-ticket-percent-outline</v-icon>
                    <span class="font-weight-bold text-subtitle-2 text-grey-darken-3">Biaya Pendaftaran / HTM</span>
                  </div>
                  <v-row>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model.number="formData.price"
                        label="Nominal Biaya (Rp)"
                        type="number"
                        min="0"
                        prefix="Rp"
                        variant="outlined"
                        hint="Isi 0 jika kegiatan gratis, atau nominal (misal: 25000)"
                        persistent-hint
                        class="bg-white"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6">
                      <v-text-field
                        v-model="formData.price_description"
                        label="Keterangan Biaya (Opsional)"
                        placeholder="Contoh: per orang, sewa lapangan & shuttlecock"
                        variant="outlined"
                        hint="Keterangan apa saja yang didapat peserta"
                        persistent-hint
                        class="bg-white"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </div>

                <v-text-field
                  v-model="formData.registration_link"
                  label="Link Pendaftaran (Google Form / Tally / Linktree)"
                  placeholder="https://forms.gle/..."
                  variant="outlined"
                  :rules="[rules.required, rules.url]"
                  class="mb-2"
                ></v-text-field>
              </v-card-text>
              <!-- Card Action Footer -->
              <v-card-actions class="px-5 px-sm-6 py-4 border-t d-flex align-center justify-space-between flex-wrap ga-2">
                <v-btn @click="cancel" variant="text" rounded="pill" color="grey-darken-1" size="large" class="text-none">Batal</v-btn>
                <v-btn @click="nextStep" color="#DC2626" rounded="pill" size="large" class="text-none font-weight-bold text-white px-6 elevation-2">
                  <span>Selanjutnya: Konten</span><v-icon end size="18">mdi-arrow-right</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Step 2: Konten & Deskripsi -->
            <v-card v-show="currentStep === 2" elevation="0" rounded="xl" class="border bg-white mb-6">
              <v-card-title class="pa-5 pa-sm-6 border-b">
                <h2 class="text-subtitle-1 text-sm-h6 font-weight-bold text-grey-darken-4 mb-0">2. Konten & Deskripsi</h2>
              </v-card-title>
              <v-card-text class="pa-5 pa-sm-6">
                <TiptapEditor v-model="formData.description" />
                <v-text-field v-model="formData.youtube_link" label="Link YouTube (Opsional)" placeholder="https://youtube.com/watch?v=..." variant="outlined" class="mt-4"></v-text-field>
              </v-card-text>
              <!-- Card Action Footer -->
              <v-card-actions class="px-5 px-sm-6 py-4 border-t d-flex align-center justify-space-between flex-wrap ga-2">
                <v-btn @click="previousStep" variant="outlined" rounded="pill" color="grey-darken-3" size="large" class="text-none">
                  <v-icon start size="18">mdi-arrow-left</v-icon>Sebelumnya
                </v-btn>
                <v-btn @click="nextStep" color="#DC2626" rounded="pill" size="large" class="text-none font-weight-bold text-white px-6 elevation-2">
                  <span>Selanjutnya: Poster</span><v-icon end size="18">mdi-arrow-right</v-icon>
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Step 3: Poster Event -->
            <v-card v-show="currentStep === 3" elevation="0" rounded="xl" class="border bg-white mb-6">
              <v-card-title class="pa-5 pa-sm-6 border-b">
                <h2 class="text-subtitle-1 text-sm-h6 font-weight-bold text-grey-darken-4 mb-0">3. Poster Event</h2>
              </v-card-title>
              <v-card-text class="pa-5 pa-sm-6">
                <v-file-input v-model="formData.poster" label="Upload Poster Event (Aspect 4:5 atau 1:1)" variant="outlined" accept="image/*" :rules="isEditMode ? [] : [rules.fileRequired]"></v-file-input>
                <div v-if="posterPreview" class="mt-4 text-center">
                  <v-img :src="posterPreview" max-height="350" rounded="lg" class="mx-auto border"></v-img>
                  <span class="text-caption text-grey mt-2 d-block">Preview Poster yang Aktif</span>
                </div>
              </v-card-text>
              <!-- Card Action Footer -->
              <v-card-actions class="px-5 px-sm-6 py-4 border-t d-flex align-center justify-space-between flex-wrap ga-2">
                <v-btn @click="previousStep" variant="outlined" rounded="pill" color="grey-darken-3" size="large" class="text-none">
                  <v-icon start size="18">mdi-arrow-left</v-icon>Sebelumnya
                </v-btn>
                <v-btn v-if="formData.type === 'walking'" @click="nextStep" color="#DC2626" rounded="pill" size="large" class="text-none font-weight-bold text-white px-6 elevation-2">
                  <span>Selanjutnya: Rute</span><v-icon end size="18">mdi-arrow-right</v-icon>
                </v-btn>
                <v-btn v-else @click="submit" :loading="loading" color="#DC2626" rounded="pill" size="large" class="text-none font-weight-bold text-white px-8 elevation-3">
                  <v-icon start size="18">mdi-check-circle</v-icon>
                  <span>{{ isEditMode ? 'Simpan Perubahan' : 'Bagikan Event' }}</span>
                </v-btn>
              </v-card-actions>
            </v-card>

            <!-- Step 4: Rute Jalan Kaki (Walking only) -->
            <v-card v-show="currentStep === 4 && formData.type === 'walking'" elevation="0" rounded="xl" class="border bg-white mb-6">
              <v-card-title class="pa-5 pa-sm-6 border-b">
                <h2 class="text-subtitle-1 text-sm-h6 font-weight-bold text-grey-darken-4 mb-0">4. Rute Jalan Kaki</h2>
              </v-card-title>
              <v-card-text class="pa-5 pa-sm-6">
                <ClientOnly>
                  <RouteMapEditor v-model="formData.route" @update:distance="updateDistance" :is-visible="currentStep === 4" />
                </ClientOnly>
                <v-text-field v-model="formData.duration" label="Estimasi Durasi (menit)" type="number" variant="outlined" class="mt-4"></v-text-field>
              </v-card-text>
              <!-- Card Action Footer -->
              <v-card-actions class="px-5 px-sm-6 py-4 border-t d-flex align-center justify-space-between flex-wrap ga-2">
                <v-btn @click="previousStep" variant="outlined" rounded="pill" color="grey-darken-3" size="large" class="text-none">
                  <v-icon start size="18">mdi-arrow-left</v-icon>Sebelumnya
                </v-btn>
                <v-btn @click="submit" :loading="loading" color="#DC2626" rounded="pill" size="large" class="text-none font-weight-bold text-white px-8 elevation-3">
                  <v-icon start size="18">mdi-check-circle</v-icon>
                  <span>{{ isEditMode ? 'Simpan Perubahan' : 'Bagikan Event' }}</span>
                </v-btn>
              </v-card-actions>
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
import { useImageUrl } from '~/composables/useImageUrl'
import TiptapEditor from '~/components/common/TiptapEditor.vue'
import { useHead, useRuntimeConfig } from '#app'

const RouteMapEditor = defineAsyncComponent(() => import('~/components/events/RouteMapEditor.vue'))

const props = defineProps<{ eventId?: string }>()
const emit = defineEmits<{ (e: 'success', id: string): void, (e: 'cancel'): void }>()

const { api } = useApi()
const { getImageUrl } = useImageUrl()
const config = useRuntimeConfig()

const eventForm = ref<any>(null)
const currentStep = ref(1)
const loading = ref(false)
const posterPreview = ref('')
const activations = ref<any[]>([])

const isEditMode = computed(() => !!props.eventId)

const formData = ref({
  type: 'regular',
  activation_id: null as number | null,
  name: '',
  date: '',
  time: '',
  meeting_point: '',
  is_curated_tikum: false,
  price: 0,
  price_description: '',
  registration_link: '',
  description: '',
  youtube_link: '',
  poster: null as File | null,
  route: [] as any[],
  start_lat: null as number | null,
  start_lng: null as number | null,
  finish_lat: null as number | null,
  finish_lng: null as number | null,
  distance: null as number | null,
  duration: null as number | null,
  _method: 'POST'
})

useHead(() => ({
  title: isEditMode.value ? `Edit: ${formData.value.name || 'Event'}` : 'Buat Event Baru'
}))

const steps = computed(() => [
  { title: 'Informasi Dasar', value: 1 },
  { title: 'Konten', value: 2 },
  { title: 'Poster', value: 3 },
  ...(formData.value.type === 'walking' ? [{ title: 'Rute', value: 4 }] : [])
])
const totalSteps = computed(() => steps.value.length)
const rules = {
  required: (v: any) => !!v || 'Wajib diisi',
  fileRequired: (v: any) => !!v || 'Poster wajib diisi',
  url: (v: string) => !v || /^https?:\/\//.test(v) || 'URL tidak valid (awali dengan https://)'
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

    let date = ''
    let time = event.time || ''
    if (event.date) {
      if (typeof event.date === 'string') {
        date = event.date.substring(0, 10)
        if (!time && event.date.includes('T')) {
          time = event.date.split('T')[1].substring(0, 5)
        } else if (!time && event.date.includes(' ')) {
          time = event.date.split(' ')[1].substring(0, 5)
        }
      } else {
        const d = new Date(event.date)
        date = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
      }
    }

    formData.value = {
      ...formData.value,
      ...event,
      date,
      time: time || '06:00',
      meeting_point: event.meeting_point || '',
      is_curated_tikum: Boolean(event.is_curated_tikum),
      price: event.price !== null && event.price !== undefined ? Number(event.price) : 0,
      price_description: event.price_description || '',
      _method: 'PUT',
      poster: null
    }

    if (event.poster) {
      posterPreview.value = getImageUrl(event.poster)
    }
  } catch (error) {
    console.error('Failed to fetch event', error)
  }
}

const submit = async () => {
  if (eventForm.value) {
    const { valid } = await eventForm.value.validate()
    if (!valid) {
      currentStep.value = 1
      return
    }
  }

  loading.value = true
  const endpoint = isEditMode.value ? `/events/${props.eventId}` : '/events'
  const data = formData.value
  const payload = new FormData()

  payload.append('name', data.name || '')
  payload.append('type', data.type || 'regular')
  if (data.activation_id) {
    payload.append('activation_id', String(data.activation_id))
  }
  payload.append('date', data.date || '')
  payload.append('time', data.time || '')
  payload.append('meeting_point', data.meeting_point || '')
  payload.append('is_curated_tikum', data.is_curated_tikum ? '1' : '0')
  payload.append('price', String(data.price ?? 0))
  if (data.price_description) {
    payload.append('price_description', data.price_description)
  }
  payload.append('registration_link', data.registration_link || '')
  payload.append('description', data.description || '')
  if (data.youtube_link) {
    payload.append('youtube_link', data.youtube_link)
  }

  if (data.type === 'walking') {
    payload.append('start_lat', data.start_lat?.toString() || '0')
    payload.append('start_lng', data.start_lng?.toString() || '0')
    payload.append('finish_lat', data.finish_lat?.toString() || '0')
    payload.append('finish_lng', data.finish_lng?.toString() || '0')
    payload.append('distance', data.distance?.toString() || '0')
    if (data.duration) {
      payload.append('duration', data.duration.toString())
    }
    if (data.route && data.route.length > 0) {
      payload.append('route', JSON.stringify(data.route))
    }
  }

  if (data.poster) {
    payload.append('poster', data.poster)
  }

  if (isEditMode.value) {
    payload.append('_method', 'PUT')
  }

  try {
    const response = await api.post(endpoint, payload, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    emit('success', response.data.event.id)
  } catch (error: any) {
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

<style scoped>
.event-form-page {
  background-color: #FAFAF9;
  min-height: 100vh;
}
</style>
