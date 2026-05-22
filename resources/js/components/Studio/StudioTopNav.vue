<template>
    <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-8 h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-10">
            <!-- Brand -->
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-10 h-10 bg-sumi rounded-xl flex items-center justify-center transition-all duration-500 group-hover:rotate-[15deg] group-hover:scale-110 group-hover:shadow-lg">
                    <img src="/logo-b-bogor.svg" class="w-6 h-6 invert transition-transform duration-500 group-hover:scale-90" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-montserrat font-black tracking-widest text-sumi uppercase leading-none transition-colors duration-300 group-hover:text-indigo">Studio</span>
                    <span class="text-[10px] font-montserrat font-bold text-matcha tracking-[0.3em] uppercase leading-none mt-1">Customizer</span>
                </div>
            </div>

            <div class="h-8 w-[1px] bg-gray-200"></div>

            <!-- Selection Group -->
            <div class="flex items-center gap-6 font-montserrat">
                 <!-- Model Dropdown -->
                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Model :</span>
                     <div class="relative" ref="modelDropdownRef">
                        <button
                            @click="isModelOpen = !isModelOpen; isVariantOpen = false"
                            :disabled="catalogLoading"
                            class="h-10 pl-9 pr-10 min-w-[200px] rounded-xl border border-gray-200 bg-gray-50/80 text-[12px] font-bold text-sumi transition-all duration-300 flex items-center justify-between hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group"
                        >
                            <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">style</span>
                            <span class="truncate">{{ activeFolderLabel || 'Pilih Model' }}</span>
                            <span class="material-symbols-outlined absolute right-3 text-lg text-gray-400 group-hover:text-indigo transition-transform duration-300" :class="{ 'rotate-180': isModelOpen }">expand_more</span>
                        </button>

                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                        >
                            <div v-if="isModelOpen" class="absolute top-full left-0 mt-2 w-full bg-white rounded-2xl border border-gray-100 shadow-2xl py-2 z-[60] overflow-hidden backdrop-blur-xl">
                                <button
                                    v-for="folder in catalogFolders"
                                    :key="folder.key"
                                    @click="selectFolder(folder.key)"
                                    class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item"
                                    :class="activeFolderKey === folder.key ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                >
                                    <span>{{ folder.label }}</span>
                                    <span v-if="activeFolderKey === folder.key" class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                            </div>
                        </transition>
                     </div>
                 </div>

                 <!-- Variant Picker Button -->
                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Variant :</span>
                     <button
                        @click="openVariantPicker"
                        :disabled="catalogLoading"
                        class="h-10 pl-9 pr-4 min-w-[180px] rounded-xl border border-gray-200 bg-gray-50/80 text-[12px] font-bold text-sumi transition-all duration-300 flex items-center gap-2 hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group relative"
                     >
                        <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">auto_awesome</span>
                        <span class="truncate flex-1 text-left">{{ activeVariantLabel || 'Pilih Varian' }}</span>
                        <span class="material-symbols-outlined text-sm text-gray-300 group-hover:text-indigo transition-colors duration-300 flex-shrink-0">unfold_more</span>
                     </button>
                 </div>
            </div>
        </div>

        <div class="flex items-center gap-4 font-montserrat">
            <!-- Action Tools -->
            <div class="flex items-center bg-gray-50/80 rounded-2xl p-1.5 border border-gray-200 shadow-sm">
                <button @click="$emit('undo')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Undo (Ctrl+Z)">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:-rotate-12">undo</span>
                </button>
                <button @click="$emit('redo')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Redo (Ctrl+Y)">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:rotate-12">redo</span>
                </button>
                <div class="w-[1px] h-6 bg-gray-300 mx-1"></div>
                <button @click="$emit('reset')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-red-500 hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Reset Design">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:rotate-180">restart_alt</span>
                </button>
            </div>

            <button @click="$emit('checkout')" class="group relative overflow-hidden h-12 px-8 bg-sumi text-washi font-bold text-[11px] uppercase tracking-[0.2em] transition-all duration-300 rounded-2xl shadow-md hover:shadow-xl hover:bg-indigo hover:-translate-y-1 active:translate-y-0 active:scale-95 flex items-center gap-3 border border-transparent hover:border-indigo/50">
                <span class="relative z-10 flex items-center gap-3">
                    {{ isSaving ? 'PROCESSING...' : 'PROCESS ORDER' }}
                    <span class="material-symbols-outlined text-lg transition-transform duration-300 group-hover:translate-x-1" v-if="!isSaving">shopping_cart_checkout</span>
                </span>
                <div class="absolute inset-0 h-full w-full bg-white/10 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            </button>
        </div>
    </nav>

    <!-- ========================
         VARIANT PICKER MODAL
         ======================== -->
    <Teleport to="body">
        <Transition name="variant-backdrop">
            <div v-if="isVariantOpen" class="fixed inset-0 z-[200] flex items-center justify-center p-4" @click.self="isVariantOpen = false">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="isVariantOpen = false"></div>

                <!-- Modal -->
                <Transition name="variant-card">
                    <div v-if="isVariantOpen" class="variant-modal relative z-10 bg-white rounded-[2rem] shadow-2xl flex flex-col overflow-hidden w-full max-w-sm">

                        <!-- ── Header ── -->
                        <div class="flex items-start justify-between px-6 pt-6 pb-0 flex-shrink-0">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.25em] text-gray-300 font-montserrat mb-0.5">
                                    {{ centeredVariantIndex + 1 }} / {{ availableModels.length }}
                                </p>
                                <h2 class="text-lg font-black uppercase tracking-widest text-sumi font-montserrat leading-none">
                                    Pilih Varian
                                </h2>
                            </div>
                            <button
                                @click="isVariantOpen = false"
                                class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors mt-0.5 flex-shrink-0"
                            >
                                <span class="material-symbols-outlined text-base text-gray-500">close</span>
                            </button>
                        </div>

                        <!-- ── Shoe Carousel ── -->
                        <div class="relative flex-shrink-0 mt-4">

                            <!-- Desktop nav arrow — UP -->
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 z-20 hidden md:block">
                                <button
                                    @click="navigateVariant(-1)"
                                    :disabled="centeredVariantIndex === 0"
                                    class="nav-arrow w-10 h-10 rounded-full border border-gray-200 bg-white shadow-md flex items-center justify-center text-gray-400 hover:text-indigo hover:border-indigo hover:shadow-lg transition-all duration-200 active:scale-95 disabled:opacity-20 disabled:cursor-not-allowed disabled:hover:text-gray-400 disabled:hover:border-gray-200 disabled:hover:shadow-md"
                                >
                                    <span class="material-symbols-outlined text-xl">keyboard_arrow_up</span>
                                </button>
                            </div>

                            <!-- Scroll viewport — shows ONE shoe at a time -->
                            <div
                                ref="variantScrollRef"
                                class="variant-scroll"
                                @scroll="onVariantScroll"
                            >
                                <div
                                    v-for="(model, index) in availableModels"
                                    :key="model.id"
                                    :ref="(el) => { if (el) variantItemRefs[index] = el as HTMLElement }"
                                    class="variant-slide px-6 flex flex-col items-center justify-center gap-3 cursor-pointer"
                                    @click="confirmAndSelectVariant(model.id)"
                                >
                                    <!-- Shoe Hero Image -->
                                    <div class="shoe-hero relative w-full rounded-2xl overflow-hidden flex items-center justify-center">
                                        <div class="absolute inset-0 bg-gradient-radial rounded-2xl"></div>

                                        <!-- Lazy: only mount img when slide is near viewport -->
                                        <template v-if="isSlideVisible(index)">
                                            <img
                                                v-if="thumbSrc(model)"
                                                :src="thumbSrc(model)"
                                                class="relative z-10 w-full h-full object-contain shoe-img drop-shadow-xl"
                                                draggable="false"
                                                alt=""
                                            />
                                            <span v-else class="relative z-10 material-symbols-outlined text-7xl text-gray-300">checkroom</span>
                                        </template>
                                        <!-- Placeholder when not yet in view -->
                                        <div v-else class="relative z-10 w-16 h-16 rounded-full bg-gray-200 animate-pulse"></div>

                                        <!-- Active badge -->
                                        <div v-if="currentModel === model.id" class="absolute top-3 right-3 z-20 flex items-center gap-1 bg-indigo text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full shadow-lg">
                                            <span class="material-symbols-outlined text-[10px]">check</span>
                                            Aktif
                                        </div>
                                    </div>

                                    <!-- Variant label -->
                                    <p class="text-[13px] font-black uppercase tracking-wider text-sumi font-montserrat leading-tight text-center">{{ model.label }}</p>
                                </div>
                            </div>

                            <!-- Desktop nav arrow — DOWN -->
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 z-20 hidden md:block">
                                <button
                                    @click="navigateVariant(1)"
                                    :disabled="centeredVariantIndex >= availableModels.length - 1"
                                    class="nav-arrow w-10 h-10 rounded-full border border-gray-200 bg-white shadow-md flex items-center justify-center text-gray-400 hover:text-indigo hover:border-indigo hover:shadow-lg transition-all duration-200 active:scale-95 disabled:opacity-20 disabled:cursor-not-allowed disabled:hover:text-gray-400 disabled:hover:border-gray-200 disabled:hover:shadow-md"
                                >
                                    <span class="material-symbols-outlined text-xl">keyboard_arrow_down</span>
                                </button>
                            </div>
                        </div>

                        <!-- ── Dot Indicators ── -->
                        <div class="flex items-center justify-center gap-1.5 mt-6 flex-shrink-0 px-6 flex-wrap">
                            <div
                                v-for="(_, i) in availableModels"
                                :key="i"
                                class="rounded-full transition-all duration-300 ease-out"
                                :class="i === centeredVariantIndex
                                    ? 'w-5 h-[5px] bg-indigo'
                                    : 'w-[5px] h-[5px] bg-gray-200 hover:bg-gray-300'"
                            ></div>
                        </div>

                        <!-- ── Confirm Button ── -->
                        <div class="px-6 pb-6 pt-4 flex-shrink-0">
                            <button
                                @click="confirmVariant()"
                                class="w-full h-13 py-3.5 bg-sumi text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-indigo transition-all duration-300 flex items-center justify-center gap-2.5 shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] font-montserrat group"
                            >
                                <span class="material-symbols-outlined text-base transition-transform group-hover:rotate-12">check_circle</span>
                                Pilih Varian Ini
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { useStudioStore } from '../../composables/useStudioStore';
import { loadImage, drawFilledLayer } from '../../utils/studio-utils';
import type { CatalogModel } from '../../types/studio';

const {
    catalogFolders,
    catalogLoading,
    activeFolderKey,
    currentModel,
    availableModels,
    modelThumbnailURLs,
    layerColors,
    isSaving
} = useStudioStore();

defineEmits(['undo', 'redo', 'reset', 'checkout']);

// Each slide is 320px tall — matches .variant-slide in CSS
const ITEM_HEIGHT = 320;

// Only load images for slides within 2 positions of the visible one
const isSlideVisible = (index: number) => Math.abs(index - centeredVariantIndex.value) <= 2;

// Deterministic color from layer ID so every model looks colorful even before being loaded.
// Uses golden-angle hue distribution to avoid similar adjacent colors.
const deterministicLayerColor = (layerId: number): string => {
    const h = Math.round((layerId * 137.508) % 360);
    const s = 45 + (layerId * 23 % 30);
    const l = 40 + (layerId * 17 % 22);
    return `hsl(${h},${s}%,${l}%)`;
};

const generatingSet = new Set<string | number>();

const generateThumbnailForModel = async (model: CatalogModel): Promise<void> => {
    if (modelThumbnailURLs.value[model.id] || generatingSet.has(model.id)) return;
    if (!model.layers?.length && !model.preview_base_file && !model.main_file) return;

    generatingSet.add(model.id);
    try {
        const SIZE = 400;
        const canvas = document.createElement('canvas');
        canvas.width = SIZE;
        canvas.height = SIZE;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        const baseUrl = `/shoes-svg/${activeFolderKey.value}/${model.id}/`;

        // Draw base file
        const baseFile = model.preview_base_file ?? model.main_file;
        if (baseFile) {
            const baseImg = await loadImage(baseUrl + baseFile);
            ctx.drawImage(baseImg, 0, 0, SIZE, SIZE);
        }

        // Draw each accent layer with stored color or deterministic fallback
        for (const layer of (model.layers ?? [])) {
            const layerImg = await loadImage(baseUrl + layer.file);
            const color = layerColors.value[layer.id] ?? deterministicLayerColor(layer.id);
            const filled = drawFilledLayer(layerImg, color, SIZE, SIZE);
            ctx.drawImage(filled, 0, 0);
        }

        modelThumbnailURLs.value[model.id] = canvas.toDataURL('image/png');
    } catch {
        // Network error or CORS issue — silently skip; fallback to base SVG
    } finally {
        generatingSet.delete(model.id);
    }
};

// Use the Konva-rendered colored thumbnail when available, fall back to raw SVG
const thumbSrc = (model: any) => {
    const cached = modelThumbnailURLs.value[model.id];
    if (cached) return cached;
    const file = model.preview_base_file ?? model.main_file;
    if (!file) return '';
    return `/shoes-svg/${activeFolderKey.value}/${model.id}/${file}`;
};

const isModelOpen = ref(false);
const isVariantOpen = ref(false);
const modelDropdownRef = ref<HTMLElement | null>(null);
const variantScrollRef = ref<HTMLElement | null>(null);
const variantItemRefs = ref<Record<number, HTMLElement>>({});
const centeredVariantIndex = ref(0);

const activeFolderLabel = computed(() =>
    catalogFolders.value.find(f => f.key === activeFolderKey.value)?.label
);

const activeVariantLabel = computed(() =>
    availableModels.value.find(m => m.id === currentModel.value)?.label
);

const selectFolder = (key: string) => {
    activeFolderKey.value = key;
    const folder = catalogFolders.value.find(f => f.key === key);
    currentModel.value = folder?.models[0]?.id ?? null;
    isModelOpen.value = false;
};

const selectVariant = (id: number | string) => {
    currentModel.value = id;
    isVariantOpen.value = false;
};

const openVariantPicker = () => {
    isModelOpen.value = false;
    isVariantOpen.value = true;
};

const navigateVariant = (direction: 1 | -1) => {
    const newIdx = Math.max(0, Math.min(availableModels.value.length - 1, centeredVariantIndex.value + direction));
    centeredVariantIndex.value = newIdx;
    if (variantScrollRef.value) {
        variantScrollRef.value.scrollTo({ top: newIdx * ITEM_HEIGHT, behavior: 'smooth' });
    }
};

const confirmVariant = () => {
    const model = availableModels.value[centeredVariantIndex.value];
    if (model) selectVariant(model.id);
};

// Tap on any slide: scroll it into view first, then select
const confirmAndSelectVariant = (id: number | string) => {
    selectVariant(id);
};

const onVariantScroll = () => {
    if (!variantScrollRef.value) return;
    const idx = Math.round(variantScrollRef.value.scrollTop / ITEM_HEIGHT);
    centeredVariantIndex.value = Math.max(0, Math.min(availableModels.value.length - 1, idx));
};

// On open: scroll to current variant and generate thumbnails for all models
watch(isVariantOpen, async (val) => {
    if (!val) {
        variantItemRefs.value = {};
        return;
    }

    const idx = Math.max(0, availableModels.value.findIndex(m => m.id === currentModel.value));
    centeredVariantIndex.value = idx;
    await nextTick();
    await nextTick();
    if (variantScrollRef.value) {
        variantScrollRef.value.scrollTop = idx * ITEM_HEIGHT;
    }

    // Generate colored thumbnails for models that don't have one yet.
    // Stagger by 60ms per model to avoid flooding the network.
    availableModels.value.forEach((model, i) => {
        if (!modelThumbnailURLs.value[model.id] && !generatingSet.has(model.id)) {
            setTimeout(() => generateThumbnailForModel(model), i * 60);
        }
    });
});

// Click outside closes model dropdown only
const handleClickOutside = (event: MouseEvent) => {
    if (modelDropdownRef.value && !modelDropdownRef.value.contains(event.target as Node)) {
        isModelOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<style scoped>
/* Hide scrollbar on all browsers */
.variant-scroll::-webkit-scrollbar { display: none; }
.variant-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    height: 320px;
}

/* Each slide fills the viewport exactly → one shoe visible at a time */
.variant-slide {
    height: 320px;
    scroll-snap-align: start;
    flex-shrink: 0;
}

/* Shoe hero box */
.shoe-hero {
    height: 220px;
}

/* Radial bg */
.shoe-hero .bg-gradient-radial {
    background: radial-gradient(ellipse at 50% 55%, #e8e8e8 0%, #f5f5f5 65%, #fafafa 100%);
}

/* Zoom in on the shoe — SVG viewboxes often have built-in whitespace */
.shoe-img {
    transform: scale(1.45);
    transform-origin: center center;
    transition: transform 0.3s ease;
}
.variant-slide:hover .shoe-img {
    transform: scale(1.55);
}

/* Backdrop fade */
.variant-backdrop-enter-active,
.variant-backdrop-leave-active { transition: opacity 0.25s ease; }
.variant-backdrop-enter-from,
.variant-backdrop-leave-to { opacity: 0; }

/* Card spring pop */
.variant-card-enter-active { transition: all 0.32s cubic-bezier(0.34, 1.56, 0.64, 1); }
.variant-card-leave-active  { transition: all 0.2s cubic-bezier(0.4, 0, 1, 1); }
.variant-card-enter-from    { opacity: 0; transform: scale(0.88) translateY(16px); }
.variant-card-leave-to      { opacity: 0; transform: scale(0.94) translateY(8px); }

</style>
