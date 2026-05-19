import { CAROUSEL_VALIDATION } from '@/constants/Admin/carouselConstants'

/**
 * Factory function untuk membuat mock file dengan properti yang dapat dikonfigurasi
 */
export const createMockFile = (
  filename: string = 'test.png',
  size: number = 1024,
  type: string = 'image/png'
): File => {
  const file = new File([''], filename, { type })
  Object.defineProperty(file, 'size', { value: size })
  
  return file
}

/**
 * Test data fixtures untuk carousel helper tests
 * Memudahkan reusability dan maintenance test cases
 */
export const carouselTestData = {
  // Valid files
  validFile: createMockFile('test.png', 1024, 'image/png'),
  validJpg: createMockFile('test.jpg', 2048, 'image/jpeg'),
  validJpeg: createMockFile('photo.jpeg', 3072, 'image/jpeg'),

  // Invalid files - size
  oversizedFile: createMockFile(
    'large.png',
    (CAROUSEL_VALIDATION.image.maxSize + 1) * 1024,
    'image/png'
  ),
  emptyFile: createMockFile('empty.png', 0, 'image/png'),

  // Invalid files - type
  gifFile: createMockFile('test.gif', 512, 'image/gif'),
  pdfFile: createMockFile('document.pdf', 512, 'application/pdf'),
  svgFile: createMockFile('icon.svg', 256, 'image/svg+xml'),
  webpFile: createMockFile('image.webp', 1024, 'image/webp'),

  // Edge cases
  fileWithoutExtension: new File([''], 'testfile', { type: 'image/png' }),
  doubleExtensionFile: createMockFile('test.png.gif', 512, 'image/gif'),
}

/**
 * Filenames untuk extension validation tests
 */
export const filenameTestCases = {
  valid: ['image.png', 'photo.jpg', 'picture.jpeg', 'UPPERCASE.PNG', 'mixedCase.Jpg'],
  invalid: [
    'image.gif',
    'image.pdf',
    'image',
    '',
    'image.svg',
    'image.webp',
    'image.txt',
    'image.bmp',
  ],
}
