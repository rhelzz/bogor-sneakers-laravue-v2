export interface GallerySlot {
    id: number;
    slot: number;
    image_path: string | null;
    image_url: string | null;
    title: string | null;
    author: string | null;
    updated_at?: string;
}
