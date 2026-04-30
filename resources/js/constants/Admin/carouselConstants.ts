/**
 * Carousel Home Constants
 * Static data and validation rules untuk Carousel Home management
 */

// Validation Rules (sesuai dengan backend)
export const CAROUSEL_VALIDATION = {
  image: {
    maxSize: 5120, // 5MB in KB
    allowedMimes: ['image/jpeg', 'image/png'],
    allowedExtensions: ['jpg', 'jpeg', 'png'],
  },
} as const;

// Error Messages
export const CAROUSEL_ERRORS = {
  image: {
    required: 'Image harus diunggah',
    invalid: 'File harus berupa gambar',
    mimes: 'Format gambar harus JPEG atau PNG',
    maxSize: 'Ukuran gambar maksimal 5MB',
  },
  general: {
    uploadFailed: 'Gagal mengunggah gambar',
    updateFailed: 'Gagal memperbarui slide',
    deleteFailed: 'Gagal menghapus slide',
  },
} as const;

// Success Messages
export const CAROUSEL_MESSAGES = {
  uploaded: 'Slide carousel berhasil ditambahkan!',
  updated: 'Slide carousel berhasil diperbarui!',
  deleted: 'Slide carousel berhasil dihapus!',
} as const;

// Carousel Slide Status
export const CAROUSEL_STATUS = {
  ACTIVE: 'Aktif',
  INACTIVE: 'Non-aktif',
} as const;

// Badge Colors
export const CAROUSEL_COLORS = {
  active: '#10b981', // Emerald green
  inactive: '#64748b', // Slate
} as const;
