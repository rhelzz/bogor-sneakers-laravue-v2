<template>
    <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-4 md:px-8 h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-3 md:gap-10">
            <!-- Brand -->
            <div class="flex items-center gap-2 md:gap-4 group cursor-pointer flex-shrink-0">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-sumi rounded-xl flex items-center justify-center transition-all duration-500 group-hover:rotate-[15deg] group-hover:scale-110 group-hover:shadow-lg">
                    <img src="/logo-b-bogor.svg" class="w-5 h-5 md:w-6 md:h-6 invert transition-transform duration-500 group-hover:scale-90" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-xs md:text-sm font-montserrat font-black tracking-widest text-sumi uppercase leading-none transition-colors duration-300 group-hover:text-indigo">Studio</span>
                    <span class="hidden sm:block text-[10px] font-montserrat font-bold text-matcha tracking-[0.3em] uppercase leading-none mt-1">Customizer</span>
                </div>
            </div>

            <div class="hidden md:block h-8 w-[1px] bg-gray-200"></div>

            <!-- Selection Group -->
            <div class="flex items-center gap-2 md:gap-6 font-montserrat">
                <!-- Model Dropdown -->
                <div class="flex items-center gap-1 md:gap-3">
                    <span class="hidden md:inline-block text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Model :</span>
                    <div class="relative" ref="modelDropdownRef">
                        <button
                            @click="isModelOpen = !isModelOpen; isVariantOpen = false"
                            :disabled="catalogLoading"
                            class="h-9 md:h-10 pl-8 md:pl-9 pr-2 md:pr-10 w-[110px] sm:w-[150px] md:min-w-[200px] md:w-auto rounded-xl border border-gray-200 bg-gray-50/80 text-[11px] md:text-[12px] font-bold text-sumi transition-all duration-300 flex items-center justify-between hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group relative"
                        >
                            <span class="material-symbols-outlined absolute left-2.5 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">style</span>
                            <span class="truncate">{{ activeFolderLabel || 'Model' }}</span>
                            <span class="material-symbols-outlined hidden md:block absolute right-3 text-lg text-gray-400 group-hover:text-indigo transition-transform duration-300" :class="{ 'rotate-180': isModelOpen }">expand_more</span>
                        </button>

                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                        >
                            <div v-if="isModelOpen" class="absolute top-full left-0 mt-2 min-w-full bg-white rounded-2xl border border-gray-100 shadow-2xl py-2 z-[60] overflow-hidden backdrop-blur-xl">
                                <button
                                    v-for="folder in catalogFolders"
                                    :key="folder.key"
                                    @click="selectFolder(folder.key)"
                                    class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item whitespace-nowrap"
                                    :class="activeFolderKey === folder.key ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                >
                                    <span>{{ folder.label }}</span>
                                    <span v-if="activeFolderKey === folder.key" class="material-symbols-outlined text-sm ml-3">check_circle</span>
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>

                <!-- Variant Picker Button -->
                <div class="flex items-center gap-1 md:gap-3">
                    <span class="hidden md:inline-block text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Variant :</span>
                    <button
                        @click="openVariantPicker"
                        :disabled="catalogLoading"
                        class="h-9 md:h-10 pl-8 md:pl-9 pr-2 md:pr-4 w-[100px] sm:w-[130px] md:min-w-[180px] md:w-auto rounded-xl border border-gray-200 bg-gray-50/80 text-[11px] md:text-[12px] font-bold text-sumi transition-all duration-300 flex items-center gap-1 md:gap-2 hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group relative"
                    >
                        <span class="material-symbols-outlined absolute left-2.5 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">auto_awesome</span>
                        <span class="truncate flex-1 text-left">{{ activeVariantLabel || 'Varian' }}</span>
                        <span class="material-symbols-outlined text-sm text-gray-300 group-hover:text-indigo transition-colors duration-300 flex-shrink-0 hidden md:block">unfold_more</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-4 font-montserrat">
            <!-- Action Tools -->
            <div class="flex items-center bg-gray-50/80 rounded-xl md:rounded-2xl p-1 md:p-1.5 border border-gray-200 shadow-sm">
                <button @click="$emit('reset')" class="relative w-9 h-9 md:w-10 md:h-10 flex items-center justify-center text-usuzumi hover:text-red-500 hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Reset Design">
                    <span class="material-symbols-outlined text-lg md:text-xl transition-transform duration-300 group-hover:rotate-180">restart_alt</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- ========================
         VARIANT PICKER MODAL
         ======================== -->
    <Teleport to="body">
        <Transition name="variant-backdrop">
            <div v-if="isVariantOpen" class="fixed inset-0 z-[200] flex items-end sm:items-center justify-center sm:p-4" @click.self="isVariantOpen = false">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="isVariantOpen = false"></div>

                <!-- Modal -->
                <Transition name="variant-card">
                    <div v-if="isVariantOpen" class="variant-modal relative z-10 bg-white sm:rounded-[2rem] rounded-t-[2rem] shadow-2xl flex flex-col overflow-hidden w-full sm:max-w-sm max-h-[90vh]">

                        <!-- ── Header ── -->
                        <div class="flex items-start justify-between px-6 pt-5 pb-0 flex-shrink-0">
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

                        <!-- ── Mobile: Horizontal Scroll List ── -->
                        <div class="sm:hidden px-4 pt-4 pb-2 flex-shrink-0">
                            <div class="flex overflow-x-auto gap-3 pb-2 snap-x snap-mandatory variant-hscroll">
                                <div
                                    v-for="(model, index) in availableModels"
                                    :key="model.id"
                                    @click="confirmAndSelectVariant(model.id)"
                                    class="flex-shrink-0 w-[140px] snap-start cursor-pointer"
                                >
                                    <div
                                        class="relative rounded-2xl overflow-hidden border-2 transition-all duration-200 aspect-square bg-gray-50"
                                        :class="currentModel === model.id
                                            ? 'border-indigo ring-2 ring-indigo/20 scale-[0.97]'
                                            : 'border-transparent hover:border-gray-200'"
                                    >
                                        <div class="absolute inset-0 bg-gradient-radial"></div>
                                        <img
                                            v-if="thumbSrc(model)"
                                            :src="thumbSrc(model)"
                                            class="relative z-10 w-full h-full object-contain p-2 drop-shadow-lg"
                                            draggable="false"
                                            alt=""
                                        />
                                        <span v-else class="relative z-10 flex items-center justify-center h-full material-symbols-outlined text-5xl text-gray-300">checkroom</span>

                                        <!-- Active indicator -->
                                        <div
                                            v-if="currentModel === model.id"
                                            class="absolute top-2 right-2 z-20 w-6 h-6 rounded-full bg-indigo flex items-center justify-center shadow-lg"
                                        >
                                            <span class="material-symbols-outlined text-white text-[13px]">check</span>
                                        </div>
                                    </div>
                                    <p class="text-center text-[10px] font-black uppercase tracking-wider mt-1.5 font-montserrat"
                                        :class="currentModel === model.id ? 'text-indigo' : 'text-usuzumi'">
                                        {{ model.label }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ── Desktop: Vertical Carousel ── -->
                        <div class="relative flex-shrink-0 mt-4 hidden sm:block">

                            <!-- Desktop nav arrow — UP -->
                            <div class="absolute -top-1 left-1/2 -translate-x-1/2 z-20">
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
                                    class="variant-slide px-6 flex flex-col items-center justify-center cursor-pointer"
                                    @click="confirmAndSelectVariant(model.id)"
                                >
                                    <!-- Shoe Hero Image -->
                                    <div class="shoe-hero relative w-full aspect-square rounded-2xl overflow-hidden flex items-center justify-center bg-gray-50/50 border-2 transition-all duration-200"
                                        :class="currentModel === model.id ? 'border-indigo/30 bg-indigo/5' : 'border-gray-100/50'">
                                        <div class="absolute inset-0 bg-gradient-radial"></div>

                                        <!-- Lazy: only mount img when slide is near viewport -->
                                        <template v-if="isSlideVisible(index)">
                                            <img
                                                v-if="thumbSrc(model)"
                                                :src="thumbSrc(model)"
                                                class="relative z-10 w-[88%] h-[88%] object-contain shoe-img drop-shadow-2xl"
                                                draggable="false"
                                                alt=""
                                            />
                                            <span v-else class="relative z-10 material-symbols-outlined text-7xl text-gray-300">checkroom</span>
                                        </template>
                                        <!-- Placeholder when not yet in view -->
                                        <div v-else class="relative z-10 w-16 h-16 rounded-full bg-gray-200 animate-pulse"></div>

                                        <!-- Model Badge -->
                                        <div class="absolute top-5 right-5 z-20 flex items-center gap-1.5 bg-sumi text-white text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full shadow-xl transition-all duration-300" :class="currentModel === model.id ? 'bg-indigo ring-4 ring-indigo/20 scale-110' : 'opacity-80'">
                                            <span v-if="currentModel === model.id" class="material-symbols-outlined text-[12px]">verified</span>
                                            {{ model.label }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop nav arrow — DOWN -->
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 z-20">
                                <button
                                    @click="navigateVariant(1)"
                                    :disabled="centeredVariantIndex >= availableModels.length - 1"
                                    class="nav-arrow w-10 h-10 rounded-full border border-gray-200 bg-white shadow-md flex items-center justify-center text-gray-400 hover:text-indigo hover:border-indigo hover:shadow-lg transition-all duration-200 active:scale-95 disabled:opacity-20 disabled:cursor-not-allowed disabled:hover:text-gray-400 disabled:hover:border-gray-200 disabled:hover:shadow-md"
                                >
                                    <span class="material-symbols-outlined text-xl">keyboard_arrow_down</span>
                                </button>
                            </div>
                        </div>

                        <!-- ── Dot Indicators (desktop) ── -->
                        <div class="hidden sm:flex items-center justify-center gap-1.5 mt-2 flex-shrink-0 px-6 flex-wrap">
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
                        <div class="px-6 pb-6 pt-4 sm:pt-6 flex-shrink-0">
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
import { loadImage } from '../../utils/studio-utils';
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

defineEmits(['reset']);

// Each slide is square + padding. Modal max-width is 384px (sm).
// Content width is approx 336px.
const ITEM_HEIGHT = 336;

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
    if (generatingSet.has(model.id)) return;
    if (!model.layers?.length && !model.preview_base_file && !model.main_file) return;

    generatingSet.add(model.id);
    try {
        const baseUrl = `/shoes-svg/${activeFolderKey.value}/${model.id}/`;

        const baseFile = model.preview_base_file ?? model.main_file;
        if (!baseFile) return;

        const baseImg = await loadImage(baseUrl + baseFile);

        const SIZE = 400;
        // 5% inset on each side keeps the shoe away from the container's rounded corners
        const INSET = Math.round(SIZE * 0.05);
        const DRAW = SIZE - INSET * 2;

        const canvas = document.createElement('canvas');
        canvas.width = SIZE;
        canvas.height = SIZE;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        ctx.drawImage(baseImg, INSET, INSET, DRAW, DRAW);

        // Draw each accent layer with stored color or deterministic fallback
        for (const layer of (model.layers ?? [])) {
            const layerImg = await loadImage(baseUrl + layer.file);
            const color = layerColors.value[layer.id] ?? deterministicLayerColor(layer.id);

            // Build a padded layer canvas so it aligns with the padded base
            const layerCanvas = document.createElement('canvas');
            layerCanvas.width = SIZE;
            layerCanvas.height = SIZE;
            const lCtx = layerCanvas.getContext('2d');
            if (!lCtx) continue;
            lCtx.drawImage(layerImg, INSET, INSET, DRAW, DRAW);
            lCtx.globalCompositeOperation = 'source-in';
            lCtx.fillStyle = color;
            lCtx.fillRect(0, 0, SIZE, SIZE);

            ctx.drawImage(layerCanvas, 0, 0);
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

// Tap on any slide: immediately select
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

// Reactive color sync: when layerColors change, regenerate the current model's thumbnail
// so the variant picker always shows up-to-date colors.
let colorSyncTimer: ReturnType<typeof setTimeout> | null = null;

watch(layerColors, () => {
    if (colorSyncTimer) clearTimeout(colorSyncTimer);
    colorSyncTimer = setTimeout(() => {
        const model = availableModels.value.find(m => m.id === currentModel.value);
        if (model) {
            // Invalidate cached thumbnail so generateThumbnailForModel rebuilds it
            delete modelThumbnailURLs.value[model.id];
            generatingSet.delete(model.id);
            generateThumbnailForModel(model);
        }
    }, 250);
}, { deep: true });

// Click outside closes model dropdown only
const handleClickOutside = (event: MouseEvent) => {
    if (modelDropdownRef.value && !modelDropdownRef.value.contains(event.target as Node)) {
        isModelOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    if (colorSyncTimer) clearTimeout(colorSyncTimer);
});
</script>

<style scoped>
/* Hide scrollbar on all browsers */
.variant-scroll::-webkit-scrollbar { display: none; }
.variant-scroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
    overflow-y: scroll;
    scroll-snap-type: y mandatory;
    height: 300px;
}

@media (min-width: 640px) {
    .variant-scroll {
        height: 336px;
    }
}

/* Each slide fills the viewport exactly → one shoe visible at a time */
.variant-slide {
    height: 300px;
    scroll-snap-align: start;
    flex-shrink: 0;
}

@media (min-width: 640px) {
    .variant-slide {
        height: 336px;
    }
}

/* Horizontal scroll (mobile variant list) */
.variant-hscroll {
    -ms-overflow-style: none;
    scrollbar-width: none;
    scroll-behavior: smooth;
}
.variant-hscroll::-webkit-scrollbar { display: none; }

/* Shoe hero box */
.shoe-hero {
    /* Height is handled by aspect-square and parent padding */
}

/* Radial bg */
.shoe-hero .bg-gradient-radial,
.bg-gradient-radial {
    background: radial-gradient(ellipse at 50% 55%, #e8e8e8 0%, #f5f5f5 65%, #fafafa 100%);
}

/* Center the shoe — SVG viewboxes often have built-in whitespace */
.shoe-img {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.variant-slide:hover .shoe-img {
    transform: scale(1.08);
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

/* Mobile: card slides up from bottom */
@media (max-width: 639px) {
    .variant-card-enter-from { opacity: 0; transform: translateY(100%); }
    .variant-card-leave-to   { opacity: 0; transform: translateY(80%); }
}
</style>
