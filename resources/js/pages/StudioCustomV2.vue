<template>
    <div class="font-body-md text-body-md bg-[#0a0a0f] text-white min-h-screen flex flex-col overflow-hidden">
        <Head title="Studio Custom V2" />

        <!-- Top Nav -->
        <header class="shrink-0 h-16 flex items-center justify-between px-4 md:px-6 bg-[#111118] border-b border-white/5 z-50 relative">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse shadow-[0_0_8px_#d0ff00]"></div>
                <span class="text-[11px] font-black tracking-[0.25em] uppercase font-montserrat">
                    Bogor Sneakers
                    <span class="text-primary ml-1">Studio V2</span>
                </span>
            </div>

            <div class="flex items-center gap-2">
                <button @click="zoomBy(1 / 1.25)" class="hidden md:flex w-8 h-8 items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-colors text-sm font-bold" title="Zoom out">
                    <span class="material-symbols-outlined text-base">remove</span>
                </button>
                <button @click="zoomBy(1.25)" class="hidden md:flex w-8 h-8 items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-colors text-sm font-bold" title="Zoom in">
                    <span class="material-symbols-outlined text-base">add</span>
                </button>
                <button @click="resetView()" class="hidden md:flex w-8 h-8 items-center justify-center rounded-lg bg-white/5 hover:bg-white/10 text-white/60 hover:text-white transition-colors" title="Reset view">
                    <span class="material-symbols-outlined text-base">fit_screen</span>
                </button>
                <div class="hidden md:block w-px h-5 bg-white/10 mx-1"></div>
                <button
                    @click="confirmReset"
                    :disabled="isCatalogLoading"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition-all text-[10px] font-bold tracking-wider uppercase disabled:opacity-30 disabled:cursor-not-allowed"
                    title="Reset desain"
                >
                    <span class="material-symbols-outlined text-sm">restart_alt</span>
                    <span class="hidden md:inline">Reset</span>
                </button>
            </div>
        </header>

        <!-- Body -->
        <div class="grow flex h-[calc(100vh-64px)] relative">

            <!-- Canvas area -->
            <main class="grow flex relative overflow-hidden">
                <StudioCanvas
                    :is-syncing="isSyncing || isCatalogLoading"
                    @init="handleInitStage"
                />

                <!-- Catalog error overlay -->
                <div
                    v-if="catalogError"
                    class="absolute inset-0 flex items-center justify-center bg-[#0a0a0f]/80 z-20"
                >
                    <div class="text-center px-6">
                        <span class="material-symbols-outlined text-4xl text-white/30 mb-3 block">error_outline</span>
                        <p class="text-white/60 text-sm mb-4">{{ catalogError }}</p>
                        <button
                            @click="reloadCatalog"
                            class="px-4 py-2 rounded-lg bg-primary text-[#111118] text-[11px] font-black tracking-wider uppercase"
                        >Coba Lagi</button>
                    </div>
                </div>

                <!-- Desktop expand button -->
                <transition name="panel-expand-btn">
                    <button
                        v-if="!isPanelOpen"
                        @click="isPanelOpen = true"
                        class="hidden md:flex absolute right-0 top-1/2 -translate-y-1/2 z-40 w-7 h-16 flex-col items-center justify-center bg-[#111118] border-l border-t border-b border-white/10 rounded-l-xl shadow-xl hover:bg-white/10 transition-all duration-300 group"
                        title="Tampilkan panel"
                    >
                        <span class="material-symbols-outlined text-[10px] text-white/40 group-hover:text-white transition-colors">chevron_left</span>
                    </button>
                </transition>

                <!-- Mobile backdrop -->
                <transition name="fade">
                    <div
                        v-if="isMobilePanelOpen"
                        class="md:hidden fixed inset-0 bg-black/60 z-30"
                        style="top: 64px"
                        @click="isMobilePanelOpen = false"
                    />
                </transition>

                <!-- Mobile FAB -->
                <button
                    class="md:hidden fixed top-18 left-3 z-50 w-10 h-10 bg-[#111118] border border-white/10 text-white rounded-xl shadow-xl flex items-center justify-center active:scale-95 transition-all duration-200"
                    @click="isMobilePanelOpen = !isMobilePanelOpen"
                >
                    <span class="material-symbols-outlined text-lg transition-transform duration-300" :class="isMobilePanelOpen ? 'rotate-90' : ''">
                        {{ isMobilePanelOpen ? 'close' : 'tune' }}
                    </span>
                </button>

                <!-- Right Panel -->
                <div
                    class="z-40 md:z-30 transition-transform duration-300 ease-in-out
                           fixed top-16 right-0 bottom-0 md:static md:top-auto md:bottom-auto w-auto"
                    :class="[
                        isMobilePanelOpen ? 'translate-x-0' : 'translate-x-full pointer-events-none',
                        'md:translate-x-0 md:pointer-events-auto'
                    ]"
                >
                    <div
                        class="h-full transition-all duration-300 ease-in-out md:overflow-hidden"
                        :class="isPanelOpen ? 'md:w-auto' : 'md:w-0'"
                    >
                        <transition name="panel-slide" mode="out-in">
                            <div
                                v-if="showCheckout"
                                key="checkout"
                                class="w-75 sm:w-85 md:w-95 shrink-0 bg-white flex flex-col h-full shadow-[-20px_0_40px_rgba(0,0,0,0.3)] relative"
                            >
                                <CheckoutForm @checkout="handleFinalCheckout" />
                            </div>

                            <AccentCarouselPanel
                                v-else-if="selectedCatalog"
                                key="accent"
                                :catalog="selectedCatalog"
                                :shoe-types="catalogs as ShoeTypeCatalog[]"
                                :accent-models="accentModels"
                                :accent-colors="accentColors"
                                :accent-enabled="accentEnabled"
                                :loading-slot="loadingSlot"
                                :layer-order="layerOrder"
                                @update:accent-model="onAccentModelChange"
                                @update:accent-color="onAccentColorChange"
                                @update:accent-enabled="onAccentEnabledChange"
                                @update:shoe-type="onShoeTypeChange"
                                @update:layer-order="onLayerOrderChange"
                                @collapse-desktop="isPanelOpen = false"
                                @close-mobile="isMobilePanelOpen = false"
                                @checkout="startCheckout"
                            />

                            <!-- Catalog loading skeleton -->
                            <div
                                v-else
                                key="loading"
                                class="w-75 h-full bg-[#111118] flex flex-col items-center justify-center gap-3"
                            >
                                <div class="w-5 h-5 border-2 border-primary/40 border-t-primary rounded-full animate-spin"></div>
                                <p class="text-[10px] text-white/30 tracking-widest uppercase">Memuat catalog…</p>
                            </div>
                        </transition>
                    </div>
                </div>
            </main>
        </div>

        <!-- Addon Modals -->
        <AddonModals
            :show-fast-track="showFastTrackAlert"
            :show-custom-box="showCustomBoxAlert"
            @confirm-fast-track="confirmFastTrack"
            @confirm-custom-box="confirmCustomBox"
            @close="cancelCheckout"
        />

        <!-- Checkout Confirm Modal -->
        <CheckoutConfirmModal
            :show="showConfirmModal"
            :customer-name="checkoutForm.name"
            :customer-phone="checkoutForm.phone"
            :customer-address="checkoutForm.address"
            :formatted-total="formattedTotal"
            :is-submitting="isSaving"
            @close="showConfirmModal = false"
            @confirm="submitCheckout"
        />

        <!-- Toast -->
        <transition name="toast">
            <div
                v-if="toastMessage"
                class="fixed bottom-12 left-1/2 z-110 -translate-x-1/2 rounded-2xl bg-[#111118] px-6 py-4 text-[10px] font-black text-white shadow-2xl flex items-center gap-4 uppercase tracking-[0.2em] border border-white/10"
            >
                <div class="w-2 h-2 bg-primary rounded-full animate-pulse shadow-[0_0_10px_#d0ff00]"></div>
                {{ toastMessage }}
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';

import StudioCanvas from '../components/Studio/StudioCanvas.vue';
import CheckoutForm from '../components/Studio/Checkout/CheckoutForm.vue';
import AddonModals from '../components/Studio/Modals/AddonModals.vue';
import CheckoutConfirmModal from '@/components/ui/CheckoutConfirmModal.vue';
import AccentCarouselPanel from '../components/StudioV2/AccentCarouselPanel.vue';

import { useKonvaRendererV2 } from '../composables/useKonvaRendererV2';
import { useStudioStore } from '../composables/useStudioStore';
import { useStudioV2Catalog } from '../composables/useStudioV2Catalog';
import type { ShoeTypeCatalog } from '../data/studio-v2-catalog';

const { checkoutForm, isSaving, toastMessage, showToast } = useStudioStore();

const {
    initStage,
    loadAllLayers,
    updateSlotModel,
    updateSlotColor,
    setSlotEnabled,
    createPreviewURL,
    createPatternURL,
    downloadURL,
    isSyncing,
    zoomBy,
    resetView,
    reorderSlots,
} = useKonvaRendererV2();

// ─── Catalog (from API) ───────────────────────────────────────────────────────
const {
    catalogs,
    isLoading: isCatalogLoading,
    error: catalogError,
    load: loadCatalog,
} = useStudioV2Catalog();

const selectedCatalog = ref<ShoeTypeCatalog | null>(null);

// When catalog loads, initialize with first catalog
watch(catalogs, (list) => {
    if (list.length && !selectedCatalog.value) {
        selectedCatalog.value = list[0];
        resetAccentState(list[0]);
    }
}, { immediate: true });

const reloadCatalog = () => loadCatalog();

// ─── UI State ─────────────────────────────────────────────────────────────────
const isPanelOpen = ref(true);
const isMobilePanelOpen = ref(false);
const showCheckout = ref(false);
const showFastTrackAlert = ref(false);
const showCustomBoxAlert = ref(false);
const showConfirmModal = ref(false);
const loadingSlot = ref<number | null>(null);

// ─── Accent State ─────────────────────────────────────────────────────────────
const accentModels = ref<Record<number, number>>({});
const accentColors = ref<Record<number, string>>({});
const accentEnabled = ref<Record<number, boolean>>({});
// layerOrder[0] = topmost slot in canvas (matches loadAllLayers default: ascending slots, highest on top)
const layerOrder = ref<number[]>([]);

// Whether the canvas stage has been initialized (waiting for the @init event)
let stageInitialized = false;

const resetAccentState = (catalog: ShoeTypeCatalog) => {
    accentModels.value = Object.fromEntries(catalog.allAccentSlots.map(s => [s, 1]));
    accentColors.value = Object.fromEntries(catalog.allAccentSlots.map(s => [s, catalog.defaultColors[s] ?? '#ffffff']));
    accentEnabled.value = Object.fromEntries(catalog.allAccentSlots.map(s => [s, true]));
    layerOrder.value = [...catalog.allAccentSlots].reverse();
};

// ─── Canvas ───────────────────────────────────────────────────────────────────
const handleInitStage = (container: HTMLDivElement) => {
    initStage(container);
    stageInitialized = true;
    if (selectedCatalog.value) {
        loadAllLayers(selectedCatalog.value, accentModels.value, accentColors.value, accentEnabled.value);
    }
};

// If catalog finishes loading after stage is already init'd, trigger load
watch(selectedCatalog, (catalog) => {
    if (catalog && stageInitialized) {
        loadAllLayers(catalog, accentModels.value, accentColors.value, accentEnabled.value);
    }
});

const confirmReset = () => {
    if (!selectedCatalog.value) return;
    if (confirm('Reset semua pilihan aksen ke default?')) {
        resetAccentState(selectedCatalog.value);
        loadAllLayers(selectedCatalog.value, accentModels.value, accentColors.value, accentEnabled.value);
    }
};

// ─── Shoe Type Change ─────────────────────────────────────────────────────────
const onShoeTypeChange = (typeId: string) => {
    const catalog = catalogs.value.find(t => t.id === typeId);
    if (!catalog || catalog.id === selectedCatalog.value?.id) return;
    selectedCatalog.value = catalog;
    resetAccentState(catalog);
    // watch(selectedCatalog) will trigger loadAllLayers
};

// ─── Accent Event Handlers ────────────────────────────────────────────────────
const onAccentModelChange = async (slot: number, model: number) => {
    if (!selectedCatalog.value) return;
    accentModels.value[slot] = model;
    loadingSlot.value = slot;
    await updateSlotModel(selectedCatalog.value, slot, model, accentColors.value[slot]);
    reorderSlots(layerOrder.value);
    loadingSlot.value = null;
};

const onAccentColorChange = (slot: number, color: string) => {
    accentColors.value[slot] = color;
    updateSlotColor(slot, color);
};

const onAccentEnabledChange = async (slot: number, enabled: boolean) => {
    if (!selectedCatalog.value) return;
    accentEnabled.value[slot] = enabled;
    const needsLoad = setSlotEnabled(slot, enabled);
    if (needsLoad) {
        loadingSlot.value = slot;
        await updateSlotModel(selectedCatalog.value, slot, accentModels.value[slot], accentColors.value[slot]);
        reorderSlots(layerOrder.value);
        loadingSlot.value = null;
    }
};

const onLayerOrderChange = (newOrder: number[]) => {
    layerOrder.value = newOrder;
    reorderSlots(newOrder);
};

// ─── Checkout Flow ────────────────────────────────────────────────────────────
const startCheckout = () => {
    showFastTrackAlert.value = true;
    isPanelOpen.value = true;
    isMobilePanelOpen.value = true;
};

const confirmFastTrack = (val: boolean) => {
    checkoutForm.fastTrackEnabled = val;
    showFastTrackAlert.value = false;
    showCustomBoxAlert.value = true;
};

const confirmCustomBox = (val: boolean) => {
    checkoutForm.customBoxEnabled = val;
    checkoutForm.isCustomBoxLocked = val;
    showCustomBoxAlert.value = false;
    showCheckout.value = true;
};

const cancelCheckout = () => {
    showFastTrackAlert.value = false;
    showCustomBoxAlert.value = false;
};

const handleFinalCheckout = () => {
    if (isSaving.value) return;
    checkoutForm.address = `${checkoutForm.addressDetail}, ${checkoutForm.address}`;
    showConfirmModal.value = true;
};

const BASE_PRICE = 899000;

const formattedTotal = computed(() => {
    const total = BASE_PRICE
        + (checkoutForm.fastTrackEnabled ? 125000 : 0)
        + (checkoutForm.customBoxEnabled ? 65000 : 0)
        + checkoutForm.shippingCost;
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
});

const WHATSAPP_INTRO = 'Halo Admin Bogor Sneakers, saya ingin memesan sepatu Custom Studio V2:';

const submitCheckout = async () => {
    if (isSaving.value || !selectedCatalog.value) return;
    isSaving.value = true;
    showConfirmModal.value = false;

    try {
        const previewUrl = createPreviewURL();
        const patternUrl = await createPatternURL(selectedCatalog.value, accentModels.value, accentColors.value, accentEnabled.value);

        const timestamp = Date.now();
        const safeName = checkoutForm.name.replace(/\s+/g, '_') || 'GUEST';
        downloadURL(previewUrl, `PREVIEW_V2_${safeName}_${timestamp}.png`);
        downloadURL(patternUrl, `POLA_V2_${safeName}_${timestamp}.png`);

        const total = BASE_PRICE
            + (checkoutForm.fastTrackEnabled ? 125000 : 0)
            + (checkoutForm.customBoxEnabled ? 65000 : 0)
            + checkoutForm.shippingCost;

        const accentSummary = selectedCatalog.value.allAccentSlots
            .filter(slot => accentEnabled.value[slot] !== false)
            .map(slot => `  Aksen ${slot}: M${accentModels.value[slot]}`)
            .join('\n');

        const waMessage = encodeURIComponent([
            WHATSAPP_INTRO,
            `Nama: ${checkoutForm.name}`,
            `WA: ${checkoutForm.phone}`,
            `Email: ${checkoutForm.email}`,
            `Tipe: ${selectedCatalog.value.label} Custom V2`,
            `Konfigurasi Aksen:\n${accentSummary}`,
            `Size: ${checkoutForm.shoeSize}`,
            `Fast Track: ${checkoutForm.fastTrackEnabled ? 'Ya' : 'Tidak'}`,
            `Custom Box: ${checkoutForm.customBoxEnabled ? 'Ya' : 'Tidak'}`,
            `Ongkir (${checkoutForm.courier.toUpperCase()}): ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(checkoutForm.shippingCost)}`,
            `Total Est: ${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total)}`,
            `Destinasi: ${checkoutForm.address}`,
        ].join('\n'));

        window.open(`https://wa.me/6285511223344?text=${waMessage}`, '_blank');
        showToast('Order V2 diproses & File terunduh!');
    } catch (err) {
        console.error(err);
        showToast('Gagal memproses pesanan');
    } finally {
        isSaving.value = false;
    }
};

// ─── Keyboard shortcuts ────────────────────────────────────────────────────────
const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === '+' || e.key === '=') zoomBy(1.25);
    if (e.key === '-') zoomBy(1 / 1.25);
    if (e.key === '0') resetView();
};

onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
    loadCatalog();
});
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown));
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
</style>
