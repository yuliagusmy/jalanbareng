<template>
  <section class="editorial-hero-wrapper">
    <div class="editorial-artboard">
      <!-- 1. Top Section: Centered Full-Width Headline (No side elements squeezing it) -->
      <div class="top-headline-wrapper text-center">
        <div class="headline-container position-relative d-inline-block">
          <span class="sticker sticker-sunflower" aria-hidden="true">🌻</span>
          <span class="sticker sticker-blossom" aria-hidden="true">🌸</span>
          <span class="sticker sticker-sparkle" aria-hidden="true">✦</span>

          <h1 class="main-headline font-weight-black text-grey-darken-4 mb-2">
            Setiap Sudut Kota Punya Cerita,<br>
            <span class="typewriter-line text-primary">
              {{ currentText }}<span class="cursor">|</span>
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

        <!-- THE 5 LANES COLLAGE (All 5 lanes vertically centered) -->
        <div class="cards-collage-grid">
          <!-- Lane 1: Left Outer Card (Centered) -->
          <div class="collage-lane lane-outer">
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

          <!-- Lane 5: Right Outer Card (Centered) -->
          <div class="collage-lane lane-outer">
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

      <!-- 3. Bottom Row: Aligned Quote + HIGH-CONTRAST PRIMARY CTA BUTTON + Teaser Card -->
      <div class="artboard-bottom-grid">
        <!-- Bottom Left: Community Quote -->
        <div class="bottom-left-quote">
          <div class="quote-symbol">“</div>
          <p class="quote-text text-grey-darken-3 mb-1">
            Nggak nyangka perjumpaan santai di trotoar malah bikin dapet sahabat baru seperti keluarga sendiri.
          </p>
          <div class="quote-author font-weight-bold text-primary">
            Rifky Pratama <span class="author-sub text-grey-darken-1">— Pejalan Makassar</span>
          </div>
          <span class="sticker sticker-flower-bottom" aria-hidden="true">🌸</span>
        </div>

        <!-- Bottom Center: HIGHLY HIGHLIGHTED PRIMARY ACTION BUTTON -->
        <div class="bottom-center-cta text-center">
          <button class="primary-highlighted-btn" @click="emitScroll" aria-label="Daftar aktivasi pekan ini">
            <v-icon start size="22" color="white">mdi-calendar-check</v-icon>
            <span class="btn-text">DAFTAR AKTIVASI PEKAN INI</span>
            <v-icon end size="20" color="white" class="bouncing-arrow">mdi-chevron-down</v-icon>
          </button>
          
          <div class="cta-status-badge d-inline-flex align-center ga-2 mt-2">
            <span class="pulse-green-dot"></span>
            <span class="text-caption font-weight-bold text-grey-darken-2">
              4 Aktivasi Dibuka • Klik untuk Memilih
            </span>
          </div>
        </div>

        <!-- Bottom Right: Weekly Teaser Box -->
        <div class="bottom-right-teaser" @click="emitScroll">
          <div class="weekly-teaser-card elevation-1">
            <div class="teaser-meta text-grey-darken-1 font-weight-medium">
              Pekan Ini • Pendaftaran Buka
            </div>
            <div class="d-flex align-center justify-space-between ga-3 mt-1">
              <span class="teaser-num font-weight-black">01</span>
              <span class="teaser-text font-weight-bold text-grey-darken-4">
                4 Aktivasi siap kamu ikuti akhir pekan ini
              </span>
              <div class="teaser-arrow-circle">
                <v-icon size="16" color="white">mdi-arrow-right</v-icon>
              </div>
            </div>
            <span class="sticker sticker-rose" aria-hidden="true">🥀</span>
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
  'Jalan Bareng Bergerak Bersama Warga',
  'Menghubungkan Kawan Baru di Trotoar',
  'Baca Buku & Diskusi di Ruang Terbuka',
  'Menelusuri Kota Lebih Lambat',
  'Berjalan Santai & Berbagi Cerita',
  'Ruang Bersama untuk Semua Warga'
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
  max-width: 1100px;
  margin: 0 auto 38px;
}

.headline-container {
  padding: 0 20px;
}

.main-headline {
  font-size: clamp(2.4rem, 5.8vw, 4.85rem);
  line-height: 1.08;
  letter-spacing: -0.035em;
  white-space: normal;
}

.typewriter-line {
  font-size: 0.83em;
  color: #DC2626;
  display: inline-block;
  word-break: break-word;
}

.cursor {
  display: inline-block;
  font-weight: 300;
  color: #DC2626;
  animation: blink 1s infinite;
  margin-left: 2px;
}

@keyframes blink {
  0%, 49% { opacity: 1; }
  50%, 100% { opacity: 0; }
}

.sub-headline {
  font-size: clamp(1rem, 1.6vw, 1.18rem);
  max-width: 680px;
  line-height: 1.6;
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
  background-color: #FDE047;
  border: 2px solid #FFFFFF;
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

/* Mobile Adjustments (320px - 599px): 100% Width Fit, NO horizontal scroll */
@media (max-width: 599px) {
  .editorial-hero-wrapper {
    padding: 16px 8px 28px;
  }

  .top-headline-wrapper {
    margin-bottom: 20px;
  }

  .main-headline {
    font-size: clamp(1.6rem, 5.8vw, 2.2rem);
  }

  .cards-collage-grid {
    align-items: center;
    gap: 5px;
  }

  .lane-outer, .lane-left, .lane-right {
    align-items: center;
    justify-content: center;
  }

  .lane-stacked {
    gap: 5px;
    align-items: center;
    justify-content: center;
  }

  .card-amber, .card-mint {
    height: 135px;
    border-radius: 12px;
  }

  .card-stacked-top, .card-stacked-bottom-short {
    height: 68px;
    border-radius: 10px;
  }

  .card-stacked-bottom, .card-stacked-top-tall {
    height: 92px;
    border-radius: 10px;
  }

  .card-center-hero {
    height: 195px;
    border-radius: 16px;
  }

  .lane-center {
    align-items: center;
    justify-content: center;
    margin-top: -10px;
    margin-bottom: 0;
  }

  .center-star-badge {
    top: -11px;
  }

  .center-star-badge svg {
    width: 18px;
    height: 18px;
  }

  .card-tag-bottom {
    font-size: 0.54rem;
    padding: 1px 5px;
    bottom: 4px;
  }
}

/* 4. Bottom Section Grid: Strict Alignment with Collage Columns */
.artboard-bottom-grid {
  display: grid;
  grid-template-columns: 260px 1fr 260px;
  align-items: center;
  gap: 16px;
  width: 100%;
}

@media (max-width: 959px) {
  .artboard-bottom-grid {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 18px;
    text-align: center;
  }
}

.bottom-left-quote {
  position: relative;
  text-align: left;
}

@media (max-width: 959px) {
  .bottom-left-quote {
    order: 2;
    text-align: center;
    max-width: 360px;
  }
}

.quote-symbol {
  font-family: Georgia, serif;
  font-size: 2.4rem;
  line-height: 1;
  color: #111827;
  margin-bottom: -8px;
}

.quote-text {
  font-size: 0.85rem;
  line-height: 1.42;
}

.quote-author {
  font-size: 0.82rem;
}

.author-sub {
  font-size: 0.76rem;
  font-weight: 500;
}

/* HIGHLY HIGHLIGHTED PRIMARY ACTION BUTTON */
.bottom-center-cta {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

@media (max-width: 959px) {
  .bottom-center-cta {
    order: 1;
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

  .headline-container {
    padding: 0 14px;
  }

  .main-headline {
    font-size: 1.95rem !important;
    line-height: 1.18 !important;
    letter-spacing: -0.025em !important;
  }

  .typewriter-line {
    font-size: 0.88em !important;
  }

  .sub-headline {
    font-size: 0.88rem !important;
    line-height: 1.55 !important;
    padding: 0 8px;
    margin-top: 10px;
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

  .primary-highlighted-btn {
    width: 100%;
    max-width: 320px;
    padding: 14px 20px;
    font-size: 0.9rem;
  }
}

.cta-status-badge {
  background: #F3F4F6;
  border: 1px solid #E5E7EB;
  padding: 3px 12px;
  border-radius: 14px;
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

/* Weekly Teaser Card */
.bottom-right-teaser {
  display: flex;
  justify-content: flex-end;
}

@media (max-width: 959px) {
  .bottom-right-teaser {
    order: 3;
    justify-content: center;
    width: 100%;
  }
}

.weekly-teaser-card {
  position: relative;
  background: #FFFFFF;
  border: 1px solid #E5E7EB;
  border-radius: 18px;
  padding: 12px 18px;
  max-width: 260px;
  cursor: pointer;
  transition: all 0.2s ease;
  text-align: left;
}

.weekly-teaser-card:hover {
  transform: translateY(-2px);
  border-color: #D1D5DB;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06) !important;
}

.teaser-meta {
  font-size: 0.68rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.teaser-num {
  font-size: 1.45rem;
  line-height: 1;
  color: #111827;
}

.teaser-text {
  font-size: 0.78rem;
  line-height: 1.3;
}

.teaser-arrow-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #111827;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}

.weekly-teaser-card:hover .teaser-arrow-circle {
  transform: translateX(3px);
  background: #DC2626;
}
</style>
