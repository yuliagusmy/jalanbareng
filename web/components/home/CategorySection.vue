<template>
  <div class="category-section mb-10 mb-md-16">
    <v-row align="center">
      <!-- Left Side: Title & Description -->
      <v-col cols="12" md="4" lg="3">
        <div class="category-intro">
          <p class="category-label text-overline text-primary font-weight-bold mb-2">
            JELAJAHI DESTINASI
          </p>
          <h2 class="category-title text-h4 text-md-h3 section-headline mb-4">
            Temukan Destinasi<br>
            <span class="text-primary">Favoritmu</span>
          </h2>
          <p class="text-body-2 text-md-body-1 text-grey-darken-1 mb-6" style="line-height: 1.6;">
            Telusuri berbagai kategori destinasi menarik,
            dari pantai eksotis hingga kuliner khas yang menggugah selera.
          </p>
          <v-btn
            to="/destinations"
            color="primary"
            size="large"
            rounded="pill"
            class="px-8"
            variant="flat"
          >
            JELAJAHI SEMUA
            <v-icon end>mdi-arrow-right</v-icon>
          </v-btn>
        </div>
      </v-col>

      <!-- Right Side: Category Cards -->
      <v-col cols="12" md="8" lg="9">
        <!-- Accessible region wrapper -->
        <div
          class="category-cards-wrapper"
          role="region"
          aria-label="Kategori destinasi, geser untuk melihat lebih banyak"
        >
          <!-- Loading State -->
          <div v-if="loading" class="category-scroll-container" aria-busy="true" aria-label="Memuat kategori...">
            <div
              v-for="n in 6"
              :key="n"
              class="category-scroll-item"
            >
              <v-skeleton-loader type="image, article" height="320"></v-skeleton-loader>
            </div>
          </div>

          <!-- Scroll area with fade cue + keyboard support -->
          <div v-else class="scroll-area-wrapper">
            <!-- Right-edge fade gradient: visible only when not fully scrolled -->
            <div class="scroll-fade-right" :class="{ hidden: isScrolledToEnd }" aria-hidden="true"></div>

            <!-- Category Cards (Side-by-side with horizontal scroll) -->
            <div
              ref="scrollContainerRef"
              class="category-scroll-container"
              tabindex="0"
              role="list"
              aria-label="Daftar kategori destinasi"
              @scroll="onScroll"
              @keydown.right.prevent="scrollByKey(1)"
              @keydown.left.prevent="scrollByKey(-1)"
            >
              <div
                v-for="category in categories"
                :key="category.id"
                class="category-scroll-item"
                role="listitem"
              >
                <v-card
                  :to="`/destinations?category_id=${category.id}`"
                  class="category-card"
                  elevation="0"
                  :aria-label="`Jelajahi kategori ${category.name}`"
                >
                  <div class="category-image-wrapper">
                    <img
                      :src="category.image"
                      :alt="category.name"
                      class="category-image"
                    />
                    <div class="category-overlay" aria-hidden="true"></div>
                    <div class="category-content">
                      <h3 class="category-name">{{ category.name }}</h3>
                    </div>
                  </div>
                </v-card>
              </div>
            </div>
          </div>

          <!-- Dot indicator: visible only on mobile -->
          <div
            v-if="!loading && categories.length > 0"
            class="scroll-dots d-flex d-md-none justify-center ga-1 mt-3"
            role="tablist"
            aria-label="Posisi scroll kategori"
          >
            <button
              v-for="(_, i) in dotCount"
              :key="i"
              class="scroll-dot"
              :class="{ active: activeDot === i }"
              :aria-label="`Geser ke kategori grup ${i + 1}`"
              :aria-selected="activeDot === i"
              role="tab"
              @click="scrollToDot(i)"
            ></button>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  categories: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const scrollContainerRef = ref<HTMLElement | null>(null)
const scrollLeft = ref(0)
const scrollWidth = ref(0)
const clientWidth = ref(0)

// True ketika user sudah scroll sampai ujung kanan
const isScrolledToEnd = computed(() => {
  if (scrollWidth.value === 0) return false
  return scrollLeft.value + clientWidth.value >= scrollWidth.value - 8
})

// Jumlah dots: 1 dot per ~3 item (kira-kira 1 "layar penuh" per grup)
const ITEMS_PER_DOT = 3
const dotCount = computed(() => {
  const count = props.categories.length
  return Math.max(1, Math.ceil(count / ITEMS_PER_DOT))
})

// Dot aktif berdasarkan posisi scroll
const activeDot = computed(() => {
  if (!scrollContainerRef.value || scrollWidth.value <= clientWidth.value) return 0
  const maxScroll = scrollWidth.value - clientWidth.value
  const ratio = scrollLeft.value / maxScroll
  return Math.round(ratio * (dotCount.value - 1))
})

const onScroll = () => {
  if (!scrollContainerRef.value) return
  scrollLeft.value = scrollContainerRef.value.scrollLeft
  scrollWidth.value = scrollContainerRef.value.scrollWidth
  clientWidth.value = scrollContainerRef.value.clientWidth
}

// Scroll saat klik dot
const scrollToDot = (index: number) => {
  if (!scrollContainerRef.value) return
  const maxScroll = scrollContainerRef.value.scrollWidth - scrollContainerRef.value.clientWidth
  const targetScroll = (index / (dotCount.value - 1)) * maxScroll
  scrollContainerRef.value.scrollTo({ left: targetScroll, behavior: 'smooth' })
}

// Scroll via keyboard arrow
const scrollByKey = (direction: 1 | -1) => {
  if (!scrollContainerRef.value) return
  const itemWidth = 157 // 145px card + 12px gap
  scrollContainerRef.value.scrollBy({ left: direction * itemWidth, behavior: 'smooth' })
}

onMounted(() => {
  if (scrollContainerRef.value) {
    scrollWidth.value = scrollContainerRef.value.scrollWidth
    clientWidth.value = scrollContainerRef.value.clientWidth
  }
})
</script>

<style scoped>
.category-title {
  font-weight: 800 !important;
  letter-spacing: -0.035em !important;
  line-height: 1.2 !important;
}

/* Category Scroll Container */
.category-scroll-container {
  display: flex;
  overflow-x: auto;
  gap: 12px;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  padding: 8px 4px 16px;
  margin: 0 -4px;
}

.category-scroll-container::-webkit-scrollbar {
  height: 6px;
}

.category-scroll-container::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.04);
  border-radius: 9999px;
}

.category-scroll-container::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.15);
  border-radius: 9999px;
}

.category-scroll-container::-webkit-scrollbar-thumb:hover {
  background: rgba(220, 38, 38, 0.4);
}

.category-scroll-item {
  flex: 0 0 160px;
  width: 160px;
  scroll-snap-align: start;
}

@media (max-width: 600px) {
  .category-scroll-item {
    flex: 0 0 145px;
    width: 145px;
  }
}

/* Category Cards - Portrait Tall & Thin */
.category-card {
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  cursor: pointer;
}

.category-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.category-image-wrapper {
  position: relative;
  height: 320px;
  overflow: hidden;
}

.category-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.category-card:hover .category-image {
  transform: scale(1.1);
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0) 100%);
}

.category-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 16px 12px;
  z-index: 2;
  text-align: center;
}

.category-name {
  color: white;
  font-size: 13px;
  font-weight: 600;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.4);
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
  text-wrap: balance;
  line-height: 1.3;
}

/* Responsive */
@media (max-width: 960px) {
  .category-image-wrapper {
    height: 280px;
  }
}

@media (max-width: 600px) {
  .category-image-wrapper {
    height: 240px;
  }

  .category-name {
    font-size: 12px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .category-card,
  .category-image {
    transition: none;
  }

  .category-card:hover {
    transform: none;
  }

  .category-card:hover .category-image {
    transform: none;
  }
}

/* Scroll area wrapper — needed for fade gradient positioning */
.scroll-area-wrapper {
  position: relative;
}

/* Right-edge fade gradient — visual cue "ada konten lagi" */
.scroll-fade-right {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 16px; /* account for scrollbar padding */
  width: 56px;
  background: linear-gradient(to right, transparent, rgba(250, 250, 249, 0.92));
  pointer-events: none;
  z-index: 2;
  border-radius: 0 16px 16px 0;
  transition: opacity 0.25s ease;
}

.scroll-fade-right.hidden {
  opacity: 0;
}

/* Scroll container focus style for keyboard nav */
.category-scroll-container:focus {
  outline: 2px solid #DC2626;
  outline-offset: 2px;
  border-radius: 8px;
}

.category-scroll-container:focus:not(:focus-visible) {
  outline: none;
}

/* Dot indicators */
.scroll-dots {
  padding: 4px 0;
}

.scroll-dot {
  width: 6px;
  height: 6px;
  border-radius: 9999px;
  background: #D1D5DB;
  border: none;
  padding: 0;
  cursor: pointer;
  transition: width 0.25s ease, background-color 0.25s ease;
  flex-shrink: 0;
}

.scroll-dot.active {
  width: 20px;
  background: #DC2626;
}

.scroll-dot:hover:not(.active) {
  background: #9CA3AF;
}

@media (prefers-reduced-motion: reduce) {
  .scroll-dot {
    transition: none;
  }

  .scroll-fade-right {
    transition: none;
  }
}
</style>
