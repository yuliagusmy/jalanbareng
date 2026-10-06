<template>
  <section class="comment-section" aria-label="Komentar">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-6">
      <div class="d-flex align-center ga-3">
        <div class="section-line" aria-hidden="true"></div>
        <h2 class="text-h6 font-weight-black text-grey-darken-4">
          Komentar
          <span v-if="totalComments > 0" class="comment-badge">{{ totalComments }}</span>
        </h2>
      </div>
    </div>

    <!-- Write comment box -->
    <div class="write-comment-box mb-8">
      <template v-if="authStore.isLoggedIn">
        <div class="d-flex ga-3 align-start">
          <v-avatar size="40" color="#FEF2F2" class="flex-shrink-0 mt-1">
            <v-icon color="#DC2626" size="22">mdi-account</v-icon>
          </v-avatar>
          <div class="flex-grow-1">
            <v-textarea
              v-model="newComment"
              variant="outlined"
              rounded="lg"
              rows="3"
              auto-grow
              max-rows="6"
              placeholder="Tulis komentar kamu di sini…"
              hide-details
              :disabled="submitting"
              class="comment-textarea"
              @keydown.ctrl.enter="submitComment()"
              @keydown.meta.enter="submitComment()"
            />
            <div class="d-flex justify-end mt-2">
              <v-btn
                color="primary"
                rounded="pill"
                size="small"
                :loading="submitting"
                :disabled="!newComment.trim()"
                @click="submitComment()"
                aria-label="Kirim komentar"
              >
                <v-icon start size="16">mdi-send</v-icon>
                Kirim
              </v-btn>
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="login-prompt" @click="promptLogin" role="button" tabindex="0" @keydown.enter="promptLogin" aria-label="Masuk untuk berkomentar">
          <v-avatar size="40" color="#F3F4F6" class="flex-shrink-0">
            <v-icon color="#9CA3AF" size="22">mdi-account-outline</v-icon>
          </v-avatar>
          <div class="login-prompt-text">
            <span class="text-body-2 text-grey-darken-1">Masuk untuk ikut berkomentar…</span>
          </div>
          <v-btn size="small" color="primary" rounded="pill" variant="flat" class="ml-auto flex-shrink-0">
            Masuk
          </v-btn>
        </div>
      </template>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-8">
      <v-progress-circular indeterminate color="primary" size="36" />
    </div>

    <!-- Empty state -->
    <div v-else-if="comments.length === 0" class="empty-comments text-center py-8">
      <v-icon size="48" color="grey-lighten-1" class="mb-3">mdi-chat-outline</v-icon>
      <p class="text-body-2 text-grey-darken-1">Belum ada komentar. Jadilah yang pertama!</p>
    </div>

    <!-- Comments list -->
    <div v-else class="comments-list">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="comment-item"
      >
        <!-- Top-level comment -->
        <div class="comment-bubble">
          <div class="d-flex ga-3 align-start">
            <v-avatar size="36" color="#F3F4F6" class="flex-shrink-0">
              <img
                v-if="comment.user?.avatar_url"
                :src="comment.user.avatar_url"
                :alt="comment.user.name"
              />
              <v-icon v-else color="#9CA3AF" size="20">mdi-account</v-icon>
            </v-avatar>
            <div class="flex-grow-1 min-width-0">
              <div class="d-flex align-center ga-2 flex-wrap mb-1">
                <span class="font-weight-bold text-body-2 text-grey-darken-4">{{ comment.user?.name || 'Anonim' }}</span>
                <span class="text-caption text-grey">{{ timeAgo(comment.created_at) }}</span>
              </div>
              <p class="comment-text">{{ comment.content }}</p>
              <div class="d-flex align-center ga-3 mt-2">
                <!-- Like reply button -->
                <button
                  class="action-btn"
                  :class="{ liked: comment.is_liked }"
                  :aria-label="comment.is_liked ? 'Batalkan like' : 'Like komentar'"
                  @click="toggleCommentLike(comment)"
                >
                  <v-icon size="14">{{ comment.is_liked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                  <span>{{ comment.likes_count || 0 }}</span>
                </button>
                <!-- Reply button -->
                <button
                  class="action-btn"
                  aria-label="Balas komentar"
                  @click="startReply(comment)"
                >
                  <v-icon size="14">mdi-reply</v-icon>
                  <span>Balas</span>
                </button>
                <!-- Delete (own comment) -->
                <button
                  v-if="authStore.user?.id === comment.user_id"
                  class="action-btn delete-btn"
                  aria-label="Hapus komentar"
                  @click="deleteComment(comment)"
                >
                  <v-icon size="14">mdi-delete-outline</v-icon>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Replies -->
        <div v-if="comment.replies?.length" class="replies-wrap">
          <div
            v-for="reply in comment.replies"
            :key="reply.id"
            class="comment-bubble reply-bubble"
          >
            <div class="d-flex ga-3 align-start">
              <v-avatar size="30" color="#F3F4F6" class="flex-shrink-0">
                <img
                  v-if="reply.user?.avatar_url"
                  :src="reply.user.avatar_url"
                  :alt="reply.user.name"
                />
                <v-icon v-else color="#9CA3AF" size="16">mdi-account</v-icon>
              </v-avatar>
              <div class="flex-grow-1 min-width-0">
                <div class="d-flex align-center ga-2 flex-wrap mb-1">
                  <span class="font-weight-bold text-caption text-grey-darken-4">{{ reply.user?.name || 'Anonim' }}</span>
                  <span class="text-caption text-grey">{{ timeAgo(reply.created_at) }}</span>
                </div>
                <p class="comment-text text-caption">{{ reply.content }}</p>
                <div class="d-flex align-center ga-3 mt-1">
                  <button
                    class="action-btn"
                    :class="{ liked: reply.is_liked }"
                    :aria-label="reply.is_liked ? 'Batalkan like' : 'Like balasan'"
                    @click="toggleCommentLike(reply)"
                  >
                    <v-icon size="12">{{ reply.is_liked ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                    <span>{{ reply.likes_count || 0 }}</span>
                  </button>
                  <button
                    v-if="authStore.user?.id === reply.user_id"
                    class="action-btn delete-btn"
                    aria-label="Hapus balasan"
                    @click="deleteComment(reply)"
                  >
                    <v-icon size="12">mdi-delete-outline</v-icon>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Inline reply box -->
        <div v-if="replyingTo?.id === comment.id" class="reply-input-wrap">
          <div class="d-flex ga-2 align-start">
            <v-avatar size="30" color="#FEF2F2" class="flex-shrink-0 mt-1">
              <v-icon color="#DC2626" size="16">mdi-account</v-icon>
            </v-avatar>
            <div class="flex-grow-1">
              <v-textarea
                v-model="replyText"
                variant="outlined"
                rounded="lg"
                rows="2"
                auto-grow
                max-rows="4"
                :placeholder="`Balas komentar ${comment.user?.name || 'ini'}…`"
                hide-details
                density="compact"
                :disabled="submitting"
                autofocus
                @keydown.ctrl.enter="submitReply(comment)"
                @keydown.meta.enter="submitReply(comment)"
                @keydown.escape="replyingTo = null"
              />
              <div class="d-flex ga-2 justify-end mt-2">
                <v-btn size="x-small" variant="text" rounded="pill" @click="replyingTo = null">Batal</v-btn>
                <v-btn
                  size="x-small"
                  color="primary"
                  rounded="pill"
                  :loading="submitting"
                  :disabled="!replyText.trim()"
                  @click="submitReply(comment)"
                  aria-label="Kirim balasan"
                >
                  <v-icon start size="12">mdi-send</v-icon>
                  Balas
                </v-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useApi } from '~/composables/useApi'
import { useAuthGuard } from '~/composables/useAuthGuard'
import { useAuthStore } from '~/stores/auth'

const props = defineProps<{
  storyId: number
  initialCommentsCount?: number
}>()

const emit = defineEmits<{
  (e: 'count-change', count: number): void
}>()

const { api } = useApi()
const { requireAuth } = useAuthGuard()
const authStore = useAuthStore()

const comments = ref<any[]>([])
const loading = ref(false)
const submitting = ref(false)
const newComment = ref('')
const replyingTo = ref<any>(null)
const replyText = ref('')

const totalComments = computed(() => {
  return comments.value.reduce((sum, c) => sum + 1 + (c.replies?.length || 0), 0)
})

const timeAgo = (dateStr: string) => {
  if (!dateStr) return ''
  const diff = Date.now() - new Date(dateStr).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'Baru saja'
  if (mins < 60) return `${mins} mnt lalu`
  const hours = Math.floor(mins / 60)
  if (hours < 24) return `${hours} jam lalu`
  const days = Math.floor(hours / 24)
  if (days < 7) return `${days} hari lalu`
  return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const fetchComments = async () => {
  loading.value = true
  try {
    const res = await api.get('/comments', {
      params: { commentable_type: 'story', commentable_id: props.storyId }
    })
    comments.value = res.data.comments || []
    emit('count-change', totalComments.value)
  } catch {
    // silent
  } finally {
    loading.value = false
  }
}

const promptLogin = () => {
  requireAuth({
    action: 'komentar',
    title: 'Ikut berdiskusi!',
    message: 'Masuk untuk menulis komentar dan berdiskusi bersama pembaca lain.',
  })
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  const authed = requireAuth({ action: 'komentar', title: 'Ikut berdiskusi!', message: 'Masuk untuk menulis komentar.' })
  if (!authed) return

  submitting.value = true
  try {
    const res = await api.post('/comments', {
      commentable_type: 'story',
      commentable_id: props.storyId,
      content: newComment.value.trim(),
    })
    const newItem = { ...res.data.comment, replies: [], is_liked: false }
    comments.value.unshift(newItem)
    newComment.value = ''
    emit('count-change', totalComments.value)
  } catch {
    // silent
  } finally {
    submitting.value = false
  }
}

const startReply = (comment: any) => {
  const authed = requireAuth({ action: 'balas komentar', title: 'Ikut berdiskusi!' })
  if (!authed) return
  replyingTo.value = comment
  replyText.value = ''
}

const submitReply = async (parentComment: any) => {
  if (!replyText.value.trim()) return
  submitting.value = true
  try {
    const res = await api.post('/comments', {
      commentable_type: 'story',
      commentable_id: props.storyId,
      content: replyText.value.trim(),
      parent_id: parentComment.id,
    })
    if (!parentComment.replies) parentComment.replies = []
    parentComment.replies.push({ ...res.data.comment, is_liked: false })
    replyText.value = ''
    replyingTo.value = null
    emit('count-change', totalComments.value)
  } catch {
    // silent
  } finally {
    submitting.value = false
  }
}

const toggleCommentLike = async (comment: any) => {
  const authed = requireAuth({ action: 'like komentar', title: 'Dukung diskusi ini!' })
  if (!authed) return

  const wasLiked = comment.is_liked
  comment.is_liked = !wasLiked
  comment.likes_count = (comment.likes_count || 0) + (wasLiked ? -1 : 1)

  try {
    const res = await api.post('/likes/toggle', {
      likeable_type: 'App\\Models\\Comment',
      likeable_id: comment.id,
    })
    comment.is_liked = res.data.liked
    comment.likes_count = res.data.likes_count
  } catch {
    comment.is_liked = wasLiked
    comment.likes_count = (comment.likes_count || 0) + (wasLiked ? 1 : -1)
  }
}

const deleteComment = async (comment: any) => {
  try {
    await api.delete(`/comments/${comment.id}`)
    // Remove from list or replies
    const idx = comments.value.findIndex(c => c.id === comment.id)
    if (idx !== -1) {
      comments.value.splice(idx, 1)
    } else {
      for (const c of comments.value) {
        if (c.replies) {
          const ri = c.replies.findIndex((r: any) => r.id === comment.id)
          if (ri !== -1) { c.replies.splice(ri, 1); break }
        }
      }
    }
    emit('count-change', totalComments.value)
  } catch {
    // silent
  }
}

watch(() => authStore.isLoggedIn, () => {
  fetchComments()
})

onMounted(() => {
  fetchComments()
})
</script>

<style scoped>
.section-line {
  width: 32px;
  height: 4px;
  background: #DC2626;
  border-radius: 2px;
}

.comment-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
  background: #DC2626;
  color: white;
  border-radius: 9999px;
  font-size: 0.72rem;
  font-weight: 700;
  margin-left: 4px;
  vertical-align: middle;
}

.write-comment-box {
  background: #FAFAF9;
  border: 1px solid #E5E7EB;
  border-radius: 16px;
  padding: 16px;
}

.login-prompt {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
}

.login-prompt-text { flex: 1; min-width: 0; }

.comment-textarea :deep(.v-field) {
  background: white;
}

.comments-list { display: flex; flex-direction: column; gap: 0; }

.comment-item {
  padding-bottom: 0;
}

.comment-item + .comment-item {
  border-top: 1px solid #F3F4F6;
  padding-top: 16px;
  margin-top: 16px;
}

.comment-bubble {
  padding: 0;
}

.comment-text {
  font-size: 0.92rem;
  line-height: 1.6;
  color: #374151;
  margin: 0;
  word-break: break-word;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 8px;
  border: none;
  background: none;
  border-radius: 9999px;
  cursor: pointer;
  font-size: 0.78rem;
  font-weight: 600;
  color: #9CA3AF;
  transition: all 0.18s ease;
  font-family: inherit;
}

.action-btn:hover { color: #6B7280; background: #F3F4F6; }
.action-btn.liked { color: #DC2626; }
.action-btn.liked:hover { background: #FEF2F2; }
.action-btn.delete-btn:hover { color: #DC2626; background: #FEF2F2; }

.replies-wrap {
  margin-left: 48px;
  margin-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding-left: 16px;
  border-left: 2px solid #F3F4F6;
}

.reply-input-wrap {
  margin-left: 48px;
  margin-top: 12px;
  padding: 12px;
  background: #F9FAFB;
  border-radius: 12px;
}

.min-width-0 { min-width: 0; }

@media (max-width: 600px) {
  .replies-wrap { margin-left: 32px; }
  .reply-input-wrap { margin-left: 32px; }
}
</style>
