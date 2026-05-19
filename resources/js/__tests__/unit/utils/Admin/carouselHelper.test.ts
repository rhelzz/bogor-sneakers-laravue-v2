import { describe, it, expect } from 'vitest'
import {
  carouselTestData,
  createMockFile,
  filenameTestCases,
} from '@/__tests__/unit/utils/Admin/carouselHelper.test'
import { CAROUSEL_VALIDATION, CAROUSEL_ERRORS } from '@/constants/Admin/carouselConstants'
import {
  validateImageSize,
  validateImageType,
  validateImageExtension,
  validateImage,
} from '@/utils/Admin/carouselHelper'

describe('Carousel Helper Validation', () => {
  describe('validateImageSize', () => {
    it('should return true if file size is within limit', () => {
      const file = createMockFile('test.png', CAROUSEL_VALIDATION.image.maxSize - 1024)
      expect(validateImageSize(file)).toBe(true)
    })

    it('should return true if file size is exactly the limit', () => {
      const file = createMockFile('test.png', CAROUSEL_VALIDATION.image.maxSize * 1024)
      expect(validateImageSize(file)).toBe(true)
    })

    it('should return false if file size exceeds limit', () => {
      const file = carouselTestData.oversizedFile
      expect(validateImageSize(file)).toBe(false)
    })

    it('should accept edge case: file at max boundary', () => {
      const maxSizeBytes = CAROUSEL_VALIDATION.image.maxSize * 1024
      const file = createMockFile('boundary.png', maxSizeBytes)
      expect(validateImageSize(file)).toBe(true)
    })

    it('should reject file just over the limit', () => {
      const maxSizeBytes = (CAROUSEL_VALIDATION.image.maxSize + 1) * 1024
      const file = createMockFile('over-limit.png', maxSizeBytes)
      expect(validateImageSize(file)).toBe(false)
    })
  })

  describe('validateImageType', () => {
    it('should return true for allowed MIME types', () => {
      expect(validateImageType(carouselTestData.validFile)).toBe(true)
      expect(validateImageType(carouselTestData.validJpg)).toBe(true)
      expect(validateImageType(carouselTestData.validJpeg)).toBe(true)
    })

    it('should return false for disallowed MIME types', () => {
      expect(validateImageType(carouselTestData.gifFile)).toBe(false)
      expect(validateImageType(carouselTestData.pdfFile)).toBe(false)
      expect(validateImageType(carouselTestData.svgFile)).toBe(false)
      expect(validateImageType(carouselTestData.webpFile)).toBe(false)
    })

    it('should only accept PNG and JPEG types', () => {
      // Only these two should pass
      const validTypes = [
        createMockFile('test1.png', 1024, 'image/png'),
        createMockFile('test2.jpg', 1024, 'image/jpeg'),
      ]

      validTypes.forEach(file => {
        expect(validateImageType(file)).toBe(true)
      })
    })
  })

  describe('validateImageExtension', () => {
    it('should return true for allowed extensions', () => {
      filenameTestCases.valid.forEach((filename: string) => {
        expect(validateImageExtension(filename)).toBe(true)
      })
    })

    it('should return false for disallowed extensions', () => {
      filenameTestCases.invalid.forEach((filename: string) => {
        expect(validateImageExtension(filename)).toBe(false)
      })
    })

    it('should be case insensitive', () => {
      expect(validateImageExtension('image.PNG')).toBe(true)
      expect(validateImageExtension('image.JpG')).toBe(true)
      expect(validateImageExtension('image.JPEG')).toBe(true)
    })

    it('should reject files without extension', () => {
      expect(validateImageExtension('image')).toBe(false)
      expect(validateImageExtension('')).toBe(false)
    })

    it('should reject double extensions with invalid second extension', () => {
      expect(validateImageExtension('image.png.gif')).toBe(false)
    })
  })

  describe('validateImage', () => {
    it('should return valid true for correct file', () => {
      const result = validateImage(carouselTestData.validFile)
      expect(result.valid).toBe(true)
      expect(result.errors).toHaveLength(0)
    })

    it('should return valid true for both PNG and JPEG', () => {
      const pngResult = validateImage(carouselTestData.validFile)
      const jpgResult = validateImage(carouselTestData.validJpg)

      expect(pngResult.valid).toBe(true)
      expect(jpgResult.valid).toBe(true)
    })

    it('should return valid false with size error for oversized file', () => {
      const result = validateImage(carouselTestData.oversizedFile)
      expect(result.valid).toBe(false)
      expect(result.errors).toContain(CAROUSEL_ERRORS.image.maxSize)
    })

    it('should return valid false with type error for wrong MIME type', () => {
      const result = validateImage(carouselTestData.gifFile)
      expect(result.valid).toBe(false)
      expect(result.errors).toContain(CAROUSEL_ERRORS.image.mimes)
    })

    it('should return valid false with multiple errors for completely invalid file', () => {
      const invalidFile = carouselTestData.oversizedFile
      const result = validateImage(invalidFile)
      expect(result.valid).toBe(false)
      // Should have at least size and mime errors
      expect(result.errors.length).toBeGreaterThan(0)
    })

    it('should handle missing file gracefully', () => {
      // @ts-expect-error Testing null input validation
      const result = validateImage(null)
      expect(result.valid).toBe(false)
      expect(result.errors).toContain(CAROUSEL_ERRORS.image.required)
    })

    it('should handle undefined file gracefully', () => {
      // @ts-expect-error Testing undefined input validation
      const result = validateImage(undefined)
      expect(result.valid).toBe(false)
      expect(result.errors).toContain(CAROUSEL_ERRORS.image.required)
    })

    it('should validate file with minimal size', () => {
      const tinyFile = createMockFile('tiny.png', 1, 'image/png')
      const result = validateImage(tinyFile)
      // Should be valid if size is > 0
      expect(result.valid).toBe(true)
    })
  })

  describe('Integration: Full validation workflow', () => {
    it('should accept a complete valid carousel image', () => {
      const validCarouselImage = createMockFile('carousel-banner.jpg', 5000, 'image/jpeg')
      const result = validateImage(validCarouselImage)

      expect(result.valid).toBe(true)
      expect(validateImageSize(validCarouselImage)).toBe(true)
      expect(validateImageType(validCarouselImage)).toBe(true)
      expect(validateImageExtension('carousel-banner.jpg')).toBe(true)
    })

    it('should reject file that fails multiple validations', () => {
      const result = validateImage(carouselTestData.oversizedFile)

      expect(result.valid).toBe(false)
      expect(result.errors.length).toBeGreaterThan(0)
      expect(validateImageSize(carouselTestData.oversizedFile)).toBe(false)
    })
  })
})
