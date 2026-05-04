<template>
    <div>
        <transition name="fade">
            <div v-if="showFastTrack" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-xl p-6">
                <div class="bg-white rounded-[40px] p-10 max-w-sm w-full shadow-2xl text-center transform transition-all scale-100 animate-popup">
                    <div class="w-20 h-20 bg-primary rounded-[28px] flex items-center justify-center mb-8 mx-auto shadow-xl shadow-primary/10">
                        <span class="material-symbols-outlined text-black text-4xl">bolt</span>
                    </div>
                    <h4 class="text-2xl font-black text-black uppercase tracking-tight mb-3">Butuh Cepat?</h4>
                    <p class="text-xs text-secondary font-bold leading-relaxed mb-10 opacity-60 uppercase tracking-widest">Gunakan layanan Fast Track agar pesanan Anda masuk antrean prioritas dan selesai dalam 3-5 hari kerja.</p>
                    <div class="flex flex-col gap-3">
                        <button @click="$emit('confirmFastTrack', true)" class="w-full py-5 bg-black text-white rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all shadow-xl shadow-black/10">YA, SAYA MAU (+{{ formatCurrency(125000) }})</button>
                        <button @click="$emit('confirmFastTrack', false)" class="w-full py-4 text-secondary font-black text-[10px] uppercase tracking-[0.2em] hover:text-black transition-all">TIDAK, TERIMA KASIH</button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div v-if="showCustomBox" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-xl p-6">
                <div class="bg-white rounded-[40px] p-10 max-w-sm w-full shadow-2xl text-center transform transition-all scale-100 animate-popup">
                    <div class="w-20 h-20 bg-primary rounded-[28px] flex items-center justify-center mb-8 mx-auto shadow-xl shadow-primary/10">
                        <span class="material-symbols-outlined text-black text-4xl">inventory_2</span>
                    </div>
                    <h4 class="text-2xl font-black text-black uppercase tracking-tight mb-3">Upgrade Box?</h4>
                    <p class="text-xs text-secondary font-bold leading-relaxed mb-10 opacity-60 uppercase tracking-widest">Berikan kesan eksklusif dengan Custom Box Premium. Sangat direkomendasikan jika pesanan ini untuk hadiah.</p>
                    <div class="flex flex-col gap-3">
                        <button @click="$emit('confirmCustomBox', true)" class="w-full py-5 bg-black text-white rounded-[20px] font-black text-[11px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all shadow-xl shadow-black/10">YA, UPGRADE BOX (+{{ formatCurrency(65000) }})</button>
                        <button @click="$emit('confirmCustomBox', false)" class="w-full py-4 text-secondary font-black text-[10px] uppercase tracking-[0.2em] hover:text-black transition-all">TIDAK, BOX STANDAR</button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup lang="ts">
import { formatCurrency } from '../../../utils/studio-utils';

defineProps<{
    showFastTrack: boolean;
    showCustomBox: boolean;
}>();

defineEmits(['confirmFastTrack', 'confirmCustomBox']);
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
.animate-popup {
    animation: popup 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes popup {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
</style>
