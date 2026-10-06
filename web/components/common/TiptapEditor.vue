<template>
  <div class="tiptap-editor" :class="{ 'is-focused': isFocused }">
    <!-- Toolbar -->
    <div v-if="editor" class="tiptap-toolbar">
      <!-- Heading Dropdown -->
      <div class="toolbar-group">
        <v-menu>
          <template #activator="{ props: menuProps }">
            <button v-bind="menuProps" class="toolbar-btn" type="button" title="Format Paragraf">
              <v-icon size="16">{{ headingIcon }}</v-icon>
              <v-icon size="12" class="ml-0">mdi-chevron-down</v-icon>
            </button>
          </template>
          <v-list density="compact" rounded="lg" elevation="3" min-width="180">
            <v-list-item
              @click="editor.chain().focus().setParagraph().run()"
              :class="{ 'bg-grey-lighten-2': editor.isActive('paragraph') }"
            >
              <v-list-item-title class="text-body-2">Paragraf Normal</v-list-item-title>
            </v-list-item>
            <v-list-item
              @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
              :class="{ 'bg-grey-lighten-2': editor.isActive('heading', { level: 2 }) }"
            >
              <v-list-item-title class="text-h6 font-weight-bold">Judul Besar</v-list-item-title>
            </v-list-item>
            <v-list-item
              @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
              :class="{ 'bg-grey-lighten-2': editor.isActive('heading', { level: 3 }) }"
            >
              <v-list-item-title class="text-subtitle-1 font-weight-bold">Judul Sedang</v-list-item-title>
            </v-list-item>
            <v-list-item
              @click="editor.chain().focus().toggleHeading({ level: 4 }).run()"
              :class="{ 'bg-grey-lighten-2': editor.isActive('heading', { level: 4 }) }"
            >
              <v-list-item-title class="text-subtitle-2 font-weight-bold">Judul Kecil</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Text Formatting -->
      <div class="toolbar-group">
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('bold') }"
          @click="editor.chain().focus().toggleBold().run()"
          type="button"
          title="Tebal (Ctrl+B)"
        >
          <v-icon size="16">mdi-format-bold</v-icon>
        </button>
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('italic') }"
          @click="editor.chain().focus().toggleItalic().run()"
          type="button"
          title="Miring (Ctrl+I)"
        >
          <v-icon size="16">mdi-format-italic</v-icon>
        </button>
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('strike') }"
          @click="editor.chain().focus().toggleStrike().run()"
          type="button"
          title="Coret"
        >
          <v-icon size="16">mdi-format-strikethrough</v-icon>
        </button>
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('code') }"
          @click="editor.chain().focus().toggleCode().run()"
          type="button"
          title="Kode"
        >
          <v-icon size="16">mdi-code-tags</v-icon>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Block Elements -->
      <div class="toolbar-group">
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('bulletList') }"
          @click="editor.chain().focus().toggleBulletList().run()"
          type="button"
          title="Daftar Poin"
        >
          <v-icon size="16">mdi-format-list-bulleted</v-icon>
        </button>
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('orderedList') }"
          @click="editor.chain().focus().toggleOrderedList().run()"
          type="button"
          title="Daftar Nomor"
        >
          <v-icon size="16">mdi-format-list-numbered</v-icon>
        </button>
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('blockquote') }"
          @click="editor.chain().focus().toggleBlockquote().run()"
          type="button"
          title="Kutipan"
        >
          <v-icon size="16">mdi-format-quote-open</v-icon>
        </button>
        <button
          class="toolbar-btn"
          @click="editor.chain().focus().setHorizontalRule().run()"
          type="button"
          title="Garis Pemisah"
        >
          <v-icon size="16">mdi-minus</v-icon>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- Links & Media -->
      <div class="toolbar-group">
        <button
          class="toolbar-btn"
          :class="{ 'is-active': editor.isActive('link') }"
          @click="setLink"
          type="button"
          title="Sisipkan Tautan"
        >
          <v-icon size="16">mdi-link-variant</v-icon>
        </button>
        <button
          class="toolbar-btn"
          @click="showImageDialog = true"
          type="button"
          title="Sisipkan Gambar"
        >
          <v-icon size="16">mdi-image-plus</v-icon>
        </button>
      </div>

      <div class="toolbar-divider"></div>

      <!-- History -->
      <div class="toolbar-group">
        <button
          class="toolbar-btn"
          @click="editor.chain().focus().undo().run()"
          :disabled="!editor.can().undo()"
          type="button"
          title="Undo"
        >
          <v-icon size="16">mdi-undo</v-icon>
        </button>
        <button
          class="toolbar-btn"
          @click="editor.chain().focus().redo().run()"
          :disabled="!editor.can().redo()"
          type="button"
          title="Redo"
        >
          <v-icon size="16">mdi-redo</v-icon>
        </button>
      </div>
    </div>

    <!-- Editor Content -->
    <editor-content :editor="editor" class="tiptap-content" />

    <!-- Word count -->
    <div v-if="showWordCount && editor" class="tiptap-footer">
      <span class="word-count">{{ wordCount }} kata</span>
    </div>

    <!-- Image Dialog -->
    <v-dialog v-model="showImageDialog" max-width="460">
      <v-card rounded="xl">
        <v-card-title class="pa-5 d-flex align-center justify-space-between border-b">
          <span class="text-subtitle-1 font-weight-bold">Sisipkan Gambar</span>
          <v-btn icon="mdi-close" variant="text" density="compact" @click="showImageDialog = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-5">
          <v-tabs v-model="imageTab" class="mb-4">
            <v-tab value="url">URL Gambar</v-tab>
            <v-tab value="upload">Upload File</v-tab>
          </v-tabs>
          <v-window v-model="imageTab">
            <v-window-item value="url">
              <v-text-field
                v-model="imageUrl"
                label="URL Gambar"
                placeholder="https://contoh.com/gambar.jpg"
                variant="outlined"
                density="comfortable"
                rounded="lg"
                hide-details
                class="mb-3"
              ></v-text-field>
              <v-img v-if="imageUrl" :src="imageUrl" height="120" cover rounded="lg" class="mb-3 border"></v-img>
            </v-window-item>
            <v-window-item value="upload">
              <v-file-input
                v-model="uploadFile"
                accept="image/*"
                label="Pilih foto (JPG, PNG, WebP, maks 2MB)"
                prepend-inner-icon="mdi-image"
                prepend-icon=""
                variant="outlined"
                density="comfortable"
                rounded="lg"
                show-size
                hide-details
                @update:model-value="onUploadFileSelected"
              ></v-file-input>
              <v-img v-if="uploadPreview" :src="uploadPreview" height="120" cover rounded="lg" class="mt-3 border"></v-img>
              <p class="text-caption text-grey mt-2">Gambar akan disematkan langsung ke dalam tulisan.</p>
            </v-window-item>
          </v-window>
        </v-card-text>
        <v-card-actions class="pa-5 border-t">
          <v-spacer></v-spacer>
          <v-btn variant="text" rounded="pill" @click="cancelImageDialog">Batal</v-btn>
          <v-btn color="primary" variant="flat" rounded="pill" @click="insertImage" :loading="uploadingImage">
            <v-icon start size="16">mdi-check</v-icon>
            Sisipkan
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  minHeight: {
    type: String,
    default: '240px',
  },
  showWordCount: {
    type: Boolean,
    default: true,
  },
})

const emit = defineEmits(['update:modelValue'])

const isFocused = ref(false)
const showImageDialog = ref(false)
const imageTab = ref('url')
const imageUrl = ref('')
const uploadFile = ref<any>(null)
const uploadPreview = ref<string | null>(null)
const uploadingImage = ref(false)

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        rel: 'noopener noreferrer',
        target: '_blank',
      },
    }),
    Image.configure({
      HTMLAttributes: {
        class: 'tiptap-image',
      },
    }),
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
  onFocus: () => { isFocused.value = true },
  onBlur: () => { isFocused.value = false },
})

const headingIcon = computed(() => {
  if (!editor.value) return 'mdi-format-paragraph'
  if (editor.value.isActive('heading', { level: 2 })) return 'mdi-format-header-2'
  if (editor.value.isActive('heading', { level: 3 })) return 'mdi-format-header-3'
  if (editor.value.isActive('heading', { level: 4 })) return 'mdi-format-header-4'
  return 'mdi-format-paragraph'
})

const wordCount = computed(() => {
  if (!editor.value) return 0
  const text = editor.value.getText()
  if (!text.trim()) return 0
  return text.trim().split(/\s+/).length
})

watch(() => props.modelValue, (value) => {
  if (editor.value && editor.value.getHTML() !== value) {
    editor.value.commands.setContent(value || '', false)
  }
})

const setLink = () => {
  if (!editor.value) return
  const previousUrl = editor.value.getAttributes('link').href
  const url = window.prompt('Masukkan URL tautan:', previousUrl || 'https://')

  if (url === null) return

  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }

  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

const onUploadFileSelected = (file: any) => {
  const actualFile = Array.isArray(file) ? file[0] : file
  if (actualFile instanceof File) {
    const reader = new FileReader()
    reader.onload = (e) => {
      uploadPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(actualFile)
  } else {
    uploadPreview.value = null
  }
}

const insertImage = async () => {
  if (!editor.value) return

  if (imageTab.value === 'url') {
    if (imageUrl.value) {
      editor.value.chain().focus().setImage({ src: imageUrl.value }).run()
      cancelImageDialog()
    }
  } else {
    // Upload tab — embed base64 directly (no server upload needed in this flow)
    if (uploadPreview.value) {
      uploadingImage.value = true
      try {
        editor.value.chain().focus().setImage({ src: uploadPreview.value }).run()
        cancelImageDialog()
      } finally {
        uploadingImage.value = false
      }
    }
  }
}

const cancelImageDialog = () => {
  showImageDialog.value = false
  imageUrl.value = ''
  uploadFile.value = null
  uploadPreview.value = null
  imageTab.value = 'url'
}

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style>
/* Global ProseMirror styles (no scoped so they apply to rendered editor) */
.tiptap-editor .ProseMirror {
  outline: none;
  padding: 16px;
  min-height: v-bind(minHeight);
  line-height: 1.75;
  color: #1F2937;
  font-size: 15px;
}

.tiptap-editor .ProseMirror p {
  margin-bottom: 0.85em;
}

.tiptap-editor .ProseMirror h2 {
  font-size: 1.5em;
  font-weight: 700;
  margin: 1.2em 0 0.5em;
  color: #111827;
  line-height: 1.3;
}

.tiptap-editor .ProseMirror h3 {
  font-size: 1.25em;
  font-weight: 700;
  margin: 1em 0 0.4em;
  color: #111827;
}

.tiptap-editor .ProseMirror h4 {
  font-size: 1.1em;
  font-weight: 600;
  margin: 0.8em 0 0.3em;
  color: #374151;
}

.tiptap-editor .ProseMirror blockquote {
  border-left: 4px solid #DC2626;
  padding: 10px 16px;
  margin: 1em 0;
  background: #FFF5F5;
  border-radius: 0 8px 8px 0;
  color: #4B5563;
  font-style: italic;
}

.tiptap-editor .ProseMirror ul,
.tiptap-editor .ProseMirror ol {
  padding-left: 1.5em;
  margin-bottom: 0.85em;
}

.tiptap-editor .ProseMirror li {
  margin-bottom: 0.3em;
}

.tiptap-editor .ProseMirror code {
  background: #F3F4F6;
  border-radius: 4px;
  padding: 2px 5px;
  font-size: 0.88em;
  font-family: 'Courier New', monospace;
  color: #DC2626;
}

.tiptap-editor .ProseMirror pre {
  background: #1F2937;
  color: #E5E7EB;
  border-radius: 8px;
  padding: 12px 16px;
  overflow-x: auto;
  margin: 1em 0;
}

.tiptap-editor .ProseMirror hr {
  border: none;
  border-top: 2px solid #E5E7EB;
  margin: 1.5em 0;
}

.tiptap-editor .ProseMirror a {
  color: #DC2626;
  text-decoration: underline;
}

.tiptap-image {
  max-width: 100%;
  border-radius: 10px;
  margin: 1em 0;
  display: block;
  box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.tiptap-editor .ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  color: #9CA3AF;
  pointer-events: none;
  float: left;
  height: 0;
}
</style>

<style scoped>
.tiptap-editor {
  border: 1.5px solid #D1D5DB;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.tiptap-editor.is-focused {
  border-color: #DC2626;
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

/* Toolbar */
.tiptap-toolbar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 2px;
  padding: 8px 10px;
  border-bottom: 1.5px solid #E5E7EB;
  background: #FAFAFA;
}

.toolbar-group {
  display: flex;
  align-items: center;
  gap: 1px;
}

.toolbar-divider {
  width: 1px;
  height: 20px;
  background: #D1D5DB;
  margin: 0 4px;
}

.toolbar-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  color: #4B5563;
  transition: background 0.15s ease, color 0.15s ease;
}

.toolbar-btn:hover:not(:disabled) {
  background: #F3F4F6;
  color: #111827;
}

.toolbar-btn.is-active {
  background: #FEE2E2;
  color: #DC2626;
}

.toolbar-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

/* Footer */
.tiptap-footer {
  padding: 6px 16px;
  border-top: 1px solid #F3F4F6;
  background: #FAFAFA;
}

.word-count {
  font-size: 11px;
  color: #9CA3AF;
}

/* Mobile Responsiveness (Compact 390px Viewport) */
@media (max-width: 600px) {
  .tiptap-toolbar {
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding: 6px 8px;
    gap: 4px;
  }

  .tiptap-toolbar::-webkit-scrollbar {
    display: none;
  }

  .toolbar-btn {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
  }

  .toolbar-divider {
    flex-shrink: 0;
  }
}
</style>