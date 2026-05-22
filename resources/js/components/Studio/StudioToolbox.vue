<template>
    <div class="w-[320px] md:w-[350px] flex-shrink-0 bg-white border-l border-gray-200 flex flex-col h-full shadow-[-10px_0_30px_rgba(0,0,0,0.02)] font-montserrat relative">
        <!-- Panel Collapse Button (desktop only) — sticks out to the left of the panel -->
        <button
            @click="$emit('toggle-panel')"
            class="hidden md:flex absolute -left-8 top-1/2 -translate-y-1/2 z-10 w-8 h-16 flex-col items-center justify-center bg-white border border-r-0 border-gray-200 rounded-l-2xl shadow-sm hover:bg-indigo hover:border-indigo hover:text-white transition-all duration-200 group"
            title="Sembunyikan panel"
        >
            <span class="material-symbols-outlined text-sm text-gray-400 group-hover:text-white transition-colors">chevron_right</span>
        </button>

        <!-- Header Panel -->
        <div class="p-5 md:p-6 pb-4">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <h3 class="text-sm font-black text-sumi uppercase tracking-[0.2em] leading-tight">
                        {{ hudTitle }}
                    </h3>
                    <p class="text-[11px] text-usuzumi font-bold mt-1.5 tracking-[0.1em] uppercase opacity-60">{{ hudSubtitle }}</p>
                </div>
                <!-- Mobile close button (inside header) -->
                <button
                    @click="$emit('toggle-panel')"
                    class="md:hidden flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-400 transition-colors ml-3"
                >
                    <span class="material-symbols-outlined text-base">close</span>
                </button>
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
        <div class="p-6 md:p-8 border-t border-indigo/5 bg-white/90 backdrop-blur-xl relative">
            <div class="flex flex-col gap-6 relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-indigo animate-pulse"></div>
                            <span class="text-[10px] font-black text-usuzumi/40 uppercase tracking-[0.2em]">Estimasi Total</span>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-2xl font-black text-sumi tracking-tight">
                                {{ formatCurrency(total) }}
                            </span>
                            <span class="text-[9px] font-bold text-usuzumi/30 uppercase tracking-widest">IDR</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 bg-indigo/[0.03] px-3 py-2 rounded-xl border border-indigo/5">
                        <div class="w-6 h-6 rounded-full bg-white shadow-sm flex items-center justify-center">
                            <span class="material-symbols-outlined text-indigo text-[12px]">verified</span>
                        </div>
                        <span class="text-[8px] font-black text-indigo/60 uppercase tracking-[0.1em] leading-none">Premium<br>Quality</span>
                    </div>
                </div>
                
                <button 
                    @click="activeSideTab = 'checkout'" 
                    class="group relative overflow-hidden w-full h-14 bg-sumi text-washi font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 rounded-2xl flex items-center justify-center shadow-xl shadow-sumi/10 hover:shadow-indigo/20 hover:-translate-y-1 active:translate-y-0 active:scale-95"
                >
                    <div class="absolute inset-0 bg-indigo opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Animated Background Shine -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shine duration-1000"></div>

                    <span class="relative z-10 flex items-center gap-4">
                        Finalize Order
                        <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center transition-all duration-500 group-hover:bg-white group-hover:rotate-[360deg]">
                            <span class="material-symbols-outlined text-[14px] transition-colors duration-500 group-hover:text-indigo">arrow_forward</span>
                        </div>
                    </span>
                </button>
            </div>
            
            <p class="mt-4 text-[8px] text-center text-usuzumi/30 font-bold uppercase tracking-[0.15em]">Secure Payment Powered by Midtrans</p>
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

defineEmits(['remove-element', 'update-image-outline', 'update-layer', 'save-history', 'add-media', 'add-text', 'toggle-panel']);

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

@keyframes shine {
    100% {
        transform: translateX(100%);
    }
}
.animate-shine {
    animation: shine 1.5s infinite;
}
</style>
