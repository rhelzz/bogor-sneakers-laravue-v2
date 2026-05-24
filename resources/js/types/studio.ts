export interface Layer {
    id: number;
    file: string;
}

export interface PreviewZone {
    x: number;
    y: number;
    width: number;
    height: number;
}

export interface PatternZone {
    id: string;
    x: number;
    y: number;
    width: number;
    height: number;
    flip_x?: boolean;
    rotation?: number;
}

export interface StudioConfig {
    preview_zone: PreviewZone;
    pattern_zones: PatternZone[];
}

export interface CatalogModel {
    id: number | string;
    label: string;
    preview_base_file?: string;
    main_file?: string;
    pattern_base_file?: string;
    layers: Layer[];
    pattern_layers: Layer[];
    studio_config?: StudioConfig | null;
}

export interface CatalogFolder {
    key: string;
    label: string;
    model_count: number;
    models: CatalogModel[];
}

export interface LayerOutline {
    active: boolean;
    color: string;
    size: number;
}

export interface DesignElement {
    id: string;
    type: 'text' | 'image';
    x?: number;
    y?: number;
    width?: number;
    height?: number;
    rotation?: number;
    scaleX?: number;
    scaleY?: number;
    draggable?: boolean;
    
    // Text specific
    text?: string;
    fontSize?: number;
    fontFamily?: string;
    color?: string; // Changed from fill to match implementation
    strokeColor?: string; // Changed from stroke to match implementation
    strokeWidth?: number;
    
    // Image specific
    sourceId?: string;
    originalImageSrc?: string;
    outline?: LayerOutline;

    // Masking & Positioning specific
    maskId?: number | string | null;
    mx?: number;
    my?: number;
    mrot?: number;

    // Mirror (zona pola) — true = muncul di semua zona, false = pilih sisi
    mirror?: boolean;
    mirrorSide?: 'left' | 'right'; // hanya aktif saat mirror === false
}

export interface DesignState {
    colors: Record<number, string>;
    outlines: Record<number, LayerOutline>;
    elements: any[]; 
}

export interface StudioAssetCatalog {
    generated_at: string;
    folders: CatalogFolder[];
}
