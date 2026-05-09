<template>
    <div class="flex flex-col h-full bg-white font-montserrat">
        <!-- Header -->
        <div class="p-6 border-b border-indigo/5 flex justify-between items-center bg-white/80 backdrop-blur-xl sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button 
                    @click="activeSideTab = 'layers'" 
                    class="w-10 h-10 flex items-center justify-center text-usuzumi hover:text-indigo hover:bg-indigo/5 rounded-xl transition-all duration-300 active:scale-90 group"
                >
                    <span class="material-symbols-outlined transition-transform duration-300 group-hover:-translate-x-1">arrow_back</span>
                </button>
                <div>
                    <h3 class="text-lg font-black text-sumi tracking-tight uppercase">Checkout</h3>
                    <p class="text-[10px] text-usuzumi/50 font-bold tracking-[0.1em] uppercase mt-0.5">Shipping Details</p>
                </div>
            </div>
        </div>

        <div class="p-6 flex-grow overflow-y-auto custom-scrollbar space-y-8">
             <!-- Form Section -->
             <div class="grid gap-6">
                <!-- Name -->
                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input
                            v-model="checkoutForm.name"
                            type="text"
                            required
                            :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.name}"
                            class="w-full h-12 pl-12 pr-6 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all text-[13px] font-bold placeholder:text-usuzumi/20"
                            placeholder="Contoh: Muhammad Rizky"
                        >
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg group-focus-within:text-indigo transition-colors">person</span>
                    </div>
                </div>

                <!-- Contact Grid -->
                <div class="grid gap-6">
                    <div class="relative group">
                        <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">WhatsApp <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                v-model="checkoutForm.phone"
                                type="text"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.phone}"
                                class="w-full h-12 pl-12 pr-6 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all text-[13px] font-bold placeholder:text-usuzumi/20"
                                placeholder="0812xxxxxxxx"
                            >
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg group-focus-within:text-indigo transition-colors">call</span>
                        </div>
                    </div>
                    <div class="relative group">
                        <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                v-model="checkoutForm.email"
                                type="email"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.email}"
                                class="w-full h-12 pl-12 pr-6 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all text-[13px] font-bold placeholder:text-usuzumi/20"
                                placeholder="email@contoh.com"
                            >
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg group-focus-within:text-indigo transition-colors">mail</span>
                        </div>
                    </div>
                </div>

                <!-- Size & Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative group">
                        <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Shoe Size <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select
                                v-model="checkoutForm.shoeSize"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.shoeSize}"
                                class="w-full h-12 px-5 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all text-[13px] font-bold appearance-none cursor-pointer"
                            >
                                <option value="">Pilih</option>
                                <option v-for="size in shoeSizes" :key="size" :value="String(size)">Size {{ size }}</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg pointer-events-none">expand_more</span>
                        </div>
                    </div>
                    <div class="relative group opacity-50 grayscale">
                        <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Status</label>
                        <div class="h-12 flex items-center px-5 bg-shironeri/50 border border-indigo/5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-matcha">
                            In Stock
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Shipping Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <textarea
                            v-model="checkoutForm.address"
                            rows="3"
                            required
                            :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.address}"
                            class="w-full pl-12 pr-6 py-4 rounded-[1.5rem] border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all text-[13px] font-bold placeholder:text-usuzumi/20 resize-none"
                            placeholder="Alamat lengkap..."
                        ></textarea>
                        <span class="material-symbols-outlined absolute left-4 top-5 text-usuzumi/30 text-lg group-focus-within:text-indigo transition-colors">location_on</span>
                    </div>
                </div>
            </div>

            <!-- Addons Section (Compact) -->
            <div class="space-y-4">
                <div class="p-5 rounded-[2rem] bg-shironeri/30 border border-indigo/5 space-y-5">
                    <!-- Fast Track -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm ring-1 ring-indigo/5 flex items-center justify-center text-indigo">
                                <span class="material-symbols-outlined text-xl">bolt</span>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-sumi uppercase tracking-tight">Fast Track</p>
                                <p class="text-[8px] text-usuzumi/50 font-bold uppercase tracking-widest mt-0.5">3-5 Days</p>
                            </div>
                        </div>
                        <div
                            class="w-12 h-7 rounded-full relative transition-all duration-500 cursor-pointer p-1 ring-1 ring-inset"
                            :class="checkoutForm.fastTrackEnabled ? 'bg-indigo ring-indigo' : 'bg-usuzumi/10 ring-usuzumi/5'"
                            @click="checkoutForm.fastTrackEnabled = !checkoutForm.fastTrackEnabled"
                        >
                            <div
                                class="w-5 h-5 bg-white rounded-full shadow-lg transition-transform duration-500 ease-spring"
                                :class="checkoutForm.fastTrackEnabled ? 'translate-x-5' : ''"
                            ></div>
                        </div>
                    </div>

                    <div class="h-px bg-indigo/5"></div>

                    <!-- Premium Box -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm ring-1 ring-indigo/5 flex items-center justify-center text-indigo">
                                <span class="material-symbols-outlined text-xl">inventory_2</span>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-sumi uppercase tracking-tight">Premium Box</p>
                                <p class="text-[8px] text-usuzumi/50 font-bold uppercase tracking-widest mt-0.5">Exclusive</p>
                            </div>
                        </div>
                        <div
                            class="w-12 h-7 rounded-full relative transition-all duration-500 cursor-pointer p-1 ring-1 ring-inset"
                            :class="checkoutForm.customBoxEnabled ? 'bg-indigo ring-indigo' : 'bg-usuzumi/10 ring-usuzumi/5'"
                            @click="checkoutForm.customBoxEnabled = !checkoutForm.customBoxEnabled"
                        >
                            <div
                                class="w-5 h-5 bg-white rounded-full shadow-lg transition-transform duration-500 ease-spring"
                                :class="checkoutForm.customBoxEnabled ? 'translate-x-5' : ''"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer / Checkout Button -->
        <div class="p-6 border-t border-indigo/5 bg-white sticky bottom-0 z-20">
            <div class="space-y-3 mb-6">
                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between text-[11px] font-bold text-usuzumi/40 uppercase tracking-widest">
                        <span>Base Custom</span>
                        <span class="text-sumi font-black">{{ formatCurrency(899000) }}</span>
                    </div>
                    <div v-if="checkoutForm.fastTrackEnabled" class="flex justify-between text-[11px] font-bold text-usuzumi/40 uppercase tracking-widest">
                        <span>Fast Track</span>
                        <span class="text-indigo font-black">+{{ formatCurrency(125000) }}</span>
                    </div>
                    <div v-if="checkoutForm.customBoxEnabled" class="flex justify-between text-[11px] font-bold text-usuzumi/40 uppercase tracking-widest">
                        <span>Premium Box</span>
                        <span class="text-indigo font-black">+{{ formatCurrency(65000) }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-indigo/5 flex justify-between items-center">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-usuzumi/40 uppercase tracking-[0.2em]">Total</span>
                        <span class="text-2xl font-black text-sumi tracking-tighter mt-1">{{ totalLabel }}</span>
                    </div>
                    <div class="flex flex-col items-end opacity-40">
                         <span class="material-symbols-outlined text-2xl">verified_user</span>
                         <span class="text-[8px] font-black uppercase tracking-widest mt-1">Secured</span>
                    </div>
                </div>
            </div>

            <button
                @click="$emit('checkout')"
                :disabled="isSaving"
                class="group relative overflow-hidden w-full h-16 bg-sumi text-washi font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 rounded-[2rem] flex items-center justify-center gap-4 shadow-2xl shadow-sumi/10 hover:bg-indigo hover:-translate-y-1 active:translate-y-0 active:scale-95 disabled:opacity-50"
            >
                <div class="absolute inset-0 bg-indigo opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <!-- Animated Shine -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shine"></div>

                <span v-if="isSaving" class="relative z-10 w-5 h-5 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></span>
                <span class="relative z-10 flex items-center gap-4">
                    {{ isSaving ? 'Processing...' : 'Finalize & Checkout' }}
                    <div class="w-7 h-7 rounded-full bg-white/10 flex items-center justify-center transition-all duration-500 group-hover:bg-white group-hover:rotate-[360deg]">
                        <span class="material-symbols-outlined text-[14px] transition-colors duration-500 group-hover:text-indigo">shopping_bag</span>
                    </div>
                </span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useStudioStore } from '../../../composables/useStudioStore';
import { formatCurrency } from '../../../utils/studio-utils';

const { checkoutForm, activeSideTab, isSaving } = useStudioStore();

const shoeSizes = Array.from({ length: 11 }, (_, i) => 35 + i);

const totalLabel = computed(() => {
    let total = 899000;
    if (checkoutForm.fastTrackEnabled) total += 125000;
    if (checkoutForm.customBoxEnabled) total += 65000;
    return formatCurrency(total);
});

defineEmits(['checkout']);
</script>

<style scoped>
.ease-spring {
    transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes shine {
    100% {
        transform: translateX(100%);
    }
}
.group-hover\:animate-shine {
    animation: shine 1.5s infinite;
}
</style>
