/**
 * Carousel Home Helper Utilities
 * Helper functions untuk format data, validasi, dan operasi umum
 */

import { CAROUSEL_VALIDATION, CAROUSEL_COLORS, CAROUSEL_STATUS, CAROUSEL_ERRORS } from '@/constants/Admin/carouselConstants';

/**
 * Validasi ukuran file image
 */
export const validateImageSize = (file: File): boolean => {
  const fileSizeInKB = file.size / 1024;

  return fileSizeInKB <= CAROUSEL_VALIDATION.image.maxSize;
};

/**
 * Validasi tipe MIME image
 */
export const validateImageType = (file: File): boolean => {
  return (CAROUSEL_VALIDATION.image.allowedMimes as readonly string[]).includes(file.type);
};

/**
 * Validasi ekstension file
 */
export const validateImageExtension = (fileName: string): boolean => {
  if (!fileName) {
    return false;
  }

  const ext = fileName.split('.').pop()?.toLowerCase() || '';
  const allowedExtensions = CAROUSEL_VALIDATION.image.allowedExtensions as readonly string[];

  return allowedExtensions.includes(ext);
};

/**
 * Validasi lengkap untuk file image
 */
export const validateImage = (file: File): {
  valid: boolean;
  errors: string[];
} => {
  const errors: string[] = [];

  if (!file) {
    return { valid: false, errors: [CAROUSEL_ERRORS.image.required] };
  }

  if (!validateImageType(file)) {
    errors.push(CAROUSEL_ERRORS.image.mimes);
  }

  if (!validateImageSize(file)) {
    errors.push(CAROUSEL_ERRORS.image.maxSize);
  }

  if (!validateImageExtension(file.name)) {
    errors.push('Ekstensi file tidak didukung');
  }

  return {
    valid: errors.length === 0,
    errors,
  };
};


/**
 * Format nomor urutan dengan padding
 */
export const formatOrderNumber = (order: number): string => {
  return `#${String(order).padStart(2, '0')}`;
};

/**
 * Get badge color berdasarkan status
 */
export const getStatusColor = (isActive: boolean): string => {
  return isActive ? CAROUSEL_COLORS.active : CAROUSEL_COLORS.inactive;
};

/**
 * Get status label
 */
export const getStatusLabel = (isActive: boolean): string => {
  return isActive ? CAROUSEL_STATUS.ACTIVE : CAROUSEL_STATUS.INACTIVE;
};

/**
 * Format file size untuk display
 */
export const formatFileSize = (bytes: number): string => {
  if (bytes === 0) {
    return '0 Bytes';
  }

  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));

  return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
};

/**
 * Get file name dari path
 */
export const getFileNameFromPath = (path: string): string => {
  return path.split('/').pop() || '';
};

/**
 * Format tanggal untuk display
 */
export const formatDate = (date: string | Date): string => {
  const d = new Date(date);

  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};
