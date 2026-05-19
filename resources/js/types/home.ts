export type HeroCarouselSlide = {
    id: number;
    image_url: string;
    is_active: boolean;
    title?: string;
    desc?: string;
    icon?: string;
};

export type HomePreorderStatus = 'BUKA' | 'HAMPIR HABIS' | 'HABIS';

export type HomePreorderItem = {
    id: number;
    catalog_id: number;
    product: string;
    sku: string;
    size: string;
    batch: string;
    progress: number;
    remaining_slots: number;
    max_slots: number;
    filled_slots: number;
    status: HomePreorderStatus;
    countdown_ends_at: string | null;
};

export type HomeLatestCollectionItem = {
    id: number;
    name: string;
    size: string;
    price: number;
    status: 'PO' | 'Ready' | 'Habis';
    statusClass: string;
    brand: string;
    brand_label: string;
    image_url: string | null;
};
