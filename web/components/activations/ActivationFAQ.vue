<template>
  <v-expansion-panels variant="accordion" class="faq-panels">
    <v-expansion-panel
      v-for="(faq, index) in faqs"
      :key="faq.id"
      elevation="0"
      class="mb-2 mb-md-3 faq-panel-item"
    >
      <v-expansion-panel-title class="faq-panel-title py-3 px-3 py-sm-4 px-sm-5">
        <div class="d-flex align-center ga-2 ga-sm-3 w-100">
          <div class="faq-number">{{ index + 1 }}</div>
          <span class="faq-question-text">{{ faq.question }}</span>
        </div>
      </v-expansion-panel-title>
      <v-expansion-panel-text class="faq-panel-body px-3 px-sm-5 pb-4">
        <div class="faq-answer-text text-grey-darken-2">
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

.faq-panel-item {
  border: 1px solid #E5E7EB !important;
  border-radius: 14px !important;
  overflow: hidden;
  background: #FFFFFF !important;
  transition: all 0.25s ease;
}

:deep(.v-expansion-panel-title) {
  min-height: 48px !important;
  padding: 12px 14px !important;
}

@media (min-width: 600px) {
  :deep(.v-expansion-panel-title) {
    min-height: 56px !important;
    padding: 16px 20px !important;
  }
}

.faq-number {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #FEE2E2;
  color: #DC2626;
  font-weight: 700;
  font-size: 0.75rem;
  flex-shrink: 0;
}

@media (min-width: 600px) {
  .faq-number {
    width: 30px;
    height: 30px;
    font-size: 0.825rem;
  }
}

.faq-question-text {
  font-size: 0.9rem;
  font-weight: 600;
  color: #111827;
  line-height: 1.35;
  text-align: left;
}

@media (min-width: 600px) {
  .faq-question-text {
    font-size: 1.05rem;
  }
}

.faq-answer-text {
  font-size: 0.85rem;
  line-height: 1.6;
  padding-left: 34px;
}

@media (min-width: 600px) {
  .faq-answer-text {
    font-size: 0.95rem;
    padding-left: 42px;
  }
}

:deep(.v-expansion-panel:hover) {
  border-color: #DC2626 !important;
}

:deep(.v-expansion-panel--active) {
  border-color: #DC2626 !important;
  box-shadow: 0 4px 16px rgba(220, 38, 38, 0.08) !important;
}

:deep(.v-expansion-panel-text__wrapper) {
  padding-top: 0 !important;
}
</style>
