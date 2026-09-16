<template>
  <v-card elevation="2" rounded="xl" color="blue-lighten-5">
    <v-card-text class="pa-6">
      <div class="d-flex align-center mb-3">
        <v-icon color="info" size="32" class="mr-3">mdi-help-circle</v-icon>
        <h4 class="text-h6 font-weight-bold">Bantuan</h4>
      </div>
      <v-list density="compact" class="bg-transparent">
        <v-list-item prepend-icon="mdi-lightbulb-on-outline" title="Nama Jelas">
          <p class="text-caption">Gunakan nama yang jelas dan menarik</p>
        </v-list-item>
        <v-list-item prepend-icon="mdi-text-box-check-outline" title="Deskripsi Lengkap">
          <p class="text-caption">Sertakan deskripsi lengkap dan informatif</p>
        </v-list-item>
        <v-list-item prepend-icon="mdi-image-size-select-large" title="Poster Berkualitas">
          <p class="text-caption">Upload poster dengan resolusi tinggi</p>
        </v-list-item>
        <v-list-item prepend-icon="mdi-map-check-outline" title="Rute Aman (Event Walking)">
          <p class="text-caption">Pastikan rute jalan kaki aman dan dapat diakses</p>
        </v-list-item>
      </v-list>
    </v-card-text>
  </v-card>
</template>

<script setup lang="ts">
// This component has no script logic needed
</script>
