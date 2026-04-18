import preOrderHomeRoutes from '@/routes/admin/pre-order-home';
import type {
    ApiDeleteResponse,
    ApiUpsertResponse,
    PreOrderAssignmentPayload,
} from '@/types/Admin/preOrderHome';

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

export const createAdminPreOrderHomeApiService = () => {
    const csrfToken = resolveCsrfToken();

    const createAssignment = (
        payload: PreOrderAssignmentPayload,
    ): Promise<ApiUpsertResponse> => {
        return requestJson<ApiUpsertResponse>(
            preOrderHomeRoutes.store.url(),
            csrfToken,
            {
                method: 'POST',
                body: JSON.stringify(payload),
            },
        );
    };

    const updateAssignment = (
        assignmentId: number,
        payload: PreOrderAssignmentPayload,
    ): Promise<ApiUpsertResponse> => {
        return requestJson<ApiUpsertResponse>(
            preOrderHomeRoutes.update.url({ homePreorder: assignmentId }),
            csrfToken,
            {
                method: 'PUT',
                body: JSON.stringify(payload),
            },
        );
    };

    const deleteAssignment = (
        assignmentId: number,
    ): Promise<ApiDeleteResponse> => {
        return requestJson<ApiDeleteResponse>(
            preOrderHomeRoutes.destroy.url({ homePreorder: assignmentId }),
            csrfToken,
            {
                method: 'DELETE',
            },
        );
    };

    return {
        createAssignment,
        updateAssignment,
        deleteAssignment,
    };
};
