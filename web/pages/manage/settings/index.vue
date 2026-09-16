<template>
    <div class="settings-page">
        <v-container class="py-8">
            <!-- Header -->
            <v-card elevation="0" rounded="lg" class="mb-6">
                <v-card-title class="d-flex align-center px-6 py-4">
                    <v-icon class="mr-3" size="32" color="primary">mdi-cog</v-icon>
                    <div>
                        <h1 class="text-h5 font-weight-bold">Pengaturan Website</h1>
                        <p class="text-caption text-grey mb-0">Kelola konten footer dan pengaturan umum</p>
                    </div>
                </v-card-title>
            </v-card>

            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
                <p class="mt-4 text-body-1">Memuat pengaturan...</p>
            </div>

            <!-- Settings Form -->
            <v-form v-else ref="formRef" v-model="formValid" @submit.prevent="handleSubmit">
                <!-- Footer Brand Settings -->
                <v-card elevation="0" rounded="lg" class="mb-4">
                    <v-card-title class="px-6 py-4">
                        <h3 class="text-h6">Brand Footer</h3>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="pa-6">
                        <v-text-field v-model="settings.footer_brand_title" label="Judul Brand"
                            placeholder="Jalan Bareng" variant="outlined" :rules="[rules.required]"
                            class="mb-4"></v-text-field>

                        <v-textarea v-model="settings.footer_brand_description" label="Deskripsi"
                            placeholder="Platform komunitas untuk..." variant="outlined" :rules="[rules.required]"
                            rows="4"></v-textarea>
                    </v-card-text>
                </v-card>

                <!-- Social Media Settings -->
                <v-card elevation="0" rounded="lg" class="mb-4">
                    <v-card-title class="px-6 py-4">
                        <h3 class="text-h6">Social Media</h3>
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="pa-6">
                        <v-text-field v-model="settings.footer_social_facebook" label="Facebook URL"
                            placeholder="https://facebook.com/..." variant="outlined" prepend-inner-icon="mdi-facebook"
                            :rules="[rules.url]" class="mb-4"></v-text-field>

                        <v-text-field v-model="settings.footer_social_instagram" label="Instagram URL"
                            placeholder="https://instagram.com/..." variant="outlined"
                            prepend-inner-icon="mdi-instagram" :rules="[rules.url]" class="mb-4"></v-text-field>

                        <v-text-field v-model="settings.footer_social_twitter" label="Twitter URL"
                            placeholder="https://twitter.com/..." variant="outlined" prepend-inner-icon="mdi-twitter"
                            :rules="[rules.url]"></v-text-field>
                    </v-card-text>
                </v-card>

                <!-- Save Button -->
                <v-card elevation="0" rounded="lg">
                    <v-card-text class="pa-6">
                        <v-btn type="submit" size="large" color="primary" :loading="saving" :disabled="!formValid"
                            class="mr-2">
                            <v-icon start>mdi-content-save</v-icon>
                            Simpan Pengaturan
                        </v-btn>
                        <v-btn size="large" variant="outlined" @click="fetchSettings" :disabled="saving">
                            <v-icon start>mdi-refresh</v-icon>
                            Reset
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-form>
        </v-container>

        <!-- Success Snackbar -->
        <v-snackbar v-model="snackbar" :timeout="3000" color="success">
            Pengaturan berhasil disimpan!
            <template v-slot:actions>
                <v-btn variant="text" @click="snackbar = false">Tutup</v-btn>
            </template>
        </v-snackbar>
    </div>
</template>

<script setup lang="ts">
definePageMeta({
    middleware: ['auth', 'admin'],
    layout: 'solid'
})

const { api } = useApi()

const formRef = ref(null)
const formValid = ref(false)
const loading = ref(true)
const saving = ref(false)
const snackbar = ref(false)

const settings = ref({
    footer_brand_title: '',
    footer_brand_description: '',
    footer_social_facebook: '',
    footer_social_instagram: '',
    footer_social_twitter: '',
})

const rules = {
    required: (value: any) => !!value || 'Field ini wajib diisi',
    url: (value: any) => {
        if (!value) return true
        const pattern = /^https?:\/\/.+/
        return pattern.test(value) || 'URL harus valid (https://...)'
    }
}

const fetchSettings = async () => {
    loading.value = true
    try {
        const response = await api.get('/admin/settings', {
            params: { group: 'footer' }
        })

        const footerSettings = response.data.settings.footer || []

        footerSettings.forEach((setting: any) => {
            if (settings.value.hasOwnProperty(setting.key)) {
                settings.value[setting.key as keyof typeof settings.value] = setting.value
            }
        })
    } catch (error) {
        console.error('Error fetching settings:', error)
    } finally {
        loading.value = false
    }
}

const handleSubmit = async () => {
    if (!formValid.value) return

    saving.value = true
    try {
        const settingsArray = Object.entries(settings.value).map(([key, value]) => ({
            key,
            value,
            type: key.includes('social') ? 'url' : 'text',
            group: 'footer'
        }))

        await api.put('/admin/settings', {
            settings: settingsArray
        })

        snackbar.value = true
    } catch (error: any) {
        console.error('Error saving settings:', error)
        alert(error.response?.data?.message || 'Gagal menyimpan pengaturan')
    } finally {
        saving.value = false
    }
}

onMounted(() => {
    fetchSettings()
})
</script>

<style scoped>
.settings-page {
    background: #fafafa;
    min-height: 100vh;
}
</style>
