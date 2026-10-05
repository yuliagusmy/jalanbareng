<template>
  <div class="pa-10 text-center">
    <v-progress-circular indeterminate color="#DC2626" size="36" />
    <p class="text-body-2 text-grey-darken-1 mt-4">Mengalihkan ke panel Laporan &amp; Ekspor Admin...</p>
  </div>
</template>

<script setup lang="ts">
import { navigateTo } from '#app'

definePageMeta({
  middleware: [
    () => {
      return navigateTo('/manage/reports', { replace: true })
    }
  ]
})
</script>
