export type CatalogStatus = 'ready' | 'po' | 'habis'

export interface CatalogImageItem {
    id: number
    image_path: string
    image_url: string
    position: number
}

export interface CatalogPublicItem {
    id: number
    route_key: string
    public_id: string
    slug: string
    code: string
    name: string
    brand: string
    brandLabel: string
    collection: string
    collectionLabel: string
    description: string | null
    price: number
    status: CatalogStatus
    sizes: number[]
    popularity: number
    card_image_url: string | null
    image_url: string | null
    preorder_min_days: number | null
    preorder_max_days: number | null
    created_at: string | null
}

export interface CatalogDetailItem extends CatalogPublicItem {
    images: CatalogImageItem[]
}

export interface CatalogAdminItem {
    id: number
    public_id: string
    route_key: string
    slug: string
    name: string
    code: string
    brand: string
    collection: string
    card_image_path: string | null
    card_image_url: string | null
    description: string | null
    price: number
    status: CatalogStatus
    preorder_min_days: number | null
    preorder_max_days: number | null
    discount_type: string | null
    discount_value: number | null
    discount_starts_at: string | null
    discount_ends_at: string | null
    popularity_score: number
    is_active: boolean
    sort_order: number
    sizes: number[]
    images: CatalogImageItem[]
    created_at: string | null
    updated_at: string | null
}
