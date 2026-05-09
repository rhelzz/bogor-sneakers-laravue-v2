<template>
    <div class="font-montserrat">
        <transition name="modal-fade">
            <div v-if="showFastTrack" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-sumi/40 backdrop-blur-md">
                <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full shadow-[0_30px_60px_rgba(26,26,26,0.1)] text-center relative overflow-hidden group">
                    <!-- Decorative Background -->
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo/5 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-indigo/10 rounded-3xl flex items-center justify-center mb-8 mx-auto ring-1 ring-indigo/5 transition-transform duration-700 group-hover:scale-110 group-hover:rotate-6">
                            <span class="material-symbols-outlined text-indigo text-4xl animate-pulse">bolt</span>
                        </div>
                        
                        <h4 class="text-2xl font-black text-sumi uppercase tracking-tight mb-3">Butuh Cepat?</h4>
                        <p class="text-[11px] text-usuzumi/60 font-bold leading-relaxed mb-10 uppercase tracking-[0.15em] px-4">
                            Gunakan layanan <span class="text-indigo">Fast Track</span> agar pesanan Anda masuk antrean prioritas dan selesai dalam 3-5 hari kerja.
                        </p>
                        
                        <div class="flex flex-col gap-4">
                            <button 
                                @click="$emit('confirmFastTrack', true)" 
                                class="w-full py-5 bg-sumi text-washi rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-300 hover:bg-indigo hover:-translate-y-1 active:translate-y-0 shadow-lg shadow-sumi/10"
                            >
                                YA, SAYA MAU (+{{ formatCurrency(125000) }})
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

        <transition name="modal-fade">
            <div v-if="showCustomBox" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-sumi/40 backdrop-blur-md">
                <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full shadow-[0_30px_60px_rgba(26,26,26,0.1)] text-center relative overflow-hidden group">
                    <!-- Decorative Background -->
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-matcha/5 rounded-full blur-3xl"></div>

                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-matcha/10 rounded-3xl flex items-center justify-center mb-8 mx-auto ring-1 ring-matcha/5 transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-6">
                            <span class="material-symbols-outlined text-matcha text-4xl">inventory_2</span>
                        </div>
                        
                        <h4 class="text-2xl font-black text-sumi uppercase tracking-tight mb-3">Upgrade Box?</h4>
                        <p class="text-[11px] text-usuzumi/60 font-bold leading-relaxed mb-10 uppercase tracking-[0.15em] px-4">
                            Berikan kesan eksklusif dengan <span class="text-matcha">Custom Box Premium</span>. Sangat direkomendasikan jika pesanan ini untuk hadiah.
                        </p>
                        
                        <div class="flex flex-col gap-4">
                            <button 
                                @click="$emit('confirmCustomBox', true)" 
                                class="w-full py-5 bg-sumi text-washi rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-300 hover:bg-matcha hover:-translate-y-1 active:translate-y-0 shadow-lg shadow-sumi/10"
                            >
                                YA, UPGRADE BOX (+{{ formatCurrency(65000) }})
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
    animation: modal-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modal-fade-leave-active {
    animation: modal-out 0.3s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes modal-in {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes modal-out {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.95) translateY(10px); }
}
</style>
