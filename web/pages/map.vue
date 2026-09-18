<script setup lang="ts">
import { navigateTo } from '#app'

definePageMeta({
  middleware: [
    (to) => {
      const targetQuery = { ...to.query, view: 'map' }
      return navigateTo({ path: '/destinations', query: targetQuery }, { redirectCode: 301 })
    }
  ]
})
</script>

<template>
  <div class="d-flex align-center justify-center py-16">
    <v-progress-circular indeterminate color="#DC2626" size="36"></v-progress-circular>
  </div>
</template>
