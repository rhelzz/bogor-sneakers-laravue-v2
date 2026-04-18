import {
    GALERI_KARYA_ALLOWED_IMAGE_TYPES,
    GALERI_KARYA_ASPECT_CLASS_MAP,
    GALERI_KARYA_DEFAULT_AUTHOR,
    GALERI_KARYA_MAX_IMAGE_FILE_SIZE_BYTES,
    GALERI_KARYA_TITLE_DEFAULTS,
} from '@/constants/Admin/galeriKarya';
import type { GaleriKaryaApiRequestError } from '@/types/Admin/galeriKarya';

export const getGaleriKaryaAspectClass = (slot: number): string => {
    return GALERI_KARYA_ASPECT_CLASS_MAP[slot] ?? 'aspect-square';
};

export const getGaleriKaryaFileInputId = (slot: number): string => {
    return `gallery-slot-input-${slot}`;
};

export const getDefaultGaleriKaryaTitle = (slot: number): string => {
    return GALERI_KARYA_TITLE_DEFAULTS[slot] ?? `Galeri Karya Slot ${slot}`;
};

export const normalizeGaleriKaryaAuthorInput = (
    author?: string | null,
): string => {
    const trimmed = (author ?? '').trim();

    if (trimmed === '') {
        return GALERI_KARYA_DEFAULT_AUTHOR;
    }

    if (trimmed.startsWith('@')) {
        return trimmed;
    }

    return `@${trimmed}`;
};

export const formatGaleriKaryaUpdatedAt = (value?: string): string => {
    if (!value) {
        return 'Belum pernah diupdate';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Belum pernah diupdate';
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

export const validateGaleriKaryaFile = (file: File): string => {
    if (!GALERI_KARYA_ALLOWED_IMAGE_TYPES.includes(file.type)) {
        return 'Format file harus JPEG atau PNG.';
    }

    if (file.size > GALERI_KARYA_MAX_IMAGE_FILE_SIZE_BYTES) {
        return 'Ukuran gambar maksimal 5MB.';
    }

    return '';
};

export const normalizeGaleriKaryaApiError = (
    error: unknown,
    fallback: string,
    prioritizedFields: string[] = [],
): string => {
    const apiError = error as GaleriKaryaApiRequestError;

    for (const field of prioritizedFields) {
        const fieldError = apiError.payload?.errors?.[field]?.[0];

        if (fieldError) {
            return fieldError;
        }
    }

    return apiError.payload?.message || fallback;
};
