<template>
  <section class="comment-section" aria-label="Komentar komunitas">
    <!-- Header -->
    <div class="d-flex align-center justify-space-between mb-5">
      <div class="d-flex align-center ga-2">
        <v-icon color="primary" size="22">mdi-comment-multiple-outline</v-icon>
        <h2 class="comment-heading">
          Komentar
          <span v-if="totalCount > 0" class="comment-count-badge">{{ totalCount }}</span>
        </h2>
      </div>
    </div>

    <!-- Form Tambah Komentar (hanya jika login) -->
    <div v-if="authStore.isLoggedIn" class="new-comment-form mb-6">
      <div class="d-flex ga-3">
        <v-avatar size="40" color="#FEF2F2" class="flex-shrink-0 mt-1">
          <v-img v-if="authStore.user?.photo" :src="getImageUrl(authStore.user.photo)" cover></v-img>
          <span v-else-if="authStore.user?.name" class="text-body-2 font-weight-bold" style="color: #DC2626;">
            {{ authStore.user.name.charAt(0).toUpperCase() }}
          </span>
          <v-icon v-else color="#DC2626" size="20">mdi-account</v-icon>
        </v-avatar>
        <div class="flex-grow-1">
          <v-textarea
            v-model="newComment"
            :placeholder="`Bagikan pengalamanmu tentang ${destinationName}...`"
            variant="outlined"
            rounded="lg"
            rows="3"
            max-rows="6"
            auto-grow
            hide-details
            class="comment-textarea"
            :disabled="posting"
            @keydown.ctrl.enter="submitComment"
          />
          <div class="d-flex justify-end mt-2">
            <v-btn
              color="primary"
              variant="flat"
              rounded="pill"
              size="small"
              class="font-weight-bold"
              :loading="posting"
              :disabled="!newComment.trim()"
              @click="submitComment"
            >
              <v-icon start size="16">mdi-send</v-icon>
              Kirim Komentar
            </v-btn>
          </div>
        </div>
      </div>
    </div>

    <!-- Login CTA -->
    <div v-else class="login-cta mb-6">
      <v-icon size="20" color="grey">mdi-lock-outline</v-icon>
      <span class="text-body-2 text-grey-darken-1 ml-2">
        <nuxt-link to="/login" class="text-primary font-weight-bold" style="text-decoration: none;">Masuk</nuxt-link>
        untuk meninggalkan komentar
      </span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="d-flex justify-center py-8">
      <v-progress-circular indeterminate color="primary" size="32" />
    </div>

    <!-- Empty State -->
    <div v-else-if="comments.length === 0" class="empty-state text-center py-10">
      <v-icon size="56" color="grey-lighten-2">mdi-comment-outline</v-icon>
      <p class="text-subtitle-2 text-grey-darken-1 mt-3 mb-1">Belum ada komentar</p>
      <p class="text-caption text-grey">Jadilah yang pertama berbagi pengalaman!</p>
    </div>

    <!-- Comment List -->
    <div v-else class="comment-list">
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="comment-thread"
      >
        <!-- Parent Comment -->
        <CommentItem
          :comment="comment"
          :current-user-id="authStore.user?.id"
          :is-logged-in="authStore.isLoggedIn"
          @like="toggleLike"
          @reply="startReply"
          @delete="deleteComment"
        />

        <!-- Replies -->
        <div v-if="comment.replies && comment.replies.length > 0" class="replies-indent">
          <CommentItem
            v-for="reply in comment.replies"
            :key="reply.id"
            :comment="reply"
            :current-user-id="authStore.user?.id"
            :is-logged-in="authStore.isLoggedIn"
            is-reply
            @like="toggleLike"
            @delete="deleteComment"
          />
        </div>

        <!-- Reply Form -->
        <div v-if="replyingTo === comment.id && authStore.isLoggedIn" class="reply-form replies-indent">
          <div class="d-flex ga-3">
            <v-avatar size="32" color="#FEF2F2" class="flex-shrink-0 mt-1">
              <span v-if="authStore.user?.name" class="text-caption font-weight-bold" style="color: #DC2626;">
                {{ authStore.user.name.charAt(0).toUpperCase() }}
              </span>
            </v-avatar>
            <div class="flex-grow-1">
              <v-textarea
                :ref="el => { if (el) replyInputRef = el }"
                v-model="replyContent"
                :placeholder="`Balas komentar ${comment.user?.name}...`"
                variant="outlined"
                rounded="lg"
                rows="2"
                max-rows="4"
                auto-grow
                hide-details
                density="compact"
                class="comment-textarea"
                :disabled="postingReply"
              />
              <div class="d-flex ga-2 justify-end mt-2">
                <v-btn size="small" variant="text" @click="cancelReply">Batal</v-btn>
                <v-btn
                  color="primary"
                  variant="flat"
                  rounded="pill"
                  size="small"
                  class="font-weight-bold"
                  :loading="postingReply"
                  :disabled="!replyContent.trim()"
                  @click="submitReply(comment.id)"
                >
                  Balas
                </v-btn>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Snackbar Feedback -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" rounded="pill" location="bottom center" timeout="3000">
      {{ snackbar.text }}
    </v-snackbar>
  </section>
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
  replies?: Comment[]
  created_at: string
  is_liked?: boolean
}

const props = defineProps<{
  destinationId: number
  destinationName: string
}>()

const authStore = useAuthStore()
const { api } = useApi()
const { getImageUrl } = useImageUrl()

// State
const comments = ref<Comment[]>([])
const loading = ref(false)
const posting = ref(false)
const postingReply = ref(false)
const newComment = ref('')
const replyingTo = ref<number | null>(null)
const replyContent = ref('')
const replyInputRef = ref<any>(null)
const snackbar = ref({ show: false, text: '', color: 'success' })

const totalCount = computed(() => {
  return comments.value.reduce((acc, c) => acc + 1 + (c.replies?.length || 0), 0)
})

const showMessage = (text: string, color: 'success' | 'error' = 'success') => {
  snackbar.value = { show: true, text, color }
}

// Fetch comments
const fetchComments = async () => {
  loading.value = true
  try {
    const res = await api.get(`/comments?commentable_type=App\\Models\\Destination&commentable_id=${props.destinationId}`)
    comments.value = res.data?.comments || res.data || []
  } catch (err) {
    console.error('Failed to fetch comments:', err)
  } finally {
    loading.value = false
  }
}

// Submit new comment
const submitComment = async () => {
  if (!newComment.value.trim() || posting.value) return
  posting.value = true
  try {
    const res = await api.post('/comments', {
      commentable_type: 'App\\Models\\Destination',
      commentable_id: props.destinationId,
      content: newComment.value.trim(),
    })
    const comment = res.data?.comment
    if (comment) {
      comment.replies = []
      comments.value.unshift(comment)
    }
    newComment.value = ''
    showMessage('Komentar berhasil dikirim!')
  } catch (err) {
    showMessage('Gagal mengirim komentar. Coba lagi.', 'error')
  } finally {
    posting.value = false
  }
}

// Start reply
const startReply = (commentId: number) => {
  replyingTo.value = commentId
  replyContent.value = ''
  nextTick(() => {
    replyInputRef.value?.$el?.querySelector('textarea')?.focus()
  })
}

const cancelReply = () => {
  replyingTo.value = null
  replyContent.value = ''
}

// Submit reply
const submitReply = async (parentId: number) => {
  if (!replyContent.value.trim() || postingReply.value) return
  postingReply.value = true
  try {
    const res = await api.post('/comments', {
      commentable_type: 'App\\Models\\Destination',
      commentable_id: props.destinationId,
      parent_id: parentId,
      content: replyContent.value.trim(),
    })
    const reply = res.data?.comment
    if (reply) {
      const parent = comments.value.find(c => c.id === parentId)
      if (parent) {
        if (!parent.replies) parent.replies = []
        parent.replies.push(reply)
      }
    }
    cancelReply()
    showMessage('Balasan berhasil dikirim!')
  } catch (err) {
    showMessage('Gagal mengirim balasan. Coba lagi.', 'error')
  } finally {
    postingReply.value = false
  }
}

// Toggle like on comment
const toggleLike = async (commentId: number) => {
  // Optimistic update
  const findAndToggle = (list: Comment[]) => {
    for (const c of list) {
      if (c.id === commentId) {
        c.is_liked = !c.is_liked
        c.likes_count += c.is_liked ? 1 : -1
        return true
      }
      if (c.replies && findAndToggle(c.replies)) return true
    }
    return false
  }
  findAndToggle(comments.value)

  try {
    await api.post('/likes/toggle', {
      likeable_type: 'App\\Models\\Comment',
      likeable_id: commentId,
    })
  } catch (err) {
    // Revert
    findAndToggle(comments.value)
    showMessage('Gagal menyukai komentar', 'error')
  }
}

// Delete comment
const deleteComment = async (commentId: number, parentId: number | null) => {
  try {
    await api.delete(`/comments/${commentId}`)
    if (parentId) {
      const parent = comments.value.find(c => c.id === parentId)
      if (parent?.replies) {
        parent.replies = parent.replies.filter(r => r.id !== commentId)
      }
    } else {
      comments.value = comments.value.filter(c => c.id !== commentId)
    }
    showMessage('Komentar dihapus.')
  } catch (err) {
    showMessage('Gagal menghapus komentar.', 'error')
  }
}

onMounted(() => {
  fetchComments()
})
</script>

<style scoped>
.comment-section {
  margin-top: 2rem;
}

.comment-heading {
  font-size: 1.25rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  color: #111827;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.comment-count-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #DC2626;
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 9999px;
  min-width: 22px;
  height: 22px;
  padding: 0 6px;
}

.new-comment-form {
  background: #FAFAF9;
  border: 1px solid #E5E7EB;
  border-radius: 16px;
  padding: 16px;
}

.login-cta {
  background: #F9FAFB;
  border: 1px dashed #D1D5DB;
  border-radius: 12px;
  padding: 14px 20px;
  display: flex;
  align-items: center;
}

.comment-textarea :deep(.v-field) {
  background: white !important;
}

.comment-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.comment-thread {
  border-bottom: 1px solid #F3F4F6;
}

.comment-thread:last-child {
  border-bottom: none;
}

.replies-indent {
  margin-left: 52px;
  border-left: 2px solid #F3F4F6;
  padding-left: 16px;
}

.reply-form {
  padding: 12px 0 16px;
}
</style>
