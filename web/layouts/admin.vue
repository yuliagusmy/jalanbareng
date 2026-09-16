<template>
  <v-app>
    <!-- Top App Bar -->
    <v-app-bar
      elevation="0"
      height="70"
      color="white"
      class="admin-header"
    >
      <v-app-bar-nav-icon
        @click="drawer = !drawer"
        class="ml-2"
      ></v-app-bar-nav-icon>

      <v-toolbar-title class="ml-2">
        <span class="text-h6 font-weight-bold text-primary">
          Admin Panel
        </span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <NotificationBell class="mr-2" />

      <!-- User Menu -->
      <v-menu offset-y>
        <template v-slot:activator="{ props }">
          <v-btn
            icon
            v-bind="props"
            class="mr-2"
          >
            <v-avatar size="40">
              <v-img
                v-if="authStore.user?.photo"
                :src="authStore.user.photo"
                :alt="authStore.user.name"
              ></v-img>
              <v-icon v-else>mdi-account-circle</v-icon>
            </v-avatar>
          </v-btn>
        </template>
        <v-list min-width="250" rounded="xl" class="py-2">
          <v-list-item class="px-4 py-3">
            <template v-slot:prepend>
              <v-avatar size="48">
                <v-img
                  v-if="authStore.user?.photo"
                  :src="authStore.user.photo"
                ></v-img>
                <v-icon v-else size="large">mdi-account-circle</v-icon>
              </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold">{{ authStore.user?.name }}</v-list-item-title>
            <v-list-item-subtitle class="text-caption">{{ authStore.user?.email }}</v-list-item-subtitle>
          </v-list-item>

          <v-divider class="my-2"></v-divider>

          <v-list-item to="/" rounded="lg" class="mx-2">
            <template v-slot:prepend>
              <v-icon>mdi-home</v-icon>
            </template>
            <v-list-item-title>Kembali ke Beranda</v-list-item-title>
          </v-list-item>

          <v-list-item to="/profile" rounded="lg" class="mx-2">
            <template v-slot:prepend>
              <v-icon>mdi-account</v-icon>
            </template>
            <v-list-item-title>Profil Saya</v-list-item-title>
          </v-list-item>

          <v-divider class="my-2"></v-divider>

          <v-list-item @click="handleLogout" rounded="lg" class="mx-2">
            <template v-slot:prepend>
              <v-icon color="error">mdi-logout</v-icon>
            </template>
            <v-list-item-title class="text-error">Keluar</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>

    <!-- Sidebar Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      :permanent="$vuetify.display.mdAndUp"
      app
      width="280"
      class="admin-drawer"
    >
      <!-- Sidebar Header -->
      <div class="pa-6 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <v-icon size="48" color="white" class="mb-3">mdi-shield-crown</v-icon>
        <h3 class="text-h6 font-weight-bold text-white mb-1">
          {{ authStore.isAdmin ? 'Administrator' : 'Community Admin' }}
        </h3>
        <p class="text-caption text-white mb-0" style="opacity: 0.9;">
          Panel Manajemen
        </p>
      </div>

      <v-divider></v-divider>

      <!-- Navigation Menu -->
      <v-list nav class="pa-4">
        <v-list-subheader class="text-caption font-weight-bold text-grey mb-2">
          MANAJEMEN AKTIVASI
        </v-list-subheader>

        <v-list-item
          to="/manage/activations"
          rounded="lg"
          class="mb-2"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>mdi-star-four-points</v-icon>
          </template>
          <v-list-item-title>Kelola Aktivasi</v-list-item-title>
        </v-list-item>

        <v-divider class="my-4"></v-divider>

        <v-list-subheader class="text-caption font-weight-bold text-grey mb-2">
          MANAJEMEN EVENT
        </v-list-subheader>

        <v-list-item
          to="/manage/events"
          rounded="lg"
          class="mb-2"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>mdi-calendar-edit</v-icon>
          </template>
          <v-list-item-title>Kelola Event</v-list-item-title>
        </v-list-item>

        <v-list-item
          to="/events/create"
          rounded="lg"
          class="mb-2"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>mdi-calendar-plus</v-icon>
          </template>
          <v-list-item-title>Buat Event Baru</v-list-item-title>
        </v-list-item>

        <v-divider class="my-4"></v-divider>

        <v-list-subheader class="text-caption font-weight-bold text-grey mb-2">
          MANAJEMEN DESTINASI
        </v-list-subheader>

        <v-list-item
          to="/manage/destinations"
          rounded="lg"
          class="mb-2"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>mdi-map-marker-multiple</v-icon>
          </template>
          <v-list-item-title>Kelola Destinasi</v-list-item-title>
        </v-list-item>

        <v-list-item
          to="/destinations/create"
          rounded="lg"
          class="mb-2"
          color="primary"
        >
          <template v-slot:prepend>
            <v-icon>mdi-map-marker-plus</v-icon>
          </template>
          <v-list-item-title>Tambah Destinasi</v-list-item-title>
        </v-list-item>

        <template v-if="authStore.isAdmin">
          <v-divider class="my-4"></v-divider>

          <v-list-subheader class="text-caption font-weight-bold text-grey mb-2">
            ADMIN
          </v-list-subheader>

          <v-list-item
            to="/manage/users"
            rounded="lg"
            class="mb-2"
            color="primary"
          >
            <template v-slot:prepend>
              <v-icon>mdi-account-group</v-icon>
            </template>
            <v-list-item-title>Kelola User</v-list-item-title>
          </v-list-item>

          <v-list-item
            to="/manage/categories"
            rounded="lg"
            class="mb-2"
            color="primary"
          >
            <template v-slot:prepend>
              <v-icon>mdi-tag-multiple</v-icon>
            </template>
            <v-list-item-title>Kelola Kategori</v-list-item-title>
          </v-list-item>

          <v-list-item
            to="/manage/stories"
            rounded="lg"
            class="mb-2"
            color="primary"
          >
            <template v-slot:prepend>
              <v-icon>mdi-feather</v-icon>
            </template>
            <v-list-item-title>Kurasi Tulisan</v-list-item-title>
          </v-list-item>
        </template>
      </v-list>

      <template v-slot:append>
        <div class="pa-4">
          <v-btn
            to="/"
            color="grey-lighten-2"
            block
            prepend-icon="mdi-arrow-left"
            rounded="lg"
          >
            Kembali ke Website
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <!-- Main Content -->
    <v-main class="admin-main">
      <slot />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
const authStore = useAuthStore()
const router = useRouter()
const drawer = ref(true)

const handleLogout = async () => {
  await authStore.logout()
}

// Check if user has admin/community admin access
onMounted(() => {
  if (!authStore.isAdmin && !authStore.isCommunityAdmin) {
    router.push('/')
  }
})
</script>

<style scoped>
.admin-header {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.admin-drawer {
  border-right: 1px solid rgba(0, 0, 0, 0.12);
}

.admin-main {
  background-color: #f5f5f5;
    padding-top: 100px !important;
}

/* Active menu item styling */
.v-list-item--active {
  background-color: rgba(103, 58, 183, 0.08);
}
</style>
