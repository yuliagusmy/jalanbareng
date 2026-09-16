<template>
  <v-row class="mb-10 mb-md-16">
    <v-col cols="6" md="3" v-for="(stat, index) in stats" :key="index" class="text-center">
      <v-card elevation="0" color="transparent">
        <v-card-text class="pa-2 pa-sm-4">
          <v-icon :color="stat.color" :size="$vuetify.display.mobile ? 32 : 48" class="mb-2 mb-sm-3">{{ stat.icon }}</v-icon>
          <div :class="$vuetify.display.mobile ? 'text-h5' : 'text-h3'" class="font-weight-bold" :style="{ color: stat.color }">
            {{ stat.value }}
          </div>
          <div :class="$vuetify.display.mobile ? 'text-caption' : 'text-body-1'" class="text-grey-darken-1 mt-1 mt-sm-2">
            {{ stat.label }}
          </div>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
const props = defineProps({
  stats: {
    type: Array,
    required: true
  }
})
</script>
