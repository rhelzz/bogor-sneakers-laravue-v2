<template>
    <nav class="fixed top-0 w-full z-50 flex justify-between items-center px-8 h-20 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm transition-all duration-300">
        <div class="flex items-center gap-10">
            <!-- Brand -->
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-10 h-10 bg-sumi rounded-xl flex items-center justify-center transition-all duration-500 group-hover:rotate-[15deg] group-hover:scale-110 group-hover:shadow-lg">
                    <img src="/logo-b-bogor.svg" class="w-6 h-6 invert transition-transform duration-500 group-hover:scale-90" alt="Logo">
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-montserrat font-black tracking-widest text-sumi uppercase leading-none transition-colors duration-300 group-hover:text-indigo">Studio</span>
                    <span class="text-[10px] font-montserrat font-bold text-matcha tracking-[0.3em] uppercase leading-none mt-1">Customizer</span>
                </div>
            </div>

            <div class="h-8 w-[1px] bg-gray-200"></div>

            <!-- Selection Group -->
            <div class="flex items-center gap-6 font-montserrat">
                 <!-- Model Dropdown -->
                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Model :</span>
                     <div class="relative" ref="modelDropdownRef">
                        <button 
                            @click="isModelOpen = !isModelOpen; isVariantOpen = false"
                            :disabled="catalogLoading"
                            class="h-10 pl-9 pr-10 min-w-[200px] rounded-xl border border-gray-200 bg-gray-50/80 text-[12px] font-bold text-sumi transition-all duration-300 flex items-center justify-between hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group"
                        >
                            <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">style</span>
                            <span class="truncate">{{ activeFolderLabel || 'Pilih Model' }}</span>
                            <span class="material-symbols-outlined absolute right-3 text-lg text-gray-400 group-hover:text-indigo transition-transform duration-300" :class="{ 'rotate-180': isModelOpen }">expand_more</span>
                        </button>
                        
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                        >
                            <div v-if="isModelOpen" class="absolute top-full left-0 mt-2 w-full bg-white rounded-2xl border border-gray-100 shadow-2xl py-2 z-[60] overflow-hidden backdrop-blur-xl">
                                <button 
                                    v-for="folder in catalogFolders" 
                                    :key="folder.key"
                                    @click="selectFolder(folder.key)"
                                    class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item"
                                    :class="activeFolderKey === folder.key ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                >
                                    <span>{{ folder.label }}</span>
                                    <span v-if="activeFolderKey === folder.key" class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                            </div>
                        </transition>
                     </div>
                 </div>

                 <!-- Variant Dropdown -->
                 <div class="flex items-center gap-3">
                     <span class="text-[10px] font-bold text-usuzumi uppercase tracking-widest whitespace-nowrap">Variant :</span>
                     <div class="relative" ref="variantDropdownRef">
                        <button 
                            @click="isVariantOpen = !isVariantOpen; isModelOpen = false"
                            :disabled="catalogLoading"
                            class="h-10 pl-9 pr-10 min-w-[180px] rounded-xl border border-gray-200 bg-gray-50/80 text-[12px] font-bold text-sumi transition-all duration-300 flex items-center justify-between hover:bg-white hover:border-indigo hover:shadow-md active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed group"
                        >
                            <span class="material-symbols-outlined absolute left-3 text-base text-gray-400 group-hover:text-indigo transition-colors duration-300">auto_awesome</span>
                            <span class="truncate">{{ activeVariantLabel || 'Pilih Varian' }}</span>
                            <span class="material-symbols-outlined absolute right-3 text-lg text-gray-400 group-hover:text-indigo transition-transform duration-300" :class="{ 'rotate-180': isVariantOpen }">expand_more</span>
                        </button>
                        
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
                            enter-to-class="transform scale-100 opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="transform scale-100 opacity-100 translate-y-0"
                            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
                        >
                            <div v-if="isVariantOpen" class="absolute top-full left-0 mt-2 w-full bg-white rounded-2xl border border-gray-100 shadow-2xl py-2 z-[60] overflow-hidden backdrop-blur-xl">
                                <button 
                                    v-for="model in availableModels" 
                                    :key="model.id"
                                    @click="selectVariant(model.id)"
                                    class="w-full px-4 py-2.5 text-left text-[11px] font-bold uppercase tracking-wider transition-all duration-200 flex items-center justify-between group/item"
                                    :class="currentModel === model.id ? 'bg-indigo/5 text-indigo' : 'text-usuzumi hover:bg-gray-50 hover:text-sumi'"
                                >
                                    <span>{{ model.label }}</span>
                                    <span v-if="currentModel === model.id" class="material-symbols-outlined text-sm">check_circle</span>
                                </button>
                            </div>
                        </transition>
                     </div>
                 </div>
            </div>
        </div>

        <div class="flex items-center gap-4 font-montserrat">
            <!-- Action Tools -->
            <div class="flex items-center bg-gray-50/80 rounded-2xl p-1.5 border border-gray-200 shadow-sm">
                <button @click="$emit('undo')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Undo (Ctrl+Z)">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:-rotate-12">undo</span>
                </button>
                <button @click="$emit('redo')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Redo (Ctrl+Y)">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:rotate-12">redo</span>
                </button>
                <div class="w-[1px] h-6 bg-gray-300 mx-1"></div>
                <button @click="$emit('reset')" class="relative w-10 h-10 flex items-center justify-center text-usuzumi hover:text-red-500 hover:bg-white rounded-xl transition-all duration-300 hover:shadow-sm hover:-translate-y-0.5 active:translate-y-0 active:scale-95 group" title="Reset Design">
                    <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover:rotate-180">restart_alt</span>
                </button>
            </div>

            <button @click="$emit('checkout')" class="group relative overflow-hidden h-12 px-8 bg-sumi text-washi font-bold text-[11px] uppercase tracking-[0.2em] transition-all duration-300 rounded-2xl shadow-md hover:shadow-xl hover:bg-indigo hover:-translate-y-1 active:translate-y-0 active:scale-95 flex items-center gap-3 border border-transparent hover:border-indigo/50">
                <span class="relative z-10 flex items-center gap-3">
                    {{ isSaving ? 'PROCESSING...' : 'PROCESS ORDER' }}
                    <span class="material-symbols-outlined text-lg transition-transform duration-300 group-hover:translate-x-1" v-if="!isSaving">shopping_cart_checkout</span>
                </span>
                <div class="absolute inset-0 h-full w-full bg-white/10 scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"></div>
            </button>
        </div>
    </nav>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
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

// Dropdown State
const isModelOpen = ref(false);
const isVariantOpen = ref(false);
const modelDropdownRef = ref<HTMLElement | null>(null);
const variantDropdownRef = ref<HTMLElement | null>(null);

const activeFolderLabel = computed(() => {
    return catalogFolders.value.find(f => f.key === activeFolderKey.value)?.label;
});

const activeVariantLabel = computed(() => {
    return availableModels.value.find(m => m.id === currentModel.value)?.label;
});

const selectFolder = (key: string) => {
    activeFolderKey.value = key;
    isModelOpen.value = false;
};

const selectVariant = (id: string) => {
    currentModel.value = id;
    isVariantOpen.value = false;
};

// Click Outside Handler
const handleClickOutside = (event: MouseEvent) => {
    if (modelDropdownRef.value && !modelDropdownRef.value.contains(event.target as Node)) {
        isModelOpen.value = false;
    }
    if (variantDropdownRef.value && !variantDropdownRef.value.contains(event.target as Node)) {
        isVariantOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
