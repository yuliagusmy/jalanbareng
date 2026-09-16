<template>
  <div v-if="featuredActivation" class="mb-10 mb-md-16">
    <div class="text-center mb-6 mb-md-8 px-4">
      <v-chip color="secondary" variant="flat" class="mb-3 mb-md-4">
        <v-icon start>mdi-star-circle</v-icon>
        Aktivasi Unggulan
      </v-chip>
          <h2 :class="$vuetify.display.mobile ? 'text-h5' : 'text-h3'" class="font-weight-bold mb-2">{{
        featuredActivation.name }}</h2>
      <p :class="$vuetify.display.mobile ? 'text-caption' : 'text-body-1'" class="text-grey-darken-1">{{
        featuredActivation.tagline }}</p>
    </div>

    <FeaturedActivationOption2 :activation="featuredActivation" />
  </div>
</template>

<script setup lang="ts">
import FeaturedActivationOption2 from '~/components/FeaturedActivationOption2.vue'

const props = defineProps({
  featuredActivation: {
    type: Object,
    default: null
  }
})
</script>
