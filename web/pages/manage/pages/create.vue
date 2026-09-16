<template>
    <div class="create-page-page">
        <v-container class="py-8">
            <!-- Header -->
            <v-card elevation="0" rounded="lg" class="mb-6">
                <v-card-title class="d-flex align-center px-6 py-4">
                    <v-btn icon variant="text" @click="router.back()" class="mr-2">
                        <v-icon>mdi-arrow-left</v-icon>
                    </v-btn>
                    <div>
                        <h1 class="text-h5 font-weight-bold">Tambah Halaman Baru</h1>
                        <p class="text-caption text-grey mb-0">Buat halaman statis baru</p>
                    </div>
                </v-card-title>
            </v-card>

            <!-- Form -->
            <v-form ref="formRef" v-model="formValid" @submit.prevent="handleSubmit">
                <v-row>
                    <v-col cols="12" md="8">
                        <v-card elevation="0" rounded="lg" class="mb-4">
                            <v-card-text class="pa-6">
                                <v-text-field v-model="form.title" label="Judul Halaman"
                                    placeholder="Contoh: Tentang Kami" variant="outlined" :rules="[rules.required]"
                                    class="mb-4"></v-text-field>

                                <v-text-field v-model="form.slug" label="Slug (URL)" placeholder="tentang-kami"
                                    variant="outlined" hint="Kosongkan untuk generate otomatis dari judul"
                                    persistent-hint class="mb-4"></v-text-field>

                                <v-textarea v-model="form.content" label="Konten"
                                    placeholder="Tulis konten halaman di sini..." variant="outlined"
                                    :rules="[rules.required]" rows="15" class="mb-4"></v-textarea>

                                <v-text-field v-model="form.meta_description" label="Meta Description"
                                    placeholder="Deskripsi singkat untuk SEO" variant="outlined"
                                    hint="Maksimal 160 karakter" persistent-hint counter="160"
                                    maxlength="160"></v-text-field>
                            </v-card-text>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card elevation="0" rounded="lg" class="mb-4">
                            <v-card-title class="px-6 py-4">
                                <h3 class="text-h6">Pengaturan</h3>
                            </v-card-title>
                            <v-divider></v-divider>
                            <v-card-text class="pa-6">
                                <v-switch v-model="form.is_published" label="Publish Halaman" color="primary"
                                    hide-details class="mb-4"></v-switch>

                                <v-text-field v-model.number="form.order" label="Urutan" type="number"
                                    variant="outlined" hint="Urutan tampil di footer (semakin kecil semakin awal)"
                                    persistent-hint min="0"></v-text-field>
                            </v-card-text>
                        </v-card>

                        <v-card elevation="0" rounded="lg">
                            <v-card-text class="pa-6">
                                <v-btn type="submit" block size="large" color="primary" :loading="loading"
                                    :disabled="!formValid" class="mb-2">
                                    <v-icon start>mdi-content-save</v-icon>
                                    Simpan Halaman
                                </v-btn>
                                <v-btn block size="large" variant="outlined" @click="router.back()" :disabled="loading">
                                    Batal
                                </v-btn>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-form>
        </v-container>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: ['auth', 'admin'],
    layout: 'solid'
})

const { api } = useApi()
const router = useRouter()

const formRef = ref(null)
const formValid = ref(false)
const loading = ref(false)

const form = ref({
    title: '',
    slug: '',
    content: '',
    meta_description: '',
    is_published: true,
    order: 0
})

const rules = {
    required: (value: any) => !!value || 'Field ini wajib diisi'
}

const handleSubmit = async () => {
    if (!formValid.value) return

    loading.value = true
    try {
        await api.post('/admin/pages', form.value)

        router.push('/manage/pages')
    } catch (error: any) {
        console.error('Error creating page:', error)
        alert(error.response?.data?.message || 'Gagal membuat halaman')
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.create-page-page {
    background: #fafafa;
    min-height: 100vh;
}
</style>
