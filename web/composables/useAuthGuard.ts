import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useRouter } from '#imports'

export interface AuthGuardOptions {
  action?: string
  title?: string
  message?: string
  redirect?: string
  onSuccess?: () => void
}

const isAuthPromptOpen = ref(false)
const authPromptTitle = ref('Bergabung Bersama Jalan Bareng')
const authPromptMessage = ref('Masuk atau buat akun untuk ikut berkontribusi dan berinteraksi bersama warga pejalan kaki.')
const authPromptAction = ref('interaksi')
const authPromptRedirect = ref('')

export function useAuthGuard() {
  const authStore = useAuthStore()
  const router = useRouter()

  const requireAuth = (options: AuthGuardOptions = {}): boolean => {
    if (authStore.isLoggedIn) {
      options.onSuccess?.()
      return true
    }

    authPromptAction.value = options.action || 'interaksi'
    authPromptTitle.value = options.title || 'Bergabung Bersama Jalan Bareng'
    authPromptMessage.value = options.message || 'Masuk atau buat akun untuk ikut berkontribusi dan berinteraksi bersama warga pejalan kaki.'
    authPromptRedirect.value = options.redirect || ''
    isAuthPromptOpen.value = true
    return false
  }

  const closeAuthPrompt = () => {
    isAuthPromptOpen.value = false
  }

  const goToLogin = () => {
    isAuthPromptOpen.value = false
    const query = authPromptRedirect.value ? { redirect: authPromptRedirect.value } : undefined
    router.push({ path: '/login', query })
  }

  const goToRegister = () => {
    isAuthPromptOpen.value = false
    const query = authPromptRedirect.value ? { redirect: authPromptRedirect.value } : undefined
    router.push({ path: '/register', query })
  }

  return {
    isAuthPromptOpen,
    authPromptTitle,
    authPromptMessage,
    authPromptAction,
    authPromptRedirect,
    requireAuth,
    closeAuthPrompt,
    goToLogin,
    goToRegister,
  }
}
