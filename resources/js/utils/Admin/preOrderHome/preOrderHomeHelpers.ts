import type {
    ApiRequestError,
    PreOrderStatus,
} from '@/types/Admin/preOrderHome';

export const toSafePreOrderUrutan = (value: unknown): number => {
    const parsed = Number(value);

    if (!Number.isInteger(parsed) || parsed < 1) {
        return 1;
    }

    return parsed;
};

export const toLocalPreOrderDatetimeInput = (
    isoValue?: string | null,
): string => {
    if (!isoValue) {
        return '';
    }

    const date = new Date(isoValue);

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    const pad = (value: number) => String(value).padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
};

export const toIsoFromLocalPreOrderDatetime = (
    localValue: string,
): string | null => {
    const date = new Date(localValue);

    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date.toISOString();
};

export const formatPreOrderDateTime = (isoValue?: string | null): string => {
    if (!isoValue) {
        return '-';
    }

    const date = new Date(isoValue);

    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

export const statusBadgeClassForPreOrder = (status: PreOrderStatus): string => {
    if (status === 'habis') {
        return 'bg-sumi text-washi shadow-sm';
    }

    if (status === 'hampir_habis') {
        return 'bg-amber-100 text-amber-700 shadow-sm';
    }

    return 'bg-matcha text-washi shadow-sm';
};

export const normalizePreOrderApiError = (
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
