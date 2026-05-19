<template>
    <section id="katalog" class="pattern-hemp bg-washi px-6 py-16 sm:py-24 lg:px-16">
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-6 border-b border-sumi/10 pb-8 animate-slide-up">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-matcha"></span>
                        <span class="text-[10px] font-bold tracking-widest text-hai uppercase">OUR LATEST DROPS</span>
                    </div>
                    <h2 class="font-heading text-4xl font-black tracking-tight text-sumi uppercase">
                        Koleksi<br />
                        <span class="text-matcha">Terbaru</span>
                    </h2>
                </div>
                <p class="max-w-xs text-xs sm:text-sm text-hai font-sans md:text-right">
                    Eksplorasi kurasi sneaker pilihan di Bogor. Mulai dari rilisan terbatas, restock terpopuler, hingga kolaborasi eksklusif.
                </p>
            </div>

            <!-- Filter Tabs - Horizontal Swipe Track on Mobile -->
            <div class="mb-10 -mx-6 px-6 overflow-x-auto flex flex-nowrap md:flex-wrap md:justify-center items-center gap-2.5 no-scrollbar scroll-smooth">
                <button
                    v-for="filter in productFilters"
                    :key="filter"
                    @click="produktFilter = filter"
                    :class="[
                        'shrink-0 rounded-full px-5 py-2.5 text-xs font-bold tracking-widest uppercase transition-all duration-300 border',
                        produktFilter === filter
                            ? 'bg-sumi text-washi border-sumi scale-[1.02] shadow-md'
                            : 'bg-washi text-hai border-sumi/10 hover:border-sumi/30 hover:bg-sumi/[0.02]',
                    ]"
                >
                    {{ toBrandLabel(filter) }}
                </button>
            </div>

            <!-- Product Grid - 2 Column on Mobile! -->
            <div
                class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:grid-cols-4"
                id="katalogGrid"
            >
                <div
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="group relative flex flex-col justify-between overflow-hidden rounded-3xl border border-sumi/5 bg-shironeri p-2 sm:p-3 card-lift"
                >
                    <!-- Image Container with Zoom effect -->
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-sumi/5">
                        <img
                            v-if="product.image_url"
                            :src="product.image_url"
                            :alt="product.name"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110"
                            loading="lazy"
                            decoding="async"
                        />
                        <div
                            v-else
                            class="absolute inset-0 flex items-center justify-center bg-sumi/5 text-hai/30"
                        >
                            <i class="bi bi-image text-3xl"></i>
                        </div>

                        <!-- Status Badge -->
                        <span
                            class="absolute top-2.5 left-2.5 rounded-full px-2.5 py-1 text-[8px] sm:text-[9px] font-black tracking-wider uppercase border shadow-sm backdrop-blur-xs"
                            :class="[
                                product.status === 'Ready' 
                                    ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' 
                                    : product.status === 'PO' 
                                        ? 'bg-amber-500/10 text-amber-600 border-amber-500/20' 
                                        : 'bg-rose-500/10 text-rose-500 border-rose-500/20'
                            ]"
                        >
                            {{ product.status === 'Habis' ? 'Sold Out' : product.status }}
                        </span>
                    </div>

                    <!-- Info Details -->
                    <div class="p-3 sm:p-4 flex flex-col flex-1 justify-between">
                        <div>
                            <!-- Brand -->
                            <span class="text-[9px] sm:text-[10px] font-bold text-matcha tracking-widest uppercase block mb-1">
                                {{ product.brand_label || product.brand }}
                            </span>
                            
                            <!-- Product Name -->
                            <h4 
                                class="font-heading font-black text-sumi text-xs sm:text-sm tracking-wide uppercase line-clamp-2 min-h-[32px] sm:min-h-[40px] transition-colors group-hover:text-matcha"
                                :class="product.status === 'Habis' ? 'text-hai/60' : ''"
                            >
                                {{ product.name }}
                            </h4>

                            <!-- Size -->
                            <p class="mt-1 text-[10px] text-hai font-sans">
                                Size EU: <span class="font-bold text-sumi">{{ product.size }}</span>
                            </p>
                        </div>

                        <!-- Price & Action Row -->
                        <div class="flex items-center justify-between mt-4 border-t border-sumi/5 pt-3">
                            <p 
                                class="text-sm sm:text-base font-black text-sumi font-sans"
                                :class="product.status === 'Habis' ? 'text-hai/50 line-through' : ''"
                            >
                                {{ formatCompactPrice(product.price) }}
                            </p>
                            
                            <a
                                href="/katalog" 
                                class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-full bg-sumi text-washi transition-all duration-300 hover:bg-matcha hover:scale-[1.05] active:scale-[0.95] disabled:cursor-not-allowed disabled:bg-sumi/10 disabled:text-hai/40"
                            >
                                <i class="bi bi-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

import type { HomeLatestCollectionItem } from '@/types/home';

const props = defineProps<{
    latestCollections: HomeLatestCollectionItem[];
}>();

const produktFilter = ref('all');

const productBrandLabels = computed(() => {
    const map = new Map<string, string>();
    props.latestCollections.forEach((product) => {
        if (!map.has(product.brand)) {
            map.set(product.brand, product.brand_label);
        }
    });
    return map;
});

const productFilters = computed(() => {
    const brands = Array.from(
        new Set(
            props.latestCollections
                .map((product) => product.brand)
                .filter(Boolean),
        ),
    );
    return ['all', ...brands];
});

const toBrandLabel = (brandKey: string) => {
    if (brandKey === 'all') return 'Semua';
    return (
        productBrandLabels.value.get(brandKey) ??
        brandKey.charAt(0).toUpperCase() + brandKey.slice(1)
    );
};

const filteredProducts = computed(() => {
    return props.latestCollections.filter((product) => {
        return (
            produktFilter.value === 'all' ||
            product.brand === produktFilter.value
        );
    });
});

const formatCompactPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(price);
};
</script>

<style scoped>
.pattern-hemp {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cg fill='none' stroke='%231A1A1A' stroke-width='0.7' opacity='0.14'%3E%3Cpath d='M30 0 L15 15 M30 0 L45 15 M0 30 L15 15 M0 30 L15 45 M60 30 L45 15 M60 30 L45 45 M30 60 L15 45 M30 60 L45 45'/%3E%3Crect x='20' y='20' width='20' height='20'/%3E%3C/g%3E%3C/svg%3E");
    background-size: 60px 60px;
}

.card-lift {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow:
        0 2px 8px rgba(26, 26, 26, 0.03),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.card-lift:hover {
    transform: translateY(-6px);
    box-shadow:
        0 16px 32px rgba(26, 26, 26, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
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
</style>
