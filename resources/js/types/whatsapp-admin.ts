export type WhatsappAdminColorKey =
    | 'matcha'
    | 'indigo'
    | 'sakura'
    | 'amber'
    | 'sky'
    | 'slate';

export type WhatsappAdminItem = {
    id: number;
    name: string;
    role: string;
    phone: string;
    initial: string;
    color: string;
    color_key: WhatsappAdminColorKey;
    is_active: boolean;
    sort_order: number;
    created_at: string | null;
    updated_at: string | null;
};

export type WhatsappColorOption = {
    key: WhatsappAdminColorKey;
    label: string;
    class: string;
};
