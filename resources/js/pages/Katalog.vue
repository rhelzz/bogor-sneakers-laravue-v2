<template>
    <div class="font-body min-h-screen bg-washi text-sumi antialiased">
        <FloatingMenuNav current-page="katalog" />
        <FloatingAdminPanel :contacts="contacts" />
        <FloatingOrderPanel :orders="orders" />

        <section
            class="flex h-[calc(100vh-64px)] flex-col overflow-hidden bg-washi"
        >
            <header
                class="flex flex-wrap items-end gap-4 border-b border-sumi/10 bg-washi px-4 py-4 sm:px-6"
            >
                <div>
                    <h1
                        class="text-[30px] leading-none font-bold tracking-tight sm:text-[36px]"
                    >
                        Katalog
                    </h1>
                    <p class="mt-2 text-xs tracking-[0.03em] text-hai">
                        Menampilkan
                        <strong class="text-sumi">{{ shownCount }}</strong>
                        dari
                        <strong class="text-sumi">{{ filteredCount }}</strong>
                        produk
                    </p>
                </div>

                <div class="relative min-w-55 flex-1 sm:min-w-[320px]">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama / brand / kode..."
                        autocomplete="off"
                        spellcheck="false"
                        class="w-full rounded-xl border border-sumi/15 bg-shironeri px-3 py-2.5 pr-10 text-xs tracking-[0.04em] text-sumi transition outline-none focus:border-matcha"
                    />
                    <span
                        class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-hai"
                    >
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </span>
                </div>

                <div
                    class="flex w-full items-center justify-between gap-2 sm:w-auto sm:justify-end"
                >
                    <select
                        v-model="sortMode"
                        class="min-w-42.5 flex-1 rounded-xl border border-sumi/15 bg-shironeri px-3 py-2.5 text-xs tracking-[0.05em] text-sumi transition outline-none focus:border-matcha sm:flex-none"
                    >
                        <option value="newest">Terbaru</option>
                        <option value="price-asc">
                            Harga: Rendah ke Tinggi
                        </option>
                        <option value="price-desc">
                            Harga: Tinggi ke Rendah
                        </option>
                        <option value="popular">Terpopuler</option>
                        <option value="az">A ke Z</option>
                    </select>

                    <div
                        class="flex overflow-hidden rounded-xl border border-sumi/15 bg-shironeri"
                    >
                        <button
                            class="h-9 w-9 text-hai transition hover:text-sumi"
                            :class="
                                viewMode === '3col'
                                    ? 'bg-sumi text-washi hover:text-washi'
                                    : ''
                            "
                            title="3 kolom"
                            @click="setViewMode('3col')"
                        >
                            <svg
                                width="13"
                                height="13"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="mx-auto"
                            >
                                <rect
                                    x="3"
                                    y="3"
                                    width="5"
                                    height="18"
                                    rx="1"
                                />
                                <rect
                                    x="10"
                                    y="3"
                                    width="5"
                                    height="18"
                                    rx="1"
                                />
                                <rect
                                    x="17"
                                    y="3"
                                    width="5"
                                    height="18"
                                    rx="1"
                                />
                            </svg>
                        </button>
                        <button
                            class="h-9 w-9 text-hai transition hover:text-sumi"
                            :class="
                                viewMode === '4col'
                                    ? 'bg-sumi text-washi hover:text-washi'
                                    : ''
                            "
                            title="4 kolom"
                            @click="setViewMode('4col')"
                        >
                            <svg
                                width="13"
                                height="13"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="mx-auto"
                            >
                                <rect
                                    x="2"
                                    y="3"
                                    width="4"
                                    height="18"
                                    rx="1"
                                />
                                <rect
                                    x="7.5"
                                    y="3"
                                    width="4"
                                    height="18"
                                    rx="1"
                                />
                                <rect
                                    x="13"
                                    y="3"
                                    width="4"
                                    height="18"
                                    rx="1"
                                />
                                <rect
                                    x="18.5"
                                    y="3"
                                    width="4"
                                    height="18"
                                    rx="1"
                                />
                            </svg>
                        </button>
                        <button
                            class="h-9 w-9 text-hai transition hover:text-sumi"
                            :class="
                                viewMode === 'list'
                                    ? 'bg-sumi text-washi hover:text-washi'
                                    : ''
                            "
                            title="List"
                            @click="setViewMode('list')"
                        >
                            <svg
                                width="13"
                                height="13"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                class="mx-auto"
                            >
                                <line x1="8" y1="6" x2="21" y2="6" />
                                <line x1="8" y1="12" x2="21" y2="12" />
                                <line x1="8" y1="18" x2="21" y2="18" />
                                <line x1="3" y1="6" x2="3.01" y2="6" />
                                <line x1="3" y1="12" x2="3.01" y2="12" />
                                <line x1="3" y1="18" x2="3.01" y2="18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </header>

            <div class="flex flex-1 overflow-hidden">
                <aside
                    class="fixed top-32 left-0 h-[calc(100vh-128px)] w-70 overflow-y-auto border-r border-sumi/10 bg-shironeri p-4 sm:p-5"
                >
                    <div class="space-y-4">
                        <div class="border-b border-sumi/10 pb-4">
                            <div
                                class="mb-3 flex items-center justify-between text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                <span>Brand</span>
                                <button
                                    class="text-[10px] tracking-[0.08em] underline hover:text-sumi"
                                    @click="resetBrandFilter"
                                >
                                    Reset
                                </button>
                            </div>
                            <div class="space-y-1">
                                <button
                                    v-for="brand in brandFilters"
                                    :key="brand.key"
                                    class="flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left transition"
                                    :class="
                                        selectedBrands.includes(brand.key)
                                            ? 'bg-matcha/15'
                                            : 'hover:bg-sumi/5'
                                    "
                                    @click="toggleBrand(brand.key)"
                                >
                                    <span class="flex items-center gap-2">
                                        <span
                                            class="flex h-3.5 w-3.5 items-center justify-center rounded-[3px] border"
                                            :class="
                                                selectedBrands.includes(
                                                    brand.key,
                                                )
                                                    ? 'border-matcha bg-matcha text-washi'
                                                    : 'border-sumi/30'
                                            "
                                        >
                                            <i
                                                v-if="
                                                    selectedBrands.includes(
                                                        brand.key,
                                                    )
                                                "
                                                class="bi bi-check text-[10px]"
                                            />
                                        </span>
                                        <span
                                            class="text-xs tracking-[0.03em]"
                                            >{{ brand.label }}</span
                                        >
                                    </span>
                                    <span class="text-[11px] text-hai">{{
                                        brandCount[brand.key] ?? 0
                                    }}</span>
                                </button>
                            </div>
                        </div>

                        <div class="border-b border-sumi/10 pb-4">
                            <div
                                class="mb-3 flex items-center justify-between text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                <span>Ukuran (EU)</span>
                                <button
                                    class="text-[10px] tracking-[0.08em] underline hover:text-sumi"
                                    @click="resetSizeFilter"
                                >
                                    Reset
                                </button>
                            </div>
                            <div class="grid grid-cols-4 gap-1.5">
                                <button
                                    v-for="size in availableSizes"
                                    :key="size"
                                    class="rounded-md border px-1 py-1.5 text-xs transition"
                                    :class="
                                        selectedSizes.includes(size)
                                            ? 'border-sumi bg-sumi text-washi'
                                            : 'border-sumi/15 bg-shironeri text-usuzumi hover:border-matcha hover:text-sumi'
                                    "
                                    @click="toggleSize(size)"
                                >
                                    {{ size }}
                                </button>
                            </div>
                        </div>

                        <div class="border-b border-sumi/10 pb-4">
                            <p
                                class="mb-3 text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                Harga
                            </p>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model.number="priceMin"
                                    type="number"
                                    min="0"
                                    :max="MAX_PRICE"
                                    placeholder="Min"
                                    class="w-full rounded-md border border-sumi/15 bg-shironeri px-2 py-1.5 text-xs outline-none focus:border-matcha"
                                />
                                <span class="text-xs text-hai">-</span>
                                <input
                                    v-model.number="priceMax"
                                    type="number"
                                    min="0"
                                    :max="MAX_PRICE"
                                    placeholder="Maks"
                                    class="w-full rounded-md border border-sumi/15 bg-shironeri px-2 py-1.5 text-xs outline-none focus:border-matcha"
                                />
                            </div>
                            <input
                                v-model.number="priceMax"
                                type="range"
                                min="0"
                                :max="MAX_PRICE"
                                step="50000"
                                class="mt-3 w-full accent-matcha"
                            />
                        </div>

                        <div class="border-b border-sumi/10 pb-4">
                            <p
                                class="mb-3 text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                Status Stok
                            </p>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="status in statusFilters"
                                    :key="status.key"
                                    class="rounded-full border px-2.5 py-1 text-[10px] tracking-[0.08em] uppercase transition"
                                    :class="statusButtonClass(status.key)"
                                    @click="selectedStatus = status.key"
                                >
                                    {{ status.label }}
                                </button>
                            </div>
                        </div>

                        <div class="border-b border-sumi/10 pb-4">
                            <div
                                class="mb-3 flex items-center justify-between text-[11px] tracking-[0.14em] text-hai uppercase"
                            >
                                <span>Koleksi</span>
                                <button
                                    class="text-[10px] tracking-[0.08em] underline hover:text-sumi"
                                    @click="resetCollectionFilter"
                                >
                                    Reset
                                </button>
                            </div>
                            <div class="space-y-1">
                                <button
                                    v-for="collection in collectionFilters"
                                    :key="collection.key"
                                    class="flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left transition"
                                    :class="
                                        selectedCollections.includes(
                                            collection.key,
                                        )
                                            ? 'bg-matcha/15'
                                            : 'hover:bg-sumi/5'
                                    "
                                    @click="toggleCollection(collection.key)"
                                >
                                    <span class="flex items-center gap-2">
                                        <span
                                            class="flex h-3.5 w-3.5 items-center justify-center rounded-[3px] border"
                                            :class="
                                                selectedCollections.includes(
                                                    collection.key,
                                                )
                                                    ? 'border-matcha bg-matcha text-washi'
                                                    : 'border-sumi/30'
                                            "
                                        >
                                            <i
                                                v-if="
                                                    selectedCollections.includes(
                                                        collection.key,
                                                    )
                                                "
                                                class="bi bi-check text-[10px]"
                                            />
                                        </span>
                                        <span
                                            class="text-xs tracking-[0.03em]"
                                            >{{ collection.label }}</span
                                        >
                                    </span>
                                    <span class="text-[11px] text-hai">{{
                                        collectionCount[collection.key] ?? 0
                                    }}</span>
                                </button>
                            </div>
                        </div>

                        <button
                            class="w-full rounded-full bg-sumi px-3 py-2.5 text-xs tracking-[0.12em] text-washi uppercase transition hover:opacity-85"
                            @click="showApplyFeedback"
                        >
                            {{ applyButtonLabel }}
                        </button>
                    </div>
                </aside>

                <main class="ml-70 flex-1 overflow-y-auto p-4 sm:p-5">
                    <div class="mb-4 flex flex-wrap items-center gap-2">
                        <span
                            class="text-[11px] tracking-widest text-hai uppercase"
                            >Filter:</span
                        >
                        <span
                            v-for="chip in activeFilterChips"
                            :key="chip.key"
                            class="inline-flex items-center gap-1.5 rounded-full border border-sumi/15 bg-shironeri px-2 py-1 text-[11px] text-sumi"
                        >
                            {{ chip.label }}
                            <button
                                class="flex h-4 w-4 items-center justify-center rounded-full bg-sumi/10 text-[10px] text-usuzumi transition hover:bg-sumi/25 hover:text-washi"
                                :aria-label="`Hapus ${chip.label}`"
                                @click="removeFilterChip(chip)"
                            >
                                <i class="bi bi-x" />
                            </button>
                        </span>
                        <button
                            class="ml-auto text-[11px] text-hai underline disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="activeFilterChips.length === 0"
                            @click="clearAllFilters"
                        >
                            Hapus semua
                        </button>
                    </div>

                    <div class="mb-4 border-b border-sumi/10 pb-3">
                        <p class="text-xs tracking-[0.04em] text-hai">
                            Menampilkan
                            <strong class="text-sumi">{{ shownCount }}</strong>
                            produk · halaman
                            <strong class="text-sumi">{{ currentPage }}</strong>
                            dari
                            <strong class="text-sumi">{{ totalPages }}</strong>
                        </p>
                    </div>

                    <div :class="['grid gap-3', gridClass]">
                        <article
                            v-for="product in visibleProducts"
                            :key="product.public_id"
                            class="group overflow-hidden border border-sumi/12 bg-washi transition"
                            :class="[
                                viewMode === 'list'
                                    ? 'flex rounded-xl hover:border-sumi/30'
                                    : 'rounded-2xl hover:-translate-y-1 hover:border-sumi/25',
                                product.status === 'habis' ? 'opacity-60' : '',
                            ]"
                            @click="goToProduct(product.route_key)"
                        >
                            <div
                                class="relative overflow-hidden bg-sumi/5"
                                :class="
                                    viewMode === 'list'
                                        ? 'm-3 h-24 w-24 shrink-0 rounded-xl'
                                        : 'aspect-square w-full'
                                "
                            >
                                <img
                                    v-if="product.image_url"
                                    :src="product.image_url"
                                    :alt="product.name"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />

                                <div
                                    v-else
                                    class="absolute inset-0 flex items-center justify-center text-hai/35"
                                >
                                    <i class="bi bi-image text-3xl" />
                                </div>

                                <span
                                    class="absolute top-2 left-2 rounded px-2 py-1 text-[10px] tracking-[0.08em] uppercase"
                                    :class="statusBadgeClass(product.status)"
                                >
                                    {{ statusText(product.status) }}
                                </span>
                            </div>

                            <div
                                class="min-w-0 flex-1"
                                :class="
                                    viewMode === 'list'
                                        ? 'flex items-center gap-3 py-3 pr-3'
                                        : 'flex flex-col gap-2 p-4'
                                "
                            >
                                <div
                                    :class="
                                        viewMode === 'list'
                                            ? 'min-w-0 flex-1'
                                            : ''
                                    "
                                >
                                    <p
                                        v-if="viewMode !== 'list'"
                                        class="text-[10px] tracking-[0.11em] text-hai uppercase"
                                    >
                                        {{ product.brandLabel }} ·
                                        {{
                                            collectionLabel(product.collection)
                                        }}
                                    </p>
                                    <p
                                        class="font-bold text-sumi"
                                        :class="
                                            viewMode === 'list'
                                                ? 'truncate text-sm'
                                                : 'text-sm leading-snug'
                                        "
                                    >
                                        {{ product.name }}
                                    </p>

                                    <div
                                        v-if="viewMode !== 'list'"
                                        class="mt-1.5 flex flex-wrap gap-1"
                                    >
                                        <span
                                            v-for="size in product.sizes.slice(
                                                0,
                                                6,
                                            )"
                                            :key="`${product.id}-${size}`"
                                            class="rounded bg-sumi/6 px-1.5 py-0.5 text-[10px] text-usuzumi"
                                        >
                                            {{ size }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center gap-2"
                                    :class="
                                        viewMode === 'list'
                                            ? ''
                                            : 'mt-auto justify-between border-t border-sumi/10 pt-3'
                                    "
                                >
                                    <div>
                                        <p class="text-[15px] font-bold">
                                            {{
                                                formatCompactPrice(
                                                    product.price,
                                                )
                                            }}
                                        </p>
                                        <p class="text-[10px] text-hai">
                                            {{ priceSubtext(product.status) }}
                                        </p>
                                    </div>
                                    <button
                                        class="rounded-full bg-sumi px-3 py-1.5 text-[10px] tracking-[0.09em] text-washi uppercase transition hover:opacity-85"
                                        @click.stop="
                                            goToProduct(product.route_key)
                                        "
                                    >
                                        {{
                                            product.status === 'habis'
                                                ? 'Notif'
                                                : 'Order'
                                        }}
                                    </button>
                                </div>
                            </div>
                        </article>

                        <div
                            v-if="visibleProducts.length === 0"
                            class="col-span-full rounded-2xl border border-dashed border-sumi/20 bg-shironeri px-4 py-12 text-center"
                        >
                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl border border-sumi/15 text-hai"
                            >
                                <i class="bi bi-search" />
                            </div>
                            <p class="font-bold">Produk tidak ditemukan</p>
                            <p class="mt-1 text-sm text-hai">
                                Coba ubah filter atau kata kunci pencarian
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="filteredCount > 0"
                        class="mt-6 flex flex-col items-center gap-3"
                    >
                        <p
                            class="text-[11px] tracking-[0.09em] text-hai uppercase"
                        >
                            Menampilkan {{ shownCount }} dari
                            {{ filteredCount }} produk
                        </p>
                        <div
                            class="h-0.75 w-full max-w-md overflow-hidden rounded-full bg-sumi/10"
                        >
                            <div
                                class="h-full bg-matcha transition-all"
                                :style="{ width: `${loadProgress}%` }"
                            />
                        </div>
                        <button
                            class="rounded-full border border-sumi/20 px-5 py-2 text-xs tracking-[0.12em] uppercase transition hover:bg-sumi/5 disabled:cursor-not-allowed disabled:opacity-45"
                            :disabled="shownCount >= filteredCount"
                            @click="loadMore"
                        >
                            Muat lebih banyak
                        </button>

                        <div class="flex items-center gap-1">
                            <button
                                class="flex h-8 w-8 items-center justify-center rounded-md border border-sumi/15 text-sm text-hai transition hover:bg-sumi/5 hover:text-sumi disabled:cursor-not-allowed disabled:opacity-35"
                                :disabled="currentPage === 1"
                                @click="goToPage(currentPage - 1)"
                            >
                                ‹
                            </button>
                            <button
                                v-for="page in pageNumbers"
                                :key="page"
                                class="flex h-8 w-8 items-center justify-center rounded-md border text-xs transition"
                                :class="
                                    currentPage === page
                                        ? 'border-sumi bg-sumi text-washi'
                                        : 'border-sumi/15 text-hai hover:bg-sumi/5 hover:text-sumi'
                                "
                                @click="goToPage(page)"
                            >
                                {{ page }}
                            </button>
                            <button
                                class="flex h-8 w-8 items-center justify-center rounded-md border border-sumi/15 text-sm text-hai transition hover:bg-sumi/5 hover:text-sumi disabled:cursor-not-allowed disabled:opacity-35"
                                :disabled="currentPage === totalPages"
                                @click="goToPage(currentPage + 1)"
                            >
                                ›
                            </button>
                        </div>
                    </div>
                </main>
            </div>
        </section>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, onUnmounted, ref, watch } from 'vue';

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue';
import type { CatalogPublicItem, CatalogStatus } from '@/types/catalog';
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui';

type SortMode = 'newest' | 'price-asc' | 'price-desc' | 'popular' | 'az';
type ViewMode = '3col' | '4col' | 'list';

interface FilterChip {
    key: string;
    label: string;
    type: 'search' | 'brand' | 'size' | 'status' | 'collection' | 'price';
    value?: string | number;
}

const props = defineProps<{
    products: CatalogPublicItem[];
    maxPrice?: number;
}>();

const MAX_PRICE = Math.max(3000000, Number(props.maxPrice ?? 0));
const ITEMS_PER_PAGE = 12;

const statusFilters = [
    { key: 'all', label: 'Semua' },
    { key: 'ready', label: 'Ready' },
    { key: 'po', label: 'PO' },
    { key: 'habis', label: 'Habis' },
] as const;

const products = ref<CatalogPublicItem[]>([...props.products]);

const brandFilters = computed(() => {
    const map = new Map<string, string>();

    products.value.forEach((product) => {
        if (!map.has(product.brand)) {
            map.set(product.brand, product.brandLabel);
        }
    });

    return Array.from(map.entries())
        .map(([key, label]) => ({ key, label }))
        .sort((a, b) => a.label.localeCompare(b.label, 'id-ID'));
});

const collectionFilters = computed(() => {
    const map = new Map<string, string>();

    products.value.forEach((product) => {
        if (!map.has(product.collection)) {
            map.set(product.collection, product.collectionLabel);
        }
    });

    return Array.from(map.entries())
        .map(([key, label]) => ({ key, label }))
        .sort((a, b) => a.label.localeCompare(b.label, 'id-ID'));
});

const availableSizes = computed(() => {
    return Array.from({ length: 15 }, (_, idx) => idx + 36);
});

const contacts = ref<FloatingContact[]>([
    {
        id: 1,
        name: 'Rizky - Admin',
        role: 'PO · Order · Ketersediaan',
        phone: '6281234567890',
        initial: 'R',
        color: 'bg-matcha/20 text-matcha',
    },
    {
        id: 2,
        name: 'Farhan - CS',
        role: 'Komplain · Tracking · Retur',
        phone: '6289876543210',
        initial: 'F',
        color: 'bg-indigo/20 text-indigo',
    },
    {
        id: 3,
        name: 'Dinda - DIY',
        role: 'Kustom · Desain · Konsultasi',
        phone: '6285511223344',
        initial: 'D',
        color: 'bg-sakura/30 text-sakura',
    },
]);

const orders = ref<FloatingOrder[]>([
    {
        id: '#BGS-2841',
        product: 'Nike Air Max 97 Silver',
        size: '42',
        status: 'Produksi',
        statusClass:
            'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700',
        progress: 55,
        progressClass: 'bg-sumi',
    },
    {
        id: '#BGS-2790',
        product: 'Adidas Samba OG White',
        size: '40',
        status: 'Dikirim',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700',
        progress: 85,
        progressClass: 'bg-sumi',
    },
    {
        id: '#BGS-2755',
        product: 'Jordan 1 Retro High Bred',
        size: '43',
        status: 'Selesai',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha',
        progress: 100,
        progressClass: 'bg-matcha',
    },
    {
        id: '#BGS-2870',
        product: 'NB 574 Navy',
        size: '41',
        status: 'Menunggu',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-sumi/10 text-hai',
        progress: 10,
        progressClass: 'bg-hai/50',
    },
]);

const searchQuery = ref('');
const sortMode = ref<SortMode>('newest');
const viewMode = ref<ViewMode>('3col');
const selectedStatus = ref<(typeof statusFilters)[number]['key']>('all');

const selectedBrands = ref<string[]>([]);
const selectedSizes = ref<number[]>([]);
const selectedCollections = ref<string[]>([]);

const priceMin = ref(0);
const priceMax = ref(MAX_PRICE);

const currentPage = ref(1);
const applyButtonLabel = ref('Terapkan Filter');

let applyFeedbackTimeout: ReturnType<typeof setTimeout> | undefined;

const brandCount = computed<Record<string, number>>(() => {
    return products.value.reduce(
        (acc, product) => {
            acc[product.brand] = (acc[product.brand] ?? 0) + 1;

            return acc;
        },
        {} as Record<string, number>,
    );
});

const collectionCount = computed<Record<string, number>>(() => {
    return products.value.reduce(
        (acc, product) => {
            acc[product.collection] = (acc[product.collection] ?? 0) + 1;

            return acc;
        },
        {} as Record<string, number>,
    );
});

const filteredProducts = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();

    return products.value.filter((product) => {
        const matchesQuery =
            query.length === 0 ||
            product.name.toLowerCase().includes(query) ||
            product.brandLabel.toLowerCase().includes(query) ||
            product.code.toLowerCase().includes(query);

        const matchesBrand =
            selectedBrands.value.length === 0 ||
            selectedBrands.value.includes(product.brand);

        const matchesSize =
            selectedSizes.value.length === 0 ||
            selectedSizes.value.some((size) => product.sizes.includes(size));

        const matchesCollection =
            selectedCollections.value.length === 0 ||
            selectedCollections.value.includes(product.collection);

        const matchesStatus =
            selectedStatus.value === 'all' ||
            product.status === selectedStatus.value;

        const matchesPrice =
            product.price >= priceMin.value && product.price <= priceMax.value;

        return (
            matchesQuery &&
            matchesBrand &&
            matchesSize &&
            matchesCollection &&
            matchesStatus &&
            matchesPrice
        );
    });
});

const sortedProducts = computed(() => {
    const items = [...filteredProducts.value];

    const createdAtRank = (value: string | null) => {
        if (!value) {
            return 0;
        }

        const timestamp = Date.parse(value);

        if (Number.isNaN(timestamp)) {
            return 0;
        }

        return timestamp;
    };

    switch (sortMode.value) {
        case 'price-asc':
            return items.sort((a, b) => a.price - b.price);
        case 'price-desc':
            return items.sort((a, b) => b.price - a.price);
        case 'popular':
            return items.sort((a, b) => b.popularity - a.popularity);
        case 'az':
            return items.sort((a, b) => a.name.localeCompare(b.name, 'id-ID'));
        default:
            return items.sort((a, b) => {
                const compareByDate =
                    createdAtRank(b.created_at) - createdAtRank(a.created_at);

                if (compareByDate !== 0) {
                    return compareByDate;
                }

                return b.id - a.id;
            });
    }
});

const totalPages = computed(() =>
    Math.max(1, Math.ceil(sortedProducts.value.length / ITEMS_PER_PAGE)),
);

const shownCount = computed(() => {
    const limit = currentPage.value * ITEMS_PER_PAGE;

    return Math.min(limit, sortedProducts.value.length);
});

const filteredCount = computed(() => sortedProducts.value.length);

const visibleProducts = computed(() => {
    return sortedProducts.value.slice(0, shownCount.value);
});

const pageNumbers = computed(() =>
    Array.from({ length: totalPages.value }, (_, idx) => idx + 1),
);

const loadProgress = computed(() => {
    if (filteredCount.value === 0) {
        return 0;
    }

    return (shownCount.value / filteredCount.value) * 100;
});

const gridClass = computed(() => {
    if (viewMode.value === 'list') {
        return 'grid-cols-1';
    }

    if (viewMode.value === '4col') {
        return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4';
    }

    return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3';
});

const activeFilterChips = computed<FilterChip[]>(() => {
    const chips: FilterChip[] = [];

    if (searchQuery.value.trim().length > 0) {
        chips.push({
            key: 'search',
            label: `Cari: ${searchQuery.value.trim()}`,
            type: 'search',
        });
    }

    selectedBrands.value.forEach((brand) => {
        const label =
            brandFilters.value.find((item) => item.key === brand)?.label ??
            brand;
        chips.push({
            key: `brand-${brand}`,
            label,
            type: 'brand',
            value: brand,
        });
    });

    selectedSizes.value.forEach((size) => {
        chips.push({
            key: `size-${size}`,
            label: `Sz. ${size}`,
            type: 'size',
            value: size,
        });
    });

    selectedCollections.value.forEach((collection) => {
        const label =
            collectionFilters.value.find((item) => item.key === collection)
                ?.label ?? collection;
        chips.push({
            key: `collection-${collection}`,
            label,
            type: 'collection',
            value: collection,
        });
    });

    if (selectedStatus.value !== 'all') {
        const label =
            statusFilters.find((item) => item.key === selectedStatus.value)
                ?.label ?? selectedStatus.value;
        chips.push({
            key: `status-${selectedStatus.value}`,
            label,
            type: 'status',
            value: selectedStatus.value,
        });
    }

    if (priceMin.value > 0 || priceMax.value < MAX_PRICE) {
        chips.push({
            key: 'price',
            label: `${formatCompactPrice(priceMin.value)} - ${formatCompactPrice(priceMax.value)}`,
            type: 'price',
        });
    }

    return chips;
});

watch([priceMin, priceMax], () => {
    const safeMin = normalizePrice(priceMin.value);
    const safeMax = normalizePrice(priceMax.value);

    if (safeMin !== priceMin.value) {
        priceMin.value = safeMin;
    }

    if (safeMax !== priceMax.value) {
        priceMax.value = safeMax;
    }

    if (priceMin.value > priceMax.value) {
        priceMax.value = priceMin.value;
    }
});

watch(
    [
        searchQuery,
        sortMode,
        selectedStatus,
        selectedBrands,
        selectedSizes,
        selectedCollections,
        priceMin,
        priceMax,
    ],
    () => {
        currentPage.value = 1;
    },
    { deep: true },
);

watch(sortedProducts, () => {
    if (currentPage.value > totalPages.value) {
        currentPage.value = totalPages.value;
    }
});

const setViewMode = (mode: ViewMode) => {
    viewMode.value = mode;
};

const toggleBrand = (brand: string) => {
    if (selectedBrands.value.includes(brand)) {
        selectedBrands.value = selectedBrands.value.filter(
            (item) => item !== brand,
        );

        return;
    }

    selectedBrands.value = [...selectedBrands.value, brand];
};

const toggleCollection = (collection: string) => {
    if (selectedCollections.value.includes(collection)) {
        selectedCollections.value = selectedCollections.value.filter(
            (item) => item !== collection,
        );

        return;
    }

    selectedCollections.value = [...selectedCollections.value, collection];
};

const toggleSize = (size: number) => {
    if (selectedSizes.value.includes(size)) {
        selectedSizes.value = selectedSizes.value.filter(
            (item) => item !== size,
        );

        return;
    }

    selectedSizes.value = [...selectedSizes.value, size];
};

const resetBrandFilter = () => {
    selectedBrands.value = [];
};

const resetSizeFilter = () => {
    selectedSizes.value = [];
};

const resetCollectionFilter = () => {
    selectedCollections.value = [];
};

const showApplyFeedback = () => {
    applyButtonLabel.value = 'Diterapkan';

    if (applyFeedbackTimeout !== undefined) {
        clearTimeout(applyFeedbackTimeout);
    }

    applyFeedbackTimeout = setTimeout(() => {
        applyButtonLabel.value = 'Terapkan Filter';
    }, 1200);
};

const goToPage = (page: number) => {
    const target = Math.min(Math.max(page, 1), totalPages.value);
    currentPage.value = target;
};

const loadMore = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value += 1;
    }
};

const clearAllFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = 'all';
    selectedBrands.value = [];
    selectedSizes.value = [];
    selectedCollections.value = [];
    priceMin.value = 0;
    priceMax.value = MAX_PRICE;
};

const removeFilterChip = (chip: FilterChip) => {
    if (chip.type === 'search') {
        searchQuery.value = '';

        return;
    }

    if (chip.type === 'brand' && typeof chip.value === 'string') {
        selectedBrands.value = selectedBrands.value.filter(
            (item) => item !== chip.value,
        );

        return;
    }

    if (chip.type === 'size' && typeof chip.value === 'number') {
        selectedSizes.value = selectedSizes.value.filter(
            (item) => item !== chip.value,
        );

        return;
    }

    if (chip.type === 'collection' && typeof chip.value === 'string') {
        selectedCollections.value = selectedCollections.value.filter(
            (item) => item !== chip.value,
        );

        return;
    }

    if (chip.type === 'status') {
        selectedStatus.value = 'all';

        return;
    }

    if (chip.type === 'price') {
        priceMin.value = 0;
        priceMax.value = MAX_PRICE;
    }
};

const goToProduct = (routeKey: string) => {
    router.visit(`/katalog/${routeKey}`);
};

const statusText = (status: CatalogStatus) => {
    if (status === 'po') {
        return 'PO';
    }

    if (status === 'ready') {
        return 'Ready';
    }

    return 'Habis';
};

const statusBadgeClass = (status: CatalogStatus) => {
    if (status === 'po' || status === 'ready') {
        return 'bg-matcha text-washi shadow-sm';
    }

    return 'bg-sumi text-washi shadow-sm';
};

const statusButtonClass = (status: (typeof statusFilters)[number]['key']) => {
    if (selectedStatus.value !== status) {
        return 'border-sumi/15 bg-shironeri text-hai hover:border-sumi/30 hover:text-sumi';
    }

    if (status === 'po' || status === 'ready') {
        return 'border-matcha/50 bg-matcha/15 text-matcha';
    }

    if (status === 'habis') {
        return 'border-sumi/30 bg-sumi/5 text-hai';
    }

    return 'border-sumi bg-sumi text-washi';
};

const collectionLabel = (collection: string) => {
    return (
        collectionFilters.value.find((item) => item.key === collection)
            ?.label ?? collection
    );
};

const priceSubtext = (status: CatalogStatus) => {
    if (status === 'po') {
        return 'Pre-order · 14-21 hari';
    }

    if (status === 'ready') {
        return 'Stok tersedia';
    }

    return 'Stok habis · Notif saya';
};

const formatCompactPrice = (price: number) => {
    const inThousands = Math.round(price / 1000);

    return `Rp ${inThousands.toLocaleString('id-ID')}K`;
};

const normalizePrice = (value: number) => {
    if (!Number.isFinite(value)) {
        return 0;
    }

    return Math.min(Math.max(Math.floor(value), 0), MAX_PRICE);
};

onUnmounted(() => {
    if (applyFeedbackTimeout !== undefined) {
        clearTimeout(applyFeedbackTimeout);
    }
});
</script>
