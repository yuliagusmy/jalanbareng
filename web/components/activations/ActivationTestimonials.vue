<template>
  <v-row>
    <v-col
      v-for="testimonial in testimonials"
      :key="testimonial.id"
      cols="12"
      md="6"
      lg="4"
    >
          <v-card height="100%" variant="outlined">
            <v-card-text class="d-flex flex-column h-100">
              <!-- Quote Icon -->
              <v-icon size="32" color="primary" class="mb-3">
                mdi-format-quote-open
              </v-icon>

              <!-- Quote Text -->
              <p class="text-body-1 mb-4 flex-grow-1">
                "{{ testimonial.quote }}"
              </p>

              <!-- Author -->
              <div class="d-flex align-center">
                <v-avatar
                  v-if="testimonial.author_photo"
                  size="40"
                  class="mr-3"
                >
                  <v-img :src="`${apiBase}/storage/${testimonial.author_photo}`" />
                </v-avatar>
                <v-avatar
                  v-else
                  size="40"
                  color="primary"
                  class="mr-3"
                >
                  <span class="text-white">{{ getInitials(testimonial.author_name) }}</span>
                </v-avatar>
                <div>
                  <div class="font-weight-medium">{{ testimonial.author_name }}</div>
                  <div class="text-caption text-medium-emphasis">Peserta</div>
                </div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
  </v-row>
</template>

<script setup lang="ts">
interface Props {
  testimonials: any[]
}

defineProps<Props>()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const getInitials = (name: string) => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}
</script>
