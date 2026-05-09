<template>
    <aside class="w-20 border-r border-gray-200 bg-white/80 backdrop-blur-md flex flex-col z-40 transition-all duration-300">
        <div class="flex-grow flex flex-col items-center py-8 gap-8">
            <!-- Navigation Tabs -->
            <button
                @click="activeSideTab = 'layers'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 active:scale-90 hover:-translate-y-0.5"
                :class="activeSideTab === 'layers' 
                    ? 'bg-sumi text-washi shadow-lg shadow-sumi/20' 
                    : 'text-usuzumi hover:bg-white hover:text-indigo hover:shadow-sm'"
                title="Layers"
            >
                <span class="material-symbols-outlined text-2xl transition-transform duration-300 group-hover:scale-110">layers</span>
                <!-- Custom Tooltip -->
                <span class="absolute left-full ml-4 px-3 py-1.5 bg-sumi text-washi text-[11px] font-montserrat font-bold rounded-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 pointer-events-none transition-all duration-300 whitespace-nowrap z-50 uppercase tracking-[0.2em] shadow-xl">
                    Layers
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-sumi"></div>
                </span>
                <!-- Active Indicator -->
                <div v-if="activeSideTab === 'layers'" class="absolute left-[-14px] w-1 h-8 bg-indigo rounded-r-md shadow-[0_0_10px_rgba(79,70,229,0.3)]"></div>
            </button>

            <button
                @click="activeSideTab = 'artwork'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 active:scale-90 hover:-translate-y-0.5"
                :class="activeSideTab === 'artwork' 
                    ? 'bg-sumi text-washi shadow-lg shadow-sumi/20' 
                    : 'text-usuzumi hover:bg-white hover:text-indigo hover:shadow-sm'"
                title="Artwork"
            >
                <span class="material-symbols-outlined text-2xl transition-transform duration-300 group-hover:scale-110">palette</span>
                <span class="absolute left-full ml-4 px-3 py-1.5 bg-sumi text-washi text-[11px] font-montserrat font-bold rounded-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 pointer-events-none transition-all duration-300 whitespace-nowrap z-50 uppercase tracking-[0.2em] shadow-xl">
                    Artwork
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-sumi"></div>
                </span>
                <div v-if="activeSideTab === 'artwork'" class="absolute left-[-14px] w-1 h-8 bg-indigo rounded-r-md shadow-[0_0_10px_rgba(79,70,229,0.3)]"></div>
            </button>

            <button
                @click="activeSideTab = 'text'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 active:scale-90 hover:-translate-y-0.5"
                :class="activeSideTab === 'text' 
                    ? 'bg-sumi text-washi shadow-lg shadow-sumi/20' 
                    : 'text-usuzumi hover:bg-white hover:text-indigo hover:shadow-sm'"
                title="Text"
            >
                <span class="material-symbols-outlined text-2xl transition-transform duration-300 group-hover:scale-110">title</span>
                <span class="absolute left-full ml-4 px-3 py-1.5 bg-sumi text-washi text-[11px] font-montserrat font-bold rounded-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 pointer-events-none transition-all duration-300 whitespace-nowrap z-50 uppercase tracking-[0.2em] shadow-xl">
                    Teks
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-sumi"></div>
                </span>
                <div v-if="activeSideTab === 'text'" class="absolute left-[-14px] w-1 h-8 bg-indigo rounded-r-md shadow-[0_0_10px_rgba(79,70,229,0.3)]"></div>
            </button>
        </div>

        <div class="p-4 border-t border-gray-200 flex flex-col items-center gap-6">
            <button 
                @click="$emit('share')" 
                class="group relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo rounded-xl hover:bg-white hover:shadow-sm transition-all duration-300 active:scale-90"
                title="Share Design"
            >
                <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:rotate-12">share</span>
            </button>
            
            <button
                @click="activeSideTab = 'checkout'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all duration-300 active:scale-90 hover:-translate-y-0.5"
                :class="activeSideTab === 'checkout' 
                    ? 'bg-indigo text-washi shadow-xl shadow-indigo/20' 
                    : 'bg-gray-100 text-usuzumi hover:bg-white hover:text-indigo hover:shadow-sm'"
                title="Checkout"
            >
                <span class="material-symbols-outlined text-2xl transition-transform duration-300 group-hover:scale-110">shopping_cart</span>
                <span class="absolute left-full ml-4 px-3 py-1.5 bg-indigo text-washi text-[10px] font-montserrat font-bold rounded-lg opacity-0 translate-x-[-10px] group-hover:opacity-100 group-hover:translate-x-0 pointer-events-none transition-all duration-300 whitespace-nowrap z-50 uppercase tracking-[0.2em] shadow-xl">
                    Cart
                    <div class="absolute right-full top-1/2 -translate-y-1/2 border-8 border-transparent border-r-indigo"></div>
                </span>
            </button>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { useStudioStore } from '../../composables/useStudioStore';

const { activeSideTab } = useStudioStore();

defineEmits(['share']);
</script>
