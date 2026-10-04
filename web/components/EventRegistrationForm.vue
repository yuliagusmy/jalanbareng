<template>
  <v-btn
    :loading="loading"
    color="#DC2626"
    size="small"
    rounded="pill"
    class="font-weight-bold"
    @click.stop="register"
  >
    Daftar Event
  </v-btn>
</template>

<script setup lang="ts">
const props = defineProps<{ eventId: number | string }>()
const api = useApi()
const loading = ref(false)

const register = async () => {
  loading.value = true
  try {
    await api.post(`/events/${props.eventId}/join`)
    alert('Berhasil mendaftar ke event!')
  } catch (error: any) {
    alert(error?.data?.message || 'Gagal mendaftar event.')
  } finally {
    loading.value = false
  }
}
</script>
