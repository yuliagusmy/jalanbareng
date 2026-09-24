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
        <div class="category-cards-wrapper">
          <!-- Loading State -->
          <div v-if="loading" class="category-scroll-container">
            <div
              v-for="n in 6"
              :key="n"
              class="category-scroll-item"
            >
              <v-skeleton-loader type="image, article" height="320"></v-skeleton-loader>
            </div>
          </div>

          <!-- Category Cards (Side-by-side with horizontal scroll) -->
          <div v-else class="category-scroll-container">
            <div
              v-for="category in categories"
              :key="category.id"
              class="category-scroll-item"
            >
              <v-card
                :to="`/destinations?category_id=${category.id}`"
                class="category-card"
                elevation="0"
              >
                <div class="category-image-wrapper">
                  <img
                    :src="category.image"
                    :alt="category.name"
                    class="category-image"
                  />
                  <div class="category-overlay"></div>
                  <div class="category-content">
                    <h3 class="category-name">{{ category.name }}</h3>
                  </div>
                </div>
              </v-card>
            </div>
          </div>
        </div>
      </v-col>
    </v-row>
  </div>
</template>

<script setup lang="ts">
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
    flex: 0 0 135px;
    width: 135px;
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
  font-size: 14px;
  font-weight: 600;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.3);
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
</style>
