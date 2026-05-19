<template>
    <section
        id="how-it-works"
        class="pattern-bamboo bg-shironeri px-4 py-20 sm:px-6 md:py-28 lg:px-16 overflow-hidden relative"
    >
        <!-- Decorative glowing ambient light circles -->
        <div class="absolute top-0 right-0 w-80 h-80 bg-radial from-matcha/5 to-transparent pointer-events-none rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-radial from-sakura/5 to-transparent pointer-events-none rounded-full blur-3xl"></div>

        <div class="mx-auto max-w-7xl relative z-10" @mouseleave="hoveredStep = null">
            <!-- Header with Japanese aesthetic -->
            <div class="flex flex-col items-center mb-16 md:mb-24 text-center">
                <div class="inline-flex items-center gap-2 mb-4 px-3.5 py-1 rounded-full border border-matcha/20 bg-matcha/5 shadow-xs">
                    <span class="font-heading text-[9px] sm:text-[10px] font-bold tracking-[0.2em] text-matcha uppercase">
                        ご注文手順
                    </span>
                    <span class="h-1.5 w-1.5 rounded-full bg-matcha animate-pulse"></span>
                    <span class="font-heading text-[9px] sm:text-[10px] font-bold tracking-[0.2em] text-matcha uppercase">
                        HOW TO ORDER
                    </span>
                </div>
                
                <h2 class="font-heading text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-sumi uppercase relative inline-block">
                    Cara Mudah Memesan
                    <span class="absolute -bottom-2 left-1/2 -translate-x-1/2 h-1 w-24 bg-matcha rounded-full shadow-xs"></span>
                </h2>
                
                <p class="mt-6 max-w-xl text-xs sm:text-sm md:text-base leading-relaxed text-hai font-sans">
                    Proses pemesanan transparan dan terverifikasi dari pemilihan katalog hingga sepatu impian tiba dengan selamat di tangan Anda.
                </p>
            </div>

            <!-- Steps Container with Desktop Timeline Line -->
            <div class="relative mb-20 sm:mb-28">
                <!-- Desktop Horizontal Connecting Line (lg and up) -->
                <div class="hidden lg:block absolute top-[52px] left-[12%] right-[12%] h-[2px] bg-sumi/5 pointer-events-none z-0">
                    <!-- Default subtle dashed line -->
                    <div class="absolute inset-0 bg-gradient-to-r from-matcha/30 via-indigo-500/30 to-sakura/30 rounded-full"></div>
                    <!-- Active solid glowing line dynamically scaling based on hover state -->
                    <div 
                        class="absolute top-0 left-0 h-full bg-gradient-to-r from-matcha via-indigo-500 via-take to-sakura transition-all duration-700 ease-out rounded-full shadow-[0_0_8px_rgba(124,140,90,0.4)]"
                        :style="{ 
                            width: hoveredStep === null ? '100%' : `${(hoveredStep / 3) * 100}%`,
                            opacity: hoveredStep === null ? '0.25' : '1'
                        }"
                    ></div>
                </div>

                <!-- Steps Grid / Responsive Layout -->
                <div class="relative z-10 grid grid-cols-1 gap-12 lg:grid-cols-4 lg:gap-8">
                    <!-- Mobile Left-Side Vertical Timeline Line (shows on mobile < lg) -->
                    <div class="lg:hidden absolute top-4 bottom-4 left-6 w-[2px] bg-sumi/5 pointer-events-none z-0">
                        <div 
                            class="absolute top-0 left-0 w-full bg-gradient-to-b from-matcha via-indigo-500 via-take to-sakura transition-all duration-700 ease-out rounded-full"
                            :style="{ 
                                height: hoveredStep === null ? '100%' : `${((hoveredStep + 1) / 4) * 100}%`,
                                opacity: hoveredStep === null ? '0.25' : '1'
                            }"
                        ></div>
                    </div>

                    <div
                        v-for="(step, idx) in steps"
                        :key="idx"
                        class="group relative flex flex-col pl-16 lg:pl-0 lg:items-center text-left lg:text-center transition-all duration-300 cursor-pointer"
                        @mouseenter="hoveredStep = idx"
                        @click="hoveredStep = idx"
                    >
                        <!-- Dynamic Border Glow & Background (Desktop card styling) -->
                        <div 
                            class="absolute inset-0 -m-4 rounded-3xl border border-transparent transition-all duration-500 hidden lg:block pointer-events-none z-0"
                            :class="[
                                step.hoverBorder, 
                                step.hoverShadow, 
                                hoveredStep === idx ? 'bg-washi/60 border-sumi/10 shadow-lg scale-[1.02]' : 'bg-transparent'
                            ]"
                        ></div>

                        <!-- Step Number Badge (Positioned on the timeline line) -->
                        <div class="absolute left-1 lg:left-auto lg:relative lg:mb-6 z-10 flex items-center justify-center">
                            <!-- Outer pulsing circle -->
                            <span 
                                class="absolute h-10 w-10 sm:h-12 sm:w-12 rounded-full opacity-0 scale-75 transition-all duration-500" 
                                :class="[step.color, hoveredStep === idx ? 'opacity-20 scale-110 animate-ping-slow' : '']"
                            ></span>
                            
                            <!-- Main Number Circle -->
                            <span
                                class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-full text-base font-black text-washi shadow-sm border-2 border-shironeri transition-all duration-300"
                                :class="[step.color, hoveredStep === idx ? 'scale-110 rotate-[360deg] shadow-md' : '']"
                            >
                                {{ idx + 1 }}
                            </span>
                            
                            <!-- Japanese Step Sub-Badge (Desktop only) -->
                            <span class="absolute -bottom-5 hidden lg:block font-heading text-[8px] font-bold text-hai/50 uppercase tracking-widest bg-shironeri px-2 py-0.5 rounded-full border border-sumi/5 shadow-2xs">
                                {{ step.jaLabel }}
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div 
                            class="relative z-10 w-full bg-washi lg:bg-transparent p-6 lg:p-0 rounded-2xl border border-sumi/5 lg:border-none shadow-xs lg:shadow-none transition-all duration-300"
                            :class="[hoveredStep === idx ? 'border-sumi/10 shadow-md lg:shadow-none' : '']"
                        >
                            <!-- Icon Container -->
                            <div
                                class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl transition-all duration-500 shadow-2xs"
                                :class="[step.bgColor, hoveredStep === idx ? 'scale-110 rotate-6 shadow-xs' : '']"
                            >
                                <i :class="`${step.icon} text-xl ${step.textColor} transition-transform duration-300`"></i>
                            </div>

                            <!-- Title -->
                            <h3 class="font-heading mb-2 text-base sm:text-lg font-extrabold text-sumi flex items-center lg:justify-center gap-2">
                                {{ step.title }}
                                <!-- Checkmark for dynamic active states -->
                                <i 
                                    class="bi bi-patch-check-fill text-xs transition-all duration-300" 
                                    :class="[step.textColor, hoveredStep === idx ? 'opacity-100 scale-110' : 'opacity-0 scale-75']"
                                ></i>
                            </h3>

                            <!-- Description -->
                            <p class="text-xs sm:text-sm leading-relaxed text-hai mb-4 lg:px-4 font-sans">
                                {{ step.description }}
                            </p>

                            <!-- Direct Action CTA (Dynamic interactive links - always visible for great discoverability) -->
                            <div class="mt-4 transition-all duration-300 transform translate-y-0 opacity-100">
                                <a
                                    :href="step.ctaLink"
                                    class="inline-flex items-center gap-1.5 text-[10px] sm:text-[11px] font-extrabold tracking-wider uppercase border-b-2 py-0.5 transition-all duration-300"
                                    :class="[
                                        hoveredStep === idx 
                                            ? 'text-sumi border-sumi scale-105' 
                                            : `${step.textColor} ${step.borderCtaColor}`
                                    ]"
                                >
                                    {{ step.ctaText }}
                                    <i 
                                        class="bi bi-chevron-right text-[8px] sm:text-[9px] transition-transform duration-300" 
                                        :class="[hoveredStep === idx ? 'translate-x-1' : '']"
                                    ></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guarantees Grid - Sleek & Modern Card Design -->
            <div class="accent-top pt-16">
                <div class="text-center mb-10">
                    <span class="font-heading text-[9px] sm:text-[10px] font-bold tracking-widest text-hai/50 uppercase block mb-1">
                        Jaminan Layanan Kami
                    </span>
                    <h3 class="font-heading text-lg sm:text-xl font-extrabold text-sumi uppercase">
                        Bogor Sneakers Guarantees
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div
                        v-for="guarantee in guarantees"
                        :key="guarantee.id"
                        class="group flex items-start gap-4 rounded-2xl border border-sumi/5 bg-washi p-6 shadow-2xs transition-all duration-300 hover:border-sumi/10 hover:shadow-md hover:-translate-y-1 bg-gradient-to-br from-washi to-shironeri"
                    >
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full transition-all duration-500 group-hover:scale-110 group-hover:rotate-12 shadow-3xs"
                            :class="guarantee.bgColor"
                        >
                            <i :class="`${guarantee.icon} text-xl ${guarantee.textColor} transition-transform duration-300 group-hover:scale-110`"></i>
                        </div>
                        <div>
                            <p class="font-heading font-bold text-sumi mb-1 text-sm sm:text-base group-hover:text-matcha transition-colors">
                                {{ guarantee.title }}
                            </p>
                            <p class="text-xs leading-relaxed text-hai font-sans">
                                {{ guarantee.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const hoveredStep = ref<number | null>(null);

// Dynamic WhatsApp link resolution based on backend contacts from page props
const whatsappUrl = computed(() => {
    const contacts = page.props.floatingContacts as any[];
    if (Array.isArray(contacts) && contacts.length > 0) {
        // Use first active admin phone number
        const admin = contacts[0];
        if (admin && admin.phone) {
            const phone = admin.phone.replace(/\D/g, '');
            const normalized = phone.startsWith('0') ? '62' + phone.substring(1) : (phone.startsWith('8') ? '62' + phone : phone);
            return `https://wa.me/${normalized}?text=Halo%20Admin%20Bogor%20Sneakers,%20saya%20tertarik%20untuk%20memesan%20sepatu`;
        }
    }
    // Fallback WhatsApp link
    return 'https://wa.me/6281285558484?text=Halo%20Admin%20Bogor%20Sneakers,%20saya%20tertarik%20untuk%20memesan%20sepatu';
});

// Stepper steps with localized content & actions connected dynamically to app features
const steps = computed(() => [
    {
        title: 'Pilih Produk',
        description: 'Browse katalog kami. Filter brand, size, dan warna sesuai selera. Cek stok real-time.',
        icon: 'bi bi-search',
        color: 'bg-matcha',
        bgColor: 'bg-matcha/10',
        textColor: 'text-matcha',
        borderCtaColor: 'border-matcha/30',
        jaLabel: '第一歩',
        ctaText: 'Eksplor Katalog',
        ctaLink: '#katalog',
        hoverBorder: 'group-hover:border-matcha/20',
        hoverShadow: 'group-hover:shadow-[0_20px_40px_rgba(124,140,90,0.06)]',
    },
    {
        title: 'DM / WA Kami',
        description: 'Hubungi via Instagram atau WhatsApp. Kami konfirmasi ketersediaan dalam <5 menit.',
        icon: 'bi bi-chat-dots',
        color: 'bg-indigo-500',
        bgColor: 'bg-indigo-500/10',
        textColor: 'text-indigo-600',
        borderCtaColor: 'border-indigo-500/30',
        jaLabel: '第二歩',
        ctaText: 'Hubungi WhatsApp',
        ctaLink: whatsappUrl.value,
        hoverBorder: 'group-hover:border-indigo-500/20',
        hoverShadow: 'group-hover:shadow-[0_20px_40px_rgba(99,102,241,0.06)]',
    },
    {
        title: 'Transfer DP',
        description: 'Bayar DP 50% untuk konfirmasi slot PO. Transfer ke rekening yang terverifikasi.',
        icon: 'bi bi-credit-card',
        color: 'bg-take',
        bgColor: 'bg-take/10',
        textColor: 'text-take',
        borderCtaColor: 'border-take/30',
        jaLabel: '第三歩',
        ctaText: 'Ketentuan Pembayaran',
        ctaLink: whatsappUrl.value,
        hoverBorder: 'group-hover:border-take/20',
        hoverShadow: 'group-hover:shadow-[0_20px_40px_rgba(74,157,111,0.06)]',
    },
    {
        title: 'Terima Sepatu',
        description: 'Sepatu tiba, bayar sisa pelunasan. Cek keaslian bersama kurir atau ambil langsung.',
        icon: 'bi bi-box2-heart',
        color: 'bg-sakura',
        bgColor: 'bg-sakura/20',
        textColor: 'text-sakura',
        borderCtaColor: 'border-sakura/30',
        jaLabel: '第四歩',
        ctaText: 'Lacak Pengiriman',
        ctaLink: '/tracking',
        hoverBorder: 'group-hover:border-sakura/20',
        hoverShadow: 'group-hover:shadow-[0_20px_40px_rgba(217,119,129,0.06)]',
    },
]);

const guarantees = ref([
    {
        id: 1,
        title: '100% Legit Guarantee',
        desc: 'Semua sneakers dijamin 100% original. Uang kembali penuh jika terbukti palsu.',
        icon: 'bi bi-shield-check',
        bgColor: 'bg-matcha/10',
        textColor: 'text-matcha',
    },
    {
        id: 2,
        title: 'Pengiriman Aman',
        desc: 'Proteksi ekstra menggunakan bubble wrap tebal dan double box tanpa biaya tambahan.',
        icon: 'bi bi-truck',
        bgColor: 'bg-indigo-500/10',
        textColor: 'text-indigo-600',
    },
    {
        id: 3,
        title: 'CS Responsif',
        desc: 'Tim admin kami siap membantu membalas pesan Anda dalam kurang dari 5 menit (09.00-21.00).',
        icon: 'bi bi-headset',
        bgColor: 'bg-take/10',
        textColor: 'text-take',
    },
]);
</script>

<style scoped>
.pattern-bamboo {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cpath fill='none' stroke='%237C8C5A' stroke-width='1' opacity='0.14' d='M20 0 L20 80 M60 0 L60 80'/%3E%3Crect x='15' y='10' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='15' y='35' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='15' y='60' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='55' y='20' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3Crect x='55' y='45' width='10' height='15' fill='none' stroke='%237C8C5A' stroke-width='0.8' opacity='0.11'/%3E%3C/svg%3E");
    background-size: 80px 80px;
}

.accent-top {
    border-top: 2px solid #7c8c5a;
    padding-top: 1rem;
}

@keyframes pingSlow {
    0% {
        transform: scale(0.9);
        opacity: 0.4;
    }
    100% {
        transform: scale(1.3);
        opacity: 0;
    }
}

.animate-ping-slow {
    animation: pingSlow 1.8s cubic-bezier(0.16, 1, 0.3, 1) infinite;
}

@keyframes timelineProgress {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-timeline-progress {
    background-size: 200% 200%;
    animation: timelineProgress 6s ease infinite;
}
</style>
