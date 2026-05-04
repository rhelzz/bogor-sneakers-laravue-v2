<template>
    <aside class="w-20 border-r border-gray-100 bg-white flex flex-col z-40">
        <div class="flex-grow flex flex-col items-center py-8 gap-6">
            <button
                @click="activeSideTab = 'layers'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                :class="activeSideTab === 'layers' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
            >
                <span class="material-symbols-outlined text-2xl">layers</span>
                <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Layers</span>
            </button>

            <button
                @click="activeSideTab = 'artwork'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                :class="activeSideTab === 'artwork' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
            >
                <span class="material-symbols-outlined text-2xl">palette</span>
                <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Artwork</span>
            </button>

            <button
                @click="activeSideTab = 'text'"
                class="group relative w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                :class="activeSideTab === 'text' ? 'bg-primary text-black shadow-lg shadow-primary/20' : 'text-secondary hover:bg-gray-50 hover:text-black'"
            >
                <span class="material-symbols-outlined text-2xl">title</span>
                <span class="absolute left-full ml-4 px-2 py-1 bg-black text-white text-[10px] font-black rounded opacity-0 group-hover:opacity-100 pointer-events-none transition-all whitespace-nowrap z-50 uppercase tracking-[0.2em]">Teks</span>
            </button>
        </div>

        <div class="p-4 border-t border-gray-100 flex flex-col items-center gap-4">
            <button @click="$emit('share')" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black rounded-xl hover:bg-gray-50 transition-all">
                <span class="material-symbols-outlined">share</span>
            </button>
            <button
                @click="activeSideTab = 'checkout'"
                class="w-12 h-12 flex items-center justify-center rounded-2xl transition-all"
                :class="activeSideTab === 'checkout' ? 'bg-black text-white shadow-xl shadow-black/20' : 'bg-gray-100 text-secondary hover:bg-gray-200'"
            >
                <span class="material-symbols-outlined text-2xl">shopping_cart</span>
            </button>
        </div>
    </aside>
</template>

<script setup lang="ts">
import { useStudioStore } from '../../composables/useStudioStore';

const { activeSideTab } = useStudioStore();

defineEmits(['share']);
</script>
