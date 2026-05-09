<template>
    <div class="w-[320px] md:w-[350px] flex-shrink-0 bg-white border-l border-gray-200 flex flex-col h-full shadow-[-10px_0_30px_rgba(0,0,0,0.02)] font-montserrat">
        <!-- Header Panel -->
        <div class="p-6 pb-4">
            <div class="flex flex-col">
                <h3 class="text-sm font-black text-sumi uppercase tracking-[0.2em] leading-tight">
                    {{ hudTitle }}
                </h3>
                <p class="text-[9px] text-usuzumi font-bold mt-1.5 tracking-[0.15em] uppercase opacity-70">{{ hudSubtitle }}</p>
            </div>

            <ActiveElementControls 
                @remove="$emit('remove-element')"
                @updateImageOutline="$emit('update-image-outline')"
            />
        </div>

        <!-- Content Panel -->
        <div class="flex-grow overflow-y-auto custom-scrollbar px-6 py-2">
            <LayerPanel 
                v-if="activeSideTab === 'layers'" 
                @update-layer="(id) => $emit('update-layer', id)"
                @save-history="$emit('save-history')"
            />
            <ArtworkPanel 
                v-if="activeSideTab === 'artwork'" 
                @add-media="(id) => $emit('add-media', id)"
            />
            <TextPanel 
                v-if="activeSideTab === 'text'" 
                @add-text="$emit('add-text')"
            />
        </div>

        <!-- Bottom Panel (Price & Action) -->
        <div class="p-6 border-t border-gray-100 bg-shironeri/50 backdrop-blur-sm">
            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-usuzumi uppercase tracking-[0.2em]">Estimasi Total</span>
                    <span class="text-2xl font-black text-sumi mt-1 tracking-tighter">{{ formatCurrency(total) }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-indigo text-xl">payments</span>
                </div>
            </div>
            
            <button 
                @click="activeSideTab = 'checkout'" 
                class="group relative overflow-hidden w-full h-14 bg-sumi text-washi font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-300 rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-sumi/10 hover:bg-indigo hover:-translate-y-1 active:translate-y-0 active:scale-95"
            >
                <span class="relative z-10 flex items-center gap-3">
                    Finalize Order
                    <span class="material-symbols-outlined text-lg transition-transform duration-300 group-hover:translate-x-1">arrow_forward_ios</span>
                </span>
                <div class="absolute inset-0 h-full w-full bg-white/10 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useStudioStore } from '../../composables/useStudioStore';
import { formatCurrency } from '../../utils/studio-utils';
import LayerPanel from './Panels/LayerPanel.vue';
import ArtworkPanel from './Panels/ArtworkPanel.vue';
import TextPanel from './Panels/TextPanel.vue';
import ActiveElementControls from './Panels/ActiveElementControls.vue';

const { activeSideTab, checkoutForm } = useStudioStore();

defineEmits(['remove-element', 'update-image-outline', 'update-layer', 'save-history', 'add-media', 'add-text']);

const hudTitle = computed(() => {
    if (activeSideTab.value === 'layers') return 'Aksen Warna';
    if (activeSideTab.value === 'artwork') return 'Media Artwork';
    if (activeSideTab.value === 'text') return 'Kustom Teks';
    return 'Studio';
});

const hudSubtitle = computed(() => {
    if (activeSideTab.value === 'layers') return 'Personalize Layers';
    if (activeSideTab.value === 'artwork') return 'Upload Branding';
    if (activeSideTab.value === 'text') return 'Identity Design';
    return 'KONFIGURASI';
});

const total = computed(() => {
    let t = 899000;
    if (checkoutForm.fastTrackEnabled) t += 125000;
    if (checkoutForm.customBoxEnabled) t += 65000;
    return t;
});
</script>

<style scoped>
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
</style>
