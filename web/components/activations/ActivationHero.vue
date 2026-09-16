<template>
  <div class="activation-hero" :style="heroStyle">
    <v-container class="fill-height">
      <v-row align="center" justify="center" class="fill-height">
        <v-col cols="12" md="8" class="text-center">
          <!-- Icon -->
          <v-avatar
            v-if="activation.icon"
            size="80"
            color="white"
            class="mb-4"
          >
            <v-img :src="`${apiBase}/storage/${activation.icon}`" />
          </v-avatar>

          <!-- Title -->
          <h1 class="text-h2 text-md-h1 font-weight-bold mb-4 text-white">
            {{ activation.hero_title || activation.name }}
          </h1>

          <!-- Subtitle -->
          <p v-if="activation.hero_subtitle" class="text-h6 text-md-h5 mb-6 text-white">
            {{ activation.hero_subtitle }}
          </p>

          <!-- CTAs -->
          <div class="d-flex flex-column flex-sm-row gap-3 justify-center">
            <v-btn
              v-if="activation.cta_primary_label"
              :href="activation.cta_primary_url"
              target="_blank"
              color="white"
              size="x-large"
              variant="elevated"
            >
              {{ activation.cta_primary_label }}
            </v-btn>
            <v-btn
              v-if="activation.cta_secondary_label"
              :href="activation.cta_secondary_url"
              target="_blank"
              color="white"
              size="x-large"
              variant="outlined"
            >
              {{ activation.cta_secondary_label }}
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>

    <!-- Overlay -->
    <div class="hero-overlay" />
  </div>
</template>

<script setup lang="ts">
interface Props {
  activation: any
}

const props = defineProps<Props>()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const heroStyle = computed(() => {
  const styles: any = {
    minHeight: '500px',
    position: 'relative',
    display: 'flex',
    alignItems: 'center'
  }

  if (props.activation.hero_image) {
    styles.backgroundImage = `url(${apiBase}/storage/${props.activation.hero_image})`
    styles.backgroundSize = 'cover'
    styles.backgroundPosition = 'center'
  } else {
    styles.backgroundColor = props.activation.color_theme || '#1976D2'
  }

  return styles
})
</script>

<style scoped>
.activation-hero {
  position: relative;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.5));
  z-index: 0;
}

.activation-hero .v-container {
  position: relative;
  z-index: 1;
}
</style>
