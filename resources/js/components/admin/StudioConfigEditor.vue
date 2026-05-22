<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { StudioConfig, PatternZone } from '@/types/studio';

interface ShoeVariant {
    id: number;
    name: string;
    studio_config?: StudioConfig | null;
}

const props = defineProps<{
    variant: ShoeVariant;
    modelSlug: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const DEFAULT_PREVIEW_ZONE = { x: 0, y: 0, width: 1024, height: 1024 };
const DEFAULT_PATTERN_CANVAS = 2048;

// Build a mutable deep copy of the config
const buildDefaultConfig = (): StudioConfig => ({
    preview_zone: props.variant.studio_config?.preview_zone
        ? { ...props.variant.studio_config.preview_zone }
        : { ...DEFAULT_PREVIEW_ZONE },
    pattern_zones: props.variant.studio_config?.pattern_zones
        ? props.variant.studio_config.pattern_zones.map(z => ({ ...z }))
        : [
            { id: 'main', x: 64, y: 128, width: 896, height: 896, flip_x: false, rotation: 0 },
            { id: 'mirror', x: 1088, y: 128, width: 896, height: 896, flip_x: true, rotation: 0 },
          ],
});

const config = reactive<StudioConfig>(buildDefaultConfig());

const form = useForm<{ studio_config: StudioConfig }>({
    studio_config: config,
});

// Keep form in sync with reactive config
watch(config, (val) => { form.studio_config = val; }, { deep: true });

const addZone = () => {
    config.pattern_zones.push({
        id: `zone_${Date.now()}`,
        x: 0,
        y: 0,
        width: DEFAULT_PATTERN_CANVAS / 2,
        height: DEFAULT_PATTERN_CANVAS / 2,
        flip_x: false,
        rotation: 0,
    });
};

const removeZone = (index: number) => {
    if (config.pattern_zones.length <= 1) return;
    config.pattern_zones.splice(index, 1);
};

const variantFolder = computed(() => props.variant.name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, ''));

const previewBaseUrl = computed(
    () => `/shoes-svg/${props.modelSlug}/${variantFolder.value}/`
);

const save = () => {
    // Build route manually since wayfinder may not have studio-config yet
    const url = `/admin/variants/${props.variant.id}/studio-config`;
    form.put(url, {
        onSuccess: () => emit('close'),
    });
};

const numericInput = (zone: PatternZone, field: keyof PatternZone, value: string) => {
    (zone as any)[field] = Number(value);
};
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" @click.self="$emit('close')">
        <div class="w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl text-left">
            <!-- Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Studio Config — {{ variant.name }}</h3>
                    <p class="text-xs text-slate-400">Atur zona koordinat preview dan pola untuk penempatan artwork otomatis.</p>
                </div>
                <button @click="$emit('close')" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-8">
                <!-- Preview Zone -->
                <section>
                    <h4 class="mb-3 text-sm font-bold text-slate-700 uppercase tracking-widest">Preview Zone (canvas 1024×1024)</h4>
                    <p class="mb-4 text-xs text-slate-400">Area di canvas preview yang menjadi bidang kerja user. Biasanya seluruh canvas: x=0, y=0, width=1024, height=1024.</p>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div v-for="field in (['x', 'y', 'width', 'height'] as const)" :key="field">
                            <label class="mb-1 block text-xs font-bold text-slate-600 uppercase">{{ field }}</label>
                            <input
                                v-model.number="config.preview_zone[field]"
                                type="number"
                                :min="field === 'width' || field === 'height' ? 1 : 0"
                                :max="1024"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                            />
                        </div>
                    </div>
                </section>

                <!-- Pattern Zones -->
                <section>
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-sm font-bold text-slate-700 uppercase tracking-widest">Pattern Zones (canvas 2048×2048)</h4>
                            <p class="mt-0.5 text-xs text-slate-400">Setiap zona memetakan posisi artwork dari preview ke area tertentu di file pola.</p>
                        </div>
                        <button
                            type="button"
                            @click="addZone"
                            class="flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-600 hover:bg-indigo-100 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Zona
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="(zone, idx) in config.pattern_zones"
                            :key="idx"
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-xs font-bold text-white">{{ idx + 1 }}</span>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">ID Zona</label>
                                        <input
                                            v-model="zone.id"
                                            type="text"
                                            placeholder="contoh: main, mirror"
                                            class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400/20"
                                        />
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="removeZone(idx)"
                                    :disabled="config.pattern_zones.length <= 1"
                                    class="rounded-lg p-1.5 text-slate-400 hover:bg-rose-50 hover:text-rose-500 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                    title="Hapus zona"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Coordinate inputs -->
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-3">
                                <div v-for="field in (['x', 'y', 'width', 'height'] as const)" :key="field">
                                    <label class="mb-1 block text-xs font-bold text-slate-500 uppercase">{{ field }}</label>
                                    <input
                                        v-model.number="zone[field]"
                                        type="number"
                                        :min="field === 'width' || field === 'height' ? 1 : 0"
                                        :max="2048"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10"
                                    />
                                </div>
                            </div>

                            <!-- Flip & Rotation -->
                            <div class="flex items-center gap-6">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input
                                        v-model="zone.flip_x"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                    />
                                    <span class="text-xs font-bold text-slate-600">Flip X (Mirror)</span>
                                </label>

                                <div class="flex items-center gap-2">
                                    <label class="text-xs font-bold text-slate-500 uppercase">Rotasi (°)</label>
                                    <input
                                        v-model.number="zone.rotation"
                                        type="number"
                                        min="-360"
                                        max="360"
                                        class="w-20 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-sm outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400/20"
                                    />
                                </div>
                            </div>

                            <!-- Zone preview (visual rect) -->
                            <div class="mt-3 rounded-xl bg-white border border-slate-100 p-3">
                                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Visualisasi pada canvas 2048×2048</p>
                                <div class="relative bg-slate-100 rounded-lg overflow-hidden" style="height: 80px;">
                                    <div
                                        :style="{
                                            position: 'absolute',
                                            left: `${(zone.x / 2048) * 100}%`,
                                            top: `${(zone.y / 2048) * 100}%`,
                                            width: `${(zone.width / 2048) * 100}%`,
                                            height: `${(zone.height / 2048) * 100}%`,
                                            background: zone.flip_x ? 'rgba(239,68,68,0.3)' : 'rgba(99,102,241,0.3)',
                                            border: zone.flip_x ? '1.5px solid rgb(239,68,68)' : '1.5px solid rgb(99,102,241)',
                                            borderRadius: '4px',
                                        }"
                                    >
                                        <span class="absolute inset-0 flex items-center justify-center text-[8px] font-bold"
                                            :class="zone.flip_x ? 'text-red-600' : 'text-indigo-600'"
                                        >
                                            {{ zone.id }}{{ zone.flip_x ? ' (flip)' : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Error display -->
                <div v-if="Object.keys(form.errors).length > 0" class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                    <p class="text-sm font-bold text-rose-600 mb-1">Terjadi kesalahan validasi:</p>
                    <ul class="list-disc list-inside text-xs text-rose-500 space-y-0.5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>
            </div>

            <!-- Footer -->
            <div class="sticky bottom-0 z-10 flex justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4">
                <button type="button" @click="$emit('close')" class="rounded-xl px-5 py-2.5 font-bold text-slate-500 hover:bg-slate-100 transition-colors">
                    Batal
                </button>
                <button
                    type="button"
                    @click="save"
                    :disabled="form.processing"
                    class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-indigo-700 disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Config' }}
                </button>
            </div>
        </div>
    </div>
</template>
