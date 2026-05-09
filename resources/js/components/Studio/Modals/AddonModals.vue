<template>
    <div class="font-montserrat">
        <!-- Fast Track Modal -->
        <transition name="modal-fade">
            <div v-if="showFastTrack" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-sumi/60 backdrop-blur-md">
                <div class="bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-[0_40px_80px_rgba(26,26,26,0.2)] text-center relative overflow-hidden group border border-white/20">
                    <!-- Step Indicator -->
                    <div class="absolute top-8 left-10 flex items-center gap-2">
                        <div class="flex gap-1">
                            <div class="w-4 h-1 rounded-full bg-indigo transition-all duration-500"></div>
                            <div class="w-2 h-1 rounded-full bg-indigo/10 transition-all duration-500"></div>
                        </div>
                        <span class="text-[8px] font-black text-indigo uppercase tracking-[0.2em]">Offer 01</span>
                    </div>

                    <!-- Decorative Background -->
                    <div class="absolute -top-16 -right-16 w-48 h-48 bg-indigo/10 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-125"></div>
                    
                    <div class="relative z-10 pt-4">
                        <div class="w-24 h-24 bg-indigo/10 rounded-[2.5rem] flex items-center justify-center mb-8 mx-auto ring-1 ring-indigo/5 transition-all duration-700 group-hover:scale-110 group-hover:rotate-6 group-hover:shadow-2xl group-hover:shadow-indigo/20">
                            <span class="material-symbols-outlined text-indigo text-5xl animate-pulse">bolt</span>
                        </div>
                        
                        <h4 class="text-2xl font-black text-sumi uppercase tracking-tight mb-3">Butuh Cepat?</h4>
                        <p class="text-[11px] text-usuzumi/70 font-medium leading-relaxed mb-10 uppercase tracking-[0.1em] px-4">
                            Gunakan layanan <span class="text-indigo font-black underline decoration-indigo/20 underline-offset-4">Fast Track</span> agar pesanan Anda masuk antrean prioritas dan selesai dalam 3-5 hari kerja.
                        </p>
                        
                        <div class="flex flex-col gap-4">
                            <button 
                                @click="$emit('confirmFastTrack', true)" 
                                class="group/btn relative overflow-hidden w-full py-5 bg-sumi text-washi rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 hover:bg-indigo hover:-translate-y-1 active:translate-y-0 shadow-xl shadow-sumi/20"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:animate-shine duration-1000"></div>
                                <span class="relative z-10">YA, SAYA MAU (+{{ formatCurrency(125000) }})</span>
                            </button>
                            <button 
                                @click="$emit('confirmFastTrack', false)" 
                                class="w-full py-3 text-usuzumi/40 font-black text-[10px] uppercase tracking-[0.2em] hover:text-sumi transition-colors"
                            >
                                TIDAK, TERIMA KASIH
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Custom Box Modal -->
        <transition name="modal-fade">
            <div v-if="showCustomBox" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-sumi/60 backdrop-blur-md">
                <div class="bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-[0_40px_80px_rgba(26,26,26,0.2)] text-center relative overflow-hidden group border border-white/20">
                    <!-- Step Indicator -->
                    <div class="absolute top-8 left-10 flex items-center gap-2">
                        <div class="flex gap-1">
                            <div class="w-2 h-1 rounded-full bg-matcha/10 transition-all duration-500"></div>
                            <div class="w-4 h-1 rounded-full bg-matcha transition-all duration-500"></div>
                        </div>
                        <span class="text-[8px] font-black text-matcha uppercase tracking-[0.2em]">Offer 02</span>
                    </div>

                    <!-- Decorative Background -->
                    <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-matcha/10 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-125"></div>

                    <div class="relative z-10 pt-4">
                        <div class="w-24 h-24 bg-matcha/10 rounded-[2.5rem] flex items-center justify-center mb-8 mx-auto ring-1 ring-matcha/5 transition-all duration-700 group-hover:scale-110 group-hover:-rotate-6 group-hover:shadow-2xl group-hover:shadow-matcha/20">
                            <span class="material-symbols-outlined text-matcha text-5xl">inventory_2</span>
                        </div>
                        
                        <h4 class="text-2xl font-black text-sumi uppercase tracking-tight mb-3">Upgrade Box?</h4>
                        <p class="text-[11px] text-usuzumi/70 font-medium leading-relaxed mb-10 uppercase tracking-[0.1em] px-4">
                            Berikan kesan eksklusif dengan <span class="text-matcha font-black underline decoration-matcha/20 underline-offset-4">Custom Box Premium</span>. Sangat direkomendasikan jika pesanan ini untuk hadiah.
                        </p>
                        
                        <div class="flex flex-col gap-4">
                            <button 
                                @click="$emit('confirmCustomBox', true)" 
                                class="group/btn relative overflow-hidden w-full py-5 bg-sumi text-washi rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 hover:bg-matcha hover:-translate-y-1 active:translate-y-0 shadow-xl shadow-sumi/20"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:animate-shine duration-1000"></div>
                                <span class="relative z-10">YA, UPGRADE BOX (+{{ formatCurrency(65000) }})</span>
                            </button>
                            <button 
                                @click="$emit('confirmCustomBox', false)" 
                                class="w-full py-3 text-usuzumi/40 font-black text-[10px] uppercase tracking-[0.2em] hover:text-sumi transition-colors"
                            >
                                TIDAK, BOX STANDAR
                            </button>
                        </div>
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
.modal-fade-enter-active {
    animation: modal-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modal-fade-leave-active {
    animation: modal-out 0.4s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes modal-in {
    from { opacity: 0; transform: scale(0.85) translateY(30px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes modal-out {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.95) translateY(10px); }
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
