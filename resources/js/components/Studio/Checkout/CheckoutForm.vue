<template>
    <div class="flex flex-col h-full">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center bg-white sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="activeSideTab = 'layers'" class="w-10 h-10 flex items-center justify-center text-secondary hover:text-black hover:bg-gray-100 rounded-xl transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <div>
                    <h3 class="text-lg font-black text-black uppercase tracking-tight">GUEST CHECKOUT</h3>
                    <p class="text-[10px] text-secondary font-bold tracking-widest uppercase opacity-60">Lengkapi data pengiriman</p>
                </div>
            </div>
        </div>

        <div class="p-8 flex-grow overflow-y-auto custom-scrollbar space-y-8">
             <div class="grid gap-6">
                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Nama Lengkap <span class="text-error">*</span></label>
                    <input
                        v-model="checkoutForm.name"
                        type="text"
                        required
                        :class="{'border-error/50 bg-error/[0.02]': checkoutForm.formTouched && !checkoutForm.name}"
                        class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30"
                        placeholder="Contoh: Muhammad Rizky"
                    >
                    <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">person</span>
                </div>
                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">WhatsApp Number <span class="text-error">*</span></label>
                    <input
                        v-model="checkoutForm.phone"
                        type="text"
                        required
                        :class="{'border-error/50 bg-error/[0.02]': checkoutForm.formTouched && !checkoutForm.phone}"
                        class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30"
                        placeholder="0812xxxxxxxx"
                    >
                    <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">call</span>
                </div>
                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Email Address <span class="text-error">*</span></label>
                    <input
                        v-model="checkoutForm.email"
                        type="email"
                        required
                        :class="{'border-error/50 bg-error/[0.02]': checkoutForm.formTouched && !checkoutForm.email}"
                        class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30"
                        placeholder="email@contoh.com"
                    >
                    <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">mail</span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="relative group">
                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Shoe Size <span class="text-error">*</span></label>
                        <select
                            v-model="checkoutForm.shoeSize"
                            required
                            :class="{'border-error/50 bg-error/[0.02]': checkoutForm.formTouched && !checkoutForm.shoeSize}"
                            class="w-full h-12 px-5 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold appearance-none cursor-pointer"
                        >
                            <option value="">Pilih</option>
                            <option v-for="size in shoeSizes" :key="size" :value="String(size)">Size {{ size }}</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg pointer-events-none">expand_more</span>
                    </div>
                    <div class="relative group opacity-40 grayscale pointer-events-none">
                        <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1">Stock</label>
                        <div class="h-12 flex items-center px-5 bg-gray-100 rounded-2xl text-[10px] font-black uppercase tracking-widest">Available</div>
                    </div>
                </div>

                <div class="relative group">
                    <label class="text-[10px] font-black uppercase text-secondary tracking-widest mb-2 block ml-1 opacity-60">Shipping Address <span class="text-error">*</span></label>
                    <textarea
                        v-model="checkoutForm.address"
                        rows="4"
                        required
                        :class="{'border-error/50 bg-error/[0.02]': checkoutForm.formTouched && !checkoutForm.address}"
                        class="w-full px-5 py-4 rounded-2xl border-gray-200 bg-gray-50/50 focus:bg-white focus:border-black focus:ring-0 transition-all text-sm font-bold placeholder:opacity-30 resize-none"
                        placeholder="Masukkan alamat lengkap pengiriman..."
                    ></textarea>
                    <span class="material-symbols-outlined absolute right-4 top-[38px] text-gray-300 text-lg group-focus-within:text-black transition-colors">location_on</span>
                </div>
            </div>

            <div class="p-6 rounded-[32px] bg-gray-50 border border-gray-100 space-y-4">
                 <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-xl">bolt</span>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight">Fast Track</p>
                            <p class="text-[9px] text-secondary font-bold uppercase tracking-widest opacity-60">Pengerjaan 3-5 Hari</p>
                        </div>
                    </div>
                    <div
                        class="w-14 h-7 rounded-full relative transition-all cursor-pointer border-2"
                        :class="checkoutForm.fastTrackEnabled ? 'bg-primary border-primary' : 'bg-gray-200 border-gray-200'"
                        @click="checkoutForm.fastTrackEnabled = !checkoutForm.fastTrackEnabled"
                    >
                        <div
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all"
                            :class="checkoutForm.fastTrackEnabled ? 'translate-x-7' : ''"
                        ></div>
                    </div>
                 </div>

                 <div class="h-[1px] bg-gray-200/50"></div>

                 <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-xl">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-xs font-black uppercase tracking-tight">Premium Box</p>
                            <p class="text-[9px] text-secondary font-bold uppercase tracking-widest opacity-60">Custom Exclusive Box</p>
                        </div>
                    </div>
                    <div
                        class="w-14 h-7 rounded-full relative transition-all cursor-pointer border-2"
                        :class="checkoutForm.customBoxEnabled ? 'bg-primary border-primary' : 'bg-gray-200 border-gray-200'"
                        @click="checkoutForm.customBoxEnabled = !checkoutForm.customBoxEnabled"
                    >
                        <div
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-all"
                            :class="checkoutForm.customBoxEnabled ? 'translate-x-7' : ''"
                        ></div>
                    </div>
                 </div>
            </div>
        </div>

        <div class="p-8 border-t border-gray-100 bg-white sticky bottom-0">
            <div class="space-y-3 mb-8">
                <div class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                    <span>Base Custom Pair</span>
                    <span class="text-black">{{ formatCurrency(899000) }}</span>
                </div>
                <div v-if="checkoutForm.fastTrackEnabled" class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                    <span>Fast Track Fee</span>
                    <span class="text-primary">+{{ formatCurrency(125000) }}</span>
                </div>
                <div v-if="checkoutForm.customBoxEnabled" class="flex justify-between text-[11px] font-bold text-secondary uppercase tracking-[0.1em]">
                    <span>Premium Box Fee</span>
                    <span class="text-primary">+{{ formatCurrency(65000) }}</span>
                </div>
                <div class="pt-4 border-t border-gray-100 flex justify-between items-end">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-secondary uppercase tracking-[0.2em] opacity-60">Total Amount</span>
                        <span class="text-3xl font-black text-black tracking-tighter mt-1">{{ totalLabel }}</span>
                    </div>
                </div>
            </div>
            <button
                @click="$emit('checkout')"
                :disabled="isSaving"
                class="w-full h-16 bg-black text-white font-black text-[12px] uppercase tracking-[0.2em] hover:bg-primary hover:text-black transition-all rounded-[24px] flex items-center justify-center gap-4 shadow-2xl shadow-black/20 disabled:opacity-50 active:scale-95"
            >
                 <span v-if="isSaving" class="w-5 h-5 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></span>
                 {{ isSaving ? 'PROCESSING ORDER...' : 'FINALIZE & CHECKOUT' }}
                 <span v-if="!isSaving" class="material-symbols-outlined text-xl">shopping_bag</span>
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
