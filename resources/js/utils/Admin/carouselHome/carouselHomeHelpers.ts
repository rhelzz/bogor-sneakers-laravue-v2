import {
    CAROUSEL_ALLOWED_IMAGE_TYPES,
    CAROUSEL_MAX_IMAGE_FILE_SIZE_BYTES,
} from '@/constants/Admin/carouselHome';
import type { CarouselApiRequestError } from '@/types/Admin/carouselHome';

export const isAllowedCarouselImageType = (file: File): boolean => {
    return CAROUSEL_ALLOWED_IMAGE_TYPES.includes(file.type);
};

export const isAllowedCarouselImageSize = (file: File): boolean => {
    return file.size <= CAROUSEL_MAX_IMAGE_FILE_SIZE_BYTES;
};

export const normalizeCarouselApiError = (
    error: unknown,
    fallback: string,
): string => {
    const apiError = error as CarouselApiRequestError;

    return apiError.payload?.message || fallback;
};

export const normalizeCarouselImageUploadError = (
    error: unknown,
    fallback: string,
): string => {
    const apiError = error as CarouselApiRequestError;
    const imageError = apiError.payload?.errors?.image?.[0];

    return imageError || apiError.payload?.message || fallback;
};

export const getCarouselImageUrl = (imagePath: string): string => {
    if (!imagePath) {
        return '';
    }

    if (
        imagePath.startsWith('blob:') ||
        imagePath.startsWith('http://') ||
        imagePath.startsWith('https://') ||
        imagePath.startsWith('/')
    ) {
        return imagePath;
    }

    return `/storage/${imagePath}`;
};
