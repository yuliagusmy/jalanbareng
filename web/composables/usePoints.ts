import { ref } from 'vue'
import { useApi } from '~/composables/useApi'

export interface PointWalletData {
  point: {
    balance: number
    total_earned: number
    total_withdrawn: number
  }
  balance: number
  rupiah_equivalent: number
  total_earned: number
  total_withdrawn: number
  min_cashout_points: number
  rupiah_per_point: number
  transactions: {
    data: Array<{
      id: number
      amount: number
      type: 'credit' | 'debit'
      source: string
      status: string
      description: string
      created_at: string
    }>
    current_page: number
    last_page: number
    total: number
  }
  recent_cashouts: Array<{
    id: number
    points_requested: number
    rupiah_amount: number
    payment_method: string
    account_number: string
    account_name: string
    status: 'pending' | 'approved' | 'rejected'
    receipt_image_url?: string
    admin_notes?: string
    created_at: string
  }>
}

export function usePoints() {
  const { api } = useApi()
  const loading = ref(false)
  const walletData = ref<PointWalletData | null>(null)
  const error = ref<string | null>(null)

  const fetchMyPoints = async () => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get('/points/me')
      walletData.value = response.data.data
      return walletData.value
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Gagal memuat informasi poin'
      console.error('Error fetching points:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const requestCashout = async (payload: {
    points_requested: number
    payment_method: string
    account_number: string
    account_name: string
  }) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post('/points/cashout', payload)
      await fetchMyPoints() // Refresh wallet data
      return { success: true, message: response.data.message, data: response.data.data }
    } catch (err: any) {
      const msg = err.response?.data?.message || 'Gagal mengajukan penarikan poin'
      error.value = msg
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    walletData,
    fetchMyPoints,
    requestCashout,
  }
}
