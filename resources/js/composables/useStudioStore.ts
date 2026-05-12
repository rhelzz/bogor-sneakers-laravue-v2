import { ref, computed, reactive } from 'vue';
import type { CatalogFolder, CatalogModel, LayerOutline, DesignElement } from '../types/studio';

// Constants
export const ORIGIN_ID = 8137;

// Central State
const catalogFolders = ref<CatalogFolder[]>([]);
const catalogLoading = ref(false);
const activeFolderKey = ref('');
const currentModel = ref<number | string | null>(null);

const layerColors = ref<Record<number, string>>({});
const layerOutlines = ref<Record<number, LayerOutline>>({});
const uploadedMedia = ref<any[]>([]);
const activeElement = ref<DesignElement | null>(null);

const activeSideTab = ref('layers');
const toastMessage = ref('');
const isSaving = ref(false);

// Checkout State
const checkoutForm = reactive({
    name: '',
    phone: '',
    email: '',
    address: '',
    addressDetail: '',
    destinationId: null as number | null,
    courier: '',
    availableCouriers: [] as any[],
    shippingCost: 0,
    isCalculatingShipping: false,
    shoeSize: '',
    fastTrackEnabled: false,
    customBoxEnabled: false,
    formTouched: false
});

export function useStudioStore() {
    const activeFolder = computed(() => 
        catalogFolders.value.find(f => f.key === activeFolderKey.value)
    );

    const availableModels = computed(() => 
        activeFolder.value?.models || []
    );

    const currentModelMeta = computed(() => 
        availableModels.value.find(m => m.id === currentModel.value)
    );

    const showToast = (msg: string) => {
        toastMessage.value = msg;
        setTimeout(() => {
            toastMessage.value = '';
        }, 3000);
    };

    const resetDesignState = () => {
        layerColors.value = {};
        layerOutlines.value = {};
        // Elements will be handled by KonvaRenderer
    };

    return {
        // Constants
        ORIGIN_ID,

        // State
        catalogFolders,
        catalogLoading,
        activeFolderKey,
        currentModel,
        layerColors,
        layerOutlines,
        uploadedMedia,
        activeElement,
        activeSideTab,
        toastMessage,
        isSaving,
        checkoutForm,

        // Computed
        activeFolder,
        availableModels,
        currentModelMeta,

        // Actions
        showToast,
        resetDesignState
    };
}
