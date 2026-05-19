<template>
    <section
        id="hero"
        class="relative flex flex-col-reverse lg:flex-row min-h-[calc(100vh-80px)] lg:min-h-screen overflow-hidden bg-washi"
    >
        <!-- Japanese vertical accent - Desktop Only -->
        <div
            class="absolute top-1/2 left-6 hidden -translate-y-1/2 lg:block z-20 pointer-events-none"
        >
            <span class="vertical-text font-noto text-[10px] font-semibold tracking-[0.35em] text-hai/30 uppercase"
                >ボゴールスニーカー</span
            >
        </div>

        <!-- Left Content -->
        <div
            class="pattern-wave relative z-10 flex w-full lg:w-1/2 flex-col justify-center px-6 py-12 sm:px-12 md:px-16 lg:px-24 border-t lg:border-t-0 lg:border-r border-sumi/10 bg-washi"
        >
            <!-- Badge -->
            <div class="animate-slide-up mb-6 sm:mb-8" style="animation-delay: 0.1s">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-matcha/30 bg-matcha/5 px-3 py-1 text-[10px] font-bold tracking-widest text-matcha uppercase">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-matcha opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-matcha"></span>
                        </span>
                        EST. 2019 - BOGOR, IDN
                    </span>
                </div>
            </div>

            <!-- Heading -->
            <div class="animate-slide-up mb-6" style="animation-delay: 0.2s">
                <h1
                    class="font-heading text-4xl sm:text-5xl md:text-6xl xl:text-7xl leading-[0.95] font-black tracking-tight text-sumi uppercase"
                >
                    BOGOR'S<br />
                    <span class="relative inline-block text-matcha">
                        SNEAKERS
                        <span class="absolute -bottom-1 left-0 h-1.5 w-full bg-matcha/20 rounded-full"></span>
                    </span>
                </h1>
            </div>

            <!-- Description -->
            <div class="animate-slide-up mb-8" style="animation-delay: 0.3s">
                <p class="max-w-md text-base sm:text-lg leading-relaxed text-hai font-sans">
                    Sneaker culture meets street identity. Discover rare drops, local vibes, and expert-verified authentication.
                    <span class="block mt-3 font-semibold text-sumi">Rare. Local. 100% Legit.</span>
                </p>
            </div>

            <!-- Info Metadata Shelf -->
            <div class="animate-slide-up mb-8 sm:mb-10" style="animation-delay: 0.4s">
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 xl:grid-cols-4 gap-6 border-y border-sumi/10 py-8">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-widest text-hai/80 uppercase block">Location</span>
                        <span class="text-xs sm:text-sm font-bold text-sumi">Bogor, West Java</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-widest text-hai/80 uppercase block">Catalog</span>
                        <span class="text-xs sm:text-sm font-bold text-sumi">240+ Styles</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-widest text-hai/80 uppercase block">Status</span>
                        <span class="text-xs sm:text-sm font-bold text-sumi flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Open 09-21
                        </span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold tracking-widest text-hai/80 uppercase block">Authenticity</span>
                        <span class="text-xs sm:text-sm font-bold text-sumi">100% Verified</span>
                    </div>
                </div>
            </div>

            <!-- CTA Actions -->
            <div
                class="animate-slide-up flex flex-col sm:flex-row items-stretch sm:items-center gap-6"
                style="animation-delay: 0.5s"
            >
                <a
                    href="#katalog"
                    class="group/btn relative inline-flex items-center justify-center gap-2 overflow-hidden rounded-full bg-sumi px-8 py-4 text-xs font-bold tracking-widest text-washi transition-all duration-300 hover:bg-matcha hover:scale-[1.03] active:scale-[0.98] shadow-md hover:shadow-matcha/20 text-center"
                >
                    LIHAT KATALOG
                    <i class="bi bi-arrow-right transition-transform duration-300 group-hover/btn:translate-x-1"></i>
                </a>

                <a 
                    href="#katalog" 
                    class="group/scroll inline-flex items-center justify-center gap-3 text-xs font-bold tracking-wider text-hai transition-colors hover:text-sumi py-2"
                >
                    <span class="flex h-8 w-8 items-center justify-center rounded-full border border-sumi/10 transition-colors group-hover/scroll:border-sumi">
                        <i class="bi bi-arrow-down animate-bounce text-[10px]"></i>
                    </span>
                    <span>SCROLL UNTUK EKSPLOR</span>
                </a>
            </div>
        </div>

        <!-- Right Visual - Carousel -->
        <div
            class="group relative flex w-full lg:w-1/2 h-[45vh] sm:h-[50vh] md:h-[55vh] lg:h-auto items-center justify-center bg-sumi overflow-hidden"
        >
            <!-- Carousel Container -->
            <div class="relative flex h-full w-full items-center justify-center">
                <!-- Carousel Slides - Full Background -->
                <div class="absolute inset-0 bg-kuro">
                    <div
                        v-for="(slide, idx) in activeSlides"
                        :key="slide.id"
                        class="carousel-slide absolute inset-0 overflow-hidden transition-all duration-1000 ease-in-out"
                        :class="
                            idx === currentIndex
                                ? 'scale-100 opacity-100 z-10'
                                : 'pointer-events-none scale-105 opacity-0 z-0'
                        "
                    >
                        <!-- Active Image with Ken Burns Effect -->
                        <img
                            v-if="slide.image_url"
                            :src="slide.image_url"
                            :alt="`Carousel ${idx + 1}`"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-[6000ms] ease-out"
                            :class="idx === currentIndex ? 'scale-110' : 'scale-100'"
                            loading="lazy"
                        />

                        <!-- Placeholder Image / Empty State -->
                        <div
                            v-else
                            class="absolute inset-0 flex items-center justify-center bg-radial from-usuzumi/20 via-kuro to-kuro p-6 text-center"
                        >
                            <div class="relative flex flex-col items-center max-w-xs p-8 rounded-2xl border border-washi/5 bg-washi/[0.02] backdrop-blur-md">
                                <!-- Sneaker/Image Graphic -->
                                <div class="relative mb-6 flex h-16 w-16 items-center justify-center rounded-full border border-washi/10 bg-washi/[0.03]">
                                    <i class="bi text-2xl text-matcha animate-pulse" :class="slide.icon || 'bi-tag'"></i>
                                    <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5 items-center justify-center rounded-full bg-matcha text-[8px] font-bold text-sumi">!</span>
                                </div>
                                
                                <h3 class="text-xs font-bold tracking-widest text-washi uppercase mb-2">
                                    {{ slide.title || 'Preview Produk' }}
                                </h3>
                                
                                <p class="text-[10px] leading-relaxed text-washi/40 max-w-[200px] mb-6 min-h-[48px] flex items-center justify-center">
                                    {{ slide.desc || 'Belum ada slide aktif di database. Tambahkan gambar produk melalui Dashboard Admin.' }}
                                </p>

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-matcha/10 px-2.5 py-1 text-[8px] font-bold tracking-wider text-matcha uppercase border border-matcha/20">
                                    {{ activeSlides.length > 1 ? `Standby Slide ${idx + 1}/${activeSlides.length}` : 'Dynamic Standby' }}
                                </span>
                            </div>
                        </div>

                        <!-- Gradient Overlay -->
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-kuro/70 via-kuro/20 to-transparent pointer-events-none"
                        ></div>
                    </div>
                </div>

                <!-- Editorial Floating Badge -->
                <div class="absolute top-6 right-6 z-20 animate-slide-up" style="animation-delay: 0.4s">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-washi/20 bg-kuro/40 px-3.5 py-1.5 text-[9px] font-bold tracking-widest text-washi uppercase backdrop-blur-md">
                        <i class="bi bi-shield-check text-matcha"></i>
                        Curated Collection
                    </span>
                </div>

                <!-- Navigation Buttons - Overlay (Only show if more than 1 slide) -->
                <button
                    v-if="activeSlides.length > 1"
                    @click="prev"
                    class="absolute left-4 z-20 flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full border border-washi/10 bg-kuro/40 text-washi backdrop-blur-md transition-all duration-300 hover:bg-kuro/60 active:scale-90 lg:opacity-0 lg:group-hover:opacity-100"
                    aria-label="Previous Slide"
                >
                    <i class="bi bi-chevron-left text-sm sm:text-base"></i>
                </button>
                <button
                    v-if="activeSlides.length > 1"
                    @click="next"
                    class="absolute right-4 z-20 flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-full border border-washi/10 bg-kuro/40 text-washi backdrop-blur-md transition-all duration-300 hover:bg-kuro/60 active:scale-90 lg:opacity-0 lg:group-hover:opacity-100"
                    aria-label="Next Slide"
                >
                    <i class="bi bi-chevron-right text-sm sm:text-base"></i>
                </button>

                <!-- Bar Indicators - Bottom (Only show if more than 1 slide) -->
                <div
                    v-if="activeSlides.length > 1"
                    class="absolute inset-x-0 bottom-6 z-20 flex justify-center gap-2"
                >
                    <button
                        v-for="(slide, idx) in activeSlides"
                        :key="`dot-${slide.id}`"
                        @click="currentIndex = idx"
                        class="h-1.5 transition-all duration-500 rounded-full backdrop-blur-xs"
                        :class="
                            idx === currentIndex
                                ? 'w-8 bg-washi'
                                : 'w-2 bg-washi/40 hover:bg-washi/70'
                        "
                        :aria-label="`Go to slide ${idx + 1}`"
                    ></button>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';

import type { HeroCarouselSlide } from '@/types/home';

const props = defineProps<{
    slides: HeroCarouselSlide[];
}>();

const currentIndex = ref(0);
let interval: ReturnType<typeof setInterval> | undefined;

// Elegant Fallback slides if database slide is empty
const activeSlides = computed(() => {
    if (props.slides && props.slides.length > 0) {
        return props.slides;
    }
    // Return three rich fallback slides to showcase auto-sliding empty states beautifully
    return [
        {
            id: -1,
            image_url: '',
            is_active: true,
            title: 'Koleksi Terkurasi',
            desc: 'Dapatkan koleksi sneaker pilihan terbaik dari brand lokal dan global terpopuler di Bogor.',
            icon: 'bi-tag'
        },
        {
            id: -2,
            image_url: '',
            is_active: true,
            title: '100% Legit & Authentic',
            desc: 'Seluruh produk dijamin keasliannya lewat proses verifikasi kurator ahli Bogor Sneakers.',
            icon: 'bi-shield-check'
        },
        {
            id: -3,
            image_url: '',
            is_active: true,
            title: 'Street Identity Bogor',
            desc: 'Dukung pergerakan sneakerhead lokal dan temukan streetwear style yang merepresentasikan dirimu.',
            icon: 'bi-fire'
        }
    ];
});

const next = () => {
    if (activeSlides.value.length === 0) return;
    currentIndex.value = (currentIndex.value + 1) % activeSlides.value.length;
};

const prev = () => {
    if (activeSlides.value.length === 0) return;
    currentIndex.value =
        (currentIndex.value - 1 + activeSlides.value.length) % activeSlides.value.length;
};

onMounted(() => {
    interval = setInterval(next, 6000); // 6 seconds auto cycle
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>

<style scoped>
.pattern-wave {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='60' viewBox='0 0 120 60'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.08' d='M0 30 Q 15 15 30 30 T 60 30 T 90 30 T 120 30'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.6' opacity='0.06' d='M0 40 Q 15 25 30 40 T 60 40 T 90 40 T 120 40'/%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.04' d='M0 50 Q 15 35 30 50 T 60 50 T 90 50 T 120 50'/%3E%3C/svg%3E");
    background-size: 120px 60px;
}

.vertical-text {
    writing-mode: vertical-rl;
    text-orientation: mixed;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.carousel-slide {
    transition: all 1s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
