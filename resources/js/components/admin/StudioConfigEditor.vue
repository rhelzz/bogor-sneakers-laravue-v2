<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useElementSize } from '@vueuse/core';
import type { StudioConfig } from '@/types/studio';

interface SVGAsset {
    id: number;
    file_name: string;
    file_path: string;
}

interface ShoeVariant {
    id: number;
    name: string;
    studio_config?: StudioConfig | null;
    svgs?: SVGAsset[];
}

const props = defineProps<{
    variant: ShoeVariant;
    modelSlug: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const DEFAULT_PREVIEW_ZONE = { x: 0, y: 0, width: 1024, height: 1024 };

const buildPreviewZone = () =>
    props.variant.studio_config?.preview_zone
        ? { ...props.variant.studio_config.preview_zone }
        : { ...DEFAULT_PREVIEW_ZONE };

const config = reactive({ preview_zone: buildPreviewZone() });
const form = useForm({ studio_config: { preview_zone: config.preview_zone } });
watch(config, (val) => { form.studio_config = val; }, { deep: true });

const save = () => {
    form.put(`/admin/variants/${props.variant.id}/studio-config`, {
        onSuccess: () => emit('close'),
    });
};

const previewBaseSvg = computed(() => props.variant.svgs?.find(s => s.file_name.toLowerCase().includes('_base') && !s.file_name.toLowerCase().includes('_pola')));

const canvasRef = ref<HTMLElement | null>(null);
const { width: canvasWidth } = useElementSize(canvasRef);
const isDragging = ref(false);
const dragStartPos = ref({ x: 0, y: 0 });
const dragInitialZonePos = ref({ x: 0, y: 0 });

const startDrag = (e: MouseEvent) => {
    isDragging.value = true;
    dragStartPos.value = { x: e.clientX, y: e.clientY };
    dragInitialZonePos.value = { x: config.preview_zone.x, y: config.preview_zone.y };

    const move = (me: MouseEvent) => {
        const scale = 1024 / (canvasWidth.value || 1);
        const dx = (me.clientX - dragStartPos.value.x) * scale;
        const dy = (me.clientY - dragStartPos.value.y) * scale;
        config.preview_zone.x = Math.round(dragInitialZonePos.value.x + dx);
        config.preview_zone.y = Math.round(dragInitialZonePos.value.y + dy);
    };
    const up = () => {
        isDragging.value = false;
        window.removeEventListener('mousemove', move);
        window.removeEventListener('mouseup', up);
    };
    window.addEventListener('mousemove', move);
    window.addEventListener('mouseup', up);
};
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
        appear
    >
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="$emit('close')">
            <div class="w-full max-w-6xl max-h-[90vh] flex flex-col rounded-[2.5rem] bg-white shadow-2xl overflow-hidden border border-slate-200">

                <!-- Header -->
                <div class="flex items-center justify-between px-10 py-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 tracking-tight">Studio Config Editor</h3>
                        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-widest mt-0.5">{{ variant.name }}</p>
                    </div>

                    <button @click="$emit('close')" class="p-2 rounded-xl hover:bg-slate-100 text-slate-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Main Content -->
                <div class="flex-1 flex overflow-hidden">

                    <!-- Left Pane: Interactive Visualizer -->
                    <div class="flex-1 bg-slate-50 p-10 flex flex-col items-center justify-center border-r border-slate-100 relative">
                        <div class="absolute top-10">
                            <span class="px-4 py-1.5 bg-white border border-slate-200 rounded-full text-[10px] font-bold text-slate-500 uppercase tracking-widest shadow-sm">
                                <span class="inline-block w-2 h-2 rounded-full bg-indigo-500 mr-2 animate-pulse"></span>
                                Interaksi Live — Klik & Seret untuk Geser
                            </span>
                        </div>

                        <div
                            ref="canvasRef"
                            class="relative w-full max-w-[500px] aspect-square bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden cursor-crosshair select-none group"
                        >
                            <!-- Grid Overlay -->
                            <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#000 1px, transparent 0); background-size: 20px 20px;"></div>

                            <!-- Base SVG -->
                            <div class="absolute inset-0 flex items-center justify-center p-6">
                                <img
                                    v-if="previewBaseSvg"
                                    :src="`/${previewBaseSvg.file_path}`"
                                    class="w-full h-full object-contain opacity-80"
                                    draggable="false"
                                />
                            </div>

                            <!-- Preview Zone (Drag & Drop) -->
                            <div
                                @mousedown="startDrag($event)"
                                class="absolute border-4 border-indigo-600 bg-indigo-600/5 rounded-2xl flex items-center justify-center cursor-grab active:cursor-grabbing shadow-lg transition-shadow active:shadow-indigo-500/20"
                                :style="{
                                    left: `${(config.preview_zone.x / 1024) * 100}%`,
                                    top: `${(config.preview_zone.y / 1024) * 100}%`,
                                    width: `${(config.preview_zone.width / 1024) * 100}%`,
                                    height: `${(config.preview_zone.height / 1024) * 100}%`,
                                }"
                            >
                                <span class="bg-indigo-600 text-white text-[9px] font-bold px-3 py-1 rounded-lg uppercase tracking-widest shadow-xl">Workspace</span>
                            </div>

                            <div class="absolute bottom-4 left-6 right-6 flex justify-between text-[9px] font-bold text-slate-400 uppercase tracking-widest pointer-events-none">
                                <span>Unit: 1024</span>
                                <span>Status: Terkoneksi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Pane: Form -->
                    <div class="w-[420px] bg-white p-10 overflow-y-auto flex flex-col">
                        <header class="mb-10">
                            <h4 class="text-lg font-bold text-slate-800 mb-2">Koordinat Preview</h4>
                            <p class="text-xs text-slate-400 leading-relaxed font-medium">Tentukan area interaksi utama. Nilai ini menjadi referensi pemetaan pola.</p>
                        </header>

                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="f in (['x', 'y'] as const)" :key="f" class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">{{ f }} Offset</label>
                                    <input v-model.number="config.preview_zone[f]" type="number" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 outline-none focus:border-indigo-400 focus:bg-white transition-all shadow-sm" />
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-for="f in (['width', 'height'] as const)" :key="f" class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">{{ f }}</label>
                                    <input v-model.number="config.preview_zone[f]" type="number" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-700 outline-none focus:border-indigo-400 focus:bg-white transition-all shadow-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-8 flex justify-end gap-3 border-t border-slate-50">
                            <button @click="$emit('close')" class="px-6 py-3 rounded-2xl text-xs font-bold text-slate-400 hover:bg-slate-50 transition-all">Batalkan</button>
                            <button @click="save" :disabled="form.processing" class="px-8 py-3 rounded-2xl bg-indigo-600 text-white text-xs font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 disabled:opacity-50 transition-all">Simpan Config</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.animate-in {
  animation: animate-in 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes animate-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
