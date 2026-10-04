<template>
  <v-container class="py-8">
    <h1 class="text-h4 font-weight-bold mb-6">Laporan & Ekspor Data Admin</h1>
    
    <v-row>
      <v-col cols="12" md="6">
        <v-card class="pa-6" rounded="xl" elevation="1">
          <h3 class="text-h6 font-weight-bold mb-2">Ekspor Data Member</h3>
          <p class="text-body-2 text-grey-darken-1 mb-4">Unduh seluruh data registrasi member komunitas dalam format CSV.</p>
          <v-btn color="#DC2626" rounded="pill" block @click="exportMembers">
            <v-icon start>mdi-download</v-icon> Ekspor CSV Member
          </v-btn>
        </v-card>
      </v-col>
      
      <v-col cols="12" md="6">
        <v-card class="pa-6" rounded="xl" elevation="1">
          <h3 class="text-h6 font-weight-bold mb-2">Ekspor Pendaftar Aktivasi</h3>
          <p class="text-body-2 text-grey-darken-1 mb-4">Unduh daftar peserta yang mendaftar ke berbagai event atau aktivasi.</p>
          <v-btn color="#DC2626" rounded="pill" block @click="exportParticipants">
            <v-icon start>mdi-download</v-icon> Ekspor CSV Pendaftar
          </v-btn>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const exportMembers = () => {
  window.open(`${apiBase}/admin/reports/members`, '_blank')
}

const exportParticipants = () => {
  window.open(`${apiBase}/admin/reports/participants`, '_blank')
}

useSeoMeta({
  title: 'Laporan Admin - Jalan Bareng',
})
</script>
