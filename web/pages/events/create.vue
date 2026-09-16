<template>
  <EventForm @success="onSuccess" @cancel="onCancel" />
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import EventForm from '~/components/events/EventForm.vue'

useHead({
  title: 'Buat Event Baru'
})

definePageMeta({ layout: 'solid' })

const router = useRouter()

const onSuccess = (eventId: string) => {
  // Redirect to the new event's page after successful creation
  router.push(`/events/${eventId}`)
}

const onCancel = () => {
  // Go back to the previous page
  router.back()
}
</script>