<template>
  <div class="hero-carousel-wrapper" @mouseenter="pauseAutoPlay" @mouseleave="resumeAutoPlay">
    <!-- Outer Card Frame -->
    <div class="carousel-card elevation-2">
      <!-- Top Badge -->
      <div class="carousel-badge-overlay">
        <span class="d-inline-flex align-center ga-1 px-3 py-1 rounded-pill carousel-tag">
          <v-icon size="14" color="#DC2626">mdi-camera-outline</v-icon>
          <span>Dokumentasi Pejalan Kaki</span>
        </span>
      </div>

      <!-- Slide Image Display -->
      <div class="slides-viewport">
        <transition name="carousel-fade" mode="out-in">
          <div :key="currentIndex" class="slide-content">
            <img
              :src="photos[currentIndex].src"
              :alt="photos[currentIndex].caption"
              class="slide-image"
              loading="lazy"
            />
            <!-- Gradient & Caption Bottom Bar -->
            <div class="slide-caption-bar">
              <div class="d-flex justify-space-between align-center">
                <p class="caption-text mb-0 text-white font-weight-medium">
                  {{ photos[currentIndex].caption }}
                </p>
                <span class="slide-counter text-white font-weight-bold">
                  {{ currentIndex + 1 }} / {{ photos.length }}
                </span>
              </div>
            </div>
          </div>
        </transition>
      </div>

      <!-- Navigation Arrows -->
      <button
        class="nav-btn nav-btn-prev"
        @click="prevSlide"
        aria-label="Foto sebelumnya"
      >
        <v-icon size="20">mdi-chevron-left</v-icon>
      </button>
      <button
        class="nav-btn nav-btn-next"
        @click="nextSlide"
        aria-label="Foto selanjutnya"
      >
        <v-icon size="20">mdi-chevron-right</v-icon>
      </button>

      <!-- Dot Indicators -->
      <div class="indicators-row">
        <button
          v-for="(photo, index) in photos"
          :key="index"
          class="indicator-dot"
          :class="{ active: index === currentIndex }"
          @click="goToSlide(index)"
          :aria-label="`Buka foto ke-${index + 1}`"
        ></button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const photos = [
  {
    src: '/images/hero/walk_1.jpg',
    caption: 'Rombongan pejalan kaki menelusuri trotoar kota di pagi hari'
  },
  {
    src: '/images/hero/walk_2.jpg',
    caption: 'Kebersamaan peserta di titik kumpul ruang terbuka publik'
  },
  {
    src: '/images/hero/walk_3.jpg',
    caption: 'Menyapa lorong-lorong dan sudut bersejarah kota'
  },
  {
    src: '/images/hero/walk_4.jpg',
    caption: 'Langkah santai dan obrolan hangat bersama kawan baru'
  },
  {
    src: '/images/hero/walk_5.jpg',
    caption: 'Eksplorasi bangunan cagar budaya dan narasi lokal'
  },
  {
    src: '/images/hero/walk_6.jpg',
    caption: 'Makan bareng mencicipi sajian kuliner legendaris'
  },
  {
    src: '/images/hero/walk_7.jpg',
    caption: 'Sesi kumpul santai dan berbagi catatan seusai jalan'
  }
]

const currentIndex = ref(0)
let autoPlayInterval: any = null
const isPaused = ref(false)

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % photos.length
}

const prevSlide = () => {
  currentIndex.value = (currentIndex.value - 1 + photos.length) % photos.length
}

const goToSlide = (index: number) => {
  currentIndex.value = index
}

const startAutoPlay = () => {
  stopAutoPlay()
  autoPlayInterval = setInterval(() => {
    if (!isPaused.value) {
      nextSlide()
    }
  }, 3600)
}

const stopAutoPlay = () => {
  if (autoPlayInterval) {
    clearInterval(autoPlayInterval)
    autoPlayInterval = null
  }
}

const pauseAutoPlay = () => {
  isPaused.value = true
}

const resumeAutoPlay = () => {
  isPaused.value = false
}

onMounted(() => {
  startAutoPlay()
})

onUnmounted(() => {
  stopAutoPlay()
})
</script>

<style scoped>
.hero-carousel-wrapper {
  width: 100%;
  max-width: 980px;
  margin: 0 auto;
}

.carousel-card {
  position: relative;
  width: 100%;
  border-radius: 20px;
  overflow: hidden;
  background-color: #0F172A;
  border: 1px solid #E5E7EB;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}

.carousel-badge-overlay {
  position: absolute;
  top: 14px;
  left: 16px;
  z-index: 5;
}

.carousel-tag {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  color: #111827;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.03em;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
}

.slides-viewport {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  max-height: 480px;
  overflow: hidden;
}

@media (max-width: 600px) {
  .slides-viewport {
    aspect-ratio: 16 / 10;
    max-height: 320px;
  }
}

.slide-content {
  position: relative;
  width: 100%;
  height: 100%;
}

.slide-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.slide-caption-bar {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 24px 20px 16px;
  background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.88) 100%);
  z-index: 3;
}

.caption-text {
  font-size: 0.92rem;
  line-height: 1.4;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.6);
  max-width: 80%;
}

@media (max-width: 600px) {
  .caption-text {
    font-size: 0.82rem;
    max-width: 75%;
  }
}

.slide-counter {
  font-size: 0.82rem;
  background: rgba(0, 0, 0, 0.4);
  padding: 3px 10px;
  border-radius: 12px;
  letter-spacing: 0.05em;
  white-space: nowrap;
}

/* Navigation Buttons */
.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 6;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 255, 255, 0.88);
  color: #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.nav-btn:hover {
  background: #FFFFFF;
  transform: translateY(-50%) scale(1.06);
}

.nav-btn-prev {
  left: 14px;
}

.nav-btn-next {
  right: 14px;
}

@media (max-width: 600px) {
  .nav-btn {
    width: 34px;
    height: 34px;
  }
  .nav-btn-prev {
    left: 8px;
  }
  .nav-btn-next {
    right: 8px;
  }
}

/* Indicators */
.indicators-row {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 6px;
  z-index: 6;
}

.indicator-dot {
  width: 8px;
  height: 8px;
  border-radius: 4px;
  border: none;
  background: rgba(255, 255, 255, 0.45);
  padding: 0;
  cursor: pointer;
  transition: all 0.25s ease;
}

.indicator-dot.active {
  width: 24px;
  background: #DC2626;
}

/* Transitions */
.carousel-fade-enter-active,
.carousel-fade-leave-active {
  transition: opacity 0.5s ease;
}

.carousel-fade-enter-from,
.carousel-fade-leave-to {
  opacity: 0;
}
</style>
