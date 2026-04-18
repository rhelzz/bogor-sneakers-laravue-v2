import galeriKaryaRoutes from '@/routes/admin/galeri-karya';
import type {
    GaleriKaryaMetadataPayload,
    GaleriKaryaSlotResponse,
} from '@/types/Admin/galeriKarya';

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

export const createAdminGaleriKaryaApiService = () => {
    const csrfToken = resolveCsrfToken();

    const replaceSlotImage = async (
        slotId: number,
        file: File,
    ): Promise<GaleriKaryaSlotResponse> => {
        const formData = new FormData();
        formData.append('image', file);

        return requestJson<GaleriKaryaSlotResponse>(
            galeriKaryaRoutes.replaceImage.url({ gallerySlot: slotId }),
            csrfToken,
            {
                method: 'POST',
                body: formData,
            },
        );
    };

    const updateSlotMetadata = (
        slotId: number,
        payload: GaleriKaryaMetadataPayload,
    ): Promise<GaleriKaryaSlotResponse> => {
        return requestJson<GaleriKaryaSlotResponse>(
            galeriKaryaRoutes.update.url({ gallerySlot: slotId }),
            csrfToken,
            {
                method: 'PUT',
                body: JSON.stringify(payload),
            },
        );
    };

    return {
        replaceSlotImage,
        updateSlotMetadata,
    };
};
