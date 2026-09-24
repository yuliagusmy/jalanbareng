<template>
  <section class="editorial-hero-wrapper">
    <div class="editorial-artboard">
      <!-- 1. Top Section: Centered Full-Width Headline (No side elements squeezing it) -->
      <div class="top-headline-wrapper text-center">
        <div class="headline-container position-relative mx-auto">
          <h1 class="main-headline text-grey-darken-4 mb-2 mb-md-3">
            <span class="headline-title">
              <span class="headline-lead">Setiap Sudut Kota</span>
              <span class="headline-follow"> Punya Cerita</span>
            </span>
            <span class="typewriter-line text-primary">
              <span class="typewriter-text">{{ currentText }}<span class="cursor" aria-hidden="true">|</span></span>
            </span>
          </h1>
        </div>

        <p class="sub-headline text-grey-darken-2 mx-auto mb-0">
          Gerakan ruang bersama untuk berjalan santai, merespon tata kota pejalan kaki, dan menghubungkan kawan baru di kotamu.
        </p>
      </div>

      <!-- 2. Collage Stage: Side Elements at Top Sides, All 5 Lanes Centered -->
      <div class="collage-stage position-relative">
        <!-- Tucked Left Editorial Tag: Positioned higher up at top-left -->
        <div class="tucked-left-tag d-none d-lg-block">
          <span class="category-eyebrow">#JALANBARENG</span>
          <h2 class="category-heading">
            MENJELAJAH KOTA<br>LEBIH LAMBAT & LEBIH DEKAT
          </h2>
          <span class="sticker sticker-flower-left" aria-hidden="true">🌼</span>
        </div>

        <!-- Tucked Right Rotating Stamp: #LanjutBergerak with Footprint Icon -->
        <div class="tucked-right-stamp d-none d-lg-flex" @click="emitScroll">
          <div class="circular-stamp-wrapper" title="Klik untuk lihat jadwal & daftar">
            <svg class="rotating-text-svg" viewBox="0 0 120 120">
              <path
                id="stampCirclePath"
                d="M 60, 60 m -46, 0 a 46,46 0 1,1 92,0 a 46,46 0 1,1 -92,0"
                fill="none"
              />
              <text font-size="10" font-weight="800" letter-spacing="2" fill="#111827">
                <textPath href="#stampCirclePath" startOffset="0%">
                  ✦ #MENELUSURI KOTA LEBIH LAMBAT ✦ 
                </textPath>
              </text>
            </svg>
            <div class="stamp-center-icon">
              <v-icon size="18" color="#DC2626">mdi-foot-print</v-icon>
            </div>
          </div>
        </div>

        <!-- THE 5 LANES COLLAGE (All 5 lanes vertically centered; 3 lanes on mobile, 5 lanes on tablet & desktop) -->
        <div class="cards-collage-grid">
          <!-- Lane 1: Left Outer Card (Hidden on mobile <600px, visible on tablet/desktop) -->
          <div class="collage-lane lane-outer d-none d-sm-flex">
            <div class="color-card card-amber elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[0].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[0].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
            </div>
          </div>

          <!-- Lane 2: Left Stacked Column (WIDER & Centered) -->
          <div class="collage-lane lane-stacked">
            <div class="color-card card-cream card-stacked-top elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[1].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[1].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
            </div>
            <div class="color-card card-teal card-stacked-bottom elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[2].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[2].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
            </div>
          </div>

          <!-- Lane 3: Center Hero Card (Centered & Tallest) -->
          <div class="collage-lane lane-center">
            <div class="color-card card-yellow card-center-hero elevation-3">
              <div class="card-img-wrapper">
                <img :src="cards[3].prev" class="card-img img-back" alt="Dokumentasi kawan jalan" />
                <img :src="cards[3].current" class="card-img img-front" alt="Dokumentasi kawan jalan" />
              </div>
            </div>
          </div>

          <!-- Lane 4: Right Stacked Column (WIDER & Centered) -->
          <div class="collage-lane lane-stacked">
            <div class="color-card card-coral card-stacked-top-tall elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[4].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[4].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
            </div>
            <div class="color-card card-sky card-stacked-bottom-short elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[5].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[5].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
            </div>
          </div>

          <!-- Lane 5: Right Outer Card (Hidden on mobile <600px, visible on tablet/desktop) -->
          <div class="collage-lane lane-outer d-none d-sm-flex">
            <div class="color-card card-mint elevation-1">
              <div class="card-img-wrapper">
                <img :src="cards[6].prev" class="card-img img-back" alt="Dokumentasi jalan" />
                <img :src="cards[6].current" class="card-img img-front" alt="Dokumentasi jalan" />
              </div>
              <div class="card-tag-bottom">#MenelusuriKota</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 3. Bottom Row: Centered Clean Quote (No Card) + HIGH-CONTRAST PRIMARY CTA BUTTON -->
      <div class="artboard-bottom-container">
        <!-- Primary Action Button (Centered) -->
        <div class="bottom-center-cta text-center">
          <div class="cta-interactive-group">
            <button class="primary-highlighted-btn" @click="emitScroll" aria-label="Daftar aktivasi pekan ini">
              <v-icon start size="22" color="white">mdi-calendar-check</v-icon>
              <span class="btn-text">DAFTAR AKTIVASI PEKAN INI</span>
              <v-icon end size="20" color="white" class="bouncing-arrow">mdi-chevron-down</v-icon>
            </button>
            
            <div class="cta-status-badge d-flex align-center justify-center ga-2 mt-2" @click="emitScroll">
              <span class="pulse-green-dot"></span>
              <span class="text-caption font-weight-bold text-grey-darken-2">
                4 Aktivasi Dibuka • Klik untuk Memilih
              </span>
            </div>
          </div>
        </div>

        <!-- Suara Pejalan: Clean Centered Text (Card Removed on Mobile & Laptop) -->
        <div class="bottom-quote-minimal text-center">
          <p class="quote-text-clean text-grey-darken-3 mb-1">
            <span class="quote-symbol-inline">“</span>Nggak nyangka perjumpaan santai di trotoar malah bikin dapet sahabat baru seperti keluarga sendiri.<span class="quote-symbol-inline">”</span>
          </p>
          <div class="quote-author-clean">
            <span class="font-weight-bold text-primary">Rifky Pratama</span>
            <span class="author-sub text-grey-darken-1"> · Pejalan Makassar</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const emit = defineEmits(['scroll-to-registration'])

const emitScroll = () => {
  emit('scroll-to-registration')
}

// Pool of 12 authentic community photography assets
const photoPool = [
  '/images/hero/walk_1.jpg',
  '/images/hero/walk_2.jpg',
  '/images/hero/walk_3.jpg',
  '/images/hero/walk_4.jpg',
  '/images/hero/walk_5.jpg',
  '/images/hero/walk_6.jpg',
  '/images/hero/walk_7.jpg',
  '/images/hero/walk_8.jpg',
  '/images/hero/walk_9.jpg',
  '/images/hero/walk_10.jpg',
  '/images/hero/walk_11.jpg',
  '/images/hero/walk_12.jpg'
]

// 7 cards state. Direct image swap layer without background color flash
const cards = ref([
  { id: 0, current: '/images/hero/walk_1.jpg', prev: '/images/hero/walk_1.jpg' },
  { id: 1, current: '/images/hero/walk_2.jpg', prev: '/images/hero/walk_2.jpg' },
  { id: 2, current: '/images/hero/walk_3.jpg', prev: '/images/hero/walk_3.jpg' },
  { id: 3, current: '/images/hero/walk_4.jpg', prev: '/images/hero/walk_4.jpg' }, // Center Hero
  { id: 4, current: '/images/hero/walk_5.jpg', prev: '/images/hero/walk_5.jpg' },
  { id: 5, current: '/images/hero/walk_6.jpg', prev: '/images/hero/walk_6.jpg' },
  { id: 6, current: '/images/hero/walk_7.jpg', prev: '/images/hero/walk_7.jpg' }
])

// Automatic one-by-one photo rotation logic
const cardRotationSequence = [3, 1, 5, 0, 4, 2, 6]
let sequenceIndex = 0
let autoSwapInterval: any = null

const swapOnePhoto = () => {
  const targetCardIndex = cardRotationSequence[sequenceIndex]
  sequenceIndex = (sequenceIndex + 1) % cardRotationSequence.length

  const currentActiveUrls = cards.value.map(c => c.current)
  const availablePhotos = photoPool.filter(p => !currentActiveUrls.includes(p))

  if (availablePhotos.length === 0) return

  const randomNewPhoto = availablePhotos[Math.floor(Math.random() * availablePhotos.length)]
  const targetCard = cards.value[targetCardIndex]

  targetCard.prev = targetCard.current
  targetCard.current = randomNewPhoto
}

// Typewriter Headline logic
const phrases = [
  'Jalan Bareng Bersama Warga',
  'Temukan Kawan Baru di Kotamu',
  'Baca Buku & Diskusi Terbuka',
  'Menelusuri Kota Lebih Lambat',
  'Berjalan Santai & Berbagi Cerita',
  'Ruang Bersama Semua Warga'
]

const currentText = ref('')
const currentPhraseIndex = ref(0)
const isDeleting = ref(false)
let typingTimeout: any = null

const typeLoop = () => {
  const fullText = phrases[currentPhraseIndex.value]

  if (!isDeleting.value) {
    currentText.value = fullText.substring(0, currentText.value.length + 1)
    if (currentText.value === fullText) {
      typingTimeout = setTimeout(() => {
        isDeleting.value = true
        typeLoop()
      }, 2500)
      return
    }
    const speed = 70 + Math.random() * 25
    typingTimeout = setTimeout(typeLoop, speed)
  } else {
    currentText.value = fullText.substring(0, currentText.value.length - 1)
    if (currentText.value === '') {
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
  // Preload all images in the pool for instant decoding
  if (typeof window !== 'undefined') {
    photoPool.forEach(src => {
      const img = new Image()
      img.src = src
    })
  }
  // Start swapping 1 photo every 2.4 seconds
  autoSwapInterval = setInterval(swapOnePhoto, 2400)
})

onUnmounted(() => {
  if (typingTimeout) clearTimeout(typingTimeout)
  if (autoSwapInterval) clearInterval(autoSwapInterval)
})
</script>

<style scoped>
.editorial-hero-wrapper {
  background: transparent;
  position: relative;
  overflow: visible;
  padding: 0 0 24px;
  width: 100%;
}

.editorial-artboard {
  width: 100%;
  position: relative;
}

/* 1. Full Width Top Headline Section */
.top-headline-wrapper {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto 34px;
}

.headline-container {
  padding: 0;
  width: 100%;
  max-width: 100%;
}

.main-headline {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
  margin: 0 auto;
}

.headline-title {
  display: block;
  font-size: clamp(2.4rem, 4.4vw, 5.25rem);
  font-weight: 800 !important;
  line-height: 1.08;
  letter-spacing: -0.035em !important;
  color: #111827;
}

.headline-lead,
.headline-follow {
  display: inline;
}

@media (min-width: 960px) {
  .headline-title {
    white-space: nowrap;
  }
}

.typewriter-line {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: clamp(1.85rem, 3.4vw, 3.65rem);
  font-weight: 800 !important;
  line-height: 1.15;
  letter-spacing: -0.03em !important;
  color: #DC2626;
  min-height: 1.25em;
  margin-top: 6px;
  word-break: break-word;
}

@media (min-width: 960px) {
  .typewriter-line {
    white-space: nowrap;
  }
}

.typewriter-text {
  display: inline;
}

.cursor {
  display: inline-block;
  font-weight: 300;
  color: #DC2626;
  animation: blink 1s infinite;
  margin-left: 3px;
  line-height: 1;
}

@keyframes blink {
  0%, 49% { opacity: 1; }
  50%, 100% { opacity: 0; }
}

.sub-headline {
  font-size: clamp(1rem, 1.4vw, 1.2rem);
  max-width: 720px;
  line-height: 1.6;
  margin-top: 14px;
}

/* Playful Stickers */
.sticker {
  position: absolute;
  user-select: none;
  pointer-events: none;
}

.sticker-sunflower {
  top: -16px;
  left: -28px;
  font-size: 2rem;
  animation: float-gentle 4s ease-in-out infinite;
}

.sticker-blossom {
  top: -2px;
  right: -32px;
  font-size: 1.9rem;
  animation: float-gentle 5s ease-in-out infinite alternate;
}

.sticker-sparkle {
  top: -22px;
  right: 56px;
  font-size: 1.5rem;
  color: #F59E0B;
}

.sticker-flower-left {
  top: -12px;
  right: 8px;
  font-size: 1.3rem;
}

.sticker-flower-bottom {
  bottom: -6px;
  right: -16px;
  font-size: 1.2rem;
}

.sticker-rose {
  position: absolute;
  top: -16px;
  right: 12px;
  font-size: 1.2rem;
}

@keyframes float-gentle {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-4px) rotate(4deg); }
}

/* 2. Collage Stage with Tucked Left Tag & Right Stamp */
.collage-stage {
  width: 100%;
  margin: 0 0 32px;
  position: relative;
}

/* Tucked Left Tag: Positioned higher up at the top-left */
.tucked-left-tag {
  position: absolute;
  top: -46px;
  left: 0;
  text-align: left;
  max-width: 190px;
  z-index: 5;
}

.category-eyebrow {
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 0.12em;
  color: #9CA3AF;
  display: block;
  margin-bottom: 3px;
}

.category-heading {
  font-size: clamp(0.74rem, 0.9vw, 0.84rem);
  font-weight: 900;
  line-height: 1.25;
  color: #111827;
  letter-spacing: 0.02em;
  margin: 0;
}

.sticker-flower-left {
  position: absolute;
  top: -6px;
  right: 4px;
  font-size: 1rem;
  animation: float-gentle 4s ease-in-out infinite;
}

/* Tucked Right Stamp: Positioned higher up at the top-right */
.tucked-right-stamp {
  position: absolute;
  top: -50px;
  right: 0;
  z-index: 5;
  cursor: pointer;
  transition: transform 0.25s ease;
}

.tucked-right-stamp:hover {
  transform: scale(1.08);
}

.circular-stamp-wrapper {
  position: relative;
  width: 78px;
  height: 78px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.rotating-text-svg {
  width: 100%;
  height: 100%;
  animation: rotate-stamp 18s linear infinite;
}

@keyframes rotate-stamp {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.stamp-center-icon {
  position: absolute;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  border: 1.5px solid #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #FFFFFF;
}

/* 3. Collage Grid: Vertically Centered Across All Lanes + Wider Columns 2 & 4 */
.cards-collage-grid {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  gap: 16px;
}

.collage-lane {
  display: flex;
  flex-direction: column;
}

/* Proportional flex widths: Columns 2 & 4 pulled wider */
.lane-outer {
  flex: 1.15;
}

.lane-left {
  align-items: flex-start;
  justify-content: center;
}

.lane-right {
  align-items: center;
  justify-content: center;
}

.lane-stacked {
  flex: 1.65;
  gap: 14px;
  align-items: center;
  justify-content: center;
}

.lane-center {
  flex: 1.45;
  position: relative;
  align-items: center;
  justify-content: center;
  margin-bottom: 0;
}

/* Base Card Styling */
.color-card {
  position: relative;
  width: 100%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.25s ease;
  cursor: pointer;
  border: none !important;
  outline: none !important;
}

.color-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.12) !important;
}

/* Direct Image Stacking - No background flash */
.card-img-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  border-radius: inherit;
}

.card-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.img-back {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.img-front {
  position: relative;
  z-index: 2;
  transition: opacity 0.25s ease;
}

/* Card Heights: Middle 3 Columns are LONGER (total ~348px & 415px), Outer 2 are Shorter (205px) */
.card-amber {
  height: 205px;
  border-radius: 24px;
  background-color: #FBBF24;
}

.card-stacked-top {
  height: 148px;
  border-radius: 20px;
}

.card-cream {
  background-color: #FED7AA;
}

.card-stacked-bottom {
  height: 188px;
  border-radius: 20px;
}

.card-teal {
  background-color: #0D9488;
}

.card-center-hero {
  height: 415px; /* Longest center hero! */
  border-radius: 32px;
  background-color: transparent;
  border: none !important;
}

.card-stacked-top-tall {
  height: 188px;
  border-radius: 20px;
}

.card-coral {
  background-color: #FB923C;
}

.card-stacked-bottom-short {
  height: 148px;
  border-radius: 20px;
}

.card-sky {
  background-color: #38BDF8;
}

.card-mint {
  height: 205px;
  border-radius: 24px;
  background-color: #86EFAC;
}

.card-tag-bottom {
  position: absolute;
  bottom: 8px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.68rem;
  font-weight: 800;
  color: #111827;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(4px);
  padding: 2px 8px;
  border-radius: 12px;
  white-space: nowrap;
  z-index: 3;
}

/* Tablet Adjustments (600px - 959px) */
@media (max-width: 959px) {
  .top-headline-wrapper {
    margin-bottom: 24px;
  }

  .typewriter-line {
    font-size: clamp(1.4rem, 3.8vw, 2.2rem);
    line-height: 1.25;
    min-height: 2.55em;
    max-height: 2.55em;
    height: 2.55em;
    overflow: hidden;
  }

  .cards-collage-grid {
    align-items: center;
    gap: 8px;
  }

  .lane-outer, .lane-left, .lane-right {
    align-items: center;
    justify-content: center;
  }

  .lane-stacked {
    gap: 8px;
    align-items: center;
    justify-content: center;
  }

  .lane-center {
    align-items: center;
    justify-content: center;
    margin-bottom: 0;
  }

  .card-amber, .card-mint {
    height: 165px;
    border-radius: 18px;
  }

  .card-stacked-top, .card-stacked-bottom-short {
    height: 100px;
    border-radius: 14px;
  }

  .card-stacked-bottom, .card-stacked-top-tall {
    height: 135px;
    border-radius: 14px;
  }

  .card-center-hero {
    height: 275px;
    border-radius: 24px;
  }
}

/* Mobile Adjustments (320px - 599px): Clean 3-Lane Hero Fit, NO horizontal scroll */
@media (max-width: 599px) {
  .editorial-hero-wrapper {
    padding: 8px 0 24px;
  }

  .top-headline-wrapper {
    margin-bottom: 18px;
    padding: 0 4px;
    width: 100%;
  }

  .headline-container {
    padding: 0;
    width: 100%;
  }

  .headline-title {
    display: block;
    width: 100%;
  }

  .headline-lead,
  .headline-follow {
    display: block;
    font-size: clamp(2.15rem, 9.2vw, 2.55rem) !important;
    line-height: 1.05 !important;
    letter-spacing: -0.035em !important;
    white-space: nowrap;
    text-align: center;
    width: 100%;
  }

  .main-headline {
    line-height: 1.12 !important;
    letter-spacing: -0.035em !important;
    width: 100%;
  }

  .typewriter-line {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: clamp(1.2rem, 5.2vw, 1.5rem) !important;
    line-height: 1.25 !important;
    min-height: 2.55em !important;
    max-height: 2.55em !important;
    height: 2.55em !important;
    margin-top: 6px !important;
    text-align: center !important;
    width: 100%;
    white-space: normal;
    overflow: hidden !important;
  }

  .cards-collage-grid {
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 0 4px;
  }

  .lane-stacked {
    flex: 1;
    gap: 8px;
    align-items: center;
    justify-content: center;
  }

  .lane-center {
    flex: 1.25;
    align-items: center;
    justify-content: center;
    margin-top: 0;
    margin-bottom: 0;
  }

  .card-stacked-top, .card-stacked-bottom-short {
    height: 86px;
    border-radius: 12px;
  }

  .card-stacked-bottom, .card-stacked-top-tall {
    height: 112px;
    border-radius: 12px;
  }

  .card-center-hero {
    height: 206px;
    border-radius: 18px;
  }

  .card-tag-bottom {
    font-size: 0.54rem;
    padding: 1px 5px;
    bottom: 4px;
  }
}

/* 4. Bottom Section Container: Symmetrical & Centered */
.artboard-bottom-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  width: 100%;
  max-width: 640px;
  margin: 22px auto 0;
  text-align: center;
}

.bottom-center-cta {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  width: 100%;
}

.bottom-quote-minimal {
  max-width: 520px;
  width: 100%;
  padding: 0 12px;
  text-align: center;
}

.quote-text-clean {
  font-size: 0.92rem;
  line-height: 1.5;
  font-style: italic;
  font-weight: 500;
  color: #374151;
}

.quote-symbol-inline {
  font-family: Georgia, serif;
  font-size: 1.15rem;
  color: #DC2626;
  font-weight: 700;
}

.quote-author-clean {
  font-size: 0.78rem;
  letter-spacing: 0.02em;
}

.author-sub {
  font-size: 0.76rem;
  font-weight: 500;
}

.quote-text {
  font-size: 0.8rem;
  line-height: 1.35;
}

.quote-author {
  font-size: 0.75rem;
}

.author-sub {
  font-size: 0.72rem;
  font-weight: 500;
}

/* HIGHLY HIGHLIGHTED PRIMARY ACTION BUTTON */
.bottom-center-cta {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  justify-self: center;
  width: 100%;
}

.cta-interactive-group {
  display: inline-flex;
  flex-direction: column;
  align-items: stretch;
  max-width: 100%;
}

@media (max-width: 1099px) {
  .bottom-center-cta {
    grid-area: cta;
    width: 100%;
  }
}

.primary-highlighted-btn {
  background: #DC2626;
  color: #FFFFFF;
  border: none;
  border-radius: 9999px;
  padding: 15px 36px;
  font-size: 0.96rem;
  font-weight: 900;
  letter-spacing: 0.05em;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 8px 24px -4px rgba(220, 38, 38, 0.48), 0 4px 10px rgba(0, 0, 0, 0.08);
  transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.btn-text {
  white-space: nowrap;
}

.primary-highlighted-btn:hover {
  background: #B91C1C;
  transform: translateY(-3px) scale(1.03);
  box-shadow: 0 14px 28px -4px rgba(220, 38, 38, 0.58);
}

.bouncing-arrow {
  animation: bounce-subtle 1.8s infinite;
}

@keyframes bounce-subtle {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(4px); }
}

@media (max-width: 599px) {
  .top-headline-wrapper {
    margin-bottom: 22px;
  }

  .main-headline {
    font-size: clamp(2.75rem, 10.6vw, 3.35rem) !important;
    line-height: 1.08 !important;
    letter-spacing: -0.035em !important;
  }

  .typewriter-line {
    font-size: clamp(1.2rem, 5.2vw, 1.5rem) !important;
    line-height: 1.25 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-height: 2.55em !important;
    max-height: 2.55em !important;
    height: 2.55em !important;
    margin-top: 6px !important;
    text-align: center !important;
    width: 100%;
    overflow: hidden !important;
  }

  .sub-headline {
    font-size: 0.88rem !important;
    line-height: 1.6 !important;
    padding: 0 6px;
    margin-top: 14px;
    margin-bottom: 4px;
  }

  /* Scaled-down subtle stickers on mobile */
  .sticker-sunflower {
    font-size: 1.15rem !important;
    top: -8px !important;
    left: -8px !important;
  }

  .sticker-blossom {
    font-size: 1.1rem !important;
    top: 0px !important;
    right: -8px !important;
  }

  .sticker-sparkle {
    font-size: 0.95rem !important;
    top: -12px !important;
    right: 20px !important;
  }

  .bottom-center-cta {
    width: 100%;
    padding: 0 4px;
    box-sizing: border-box;
  }

  .cta-interactive-group {
    width: 100%;
  }

  .primary-highlighted-btn {
    width: 100%;
    max-width: 100%;
    padding: 14px 16px;
    font-size: clamp(0.78rem, 3.8vw, 0.94rem);
    letter-spacing: 0.03em;
    gap: 6px;
    box-sizing: border-box;
  }

  .cta-status-badge {
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box;
    justify-content: center;
  }

  .bottom-quote-minimal {
    padding: 0 6px;
  }

  .quote-text-clean {
    font-size: 0.85rem !important;
    line-height: 1.45 !important;
  }
}

.cta-status-badge {
  background: #F3F4F6;
  border: 1px solid #E5E7EB;
  padding: 6px 14px;
  border-radius: 9999px;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

.cta-status-badge:hover {
  background: #E5E7EB;
  border-color: #D1D5DB;
}

.pulse-green-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #16A34A;
  box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
  animation: pulse-ring 1.8s infinite;
}

@keyframes pulse-ring {
  0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7); }
  70% { box-shadow: 0 0 0 6px rgba(22, 163, 74, 0); }
  100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
}
</style>
