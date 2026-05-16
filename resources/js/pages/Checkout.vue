<template>
    <div class="font-body h-screen flex flex-col bg-washi text-sumi antialiased overflow-hidden">
        <Head title="Checkout" />
        <FloatingAdminPanel :contacts="contacts" />
        
        <!-- Slim Integrated Header -->
        <header class="h-16 flex items-center justify-between px-6 border-b border-sumi/10 bg-white/50 backdrop-blur-md shrink-0 z-20">
            <div class="flex items-center gap-4">
                <Link
                    href="/katalog"
                    class="h-10 w-10 flex items-center justify-center rounded-xl border border-sumi/10 hover:bg-sumi hover:text-washi transition-all group"
                >
                    <i class="bi bi-arrow-left text-lg"></i>
                </Link>
                <div>
                    <h1 class="text-lg font-black tracking-tight uppercase leading-none">Checkout</h1>
                    <p class="text-[10px] text-hai font-bold uppercase tracking-widest mt-1">Selesaikan pesanan Anda</p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="text-[10px] font-black uppercase tracking-widest text-hai">Keamanan Terjamin</span>
                <i class="bi bi-shield-check text-matcha text-xl"></i>
            </div>
        </header>

        <main class="flex-1 flex flex-col lg:flex-row overflow-hidden">
            <!-- Left: Scrollable Form Area -->
            <section class="flex-1 overflow-y-auto custom-scrollbar bg-washi px-6 py-8 lg:px-12">
                <div class="max-w-2xl mx-auto space-y-10">
                    <!-- Customer Information -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 border-b border-sumi/10 pb-3">
                            <span class="text-xs font-black uppercase tracking-[0.2em] text-hai">01</span>
                            <h2 class="text-sm font-black tracking-tight uppercase">Informasi Kontak</h2>
                        </div>
                        
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="relative group">
                                <input
                                    v-model="form.customer_name"
                                    type="text"
                                    id="customer_name"
                                    class="peer w-full h-12 px-4 pt-4 rounded-xl border border-sumi/10 bg-shironeri focus:bg-white focus:border-matcha focus:ring-4 focus:ring-matcha/5 transition-all duration-300 ease-in-out text-[11px] font-bold outline-none placeholder-transparent"
                                    placeholder="Nama Lengkap"
                                >
                                <label
                                    for="customer_name"
                                    class="absolute left-4 top-1 text-[8px] font-black uppercase text-hai tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-[11px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-1 peer-focus:text-[8px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-matcha"
                                >
                                    Nama Lengkap
                                </label>
                            </div>
                            <div class="relative group">
                                <input
                                    v-model="form.customer_phone"
                                    type="text"
                                    id="customer_phone"
                                    class="peer w-full h-12 px-4 pt-4 rounded-xl border border-sumi/10 bg-shironeri focus:bg-white focus:border-matcha focus:ring-4 focus:ring-matcha/5 transition-all duration-300 ease-in-out text-[11px] font-bold outline-none placeholder-transparent"
                                    placeholder="WhatsApp"
                                    @input="validatePhoneInput"
                                    @blur="formatPhoneOnBlur"
                                >
                                <label
                                    for="customer_phone"
                                    class="absolute left-4 top-1 text-[8px] font-black uppercase text-hai tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-[11px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-1 peer-focus:text-[8px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-matcha"
                                >
                                    WhatsApp
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 border-b border-sumi/10 pb-3">
                            <span class="text-xs font-black uppercase tracking-[0.2em] text-hai">02</span>
                            <h2 class="text-sm font-black tracking-tight uppercase">Alamat Pengiriman</h2>
                        </div>

                        <div class="grid gap-4">
                            <!-- Destination Search -->
                            <div class="relative">
                                <!-- Validation Hint (Above) -->
                                <p v-if="!destinationId && addressSearch.length >= 3 && !isSearching && !showSuggestions" class="text-[7px] font-black uppercase text-red-500 tracking-widest mb-1.5 ml-1 animate-fade-in">
                                    Mohon pilih dari daftar saran
                                </p>

                                <div class="relative group">
                                    <input
                                        v-model="addressSearch"
                                        type="text"
                                        id="address_search"
                                        class="peer w-full h-12 pl-11 pr-10 pt-4 rounded-xl border border-sumi/10 bg-shironeri focus:bg-white transition-all duration-300 ease-in-out text-[11px] font-bold outline-none placeholder-transparent"
                                        :class="[
                                            destinationId ? 'border-matcha/30 ring-4 ring-matcha/5' : 'focus:border-matcha focus:ring-4 focus:ring-matcha/5',
                                            !destinationId && addressSearch.length >= 3 && !isSearching && !showSuggestions ? 'border-red-400' : ''
                                        ]"
                                        placeholder="Cari kecamatan atau kota..."
                                        @input="handleAddressInput"
                                        @blur="validateAddressSelection"
                                    >
                                    <i class="bi bi-geo-alt absolute left-4 top-1/2 -translate-y-1/2 text-hai text-sm transition-colors duration-300 ease-in-out peer-focus:text-matcha"></i>
                                    
                                    <!-- Status Icon -->
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                                        <i v-if="destinationId" class="bi bi-check-circle-fill text-matcha text-sm animate-fade-in"></i>
                                        <div v-else-if="isSearching" class="w-3.5 h-3.5 border-2 border-sumi/20 border-t-sumi rounded-full animate-spin"></div>
                                    </div>

                                    <label
                                        for="address_search"
                                        class="absolute left-11 top-1 text-[8px] font-black uppercase text-hai tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-3.5 peer-placeholder-shown:text-[11px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-1 peer-focus:text-[8px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-matcha"
                                    >
                                        Kecamatan / Kota
                                    </label>

                                    <!-- Suggestions Dropdown -->
                                    <div v-if="showSuggestions && suggestions.length > 0" class="absolute z-[50] left-0 right-0 mt-2 bg-white border border-sumi/10 rounded-xl shadow-2xl overflow-hidden max-h-48 overflow-y-auto">
                                        <button
                                            v-for="item in suggestions"
                                            :key="item.id"
                                            @click="selectDestination(item)"
                                            class="w-full text-left px-4 py-3 hover:bg-sumi/5 transition-colors border-b border-sumi/5 last:border-0"
                                        >
                                            <p class="text-[11px] font-bold text-sumi">{{ item.label }}</p>
                                            <p class="text-[9px] text-hai font-medium uppercase tracking-wider mt-0.5">{{ item.subdistrict_name }}, {{ item.city_name }}</p>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Detailed Address -->
                            <div class="relative group">
                                <textarea
                                    v-model="addressDetail"
                                    id="address_detail"
                                    rows="2"
                                    class="peer w-full p-4 pt-6 rounded-xl border border-sumi/10 bg-shironeri focus:bg-white focus:border-matcha focus:ring-4 focus:ring-matcha/5 transition-all duration-300 ease-in-out text-[11px] font-bold resize-none outline-none placeholder-transparent"
                                    placeholder="Alamat Lengkap"
                                ></textarea>
                                <label
                                    for="address_detail"
                                    class="absolute left-4 top-2 text-[8px] font-black uppercase text-hai tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4 peer-placeholder-shown:text-[11px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[8px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-matcha"
                                >
                                    Alamat Lengkap
                                </label>
                            </div>

                            <!-- Courier Selection -->
                            <div v-if="destinationId" class="relative group animate-fade-in">
                                <label class="text-[9px] font-black uppercase text-hai tracking-[0.2em] mb-1.5 block ml-1">Pilih Kurir</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <button
                                        v-for="c in availableCouriers"
                                        :key="c.code"
                                        @click="selectCourier(c)"
                                        class="flex flex-col items-center justify-center py-2.5 rounded-xl border transition-all duration-300 ease-in-out"
                                        :class="courier === c.code ? 'border-matcha bg-matcha/5 ring-4 ring-matcha/5' : 'border-sumi/10 bg-shironeri hover:border-sumi/30'"
                                    >
                                        <span class="text-[10px] font-black uppercase tracking-widest text-sumi">{{ c.name }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="space-y-6 pb-12">
                        <div class="flex items-center gap-3 border-b border-sumi/10 pb-3">
                            <span class="text-xs font-black uppercase tracking-[0.2em] text-hai">03</span>
                            <h2 class="text-sm font-black tracking-tight uppercase">Catatan Tambahan</h2>
                        </div>
                        <div class="relative group">
                            <textarea
                                v-model="form.notes"
                                id="notes"
                                rows="2"
                                class="peer w-full p-4 pt-6 rounded-xl border border-sumi/10 bg-shironeri focus:bg-white focus:border-matcha focus:ring-4 focus:ring-matcha/5 transition-all duration-300 ease-in-out text-[11px] font-bold resize-none outline-none placeholder-transparent"
                                placeholder="Catatan Tambahan"
                            ></textarea>
                            <label
                                for="notes"
                                class="absolute left-4 top-2 text-[8px] font-black uppercase text-hai tracking-[0.15em] transition-all duration-300 ease-in-out pointer-events-none peer-placeholder-shown:top-4 peer-placeholder-shown:text-[11px] peer-placeholder-shown:font-bold peer-placeholder-shown:normal-case peer-placeholder-shown:tracking-normal peer-focus:top-2 peer-focus:text-[8px] peer-focus:font-black peer-focus:uppercase peer-focus:tracking-[0.15em] peer-focus:text-matcha"
                            >
                                Catatan Tambahan (Opsional)
                            </label>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Right: Fixed Summary Area -->
            <aside class="w-full lg:w-[400px] bg-shironeri border-t lg:border-t-0 lg:border-l border-sumi/10 flex flex-col shrink-0">
                <div class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8">
                    <h2 class="text-sm font-black tracking-tight uppercase mb-6 flex items-center justify-between">
                        Ringkasan Pesanan
                        <span class="px-2 py-0.5 rounded bg-sumi text-washi text-[9px] font-black">{{ cartState.count }} ITEM</span>
                    </h2>
                    
                    <!-- Item List -->
                    <div class="space-y-3 mb-8">
                        <div v-for="item in cartState.items" :key="item.id" class="flex gap-4 p-3 rounded-2xl bg-white/50 border border-sumi/5 shadow-sm">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-white border border-sumi/5">
                                <img v-if="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-cover">
                                <div v-else class="flex h-full w-full items-center justify-center bg-sumi/5 text-hai">
                                    <i class="bi bi-image"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 flex flex-col justify-center">
                                <p class="text-[11px] font-bold text-sumi truncate leading-tight">{{ item.name }}</p>
                                <div class="flex justify-between items-end mt-1.5">
                                    <p class="text-[9px] text-hai font-black uppercase tracking-widest">Sz. {{ item.size }} × {{ item.qty }}</p>
                                    <p class="text-[11px] font-black text-sumi">{{ formatCurrency(item.price * item.qty) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calculations -->
                    <div class="space-y-2.5 border-t border-sumi/10 pt-6">
                        <div class="flex justify-between text-[10px] font-black text-hai uppercase tracking-widest">
                            <span>Subtotal</span>
                            <span class="text-sumi">{{ formatCurrency(cartState.total) }}</span>
                        </div>
                        <div class="flex justify-between text-[10px] font-black text-hai uppercase tracking-widest">
                            <span>Ongkos Kirim <span v-if="courier" class="text-matcha ml-1">({{ courier.toUpperCase() }})</span></span>
                            <span v-if="isCalculatingShipping" class="text-matcha animate-pulse">Menghitung...</span>
                            <span v-else class="text-sumi">{{ formatCurrency(shippingCost) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary Actions -->
                <div class="p-6 lg:p-8 bg-white/50 border-t border-sumi/10 space-y-4">
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[9px] font-black text-hai uppercase tracking-[0.2em]">Total Pembayaran</p>
                            <p class="text-2xl font-black text-sumi tracking-tighter mt-1">{{ formatCurrency(cartState.total + shippingCost) }}</p>
                        </div>
                        <div class="text-matcha flex flex-col items-end opacity-40">
                            <i class="bi bi-shield-check text-xl"></i>
                            <span class="text-[7px] font-black uppercase tracking-widest mt-0.5">Legit Secured</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- S&K Checkbox -->
                        <div class="flex items-start gap-3 px-1">
                            <div class="relative flex items-center">
                                <input
                                    type="checkbox"
                                    id="agreed_to_terms"
                                    v-model="form.agreedToTerms"
                                    class="h-4 w-4 opacity-0 absolute inset-0 z-10 cursor-pointer"
                                >
                                <div 
                                    class="h-4 w-4 rounded border transition-all duration-300 flex items-center justify-center"
                                    :class="form.agreedToTerms ? 'bg-matcha border-matcha' : 'bg-shironeri border-sumi/20'"
                                >
                                    <i 
                                        v-if="form.agreedToTerms"
                                        class="bi bi-check-lg text-white text-[10px] animate-fade-in"
                                    ></i>
                                </div>
                            </div>
                            <label for="agreed_to_terms" class="text-[9px] font-bold text-hai uppercase tracking-widest leading-relaxed cursor-pointer select-none">
                                Saya menyetujui <span class="text-sumi underline">Syarat dan Ketentuan</span> yang berlaku di Bogor Sneakers.
                            </label>
                        </div>

                        <button
                            @click="handleCheckout"
                            :disabled="isSubmitting || cartState.count === 0 || !isFormValid"
                            class="group relative overflow-hidden w-full h-14 bg-sumi text-washi font-black text-[10px] uppercase tracking-[0.2em] transition-all duration-500 rounded-xl flex items-center justify-center gap-3 shadow-xl shadow-sumi/10 hover:bg-matcha hover:text-sumi active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-washi/20 border-t-washi rounded-full animate-spin"></span>
                            <span v-else class="flex items-center gap-2">
                                Pesan Sekarang
                                <i class="bi bi-arrow-right text-base transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </button>
                        
                        <!-- Dynamic Information Hint -->
                        <div v-if="!isFormValid && !isSubmitting" class="flex items-center justify-center gap-2 animate-fade-in">
                            <i class="bi bi-info-circle text-[10px] text-hai"></i>
                            <p class="text-[8px] text-hai font-bold uppercase tracking-widest">
                                Silakan lengkapi: <span class="text-sumi underline">{{ missingFieldsLabel }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <p class="text-[9px] text-center text-hai font-bold uppercase tracking-widest">
                        Konfirmasi pembayaran via WhatsApp
                    </p>
                </div>
            </aside>
        </main>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, reactive } from 'vue';
import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingCartButton from '@/components/ui/FloatingCartButton.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import { cartState } from '@/stores/cart';
import type { FloatingContact } from '@/types/floating-ui';

// Origin Bogor
const ORIGIN_ID = 8137;

const page = usePage();
const contacts = computed<FloatingContact[]>(() => (page.props.floatingContacts as any) || []);

const form = reactive({
    customer_name: '',
    customer_phone: '',
    customer_address: '',
    notes: '',
    items: [] as any[],
    shipping_cost: 0,
    courier: '',
    agreedToTerms: false,
});

// Shipping Logic
const addressSearch = ref('');
const addressDetail = ref('');
const suggestions = ref<any[]>([]);
const isSearching = ref(false);
const showSuggestions = ref(false);
const destinationId = ref<number | null>(null);
const courier = ref('');
const availableCouriers = ref<any[]>([]);
const shippingCost = ref(0);
const isCalculatingShipping = ref(false);
const isSubmitting = ref(false);

const isFormValid = computed(() => {
    return !!(
        form.customer_name &&
        form.customer_phone.replace(/\D/g, '').length >= 10 &&
        destinationId.value &&
        addressDetail.value &&
        courier.value &&
        form.agreedToTerms &&
        !isCalculatingShipping.value
    );
});

const missingFieldsLabel = computed(() => {
    if (!form.customer_name) return 'Nama Lengkap';
    if (form.customer_phone.replace(/\D/g, '').length < 10) return 'Nomor WhatsApp';
    if (!destinationId.value) return 'Pilih Kecamatan/Kota';
    if (!addressDetail.value) return 'Alamat Lengkap';
    if (!courier.value) return 'Pilih Kurir';
    if (!form.agreedToTerms) return 'Setujui Syarat & Ketentuan';
    return '';
});

const validateAddressSelection = () => {
    // Small delay to allow selectDestination to trigger first if a suggestion was clicked
    setTimeout(() => {
        if (!destinationId.value && addressSearch.value.length > 0) {
            // If they typed but didn't select from suggestions, clear or keep search but mark invalid
            showSuggestions.value = false;
        }
    }, 200);
};

const validatePhoneInput = (e: Event) => {
    const input = e.target as HTMLInputElement;
    // Remove non-digits
    let value = input.value.replace(/\D/g, '');
    
    // Max 13 digits
    if (value.length > 13) {
        value = value.substring(0, 13);
    }
    
    form.customer_phone = value;
};

const formatPhoneOnBlur = () => {
    let value = form.customer_phone.replace(/\D/g, '');
    
    // Check if it starts with 0 or 62 to normalize
    if (value.startsWith('0')) {
        value = value.substring(1);
    } else if (value.startsWith('62')) {
        value = value.substring(2);
    }

    // Standard Indonesian length is usually 10-13 digits total (including prefix)
    // If it's exactly 12 digits after normalization (making it 13 digits with '0' prefix)
    if (value.length === 12) {
        // Format: +62 xxx-xxxx-xxxx-x
        const part1 = value.substring(0, 3);
        const part2 = value.substring(3, 7);
        const part3 = value.substring(7, 11);
        const part4 = value.substring(11, 12);
        form.customer_phone = `+62 ${part1}-${part2}-${part3}-${part4}`;
    }
};

let searchTimeout: any = null;
let abortController: AbortController | null = null;

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val);
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
    }, 500);
};

const fetchDestinations = async () => {
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
        if (err.name !== 'AbortError') console.error('Failed to fetch destinations:', err);
    } finally {
        isSearching.value = false;
    }
};

const selectDestination = (item: any) => {
    addressSearch.value = item.label;
    destinationId.value = item.id;
    showSuggestions.value = false;
    fetchCouriers();
};

const fetchCouriers = async () => {
    try {
        const response = await fetch('/shipping/couriers');
        const result = await response.json();
        if (result.meta.code === 200) {
            availableCouriers.value = result.data;
        }
    } catch (err) {
        console.error('Failed to fetch couriers:', err);
    }
};

const selectCourier = (c: any) => {
    courier.value = c.code;
    calculateShippingCost();
};

const calculateShippingCost = async () => {
    if (!destinationId.value || !courier.value) return;
    isCalculatingShipping.value = true;
    try {
        const response = await fetch('/shipping/calculate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
            },
            body: JSON.stringify({
                origin: ORIGIN_ID,
                destination: destinationId.value,
                weight: 1000,
                courier: courier.value,
                price: 'lowest'
            })
        });
        const result = await response.json();
        if (result.meta.code === 200 && Array.isArray(result.data)) {
            const regService = result.data.find((s: any) => s.service === 'REG');
            shippingCost.value = regService ? regService.cost : (result.data[0]?.cost || 0);
        }
    } catch (err) {
        console.error('Failed to calculate shipping:', err);
    } finally {
        isCalculatingShipping.value = false;
    }
};

// Redirect if empty
onMounted(() => {
    if (cartState.count === 0) {
        router.visit('/katalog');
    }
});

const handleCheckout = async () => {
    if (isSubmitting.value) return;
    
    // Validation basic
    if (!form.customer_name || !form.customer_phone || !destinationId.value || !addressDetail.value || !courier.value) {
        alert('Mohon lengkapi semua informasi pengiriman.');
        return;
    }

    isSubmitting.value = true;
    
    // Construct final address
    form.customer_address = `${addressDetail.value}, ${addressSearch.value}`;
    form.shipping_cost = shippingCost.value;
    form.courier = courier.value;
    
    // Map cart items to backend format
    form.items = cartState.items.map(item => ({
        catalog_id: item.catalog_id,
        size: String(item.size),
        quantity: item.qty
    }));

    try {
        const response = await fetch('/api/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
            },
            body: JSON.stringify(form)
        });

        const result = await response.json();

        if (response.status === 201 && result.status === 'success') {
            // Success! Open WA and redirect or clear cart
            window.open(result.data.wa_link, '_blank');
            
            // Re-fetch to clear cart via server side logic
            router.visit('/', {
                onSuccess: () => {
                    alert('Pesanan berhasil dibuat! Silakan konfirmasi pembayaran via WhatsApp.');
                }
            });
        } else {
            alert(result.message || 'Terjadi kesalahan saat membuat pesanan.');
        }
    } catch (err) {
        console.error('Checkout error:', err);
        alert('Gagal memproses pesanan. Silakan coba lagi.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e2e2;
    border-radius: 10px;
}
</style>
