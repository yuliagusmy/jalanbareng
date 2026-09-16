<template>
  <EventForm :event-id="eventId" @success="onSuccess" @cancel="onCancel" />
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { nextTick } from 'vue'
import EventForm from '~/components/events/EventForm.vue'

definePageMeta({ layout: 'solid' })

const route = useRoute()
const router = useRouter()

const eventId = Array.isArray(route.params.id) ? route.params.id[0] : route.params.id

const onSuccess = (id: string) => {
  // After editing, redirect back to the event's main page
  nextTick(() => {
    router.push(`/events/${id}`)
  })
}

const onCancel = () => {
  // Go back to the previous page
  router.back()
}
</script>