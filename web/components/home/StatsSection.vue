<template>
  <v-row class="mb-10 mb-md-16" dense>
    <v-col cols="6" md="3" v-for="(stat, index) in stats" :key="index">
      <v-card
        elevation="0"
        rounded="xl"
        class="h-100 pa-4 pa-sm-6 text-center stat-item-card border"
      >
        <div class="stat-icon-badge mx-auto mb-3" :style="{ backgroundColor: getBadgeBg(stat.color) }">
          <v-icon :color="stat.color" :size="$vuetify.display.mobile ? 24 : 28">{{ stat.icon }}</v-icon>
        </div>
        <div
          class="font-weight-black stat-number mb-1"
          :class="$vuetify.display.mobile ? 'text-h5' : 'text-h4'"
          :style="{ color: stat.color }"
        >
          {{ stat.value }}
        </div>
        <div
          class="font-weight-bold text-grey-darken-2"
          :class="$vuetify.display.mobile ? 'text-caption' : 'text-body-2'"
        >
          {{ stat.label }}
        </div>
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

const getBadgeBg = (hexColor: string) => {
  return `${hexColor}15`
}
</script>

<style scoped>
.stat-item-card {
  background-color: #FFFFFF;
  border-color: #E2E8F0 !important;
  transition: all 0.25s ease;
}

.stat-item-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
  border-color: #CBD5E1 !important;
}

.stat-icon-badge {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-number {
  line-height: 1.2;
  letter-spacing: -0.5px;
}
</style>
