<template>
  <div 
    ref="containerRef" 
    class="lazy-image-container"
    :class="{ 'is-loading': isLoading, 'is-loaded': isLoaded, 'has-error': hasError }"
  >
    <!-- Placeholder (skeleton or low-res blur) -->
    <div v-if="isLoading" class="lazy-image-placeholder">
      <v-skeleton-loader
        v-if="!src"
        type="image"
        :height="height"
        :width="width"
      />
      <div v-else class="blur-placeholder" :style="placeholderStyle" />
    </div>

    <!-- Actual Image -->
    <img
      v-show="isLoaded"
      :src="isLoaded ? src : undefined"
      :alt="alt"
      :width="width"
      :height="height"
      :class="imgClass"
      :style="imgStyle"
      @load="onLoad"
      @error="onError"
    />

    <!-- Error State -->
    <div v-if="hasError" class="lazy-image-error">
      <v-icon size="48" color="grey-lighten-1">mdi-image-broken-variant</v-icon>
      <p class="text-caption text-grey mt-2">{{ errorText }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

interface Props {
  src: string
  alt?: string
  width?: string | number
  height?: string | number
  imgClass?: string
  imgStyle?: string | Record<string, any>
  placeholderSrc?: string // Optional low-res placeholder for blur effect
  errorText?: string
  rootMargin?: string // Intersection Observer rootMargin (default: '50px')
  threshold?: number // Intersection Observer threshold (default: 0.01)
}

const props = withDefaults(defineProps<Props>(), {
  alt: '',
  width: undefined,
  height: undefined,
  imgClass: '',
  imgStyle: '',
  placeholderSrc: undefined,
  errorText: 'Gagal memuat gambar',
  rootMargin: '50px',
  threshold: 0.01,
})

const containerRef = ref<HTMLElement | null>(null)
const isLoading = ref(true)
const isLoaded = ref(false)
const hasError = ref(false)
const observer = ref<IntersectionObserver | null>(null)

const placeholderStyle = computed(() => {
  if (!props.placeholderSrc) return {}
  
  return {
    backgroundImage: `url(${props.placeholderSrc})`,
    backgroundSize: 'cover',
    backgroundPosition: 'center',
    filter: 'blur(10px)',
    transform: 'scale(1.1)',
    width: props.width ? `${props.width}px` : '100%',
    height: props.height ? `${props.height}px` : '100%',
  }
})

const onLoad = () => {
  isLoading.value = false
  isLoaded.value = true
  hasError.value = false
}

const onError = () => {
  isLoading.value = false
  isLoaded.value = false
  hasError.value = true
}

const loadImage = () => {
  if (isLoaded.value || hasError.value) return
  
  // Trigger image load by setting src
  isLoading.value = true
}

onMounted(() => {
  if (!containerRef.value) return
  
  // Check if IntersectionObserver is supported
  if ('IntersectionObserver' in window) {
    observer.value = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            loadImage()
            // Stop observing after image starts loading
            if (observer.value && containerRef.value) {
              observer.value.unobserve(containerRef.value)
            }
          }
        })
      },
      {
        rootMargin: props.rootMargin,
        threshold: props.threshold,
      }
    )
    
    observer.value.observe(containerRef.value)
  } else {
    // Fallback for browsers without IntersectionObserver
    loadImage()
  }
})

onUnmounted(() => {
  if (observer.value && containerRef.value) {
    observer.value.unobserve(containerRef.value)
    observer.value.disconnect()
  }
})

// Watch src changes (if image source changes dynamically)
watch(() => props.src, () => {
  isLoading.value = true
  isLoaded.value = false
  hasError.value = false
  loadImage()
})
</script>

<style scoped>
.lazy-image-container {
  position: relative;
  overflow: hidden;
  background-color: #f5f5f5;
}

.lazy-image-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.blur-placeholder {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transition: opacity 0.3s ease;
}

.lazy-image-container.is-loaded .blur-placeholder {
  opacity: 0;
}

.lazy-image-container img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: opacity 0.3s ease;
}

.lazy-image-error {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  min-height: 200px;
  background-color: #fafafa;
}

/* Fade-in animation when image loads */
.lazy-image-container.is-loaded img {
  animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
