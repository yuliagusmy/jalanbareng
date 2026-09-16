<template>
  <v-expansion-panels variant="accordion" class="faq-panels">
    <v-expansion-panel
      v-for="(faq, index) in faqs"
      :key="faq.id"
      elevation="0"
      class="mb-3"
    >
      <v-expansion-panel-title class="text-h6 font-weight-medium py-5 px-6">
        <div class="d-flex align-center">
          <div class="faq-number mr-4">{{ index + 1 }}</div>
          <span>{{ faq.question }}</span>
        </div>
      </v-expansion-panel-title>
      <v-expansion-panel-text class="px-6 pb-5">
        <div class="text-body-1 text-grey-darken-1 pl-12">
          {{ faq.answer }}
        </div>
      </v-expansion-panel-text>
    </v-expansion-panel>
  </v-expansion-panels>
</template>

<script setup lang="ts">
interface Props {
  faqs: any[]
}

defineProps<Props>()
</script>

<style scoped>
.faq-panels {
  max-width: 100%;
}

.faq-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  font-weight: bold;
  font-size: 14px;
  flex-shrink: 0;
}

:deep(.v-expansion-panel) {
  border: 1px solid #e0e0e0;
  border-radius: 12px !important;
  overflow: hidden;
  transition: all 0.3s ease;
}

:deep(.v-expansion-panel:hover) {
  border-color: #667eea;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

:deep(.v-expansion-panel--active) {
  border-color: #667eea;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

:deep(.v-expansion-panel-title) {
  min-height: 64px;
}

:deep(.v-expansion-panel-text__wrapper) {
  padding-top: 0;
}
</style>
