import type { PreOrderAssignmentPayload } from '@/types/Admin/preOrderHome';

export const INITIAL_PREORDER_PAYLOAD: PreOrderAssignmentPayload = {
    catalog_id: '',
    status: 'buka',
    is_visible: true,
    max_slots: 50,
    filled_slots: 0,
    countdown_ends_at: '',
    batch_label: '',
};

export const PREORDER_STATUS_OPTIONS = [
    { value: 'buka', label: 'Buka (Open)' },
    { value: 'hampir_habis', label: 'Hampir Habis' },
    { value: 'habis', label: 'Habis (Closed)' },
] as const;

export const VISIBLE_LIMIT = 5;