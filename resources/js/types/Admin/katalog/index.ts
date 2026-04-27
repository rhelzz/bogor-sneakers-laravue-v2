import type {
    CatalogAdminItem,
    CatalogImageItem,
    CatalogStatus,
} from '@/types/catalog';

export type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

export type ApiRequestError = {
    status: number;
    payload: ApiErrorResponse | null;
};

export interface CatalogFormState {
    id: number | null;
    name: string;
    code: string;
    brand: string;
    collection: string;
    description: string;
    price: number;
    status: CatalogStatus;
    preorder_min_days: number | null;
    preorder_max_days: number | null;
    popularity_score: number;
    sort_order: number;
    is_active: boolean;
    sizes_raw: string;
}

export interface CatalogUpsertPayload {
    name: string;
    code: string;
    brand: string;
    collection: string;
    description: string | null;
    price: number;
    status: CatalogStatus;
    preorder_min_days: number | null;
    preorder_max_days: number | null;
    popularity_score: number;
    sort_order: number;
    is_active: boolean;
    sizes: number[];
}

export interface CatalogUpsertResponse {
    message: string;
    catalog: CatalogAdminItem;
}

export interface CatalogDeleteResponse {
    message: string;
    id: number;
}

export interface CatalogPreviewImageUploadResponse {
    message: string;
    kind: 'preview';
    image: CatalogImageItem;
    catalog_id: number;
}

export interface CatalogCardImageUploadResponse {
    message: string;
    kind: 'card';
    catalog_id: number;
    card_image_path: string | null;
    card_image_url: string | null;
}

export type CatalogImageUploadResponse =
    | CatalogPreviewImageUploadResponse
    | CatalogCardImageUploadResponse;

export interface CatalogImageDeleteResponse {
    message: string;
    id: number;
}

export interface CatalogImageReorderResponse {
    message: string;
    images: CatalogImageItem[];
}
