<template>
  <div class="tiptap-editor" :class="{ 'is-focused': isFocused }">
    <div v-if="editor" class="toolbar">
      <v-btn icon size="small" :variant="editor.isActive('bold') ? 'tonal' : 'text'" @click="editor.chain().focus().toggleBold().run()">
        <v-icon>mdi-format-bold</v-icon>
      </v-btn>
      <v-btn icon size="small" :variant="editor.isActive('italic') ? 'tonal' : 'text'" @click="editor.chain().focus().toggleItalic().run()">
        <v-icon>mdi-format-italic</v-icon>
      </v-btn>
      <v-btn icon size="small" :variant="editor.isActive('strike') ? 'tonal' : 'text'" @click="editor.chain().focus().toggleStrike().run()">
        <v-icon>mdi-format-strikethrough</v-icon>
      </v-btn>
      <v-divider vertical class="mx-2"></v-divider>
      <v-btn icon size="small" :variant="editor.isActive('link') ? 'tonal' : 'text'" @click="setLink">
        <v-icon>mdi-link</v-icon>
      </v-btn>
      <v-btn icon size="small" @click="addImage">
        <v-icon>mdi-image</v-icon>
      </v-btn>
      <v-divider vertical class="mx-2"></v-divider>
      <v-btn icon size="small" :variant="editor.isActive('bulletList') ? 'tonal' : 'text'" @click="editor.chain().focus().toggleBulletList().run()">
        <v-icon>mdi-format-list-bulleted</v-icon>
      </v-btn>
      <v-btn icon size="small" :variant="editor.isActive('orderedList') ? 'tonal' : 'text'" @click="editor.chain().focus().toggleOrderedList().run()">
        <v-icon>mdi-format-list-numbered</v-icon>
      </v-btn>
    </div>
    <editor-content :editor="editor" class="editor-content" />
  </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Link.configure({
      openOnClick: false,
    }),
    Image,
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  },
})

const isFocused = ref(false)

watch(() => props.modelValue, (value) => {
  if (editor.value && editor.value.getHTML() !== value) {
    editor.value.commands.setContent(value, false)
  }
})

const setLink = () => {
  const previousUrl = editor.value.getAttributes('link').href
  const url = window.prompt('URL', previousUrl)

  if (url === null) {
    return
  }

  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }

  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

const addImage = () => {
  const url = window.prompt('URL gambar')

  if (url) {
    editor.value.chain().focus().setImage({ src: url }).run()
  }
}

onMounted(() => {
    if(editor.value) {
        editor.value.on('focus', () => isFocused.value = true)
        editor.value.on('blur', () => isFocused.value = false)
    }
})

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy()
  }
})
</script>

<style>
.tiptap-editor .ProseMirror {
  min-height: 200px;
}

.tiptap-editor .ProseMirror:focus {
  outline: none;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 8px;
  border-bottom: 1px solid #ccc;
}

.tiptap-editor {
  border: 1px solid #ccc;
  border-radius: 8px;
  transition: border-color 0.3s;
}

.tiptap-editor.is-focused {
  border-color: #667eea;
}

.editor-content {
  padding: 16px;
}
</style>