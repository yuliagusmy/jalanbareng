<template>
  <!-- Bottom Sheet Overlay -->
  <Teleport to="body">
    <div
      class="dir-sheet-backdrop"
      :class="{ open: modelValue }"
      aria-hidden="true"
      @click="$emit('update:modelValue', false)"
    ></div>

    <div
      class="dir-sheet"
      :class="{ open: modelValue, expanded: isExpanded }"
      role="dialog"
      aria-modal="true"
      aria-label="Direktori Destinasi"
    >
      <!-- Drag Handle -->
      <div
        class="dir-sheet-handle-area"
        @click="toggleExpand"
        @touchstart="onTouchStart"
        @touchmove="onTouchMove"
        @touchend="onTouchEnd"
        aria-label="Seret untuk memperluas panel"
      >
        <div class="dir-sheet-handle" aria-hidden="true"></div>
        <div class="dir-sheet-header d-flex align-center justify-space-between px-4 pb-2">
          <div class="d-flex align-center ga-2">
            <v-icon size="16" color="#DC2626">mdi-map-marker-multiple-outline</v-icon>
            <span class="text-subtitle-2 font-weight-bold text-grey-darken-4">
              Direktori
              <span class="text-grey-darken-1 font-weight-regular">({{ totalCount }})</span>
            </span>
          </div>
          <div class="d-flex align-center ga-2">
            <v-btn
              icon
              size="x-small"
              variant="text"
              :aria-label="isExpanded ? 'Kompres panel' : 'Perluas panel'"
              @click.stop="toggleExpand"
            >
              <v-icon size="16">{{ isExpanded ? 'mdi-chevron-down' : 'mdi-chevron-up' }}</v-icon>
            </v-btn>
            <v-btn
              icon
              size="x-small"
              variant="text"
              aria-label="Tutup direktori"
              @click.stop="$emit('update:modelValue', false)"
            >
              <v-icon size="16">mdi-close</v-icon>
            </v-btn>
          </div>
        </div>
      </div>

      <!-- Search within sheet -->
      <div class="px-4 pb-3">
        <v-text-field
          v-model="localSearch"
          placeholder="Cari destinasi..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          rounded="pill"
          density="compact"
          hide-details
          clearable
          class="sheet-search"
          @update:model-value="onSearch"
        />
      </div>

      <!-- Content tabs: Landmark | Destinasi -->
      <div class="dir-sheet-tabs px-4 pb-2">
        <button
          type="button"
          class="sheet-tab-btn"
          :class="{ active: activeTab === 'landmark' }"
          @click="activeTab = 'landmark'"
        >
          <v-icon size="14" class="mr-1">mdi-compass-outline</v-icon>
          Landmark ({{ landmarks.length }})
        </button>
        <button
          type="button"
          class="sheet-tab-btn"
          :class="{ active: activeTab === 'destinations' }"
          @click="activeTab = 'destinations'"
        >
          <v-icon size="14" class="mr-1">mdi-map-marker-outline</v-icon>
          Destinasi ({{ filteredDestinations.length }})
        </button>
      </div>

      <!-- Scrollable Content -->
      <div class="dir-sheet-body">

        <!-- LANDMARK TAB -->
        <div v-if="activeTab === 'landmark'" class="px-4 pb-4">
          <div
            v-for="lm in filteredLandmarks"
            :key="lm.id"
            class="landmark-row"
            @click="$emit('select-landmark', lm.name)"
            role="button"
            tabindex="0"
            :aria-label="`Pilih landmark ${lm.name}`"
            @keydown.enter="$emit('select-landmark', lm.name)"
          >
            <div class="landmark-row-img">
              <img :src="lm.image" :alt="lm.name" loading="lazy" />
            </div>
            <div class="landmark-row-info flex-grow-1 min-w-0">
              <div class="text-caption text-grey-darken-1 mb-1">
                <v-icon size="11" color="#DC2626">mdi-map-marker</v-icon>
                {{ lm.city }} · {{ lm.category }}
              </div>
              <div class="font-weight-bold text-grey-darken-4 text-truncate" style="font-size: 0.85rem;">
                {{ lm.name }}
              </div>
              <div class="text-caption text-grey mt-1">
                <v-icon size="11" color="#10B981">mdi-walk</v-icon>
                {{ lm.walkDistance }} · {{ lm.bestTime }}
              </div>
            </div>
            <v-icon size="16" color="#DC2626" class="flex-shrink-0">mdi-arrow-right</v-icon>
          </div>

          <div v-if="filteredLandmarks.length === 0" class="text-center py-6 text-grey">
            <v-icon size="36" color="grey-lighten-1" class="mb-2">mdi-compass-off-outline</v-icon>
            <div class="text-caption">Tidak ada landmark yang cocok</div>
          </div>
        </div>

        <!-- DESTINATIONS TAB -->
        <div v-if="activeTab === 'destinations'" class="px-4 pb-4">
          <!-- Category Filter Chips -->
          <div class="category-chips-row mb-3">
            <button
              type="button"
              class="cat-chip"
              :class="{ active: selectedCategory === null }"
              @click="$emit('filter-category', null)"
            >
              Semua
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              class="cat-chip"
              :class="{ active: selectedCategory === cat.id }"
              @click="$emit('filter-category', cat.id)"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Loading -->
          <div v-if="loading" class="text-center py-6">
            <v-progress-circular indeterminate color="#DC2626" size="28" />
          </div>

          <!-- Destination rows -->
          <div v-else>
            <nuxt-link
              v-for="dest in filteredDestinations"
              :key="dest.id"
              :to="`/destinations/${dest.id}`"
              class="dest-row text-decoration-none"
              @click="$emit('update:modelValue', false)"
            >
              <v-avatar size="48" rounded="lg" class="flex-shrink-0">
                <img
                  :src="getImageUrl(dest.primary_photo)"
                  :alt="dest.name"
                  style="width:100%;height:100%;object-fit:cover;"
                />
              </v-avatar>
              <div class="flex-grow-1 min-w-0">
                <div class="font-weight-bold text-grey-darken-4 text-truncate" style="font-size: 0.85rem;">
                  {{ dest.name }}
                </div>
                <div class="text-caption text-grey-darken-1 mt-1 d-flex align-center ga-2">
                  <span v-if="dest.category?.name || dest.category_name">
                    {{ dest.category?.name || dest.category_name }}
                  </span>
                  <span class="d-flex align-center ga-1">
                    <v-icon size="11" color="red">mdi-heart</v-icon>
                    {{ dest.likes_count || 0 }}
                  </span>
                </div>
              </div>
              <v-icon size="16" color="grey" class="flex-shrink-0">mdi-chevron-right</v-icon>
            </nuxt-link>

            <div v-if="filteredDestinations.length === 0" class="text-center py-6 text-grey">
              <v-icon size="36" color="grey-lighten-1" class="mb-2">mdi-map-marker-off-outline</v-icon>
              <div class="text-caption">Tidak ada destinasi yang cocok</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { LANDMARKS_DATA, type LandmarkItem } from '~/utils/landmarks'
import { useRuntimeConfig } from '#app'

const props = defineProps<{
  modelValue: boolean
  destinations: any[]
  categories: any[]
  selectedCategory: number | null
  loading?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', v: boolean): void
  (e: 'select-landmark', name: string): void
  (e: 'filter-category', id: number | null): void
  (e: 'search', q: string): void
}>()

const config = useRuntimeConfig()
const activeTab = ref<'landmark' | 'destinations'>('landmark')
const isExpanded = ref(false)
const localSearch = ref('')

// Touch drag state
let touchStartY = 0
let touchCurrentY = 0

const onTouchStart = (e: TouchEvent) => {
  touchStartY = e.touches[0].clientY
}
const onTouchMove = (e: TouchEvent) => {
  touchCurrentY = e.touches[0].clientY
}
const onTouchEnd = () => {
  const delta = touchCurrentY - touchStartY
  if (delta < -40) isExpanded.value = true   // swipe up
  if (delta > 40) isExpanded.value = false    // swipe down
}

const toggleExpand = () => {
  isExpanded.value = !isExpanded.value
}

const onSearch = (q: string) => {
  emit('search', q ?? '')
}

const getImageUrl = (path?: string) => {
  if (!path) return 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=400&h=300&fit=crop'
  if (path.startsWith('http')) return path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const landmarks = LANDMARKS_DATA

const filteredLandmarks = computed(() => {
  const q = localSearch.value.toLowerCase().trim()
  if (!q) return landmarks
  return landmarks.filter(lm =>
    lm.name.toLowerCase().includes(q) ||
    lm.category.toLowerCase().includes(q) ||
    lm.city.toLowerCase().includes(q)
  )
})

const filteredDestinations = computed(() => {
  const q = localSearch.value.toLowerCase().trim()
  if (!q) return props.destinations
  return props.destinations.filter(d =>
    d.name?.toLowerCase().includes(q) ||
    d.category?.name?.toLowerCase().includes(q) ||
    d.category_name?.toLowerCase().includes(q)
  )
})

const totalCount = computed(() =>
  landmarks.length + props.destinations.length
)
</script>

<style scoped>
/* Backdrop */
.dir-sheet-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  z-index: 199;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}
.dir-sheet-backdrop.open {
  opacity: 1;
  pointer-events: all;
}

/* Sheet container */
.dir-sheet {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 200;
  background: #FFFFFF;
  border-radius: 20px 20px 0 0;
  box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.14);
  transform: translateY(100%);
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
  max-height: 50vh;
  display: flex;
  flex-direction: column;
}

.dir-sheet.open {
  transform: translateY(0);
}

.dir-sheet.expanded {
  max-height: 85vh;
}

/* Handle */
.dir-sheet-handle-area {
  cursor: pointer;
  padding-top: 10px;
}

.dir-sheet-handle {
  width: 40px;
  height: 4px;
  background: #D1D5DB;
  border-radius: 9999px;
  margin: 0 auto 10px;
}

/* Tabs */
.dir-sheet-tabs {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid #F1F5F9;
  padding-bottom: 8px;
}

.sheet-tab-btn {
  flex: 1;
  padding: 7px 0;
  border: none;
  border-radius: 8px;
  background: #F8FAFC;
  color: #6B7280;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sheet-tab-btn.active {
  background: #FEF2F2;
  color: #DC2626;
}

/* Scrollable body */
.dir-sheet-body {
  flex: 1;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}

.dir-sheet-body::-webkit-scrollbar { display: none; }

/* Landmark rows */
.landmark-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #F9FAFB;
  cursor: pointer;
  transition: background 0.15s ease;
  border-radius: 8px;
}

.landmark-row:active {
  background: #FEF2F2;
}

.landmark-row-img {
  width: 52px;
  height: 52px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: #F3F4F6;
}

.landmark-row-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Destination rows */
.dest-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 0;
  border-bottom: 1px solid #F9FAFB;
  color: inherit;
}

/* Category chips */
.category-chips-row {
  display: flex;
  gap: 6px;
  overflow-x: auto;
  scrollbar-width: none;
  padding-bottom: 4px;
}
.category-chips-row::-webkit-scrollbar { display: none; }

.cat-chip {
  flex-shrink: 0;
  padding: 5px 12px;
  border-radius: 9999px;
  border: 1px solid #E5E7EB;
  background: #FFFFFF;
  color: #4B5563;
  font-size: 0.72rem;
  font-weight: 600;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.18s ease;
}

.cat-chip.active {
  background: #DC2626;
  border-color: #DC2626;
  color: #FFFFFF;
}

/* Search field */
.sheet-search :deep(.v-field__outline) {
  border-color: #E5E7EB;
}

@media (prefers-reduced-motion: reduce) {
  .dir-sheet,
  .dir-sheet-backdrop {
    transition: none;
  }
}
</style>
