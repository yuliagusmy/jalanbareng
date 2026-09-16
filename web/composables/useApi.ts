import axios, { type AxiosInstance, type AxiosRequestConfig } from 'axios'
import { useNuxtApp, useRuntimeConfig, useCookie } from '#app'

export const useApi = () => {
  const config = useRuntimeConfig()
  const nuxtApp = useNuxtApp()

  const api: AxiosInstance = axios.create({
    baseURL: config.public.apiUrl as string,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    withCredentials: true
  })

  api.interceptors.request.use(
    (config) => {
      // On the server, we need to forward the cookie from the incoming request.
      if (process.server) {
        const cookie = nuxtApp.ssrContext?.event.node.req.headers.cookie
        if (cookie) {
          config.headers.Cookie = cookie
        }
      }

      // For both client and server, add the Authorization header if the token cookie exists.
      const authToken = useCookie('auth_token')
      if (authToken.value) {
        config.headers.Authorization = `Bearer ${authToken.value}`
      }

      return config
    },
    (error) => Promise.reject(error)
  )

  // Response interceptor to handle 401 errors
  api.interceptors.response.use(
    (response) => response,
    (error) => {
      if (error.response?.status === 401) {
        const authStore = useAuthStore()
        authStore.logout()
      }
      return Promise.reject(error)
    }
  )

  return {
    api,
    get: (url: string, config?: AxiosRequestConfig) => api.get(url, config),
    post: (url: string, data?: any, config?: AxiosRequestConfig) => api.post(url, data, config),
    put: (url: string, data?: any, config?: AxiosRequestConfig) => api.put(url, data, config),
    patch: (url: string, data?: any, config?: AxiosRequestConfig) => api.patch(url, data, config),
    delete: (url: string, config?: AxiosRequestConfig) => api.delete(url, config),
  }
}