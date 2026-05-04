<template>
    <div class="flex-grow min-w-0 flex items-center justify-center relative overflow-hidden h-full">
        <!-- High-tech backdrop effect -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent opacity-60"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary/5 rounded-full blur-[120px]"></div>
            <!-- UI Grid lines -->
            <div class="absolute inset-0 opacity-[0.03] bg-[size:40px_40px] bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)]"></div>
        </div>

        <!-- Konva Container -->
        <div ref="konvaContainerRef" class="relative w-full h-full z-10 cursor-grab active:cursor-grabbing"></div>

        <!-- Floating Delete Button on Canvas -->
        <transition name="fade">
            <button
                v-if="activeElement"
                @click="$emit('removeElement')"
                class="absolute top-6 right-6 z-30 w-12 h-12 bg-red-500 text-white rounded-2xl shadow-2xl flex items-center justify-center hover:bg-black transition-all active:scale-90 group"
                title="Hapus Elemen Terpilih"
            >
                <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform">close</span>
            </button>
        </transition>

        <!-- Floating View Controls -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 p-2 bg-white/80 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl z-20">
            <button @click="zoomOut" class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-gray-50 rounded-xl transition-all">
                <span class="material-symbols-outlined">remove</span>
            </button>
            <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
            <button @click="resetZoom" class="px-3 h-10 flex items-center gap-2 text-[10px] font-black text-secondary hover:text-black transition-all uppercase tracking-[0.2em]">
                <span class="material-symbols-outlined text-lg">center_focus_strong</span>
                Reset View
            </button>
            <div class="h-4 w-[1px] bg-gray-200 mx-1"></div>
            <button @click="zoomIn" class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-gray-50 rounded-xl transition-all">
                <span class="material-symbols-outlined">add</span>
            </button>
        </div>

        <!-- Syncing Indicator -->
        <transition name="fade">
            <div
                v-if="isSyncing || catalogLoading"
                class="absolute inset-0 z-50 flex flex-col items-center justify-center gap-6 bg-white/90 backdrop-blur-md"
            >
                <div class="relative">
                    <div class="h-16 w-16 rounded-full border-[3px] border-gray-100 border-t-primary animate-spin"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl animate-pulse">view_in_ar</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-black">Mempersiapkan Model</p>
                    <p class="text-[10px] text-secondary font-medium mt-1">Mengunduh aset SVG kualitas tinggi...</p>
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
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
