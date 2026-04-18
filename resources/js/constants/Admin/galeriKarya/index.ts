export const GALERI_KARYA_ALLOWED_IMAGE_TYPES = [
    'image/jpeg',
    'image/png',
];

export const GALERI_KARYA_MAX_IMAGE_FILE_SIZE_BYTES = 5 * 1024 * 1024;
export const GALERI_KARYA_DEFAULT_AUTHOR = '@bogorsneakers';

export const GALERI_KARYA_ASPECT_CLASS_MAP: Record<number, string> = {
    1: 'aspect-[3/4]',
    2: 'aspect-square',
    3: 'aspect-[4/5]',
    4: 'aspect-[3/4]',
    5: 'aspect-square',
    6: 'aspect-[4/3]',
    7: 'aspect-[3/4]',
    8: 'aspect-square',
};

export const GALERI_KARYA_TITLE_DEFAULTS: Record<number, string> = {
    1: 'Air Max 97 x Jogja Streets',
    2: 'Samba OG - Bogor Vibe',
    3: 'Jordan 1 Bred - Cold Day',
    4: 'NB 574 Navy x Rain',
    5: 'Vans Old Skool',
    6: 'Converse Chuck 70',
    7: 'Asics Gel-Kayano',
    8: 'Puma RS-X Effekt',
};
