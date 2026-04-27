import katalogRoutes from '@/routes/admin/katalog';
import type {
    CatalogDeleteResponse,
    CatalogImageDeleteResponse,
    CatalogImageReorderResponse,
    CatalogImageUploadResponse,
    CatalogUpsertPayload,
    CatalogUpsertResponse,
} from '@/types/Admin/katalog';

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

export const createAdminCatalogApiService = () => {
    const csrfToken = resolveCsrfToken();

    const upsertCatalog = async (
        catalogId: number | null,
        payload: CatalogUpsertPayload,
    ): Promise<CatalogUpsertResponse> => {
        if (catalogId) {
            return requestJson<CatalogUpsertResponse>(
                katalogRoutes.update.url({ catalog: catalogId }),
                csrfToken,
                {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                },
            );
        }

        return requestJson<CatalogUpsertResponse>(
            katalogRoutes.store.url(),
            csrfToken,
            {
                method: 'POST',
                body: JSON.stringify(payload),
            },
        );
    };

    const deleteCatalog = (catalogId: number): Promise<CatalogDeleteResponse> => {
        return requestJson<CatalogDeleteResponse>(
            katalogRoutes.destroy.url({ catalog: catalogId }),
            csrfToken,
            {
                method: 'DELETE',
            },
        );
    };

    const uploadCatalogImage = async (
        catalogId: number,
        file: File,
        kind: 'card' | 'preview' = 'preview',
    ): Promise<CatalogImageUploadResponse> => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('kind', kind);

        return requestJson<CatalogImageUploadResponse>(
            katalogRoutes.images.store.url({ catalog: catalogId }),
            csrfToken,
            {
                method: 'POST',
                body: formData,
            },
        );
    };

    const deleteCatalogImage = (
        catalogId: number,
        imageId: number,
    ): Promise<CatalogImageDeleteResponse> => {
        return requestJson<CatalogImageDeleteResponse>(
            katalogRoutes.images.destroy.url({
                catalog: catalogId,
                catalogImage: imageId,
            }),
            csrfToken,
            {
                method: 'DELETE',
            },
        );
    };

    const reorderCatalogImages = (
        catalogId: number,
        imageIds: number[],
    ): Promise<CatalogImageReorderResponse> => {
        return requestJson<CatalogImageReorderResponse>(
            katalogRoutes.images.reorder.url({ catalog: catalogId }),
            csrfToken,
            {
                method: 'PUT',
                body: JSON.stringify({ image_ids: imageIds }),
            },
        );
    };

    return {
        upsertCatalog,
        deleteCatalog,
        uploadCatalogImage,
        deleteCatalogImage,
        reorderCatalogImages,
    };
};
