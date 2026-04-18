import type { CarouselSlide } from '@/types/carousel';

export type CarouselApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

export type CarouselApiRequestError = {
    status: number;
    payload: CarouselApiErrorResponse | null;
};

export interface CarouselUploadFormState {
    image: File | null;
}

export interface CarouselSlideResponse {
    message: string;
    slide: CarouselSlide;
}

export interface CarouselDeleteResponse {
    message: string;
    id: number;
}
