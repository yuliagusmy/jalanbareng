<template>
  <button
    class="like-btn"
    :class="{ 'is-liked': isLiked, 'is-loading': loading }"
    :aria-label="isLiked ? 'Batalkan like' : 'Like cerita ini'"
    :aria-pressed="isLiked"
    @click="handleToggle"
  >
    <span class="like-icon-wrap">
      <v-icon :class="{ bounce: justLiked }" size="22">
        {{ isLiked ? 'mdi-heart' : 'mdi-heart-outline' }}
      </v-icon>
    </span>
    <span class="like-count">{{ displayCount }}</span>
    <span class="like-label">{{ isLiked ? 'Disukai' : 'Suka' }}</span>
  </button>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useApi } from '~/composables/useApi'
import { useAuthGuard } from '~/composables/useAuthGuard'
import { useAuthStore } from '~/stores/auth'

const props = defineProps<{
  storyId: number
  initialLikesCount?: number
}>()

const { api } = useApi()
const { requireAuth } = useAuthGuard()
const authStore = useAuthStore()

const isLiked = ref(false)
const localCount = ref(props.initialLikesCount ?? 0)
const loading = ref(false)
const justLiked = ref(false)

const displayCount = computed(() => {
  if (localCount.value >= 1000) {
    return (localCount.value / 1000).toFixed(1) + 'k'
  }
  return localCount.value
})

const checkLikeStatus = async () => {
  if (!authStore.isLoggedIn) return
  try {
    const res = await api.get('/likes/check', {
      params: { likeable_type: 'App\\Models\\Story', likeable_id: props.storyId }
    })
    isLiked.value = res.data.liked
  } catch {
    // silent
  }
}

const handleToggle = async () => {
  const authed = requireAuth({
    action: 'like',
    title: 'Suka dengan cerita ini?',
    message: 'Masuk atau buat akun untuk memberikan like dan mendukung penulis Jalan Bareng.',
  })
  if (!authed) return

  if (loading.value) return
  loading.value = true

  const wasLiked = isLiked.value
  isLiked.value = !wasLiked
  localCount.value = wasLiked ? localCount.value - 1 : localCount.value + 1

  if (!wasLiked) {
    justLiked.value = true
    setTimeout(() => { justLiked.value = false }, 600)
  }

  try {
    const res = await api.post('/likes/toggle', {
      likeable_type: 'App\\Models\\Story',
      likeable_id: props.storyId,
    })
    isLiked.value = res.data.liked
    localCount.value = res.data.likes_count
  } catch {
    isLiked.value = wasLiked
    localCount.value = wasLiked ? localCount.value + 1 : localCount.value - 1
  } finally {
    loading.value = false
  }
}

watch(() => props.initialLikesCount, (v) => {
  if (v !== undefined) localCount.value = v
})

watch(() => authStore.isLoggedIn, (loggedIn) => {
  if (loggedIn) checkLikeStatus()
  else isLiked.value = false
})

onMounted(() => {
  if (authStore.isLoggedIn) checkLikeStatus()
})
</script>

<style scoped>
.like-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border: 2px solid #E5E7EB;
  border-radius: 9999px;
  background: white;
  cursor: pointer;
  transition: all 0.22s ease;
  font-family: inherit;
  font-size: 0.9rem;
  font-weight: 600;
  color: #6B7280;
  user-select: none;
  outline: none;
}

.like-btn:hover {
  border-color: #FCA5A5;
  color: #DC2626;
  background: #FEF2F2;
  transform: translateY(-1px);
}

.like-btn:active { transform: scale(0.96); }

.like-btn.is-liked {
  border-color: #DC2626;
  background: #FEF2F2;
  color: #DC2626;
}

.like-btn.is-loading {
  opacity: 0.7;
  pointer-events: none;
}

.like-icon-wrap { display: flex; align-items: center; color: inherit; }
.like-count { font-size: 0.9rem; font-weight: 700; line-height: 1; }
.like-label { font-size: 0.85rem; }

@keyframes heartBounce {
  0%   { transform: scale(1); }
  30%  { transform: scale(1.45); }
  60%  { transform: scale(0.9); }
  100% { transform: scale(1); }
}

.bounce { animation: heartBounce 0.55s cubic-bezier(0.34, 1.56, 0.64, 1); }

@media (max-width: 600px) {
  .like-label { display: none; }
  .like-btn { padding: 10px 16px; }
}
</style>
