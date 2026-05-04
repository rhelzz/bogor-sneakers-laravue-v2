<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-black uppercase tracking-widest text-secondary opacity-60">Daftar Layer</span>
        </div>
        
        <!-- Image Reference Section -->
        <div class="mb-8 pb-6 border-b border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-secondary">image_search</span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-secondary opacity-60">Ref. Gambar</span>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        v-if="referenceImage"
                        @click="clearReferenceImage"
                        class="text-[9px] font-black text-red-500 uppercase hover:underline"
                    >Hapus</button>
                </div>
            </div>

            <div v-if="!referenceImage" class="relative group">
                <input
                    type="file"
                    @change="saveReferenceImage"
                    accept="image/*"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                >
                <div class="py-6 border-2 border-dashed border-gray-100 rounded-2xl flex flex-col items-center justify-center gap-2 bg-gray-50/50 group-hover:bg-white group-hover:border-primary transition-all">
                    <span class="material-symbols-outlined text-gray-300">add_photo_alternate</span>
                    <span class="text-[8px] font-black text-secondary uppercase tracking-tighter">Klik untuk tambah referensi</span>
                </div>
            </div>

            <div v-else class="space-y-4">
                <div class="relative group rounded-2xl overflow-hidden border border-gray-100 aspect-video bg-gray-50">
                    <img
                        :src="referenceImage"
                        @click="pickColorFromRef"
                        class="w-full h-full object-contain cursor-crosshair transition-transform active:scale-95"
                        title="Klik pada gambar untuk mengambil warna ke layer aktif"
                    >
                </div>

                <!-- Extracted Palette -->
                <div v-if="extractedPalette.length > 0" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[8px] font-black uppercase text-secondary tracking-widest opacity-50">Ekstraksi Warna</span>
                        <div class="flex items-center gap-3">
                            <button
                                @click="randomizeFromRef"
                                class="text-[8px] font-black text-primary uppercase hover:underline flex items-center gap-1"
                            >
                                <span class="material-symbols-outlined text-[10px]">casino</span>
                                Acak Palet
                            </button>
                            <button
                                @click="applyExtractedPalette"
                                class="text-[8px] font-black text-primary uppercase hover:underline flex items-center gap-1"
                            >
                                <span class="material-symbols-outlined text-[10px]">auto_fix</span>
                                Terapkan
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-1.5">
                        <button
                            v-for="color in extractedPalette"
                            :key="`ext-${color}`"
                            class="aspect-square rounded-lg border border-white shadow-sm hover:scale-110 transition-transform active:scale-90"
                            :style="{ backgroundColor: color }"
                            @click="activeLayerPickId !== null ? setLayerColor(activeLayerPickId, color) : showToast('Pilih layer aksen dulu')"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="layerIds.length === 0" class="flex flex-col items-center justify-center py-20 opacity-20">
            <span class="material-symbols-outlined text-6xl mb-4 text-secondary">extension</span>
            <p class="text-xs font-black uppercase tracking-widest text-secondary">Loading Layers...</p>
        </div>

        <div v-else class="grid gap-2">
            <div
                v-for="id in layerIds"
                :key="`layer-${id}`"
                class="group relative overflow-hidden rounded-xl border transition-all duration-300"
                :class="activeLayerPickId === id
                    ? 'border-primary bg-primary/5 shadow-sm'
                    : 'border-gray-100 bg-white hover:border-gray-200 cursor-pointer'"
                @click="activeLayerPickId = activeLayerPickId === id ? null : id"
            >
                <div class="p-3 flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded bg-gray-50 flex items-center justify-center text-[9px] font-black text-secondary">
                            {{ id }}
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest">Aksen {{ id }}</span>
                    </div>

                    <div class="flex items-center gap-2" @click.stop>
                        <div class="flex items-center bg-white rounded-lg px-2 py-1 border border-gray-100 shadow-sm focus-within:border-primary transition-colors">
                            <span class="text-[9px] font-bold text-gray-400 mr-1">#</span>
                            <input
                                type="text"
                                :value="layerColors[id]?.replace('#', '')"
                                @input="(e) => {
                                    const val = (e.target as HTMLInputElement).value;
                                    if (val.length === 6 || val.length === 3) setLayerColor(id, '#' + val)
                                }"
                                class="w-12 bg-transparent border-none p-0 text-[10px] font-black uppercase tracking-tighter focus:ring-0"
                                maxlength="6"
                            >
                        </div>
                        <div class="relative w-7 h-7 group/picker">
                            <div
                                class="w-full h-full rounded-full border-2 border-white shadow-sm transition-transform group-hover/picker:scale-110 cursor-pointer"
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

                <transition name="expand">
                    <div v-if="activeLayerPickId === id" class="px-3 pb-3 pt-1 border-t border-gray-100/50 space-y-3">
                        <div class="grid grid-cols-8 gap-1.5">
                            <button
                                v-for="color in randomPalette"
                                :key="color"
                                class="w-full aspect-square rounded-md border border-white shadow-sm hover:scale-110 transition-transform active:scale-90"
                                :style="{ backgroundColor: color }"
                                @click.stop="setLayerColor(id, color)"
                            ></button>
                        </div>

                        <div class="flex items-center justify-between p-2.5 bg-white rounded-xl border border-gray-100/50" @click.stop>
                            <div class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    :id="`outline-${id}`"
                                    v-model="layerOutlines[id].active"
                                    class="w-3.5 h-3.5 rounded border-gray-300 text-black focus:ring-black transition-all cursor-pointer"
                                >
                                <label :for="`outline-${id}`" class="text-[8px] font-black uppercase text-secondary tracking-widest cursor-pointer">Outline</label>
                            </div>
                            <div v-if="layerOutlines[id].active" class="flex items-center gap-2">
                                <input
                                    type="color"
                                    v-model="layerOutlines[id].color"
                                    class="w-5 h-5 rounded-full overflow-hidden border-none p-0 cursor-pointer shadow-sm"
                                >
                                <input
                                    type="range"
                                    v-model.number="layerOutlines[id].size"
                                    min="1" max="10"
                                    class="w-16 accent-black"
                                >
                            </div>
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

const FALLBACK_PALETTE = [
    '#000000', '#ffffff', '#d0ff00', '#ff0000', '#0000ff', '#808080', '#a855f7', '#f97316',
    '#7c8c5a', '#d97781', '#4a9d6f', '#1a1a1a', '#f7f5f0', '#888888', '#b4b4b4', '#5a6b6a'
];
const randomPalette = ref(FALLBACK_PALETTE);

const emit = defineEmits(['updateLayer', 'saveHistory']);

onMounted(() => {
    referenceImage.value = localStorage.getItem('studio_ref_image');
    const storedPalette = localStorage.getItem('studio_extracted_palette');
    if (storedPalette) {
        extractedPalette.value = JSON.parse(storedPalette);
    }
});

const setLayerColor = (id: number, color: string) => {
    layerColors.value[id] = color;
    emit('updateLayer', id);
    emit('saveHistory');
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
    };
    reader.readAsDataURL(file);
};

const clearReferenceImage = () => {
    referenceImage.value = null;
    extractedPalette.value = [];
    localStorage.removeItem('studio_ref_image');
    localStorage.removeItem('studio_extracted_palette');
};

const applyExtractedPalette = () => {
    if (extractedPalette.value.length === 0) return;
    layerIds.value.forEach((id, index) => {
        const color = extractedPalette.value[index % extractedPalette.value.length];
        setLayerColor(id, color);
    });
    showToast('Tema warna diterapkan!');
};

const randomizeFromRef = () => {
    if (extractedPalette.value.length === 0) {
        showToast('Upload referensi gambar dulu');
        return;
    }
    layerIds.value.forEach(id => {
        const randomColor = extractedPalette.value[Math.floor(Math.random() * extractedPalette.value.length)];
        setLayerColor(id, randomColor);
    });
    showToast('Warna diacak dari referensi!');
};

const pickColorFromRef = (e: MouseEvent) => {
    if (activeLayerPickId.value === null) {
        showToast('Pilih layer aksen terlebih dahulu');
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
};
</script>

<style scoped>
.expand-enter-active, .expand-leave-active {
    transition: all 0.3s ease;
    max-height: 200px;
    opacity: 1;
    overflow: hidden;
}
.expand-enter-from, .expand-leave-to {
    max-height: 0;
    opacity: 0;
}
</style>
