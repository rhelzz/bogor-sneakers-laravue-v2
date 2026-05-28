// ─── Types ────────────────────────────────────────────────────────────────────

/** Raw shape returned by /api/studio-custom-v2/catalog */
export interface ApiShoeType {
    id: string;
    label: string;
    base_path: string;
    models: Record<number, {
        accent_slots: number[];
        pola_slots: number[];
    }>;
}

export interface ShoeTypeCatalog {
    id: string;
    label: string;
    basePath: string;
    totalModels: number;
    /** model → sorted array of available accent slot numbers (exact files on disk) */
    accentSlots: Record<number, number[]>;
    /** model → sorted array of available pola slot numbers (exact files on disk) */
    polaSlots: Record<number, number[]>;
    /** All unique accent slot numbers across all models, sorted */
    allAccentSlots: number[];
    /** slot → default hex color */
    defaultColors: Record<number, string>;
    baseSvg: (m: number) => string;
    overlaySvg: (m: number) => string;
    aksenSvg: (m: number, slot: number) => string;
    polaBaseSvg: (m: number) => string;
    polaAksenSvg: (m: number, slot: number) => string;
    /** Returns sorted model numbers that have the given accent slot. */
    getAvailableModels: (slot: number) => number[];
}

// ─── Default colors (static, not from API) ───────────────────────────────────

const DEFAULT_COLORS: Record<number, string> = {
    1: '#C8A96E',
    2: '#8B7355',
    3: '#D4C5A0',
    4: '#6B8E6B',
    5: '#8B6B6B',
    6: '#7B8B9B',
    7: '#9B8B7B',
    8: '#E8E0D0',
    9: '#A09080',
    10: '#B09070',
    11: '#7B9B8B',
    12: '#9B8BAA',
    13: '#C8B090',
    14: '#8B9BAA',
};

// ─── Factory ─────────────────────────────────────────────────────────────────

export function buildCatalogFromApi(data: ApiShoeType): ShoeTypeCatalog {
    const basePath = data.base_path;
    const p = (f: string) => `${basePath}/${f}`;

    const accentSlots: Record<number, number[]> = {};
    const polaSlots: Record<number, number[]> = {};

    for (const [mStr, info] of Object.entries(data.models)) {
        const m = Number(mStr);
        accentSlots[m] = info.accent_slots;
        polaSlots[m] = info.pola_slots;
    }

    const allAccentSlots = [...new Set(Object.values(accentSlots).flat())]
        .sort((a, b) => a - b);

    const getAvailableModels = (slot: number): number[] =>
        Object.entries(accentSlots)
            .filter(([, slots]) => slots.includes(slot))
            .map(([m]) => Number(m))
            .sort((a, b) => a - b);

    return {
        id: data.id,
        label: data.label,
        basePath,
        totalModels: Object.keys(data.models).length,
        accentSlots,
        polaSlots,
        allAccentSlots,
        defaultColors: DEFAULT_COLORS,
        baseSvg: (m) => p(`m${m}_base.svg`),
        overlaySvg: (m) => p(`m${m}_overlay.svg`),
        aksenSvg: (m, slot) => p(`m${m}_aksen${slot}.svg`),
        polaBaseSvg: (m) => p(`m${m}_pola_base.svg`),
        polaAksenSvg: (m, slot) => p(`m${m}_pola_aksen${slot}.svg`),
        getAvailableModels,
    };
}
