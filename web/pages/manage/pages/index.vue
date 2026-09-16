<template>
    <div class="admin-pages-page">
        <v-container class="py-8">
            <!-- Header -->
            <v-card elevation="0" rounded="lg" class="mb-6">
                <v-card-title class="d-flex align-center px-6 py-4">
                    <v-icon class="mr-3" size="32" color="primary">mdi-file-document-multiple</v-icon>
                    <div>
                        <h1 class="text-h5 font-weight-bold">Kelola Halaman</h1>
                        <p class="text-caption text-grey mb-0">Manage halaman statis website</p>
                    </div>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" variant="flat" rounded="lg" to="/manage/pages/create" size="large">
                        <v-icon start>mdi-plus</v-icon>
                        Tambah Halaman
                    </v-btn>
                </v-card-title>
            </v-card>

            <!-- Filter & Search -->
            <v-card elevation="0" rounded="lg" class="mb-6 pa-4">
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field v-model="search" placeholder="Cari halaman..." prepend-inner-icon="mdi-magnify"
                            variant="outlined" rounded="lg" density="comfortable" clearable hide-details
                            @update:model-value="fetchPages"></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="filterPublished" :items="publishedOptions" placeholder="Semua Status"
                            variant="outlined" rounded="lg" density="comfortable" clearable hide-details
                            @update:model-value="fetchPages">
                            <template v-slot:prepend-inner>
                                <v-icon>mdi-filter</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select v-model="sortBy" :items="sortOptions" item-title="label" item-value="value"
                            variant="outlined" rounded="lg" density="comfortable" hide-details
                            @update:model-value="fetchPages">
                            <template v-slot:prepend-inner>
                                <v-icon>mdi-sort</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </v-row>
            </v-card>

            <!-- Pages Table -->
            <v-card elevation="0" rounded="lg">
                <v-data-table :headers="headers" :items="pages" :loading="loading" :items-per-page="pagination.per_page"
                    hide-default-footer class="elevation-0">
                    <template v-slot:item.title="{ item }">
                        <div>
                            <div class="font-weight-bold">{{ item.title }}</div>
                            <div class="text-caption text-grey">/pages/{{ item.slug }}</div>
                        </div>
                    </template>

                    <template v-slot:item.is_published="{ item }">
                        <v-chip :color="item.is_published ? 'success' : 'grey'" size="small">
                            {{ item.is_published ? 'Published' : 'Draft' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.order="{ item }">
                        <v-chip size="small" variant="tonal">
                            {{ item.order }}
                        </v-chip>
                    </template>

                    <template v-slot:item.updated_at="{ item }">
                        <div class="text-caption">{{ formatDate(item.updated_at) }}</div>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex ga-1">
                            <v-btn icon size="small" variant="text" color="primary" :to="`/pages/${item.slug}`"
                                target="_blank">
                                <v-icon size="small">mdi-eye</v-icon>
                                <v-tooltip activator="parent" location="top">Lihat Halaman</v-tooltip>
                            </v-btn>
                            <v-btn icon size="small" variant="text" color="warning" @click="editPage(item.id)">
                                <v-icon size="small">mdi-pencil</v-icon>
                                <v-tooltip activator="parent" location="top">Edit</v-tooltip>
                            </v-btn>
                            <v-btn icon size="small" variant="text" color="error" @click="confirmDelete(item)">
                                <v-icon size="small">mdi-delete</v-icon>
                                <v-tooltip activator="parent" location="top">Hapus</v-tooltip>
                            </v-btn>
                        </div>
                    </template>

                    <template v-slot:loading>
                        <v-skeleton-loader type="table-row@5"></v-skeleton-loader>
                    </template>

                    <template v-slot:no-data>
                        <div class="text-center py-8">
                            <v-icon size="64" color="grey-lighten-1">mdi-file-document-off</v-icon>
                            <p class="text-body-1 text-grey mt-4">Tidak ada halaman ditemukan</p>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <v-divider></v-divider>
                <div class="pa-4 d-flex align-center">
                    <div class="text-caption text-grey">
                        Menampilkan {{ pages.length }} dari {{ pagination.total }} halaman
                    </div>
                    <v-spacer></v-spacer>
                    <v-pagination v-model="pagination.current_page"
                        :length="Math.ceil(pagination.total / pagination.per_page)" :total-visible="5"
                        density="comfortable" @update:model-value="fetchPages"></v-pagination>
                </div>
            </v-card>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="400">
            <v-card rounded="xl">
                <v-card-title class="text-h6 font-weight-bold pa-6">
                    Hapus Halaman?
                </v-card-title>
                <v-card-text class="px-6">
                    <p class="text-body-1">
                        Anda yakin ingin menghapus halaman "<strong>{{ pageToDelete?.title }}</strong>"?
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </v-card-text>
                <v-card-actions class="pa-6 pt-0">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="deleteDialog = false">Batal</v-btn>
                    <v-btn color="error" variant="flat" :loading="deleting" @click="deletePage">
                        Hapus
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: ['auth', 'admin'],
    layout: 'solid'
})

const { api } = useApi()
const router = useRouter()

const loading = ref(false)
const deleting = ref(false)
const pages = ref([])
const search = ref('')
const sortBy = ref('order')
const filterPublished = ref(null)
const deleteDialog = ref(false)
const pageToDelete = ref(null)

const pagination = ref({
    current_page: 1,
    per_page: 10,
    total: 0
})

const headers = [
    { title: 'Judul', key: 'title', sortable: true },
    { title: 'Status', key: 'is_published', sortable: false },
    { title: 'Urutan', key: 'order', sortable: true },
    { title: 'Terakhir Diubah', key: 'updated_at', sortable: true },
    { title: 'Aksi', key: 'actions', sortable: false, align: 'center' as const, width: '140px' }
]

const publishedOptions = [
    { title: 'Published', value: true },
    { title: 'Draft', value: false }
]

const sortOptions = [
    { label: 'Urutan', value: 'order' },
    { label: 'Terbaru', value: 'newest' },
    { label: 'Terlama', value: 'oldest' },
    { label: 'Judul A-Z', value: 'title' }
]

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const fetchPages = async () => {
    loading.value = true
    try {
        const params: any = {
            page: pagination.value.current_page,
            per_page: pagination.value.per_page
        }

        if (search.value) {
            params.search = search.value
        }

        if (filterPublished.value !== null) {
            params.is_published = filterPublished.value
        }

        if (sortBy.value) {
            params.sort = sortBy.value
        }

        const response = await api.get('/admin/pages', { params })
        pages.value = response.data.data || []

        pagination.value = {
            current_page: response.data.current_page,
            per_page: response.data.per_page,
            total: response.data.total
        }
    } catch (error) {
        console.error('Error fetching pages:', error)
    } finally {
        loading.value = false
    }
}

const editPage = (id: number) => {
    router.push(`/manage/pages/${id}/edit`)
}

const confirmDelete = (page: any) => {
    pageToDelete.value = page
    deleteDialog.value = true
}

const deletePage = async () => {
    if (!pageToDelete.value) return

    deleting.value = true
    try {
        await api.delete(`/admin/pages/${pageToDelete.value.id}`)
        deleteDialog.value = false
        pageToDelete.value = null
        fetchPages()
    } catch (error: any) {
        console.error('Error deleting page:', error)
        alert(error.response?.data?.message || 'Gagal menghapus halaman')
    } finally {
        deleting.value = false
    }
}

onMounted(() => {
    fetchPages()
})
</script>

<style scoped>
.admin-pages-page {
    background: #fafafa;
    min-height: 100vh;
}
</style>
