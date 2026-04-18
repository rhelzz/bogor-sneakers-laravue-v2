import carouselHomeRoutes from '@/routes/admin/carousel-home';
import type {
    CarouselDeleteResponse,
    CarouselSlideResponse,
} from '@/types/Admin/carouselHome';

const resolveCsrfToken = (): string => {
    return (
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? ''
    );
};

const buildHeaders = (
    isJsonBody: boolean,
    csrfToken: string,
): Record<string, string> => {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    if (isJsonBody) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const requestJson = async <T,>(
    url: string,
    csrfToken: string,
    init: RequestInit = {},
): Promise<T> => {
    const isFormData = init.body instanceof FormData;
    const response = await fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            ...buildHeaders(!isFormData, csrfToken),
            ...(init.headers ?? {}),
        },
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        throw {
            status: response.status,
            payload,
        };
    }

    return payload as T;
};

export const createAdminCarouselHomeApiService = () => {
    const csrfToken = resolveCsrfToken();

    const uploadSlide = async (image: File): Promise<CarouselSlideResponse> => {
        const formData = new FormData();
        formData.append('image', image);

        return requestJson<CarouselSlideResponse>(
            carouselHomeRoutes.store.url(),
            csrfToken,
            {
                method: 'POST',
                body: formData,
            },
        );
    };

    const updateSlideStatus = (
        slideId: number,
        isActive: boolean,
    ): Promise<CarouselSlideResponse> => {
        return requestJson<CarouselSlideResponse>(
            carouselHomeRoutes.update.url({ carouselSlide: slideId }),
            csrfToken,
            {
                method: 'PUT',
                body: JSON.stringify({ is_active: isActive }),
            },
        );
    };

    const deleteSlide = (slideId: number): Promise<CarouselDeleteResponse> => {
        return requestJson<CarouselDeleteResponse>(
            carouselHomeRoutes.destroy.url({ carouselSlide: slideId }),
            csrfToken,
            {
                method: 'DELETE',
            },
        );
    };

    return {
        uploadSlide,
        updateSlideStatus,
        deleteSlide,
    };
};
