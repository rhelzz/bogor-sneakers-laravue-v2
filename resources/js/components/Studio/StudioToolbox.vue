<template>
    <div class="w-[300px] md:w-[320px] flex-shrink-0 bg-white border-l border-gray-200 flex flex-col h-full shadow-[-10px_0_30px_rgba(0,0,0,0.02)] font-montserrat relative">
        <!-- Panel Collapse Button (desktop only) — sticks out to the left of the panel -->
        <button
            @click="$emit('collapse-desktop')"
            class="hidden md:flex absolute -left-7 top-1/2 -translate-y-1/2 z-10 w-7 h-14 flex-col items-center justify-center bg-white border border-r-0 border-gray-200 rounded-l-xl shadow-sm hover:bg-indigo hover:border-indigo hover:text-white transition-all duration-200 group"
            title="Sembunyikan panel"
        >
            <span class="material-symbols-outlined text-[10px] text-gray-400 group-hover:text-white transition-colors">chevron_right</span>
        </button>

        <!-- Header Panel -->
        <div class="p-4 md:p-5 pb-3">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <h3 class="text-xs font-black text-sumi uppercase tracking-[0.2em] leading-tight">
                        {{ hudTitle }}
                    </h3>
                    <p class="text-[10px] text-usuzumi font-bold mt-1 tracking-[0.1em] uppercase opacity-60">{{ hudSubtitle }}</p>
                </div>
                <!-- Mobile close button (inside header) -->
                <button
                    @click="$emit('close-mobile')"
                    class="md:hidden flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-400 transition-colors ml-3"
                >
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>

            <ActiveElementControls
                @remove="$emit('remove-element')"
                @updateImageOutline="$emit('update-image-outline')"
            />
        </div>

        <!-- Mobile Tab Navigation (replaces left sidebar on mobile) -->
        <div class="md:hidden flex border-b border-gray-100 bg-gray-50/50 px-2">
            <button
                v-for="tab in [
                    { id: 'layers', icon: 'layers', label: 'Layer' },
                    { id: 'artwork', icon: 'palette', label: 'Artwork' },
                    { id: 'text', icon: 'title', label: 'Teks' }
                ]"
                :key="tab.id"
                @click="activeSideTab = tab.id"
                class="flex-1 flex flex-col items-center gap-0.5 py-2 text-[8px] font-black uppercase tracking-wider transition-all duration-200 border-b-2"
                :class="activeSideTab === tab.id
                    ? 'text-indigo border-indigo -mb-px'
                    : 'text-usuzumi/40 border-transparent hover:text-usuzumi/70'"
            >
                <span class="material-symbols-outlined text-base">{{ tab.icon }}</span>
                <span>{{ tab.label }}</span>
            </button>
        </div>

        <!-- Content Panel -->
        <div class="flex-grow overflow-y-auto custom-scrollbar px-5 py-1">
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

        <!-- Bottom Panel (Action) -->
        <div class="p-5 md:p-6 border-t border-indigo/5 bg-white/90 backdrop-blur-xl relative">
            <div class="flex flex-col gap-4 relative z-10">
                <button 
                    @click="activeSideTab = 'checkout'" 
                    class="group relative overflow-hidden w-full h-12 bg-sumi text-washi font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-500 rounded-xl flex items-center justify-center shadow-xl shadow-sumi/10 hover:shadow-indigo/20 hover:-translate-y-1 active:translate-y-0 active:scale-95"
                >
                    <div class="absolute inset-0 bg-indigo opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <!-- Animated Background Shine -->
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shine duration-1000"></div>

                    <span class="relative z-10 flex items-center gap-3">
                        Finalize Order
                        <div class="w-6 h-6 rounded-full bg-white/10 flex items-center justify-center transition-all duration-500 group-hover:bg-white group-hover:rotate-[360deg]">
                            <span class="material-symbols-outlined text-[12px] transition-colors duration-500 group-hover:text-indigo">arrow_forward</span>
                        </div>
                    </span>
                </button>
            </div>
            
            <p class="mt-3 text-[7px] text-center text-usuzumi/30 font-bold uppercase tracking-[0.15em]">Secure Payment Powered by Midtrans</p>
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

defineEmits(['remove-element', 'update-image-outline', 'update-layer', 'save-history', 'add-media', 'add-text', 'collapse-desktop', 'close-mobile']);

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
