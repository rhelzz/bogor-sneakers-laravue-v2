<template>
    <div class="flex-grow min-w-0 flex items-center justify-center relative overflow-hidden h-full bg-[#F5F5F7] font-montserrat">
        <!-- Premium Backdrop -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Subtle Radial Gradient -->
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white/80 via-transparent to-transparent opacity-100"></div>
            
            <!-- Soft Brand Glows -->
            <div class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-indigo/[0.03] rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[600px] h-[600px] bg-matcha/[0.02] rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
            
            <!-- Professional Studio Grid -->
            <div class="absolute inset-0 opacity-[0.04] bg-[size:32px_32px] bg-[linear-gradient(to_right,#1a1a1a_1px,transparent_1px),linear-gradient(to_bottom,#1a1a1a_1px,transparent_1px)]"></div>
            
            <!-- Decorative Corners -->
            <div class="absolute top-12 left-12 w-20 h-20 border-t-2 border-l-2 border-sumi/[0.05] rounded-tl-3xl"></div>
            <div class="absolute bottom-12 right-12 w-20 h-20 border-b-2 border-r-2 border-sumi/[0.05] rounded-br-3xl"></div>
        </div>

        <!-- Konva Container -->
        <div ref="konvaContainerRef" class="relative w-full h-full z-10 cursor-grab active:cursor-grabbing"></div>

        <!-- Floating Action Overlay -->
        <div class="absolute inset-x-0 bottom-0 pointer-events-none p-10 flex flex-col items-center gap-6 z-20">
            <!-- Delete Selection Button -->
            <transition name="pop">
                <button
                    v-if="activeElement"
                    @click="$emit('removeElement')"
                    class="pointer-events-auto flex items-center gap-3 px-6 h-12 bg-white text-sumi rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.08)] border border-indigo/5 transition-all duration-300 hover:bg-red-500 hover:text-white hover:-translate-y-1 active:scale-95 group"
                >
                    <span class="material-symbols-outlined text-xl transition-transform duration-500 group-hover:rotate-90">delete</span>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Hapus Elemen</span>
                </button>
            </transition>

            <!-- View Controls -->
            <div class="pointer-events-auto flex items-center bg-white/90 backdrop-blur-2xl rounded-2xl border border-indigo/5 shadow-[0_20px_50px_rgba(0,0,0,0.06)] p-1.5 transition-all duration-500 hover:shadow-[0_30px_60px_rgba(0,0,0,0.1)]">
                <button 
                    @click="zoomOut" 
                    class="w-11 h-11 flex items-center justify-center text-usuzumi/60 hover:text-indigo hover:bg-indigo/5 rounded-xl transition-all duration-300 active:scale-90"
                    title="Zoom Out"
                >
                    <span class="material-symbols-outlined">remove</span>
                </button>
                
                <div class="h-6 w-px bg-indigo/5 mx-2"></div>
                
                <button 
                    @click="resetZoom" 
                    class="px-5 h-11 flex items-center gap-3 text-[11px] font-black text-usuzumi/60 hover:text-indigo hover:bg-indigo/5 transition-all duration-300 rounded-xl group"
                >
                    <span class="material-symbols-outlined text-lg transition-transform duration-500 group-hover:rotate-180">center_focus_strong</span>
                    <span class="uppercase tracking-[0.2em]">Reset View</span>
                </button>
                
                <div class="h-6 w-px bg-indigo/5 mx-2"></div>
                
                <button 
                    @click="zoomIn" 
                    class="w-11 h-11 flex items-center justify-center text-usuzumi/60 hover:text-indigo hover:bg-indigo/5 rounded-xl transition-all duration-300 active:scale-90"
                    title="Zoom In"
                >
                    <span class="material-symbols-outlined">add</span>
                </button>
            </div>
        </div>

        <!-- Canvas Loading Overlay -->
        <transition name="fade">
            <div
                v-if="isSyncing || catalogLoading"
                class="absolute inset-0 z-50 flex flex-col items-center justify-center gap-8 bg-white/95 backdrop-blur-xl"
            >
                <div class="relative group">
                    <!-- Rotating Ring -->
                    <div class="h-20 w-20 rounded-full border-[3px] border-indigo/5 border-t-indigo animate-spin"></div>
                    
                    <!-- Pulsing Core -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-10 h-10 bg-indigo/10 rounded-2xl flex items-center justify-center transition-transform duration-700 group-hover:scale-110">
                            <span class="material-symbols-outlined text-indigo text-xl animate-pulse">view_in_ar</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-center space-y-2">
                    <h4 class="text-[12px] font-black uppercase tracking-[0.3em] text-sumi">Mempersiapkan Model</h4>
                    <p class="text-[11px] text-usuzumi/40 font-bold uppercase tracking-widest animate-pulse">Sinkronisasi Aset Studio...</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useStudioStore } from '../../composables/useStudioStore';

const props = defineProps<{
    isSyncing: boolean;
}>();

const { activeElement, catalogLoading } = useStudioStore();
const konvaContainerRef = ref<HTMLDivElement | null>(null);

const emit = defineEmits(['init', 'zoomIn', 'zoomOut', 'resetZoom', 'removeElement']);

onMounted(() => {
    if (konvaContainerRef.value) {
        emit('init', konvaContainerRef.value);
    }
});

const zoomIn = () => emit('zoomIn');
const zoomOut = () => emit('zoomOut');
const resetZoom = () => emit('resetZoom');
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

.pop-enter-active {
    animation: pop-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.pop-leave-active {
    animation: pop-out 0.3s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes pop-in {
    from { opacity: 0; transform: scale(0.8) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes pop-out {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.9) translateY(10px); }
}
</style>
