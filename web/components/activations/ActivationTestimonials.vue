<template>
  <v-row dense class="testimonial-row">
    <v-col
      v-for="testimonial in testimonials"
      :key="testimonial.id"
      cols="12"
      sm="6"
      md="4"
    >
      <div class="testimonial-card h-100">
        <!-- Quote Icon & Text -->
        <div class="d-flex align-start mb-2">
          <v-icon size="20" color="#DC2626" class="mr-2 flex-shrink-0 mt-0.5">
            mdi-format-quote-open
          </v-icon>
          <p class="testimonial-quote mb-0 flex-grow-1">
            “{{ testimonial.quote }}”
          </p>
        </div>

        <!-- Author Meta -->
        <div class="d-flex align-center mt-3 pt-2 border-top-light">
          <v-avatar
            v-if="testimonial.author_photo"
            size="32"
            class="mr-2"
          >
            <v-img :src="`${apiBase}/storage/${testimonial.author_photo}`" />
          </v-avatar>
          <v-avatar
            v-else
            size="32"
            color="#FEE2E2"
            class="mr-2"
          >
            <span class="text-caption font-weight-bold text-red-darken-2">{{ getInitials(testimonial.author_name) }}</span>
          </v-avatar>
          <div class="min-width-0">
            <div class="author-name text-truncate">{{ testimonial.author_name }}</div>
            <div class="author-role text-caption text-grey">Pejalan</div>
          </div>
        </div>
      </div>
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

<style scoped>
.testimonial-card {
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 14px;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

@media (min-width: 600px) {
  .testimonial-card {
    padding: 18px 20px;
    border-radius: 16px;
  }
}

.testimonial-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
}

.testimonial-quote {
  font-size: 0.85rem;
  line-height: 1.55;
  color: #374151;
  font-style: italic;
}

@media (min-width: 600px) {
  .testimonial-quote {
    font-size: 0.925rem;
  }
}

.border-top-light {
  border-top: 1px solid #F3F4F6;
}

.author-name {
  font-size: 0.825rem;
  font-weight: 700;
  color: #111827;
  line-height: 1.2;
}

.author-role {
  font-size: 0.72rem;
  line-height: 1.2;
}
</style>
