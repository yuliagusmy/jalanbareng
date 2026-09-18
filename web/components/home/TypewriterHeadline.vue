<template>
  <div class="typewriter-container">
    <h1 class="hero-title text-grey-darken-4 font-weight-black">
      <span class="static-prefix d-block text-grey-darken-2">Mari Melangkah,</span>
      <span class="typewriter-text text-primary">
        {{ currentText }}<span class="blinking-cursor">|</span>
      </span>
    </h1>
    <!-- Visually hidden for screen readers so the complete context is accessible -->
    <span class="sr-only">
      Mari Melangkah, Setiap Sudut Kota Punya Cerita. Komunitas pejalan kaki Jalan Bareng.
    </span>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const phrases = [
  'Setiap Sudut Kota Punya Cerita.',
  'Menelusuri Kota Lebih Lambat.',
  'Menyapa Kawan Jalan Baru.',
  'Menemukan Lorong Tersembunyi.',
  'Merawat Ruang Pejalan Kaki.'
]

const currentText = ref('')
const currentPhraseIndex = ref(0)
const isDeleting = ref(false)
let typingTimeout: any = null

const typeLoop = () => {
  const fullText = phrases[currentPhraseIndex.value]

  if (!isDeleting.value) {
    // Typing forward
    currentText.value = fullText.substring(0, currentText.value.length + 1)

    if (currentText.value === fullText) {
      // Finished typing full word, pause before deleting
      typingTimeout = setTimeout(() => {
        isDeleting.value = true
        typeLoop()
      }, 2400)
      return
    }

    // Typing speed with slight human variance
    const speed = 65 + Math.random() * 30
    typingTimeout = setTimeout(typeLoop, speed)
  } else {
    // Deleting
    currentText.value = fullText.substring(0, currentText.value.length - 1)

    if (currentText.value === '') {
      // Finished deleting, move to next phrase
      isDeleting.value = false
      currentPhraseIndex.value = (currentPhraseIndex.value + 1) % phrases.length
      typingTimeout = setTimeout(typeLoop, 500)
      return
    }

    typingTimeout = setTimeout(typeLoop, 35)
  }
}

onMounted(() => {
  typeLoop()
})

onUnmounted(() => {
  if (typingTimeout) {
    clearTimeout(typingTimeout)
  }
})
</script>

<style scoped>
.typewriter-container {
  min-height: 120px;
}

@media (min-width: 960px) {
  .typewriter-container {
    min-height: 140px;
  }
}

.hero-title {
  font-size: clamp(2rem, 5.2vw, 3.6rem);
  line-height: 1.16;
  letter-spacing: -0.02em;
}

.static-prefix {
  font-size: 0.58em;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #6B7280;
  margin-bottom: 6px;
}

.typewriter-text {
  color: #DC2626;
  position: relative;
  display: inline-block;
  word-break: break-word;
}

.blinking-cursor {
  display: inline-block;
  font-weight: 300;
  color: #DC2626;
  animation: blink 1s infinite;
  margin-left: 2px;
}

@keyframes blink {
  0%, 49% {
    opacity: 1;
  }
  50%, 100% {
    opacity: 0;
  }
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>
