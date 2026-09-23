<template>
  <div class="admin-users-page">
    <v-container class="py-8">
      <!-- Header -->
      <v-card elevation="0" rounded="lg" class="mb-6">
        <div class="d-flex align-center justify-space-between flex-wrap ga-3 px-4 px-sm-6 py-4">
          <div class="d-flex align-center">
            <v-icon class="mr-3" size="32" color="primary">mdi-account-multiple</v-icon>
            <div>
              <h1 class="text-h6 text-sm-h5 font-weight-bold">Kelola User</h1>
              <p class="text-caption text-grey mb-0">Manage semua user dan role</p>
            </div>
          </div>
          <v-btn
            color="primary"
            variant="flat"
            rounded="lg"
            :size="$vuetify.display.xs ? 'default' : 'large'"
            @click="openAddUserDialog"
          >
            <v-icon start>mdi-plus</v-icon>
            <span v-if="!$vuetify.display.xs">Tambah User</span>
            <span v-else>Tambah</span>
          </v-btn>
        </div>
      </v-card>

      <!-- Stats Cards -->
      <v-row class="mb-6">
        <v-col cols="6" sm="6" md="3">
          <v-card elevation="0" rounded="lg" class="pa-3 pa-sm-4" color="blue-lighten-5">
            <div class="d-flex align-center">
              <v-icon :size="$vuetify.display.xs ? 28 : 40" color="blue" class="mr-3 mr-sm-4">mdi-account-multiple</v-icon>
              <div>
                <div class="text-h5 text-sm-h4 font-weight-bold text-blue">{{ stats.total }}</div>
                <div class="text-caption text-grey-darken-1">Total User</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="6" md="3">
          <v-card elevation="0" rounded="lg" class="pa-3 pa-sm-4" color="purple-lighten-5">
            <div class="d-flex align-center">
              <v-icon :size="$vuetify.display.xs ? 28 : 40" color="purple" class="mr-3 mr-sm-4">mdi-shield-crown</v-icon>
              <div>
                <div class="text-h5 text-sm-h4 font-weight-bold text-purple">{{ stats.admins }}</div>
                <div class="text-caption text-grey-darken-1">Admin</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="6" md="3">
          <v-card elevation="0" rounded="lg" class="pa-3 pa-sm-4" color="green-lighten-5">
            <div class="d-flex align-center">
              <v-icon :size="$vuetify.display.xs ? 28 : 40" color="green" class="mr-3 mr-sm-4">mdi-account-star</v-icon>
              <div>
                <div class="text-h5 text-sm-h4 font-weight-bold text-green">{{ stats.communityAdmins }}</div>
                <div class="text-caption text-grey-darken-1">Comm. Admin</div>
              </div>
            </div>
          </v-card>
        </v-col>
        <v-col cols="6" sm="6" md="3">
          <v-card elevation="0" rounded="lg" class="pa-3 pa-sm-4" color="orange-lighten-5">
            <div class="d-flex align-center">
              <v-icon :size="$vuetify.display.xs ? 28 : 40" color="orange" class="mr-3 mr-sm-4">mdi-account</v-icon>
              <div>
                <div class="text-h5 text-sm-h4 font-weight-bold text-orange">{{ stats.members }}</div>
                <div class="text-caption text-grey-darken-1">Member</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Filter & Search -->
      <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
        <v-row>
          <v-col cols="12" sm="6" md="5">
            <v-text-field
              v-model="search"
              placeholder="Cari user..."
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              clearable
              hide-details
              @update:model-value="fetchUsers"
            ></v-text-field>
          </v-col>
          <v-col cols="12" sm="6" md="3">
            <v-select
              v-model="filterRole"
              :items="roleOptions"
              item-title="label"
              item-value="value"
              placeholder="Semua Role"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              clearable
              hide-details
              @update:model-value="fetchUsers"
            >
              <template v-slot:prepend-inner>
                <v-icon>mdi-filter</v-icon>
              </template>
            </v-select>
          </v-col>
          <v-col cols="12" sm="12" md="4">
            <v-select
              v-model="sortBy"
              :items="sortOptions"
              item-title="label"
              item-value="value"
              variant="outlined"
              rounded="lg"
              density="comfortable"
              hide-details
              @update:model-value="fetchUsers"
            >
              <template v-slot:prepend-inner>
                <v-icon>mdi-sort</v-icon>
              </template>
            </v-select>
          </v-col>
        </v-row>
      </v-card>

      <!-- Users Table -->
      <v-card elevation="0" rounded="lg" style="overflow-x: auto;">
        <v-data-table
          :headers="headers"
          :items="users"
          :loading="loading"
          :items-per-page="pagination.per_page"
          hide-default-footer
          class="elevation-0"
          style="min-width: 640px;"
        >
          <template v-slot:item.photo="{ item }">
            <v-avatar size="60" class="my-2">
              <v-img v-if="item.photo" :src="getImageUrl(item.photo)" :alt="item.name"></v-img>
              <v-icon v-else size="large" color="grey">mdi-account-circle</v-icon>
            </v-avatar>
          </template>

          <template v-slot:item.name="{ item }">
            <div>
              <div class="font-weight-bold">{{ item.name }}</div>
              <div class="text-caption text-grey">
                <v-icon size="x-small">mdi-email</v-icon>
                {{ item.email }}
              </div>
            </div>
          </template>

          <template v-slot:item.role="{ item }">
            <div>
              <v-chip
                :color="getRoleColor(item.role)"
                size="small"
                variant="tonal"
              >
                <v-icon start size="small">{{ getRoleIcon(item.role) }}</v-icon>
                {{ getRoleLabel(item.role) }}
              </v-chip>
              <v-chip
                v-if="item.ban_status"
                :color="getBanColor(item.ban_status)"
                size="x-small"
                variant="flat"
                class="mt-1"
              >
                <v-icon start size="x-small">{{ getBanIcon(item.ban_status) }}</v-icon>
                {{ getBanLabel(item.ban_status) }}
              </v-chip>
            </div>
          </template>

          <template v-slot:item.stats="{ item }">
            <div class="text-caption">
              <div><v-icon size="x-small" color="primary">mdi-map-marker</v-icon> {{ item.destinations_count || 0 }} destinasi</div>
              <div><v-icon size="x-small" color="blue">mdi-comment</v-icon> {{ item.comments_count || 0 }} komentar</div>
            </div>
          </template>

          <template v-slot:item.created_at="{ item }">
            <div class="text-caption">{{ formatDate(item.created_at) }}</div>
          </template>

          <template v-slot:item.actions="{ item }">
            <div class="d-flex ga-1">
              <v-btn
                icon
                size="small"
                variant="text"
                color="primary"
                @click="viewUser(item)"
              >
                <v-icon size="small">mdi-eye</v-icon>
                <v-tooltip activator="parent" location="top">Lihat Detail</v-tooltip>
              </v-btn>
              <v-btn
                icon
                size="small"
                variant="text"
                color="warning"
                @click="editUser(item)"
              >
                <v-icon size="small">mdi-pencil</v-icon>
                <v-tooltip activator="parent" location="top">Edit Role</v-tooltip>
              </v-btn>
              <!--
              <v-btn
                v-if="!item.ban_status"
                icon
                size="small"
                variant="text"
                color="orange"
                @click="openBanDialog(item)"
                :disabled="item.id === authStore.user?.id"
              >
                <v-icon size="small">mdi-gavel</v-icon>
                <v-tooltip activator="parent" location="top">Ban User</v-tooltip>
              </v-btn>
              <v-btn
                v-else
                icon
                size="small"
                variant="text"
                color="success"
                @click="unbanUser(item)"
              >
                <v-icon size="small">mdi-check-circle</v-icon>
                <v-tooltip activator="parent" location="top">Unban User</v-tooltip>
              </v-btn>
              -->
              <v-btn
                icon
                size="small"
                variant="text"
                color="error"
                @click="confirmDelete(item)"
                :disabled="item.id === authStore.user?.id"
              >
                <v-icon size="small">mdi-delete</v-icon>
                <v-tooltip activator="parent" location="top">
                  {{ item.id === authStore.user?.id ? 'Tidak bisa hapus diri sendiri' : 'Hapus' }}
                </v-tooltip>
              </v-btn>
            </div>
          </template>

          <template v-slot:loading>
            <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
          </template>

          <template v-slot:no-data>
            <div class="text-center py-8">
              <v-icon size="64" color="grey-lighten-1">mdi-account-off</v-icon>
              <p class="text-body-1 text-grey mt-4">Tidak ada user ditemukan</p>
            </div>
          </template>
        </v-data-table>

        <!-- Pagination -->
        <v-divider></v-divider>
        <div class="pa-4 d-flex align-center">
          <div class="text-caption text-grey">
            Menampilkan {{ users.length }} dari {{ pagination.total }} user
          </div>
          <v-spacer></v-spacer>
          <v-pagination
            v-model="pagination.current_page"
            :length="Math.ceil(pagination.total / pagination.per_page)"
            :total-visible="5"
            density="comfortable"
            @update:model-value="fetchUsers"
          ></v-pagination>
        </div>
      </v-card>
    </v-container>

    <!-- Edit User Dialog -->
    <v-dialog v-model="editDialog" max-width="500">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Edit Role User
        </v-card-title>
        <v-card-text class="px-6">
          <div class="mb-4">
            <div class="d-flex align-center mb-4">
              <v-avatar size="60" class="mr-4">
                <v-img v-if="userToEdit?.photo" :src="getImageUrl(userToEdit.photo)"></v-img>
                <v-icon v-else size="large" color="grey">mdi-account-circle</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-bold">{{ userToEdit?.name }}</div>
                <div class="text-caption text-grey">{{ userToEdit?.email }}</div>
              </div>
            </div>
          </div>

          <v-select
            v-model="editedRole"
            :items="roleOptions"
            item-title="label"
            item-value="value"
            label="Role"
            variant="outlined"
            rounded="lg"
            density="comfortable"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-shield-account</v-icon>
            </template>
          </v-select>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="editDialog = false">Batal</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :loading="updating"
            @click="updateUserRole"
          >
            Simpan
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- View User Dialog -->
    <v-dialog v-model="viewDialog" max-width="600">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6 d-flex align-center">
          <v-icon class="mr-3" color="primary">mdi-account-details</v-icon>
          Detail User
        </v-card-title>
        <v-card-text class="px-6" v-if="selectedUser">
          <div class="text-center mb-6">
            <v-avatar size="120" class="mb-4">
              <v-img v-if="selectedUser.photo" :src="getImageUrl(selectedUser.photo)"></v-img>
              <v-icon v-else size="80" color="grey">mdi-account-circle</v-icon>
            </v-avatar>
            <h3 class="text-h5 font-weight-bold">{{ selectedUser.name }}</h3>
            <p class="text-body-2 text-grey">{{ selectedUser.email }}</p>
            <v-chip
              :color="getRoleColor(selectedUser.role)"
              class="mt-2"
              variant="tonal"
            >
              <v-icon start size="small">{{ getRoleIcon(selectedUser.role) }}</v-icon>
              {{ getRoleLabel(selectedUser.role) }}
            </v-chip>
          </div>

          <v-divider class="my-4"></v-divider>

          <v-row>
            <v-col cols="6">
              <div class="text-caption text-grey">Nomor Telepon</div>
              <div class="font-weight-medium">{{ selectedUser.phone || '-' }}</div>
            </v-col>
            <v-col cols="6">
              <div class="text-caption text-grey">Bergabung</div>
              <div class="font-weight-medium">{{ formatDate(selectedUser.created_at) }}</div>
            </v-col>
          </v-row>

          <v-divider class="my-4"></v-divider>

          <div class="mb-3">
            <div class="text-caption text-grey mb-2">Social Media</div>
            <div class="d-flex flex-wrap ga-2">
              <v-chip
                v-if="selectedUser.instagram"
                size="small"
                color="pink"
                :href="`https://instagram.com/${selectedUser.instagram}`"
                target="_blank"
              >
                <v-icon start>mdi-instagram</v-icon>
                {{ selectedUser.instagram }}
              </v-chip>
              <v-chip
                v-if="selectedUser.facebook"
                size="small"
                color="blue"
                :href="`https://facebook.com/${selectedUser.facebook}`"
                target="_blank"
              >
                <v-icon start>mdi-facebook</v-icon>
                {{ selectedUser.facebook }}
              </v-chip>
              <v-chip
                v-if="selectedUser.twitter"
                size="small"
                color="light-blue"
                :href="`https://twitter.com/${selectedUser.twitter}`"
                target="_blank"
              >
                <v-icon start>mdi-twitter</v-icon>
                {{ selectedUser.twitter }}
              </v-chip>
              <div v-if="!selectedUser.instagram && !selectedUser.facebook && !selectedUser.twitter" class="text-grey">
                Tidak ada social media
              </div>
            </div>
          </div>

          <v-divider class="my-4"></v-divider>

          <v-row>
            <v-col cols="4">
              <div class="text-center">
                <div class="text-h5 font-weight-bold text-primary">{{ selectedUser.destinations_count || 0 }}</div>
                <div class="text-caption text-grey">Destinasi</div>
              </div>
            </v-col>
            <v-col cols="4">
              <div class="text-center">
                <div class="text-h5 font-weight-bold text-blue">{{ selectedUser.comments_count || 0 }}</div>
                <div class="text-caption text-grey">Komentar</div>
              </div>
            </v-col>
            <v-col cols="4">
              <div class="text-center">
                <div class="text-h5 font-weight-bold text-red">{{ selectedUser.likes_count || 0 }}</div>
                <div class="text-caption text-grey">Likes</div>
              </div>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="viewDialog = false">Tutup</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Add User Dialog -->
    <v-dialog v-model="addDialog" max-width="500">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Tambah User Baru
        </v-card-title>
        <v-card-text class="px-6">
          <v-text-field
            v-model="newUser.name"
            label="Nama Lengkap"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-3"
            :error-messages="errors.name"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-account</v-icon>
            </template>
          </v-text-field>

          <v-text-field
            v-model="newUser.email"
            label="Email"
            type="email"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-3"
            :error-messages="errors.email"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-email</v-icon>
            </template>
          </v-text-field>

          <v-text-field
            v-model="newUser.password"
            label="Password"
            type="password"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-3"
            :error-messages="errors.password"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-lock</v-icon>
            </template>
          </v-text-field>

          <v-select
            v-model="newUser.role"
            :items="roleOptions"
            item-title="label"
            item-value="value"
            label="Role"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            :error-messages="errors.role"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-shield-account</v-icon>
            </template>
          </v-select>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="closeAddDialog">Batal</v-btn>
          <v-btn
            color="primary"
            variant="flat"
            :loading="creating"
            @click="createUser"
          >
            Tambah User
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Ban User Dialog --><!--
    <v-dialog v-model="banDialog" max-width="600">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6 d-flex align-center">
          <v-icon class="mr-3" color="warning">mdi-gavel</v-icon>
          Ban User
        </v-card-title>
        <v-card-text class="px-6" v-if="userToBan">
          <div class="mb-4">
            <div class="d-flex align-center mb-4">
              <v-avatar size="60" class="mr-4">
                <v-img v-if="userToBan.photo" :src="getImageUrl(userToBan.photo)"></v-img>
                <v-icon v-else size="large" color="grey">mdi-account-circle</v-icon>
              </v-avatar>
              <div>
                <div class="font-weight-bold">{{ userToBan.name }}</div>
                <div class="text-caption text-grey">{{ userToBan.email }}</div>
              </div>
            </div>
          </div>

          <v-select
            v-model="banData.ban_status"
            :items="banLevels"
            item-title="label"
            item-value="value"
            label="Level Ban"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-3"
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-shield-alert</v-icon>
            </template>
            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props">
                <template v-slot:prepend>
                  <v-icon :color="item.raw.color">{{ item.raw.icon }}</v-icon>
                </template>
                <v-list-item-title>{{ item.raw.label }}</v-list-item-title>
                <v-list-item-subtitle>{{ item.raw.description }}</v-list-item-subtitle>
              </v-list-item>
            </template>
          </v-select>

          <v-text-field
            v-if="banData.ban_status === 'suspend'"
            v-model="banData.ban_until"
            label="Suspend Sampai"
            type="datetime-local"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            class="mb-3"
            hint="Pilih tanggal dan waktu berakhirnya suspend"
            persistent-hint
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-calendar-clock</v-icon>
            </template>
          </v-text-field>

          <v-textarea
            v-model="banData.ban_reason"
            label="Alasan Ban"
            variant="outlined"
            rounded="lg"
            density="comfortable"
            rows="3"
            hint="Jelaskan alasan mengapa user ini di-ban"
            persistent-hint
          >
            <template v-slot:prepend-inner>
              <v-icon>mdi-text</v-icon>
            </template>
          </v-textarea>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="closeBanDialog">Batal</v-btn>
          <v-btn
            color="warning"
            variant="flat"
            :loading="banning"
            @click="banUser"
          >
            Ban User
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    -->

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card rounded="xl">
        <v-card-title class="text-h6 font-weight-bold pa-6">
          Hapus User?
        </v-card-title>
        <v-card-text class="px-6">
          <p class="text-body-1">
            Anda yakin ingin menghapus user "<strong>{{ userToDelete?.name }}</strong>"?
            Semua destinasi dan komentar user ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.
          </p>
        </v-card-text>
        <v-card-actions class="pa-6 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
          <v-btn
            color="error"
            variant="flat"
            :loading="deleting"
            @click="deleteUser"
          >
            Hapus
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useApi } from '~/composables/useApi'
import { useRuntimeConfig } from '#app'
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: ['auth', 'admin'],
  layout: 'solid'
})

const { api } = useApi()
const config = useRuntimeConfig()
const authStore = useAuthStore()

const loading = ref(false)
const updating = ref(false)
const deleting = ref(false)
const creating = ref(false)
// const banning = ref(false)
const users = ref([])
const search = ref('')
const sortBy = ref('newest')
const filterRole = ref(null)
const editDialog = ref(false)
const viewDialog = ref(false)
const addDialog = ref(false)
const deleteDialog = ref(false)
// const banDialog = ref(false)
const userToEdit = ref(null)
const userToDelete = ref(null)
// const userToBan = ref(null)
const selectedUser = ref(null)
const editedRole = ref('')

/*
const banData = ref({
  ban_status: 'warning',
  ban_reason: '',
  ban_until: ''
})
*/

const newUser = ref({
  name: '',
  email: '',
  password: '',
  role: 'member'
})

const errors = ref({
  name: [],
  email: [],
  password: [],
  role: []
})

const pagination = ref({
  current_page: 1,
  per_page: 10,
  total: 0
})

const stats = computed(() => {
  const admins = users.value.filter(u => u.role === 'admin').length
  const communityAdmins = users.value.filter(u => u.role === 'community_admin').length
  const members = users.value.filter(u => u.role === 'member').length

  return {
    total: pagination.value.total,
    admins,
    communityAdmins,
    members
  }
})

const headers = [
  { title: 'Foto', key: 'photo', sortable: false, width: '80px' },
  { title: 'Nama', key: 'name', sortable: true },
  { title: 'Role', key: 'role', sortable: false },
  { title: 'Aktivitas', key: 'stats', sortable: false },
  { title: 'Bergabung', key: 'created_at', sortable: true },
  { title: 'Aksi', key: 'actions', sortable: false, align: 'center', width: '180px' }
]

const roleOptions = [
  { label: 'Member', value: 'member' },
  { label: 'Community Admin', value: 'community_admin' },
  { label: 'Admin', value: 'admin' }
]

const sortOptions = [
  { label: 'Terbaru', value: 'newest' },
  { label: 'Terlama', value: 'oldest' },
  { label: 'Nama A-Z', value: 'name_asc' },
  { label: 'Nama Z-A', value: 'name_desc' }
]

/*
const banLevels = [
  {
    label: 'Warning',
    value: 'warning',
    color: 'yellow-darken-2',
    icon: 'mdi-alert',
    description: 'Peringatan - User masih bisa akses semua fitur dengan notifikasi'
  },
  {
    label: 'Restrict',
    value: 'restrict',
    color: 'orange',
    icon: 'mdi-lock',
    description: 'Dibatasi - Tidak bisa post destinasi/comment, hanya view'
  },
  {
    label: 'Suspend',
    value: 'suspend',
    color: 'deep-orange',
    icon: 'mdi-clock-alert',
    description: 'Suspend Sementara - Tidak bisa login untuk periode tertentu'
  },
  {
    label: 'Permanent Ban',
    value: 'permanent',
    color: 'red',
    icon: 'mdi-cancel',
    description: 'Ban Permanen - Tidak bisa login sama sekali'
  }
]
*/

const getImageUrl = (path: string) => {
  if (!path) return ''
  // Check if it's already a full URL (from Google, Facebook, etc.)
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path
  }
  // Local storage path
  return `${config.public.apiUrl.replace('/api', '')}/storage/${path}`
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getRoleColor = (role: string) => {
  const colors = {
    admin: 'purple',
    community_admin: 'green',
    member: 'blue'
  }
  return colors[role] || 'grey'
}

const getRoleIcon = (role: string) => {
  const icons = {
    admin: 'mdi-shield-crown',
    community_admin: 'mdi-account-star',
    member: 'mdi-account'
  }
  return icons[role] || 'mdi-account'
}

const getRoleLabel = (role: string) => {
  const labels = {
    admin: 'Admin',
    community_admin: 'Community Admin',
    member: 'Member'
  }
  return labels[role] || role
}

const getBanColor = (banStatus: string) => {
  // const level = banLevels.find(l => l.value === banStatus)
  // return level?.color || 'grey'
  return 'grey'
}

const getBanIcon = (banStatus: string) => {
  // const level = banLevels.find(l => l.value === banStatus)
  // return level?.icon || 'mdi-cancel'
  return 'mdi-cancel'
}

const getBanLabel = (banStatus: string) => {
  // const level = banLevels.find(l => l.value === banStatus)
  // return level?.label || banStatus
  return banStatus
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const params: any = {
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
      admin_view: true // Flag untuk admin
    }

    if (search.value) {
      params.search = search.value
    }

    if (filterRole.value) {
      params.role = filterRole.value
    }

    if (sortBy.value) {
      params.sort = sortBy.value
    }

    const response = await api.get('/users', { params })
    users.value = response.data.data || response.data.users || []

    pagination.value = {
      current_page: response.data.current_page || 1,
      per_page: response.data.per_page || 10,
      total: response.data.total || users.value.length
    }
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const viewUser = (user: any) => {
  selectedUser.value = user
  viewDialog.value = true
}

const editUser = (user: any) => {
  userToEdit.value = user
  editedRole.value = user.role
  editDialog.value = true
}

const updateUserRole = async () => {
  if (!userToEdit.value) return

  updating.value = true
  try {
    await api.put(`/users/${userToEdit.value.id}`, {
      role: editedRole.value
    })

    // Update local data
    const index = users.value.findIndex(u => u.id === userToEdit.value.id)
    if (index !== -1) {
      users.value[index].role = editedRole.value
    }

    editDialog.value = false
    userToEdit.value = null
  } catch (error: any) {
    console.error('Error updating user role:', error)
    alert(error.response?.data?.message || 'Gagal mengupdate role user')
  } finally {
    updating.value = false
  }
}

const openAddUserDialog = () => {
  addDialog.value = true
}

const closeAddDialog = () => {
  addDialog.value = false
  newUser.value = {
    name: '',
    email: '',
    password: '',
    role: 'member'
  }
  errors.value = {
    name: [],
    email: [],
    password: [],
    role: []
  }
}

const createUser = async () => {
  errors.value = {
    name: [],
    email: [],
    password: [],
    role: []
  }

  creating.value = true
  try {
    const response = await api.post('/users', newUser.value)

    // Refresh users list
    await fetchUsers()

    closeAddDialog()
  } catch (error: any) {
    console.error('Error creating user:', error)
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      alert(error.response?.data?.message || 'Gagal menambah user')
    }
  } finally {
    creating.value = false
  }
}

const confirmDelete = (user: any) => {
  userToDelete.value = user
  deleteDialog.value = true
}

const deleteUser = async () => {
  if (!userToDelete.value) return

  deleting.value = true
  try {
    await api.delete(`/users/${userToDelete.value.id}`)

    deleteDialog.value = false
    userToDelete.value = null

    // Refresh users list
    await fetchUsers()
  } catch (error: any) {
    console.error('Error deleting user:', error)
    alert(error.response?.data?.message || 'Gagal menghapus user')
  } finally {
    deleting.value = false
  }
}

/*
const openBanDialog = (user: any) => {
  userToBan.value = user
  banData.value = {
    ban_status: 'warning',
    ban_reason: '',
    ban_until: ''
  }
  banDialog.value = true
}

const closeBanDialog = () => {
  banDialog.value = false
  userToBan.value = null
  banData.value = {
    ban_status: 'warning',
    ban_reason: '',
    ban_until: ''
  }
}

const banUser = async () => {
  if (!userToBan.value) return

  if (!banData.value.ban_reason) {
    alert('Alasan ban harus diisi')
    return
  }

  if (banData.value.ban_status === 'suspend' && !banData.value.ban_until) {
    alert('Tanggal akhir suspend harus diisi')
    return
  }

  banning.value = true
  try {
    await api.post(`/users/${userToBan.value.id}/ban`, banData.value)

    closeBanDialog()

    // Refresh users list
    await fetchUsers()
  } catch (error: any) {
    console.error('Error banning user:', error)
    alert(error.response?.data?.message || 'Gagal mem-ban user')
  } finally {
    banning.value = false
  }
}

const unbanUser = async (user: any) => {
  if (!confirm(`Yakin ingin mencabut ban dari user "${user.name}"?`)) {
    return
  }

  try {
    await api.post(`/users/${user.id}/unban`)

    // Refresh users list
    await fetchUsers()
  } catch (error: any) {
    console.error('Error unbanning user:', error)
    alert(error.response?.data?.message || 'Gagal mencabut ban user')
  }
}
*/

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.admin-users-page {
  background: #fafafa;
  min-height: 100vh;
}
</style>
