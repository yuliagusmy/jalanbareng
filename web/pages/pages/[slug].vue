<template>
    <div class="page-view">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
            <v-container>
                <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                <p class="mt-4 text-body-1">Memuat halaman...</p>
            </v-container>
        </div>

        <!-- Page Content -->
        <div v-else-if="page">
            <v-container class="py-12">
                <v-row justify="center">
                    <v-col cols="12" md="10" lg="8">
                        <v-card elevation="0" rounded="xl" class="pa-8">
                            <h1 class="text-h3 font-weight-bold mb-6">{{ page.title }}</h1>

                            <v-divider class="mb-6"></v-divider>

                            <div class="page-content" v-html="page.content"></div>

                            <v-divider class="my-6"></v-divider>

                            <div class="text-caption text-grey">
                                Terakhir diperbarui: {{ formatDate(page.updated_at) }}
                            </div>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </div>

        <!-- Not Found -->
        <div v-else class="text-center py-12">
            <v-container>
                <v-icon size="120" color="grey-lighten-1">mdi-file-document-remove</v-icon>
                <h2 class="text-h4 font-weight-bold mt-4 mb-2">Halaman Tidak Ditemukan</h2>
                <p class="text-body-1 text-grey mb-6">Halaman yang Anda cari tidak tersedia.</p>
                <v-btn color="primary" size="large" to="/">
                    <v-icon start>mdi-home</v-icon>
                    Kembali ke Beranda
                </v-btn>
            </v-container>
        </div>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    layout: 'solid'
})

const { api } = useApi()
const route = useRoute()

const loading = ref(true)
const page = ref(null)

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

const fetchPage = async () => {
    loading.value = true
    try {
        const response = await api.get(`/pages/${route.params.slug}`)
        page.value = response.data.page

        // Set page title for SEO
        if (page.value) {
            useHead({
                title: page.value.title,
                meta: [
                    {
                        name: 'description',
                        content: page.value.meta_description || page.value.title
                    }
                ]
            })
        }
    } catch (error: any) {
        console.error('Error fetching page:', error)
        page.value = null
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchPage()
})
</script>

<style scoped>
.page-view {
    min-height: 100vh;
    background: #fafafa;
}

.page-content :deep(h2) {
    font-size: 1.75rem;
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.page-content :deep(h3) {
    font-size: 1.5rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
}

.page-content :deep(p) {
    margin-bottom: 1rem;
    line-height: 1.7;
}

.page-content :deep(ul),
.page-content :deep(ol) {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.page-content :deep(li) {
    margin-bottom: 0.5rem;
    line-height: 1.6;
}

.page-content :deep(a) {
    color: rgb(var(--v-theme-primary));
    text-decoration: none;
}

.page-content :deep(a:hover) {
    text-decoration: underline;
}
</style>
