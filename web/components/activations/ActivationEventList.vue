<template>
  <v-row>
    <v-col cols="12">
      <h2 class="text-h4 font-weight-bold mb-4">Jadwal & Agenda</h2>
    </v-col>

    <!-- Upcoming Events -->
    <v-col v-if="activation.upcoming_events && activation.upcoming_events.length > 0" cols="12">
      <h3 class="text-h6 font-weight-bold mb-3">Event Mendatang</h3>
      <v-row>
        <v-col
          v-for="event in activation.upcoming_events.slice(0, 6)"
          :key="event.id"
          cols="12"
          md="6"
        >
          <v-card :to="`/events/${event.id}`" hover>
            <v-card-text>
              <div class="d-flex align-start">
                <!-- Date Badge -->
                <div class="text-center mr-4" style="min-width: 60px">
                  <div class="text-h4 font-weight-bold" :style="{ color: activation.color_theme }">
                    {{ formatDay(event.date) }}
                  </div>
                  <div class="text-caption text-uppercase">
                    {{ formatMonth(event.date) }}
                  </div>
                </div>

                <!-- Event Info -->
                <div class="flex-grow-1">
                  <h4 class="text-h6 mb-2">{{ event.name }}</h4>
                  <div class="d-flex align-center text-caption text-medium-emphasis mb-1">
                    <v-icon size="16" class="mr-1">mdi-clock-outline</v-icon>
                    {{ formatTime(event.date) }}
                  </div>
                  <div v-if="event.distance" class="d-flex align-center text-caption text-medium-emphasis">
                    <v-icon size="16" class="mr-1">mdi-map-marker-distance</v-icon>
                    {{ event.distance }} km
                  </div>
                </div>

                <!-- Event Type Badge -->
                <v-chip
                  v-if="event.type === 'walking'"
                  color="success"
                  size="small"
                >
                  Jalan
                </v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- View All Button -->
      <div v-if="activation.upcoming_events.length > 6" class="text-center mt-4">
        <v-btn
          :to="`/events?activation=${activation.id}`"
          :color="activation.color_theme || 'primary'"
          variant="outlined"
        >
          Lihat Semua Event
        </v-btn>
      </div>
    </v-col>

    <!-- No Upcoming Events -->
    <v-col v-else cols="12">
      <v-card variant="outlined">
        <v-card-text class="text-center py-8">
          <v-icon size="48" color="grey-lighten-1">mdi-calendar-blank</v-icon>
          <p class="text-body-1 text-medium-emphasis mt-4">
            Belum ada jadwal event terdekat.
          </p>
          <p v-if="activation.cta_primary_label" class="text-body-2 text-medium-emphasis">
            Bergabung di grup untuk mendapatkan info terbaru!
          </p>
          <v-btn
            v-if="activation.cta_primary_url"
            :href="activation.cta_primary_url"
            target="_blank"
            :color="activation.color_theme || 'primary'"
            class="mt-4"
          >
            {{ activation.cta_primary_label || 'Gabung Grup' }}
          </v-btn>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
interface Props {
  activation: any
}

defineProps<Props>()

const formatDay = (dateString: string) => {
  const date = new Date(dateString)
  return date.getDate()
}

const formatMonth = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', { month: 'short' })
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
}
</script>
