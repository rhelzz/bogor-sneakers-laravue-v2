<template>
    <div class="font-body-md text-body-md bg-background text-on-background min-h-screen flex flex-col overflow-hidden">
        <Head title="Studio Custom" />

        <StudioTopNav
            @reset="resetDesign"
        />

        <div class="flex-grow flex mt-16 h-[calc(100vh-64px)] relative">
            <StudioSideNav />

            <main class="flex-grow flex relative bg-[#f8f9fa] overflow-hidden">
                <StudioCanvas
                    :is-syncing="isSyncing"
                    @init="handleInitStage"
                    @remove-element="removeActiveElement"
                />

                <!-- Desktop: expand button (visible only when panel is collapsed) -->
                <transition name="panel-expand-btn">
                    <button
                        v-if="!isPanelOpen"
                        @click="isPanelOpen = true"
                        class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 z-40 w-7 h-16 flex-col items-center justify-center bg-white border-l border-t border-b border-gray-200 rounded-l-xl shadow-md hover:bg-indigo hover:border-indigo hover:text-white transition-all duration-300 group"
                        title="Tampilkan panel"
                    >
                        <span class="material-symbols-outlined text-[10px] text-gray-400 group-hover:text-white transition-colors">chevron_left</span>
                    </button>
                </transition>

                <!-- Mobile backdrop overlay (when panel is open) -->
                <transition name="fade">
                    <div
                        v-if="isMobilePanelOpen"
                        class="md:hidden fixed inset-0 bg-black/40 z-30"
                        style="top: 64px"
                        @click="isMobilePanelOpen = false"
                    />
                </transition>

                <!-- Mobile FAB toggle button -->
                <button
                    class="md:hidden fixed top-[72px] left-3 z-50 w-10 h-10 bg-sumi text-white rounded-xl shadow-xl flex items-center justify-center active:scale-95 transition-all duration-200"
                    @click="isMobilePanelOpen = !isMobilePanelOpen"
                    :title="isMobilePanelOpen ? 'Tutup panel' : 'Buka panel'"
                >
                    <span class="material-symbols-outlined text-lg transition-transform duration-300" :class="isMobilePanelOpen ? 'rotate-90' : ''">
                        {{ isMobilePanelOpen ? 'close' : 'tune' }}
                    </span>
                </button>

                <!-- Right Panel — desktop: flex item; mobile: fixed right drawer -->
                <div
                    class="z-40 md:z-30 transition-transform duration-300 ease-in-out
                           fixed top-16 right-0 bottom-0 md:static md:top-auto md:bottom-auto
                           w-auto"
                    :class="[
                        isMobilePanelOpen ? 'translate-x-0' : 'translate-x-full pointer-events-none',
                        'md:translate-x-0 md:pointer-events-auto'
                    ]"
                >
                    <!-- Extra wrapper: collapses width on desktop when closed -->
                    <div
                        class="h-full transition-all duration-300 ease-in-out md:overflow-hidden"
                        :class="isPanelOpen ? 'md:w-auto' : 'md:w-0'"
                    >
                        <transition name="panel-slide" mode="out-in">
                            <StudioToolbox
                                v-if="activeSideTab !== 'checkout'"
                                @remove-element="removeActiveElement"
                                @update-image-outline="handleImageOutlineUpdate"
                                @update-layer="updateLayer"
                                @save-history="saveToHistory"
                                @add-media="handleAddMedia"
                                @add-text="handleAddText"
                                @collapse-desktop="isPanelOpen = false"
                                @close-mobile="isMobilePanelOpen = false"
                            />
                            <div v-else class="w-[300px] sm:w-[340px] md:w-[380px] flex-shrink-0 bg-white border-l border-indigo/5 flex flex-col h-full shadow-[-20px_0_40px_rgba(0,0,0,0.03)] relative">
                                <CheckoutForm @checkout="handleFinalCheckout" />
                            </div>
                        </transition>
                    </div>
                </div>
            </main>
        </div>

        <!-- Mobile: Element Controls Bottom Sheet -->
        <Teleport to="body">
            <div
                v-if="activeElement"
                class="sheet-mobile md:hidden fixed bottom-0 left-0 right-0 z-[65] bg-white rounded-t-2xl flex flex-col shadow-[0_-4px_24px_rgba(0,0,0,0.10)] border-t border-gray-100"
                :class="{ 'sheet-dismissing': isSheetDismissing }"
                :style="{ maxHeight: isSheetExpanded ? '72vh' : '44vh' }"
            >
                <!-- Drag handle — tap or swipe up/down to expand/collapse/close -->
                <button
                    class="flex-shrink-0 w-full flex flex-col items-center pt-2.5 pb-2 gap-1.5 focus:outline-none"
                    @click="isSheetExpanded = !isSheetExpanded"
                    @touchstart.passive="onSheetTouchStart"
                    @touchend.prevent="onSheetTouchEnd"
                >
                    <div class="w-8 h-[3px] rounded-full bg-gray-200"></div>
                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-gray-300 font-montserrat leading-none">
                        {{ isSheetExpanded ? 'Ciutkan' : 'Perluas' }}
                    </span>
                </button>

                <!-- Scrollable controls -->
                <div class="overflow-y-auto flex-grow px-4 pb-4">
                    <ActiveElementControls
                        @remove="removeActiveElement"
                        @updateImageOutline="handleImageOutlineUpdate"
                    />
                </div>
            </div>
        </Teleport>

        <AddonModals
            :show-fast-track="showFastTrackAlert"
            :show-custom-box="showCustomBoxAlert"
            @confirm-fast-track="confirmFastTrack"
            @confirm-custom-box="confirmCustomBox"
            @close="cancelCheckout"
        />

        <transition name="toast">
            <div v-if="toastMessage" class="fixed bottom-12 left-1/2 z-[110] -translate-x-1/2 rounded-2xl bg-black px-6 py-4 text-[10px] font-black text-white shadow-2xl flex items-center gap-4 uppercase tracking-[0.2em] border border-white/10">
                <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_10px_#d0ff00]"></div>
                {{ toastMessage }}
            </div>
        </transition>

        <CheckoutConfirmModal
            :show="showConfirmModal"
            :customer-name="checkoutForm.name"
            :customer-phone="checkoutForm.phone"
            :customer-address="checkoutForm.address"
            :formatted-total="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(899000 + (checkoutForm.fastTrackEnabled ? 125000 : 0) + (checkoutForm.customBoxEnabled ? 65000 : 0) + checkoutForm.shippingCost)"
            :is-submitting="isSaving"
            @close="showConfirmModal = false"
            @confirm="submitStudioCheckout"
        />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Konva from 'konva';
import { onMounted, onUnmounted, ref, watch } from 'vue';

// Components
import ActiveElementControls from '../components/Studio/Panels/ActiveElementControls.vue';
import CheckoutForm from '../components/Studio/Checkout/CheckoutForm.vue';
import AddonModals from '../components/Studio/Modals/AddonModals.vue';
import CheckoutConfirmModal from '@/components/ui/CheckoutConfirmModal.vue';
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
import { loadImage, drawImageWithOutline } from '../utils/studio-utils';

const {
    catalogFolders,
    catalogLoading,
    activeFolderKey,
    currentModel,
    activeSideTab,
    isPanelOpen,
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

const { clearHistory } = useStudioHistory();
const showConfirmModal = ref(false);
const isMobilePanelOpen = ref(false);
const isSheetExpanded = ref(false);
const isSheetDismissing = ref(false);
const touchStartY = ref(0);

const dismissSheet = () => {
    if (isSheetDismissing.value) return;
    isSheetDismissing.value = true;
    setTimeout(() => deselect(), 300);
};

const onSheetTouchStart = (e: TouchEvent) => {
    touchStartY.value = e.touches[0].clientY;
};

const onSheetTouchEnd = (e: TouchEvent) => {
    const delta = touchStartY.value - e.changedTouches[0].clientY;
    if (Math.abs(delta) < 10) {
        isSheetExpanded.value = !isSheetExpanded.value;
    } else if (delta > 30) {
        isSheetExpanded.value = true;
    } else if (delta < -30) {
        if (isSheetExpanded.value) {
            isSheetExpanded.value = false;
        } else {
            dismissSheet();
        }
    }
};

const {
    initStage,
    loadAssets,
    updateLayer,
    saveToHistory,
    createPreviewURL,
    createPatternURL,
    downloadURL,
    isSyncing,
    getStage,
    getMainLayer,
    getElementsGroup,
    getTransformer,
    deselect
} = useKonvaRenderer();

// UI Alerts State
const showFastTrackAlert = ref(false);
const showCustomBoxAlert = ref(false);

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

const setupNodeEvents = (node: Konva.Node) => {
    node.on('dragmove transform', () => {
        const meta = node.getAttr('meta');
        if (meta && meta.maskId) {
            updateLayer(Number(meta.maskId));
        }
    });
    node.on('dragend transformend', () => {
        saveToHistory();
    });
};

const resetDesign = () => {
    if (confirm('Reset semua perubahan?')) {
        clearHistory();
        loadAssets();
    }
};

const WHATSAPP_INTRO = "Halo Admin Bogor Sneakers, saya ingin memesan sepatu custom dengan rincian berikut:";

const handleFinalCheckout = () => {
    if (isSaving.value) return;

    // Construct final address for summary
    checkoutForm.address = `${checkoutForm.addressDetail}, ${checkoutForm.address}`;
    showConfirmModal.value = true;
};

const submitStudioCheckout = async () => {
    if (isSaving.value) {
        return;
    }

    isSaving.value = true;
    showConfirmModal.value = false;

    try {
        const previewUrl = await createPreviewURL(checkoutForm);
        const patternUrl = await createPatternURL();

        const timestamp = new Date().getTime();
        const safeName = checkoutForm.name.replace(/\s+/g, '_') || 'GUEST';

        downloadURL(previewUrl, `PREVIEW_${safeName}_${timestamp}.png`);
        downloadURL(patternUrl, `POLA_${safeName}_${timestamp}.png`);

        const totalEst = 899000 + (checkoutForm.fastTrackEnabled ? 125000 : 0) + (checkoutForm.customBoxEnabled ? 65000 : 0) + checkoutForm.shippingCost;

        const waMessage = encodeURIComponent([
            WHATSAPP_INTRO,
            `Nama: ${checkoutForm.name}`,
            `WA: ${checkoutForm.phone}`,
            `Email: ${checkoutForm.email}`,
            `Model: ${activeFolderKey.value} - ${currentModelMeta.value?.label}`,
            `Size: ${checkoutForm.shoeSize}`,
            `Fast Track: ${checkoutForm.fastTrackEnabled ? 'Ya' : 'Tidak'}`,
            `Custom Box: ${checkoutForm.customBoxEnabled ? 'Ya' : 'Tidak'}`,
            `Ongkir (${checkoutForm.courier.toUpperCase()}): ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(checkoutForm.shippingCost)}`,
            `Total Est: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalEst)}`,
            `Destinasi: ${checkoutForm.address}`,
            `Kurir: ${checkoutForm.courier.toUpperCase()}`
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

    isMobilePanelOpen.value = false;

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
        strokeWidth: 0,
        mirror: true
    };
    textNode.setAttr('meta', JSON.parse(JSON.stringify(meta)));
    textNode.setAttr('cachedMeta', JSON.stringify(meta));
    setupNodeEvents(textNode);
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

    isMobilePanelOpen.value = false;

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

        const meta: DesignElement = { id: Math.random().toString(36).slice(2), type: 'image' as const, sourceId: id, originalImageSrc: media.src, outline: { active: false, color: '#ffffff', size: 3 }, mirror: true };
        konvaImg.setAttr('meta', JSON.parse(JSON.stringify(meta)));
        konvaImg.setAttr('cachedMeta', JSON.stringify(meta));
        setupNodeEvents(konvaImg);
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
        const meta = node.getAttr('meta');
        const maskId = meta?.maskId;

        node.destroy();
        transformer?.nodes([]);
        activeElement.value = null;

        if (maskId) {
            updateLayer(Number(maskId));
        } else {
            getMainLayer()?.draw();
        }
        
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
            const canvas = drawImageWithOutline(img, meta.outline.color, meta.outline.size);
            node.image(canvas);
        } else {
            node.image(img);
        }

        if (meta.maskId) {
            updateLayer(Number(meta.maskId));
        } else {
            getMainLayer()?.draw();
        }
    }
};

const confirmFastTrack = (val: boolean) => {
    checkoutForm.fastTrackEnabled = val;
    showFastTrackAlert.value = false;
    showCustomBoxAlert.value = true;
};

const confirmCustomBox = (val: boolean) => {
    checkoutForm.customBoxEnabled = val;
    checkoutForm.isCustomBoxLocked = val; // Lock if agreed
    showCustomBoxAlert.value = false;
};

const cancelCheckout = () => {
    showFastTrackAlert.value = false;
    showCustomBoxAlert.value = false;
    activeSideTab.value = 'layers';
};

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
    if (!newEl) {
        isSheetExpanded.value = false;
        isSheetDismissing.value = false;
        return;
    }

    const elementsGroup = getElementsGroup();
    if (!elementsGroup) return;

    const node = elementsGroup.findOne((n: any) => n.getAttr('meta')?.id === newEl.id);
    if (!node) return;

    const cachedStr = node.getAttr('cachedMeta');
    const cachedMeta = cachedStr ? JSON.parse(cachedStr) : {};

    let maskChanged = String(cachedMeta.maskId) !== String(newEl.maskId);
    let visualChanged = JSON.stringify(cachedMeta) !== JSON.stringify(newEl);

    if (visualChanged) {
        node.setAttr('cachedMeta', JSON.stringify(newEl));
        node.setAttr('meta', JSON.parse(JSON.stringify(newEl)));
        
        if (maskChanged) {
            node.opacity(newEl.maskId ? 0 : 1);
            if (cachedMeta.maskId) updateLayer(Number(cachedMeta.maskId));
            if (newEl.maskId) updateLayer(Number(newEl.maskId));
        } else if (newEl.maskId) {
            updateLayer(Number(newEl.maskId));
        }

        if (newEl.type === 'text') {
            const textNode = node as Konva.Text;
            textNode.text(newEl.text || '');
            textNode.fontFamily(newEl.fontFamily || 'Lexend');
            textNode.fill(newEl.color || '#000000');
            textNode.stroke(newEl.strokeColor || null);
            textNode.strokeWidth(newEl.strokeWidth || 0);
        }

        getMainLayer()?.draw();
        saveToHistory();
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
        isPanelOpen.value = true;
        isMobilePanelOpen.value = true;
    }
});
</script>

<style>
.panel-slide-enter-active, .panel-slide-leave-active { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.panel-slide-enter-from { transform: translateX(100%); opacity: 0; }
.panel-slide-leave-to { transform: translateX(100%); opacity: 0; }

.toast-enter-active, .toast-leave-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translate(-50%, 20px) scale(0.9); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.panel-expand-btn-enter-active, .panel-expand-btn-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.panel-expand-btn-enter-from, .panel-expand-btn-leave-to { opacity: 0; transform: translateY(-50%) translateX(8px); }

.sheet-mobile {
    transform: translateY(0);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: sheetSlideUp 0.3s cubic-bezier(0.34, 1.2, 0.64, 1);
}
.sheet-mobile.sheet-dismissing {
    transform: translateY(110%);
}
@keyframes sheetSlideUp {
    from { transform: translateY(100%); }
    to   { transform: translateY(0); }
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
