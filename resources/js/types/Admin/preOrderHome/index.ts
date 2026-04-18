export type PreOrderStatus = 'buka' | 'hampir_habis' | 'habis';

export interface CatalogOption {
    id: number;
    name: string;
    code: string;
    brand: string;
    collection: string;
    size_range: string;
    image_url: string | null;
    is_assigned: boolean;
    assignment_id: number | null;
}

export interface AssignmentCatalog {
    id: number;
    name: string;
    code: string;
    brand: string;
    collection: string;
    size_range: string;
    image_url: string | null;
    route_key: string;
}

export interface PreOrderAssignment {
    id: number;
    catalog_id: number;
    status: PreOrderStatus;
    status_label: string;
    is_visible: boolean;
    max_slots: number;
    filled_slots: number;
    remaining_slots: number;
    progress: number;
    countdown_ends_at: string | null;
    batch_label: string | null;
    urutan: number;
    catalog: AssignmentCatalog | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface PreOrderFormState {
    id: number | null;
    catalog_id: number | null;
    status: PreOrderStatus;
    is_visible: boolean;
    max_slots: number;
    filled_slots: number;
    countdown_ends_at: string;
    batch_label: string | null;
    urutan: number;
}

export interface PreOrderAssignmentPayload {
    catalog_id: number;
    status: PreOrderStatus;
    is_visible: boolean;
    max_slots: number;
    filled_slots: number;
    countdown_ends_at: string | null;
    batch_label: string | null;
    urutan: number;
}

export interface ApiErrorResponse {
    message?: string;
    errors?: Record<string, string[]>;
}

export type ApiRequestError = {
    status: number;
    payload: ApiErrorResponse | null;
};

export interface ApiUpsertResponse {
    message: string;
    assignment: PreOrderAssignment;
    visible_count: number;
}

export interface ApiDeleteResponse {
    message: string;
    id: number;
    visible_count: number;
}
