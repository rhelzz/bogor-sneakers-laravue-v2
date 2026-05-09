<template>
    <div class="font-body-md text-body-md bg-background text-on-background min-h-screen flex flex-col overflow-hidden">
        <Head title="Studio Custom" />

        <StudioTopNav
            @undo="undo"
            @redo="redo"
            @reset="resetDesign"
            @checkout="validateCheckout"
        />

        <div class="flex-grow flex mt-20 h-[calc(100vh-80px)] relative">
            <StudioSideNav @share="showShare" />

            <main class="flex-grow flex relative bg-[#f8f9fa] overflow-hidden">
                <StudioCanvas
                    :is-syncing="isSyncing"
                    @init="handleInitStage"
                    @zoom-in="zoomIn"
                    @zoom-out="zoomOut"
                    @reset-zoom="resetZoom"
                    @remove-element="removeActiveElement"
                />

                <div class="h-full flex-shrink-0 relative z-30">
                    <transition name="panel-slide" mode="out-in">
                        <StudioToolbox
                            v-if="activeSideTab !== 'checkout'"
                            @remove-element="removeActiveElement"
                            @update-image-outline="handleImageOutlineUpdate"
                            @update-layer="updateLayer"
                            @save-history="saveToHistory"
                            @add-media="handleAddMedia"
                            @add-text="handleAddText"
                        />
                        <div v-else class="w-[340px] sm:w-[380px] md:w-[400px] flex-shrink-0 bg-white border-l border-indigo/5 flex flex-col h-full shadow-[-20px_0_40px_rgba(0,0,0,0.03)] relative z-30">
                            <CheckoutForm @checkout="handleFinalCheckout" />
                        </div>
                    </transition>
                </div>
            </main>
        </div>

        <AddonModals
            :show-fast-track="showFastTrackAlert"
            :show-custom-box="showCustomBoxAlert"
            @confirm-fast-track="confirmFastTrack"
            @confirm-custom-box="confirmCustomBox"
        />

        <transition name="toast">
            <div v-if="toastMessage" class="fixed bottom-12 left-1/2 z-[110] -translate-x-1/2 rounded-2xl bg-black px-6 py-4 text-[10px] font-black text-white shadow-2xl flex items-center gap-4 uppercase tracking-[0.2em] border border-white/10">
                <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_10px_#d0ff00]"></div>
                {{ toastMessage }}
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Konva from 'konva';
import { onMounted, onUnmounted, ref, watch } from 'vue';

// Components
import CheckoutForm from '../components/Studio/Checkout/CheckoutForm.vue';
import AddonModals from '../components/Studio/Modals/AddonModals.vue';
import StudioCanvas from '../components/Studio/StudioCanvas.vue';
import StudioSideNav from '../components/Studio/StudioSideNav.vue';
import StudioToolbox from '../components/Studio/StudioToolbox.vue';
import StudioTopNav from '../components/Studio/StudioTopNav.vue';

// Composables
import { useKonvaRenderer } from '../composables/useKonvaRenderer';
import { useStudioHistory } from '../composables/useStudioHistory';
import { useStudioStore } from '../composables/useStudioStore';
import type { DesignElement } from '../types/studio';

// Utils
import { loadImage, drawOutlineLayer } from '../utils/studio-utils';

const {
    catalogFolders,
    catalogLoading,
    activeFolderKey,
    currentModel,
    activeSideTab,
    toastMessage,
    showToast,
    activeElement,
    currentModelMeta,
    checkoutForm,
    uploadedMedia,
    isSaving,
    layerColors,
    layerOutlines
} = useStudioStore();

const { undo: undoHistory, redo: redoHistory, isRestoring, clearHistory } = useStudioHistory();

const {
    initStage,
    loadAssets,
    updateLayer,
    saveToHistory,
    zoomBy,
    resetView,
    createPreviewURL,
    createPatternURL,
    downloadURL,
    isSyncing,
    getStage,
    getMainLayer,
    getElementsGroup,
    getTransformer
} = useKonvaRenderer();

// UI Alerts State
const showFastTrackAlert = ref(false);
const showCustomBoxAlert = ref(false);

// Actions
const fetchCatalog = async () => {
    catalogLoading.value = true;

    try {
        const res = await fetch('/api/studio-custom/catalog?refresh=1');
        const data = await res.json();
        catalogFolders.value = data.folders;

        if (data.folders.length > 0) {
            activeFolderKey.value = data.folders[0].key;

            if (data.folders[0].models.length > 0) {
                currentModel.value = data.folders[0].models[0].id;
            }
        }
    } catch (err) {
        showToast('Gagal memuat katalog');
        console.error(err);
    } finally {
        catalogLoading.value = false;
    }
};

const handleInitStage = (container: HTMLDivElement) => {
    initStage(container);

    if (catalogFolders.value.length > 0) {
        loadAssets();
    }
};

const undo = async () => {
    const prevState = undoHistory();

    if (prevState) {
        await restoreState(prevState);
    } else {
        showToast('Tidak ada perubahan untuk di-undo');
    }
};

const redo = async () => {
    const nextState = redoHistory();

    if (nextState) {
        await restoreState(nextState);
    } else {
        showToast('Tidak ada perubahan untuk di-redo');
    }
};

const restoreState = async (state: any) => {
    isRestoring.value = true;

    try {
        layerColors.value = { ...state.colors };
        layerOutlines.value = JSON.parse(JSON.stringify(state.outlines));

        for (const id in state.colors) {
            updateLayer(Number(id));
        }

        const elementsGroup = getElementsGroup();
        const transformer = getTransformer();
        elementsGroup?.destroyChildren();
        transformer?.nodes([]);
        activeElement.value = null;

        for (const elData of state.elements) {
            let node: Konva.Node | null = null;

            if (elData.className === 'Text') {
                node = new Konva.Text(elData.attrs);
            } else if (elData.className === 'Image') {
                const attrs = { ...elData.attrs };

                if (attrs.imageSrc) {
                    attrs.image = await loadImage(attrs.imageSrc);
                }

                node = new Konva.Image(attrs);
            }

            if (node) {
                elementsGroup?.add(node as any);
            }
        }

        getMainLayer()?.draw();
    } finally {
        isRestoring.value = false;
    }
};

const resetDesign = () => {
    if (confirm('Reset semua perubahan?')) {
        clearHistory();
        loadAssets();
    }
};

const validateCheckout = () => {
    checkoutForm.formTouched = true;

    if (!checkoutForm.name || !checkoutForm.phone || !checkoutForm.email || !checkoutForm.shoeSize || !checkoutForm.address) {
        showToast('Mohon lengkapi data diri dan alamat');
        activeSideTab.value = 'checkout';

        return;
    }

    handleFinalCheckout();
};

const WHATSAPP_INTRO = "Halo Admin Bogor Sneakers, saya ingin memesan sepatu custom dengan rincian berikut:";

const handleFinalCheckout = async () => {
    if (isSaving.value) {
        return;
    }

    isSaving.value = true;

    try {
        const previewUrl = await createPreviewURL(checkoutForm);
        const patternUrl = await createPatternURL();

        const timestamp = new Date().getTime();
        const safeName = checkoutForm.name.replace(/\s+/g, '_') || 'GUEST';

        downloadURL(previewUrl, `PREVIEW_${safeName}_${timestamp}.png`);
        downloadURL(patternUrl, `POLA_${safeName}_${timestamp}.png`);

        const totalEst = 899000 + (checkoutForm.fastTrackEnabled ? 125000 : 0) + (checkoutForm.customBoxEnabled ? 65000 : 0);

        const waMessage = encodeURIComponent([
            WHATSAPP_INTRO,
            `Nama: ${checkoutForm.name}`,
            `WA: ${checkoutForm.phone}`,
            `Email: ${checkoutForm.email}`,
            `Model: ${activeFolderKey.value} - ${currentModelMeta.value?.label}`,
            `Size: ${checkoutForm.shoeSize}`,
            `Fast Track: ${checkoutForm.fastTrackEnabled ? 'Ya' : 'Tidak'}`,
            `Custom Box: ${checkoutForm.customBoxEnabled ? 'Ya' : 'Tidak'}`,
            `Total Est: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalEst)}`,
            `Alamat: ${checkoutForm.address}`
        ].join('\n'));

        window.open(`https://wa.me/6285511223344?text=${waMessage}`, '_blank');

        showToast('Order diproses & File terunduh!');
    } catch (err) {
        console.error(err);
        showToast('Gagal memproses pesanan');
    } finally {
        isSaving.value = false;
    }
};

const handleAddText = () => {
    const stage = getStage();
    const elementsGroup = getElementsGroup();

    if (!stage || !elementsGroup) {
        return;
    }

    const id = Math.random().toString(36).slice(2);
    const textNode = new Konva.Text({
        text: 'BOGOR SNEAKER',
        fontSize: 50,
        fontFamily: 'Lexend',
        fontStyle: '900',
        fill: '#000000',
        x: stage.width() / 2 - 100,
        y: stage.height() / 2,
        draggable: true,
        padding: 10
    });

    const meta: DesignElement = {
        id,
        type: 'text' as const,
        text: 'BOGOR SNEAKER',
        color: '#000000',
        fontFamily: 'Lexend',
        strokeColor: '#000000',
        strokeWidth: 0
    };
    textNode.setAttr('meta', meta);
    elementsGroup.add(textNode);
    activeElement.value = meta;
    getTransformer()?.nodes([textNode]);
    getMainLayer()?.draw();
    saveToHistory();
};

const handleAddMedia = async (id: string) => {
    const media = uploadedMedia.value.find(m => m.id === id);
    const stage = getStage();
    const elementsGroup = getElementsGroup();

    if (!media || !stage || !elementsGroup) {
        return;
    }

    try {
        const img = await loadImage(media.src);
        const width = 200;
        const height = width * (img.naturalHeight / img.naturalWidth);
        const konvaImg = new Konva.Image({
            image: img,
            x: stage.width() / 2 - width / 2,
            y: stage.height() / 2 - height / 2,
            width, height, draggable: true
        });

        const meta: DesignElement = { id: Math.random().toString(36).slice(2), type: 'image' as const, sourceId: id, originalImageSrc: media.src, outline: { active: false, color: '#ffffff', size: 3 }};
        konvaImg.setAttr('meta', meta);
        elementsGroup.add(konvaImg);
        activeElement.value = meta;
        getTransformer()?.nodes([konvaImg]);
        getMainLayer()?.draw();
        saveToHistory();
    } catch (err) {
        console.error(err);
        showToast('Gagal menambahkan gambar');
    }
};

const removeActiveElement = () => {
    const transformer = getTransformer();
    const node = transformer?.nodes()[0];

    if (node) {
        node.destroy();
        transformer?.nodes([]);
        activeElement.value = null;
        getMainLayer()?.draw();
        saveToHistory();
    }
};

const handleImageOutlineUpdate = async () => {
    const transformer = getTransformer();
    const node = transformer?.nodes()[0] as Konva.Image;

    if (node && node.getAttr('meta')?.type === 'image') {
        const meta = node.getAttr('meta');
        const img = await loadImage(meta.originalImageSrc);

        if (meta.outline.active) {
            const canvas = drawOutlineLayer(img, meta.outline.color, meta.outline.size, img.naturalWidth, img.naturalHeight);
            node.image(canvas);
        } else {
            node.image(img);
        }

        getMainLayer()?.draw();
    }
};

const confirmFastTrack = (val: boolean) => {
    checkoutForm.fastTrackEnabled = val;
    showFastTrackAlert.value = false;
    showCustomBoxAlert.value = true;
};

const confirmCustomBox = (val: boolean) => {
    checkoutForm.customBoxEnabled = val;
    showCustomBoxAlert.value = false;
};

const zoomIn = () => {
    zoomBy(1.4);
};

const zoomOut = () => {
    zoomBy(1 / 1.4);
};

const resetZoom = () => {
    resetView();
};

const showShare = () => showToast('Link share disalin ke clipboard');

// Lifecycle
onMounted(async () => {
    await fetchCatalog();
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});

const handleKeyDown = (e: KeyboardEvent) => {
    if ((e.key === 'Backspace' || e.key === 'Delete') && activeElement.value) {
        const target = e.target as HTMLElement;

        if (target.tagName !== 'INPUT' && target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            removeActiveElement();
        }
    }
};

watch(activeElement, (newEl) => {
    if (!newEl || isRestoring.value) {
        return;
    }

    const elementsGroup = getElementsGroup();

    if (!elementsGroup) {
        return;
    }

    const node = elementsGroup.findOne((n: any) => n.getAttr('meta')?.id === newEl.id);

    if (!node) {
        return;
    }

    if (newEl.type === 'text') {
        const textNode = node as Konva.Text;
        let changed = false;

        if (textNode.text() !== newEl.text) {
            textNode.text(newEl.text || ''); changed = true;
        }

        if (textNode.fontFamily() !== newEl.fontFamily) {
            textNode.fontFamily(newEl.fontFamily || 'Lexend'); changed = true;
        }

        if (textNode.fill() !== newEl.color) {
            textNode.fill(newEl.color || '#000000'); changed = true;
        }

        if (textNode.stroke() !== newEl.strokeColor) {
            textNode.stroke(newEl.strokeColor || null); changed = true;
        }

        if (textNode.strokeWidth() !== (newEl.strokeWidth || 0)) {
            textNode.strokeWidth(newEl.strokeWidth || 0); changed = true;
        }

        if (changed) {
            getMainLayer()?.draw();
            saveToHistory();
        }
    }
}, { deep: true });

watch([activeFolderKey, currentModel], () => {
    if (getStage()) {
        loadAssets();
    }
});
watch(activeSideTab, (newTab) => {
    if (newTab === 'checkout') {
        showFastTrackAlert.value = true;
    }
});
</script>

<style>
.panel-slide-enter-active, .panel-slide-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.panel-slide-enter-from { transform: translateX(100%); opacity: 0; }
.panel-slide-leave-to { transform: translateX(100%); opacity: 0; }

.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translate(-50%, 20px) scale(0.9); }

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
