import type { GallerySlot } from '@/types/gallery';

export type GaleriKaryaApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

export type GaleriKaryaApiRequestError = {
    status: number;
    payload: GaleriKaryaApiErrorResponse | null;
};

export interface GaleriKaryaSlotResponse {
    message: string;
    slot: GallerySlot;
}

export interface GaleriKaryaMetadataForm {
    title: string;
    author: string;
}

export interface GaleriKaryaMetadataPayload {
    title: string;
    author: string;
}
