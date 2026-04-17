import {
    CATALOG_ALLOWED_IMAGE_TYPES,
    CATALOG_MAX_IMAGE_FILE_SIZE_BYTES,
    CATALOG_MAX_SIZE,
    CATALOG_MIN_SIZE,
} from '@/constants/Admin/katalog';
import type { ApiRequestError } from '@/types/Admin/katalog';
import type {
    CatalogAdminItem,
    CatalogImageItem,
    CatalogStatus,
} from '@/types/catalog';

export const parseCatalogSizes = (raw: string): number[] => {
    return Array.from(
        new Set(
            raw
                .split(',')
                .map((part) => Number(part.trim()))
                .filter(
                    (value) =>
                        Number.isInteger(value) &&
                        value >= CATALOG_MIN_SIZE &&
                        value <= CATALOG_MAX_SIZE,
                ),
        ),
    ).sort((a, b) => a - b);
};

export const statusBadgeClassForCatalog = (status: CatalogStatus): string => {
    if (status === 'habis') {
        return 'bg-sumi text-washi shadow-sm';
    }

    return 'bg-matcha text-washi shadow-sm';
};

export const formatCatalogCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

export const orderedCatalogImages = (
    catalog: CatalogAdminItem,
): CatalogImageItem[] => {
    return [...catalog.images].sort((a, b) => a.position - b.position);
};

export const isValidCatalogImageFile = (file: File): boolean => {
    const isValidType = CATALOG_ALLOWED_IMAGE_TYPES.includes(file.type);
    const isValidSize = file.size <= CATALOG_MAX_IMAGE_FILE_SIZE_BYTES;

    return isValidType && isValidSize;
};

export const normalizeCatalogApiError = (
    error: unknown,
    fallback: string,
): string => {
    const apiError = error as ApiRequestError;

    if (apiError.payload?.errors) {
        const firstError = Object.values(apiError.payload.errors)[0]?.[0];

        if (firstError) {
            return firstError;
        }
    }

    return apiError.payload?.message || fallback;
};
