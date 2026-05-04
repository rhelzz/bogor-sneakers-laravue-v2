<template>
    <div class="w-[320px] md:w-[340px] flex-shrink-0 bg-white border-l border-gray-100 flex flex-col h-full shadow-[-20px_0_50px_rgba(0,0,0,0.03)]">
        <div class="p-6 border-b border-gray-50">
            <h3 class="text-sm font-black text-black uppercase tracking-widest flex items-center gap-3">
                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                {{ hudTitle }}
            </h3>
            <p class="text-[10px] text-secondary font-bold mt-1 tracking-widest uppercase opacity-60">{{ hudSubtitle }}</p>

            <ActiveElementControls 
                @remove="$emit('removeElement')"
                @updateImageOutline="$emit('updateImageOutline')"
            />
        </div>

        <div class="flex-grow overflow-y-auto custom-scrollbar p-6">
            <LayerPanel 
                v-if="activeSideTab === 'layers'" 
                @updateLayer="(id) => $emit('updateLayer', id)"
                @saveHistory="$emit('saveHistory')"
            />
            <ArtworkPanel 
                v-if="activeSideTab === 'artwork'" 
                @addMedia="(id) => $emit('addMedia', id)"
            />
            <TextPanel 
                v-if="activeSideTab === 'text'" 
                @addText="$emit('addText')"
            />
        </div>

        <div class="p-8 border-t border-gray-50 bg-white">
            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col">
                    <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] opacity-60">Estimated Total</span>
                    <span class="text-2xl font-black text-black mt-1 tracking-tighter">{{ formatCurrency(total) }}</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">payments</span>
                </div>
            </div>
            <button @click="activeSideTab = 'checkout'" class="w-full h-14 bg-black text-white font-black text-[11px] uppercase tracking-widest hover:bg-primary hover:text-black transition-all rounded-2xl flex items-center justify-center gap-3 shadow-xl shadow-black/10 active:scale-95">
                Finalize Order
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
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

defineEmits(['removeElement', 'updateImageOutline', 'updateLayer', 'saveHistory', 'addMedia', 'addText']);

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
