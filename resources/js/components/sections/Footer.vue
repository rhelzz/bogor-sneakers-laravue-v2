<template>
    <!-- FOOTER -->
    <footer class="pattern-koinobori bg-sumi px-4 py-16 text-washi sm:px-6 lg:px-16 relative overflow-hidden">
        <!-- Accent decorative glows -->
        <div class="absolute -top-12 -left-12 h-64 w-64 bg-radial from-matcha/10 to-transparent pointer-events-none rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -right-12 h-64 w-64 bg-radial from-sakura/10 to-transparent pointer-events-none rounded-full blur-3xl"></div>

        <div class="mx-auto max-w-7xl relative z-10">
            <!-- Big Typography Header & Brand Stamp -->
            <div class="mb-16 flex flex-col md:flex-row md:items-end md:justify-between border-b border-washi/10 pb-12 gap-8">
                <div>
                    <h2 class="font-heading text-5xl sm:text-7xl lg:text-8xl leading-none font-black tracking-tighter opacity-15 select-none hover:opacity-25 transition-opacity duration-500 uppercase">
                        BOGOR
                    </h2>
                    <h2 class="font-heading -mt-3 sm:-mt-6 lg:-mt-8 text-5xl sm:text-7xl lg:text-8xl leading-none font-black tracking-tighter text-matcha uppercase">
                        SNEAKERS
                    </h2>
                </div>
                
                <div class="flex flex-col items-start md:items-end">
                    <div class="flex items-center gap-2 mb-2 px-3 py-1 rounded-full border border-washi/10 bg-washi/5">
                        <span class="h-1.5 w-1.5 rounded-full bg-matcha animate-pulse"></span>
                        <span class="font-heading text-[9px] font-bold tracking-[0.2em] text-washi/60 uppercase">
                            TOKYO - BOGOR CONNECTION
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm tracking-widest text-washi/40 uppercase font-mono">
                        EST. 2019 // INDONESIA
                    </p>
                </div>
            </div>

            <!-- Footer Symmetrical Grid (Mobile friendly 2-column or 4-column) -->
            <div class="grid grid-cols-2 gap-8 border-b border-washi/10 pb-12 md:grid-cols-2 lg:grid-cols-4">
                <!-- Column 1: Store & Opening Hours -->
                <div class="col-span-2 sm:col-span-1">
                    <p class="mb-5 text-xs font-bold tracking-widest text-washi/30 uppercase font-heading">
                        Hub & Waktu
                    </p>
                    <div class="space-y-4 text-xs sm:text-sm">
                        <div class="flex items-start gap-2.5 text-washi/70 hover:text-washi transition-colors">
                            <i class="bi bi-geo-alt text-matcha text-base mt-0.5"></i>
                            <div>
                                <p class="font-bold text-washi font-heading">Jl. Pajajaran No.88</p>
                                <p class="font-sans text-washi/60">Bogor, Jawa Barat</p>
                                <p class="font-sans text-washi/60">16143 Indonesia</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 text-washi/70 hover:text-washi transition-colors">
                            <i class="bi bi-clock text-matcha text-base mt-0.5"></i>
                            <div>
                                <p class="font-bold text-washi font-heading">Jam Operasional</p>
                                <p class="font-sans text-washi/60">Senin-Jumat: 09.00-21.00</p>
                                <p class="font-sans text-washi/60">Sabtu-Minggu: 10.00-22.00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Navigation Links -->
                <div>
                    <p class="mb-5 text-xs font-bold tracking-widest text-washi/30 uppercase font-heading">
                        Navigasi
                    </p>
                    <div class="space-y-3.5 text-xs sm:text-sm font-sans">
                        <a href="#katalog" class="group flex items-center gap-2 text-washi/70 transition-colors hover:text-matcha">
                            <span class="h-[1px] w-0 bg-matcha transition-all duration-300 group-hover:w-3"></span>
                            Katalog
                        </a>
                        <a href="#po-tracker" class="group flex items-center gap-2 text-washi/70 transition-colors hover:text-matcha">
                            <span class="h-[1px] w-0 bg-matcha transition-all duration-300 group-hover:w-3"></span>
                            Live PO
                        </a>
                        <a href="#tiktok" class="group flex items-center gap-2 text-washi/70 transition-colors hover:text-matcha">
                            <span class="h-[1px] w-0 bg-matcha transition-all duration-300 group-hover:w-3"></span>
                            TikTok Feed
                        </a>
                        <a href="#galeri" class="group flex items-center gap-2 text-washi/70 transition-colors hover:text-matcha">
                            <span class="h-[1px] w-0 bg-matcha transition-all duration-300 group-hover:w-3"></span>
                            Galeri DIY
                        </a>
                    </div>
                </div>

                <!-- Column 3: Social Links (Dynamic WA) -->
                <div>
                    <p class="mb-5 text-xs font-bold tracking-widest text-washi/30 uppercase font-heading">
                        Sosial
                    </p>
                    <div class="space-y-3.5 text-xs sm:text-sm font-sans">
                        <a href="https://instagram.com/bogorsneakers" target="_blank" class="group flex items-center gap-2.5 text-washi/70 transition-colors hover:text-matcha">
                            <i class="bi bi-instagram group-hover:scale-110 transition-transform duration-300 text-matcha"></i>
                            @bogorsneakers
                        </a>
                        <a href="https://tiktok.com/@bogorsneakers" target="_blank" class="group flex items-center gap-2.5 text-washi/70 transition-colors hover:text-matcha">
                            <i class="bi bi-tiktok group-hover:scale-110 transition-transform duration-300 text-matcha"></i>
                            @bogorsneakers
                        </a>
                        <a :href="whatsappUrl" target="_blank" class="group flex items-center gap-2.5 text-washi/70 transition-colors hover:text-matcha">
                            <i class="bi bi-whatsapp group-hover:scale-110 transition-transform duration-300 text-matcha"></i>
                            <span>{{ dynamicPhone }}</span>
                        </a>
                    </div>
                </div>

                <!-- Column 4: Newsletter Subscription (UX Polish) -->
                <div class="col-span-2 sm:col-span-1">
                    <p class="mb-5 text-xs font-bold tracking-widest text-washi/30 uppercase font-heading">
                        SNEAKERHEAD CLUB
                    </p>
                    <div class="space-y-3 text-xs sm:text-sm">
                        <p class="text-washi/60 leading-relaxed text-xs font-sans">
                            Bergabunglah dengan klub newsletter kami untuk info rilis produk eksklusif dan promo menarik.
                        </p>
                        
                        <div v-if="subscribed" class="p-3 bg-matcha/10 border border-matcha/20 rounded-xl text-matcha font-bold text-center animate-pulse text-xs">
                            🎉 Selamat Bergabung!
                        </div>
                        
                        <form v-else @submit.prevent="handleSubscribe" class="flex gap-2">
                            <input 
                                v-model="email" 
                                type="email" 
                                required
                                placeholder="Email Anda..." 
                                class="w-full bg-washi/5 border border-washi/15 rounded-xl px-3.5 py-2 text-xs text-washi placeholder-washi/30 focus:outline-none focus:border-matcha/50 focus:ring-1 focus:ring-matcha/50 transition-all duration-300"
                            />
                            <button 
                                type="submit" 
                                class="bg-matcha text-sumi font-heading font-black text-xs px-4 py-2 rounded-xl hover:bg-matcha/80 active:scale-95 transition-all duration-300 shadow-xs"
                            >
                                JOIN
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bottom Section (Copyright, payment badges, aesthetic stamp) -->
            <div class="flex flex-col items-center justify-between pt-8 md:flex-row gap-6">
                <div class="flex flex-col gap-3 items-center md:items-start">
                    <p class="text-[11px] text-washi/40 text-center md:text-left font-sans">
                        © 2026 Bogor Sneakers. Hak Cipta Dilindungi Undang-Undang.
                    </p>
                    <!-- Trust / Payment Badges -->
                    <div class="flex flex-wrap justify-center md:justify-start items-center gap-2 opacity-35 hover:opacity-65 transition-opacity duration-300">
                        <span class="text-[8px] font-heading font-bold uppercase tracking-widest text-washi/60 mr-1">PAYMENT:</span>
                        <span class="border border-washi/30 rounded px-1.5 py-0.5 text-[8px] font-bold tracking-wider text-washi uppercase">QRIS</span>
                        <span class="border border-washi/30 rounded px-1.5 py-0.5 text-[8px] font-bold tracking-wider text-washi uppercase">GOPAY</span>
                        <span class="border border-washi/30 rounded px-1.5 py-0.5 text-[8px] font-bold tracking-wider text-washi uppercase">TRANSFER</span>
                        <span class="border border-washi/30 rounded px-1.5 py-0.5 text-[8px] font-bold tracking-wider text-washi uppercase">CARD</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="text-xs text-washi/40 font-mono">Crafted with</span>
                    <span class="text-matcha font-heading font-extrabold flex items-center gap-1.5">
                        日本美 <span class="text-sakura animate-pulse">🌸</span>
                    </span>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Email Club State
const email = ref('');
const subscribed = ref(false);

const handleSubscribe = () => {
    if (email.value.trim() !== '') {
        subscribed.value = true;
        email.value = '';
    }
};

// Dynamic contacts integration (preserving dynamic data flow)
const page = usePage();

const firstActiveContact = computed(() => {
    const contacts = (page.props.floatingContacts as any[]) || [];
    return contacts.length > 0 ? contacts[0] : null;
});

const dynamicPhone = computed(() => {
    return firstActiveContact.value ? firstActiveContact.value.phone : '0812-XXXX-XXXX';
});

const whatsappUrl = computed(() => {
    if (!firstActiveContact.value) return '#';
    let phone = firstActiveContact.value.phone || '';
    phone = phone.replace(/\D/g, '');
    if (phone.startsWith('0')) {
        phone = '62' + phone.substring(1);
    }
    return `https://wa.me/${phone}?text=Halo%20Admin%20Bogor%20Sneakers%20!%20Saya%20tertarik%20untuk%20bertanya%20mengenai%20sepatu.`;
});
</script>

<style scoped>
.pattern-koinobori {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='100' viewBox='0 0 120 100'%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1.2' opacity='0.15' d='M10 30 Q 30 20 50 30 T 90 30'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='1' opacity='0.11' d='M10 55 Q 30 45 50 55 T 90 55'/%3E%3Cpath fill='none' stroke='%23f7f5f0' stroke-width='0.8' opacity='0.08' d='M10 80 Q 30 70 50 80 T 90 80'/%3E%3C/svg%3E");
    background-size: 120px 100px;
}
</style>
