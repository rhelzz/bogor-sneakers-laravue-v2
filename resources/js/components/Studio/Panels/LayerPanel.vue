<template>
    <div class="space-y-6 font-montserrat">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-usuzumi">Panel Layer</span>
            <div class="h-[1px] flex-grow ml-4 bg-gray-100"></div>
        </div>
        
        <!-- Image Reference Section -->
        <div class="bg-shironeri rounded-3xl p-5 border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-md">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-indigo/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base text-indigo">image_search</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black uppercase tracking-widest text-sumi">Referensi Gambar</span>
                        <span class="text-[8px] font-medium text-usuzumi">Ekstraksi warna dari foto</span>
                    </div>
                </div>
                <button
                    v-if="referenceImage"
                    @click="clearReferenceImage"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-500 transition-all active:scale-90"
                    title="Hapus Referensi"
                >
                    <span class="material-symbols-outlined text-base">delete_sweep</span>
                </button>
            </div>

            <div v-if="!referenceImage" class="relative group">
                <input
                    type="file"
                    @change="saveReferenceImage"
                    accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                >
                <div class="py-10 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center gap-3 bg-white/50 group-hover:bg-white group-hover:border-indigo group-hover:shadow-lg transition-all duration-500">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center group-hover:scale-110 group-hover:bg-indigo/5 transition-all duration-500">
                        <span class="material-symbols-outlined text-gray-300 group-hover:text-indigo transition-colors">add_photo_alternate</span>
                    </div>
                    <span class="text-[9px] font-bold text-usuzumi uppercase tracking-widest text-center px-4">Upload foto untuk<br>mengambil palet warna</span>
                </div>
            </div>

            <div v-else class="space-y-5">
                <div class="relative group rounded-2xl overflow-hidden border border-gray-100 aspect-video bg-gray-50 shadow-inner">
                    <img
                        :src="referenceImage"
                        @click="pickColorFromRef"
                        class="w-full h-full object-contain cursor-crosshair transition-all duration-500 hover:scale-105 active:scale-95"
                        title="Klik untuk mengambil warna"
                    >
                    <div class="absolute inset-0 pointer-events-none border-2 border-transparent group-hover:border-indigo/20 rounded-2xl transition-all duration-500"></div>
                </div>

                <!-- Extracted Palette -->
                <div v-if="extractedPalette.length > 0" class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[8px] font-black uppercase text-usuzumi tracking-widest">Palet Terdeteksi</span>
                        <div class="flex items-center gap-2">
                            <button
                                @click="randomizeFromRef"
                                class="h-7 px-3 rounded-lg bg-white border border-gray-100 text-[8px] font-black text-indigo uppercase flex items-center gap-1.5 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all"
                            >
                                <span class="material-symbols-outlined text-[12px]">casino</span>
                                Acak
                            </button>
                            <button
                                @click="applyExtractedPalette"
                                class="h-7 px-3 rounded-lg bg-indigo text-washi text-[8px] font-black uppercase flex items-center gap-1.5 hover:shadow-lg hover:shadow-indigo/20 hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all"
                            >
                                <span class="material-symbols-outlined text-[12px]">auto_fix</span>
                                Terapkan
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-2">
                        <button
                            v-for="color in extractedPalette"
                            :key="`ext-${color}`"
                            class="aspect-square rounded-lg border-2 border-white shadow-sm hover:scale-125 hover:rotate-3 hover:shadow-md transition-all duration-300 active:scale-90"
                            :style="{ backgroundColor: color }"
                            @click="activeLayerPickId !== null ? setLayerColor(activeLayerPickId, color) : showToast('Pilih layer dulu')"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-usuzumi">Aksen Sepatu</span>
            <div class="h-[1px] flex-grow bg-gray-100"></div>
        </div>

        <!-- Global Outline Control -->
        <div v-if="layerIds.length > 0" class="bg-gray-50/50 rounded-2xl p-4 border border-gray-100 flex items-center justify-between transition-all duration-300 hover:shadow-sm">
            <div class="flex items-center gap-3">
                <div class="relative flex items-center">
                    <input
                        type="checkbox"
                        id="global-outline"
                        :checked="isGlobalOutlineActive"
                        @change="toggleGlobalOutline"
                        class="w-4 h-4 rounded border-gray-300 text-indigo focus:ring-indigo transition-all cursor-pointer"
                    >
                </div>
                <label for="global-outline" class="text-[9px] font-black uppercase text-sumi tracking-widest cursor-pointer hover:text-indigo transition-colors">Outline Semua Layer</label>
            </div>
            
            <transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-x-4 scale-95"
                enter-to-class="opacity-100 translate-x-0 scale-100"
            >
                <div v-if="isGlobalOutlineActive" class="flex items-center gap-3">
                    <div class="relative w-6 h-6 rounded-full overflow-hidden border-2 border-white shadow-sm hover:scale-110 transition-transform">
                        <input
                            type="color"
                            :value="globalOutlineColor"
                            @input="updateGlobalOutlineColor"
                            class="absolute inset-[-4px] w-[200%] h-[200%] cursor-pointer"
                            title="Warna Outline Global"
                        >
                    </div>
                    <input
                        type="range"
                        :value="globalOutlineSize"
                        @input="updateGlobalOutlineSize"
                        min="1" max="10"
                        class="w-20 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo"
                        title="Ukuran Outline Global"
                    >
                </div>
            </transition>
        </div>

        <div v-if="layerIds.length === 0" class="flex flex-col items-center justify-center py-20 opacity-30">
            <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center animate-pulse">
                <span class="material-symbols-outlined text-4xl text-usuzumi">extension</span>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-usuzumi mt-4">Menyiapkan Layer...</p>
        </div>

        <div v-else class="grid gap-3">
            <div
                v-for="id in layerIds"
                :key="`layer-${id}`"
                class="group relative overflow-hidden rounded-2xl border transition-all duration-500"
                :class="activeLayerPickId === id
                    ? 'border-indigo bg-indigo/[0.02] shadow-md'
                    : 'border-gray-100 bg-white hover:border-indigo/30 hover:shadow-sm cursor-pointer'"
                @click="activeLayerPickId = activeLayerPickId === id ? null : id"
            >
                <!-- Selection Indicator -->
                <div 
                    class="absolute left-0 top-0 w-1 h-full bg-indigo transition-transform duration-500"
                    :class="activeLayerPickId === id ? 'scale-y-100' : 'scale-y-0'"
                ></div>

                <div class="p-4 flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-3">
                        <div 
                            class="w-10 h-10 rounded-xl flex items-center justify-center text-[10px] font-black transition-all duration-500 group-hover:scale-110"
                            :style="{ 
                                backgroundColor: layerColors[id] + '20', 
                                color: layerColors[id]
                            }"
                        >
                            {{ id }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black uppercase tracking-widest text-sumi">Aksen {{ id }}</span>
                            <span class="text-[8px] font-medium text-usuzumi uppercase tracking-tighter">{{ layerColors[id] }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3" @click.stop>
                        <div class="flex items-center bg-gray-50/50 rounded-xl px-3 py-1.5 border border-gray-100 focus-within:border-indigo focus-within:bg-white transition-all duration-300">
                            <span class="text-[10px] font-bold text-gray-300 mr-1">#</span>
                            <input
                                type="text"
                                :value="layerColors[id]?.replace('#', '')"
                                @input="(e) => {
                                    const val = (e.target as HTMLInputElement).value;
                                    if (val.length === 6 || val.length === 3) setLayerColor(id, '#' + val)
                                }"
                                class="w-14 bg-transparent border-none p-0 text-[11px] font-black uppercase tracking-widest text-sumi focus:ring-0"
                                maxlength="6"
                            >
                        </div>
                        <div class="relative w-9 h-9 group/picker">
                            <div
                                class="w-full h-full rounded-full border-4 border-white shadow-md transition-all duration-500 group-hover/picker:scale-110 cursor-pointer"
                                :style="{ backgroundColor: layerColors[id] || '#ffffff' }"
                            ></div>
                            <input
                                type="color"
                                :value="layerColors[id] || '#ffffff'"
                                @input="(e) => setLayerColor(id, (e.target as HTMLInputElement).value)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                title="Pilih Warna"
                            >
                        </div>
                    </div>
                </div>

                <transition
                    enter-active-class="transition-all duration-500 ease-out"
                    leave-active-class="transition-all duration-300 ease-in"
                    enter-from-class="max-h-0 opacity-0 translate-y-[-10px]"
                    enter-to-class="max-h-[300px] opacity-100 translate-y-0"
                    leave-from-class="max-h-[300px] opacity-100 translate-y-0"
                    leave-to-class="max-h-0 opacity-0 translate-y-[-10px]"
                >
                    <div v-if="activeLayerPickId === id" class="px-4 pb-4 pt-1 border-t border-gray-50 space-y-4">
                        <div class="space-y-2">
                            <span class="text-[8px] font-black uppercase text-usuzumi tracking-[0.2em]">Palet Rekomendasi</span>
                            <div class="grid grid-cols-8 gap-2">
                                <button
                                    v-for="color in randomPalette"
                                    :key="color"
                                    class="w-full aspect-square rounded-lg border-2 border-white shadow-sm hover:scale-125 hover:rotate-6 transition-all duration-300 active:scale-90"
                                    :style="{ backgroundColor: color }"
                                    @click.stop="setLayerColor(id, color)"
                                ></button>
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 flex items-center justify-between" @click.stop>
                            <div class="flex items-center gap-3">
                                <div class="relative flex items-center">
                                    <input
                                        type="checkbox"
                                        :id="`outline-${id}`"
                                        v-model="layerOutlines[id].active"
                                        @change="emit('save-history')"
                                        class="w-4 h-4 rounded border-gray-300 text-indigo focus:ring-indigo transition-all cursor-pointer"
                                    >
                                </div>
                                <label :for="`outline-${id}`" class="text-[9px] font-black uppercase text-sumi tracking-widest cursor-pointer hover:text-indigo transition-colors">Efek Outline</label>
                            </div>
                            <transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="opacity-0 translate-x-4 scale-95"
                                enter-to-class="opacity-100 translate-x-0 scale-100"
                            >
                                <div v-if="layerOutlines[id].active" class="flex items-center gap-3">
                                    <div class="relative w-6 h-6 rounded-full overflow-hidden border-2 border-white shadow-sm hover:scale-110 transition-transform">
                                        <input
                                            type="color"
                                            v-model="layerOutlines[id].color"
                                            @change="emit('save-history')"
                                            class="absolute inset-[-4px] w-[200%] h-[200%] cursor-pointer"
                                        >
                                    </div>
                                    <input
                                        type="range"
                                        v-model.number="layerOutlines[id].size"
                                        @change="emit('save-history')"
                                        min="1" max="10"
                                        class="w-20 h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo"
                                    >
                                </div>
                            </transition>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useStudioStore } from '../../../composables/useStudioStore';
import { generatePaletteFromDataUrl } from '../../../utils/studio-utils';

const { 
    layerColors, 
    layerOutlines, 
    currentModelMeta, 
    showToast 
} = useStudioStore();

const activeLayerPickId = ref<number | null>(null);
const referenceImage = ref<string | null>(null);
const extractedPalette = ref<string[]>([]);
const layerIds = computed(() => currentModelMeta.value?.layers.map(l => l.id) || []);

const isGlobalOutlineActive = computed(() => {
    if (layerIds.value.length === 0) return false;
    return layerIds.value.every(id => layerOutlines.value[id]?.active);
});

const globalOutlineColor = computed(() => {
    const firstActive = layerIds.value.find(id => layerOutlines.value[id]?.active);
    return firstActive ? layerOutlines.value[firstActive].color : '#000000';
});

const globalOutlineSize = computed(() => {
    const firstActive = layerIds.value.find(id => layerOutlines.value[id]?.active);
    return firstActive ? layerOutlines.value[firstActive].size : 2;
});

const toggleGlobalOutline = (e: Event) => {
    const checked = (e.target as HTMLInputElement).checked;
    layerIds.value.forEach(id => {
        if (!layerOutlines.value[id]) {
            layerOutlines.value[id] = { active: checked, color: '#000000', size: 2 };
        } else {
            layerOutlines.value[id].active = checked;
        }
    });
    emit('save-history');
};

const updateGlobalOutlineColor = (e: Event) => {
    const color = (e.target as HTMLInputElement).value;
    layerIds.value.forEach(id => {
        if (layerOutlines.value[id] && layerOutlines.value[id].active) {
            layerOutlines.value[id].color = color;
        }
    });
    emit('save-history');
};

const updateGlobalOutlineSize = (e: Event) => {
    const size = parseInt((e.target as HTMLInputElement).value);
    layerIds.value.forEach(id => {
        if (layerOutlines.value[id] && layerOutlines.value[id].active) {
            layerOutlines.value[id].size = size;
        }
    });
    emit('save-history');
};

const FALLBACK_PALETTE = [
    '#1a1a1a', '#f7f5f0', '#7c8c5a', '#2c4a6e', '#d4a5a5', '#3d5a4c', '#8a8a8a', '#4a4a4a',
    '#e2e8f0', '#cbd5e1', '#94a3b8', '#64748b', '#475569', '#334155', '#1e293b', '#0f172a'
];
const randomPalette = ref(FALLBACK_PALETTE);

const emit = defineEmits(['update-layer', 'save-history']);

onMounted(() => {
    referenceImage.value = localStorage.getItem('studio_ref_image');
    const storedPalette = localStorage.getItem('studio_extracted_palette');
    if (storedPalette) {
        extractedPalette.value = JSON.parse(storedPalette);
    }
});

const setLayerColor = (id: number, color: string) => {
    layerColors.value[id] = color;
    emit('update-layer', id);
    emit('save-history');
};

const saveReferenceImage = async (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async (event) => {
        const dataUrl = event.target?.result as string;
        referenceImage.value = dataUrl;
        localStorage.setItem('studio_ref_image', dataUrl);
        extractedPalette.value = await generatePaletteFromDataUrl(dataUrl);
        localStorage.setItem('studio_extracted_palette', JSON.stringify(extractedPalette.value));
        showToast('Palet warna berhasil diekstrak!');
    };
    reader.readAsDataURL(file);
};

const clearReferenceImage = () => {
    referenceImage.value = null;
    extractedPalette.value = [];
    localStorage.removeItem('studio_ref_image');
    localStorage.removeItem('studio_extracted_palette');
    showToast('Referensi dihapus');
};

const applyExtractedPalette = () => {
    if (extractedPalette.value.length === 0) return;
    layerIds.value.forEach((id, index) => {
        const color = extractedPalette.value[index % extractedPalette.value.length];
        setLayerColor(id, color);
    });
    showToast('Tema warna dari foto diterapkan!');
};

const randomizeFromRef = () => {
    if (extractedPalette.value.length === 0) {
        showToast('Upload foto dulu');
        return;
    }
    layerIds.value.forEach(id => {
        const randomColor = extractedPalette.value[Math.floor(Math.random() * extractedPalette.value.length)];
        setLayerColor(id, randomColor);
    });
    showToast('Warna diacak dari foto!');
};

const pickColorFromRef = (e: MouseEvent) => {
    if (activeLayerPickId.value === null) {
        showToast('Pilih layer aksen dulu');
        return;
    }

    const img = e.target as HTMLImageElement;
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d')!;
    canvas.width = img.naturalWidth;
    canvas.height = img.naturalHeight;
    ctx.drawImage(img, 0, 0);

    const rect = img.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * img.naturalWidth;
    const y = ((e.clientY - rect.top) / rect.height) * img.naturalHeight;

    const pixel = ctx.getImageData(x, y, 1, 1).data;
    const hex = '#' + ((1 << 24) + (pixel[0] << 16) + (pixel[1] << 8) + pixel[2]).toString(16).slice(1);
    setLayerColor(activeLayerPickId.value, hex);
    showToast(`Warna ${hex} dipilih!`);
};
</script>

<style scoped>
/* Scrollbar Styling for internal use if needed */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}

/* Range Input Styling */
input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 14px;
    height: 14px;
    background: #4f46e5;
    border: 3px solid white;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s;
}

input[type="range"]::-webkit-slider-thumb:hover {
    transform: scale(1.2);
}
</style>
