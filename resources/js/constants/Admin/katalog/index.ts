import type { CatalogStatus } from '@/types/catalog';

export const CATALOG_ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/png',
    'image/webp',
];

export const CATALOG_MAX_IMAGE_FILE_SIZE_BYTES = 5 * 1024 * 1024;
export const CATALOG_MIN_SIZE = 36;
export const CATALOG_MAX_SIZE = 50;
export const CATALOG_DEFAULT_STATUS: CatalogStatus = 'ready';
export const CATALOG_DEFAULT_PREORDER_MIN_DAYS = 14;
export const CATALOG_DEFAULT_PREORDER_MAX_DAYS = 21;
