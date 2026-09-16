export interface CompressionResult {
  file: File
  originalSize: number
  compressedSize: number
  reductionPercent: number
  previewUrl: string
}

export const useImageCompressor = () => {
  const isCompressing = ref(false)

  /**
   * Compress an image file to WebP format with optional max dimensions
   */
  const compressImage = async (
    file: File,
    maxWidth: number = 1920,
    maxHeight: number = 1080,
    quality: number = 0.82
  ): Promise<CompressionResult> => {
    isCompressing.value = true

    return new Promise((resolve, reject) => {
      // If not an image, return original
      if (!file.type.startsWith('image/')) {
        isCompressing.value = false
        resolve({
          file,
          originalSize: file.size,
          compressedSize: file.size,
          reductionPercent: 0,
          previewUrl: URL.createObjectURL(file),
        })
        return
      }

      const reader = new FileReader()
      reader.onerror = () => {
        isCompressing.value = false
        reject(new Error('Gagal membaca file gambar'))
      }

      reader.onload = (event) => {
        const img = new Image()
        img.onerror = () => {
          isCompressing.value = false
          reject(new Error('Gagal memproses gambar'))
        }

        img.onload = () => {
          let width = img.width
          let height = img.height

          // Calculate new dimensions keeping aspect ratio
          if (width > maxWidth || height > maxHeight) {
            const ratio = Math.min(maxWidth / width, maxHeight / height)
            width = Math.round(width * ratio)
            height = Math.round(height * ratio)
          }

          const canvas = document.createElement('canvas')
          canvas.width = width
          canvas.height = height

          const ctx = canvas.getContext('2d')
          if (!ctx) {
            isCompressing.value = false
            reject(new Error('Gagal membuat canvas'))
            return
          }

          // Draw and smooth image
          ctx.imageSmoothingEnabled = true
          ctx.imageSmoothingQuality = 'high'
          ctx.drawImage(img, 0, 0, width, height)

          // Export as WebP
          canvas.toBlob(
            (blob) => {
              isCompressing.value = false
              if (!blob) {
                // Fallback to original
                resolve({
                  file,
                  originalSize: file.size,
                  compressedSize: file.size,
                  reductionPercent: 0,
                  previewUrl: URL.createObjectURL(file),
                })
                return
              }

              // Create clean .webp filename
              const baseName = file.name.replace(/\.[^/.]+$/, '')
              const webpName = `${baseName}.webp`
              const webpFile = new File([blob], webpName, {
                type: 'image/webp',
                lastModified: Date.now(),
              })

              const originalSize = file.size
              const compressedSize = webpFile.size
              const reductionPercent = Math.max(
                0,
                Math.round(((originalSize - compressedSize) / originalSize) * 100)
              )

              console.log(
                `[ImageCompressor] ${file.name} (${(originalSize / 1024).toFixed(1)} KB) -> ${webpName} (${(compressedSize / 1024).toFixed(1)} KB) [-${reductionPercent}%]`
              )

              const previewUrl = URL.createObjectURL(webpFile)

              resolve({
                file: webpFile,
                originalSize,
                compressedSize,
                reductionPercent,
                previewUrl,
              })
            },
            'image/webp',
            quality
          )
        }

        img.src = event.target?.result as string
      }

      reader.readAsDataURL(file)
    })
  }

  /**
   * Compress multiple images sequentially
   */
  const compressImages = async (
    files: File[],
    maxWidth: number = 1920,
    maxHeight: number = 1080,
    quality: number = 0.82
  ): Promise<CompressionResult[]> => {
    const results: CompressionResult[] = []
    for (const file of files) {
      try {
        const res = await compressImage(file, maxWidth, maxHeight, quality)
        results.push(res)
      } catch (err) {
        console.warn('Skipping uncompressible file:', file.name, err)
      }
    }
    return results
  }

  const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return `${bytes} B`
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
  }

  return {
    isCompressing,
    compressImage,
    compressImages,
    formatFileSize,
  }
}
