<template>
    <div class="flex flex-col h-full bg-[#111118] text-white select-none overflow-hidden" style="width: 300px; min-width: 300px;">
        <!-- Header -->
        <div class="shrink-0 flex items-center justify-between px-5 py-4 border-b border-white/5">
            <div class="flex items-center gap-2.5">
                <div class="w-2 h-2 rounded-full bg-primary animate-pulse shadow-[0_0_8px_#d0ff00]"></div>
                <span class="text-[10px] font-black tracking-[0.25em] uppercase font-montserrat text-white/70">Custom Studio V2</span>
            </div>
            <div class="flex items-center gap-1">
                <button
                    @click="$emit('collapse-desktop')"
                    class="hidden md:flex w-7 h-7 items-center justify-center rounded-lg hover:bg-white/10 text-white/40 hover:text-white transition-colors"
                    title="Tutup panel"
                >
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </button>
                <button
                    @click="$emit('close-mobile')"
                    class="md:hidden flex w-7 h-7 items-center justify-center rounded-lg hover:bg-white/10 text-white/40 hover:text-white transition-colors"
                >
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
        </div>

        <!-- Shoe type selector -->
        <div class="shrink-0 px-4 py-3 border-b border-white/5 bg-white/2">
            <p class="text-[8px] font-black tracking-[0.2em] uppercase text-white/30 font-montserrat mb-2">Jenis Sepatu</p>
            <div class="relative">
                <button
                    @click="isTypeDropdownOpen = !isTypeDropdownOpen"
                    class="w-full flex items-center justify-between gap-2 px-3 py-2 rounded-lg bg-white/5 hover:bg-white/8 border border-white/8 hover:border-white/15 transition-all duration-200"
                    :class="isTypeDropdownOpen ? 'border-primary/40 bg-white/8' : ''"
                >
                    <span class="text-[10px] font-black tracking-wider uppercase font-montserrat text-white/80">{{ catalog.label }}</span>
                    <span
                        class="material-symbols-outlined text-sm text-white/40 transition-transform duration-200"
                        :class="isTypeDropdownOpen ? 'rotate-180' : ''"
                    >expand_more</span>
                </button>

                <transition name="dropdown">
                    <div
                        v-if="isTypeDropdownOpen"
                        class="absolute top-full left-0 right-0 mt-1 z-50 rounded-xl border border-white/10 bg-[#1a1a28] shadow-[0_8px_32px_rgba(0,0,0,0.5)] overflow-hidden"
                    >
                        <button
                            v-for="type in props.shoeTypes"
                            :key="type.id"
                            @click="selectShoeType(type.id)"
                            class="w-full flex items-center gap-3 px-3 py-2.5 text-left transition-all duration-150"
                            :class="catalog.id === type.id
                                ? 'bg-primary/15 text-primary'
                                : 'text-white/50 hover:bg-white/5 hover:text-white/80'"
                        >
                            <span
                                class="w-1.5 h-1.5 rounded-full shrink-0"
                                :class="catalog.id === type.id ? 'bg-primary' : 'bg-white/20'"
                            ></span>
                            <span class="text-[10px] font-black tracking-wider uppercase font-montserrat">{{ type.label }}</span>
                            <span v-if="catalog.id === type.id" class="material-symbols-outlined text-xs ml-auto">check</span>
                        </button>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Accent slots (scrollable) -->
        <div
            ref="scrollContainerRef"
            class="grow overflow-y-auto overflow-x-hidden custom-scrollbar-dark"
        >
            <!-- Bulk visibility controls -->
            <div class="flex items-center justify-between px-4 py-2 border-b border-white/5">
                <span class="text-[8px] font-black tracking-[0.2em] uppercase text-white/20 font-montserrat">Layer Aksen</span>
                <div class="flex items-center gap-1">
                    <button
                        @click="setAllEnabled(true)"
                        class="flex items-center gap-1 px-2 py-1 rounded-md text-[8px] font-black tracking-wider uppercase font-montserrat transition-all duration-150 text-white/40 hover:text-primary hover:bg-primary/10"
                        title="Tampilkan semua aksen"
                    >
                        <span class="material-symbols-outlined text-xs">visibility</span>
                        Semua
                    </button>
                    <div class="w-px h-3 bg-white/10"></div>
                    <button
                        @click="setAllEnabled(false)"
                        class="flex items-center gap-1 px-2 py-1 rounded-md text-[8px] font-black tracking-wider uppercase font-montserrat transition-all duration-150 text-white/40 hover:text-white/70 hover:bg-white/5"
                        title="Sembunyikan semua aksen"
                    >
                        <span class="material-symbols-outlined text-xs">visibility_off</span>
                        Semua
                    </button>
                </div>
            </div>

            <!-- Top layer label -->
            <div class="flex items-center gap-2 px-5 py-1.5">
                <span class="material-symbols-outlined text-[10px] text-white/20">arrow_upward</span>
                <span class="text-[8px] text-white/20 tracking-widest uppercase">atas canvas</span>
            </div>

            <div
                v-for="(slot, index) in props.layerOrder"
                :key="slot"
                draggable="true"
                @dragstart="onDragStart($event, slot)"
                @dragover.prevent="onDragOver($event, slot)"
                @drop="onDrop($event, slot)"
                @dragend="onDragEnd"
                class="group border-b border-white/5 transition-all duration-150 relative"
                :class="[
                    accentEnabled[slot] === false ? 'opacity-40' : '',
                    activeSlot === slot && accentEnabled[slot] !== false ? 'bg-white/6' : 'hover:bg-white/3',
                    draggingSlot === slot ? 'opacity-30 scale-[0.98]' : '',
                ]"
                @click="accentEnabled[slot] !== false && (activeSlot = slot)"
            >
                <!-- Drop indicator: insert before (top half) -->
                <div
                    v-if="dragOverSlot === slot && draggingSlot !== slot && insertBefore"
                    class="absolute top-0 left-0 right-0 h-0.5 bg-primary shadow-[0_0_6px_#d0ff00] z-10 rounded-full"
                ></div>
                <!-- Drop indicator: insert after (bottom half) -->
                <div
                    v-if="dragOverSlot === slot && draggingSlot !== slot && !insertBefore"
                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary shadow-[0_0_6px_#d0ff00] z-10 rounded-full"
                ></div>

                <!-- Slot header -->
                <div class="flex items-center justify-between px-3 pt-4 pb-2">
                    <div class="flex items-center gap-2">
                        <!-- Drag handle -->
                        <span
                            @click.stop
                            class="material-symbols-outlined text-base text-white/15 hover:text-white/50 cursor-grab active:cursor-grabbing transition-colors shrink-0"
                            title="Drag untuk reorder layer"
                        >drag_indicator</span>

                        <!-- Enable/disable toggle -->
                        <button
                            @click.stop="$emit('update:accentEnabled', slot, accentEnabled[slot] === false)"
                            class="w-5 h-5 flex items-center justify-center rounded transition-colors"
                            :class="accentEnabled[slot] === false ? 'text-white/20 hover:text-white/50' : 'text-primary/80 hover:text-primary'"
                            :title="accentEnabled[slot] === false ? 'Aktifkan aksen' : 'Nonaktifkan aksen'"
                        >
                            <span class="material-symbols-outlined text-sm">
                                {{ accentEnabled[slot] === false ? 'visibility_off' : 'visibility' }}
                            </span>
                        </button>
                        <span class="text-[9px] font-black tracking-[0.2em] uppercase font-montserrat"
                              :class="activeSlot === slot && accentEnabled[slot] !== false ? 'text-primary' : 'text-white/40'">
                            AKSEN {{ slot }}
                        </span>
                        <span v-if="activeSlot === slot && accentEnabled[slot] !== false" class="w-1 h-1 rounded-full bg-primary animate-pulse"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Z-position badge -->
                        <span class="text-[8px] font-mono text-white/20 tabular-nums">Z{{ index + 1 }}</span>
                        <!-- Model dots indicator -->
                        <div class="flex items-center gap-0.75">
                            <div
                                v-for="m in catalog.totalModels"
                                :key="m"
                                class="rounded-full transition-all duration-200"
                                :class="[
                                    isModelAvailable(slot, m) ? 'opacity-100' : 'opacity-20',
                                    accentModels[slot] === m ? 'w-2.5 h-2.5 bg-primary' : 'w-1.5 h-1.5 bg-white/30'
                                ]"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Carousel row -->
                <div class="flex items-center gap-3 px-4 pb-3">
                    <!-- Prev button -->
                    <button
                        @click.stop="cycleModel(slot, -1)"
                        class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 active:scale-90"
                        :class="catalog.getAvailableModels(slot).length > 1 && loadingSlot !== slot && accentEnabled[slot] !== false
                            ? 'bg-white/10 hover:bg-white/20 text-white/70 hover:text-white'
                            : 'bg-white/5 text-white/20 cursor-not-allowed'"
                        :disabled="catalog.getAvailableModels(slot).length <= 1 || loadingSlot === slot || accentEnabled[slot] === false"
                    >
                        <span class="material-symbols-outlined text-base">chevron_left</span>
                    </button>

                    <!-- Model display -->
                    <div class="grow flex flex-col items-center gap-1">
                        <transition name="model-flip" mode="out-in">
                            <div v-if="loadingSlot === slot" key="loading" class="text-center">
                                <div class="w-4 h-4 border-2 border-primary/40 border-t-primary rounded-full animate-spin mx-auto"></div>
                            </div>
                            <div v-else :key="`slot${slot}-m${accentModels[slot]}`" class="text-center">
                                <span class="text-xs font-black tracking-widest font-montserrat"
                                      :class="accentEnabled[slot] === false ? 'text-white/30 line-through' : 'text-white'">
                                    M{{ accentModels[slot] ?? 1 }}
                                </span>
                            </div>
                        </transition>
                        <div class="text-[9px] text-white/30 font-medium">
                            {{ accentEnabled[slot] === false ? 'off' : `${catalog.getAvailableModels(slot).indexOf(accentModels[slot] ?? 1) + 1} / ${catalog.getAvailableModels(slot).length}` }}
                        </div>
                    </div>

                    <!-- Next button -->
                    <button
                        @click.stop="cycleModel(slot, 1)"
                        class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all duration-200 active:scale-90"
                        :class="catalog.getAvailableModels(slot).length > 1 && loadingSlot !== slot && accentEnabled[slot] !== false
                            ? 'bg-white/10 hover:bg-white/20 text-white/70 hover:text-white'
                            : 'bg-white/5 text-white/20 cursor-not-allowed'"
                        :disabled="catalog.getAvailableModels(slot).length <= 1 || loadingSlot === slot || accentEnabled[slot] === false"
                    >
                        <span class="material-symbols-outlined text-base">chevron_right</span>
                    </button>

                    <!-- Color swatch + picker -->
                    <div class="shrink-0 relative">
                        <button
                            @click.stop="accentEnabled[slot] !== false && toggleColorPicker(slot)"
                            class="w-8 h-8 rounded-lg border-2 transition-all duration-200"
                            :class="[
                                accentEnabled[slot] !== false ? 'hover:scale-110 active:scale-95 cursor-pointer' : 'cursor-not-allowed opacity-50',
                                openColorSlot === slot ? 'border-primary shadow-[0_0_12px_#d0ff00]' : 'border-white/20'
                            ]"
                            :style="{ backgroundColor: accentColors[slot] }"
                            title="Pilih warna aksen"
                        ></button>
                    </div>
                </div>

                <!-- Inline color picker -->
                <transition name="picker-expand">
                    <div v-if="openColorSlot === slot && accentEnabled[slot] !== false" @click.stop class="px-4 pb-4">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-5 h-5 rounded shrink-0 border border-white/20" :style="{ backgroundColor: accentColors[slot] }"></div>
                            <input
                                type="text"
                                :value="accentColors[slot]"
                                @change="onHexInput(slot, ($event.target as HTMLInputElement).value)"
                                class="grow bg-white/5 border border-white/10 rounded-md px-2 py-1 text-[11px] font-mono text-white/80 focus:outline-none focus:border-primary/60 uppercase"
                                maxlength="7"
                                placeholder="#FFFFFF"
                            />
                            <input
                                type="color"
                                :value="accentColors[slot]"
                                @input="onColorInput(slot, ($event.target as HTMLInputElement).value)"
                                class="w-8 h-8 rounded cursor-pointer bg-transparent border-0 p-0"
                            />
                        </div>
                        <div class="grid grid-cols-8 gap-1.5">
                            <button
                                v-for="preset in PRESET_PALETTE"
                                :key="preset"
                                @click.stop="emitColor(slot, preset)"
                                class="w-full aspect-square rounded-md border transition-all duration-150 hover:scale-110 active:scale-95"
                                :style="{ backgroundColor: preset }"
                                :class="accentColors[slot] === preset ? 'border-primary shadow-[0_0_6px_#d0ff00]' : 'border-white/10'"
                                :title="preset"
                            ></button>
                        </div>
                    </div>
                </transition>
            </div>

            <!-- Bottom layer label -->
            <div class="flex items-center gap-2 px-5 py-1.5">
                <span class="material-symbols-outlined text-[10px] text-white/20">arrow_downward</span>
                <span class="text-[8px] text-white/20 tracking-widest uppercase">bawah canvas</span>
            </div>
        </div>

        <!-- Footer: Checkout button -->
        <div class="shrink-0 p-4 border-t border-white/5 bg-[#0d0d14]">
            <button
                @click="$emit('checkout')"
                class="w-full py-3 rounded-xl font-black text-[11px] tracking-[0.2em] uppercase font-montserrat transition-all duration-200 active:scale-95 flex items-center justify-center gap-2"
                style="background: linear-gradient(135deg, #d0ff00 0%, #a8cc00 100%); color: #111118;"
            >
                <span class="material-symbols-outlined text-base">shopping_bag</span>
                Order Sekarang
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onUnmounted, ref } from 'vue';
import type { ShoeTypeCatalog } from '@/data/studio-v2-catalog';

const props = defineProps<{
    catalog: ShoeTypeCatalog;
    shoeTypes: ShoeTypeCatalog[];
    accentModels: Record<number, number>;
    accentColors: Record<number, string>;
    accentEnabled: Record<number, boolean>;
    loadingSlot?: number | null;
    layerOrder: number[];
}>();

const emit = defineEmits<{
    'update:accentModel': [slot: number, model: number];
    'update:accentColor': [slot: number, color: string];
    'update:accentEnabled': [slot: number, enabled: boolean];
    'update:shoeType': [typeId: string];
    'update:layerOrder': [order: number[]];
    'collapse-desktop': [];
    'close-mobile': [];
    'checkout': [];
}>();

const activeSlot = ref<number>(props.layerOrder[0] ?? props.catalog.allAccentSlots[0] ?? 1);
const openColorSlot = ref<number | null>(null);
const isTypeDropdownOpen = ref(false);

// ─── Drag & drop layer reorder ────────────────────────────────────────────────
const scrollContainerRef = ref<HTMLDivElement | null>(null);
const draggingSlot = ref<number | null>(null);
const dragOverSlot = ref<number | null>(null);
const insertBefore = ref(true);

const SCROLL_ZONE = 80;
const MAX_SCROLL_SPEED = 10;
let autoScrollFrame = 0;
let lastDragY = 0;

const runAutoScroll = () => {
    const container = scrollContainerRef.value;
    if (!container || draggingSlot.value === null) { autoScrollFrame = 0; return; }

    const rect = container.getBoundingClientRect();
    const distFromTop = lastDragY - rect.top;
    const distFromBottom = rect.bottom - lastDragY;

    let speed = 0;
    if (distFromTop < SCROLL_ZONE && distFromTop > 0)
        speed = -MAX_SCROLL_SPEED * (1 - distFromTop / SCROLL_ZONE);
    else if (distFromBottom < SCROLL_ZONE && distFromBottom > 0)
        speed = MAX_SCROLL_SPEED * (1 - distFromBottom / SCROLL_ZONE);

    if (speed !== 0) container.scrollTop += speed;
    autoScrollFrame = requestAnimationFrame(runAutoScroll);
};

const stopAutoScroll = () => {
    if (autoScrollFrame) { cancelAnimationFrame(autoScrollFrame); autoScrollFrame = 0; }
};

// Track cursor globally during drag — avoids dragleave bubbling issues from child elements
const trackDragY = (e: DragEvent) => { lastDragY = e.clientY; };

onUnmounted(() => {
    window.removeEventListener('dragover', trackDragY);
    stopAutoScroll();
});

const onDragStart = (e: DragEvent, slot: number) => {
    draggingSlot.value = slot;
    lastDragY = e.clientY;
    if (e.dataTransfer) {
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', String(slot));
    }
    window.addEventListener('dragover', trackDragY);
    autoScrollFrame = requestAnimationFrame(runAutoScroll);
};

const onDragOver = (e: DragEvent, slot: number) => {
    e.preventDefault();
    if (e.dataTransfer) e.dataTransfer.dropEffect = 'move';
    dragOverSlot.value = slot;
    // Detect top/bottom half to show insert-before vs insert-after indicator
    const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
    insertBefore.value = e.clientY < rect.top + rect.height / 2;
};

const onDrop = (e: DragEvent, targetSlot: number) => {
    e.preventDefault();
    const sourceSlot = draggingSlot.value;
    const before = insertBefore.value;
    draggingSlot.value = null;
    dragOverSlot.value = null;
    window.removeEventListener('dragover', trackDragY);
    stopAutoScroll();

    if (sourceSlot === null || sourceSlot === targetSlot) return;

    const newOrder = [...props.layerOrder];
    newOrder.splice(newOrder.indexOf(sourceSlot), 1);
    const toIdx = newOrder.indexOf(targetSlot);
    newOrder.splice(before ? toIdx : toIdx + 1, 0, sourceSlot);
    emit('update:layerOrder', newOrder);
};

const onDragEnd = () => {
    draggingSlot.value = null;
    dragOverSlot.value = null;
    window.removeEventListener('dragover', trackDragY);
    stopAutoScroll();
};

const selectShoeType = (typeId: string) => {
    isTypeDropdownOpen.value = false;
    emit('update:shoeType', typeId);
};

const setAllEnabled = (enabled: boolean) => {
    props.catalog.allAccentSlots.forEach(slot => {
        emit('update:accentEnabled', slot, enabled);
    });
};

const PRESET_PALETTE = [
    '#FFFFFF', '#000000', '#1A1A2E', '#16213E',
    '#C8A96E', '#8B7355', '#D4C5A0', '#6B5B45',
    '#E63946', '#F4A261', '#2A9D8F', '#264653',
    '#6B8E6B', '#4A7C59', '#8B6B6B', '#7B8B9B',
    '#E9C46A', '#457B9D', '#A8DADC', '#F1FAEE',
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
];

const isModelAvailable = (slot: number, model: number): boolean =>
    props.catalog.accentSlots[model]?.includes(slot) ?? false;

const cycleModel = (slot: number, direction: 1 | -1) => {
    const available = props.catalog.getAvailableModels(slot);
    if (available.length <= 1) return;

    const current = props.accentModels[slot] ?? available[0];
    const idx = available.indexOf(current);
    const next = available[(idx + direction + available.length) % available.length];
    emit('update:accentModel', slot, next);
};

const toggleColorPicker = (slot: number) => {
    openColorSlot.value = openColorSlot.value === slot ? null : slot;
};

const emitColor = (slot: number, color: string) => {
    emit('update:accentColor', slot, color);
};

const onColorInput = (slot: number, value: string) => {
    emitColor(slot, value);
};

const onHexInput = (slot: number, value: string) => {
    const clean = value.startsWith('#') ? value : `#${value}`;
    if (/^#[0-9a-fA-F]{6}$/.test(clean)) {
        emitColor(slot, clean);
    }
};
</script>

<style scoped>
.model-flip-enter-active,
.model-flip-leave-active {
    transition: all 0.15s ease;
}
.model-flip-enter-from {
    opacity: 0;
    transform: translateY(-6px);
}
.model-flip-leave-to {
    opacity: 0;
    transform: translateY(6px);
}

.picker-expand-enter-active,
.picker-expand-leave-active {
    transition: all 0.2s ease;
    overflow: hidden;
}
.picker-expand-enter-from,
.picker-expand-leave-to {
    opacity: 0;
    max-height: 0;
}
.picker-expand-enter-to,
.picker-expand-leave-from {
    opacity: 1;
    max-height: 200px;
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.15s ease;
    transform-origin: top;
}
.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: scaleY(0.9) translateY(-4px);
}
.dropdown-enter-to,
.dropdown-leave-from {
    opacity: 1;
    transform: scaleY(1) translateY(0);
}

.custom-scrollbar-dark::-webkit-scrollbar {
    width: 3px;
}
.custom-scrollbar-dark::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar-dark::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}
</style>
