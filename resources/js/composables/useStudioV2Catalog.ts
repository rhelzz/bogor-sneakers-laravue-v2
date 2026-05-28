import { ref, readonly } from 'vue';
import { buildCatalogFromApi } from '@/data/studio-v2-catalog';
import type { ShoeTypeCatalog, ApiShoeType } from '@/data/studio-v2-catalog';

export function useStudioV2Catalog() {
    const catalogs = ref<ShoeTypeCatalog[]>([]);
    const isLoading = ref(true);
    const error = ref<string | null>(null);

    const load = async () => {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await fetch('/api/studio-custom-v2/catalog');
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data: { shoe_types: ApiShoeType[] } = await res.json();
            catalogs.value = data.shoe_types.map(buildCatalogFromApi);
        } catch (e) {
            error.value = 'Gagal memuat catalog. Coba refresh halaman.';
            console.error('[V2 Catalog]', e);
        } finally {
            isLoading.value = false;
        }
    };

    return {
        catalogs: readonly(catalogs),
        isLoading: readonly(isLoading),
        error: readonly(error),
        load,
    };
}
