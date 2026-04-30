export interface CatalogOption {
  id: number;
  name: string;
  code: string;
  brand: string;
  collection: string;
  size_range: string;
  image_url: string;
  is_assigned: boolean;
  assignment_id: number | null;
}

export interface AdminAssignment {
  id: number;
  catalog_id: number;
  status: string;
  status_label: string;
  is_visible: boolean;
  max_slots: number;
  filled_slots: number;
  remaining_slots: number;
  progress: number;
  countdown_ends_at: string | null;
  batch_label: string;
  urutan: number;
  catalog: {
    id: number;
    name: string;
    code: string;
    brand: string;
    collection: string;
    size_range: string;
    image_url: string;
    route_key: string;
  } | null;
  created_at: string | null;
  updated_at: string | null;
}

export interface PreOrderAssignmentPayload {
  catalog_id: number | '';
  status: string;
  is_visible: boolean;
  max_slots: number;
  filled_slots: number;
  countdown_ends_at: string;
  batch_label: string;
  urutan?: number;
}

export interface ApiUpsertResponse {
  message: string;
  assignment: AdminAssignment;
  visible_count: number;
  errors?: Record<string, string[]>;
}

export interface ApiDeleteResponse {
  message: string;
  id: number;
  visible_count: number;
}