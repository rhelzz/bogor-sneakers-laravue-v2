<template>
    <transition name="fade">
        <div v-if="activeElement" class="mt-4 p-5 rounded-2xl bg-gray-50 border border-gray-100 space-y-4 shadow-xl shadow-black/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-white shadow-sm flex items-center justify-center">
                        <span class="material-symbols-outlined text-xs text-primary">
                            {{ activeElement.type === 'text' ? 'text_fields' : 'Category' }}
                        </span>
                    </div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-black">
                        Edit {{ activeElement.type === 'text' ? 'Teks' : 'Logo' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="$emit('remove')" class="w-7 h-7 rounded-lg bg-red-500/10 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all active:scale-90" title="Hapus Elemen">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                    <button @click="deselect" class="w-7 h-7 rounded-lg bg-gray-200 text-gray-500 flex items-center justify-center hover:bg-gray-300 hover:text-sumi transition-all active:scale-90" title="Tutup Panel">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            </div>

            <!-- Image/Logo Specific Controls -->
            <div v-if="activeElement.type === 'image' && activeElement.outline" class="space-y-3">
                <div class="p-3 bg-white rounded-xl border border-gray-100/50 space-y-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                :id="`logo-outline-${activeElement.id}`"
                                v-model="activeElement.outline.active"
                                @change="$emit('updateImageOutline')"
                                class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black transition-all"
                            >
                            <label :for="`logo-outline-${activeElement.id}`" class="text-[11px] font-black uppercase text-sumi tracking-widest cursor-pointer">Outline Logo</label>
                        </div>
                    </div>

                    <div v-if="activeElement.outline.active" class="space-y-3 pt-3 border-t border-gray-50 mt-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-usuzumi tracking-widest opacity-40">Warna & Ukuran</span>
                            <div class="flex items-center gap-2">
                                <input type="color" v-model="activeElement.outline.color" @input="$emit('updateImageOutline')" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                                <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors">
                                    <input type="number" v-model.number="activeElement.outline.size" @input="$emit('updateImageOutline')" min="1" max="15" class="w-8 text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                    <span class="text-[9px] font-black text-gray-400 select-none">px</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Text Specific Controls -->
            <div v-if="activeElement.type === 'text'" class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest ml-1 opacity-40">Gaya & Konten Teks</label>
                    <div class="grid gap-2">
                        <select
                            v-model="activeElement.fontFamily"
                            class="w-full h-10 px-4 bg-white border border-gray-100 rounded-xl text-[11px] font-bold text-black focus:border-primary focus:ring-0 transition-all appearance-none cursor-pointer shadow-sm"
                        >
                            <option v-for="font in FONT_OPTIONS" :key="font.value" :value="font.value">
                                {{ font.label }}
                            </option>
                        </select>
                        <textarea
                            v-model="activeElement.text"
                            class="w-full p-4 bg-white border border-gray-100 rounded-2xl text-[12px] font-bold text-black focus:border-primary focus:ring-0 transition-all resize-none shadow-sm"
                            rows="2"
                            placeholder="Masukkan teks di sini..."
                        ></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest block mb-2 opacity-40">Warna</label>
                        <input type="color" v-model="activeElement.color" class="w-full h-8 p-0 border-none bg-transparent cursor-pointer">
                    </div>
                    <div class="p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                        <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest block mb-2 opacity-40">Stroke (Warna & Ukuran)</label>
                        <div class="flex items-center gap-2">
                            <input type="color" v-model="activeElement.strokeColor" class="w-6 h-6 rounded-full border-2 border-white shadow-sm p-0 cursor-pointer overflow-hidden">
                            <div class="flex items-center gap-1 bg-gray-50 border border-gray-200 rounded-lg px-2 py-0.5 shadow-sm focus-within:border-primary transition-colors flex-grow">
                                <input type="number" v-model.number="activeElement.strokeWidth" min="0" max="15" class="w-full text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent">
                                <span class="text-[9px] font-black text-gray-400 select-none">px</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Positioning & Masking Controls (For both text and image) -->
            <div class="space-y-4 pt-4 border-t border-gray-100">

                <!-- Mirror Toggle -->
                <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[14px] text-indigo">flip</span>
                            <span class="text-[10px] font-black uppercase tracking-widest text-sumi">Mirror Pola</span>
                        </div>
                        <!-- Toggle Switch -->
                        <button
                            @click="toggleMirror"
                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200 focus:outline-none"
                            :class="isMirrorOn ? 'bg-indigo' : 'bg-gray-200'"
                            :title="isMirrorOn ? 'Mirror aktif' : 'Mirror mati'"
                        >
                            <span
                                class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                                :class="isMirrorOn ? 'translate-x-4' : 'translate-x-0.5'"
                            />
                        </button>
                    </div>
                    <p class="text-[9px] text-gray-400 font-semibold leading-tight">
                        <span v-if="isMirrorOn">Elemen muncul di <b>semua zona</b> pola (atas & bawah).</span>
                        <span v-else>Pilih sisi zona pola yang akan digunakan:</span>
                    </p>

                    <!-- Sisi Kiri / Kanan (hanya muncul saat mirror mati) -->
                    <transition name="fade">
                        <div v-if="!isMirrorOn" class="grid grid-cols-2 gap-2 pt-1">
                            <button
                                @click="setMirrorSide('left')"
                                class="flex flex-col items-center gap-1 py-2 px-3 rounded-lg border-2 text-[10px] font-black uppercase tracking-wider transition-all duration-200"
                                :class="activeElement.mirrorSide !== 'right'
                                    ? 'border-indigo bg-indigo/5 text-indigo'
                                    : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:text-gray-600'"
                            >
                                <span class="material-symbols-outlined text-base">flip_to_back</span>
                                Kiri
                            </button>
                            <button
                                @click="setMirrorSide('right')"
                                class="flex flex-col items-center gap-1 py-2 px-3 rounded-lg border-2 text-[10px] font-black uppercase tracking-wider transition-all duration-200"
                                :class="activeElement.mirrorSide === 'right'
                                    ? 'border-indigo bg-indigo/5 text-indigo'
                                    : 'border-gray-100 text-gray-400 hover:border-gray-200 hover:text-gray-600'"
                            >
                                <span class="material-symbols-outlined text-base">flip_to_front</span>
                                Kanan
                            </button>
                        </div>
                    </transition>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-usuzumi tracking-widest ml-1 opacity-40 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">layers</span>
                        Masukkan ke Aksen (Clipping Mask)
                    </label>

                    <!-- Custom Masking Dropdown -->
                    <div class="relative" ref="maskDropdownRef">
                        <button
                            @click="isMaskDropdownOpen = !isMaskDropdownOpen"
                            class="w-full h-10 px-4 bg-white border border-gray-100 rounded-xl text-[11px] font-bold text-black flex items-center justify-between hover:border-indigo hover:shadow-md transition-all active:scale-95 group shadow-sm"
                        >
                            <span :class="!activeElement.maskId ? 'text-gray-400' : 'text-black'">
                                {{ activeElement.maskId ? `Aksen ${activeElement.maskId}` : 'Belum di masking' }}
                            </span>
                            <span class="material-symbols-outlined text-base text-gray-400 group-hover:text-indigo transition-transform duration-300" :class="{ 'rotate-180': isMaskDropdownOpen }">expand_more</span>
                        </button>

                        <transition name="dropdown-slide">
                            <div v-if="isMaskDropdownOpen" class="absolute top-full mt-2 left-0 right-0 bg-white rounded-2xl border border-gray-100 shadow-2xl py-2 z-[70] overflow-hidden backdrop-blur-xl">
                                <div class="px-4 py-1.5 border-b border-gray-50">
                                    <span class="text-[8px] font-black uppercase text-gray-400 tracking-widest">Pilih Aksen Masking</span>
                                </div>
                                <button
                                    @click="selectMask(null)"
                                    class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item"
                                    :class="!activeElement.maskId ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                >
                                    <span>Belum di masking</span>
                                    <span v-if="!activeElement.maskId" class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                                <div class="h-px bg-gray-50 mx-2 my-1"></div>
                                <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                    <button
                                        v-for="layer in currentModelMeta?.layers || []"
                                        :key="layer.id"
                                        @click="selectMask(layer.id)"
                                        class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item"
                                        :class="activeElement.maskId === layer.id ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                    >
                                        <span>Aksen {{ layer.id }}</span>
                                        <span v-if="activeElement.maskId === layer.id" class="material-symbols-outlined text-sm">check_circle</span>
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>

                <div v-if="!currentModelMeta?.studio_config" class="space-y-3 bg-amber-50/60 p-3 rounded-xl border border-amber-100">
                    <!-- Header -->
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-md bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-[13px] text-amber-600">tune</span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-[10px] font-black uppercase tracking-widest text-sumi leading-none">Koreksi Cetak</p>
                            <p class="text-[8px] text-amber-600/70 font-semibold mt-0.5 leading-tight">Geser cetakan sisi sepatu sebelahnya</p>
                        </div>
                        <button
                            @click="resetPrintCorrection"
                            class="flex-shrink-0 flex items-center gap-0.5 text-[8px] font-black uppercase tracking-wider text-gray-400 hover:text-red-400 transition-colors active:scale-95 px-2 py-1 rounded-lg hover:bg-red-50"
                            title="Reset ke 0"
                        >
                            <span class="material-symbols-outlined text-[11px]">restart_alt</span>
                            Reset
                        </button>
                    </div>

                    <!-- Stepper controls -->
                    <div class="grid grid-cols-3 gap-2">
                        <!-- X -->
                        <div class="bg-white rounded-xl border border-amber-100 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-2 py-1 bg-gray-50/80 border-b border-gray-100">
                                <span class="material-symbols-outlined text-[11px] text-blue-400">arrow_forward</span>
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">X px</span>
                            </div>
                            <div class="flex items-center h-9">
                                <button @click="adjustValue('mx', -1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">remove</span>
                                </button>
                                <input type="number" v-model.number="activeElement.mx" class="flex-1 text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent min-w-0">
                                <button @click="adjustValue('mx', 1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">add</span>
                                </button>
                            </div>
                        </div>

                        <!-- Y -->
                        <div class="bg-white rounded-xl border border-amber-100 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-2 py-1 bg-gray-50/80 border-b border-gray-100">
                                <span class="material-symbols-outlined text-[11px] text-green-400">arrow_downward</span>
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">Y px</span>
                            </div>
                            <div class="flex items-center h-9">
                                <button @click="adjustValue('my', -1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">remove</span>
                                </button>
                                <input type="number" v-model.number="activeElement.my" class="flex-1 text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent min-w-0">
                                <button @click="adjustValue('my', 1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">add</span>
                                </button>
                            </div>
                        </div>

                        <!-- Rotation -->
                        <div class="bg-white rounded-xl border border-amber-100 shadow-sm overflow-hidden">
                            <div class="flex items-center justify-between px-2 py-1 bg-gray-50/80 border-b border-gray-100">
                                <span class="material-symbols-outlined text-[11px] text-purple-400">rotate_right</span>
                                <span class="text-[8px] font-black text-gray-400 uppercase tracking-wider">Sudut</span>
                            </div>
                            <div class="flex items-center h-9">
                                <button @click="adjustValue('mrot', -1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">remove</span>
                                </button>
                                <input type="number" v-model.number="activeElement.mrot" class="flex-1 text-[11px] font-bold text-center border-none p-0 focus:ring-0 bg-transparent min-w-0">
                                <button @click="adjustValue('mrot', 1)" class="w-8 h-full flex items-center justify-center text-gray-300 hover:text-indigo hover:bg-indigo/5 transition-colors active:scale-90 flex-shrink-0">
                                    <span class="material-symbols-outlined text-base">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useStudioStore } from '../../../composables/useStudioStore';
import { useKonvaRenderer } from '../../../composables/useKonvaRenderer';

const { activeElement, currentModelMeta } = useStudioStore();
const { deselect } = useKonvaRenderer();

defineEmits(['remove', 'updateImageOutline']);

const isMaskDropdownOpen = ref(false);
const maskDropdownRef = ref<HTMLElement | null>(null);

// mirror === false berarti mirror dimatikan; undefined/true = aktif (default)
const isMirrorOn = computed(() => activeElement.value?.mirror !== false);

const toggleMirror = () => {
    if (!activeElement.value) return;
    if (activeElement.value.mirror === false) {
        activeElement.value.mirror = true;
        activeElement.value.mirrorSide = undefined;
    } else {
        activeElement.value.mirror = false;
        activeElement.value.mirrorSide = 'left';
    }
};

const setMirrorSide = (side: 'left' | 'right') => {
    if (!activeElement.value) return;
    activeElement.value.mirrorSide = side;
};

const selectMask = (id: number | null) => {
    if (activeElement.value) {
        activeElement.value.maskId = id;
    }
    isMaskDropdownOpen.value = false;
};

const adjustValue = (field: 'mx' | 'my' | 'mrot', delta: number) => {
    if (!activeElement.value) return;
    activeElement.value[field] = (activeElement.value[field] ?? 0) + delta;
};

const resetPrintCorrection = () => {
    if (!activeElement.value) return;
    activeElement.value.mx = 0;
    activeElement.value.my = 0;
    activeElement.value.mrot = 0;
};

const handleClickOutside = (event: MouseEvent) => {
    if (maskDropdownRef.value && !maskDropdownRef.value.contains(event.target as Node)) {
        isMaskDropdownOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener('mousedown', handleClickOutside);
});

onUnmounted(() => {
    window.removeEventListener('mousedown', handleClickOutside);
});

const FONT_OPTIONS = [
    { label: 'Modern (Lexend)', value: 'Lexend' },
    { label: 'Clean (Inter)', value: 'Inter' },
    { label: 'Bold (Roboto)', value: 'Roboto' },
    { label: 'Classic (Playfair)', value: 'Playfair Display' },
    { label: 'Sporty (Oswald)', value: 'Oswald' },
    { label: 'Technical (JetBrains)', value: 'JetBrains Mono' },
    { label: 'Retro (Bebas Neue)', value: 'Bebas Neue' }
];
</script>

<style scoped>
.dropdown-slide-enter-active,
.dropdown-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-slide-enter-from,
.dropdown-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}
</style>
