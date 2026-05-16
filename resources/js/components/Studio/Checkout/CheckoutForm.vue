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
                    <div class="relative">
                        <input
                            v-model="checkoutForm.name"
                            type="text"
                            id="name"
                            required
                            :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.name}"
                            class="peer w-full h-14 pl-12 pr-6 pt-5 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all duration-300 ease-in-out text-[13px] font-bold placeholder-transparent outline-none"
                            placeholder="Nama Lengkap"
                        >
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg transition-all duration-300 ease-in-out peer-focus:text-indigo">person</span>
                        <label
                            for="name"
                            class="absolute left-12 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4.5 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                        >
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>

                <!-- Contact Grid -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="relative group">
                        <div class="relative">
                            <input
                                v-model="checkoutForm.phone"
                                type="text"
                                id="phone"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.phone}"
                                class="peer w-full h-14 pl-12 pr-6 pt-5 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all duration-300 ease-in-out text-[13px] font-bold placeholder-transparent outline-none"
                                placeholder="WhatsApp"
                                @input="validatePhoneInput"
                                @blur="formatPhoneOnBlur"
                            >
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg transition-all duration-300 ease-in-out peer-focus:text-indigo">call</span>
                            <label
                                for="phone"
                                class="absolute left-12 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4.5 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                            >
                                WhatsApp <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="relative group">
                        <div class="relative">
                            <input
                                v-model="checkoutForm.email"
                                type="email"
                                id="email"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.email}"
                                class="peer w-full h-14 pl-12 pr-6 pt-5 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all duration-300 ease-in-out text-[13px] font-bold placeholder-transparent outline-none"
                                placeholder="Email"
                            >
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg transition-all duration-300 ease-in-out peer-focus:text-indigo">mail</span>
                            <label
                                for="email"
                                class="absolute left-12 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4.5 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                            >
                                Email <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Size & Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="relative group">
                        <div class="relative">
                            <select
                                v-model="checkoutForm.shoeSize"
                                id="shoe_size"
                                required
                                :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.shoeSize}"
                                class="peer w-full h-14 pl-5 pr-10 pt-5 rounded-2xl border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all duration-300 ease-in-out text-[13px] font-bold appearance-none cursor-pointer outline-none"
                            >
                                <option value="" disabled selected hidden></option>
                                <option v-for="size in shoeSizes" :key="size" :value="String(size)">Size {{ size }}</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg pointer-events-none transition-all duration-300 ease-in-out peer-focus:text-indigo">expand_more</span>
                            <label
                                for="shoe_size"
                                class="absolute left-5 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4.5 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                                :class="checkoutForm.shoeSize ? 'top-2 text-[9px] font-black uppercase tracking-[0.15em]' : 'top-4.5 text-[13px] font-bold normal-case tracking-normal'"
                            >
                                Shoe Size <span class="text-red-500">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="relative group opacity-50 grayscale">
                        <div class="h-14 flex items-center px-5 bg-shironeri/50 border border-indigo/5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-matcha">
                            In Stock
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="relative group">
                    <!-- Validation Hint (Above) -->
                    <p v-if="!checkoutForm.destinationId && addressSearch.length >= 3 && !isSearching && !showSuggestions" class="text-[7px] font-black uppercase text-red-500 tracking-widest mb-1.5 ml-1 animate-fade-in">
                        Pilih dari daftar saran
                    </p>

                    <div class="relative">
                        <input
                            v-model="addressSearch"
                            type="text"
                            id="address_search"
                            required
                            :class="[
                                'peer w-full h-14 pl-12 pr-10 pt-5 rounded-2xl border transition-all duration-300 ease-in-out text-[13px] font-bold placeholder-transparent outline-none',
                                checkoutForm.destinationId ? 'border-indigo/30 bg-white ring-4 ring-indigo/5' : 'border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5',
                                checkoutForm.formTouched && !checkoutForm.address ? 'border-red-200 bg-red-50/30' : '',
                                !checkoutForm.destinationId && addressSearch.length >= 3 && !isSearching && !showSuggestions ? 'border-red-400' : ''
                            ]"
                            placeholder="Shipping Address"
                            @input="handleAddressInput"
                            @blur="validateAddressSelection"
                        >
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-usuzumi/30 text-lg transition-all duration-300 ease-in-out peer-focus:text-indigo">location_on</span>
                        
                        <!-- Status Icon -->
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center">
                            <span v-if="checkoutForm.destinationId" class="material-symbols-outlined text-matcha text-xl animate-fade-in">check_circle</span>
                            <div v-else-if="isSearching" class="w-4 h-4 border-2 border-indigo/20 border-t-indigo rounded-full animate-spin"></div>
                        </div>

                        <label
                            for="address_search"
                            class="absolute left-12 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4.5 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                        >
                            Shipping Address <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Suggestions Dropdown -->
                        <div v-if="showSuggestions && suggestions.length > 0" class="absolute z-[100] left-0 right-0 mt-2 bg-white border border-indigo/10 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto custom-scrollbar">
                            <button
                                v-for="item in suggestions"
                                :key="item.id"
                                @click="selectDestination(item)"
                                class="w-full text-left px-5 py-4 hover:bg-indigo/5 transition-colors border-b border-indigo/5 last:border-0"
                            >
                                <p class="text-[13px] font-bold text-sumi">{{ item.label }}</p>
                                <p class="text-[10px] text-usuzumi/50 font-medium uppercase tracking-wider mt-1">{{ item.subdistrict_name }}, {{ item.city_name }}</p>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Detailed Address -->
                <div class="relative group">
                    <div class="relative">
                        <textarea
                            v-model="checkoutForm.addressDetail"
                            id="address_detail"
                            rows="2"
                            required
                            :class="{'border-red-200 bg-red-50/30': checkoutForm.formTouched && !checkoutForm.addressDetail}"
                            class="peer w-full pl-12 pr-6 py-6 rounded-[1.5rem] border-indigo/10 bg-shironeri/50 focus:bg-white focus:border-indigo focus:ring-4 focus:ring-indigo/5 transition-all duration-300 ease-in-out text-[13px] font-bold placeholder-transparent resize-none outline-none"
                            placeholder="Detail Alamat"
                        ></textarea>
                        <span class="material-symbols-outlined absolute left-4 top-6 text-usuzumi/30 text-lg transition-all duration-300 ease-in-out peer-focus:text-indigo">home</span>
                        <label
                            for="address_detail"
                            class="absolute left-12 top-2 text-[9px] font-black uppercase text-usuzumi/40 tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-6 peer-placeholder-shown:text-[13px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[9px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-indigo"
                        >
                            Detail Alamat <span class="text-red-500">*</span>
                        </label>
                    </div>
                </div>

                <!-- Courier Selection -->
                <transition name="panel-slide">
                    <div v-if="checkoutForm.destinationId" class="relative group">
                        <label class="text-[10px] font-black uppercase text-usuzumi/60 tracking-[0.2em] mb-2 block ml-1">Pilih Kurir <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <!-- Custom Dropdown Trigger -->
                            <button
                                @click="isCourierDropdownOpen = !isCourierDropdownOpen"
                                type="button"
                                :class="[
                                    'w-full h-14 px-5 rounded-2xl border transition-all duration-300 ease-in-out text-[13px] font-bold flex items-center justify-between group',
                                    checkoutForm.courier ? 'bg-white border-indigo/20 shadow-sm' : 'bg-shironeri/50 border-indigo/10',
                                    checkoutForm.formTouched && !checkoutForm.courier ? 'border-red-200 bg-red-50/30' : ''
                                ]"
                            >
                                <span :class="checkoutForm.courier ? 'text-sumi' : 'text-usuzumi/30'">
                                    {{ selectedCourierName || 'Pilih Kurir' }}
                                </span>
                                <span 
                                    class="material-symbols-outlined text-usuzumi/30 text-lg transition-transform duration-300 ease-in-out"
                                    :class="isCourierDropdownOpen ? 'rotate-180 text-indigo' : ''"
                                >expand_more</span>
                            </button>

                            <!-- Custom Dropdown Menu -->
                            <transition name="dropdown-slide">
                                <div v-if="isCourierDropdownOpen" class="absolute z-[110] left-0 right-0 mt-2 bg-white border border-indigo/10 rounded-2xl shadow-2xl overflow-hidden py-2">
                                    <div class="max-h-60 overflow-y-auto custom-scrollbar">
                                        <button
                                            v-for="c in checkoutForm.availableCouriers"
                                            :key="c.code"
                                            @click="selectCourier(c)"
                                            class="w-full text-left px-5 py-3 hover:bg-indigo/5 transition-all duration-300 ease-in-out flex items-center justify-between group"
                                        >
                                            <span class="text-[13px] font-bold text-sumi group-hover:text-indigo">{{ c.name }}</span>
                                            <span v-if="checkoutForm.courier === c.code" class="material-symbols-outlined text-indigo text-lg">check_circle</span>
                                        </button>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </transition>
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
                    <div v-if="checkoutForm.shippingCost > 0" class="flex justify-between text-[11px] font-bold text-usuzumi/40 uppercase tracking-widest">
                        <span>Shipping ({{ checkoutForm.courier.toUpperCase() }})</span>
                        <span class="text-indigo font-black">+{{ formatCurrency(checkoutForm.shippingCost) }}</span>
                    </div>
                    <div v-else-if="checkoutForm.isCalculatingShipping" class="flex justify-between text-[11px] font-bold text-usuzumi/40 uppercase tracking-widest animate-pulse">
                        <span>Calculating Shipping...</span>
                        <span class="text-usuzumi/20">--</span>
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

            <div class="space-y-4">
                <!-- S&K Checkbox -->
                <div class="flex items-start gap-4 px-2 mb-2">
                    <div class="relative flex items-center pt-0.5">
                        <input
                            type="checkbox"
                            id="agreed_to_terms_studio"
                            v-model="checkoutForm.agreedToTerms"
                            class="h-5 w-5 opacity-0 absolute inset-0 z-10 cursor-pointer"
                        >
                        <div 
                            class="h-5 w-5 rounded-lg border-2 transition-all duration-300 shadow-sm flex items-center justify-center"
                            :class="checkoutForm.agreedToTerms ? 'bg-indigo border-indigo' : 'bg-shironeri/50 border-indigo/10'"
                        >
                            <span 
                                v-if="checkoutForm.agreedToTerms"
                                class="material-symbols-outlined text-white text-[16px] animate-fade-in"
                            >check</span>
                        </div>
                    </div>
                    <label for="agreed_to_terms_studio" class="text-[10px] font-bold text-usuzumi/60 uppercase tracking-widest leading-relaxed cursor-pointer select-none">
                        Saya menyetujui <span class="text-indigo underline">Syarat dan Ketentuan</span> yang berlaku di Bogor Sneakers.
                    </label>
                </div>

                <button
                    @click="$emit('checkout')"
                    :disabled="isSaving || !isFormValid"
                    class="group relative overflow-hidden w-full h-16 bg-sumi text-washi font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 rounded-[2rem] flex items-center justify-center gap-4 shadow-2xl shadow-sumi/10 hover:bg-indigo hover:-translate-y-1 active:translate-y-0 active:scale-95 disabled:opacity-50 disabled:translate-y-0"
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

                <!-- Dynamic Information Hint -->
                <div v-if="!isFormValid && !isSaving" class="flex items-center justify-center gap-2 animate-fade-in">
                    <span class="material-symbols-outlined text-[14px] text-usuzumi/40">info</span>
                    <p class="text-[9px] text-usuzumi/40 font-bold uppercase tracking-widest">
                        Lengkapi: <span class="text-indigo underline">{{ missingFieldsLabel }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { useStudioStore, ORIGIN_ID } from '../../../composables/useStudioStore';
import { formatCurrency } from '../../../utils/studio-utils';

const { checkoutForm, activeSideTab, isSaving } = useStudioStore();

const isFormValid = computed(() => {
    return !!(
        checkoutForm.name &&
        checkoutForm.phone.replace(/\D/g, '').length >= 10 &&
        checkoutForm.shoeSize &&
        checkoutForm.destinationId &&
        checkoutForm.addressDetail &&
        checkoutForm.courier &&
        checkoutForm.agreedToTerms &&
        !checkoutForm.isCalculatingShipping
    );
});

const missingFieldsLabel = computed(() => {
    if (!checkoutForm.name) return 'Nama Lengkap';
    if (checkoutForm.phone.replace(/\D/g, '').length < 10) return 'Nomor WhatsApp';
    if (!checkoutForm.shoeSize) return 'Ukuran Sepatu';
    if (!checkoutForm.destinationId) return 'Pilih Kecamatan/Kota';
    if (!checkoutForm.addressDetail) return 'Alamat Lengkap';
    if (!checkoutForm.courier) return 'Pilih Kurir';
    if (!checkoutForm.agreedToTerms) return 'Setujui Syarat & Ketentuan';
    return '';
});

const validateAddressSelection = () => {
    setTimeout(() => {
        if (!checkoutForm.destinationId && addressSearch.value.length > 0) {
            showSuggestions.value = false;
        }
    }, 200);
};

const validatePhoneInput = (e: Event) => {
    const input = e.target as HTMLInputElement;
    let value = input.value.replace(/\D/g, '');
    if (value.length > 13) value = value.substring(0, 13);
    checkoutForm.phone = value;
};

const formatPhoneOnBlur = () => {
    let value = checkoutForm.phone.replace(/\D/g, '');
    if (value.startsWith('0')) value = value.substring(1);
    else if (value.startsWith('62')) value = value.substring(2);

    if (value.length === 12) {
        const part1 = value.substring(0, 3);
        const part2 = value.substring(3, 7);
        const part3 = value.substring(7, 11);
        const part4 = value.substring(11, 12);
        checkoutForm.phone = `+62 ${part1}-${part2}-${part3}-${part4}`;
    }
};

// Shipping State
const addressSearch = ref(checkoutForm.address || '');
const suggestions = ref<any[]>([]);
const isSearching = ref(false);
const showSuggestions = ref(false);
const isCourierDropdownOpen = ref(false);
let searchTimeout: any = null;
let abortController: AbortController | null = null;

const shoeSizes = Array.from({ length: 11 }, (_, i) => 35 + i);

const selectedCourierName = computed(() => {
    const courier = checkoutForm.availableCouriers.find(c => c.code === checkoutForm.courier);
    return courier ? courier.name : '';
});

const selectCourier = (courier: any) => {
    checkoutForm.courier = courier.code;
    isCourierDropdownOpen.value = false;
    calculateShippingCost();
};

const calculateShippingCost = async () => {
    if (!checkoutForm.destinationId || !checkoutForm.courier) return;

    checkoutForm.isCalculatingShipping = true;
    try {
        const response = await fetch('/shipping/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
            },
            body: JSON.stringify({
                origin: ORIGIN_ID,
                destination: checkoutForm.destinationId,
                weight: 1000, // 1kg
                courier: checkoutForm.courier,
                price: 'lowest'
            })
        });

        const result = await response.json();
        if (result.meta.code === 200 && Array.isArray(result.data)) {
            // Find REG service
            const regService = result.data.find((s: any) => s.service === 'REG');
            if (regService) {
                checkoutForm.shippingCost = regService.cost;
            } else {
                // Fallback to first available if REG not found, or 0
                checkoutForm.shippingCost = result.data[0]?.cost || 0;
            }
        }
    } catch (err) {
        console.error('Failed to calculate shipping:', err);
    } finally {
        checkoutForm.isCalculatingShipping = false;
    }
};

const handleAddressInput = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    
    if (addressSearch.value.length < 3) {
        suggestions.value = [];
        showSuggestions.value = false;
        return;
    }

    searchTimeout = setTimeout(() => {
        fetchDestinations();
    }, 500); // 500ms Debounce
};

const fetchDestinations = async () => {
    // Cancel previous request if still pending
    if (abortController) abortController.abort();
    abortController = new AbortController();

    isSearching.value = true;
    showSuggestions.value = true;

    try {
        const response = await fetch(`/shipping/destinations?search=${encodeURIComponent(addressSearch.value)}`, {
            signal: abortController.signal
        });
        const result = await response.json();
        
        if (result.meta.code === 200) {
            suggestions.value = result.data;
        }
    } catch (err: any) {
        if (err.name !== 'AbortError') {
            console.error('Failed to fetch destinations:', err);
        }
    } finally {
        isSearching.value = false;
    }
};

const selectDestination = (item: any) => {
    addressSearch.value = item.label;
    checkoutForm.address = item.label;
    checkoutForm.destinationId = item.id;
    showSuggestions.value = false;
    
    // Fetch couriers once destination is selected
    fetchCouriers();

    // If courier already selected, recalculate cost
    if (checkoutForm.courier) {
        calculateShippingCost();
    }
};

const fetchCouriers = async () => {
    try {
        const response = await fetch('/shipping/couriers');
        const result = await response.json();
        if (result.meta.code === 200) {
            checkoutForm.availableCouriers = result.data;
        }
    } catch (err) {
        console.error('Failed to fetch couriers:', err);
    }
};

// If address already exists in store, fetch couriers
onMounted(() => {
    if (checkoutForm.destinationId) {
        fetchCouriers();
    }
});

const totalLabel = computed(() => {
    let total = 899000;
    if (checkoutForm.fastTrackEnabled) total += 125000;
    if (checkoutForm.customBoxEnabled) total += 65000;
    total += checkoutForm.shippingCost;
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

.dropdown-slide-enter-active, .dropdown-slide-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.dropdown-slide-enter-from, .dropdown-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
