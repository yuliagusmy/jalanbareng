<template>
  <div class="comment-item" :class="{ 'is-reply': isReply }">
    <div class="d-flex ga-3">
      <!-- Avatar -->
      <v-avatar :size="isReply ? 32 : 40" color="#FEF2F2" class="flex-shrink-0 mt-1">
        <v-img v-if="comment.user?.photo && !avatarError"
          :src="getImageUrl(comment.user.photo)"
          @error="avatarError = true"
          cover
        ></v-img>
        <span v-else-if="comment.user?.name" :class="isReply ? 'text-caption' : 'text-body-2'" class="font-weight-bold" style="color: #DC2626;">
          {{ comment.user.name.charAt(0).toUpperCase() }}
        </span>
        <v-icon v-else color="#DC2626" :size="isReply ? 16 : 20">mdi-account</v-icon>
      </v-avatar>

      <!-- Content -->
      <div class="flex-grow-1 min-width-0">
        <div class="comment-bubble">
          <div class="d-flex align-center justify-space-between flex-wrap ga-1 mb-1">
            <div class="d-flex align-center ga-2">
              <span class="comment-author">{{ comment.user?.name || 'Anonim' }}</span>
              <span class="comment-time">{{ formatTimeAgo(comment.created_at) }}</span>
            </div>

            <!-- Delete button (own comment or admin) -->
            <v-btn
              v-if="canDelete"
              icon
              variant="text"
              size="x-small"
              color="error"
              class="delete-btn"
              aria-label="Hapus komentar"
              @click="emit('delete', comment.id, comment.parent_id)"
            >
              <v-icon size="14">mdi-delete-outline</v-icon>
            </v-btn>
          </div>

          <p class="comment-content">{{ comment.content }}</p>
        </div>

        <!-- Actions -->
        <div class="comment-actions">
          <!-- Like -->
          <button
            class="action-btn"
            :class="{ 'liked': comment.is_liked }"
            :disabled="!isLoggedIn"
            :title="isLoggedIn ? 'Suka komentar ini' : 'Login untuk menyukai'"
            @click="isLoggedIn && emit('like', comment.id)"
          >
            <v-icon size="14">{{ comment.is_liked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
            <span v-if="comment.likes_count > 0">{{ comment.likes_count }}</span>
          </button>

          <!-- Reply (only on root comments) -->
          <button
            v-if="!isReply && isLoggedIn"
            class="action-btn"
            @click="emit('reply', comment.id)"
          >
            <v-icon size="14">mdi-reply-outline</v-icon>
            Balas
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
interface CommentUser {
  id: number
  name: string
  photo: string | null
}

interface Comment {
  id: number
  user_id: number
  user: CommentUser
  content: string
  likes_count: number
  parent_id: number | null
  created_at: string
  is_liked?: boolean
}

const props = defineProps<{
  comment: Comment
  currentUserId?: number
  isLoggedIn: boolean
  isReply?: boolean
}>()

const emit = defineEmits<{
  (e: 'like', commentId: number): void
  (e: 'reply', commentId: number): void
  (e: 'delete', commentId: number, parentId: number | null): void
}>()

const { getImageUrl } = useImageUrl()
const avatarError = ref(false)

const canDelete = computed(() => {
  if (!props.isLoggedIn || !props.currentUserId) return false
  return props.comment.user_id === props.currentUserId
})

const formatTimeAgo = (dateStr: string) => {
  if (!dateStr) return ''
  const date = new Date(dateStr)
  const now = new Date()
  const diffSec = Math.floor((now.getTime() - date.getTime()) / 1000)
  if (diffSec < 60) return 'Baru saja'
  if (diffSec < 3600) return `${Math.floor(diffSec / 60)} mnt lalu`
  if (diffSec < 86400) return `${Math.floor(diffSec / 3600)} jam lalu`
  if (diffSec < 604800) return `${Math.floor(diffSec / 86400)} hari lalu`
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<style scoped>
.comment-item {
  padding: 14px 0;
}

.comment-bubble {
  background: #F9FAFB;
  border-radius: 0 12px 12px 12px;
  padding: 10px 14px;
  border: 1px solid #F3F4F6;
}

.comment-author {
  font-size: 0.85rem;
  font-weight: 700;
  color: #111827;
}

.comment-time {
  font-size: 0.75rem;
  color: #9CA3AF;
}

.comment-content {
  font-size: 0.9rem;
  color: #374151;
  line-height: 1.6;
  margin: 0;
  white-space: pre-wrap;
  word-break: break-word;
}

.comment-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 6px;
  padding-left: 4px;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.78rem;
  font-weight: 600;
  color: #6B7280;
  background: none;
  border: none;
  cursor: pointer;
  padding: 2px 6px;
  border-radius: 9999px;
  transition: all 0.2s ease;
}

.action-btn:hover:not(:disabled) {
  color: #DC2626;
  background: #FEF2F2;
}

.action-btn.liked {
  color: #DC2626;
}

.action-btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.delete-btn {
  opacity: 0;
  transition: opacity 0.2s ease;
}

.comment-item:hover .delete-btn {
  opacity: 1;
}

.is-reply .comment-bubble {
  background: #F3F4F6;
}

.min-width-0 {
  min-width: 0;
}
</style>
