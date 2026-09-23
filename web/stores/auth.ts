import { defineStore } from 'pinia'

interface Role {
  id: number
  name: string
  display_name: string
}

interface User {
  id: number
  name: string
  email: string
  phone?: string
  photo?: string
  role?: Role | string
  role_id?: number
  instagram?: string
  facebook?: string
  twitter?: string
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false
  }),

  getters: {
    currentUser: (state) => state.user,
    isLoggedIn: (state) => state.isAuthenticated,
    userRole: (state) => {
      if (!state.user) return null
      if (typeof state.user.role === 'object' && state.user.role) {
        return state.user.role.name
      }
      return state.user.role
    },
    isAdmin: (state) => {
      if (!state.user?.role) return false
      const roleName = typeof state.user.role === 'object' ? state.user.role.name : state.user.role
      return roleName === 'admin'
    },
    isCommunityAdmin: (state) => {
      if (!state.user?.role) return false
      const roleName = typeof state.user.role === 'object' ? state.user.role.name : state.user.role
      return roleName === 'community_admin'
    },
    isMember: (state) => {
      if (!state.user?.role) return false
      const roleName = typeof state.user.role === 'object' ? state.user.role.name : state.user.role
      return roleName === 'member'
    }
  },

  actions: {
    async login(credentials: { email: string; password: string }) {
      const { api } = useApi()
      const authToken = useCookie('auth_token')

      try {
        // Get CSRF cookie first (for Sanctum)
        const config = useRuntimeConfig()
        await api.get(`${config.public.apiBase}/sanctum/csrf-cookie`)

        // Login
        const response = await api.post('/auth/login', credentials)

        const token = response.data.token || response.data.access_token
        this.token = token
        this.user = response.data.user
        this.isAuthenticated = true

        // Store token in cookie
        authToken.value = token

        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async register(data: {
      name: string
      email: string
      password: string
      password_confirmation: string
      phone?: string
    }) {
      const { api } = useApi()
      const authToken = useCookie('auth_token')

      try {
        // Get CSRF cookie first
        const config = useRuntimeConfig()
        await api.get(`${config.public.apiBase}/sanctum/csrf-cookie`)

        // Register
        const response = await api.post('/auth/register', data)

        const token = response.data.token || response.data.access_token
        this.token = token
        this.user = response.data.user
        this.isAuthenticated = true

        // Store token in cookie
        authToken.value = token

        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    async logout() {
      const { api } = useApi()
      const authToken = useCookie('auth_token')

      try {
        await api.post('/auth/logout')
      } catch (error) {
        // Continue with logout even if API call fails
      } finally {
        this.token = null
        this.user = null
        this.isAuthenticated = false
        authToken.value = null

        if (process.client) {
          navigateTo('/login')
        }
      }
    },

    async fetchUser() {
      const { api } = useApi()
      const authToken = useCookie('auth_token')

      if (!authToken.value) {
        return
      }

      try {
        const response = await api.get('/auth/me')
        this.user = response.data.user
        this.isAuthenticated = true
        this.token = authToken.value
      } catch (error) {
        // Clear auth if fetch fails
        this.token = null
        this.user = null
        this.isAuthenticated = false
        authToken.value = null
      }
    },

    async updateProfile(data: Partial<User>) {
      const { api } = useApi()

      try {
        const response = await api.put('/auth/profile', data)
        this.user = response.data.user
        return response.data
      } catch (error: any) {
        throw error.response?.data || error
      }
    },

    setToken(token: string) {
      const authToken = useCookie('auth_token')
      this.token = token
      authToken.value = token
      this.isAuthenticated = true
    },

    setUser(user: User) {
      this.user = user
      this.isAuthenticated = true
    }
  }
})
