<template>
    <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-8 h-20 bg-white/90 backdrop-blur-xl border-b border-gray-100 shadow-sm">
        <div class="flex items-center gap-10">
            <!-- Brand -->
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-10 h-10 bg-black rounded-xl flex items-center justify-center transition-transform group-hover:rotate-12">
                    <img src="/logo-b-bogor.svg" class="w-6 h-6 invert" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-black tracking-widest text-black uppercase leading-none">Studio</span>
                    <span class="text-[10px] font-bold text-primary tracking-[0.3em] uppercase leading-none mt-1">Customizer</span>
                </div>
            </div>

            <div class="h-8 w-[1px] bg-gray-100"></div>

            <!-- Selection Group -->
            <div class="flex items-center gap-8">
                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-black text-secondary uppercase tracking-widest whitespace-nowrap opacity-60">Model :</span>
                     <div class="relative flex items-center group">
                        <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-black transition-colors pointer-events-none">style</span>
                        <select
                            v-model="activeFolderKey"
                            class="h-10 pl-9 pr-10 min-w-[160px] rounded-xl border-gray-200 bg-gray-50/50 text-[11px] font-black text-black focus:border-black focus:ring-0 transition-all appearance-none cursor-pointer hover:bg-white hover:border-gray-300"
                            :disabled="catalogLoading"
                        >
                            <option v-for="folder in catalogFolders" :key="folder.key" :value="folder.key">
                                {{ folder.label }}
                            </option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 text-lg text-gray-400 pointer-events-none">unfold_more</span>
                     </div>
                 </div>

                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-black text-secondary uppercase tracking-widest whitespace-nowrap opacity-60">Variant :</span>
                     <div class="relative flex items-center group">
                        <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-black transition-colors pointer-events-none">auto_awesome</span>
                        <select
                            v-model="currentModel"
                            class="h-10 pl-9 pr-10 min-w-[120px] rounded-xl border-gray-200 bg-gray-50/50 text-[11px] font-black text-black focus:border-black focus:ring-0 transition-all appearance-none cursor-pointer hover:bg-white hover:border-gray-300"
                            :disabled="catalogLoading"
                        >
                            <option v-for="model in availableModels" :key="model.id" :value="model.id">
                                {{ model.label }}
                            </option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 text-lg text-gray-400 pointer-events-none">unfold_more</span>
                     </div>
                 </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <!-- Action Tools -->
            <div class="flex items-center bg-gray-50 rounded-2xl p-1.5 border border-gray-100">
                <button @click="$emit('undo')" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90" title="Undo (Ctrl+Z)">
                    <span class="material-symbols-outlined text-xl">undo</span>
                </button>
                <button @click="$emit('redo')" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90" title="Redo (Ctrl+Y)">
                    <span class="material-symbols-outlined text-xl">redo</span>
                </button>
                <div class="w-[1px] h-6 bg-gray-200 mx-1"></div>
                <button @click="$emit('reset')" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-error hover:bg-white hover:shadow-sm rounded-xl transition-all active:scale-90" title="Reset Design">
                    <span class="material-symbols-outlined text-xl text-error/70">restart_alt</span>
                </button>
            </div>

            <button @click="$emit('checkout')" class="h-12 px-8 bg-black text-white font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all rounded-2xl shadow-xl shadow-black/10 active:scale-95 flex items-center gap-3">
                {{ isSaving ? 'PROCESSING...' : 'PROCESS ORDER' }}
                <span class="material-symbols-outlined text-lg" v-if="!isSaving">shopping_cart_checkout</span>
            </button>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { useStudioStore } from '../../composables/useStudioStore';

const { 
    catalogFolders, 
    catalogLoading, 
    activeFolderKey, 
    currentModel, 
    availableModels,
    isSaving 
} = useStudioStore();

defineEmits(['undo', 'redo', 'reset', 'checkout']);
</script>
