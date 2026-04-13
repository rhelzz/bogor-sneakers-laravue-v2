<template>
  <div class="min-h-screen bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="katalog" />
    <FloatingAdminPanel :contacts="contacts" />
    <FloatingOrderPanel :orders="orders" />

    <section class="h-[calc(100vh-64px)] overflow-hidden bg-washi flex flex-col">
      <header class="flex flex-wrap items-end gap-4 border-b border-sumi/10 bg-washi px-4 py-4 sm:px-6">
        <div>
          <p class="mb-1 text-[11px] uppercase tracking-[0.16em] text-hai">Store ID BGS-001 · Bogor, IDN</p>
          <h1 class="text-[30px] font-bold leading-none tracking-tight sm:text-[36px]">Katalog</h1>
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
            class="w-full rounded-xl border border-sumi/15 bg-shironeri px-3 py-2.5 pr-10 text-xs tracking-[0.04em] text-sumi outline-none transition focus:border-matcha"
          >
          <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-hai">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
          </span>
        </div>

        <div class="flex w-full items-center justify-between gap-2 sm:w-auto sm:justify-end">
          <select
            v-model="sortMode"
            class="min-w-42.5 flex-1 rounded-xl border border-sumi/15 bg-shironeri px-3 py-2.5 text-xs tracking-[0.05em] text-sumi outline-none transition focus:border-matcha sm:flex-none"
          >
            <option value="newest">Terbaru</option>
            <option value="price-asc">Harga: Rendah ke Tinggi</option>
            <option value="price-desc">Harga: Tinggi ke Rendah</option>
            <option value="popular">Terpopuler</option>
            <option value="az">A ke Z</option>
          </select>

          <div class="flex overflow-hidden rounded-xl border border-sumi/15 bg-shironeri">
            <button
              class="h-9 w-9 text-hai transition hover:text-sumi"
              :class="viewMode === '3col' ? 'bg-sumi text-washi hover:text-washi' : ''"
              title="3 kolom"
              @click="setViewMode('3col')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto">
                <rect x="3" y="3" width="5" height="18" rx="1" />
                <rect x="10" y="3" width="5" height="18" rx="1" />
                <rect x="17" y="3" width="5" height="18" rx="1" />
              </svg>
            </button>
            <button
              class="h-9 w-9 text-hai transition hover:text-sumi"
              :class="viewMode === '4col' ? 'bg-sumi text-washi hover:text-washi' : ''"
              title="4 kolom"
              @click="setViewMode('4col')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mx-auto">
                <rect x="2" y="3" width="4" height="18" rx="1" />
                <rect x="7.5" y="3" width="4" height="18" rx="1" />
                <rect x="13" y="3" width="4" height="18" rx="1" />
                <rect x="18.5" y="3" width="4" height="18" rx="1" />
              </svg>
            </button>
            <button
              class="h-9 w-9 text-hai transition hover:text-sumi"
              :class="viewMode === 'list' ? 'bg-sumi text-washi hover:text-washi' : ''"
              title="List"
              @click="setViewMode('list')"
            >
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="mx-auto">
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
        <aside class="fixed left-0 top-32 w-70 h-[calc(100vh-128px)] border-r border-sumi/10 bg-shironeri overflow-y-auto p-4 sm:p-5">
          <div class="space-y-4">
              <div class="border-b border-sumi/10 pb-4">
                <div class="mb-3 flex items-center justify-between text-[11px] uppercase tracking-[0.14em] text-hai">
                  <span>Brand</span>
                  <button class="text-[10px] tracking-[0.08em] underline hover:text-sumi" @click="resetBrandFilter">Reset</button>
                </div>
                <div class="space-y-1">
                  <button
                    v-for="brand in brandFilters"
                    :key="brand.key"
                    class="flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left transition"
                    :class="selectedBrands.includes(brand.key) ? 'bg-matcha/15' : 'hover:bg-sumi/5'"
                    @click="toggleBrand(brand.key)"
                  >
                    <span class="flex items-center gap-2">
                      <span
                        class="flex h-3.5 w-3.5 items-center justify-center rounded-[3px] border"
                        :class="selectedBrands.includes(brand.key) ? 'border-matcha bg-matcha text-washi' : 'border-sumi/30'"
                      >
                        <i v-if="selectedBrands.includes(brand.key)" class="bi bi-check text-[10px]" />
                      </span>
                      <span class="text-xs tracking-[0.03em]">{{ brand.label }}</span>
                    </span>
                    <span class="text-[11px] text-hai">{{ brandCount[brand.key] ?? 0 }}</span>
                  </button>
                </div>
              </div>

              <div class="border-b border-sumi/10 pb-4">
                <div class="mb-3 flex items-center justify-between text-[11px] uppercase tracking-[0.14em] text-hai">
                  <span>Ukuran (EU)</span>
                  <button class="text-[10px] tracking-[0.08em] underline hover:text-sumi" @click="resetSizeFilter">Reset</button>
                </div>
                <div class="grid grid-cols-4 gap-1.5">
                  <button
                    v-for="size in availableSizes"
                    :key="size"
                    class="rounded-md border px-1 py-1.5 text-xs transition"
                    :class="selectedSizes.includes(size) ? 'border-sumi bg-sumi text-washi' : 'border-sumi/15 bg-shironeri text-usuzumi hover:border-matcha hover:text-sumi'"
                    @click="toggleSize(size)"
                  >
                    {{ size }}
                  </button>
                </div>
              </div>

              <div class="border-b border-sumi/10 pb-4">
                <p class="mb-3 text-[11px] uppercase tracking-[0.14em] text-hai">Harga</p>
                <div class="flex items-center gap-2">
                  <input
                    v-model.number="priceMin"
                    type="number"
                    min="0"
                    :max="MAX_PRICE"
                    placeholder="Min"
                    class="w-full rounded-md border border-sumi/15 bg-shironeri px-2 py-1.5 text-xs outline-none focus:border-matcha"
                  >
                  <span class="text-xs text-hai">-</span>
                  <input
                    v-model.number="priceMax"
                    type="number"
                    min="0"
                    :max="MAX_PRICE"
                    placeholder="Maks"
                    class="w-full rounded-md border border-sumi/15 bg-shironeri px-2 py-1.5 text-xs outline-none focus:border-matcha"
                  >
                </div>
                <input
                  v-model.number="priceMax"
                  type="range"
                  min="0"
                  :max="MAX_PRICE"
                  step="50000"
                  class="mt-3 w-full accent-matcha"
                >
              </div>

              <div class="border-b border-sumi/10 pb-4">
                <p class="mb-3 text-[11px] uppercase tracking-[0.14em] text-hai">Status Stok</p>
                <div class="flex flex-wrap gap-1.5">
                  <button
                    v-for="status in statusFilters"
                    :key="status.key"
                    class="rounded-full border px-2.5 py-1 text-[10px] uppercase tracking-[0.08em] transition"
                    :class="statusButtonClass(status.key)"
                    @click="selectedStatus = status.key"
                  >
                    {{ status.label }}
                  </button>
                </div>
              </div>

              <div class="border-b border-sumi/10 pb-4">
                <div class="mb-3 flex items-center justify-between text-[11px] uppercase tracking-[0.14em] text-hai">
                  <span>Koleksi</span>
                  <button class="text-[10px] tracking-[0.08em] underline hover:text-sumi" @click="resetCollectionFilter">Reset</button>
                </div>
                <div class="space-y-1">
                  <button
                    v-for="collection in collectionFilters"
                    :key="collection.key"
                    class="flex w-full items-center justify-between rounded-lg px-2.5 py-2 text-left transition"
                    :class="selectedCollections.includes(collection.key) ? 'bg-matcha/15' : 'hover:bg-sumi/5'"
                    @click="toggleCollection(collection.key)"
                  >
                    <span class="flex items-center gap-2">
                      <span
                        class="flex h-3.5 w-3.5 items-center justify-center rounded-[3px] border"
                        :class="selectedCollections.includes(collection.key) ? 'border-matcha bg-matcha text-washi' : 'border-sumi/30'"
                      >
                        <i v-if="selectedCollections.includes(collection.key)" class="bi bi-check text-[10px]" />
                      </span>
                      <span class="text-xs tracking-[0.03em]">{{ collection.label }}</span>
                    </span>
                    <span class="text-[11px] text-hai">{{ collectionCount[collection.key] ?? 0 }}</span>
                  </button>
                </div>
              </div>

              <button
                class="w-full rounded-full bg-sumi px-3 py-2.5 text-xs uppercase tracking-[0.12em] text-washi transition hover:opacity-85"
                @click="showApplyFeedback"
              >
                {{ applyButtonLabel }}
              </button>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-4 sm:p-5 ml-70">
            <div class="mb-4 flex flex-wrap items-center gap-2">
              <span class="text-[11px] uppercase tracking-widest text-hai">Filter:</span>
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
                :key="product.id"
                class="group overflow-hidden border border-sumi/12 bg-washi transition"
                :class="[
                  viewMode === 'list' ? 'flex rounded-xl hover:border-sumi/30' : 'rounded-2xl hover:-translate-y-1 hover:border-sumi/25',
                  product.status === 'habis' ? 'opacity-60' : '',
                ]"
                @click="goToProduct(product.id)"
              >
                <div
                  class="relative overflow-hidden bg-sumi/5"
                  :class="viewMode === 'list' ? 'm-3 h-24 w-24 shrink-0 rounded-xl' : 'aspect-square w-full'"
                >
                  <div class="absolute inset-0 flex items-center justify-center text-hai/35">
                    <i class="bi bi-image text-3xl" />
                  </div>

                  <span
                    class="absolute left-2 top-2 rounded px-2 py-1 text-[10px] uppercase tracking-[0.08em]"
                    :class="statusBadgeClass(product.status)"
                  >
                    {{ statusText(product.status) }}
                  </span>

                  <button
                    class="absolute right-2 top-2 flex h-7 w-7 items-center justify-center rounded-full border border-washi/35 bg-sumi/35 text-washi/80"
                    :class="isWishlisted(product.id) ? 'border-sakura/60 text-sakura' : 'hover:text-sakura'"
                    :aria-label="`Wishlist ${product.name}`"
                    @click.stop="toggleWishlist(product.id)"
                  >
                    <i class="bi bi-heart" />
                  </button>
                </div>

                <div
                  class="min-w-0 flex-1"
                  :class="viewMode === 'list' ? 'flex items-center gap-3 py-3 pr-3' : 'flex flex-col gap-2 p-4'"
                >
                  <div :class="viewMode === 'list' ? 'min-w-0 flex-1' : ''">
                    <p v-if="viewMode !== 'list'" class="text-[10px] uppercase tracking-[0.11em] text-hai">
                      {{ product.brandLabel }} · {{ collectionLabel(product.collection) }}
                    </p>
                    <p class="font-bold text-sumi" :class="viewMode === 'list' ? 'truncate text-sm' : 'text-sm leading-snug'">
                      {{ product.name }}
                    </p>

                    <div v-if="viewMode !== 'list'" class="mt-1.5 flex flex-wrap gap-1">
                      <span
                        v-for="size in product.sizes.slice(0, 6)"
                        :key="`${product.id}-${size}`"
                        class="rounded bg-sumi/6 px-1.5 py-0.5 text-[10px] text-usuzumi"
                      >
                        {{ size }}
                      </span>
                    </div>
                  </div>

                  <div
                    class="flex items-center gap-2"
                    :class="viewMode === 'list' ? '' : 'mt-auto justify-between border-t border-sumi/10 pt-3'"
                  >
                    <div>
                      <p class="text-[15px] font-bold">{{ formatCompactPrice(product.price) }}</p>
                      <p class="text-[10px] text-hai">{{ priceSubtext(product.status) }}</p>
                    </div>
                    <button
                      class="rounded-full bg-sumi px-3 py-1.5 text-[10px] uppercase tracking-[0.09em] text-washi transition hover:opacity-85"
                      @click.stop="goToProduct(product.id)"
                    >
                      {{ product.status === 'habis' ? 'Notif' : 'Order' }}
                    </button>
                  </div>
                </div>
              </article>

              <div
                v-if="visibleProducts.length === 0"
                class="col-span-full rounded-2xl border border-dashed border-sumi/20 bg-shironeri px-4 py-12 text-center"
              >
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl border border-sumi/15 text-hai">
                  <i class="bi bi-search" />
                </div>
                <p class="font-bold">Produk tidak ditemukan</p>
                <p class="mt-1 text-sm text-hai">Coba ubah filter atau kata kunci pencarian</p>
              </div>
            </div>

            <div v-if="filteredCount > 0" class="mt-6 flex flex-col items-center gap-3">
              <p class="text-[11px] uppercase tracking-[0.09em] text-hai">
                Menampilkan {{ shownCount }} dari {{ filteredCount }} produk
              </p>
              <div class="h-0.75 w-full max-w-md overflow-hidden rounded-full bg-sumi/10">
                <div class="h-full bg-matcha transition-all" :style="{ width: `${loadProgress}%` }" />
              </div>
              <button
                class="rounded-full border border-sumi/20 px-5 py-2 text-xs uppercase tracking-[0.12em] transition hover:bg-sumi/5 disabled:cursor-not-allowed disabled:opacity-45"
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
                  :class="currentPage === page ? 'border-sumi bg-sumi text-washi' : 'border-sumi/15 text-hai hover:bg-sumi/5 hover:text-sumi'"
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
import { router } from '@inertiajs/vue3'
import { computed, onUnmounted, ref, watch } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

type ProductStatus = 'po' | 'ready' | 'habis'
type SortMode = 'newest' | 'price-asc' | 'price-desc' | 'popular' | 'az'
type ViewMode = '3col' | '4col' | 'list'

interface CatalogProduct {
  id: number
  code: string
  name: string
  brand: string
  brandLabel: string
  collection: string
  price: number
  status: ProductStatus
  sizes: number[]
  popularity: number
  newestRank: number
}

interface FilterChip {
  key: string
  label: string
  type: 'search' | 'brand' | 'size' | 'status' | 'collection' | 'price'
  value?: string | number
}

const MAX_PRICE = 3000000
const ITEMS_PER_PAGE = 12

const statusFilters = [
  { key: 'all', label: 'Semua' },
  { key: 'ready', label: 'Ready' },
  { key: 'po', label: 'PO' },
  { key: 'habis', label: 'Habis' },
] as const

const brandFilters = [
  { key: 'nike', label: 'Nike' },
  { key: 'adidas', label: 'Adidas' },
  { key: 'jordan', label: 'Jordan' },
  { key: 'newbalance', label: 'New Balance' },
  { key: 'vans', label: 'Vans' },
  { key: 'lokal', label: 'Lokal' },
] as const

const collectionFilters = [
  { key: 'lifestyle', label: 'Lifestyle' },
  { key: 'running', label: 'Running' },
  { key: 'basketball', label: 'Basketball' },
  { key: 'skate', label: 'Skateboarding' },
] as const

const availableSizes = [38, 39, 40, 41, 42, 43, 44, 45]

const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Rizky - Admin', role: 'PO · Order · Ketersediaan', phone: '6281234567890', initial: 'R', color: 'bg-matcha/20 text-matcha' },
  { id: 2, name: 'Farhan - CS', role: 'Komplain · Tracking · Retur', phone: '6289876543210', initial: 'F', color: 'bg-indigo/20 text-indigo' },
  { id: 3, name: 'Dinda - DIY', role: 'Kustom · Desain · Konsultasi', phone: '6285511223344', initial: 'D', color: 'bg-sakura/30 text-sakura' },
])

const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
  { id: '#BGS-2755', product: 'Jordan 1 Retro High Bred', size: '43', status: 'Selesai', statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha', progress: 100, progressClass: 'bg-matcha' },
  { id: '#BGS-2870', product: 'NB 574 Navy', size: '41', status: 'Menunggu', statusClass: 'px-2 py-1 rounded text-[10px] bg-sumi/10 text-hai', progress: 10, progressClass: 'bg-hai/50' },
])

const products = ref<CatalogProduct[]>([
  { id: 1, code: 'BGS-NM97-SLV', name: 'Air Max 97 Silver Bullet', brand: 'nike', brandLabel: 'Nike', collection: 'lifestyle', price: 1850000, status: 'po', sizes: [39, 40, 41, 42, 43], popularity: 94, newestRank: 12 },
  { id: 2, code: 'BGS-SMB-WHT', name: 'Samba OG White Gum', brand: 'adidas', brandLabel: 'Adidas', collection: 'lifestyle', price: 1290000, status: 'ready', sizes: [39, 40, 41, 42], popularity: 98, newestRank: 11 },
  { id: 3, code: 'BGS-J1-BRED', name: 'Jordan 1 Retro High Bred', brand: 'jordan', brandLabel: 'Jordan', collection: 'basketball', price: 2100000, status: 'po', sizes: [40, 41, 42, 43, 44], popularity: 96, newestRank: 10 },
  { id: 4, code: 'BGS-NB574-NVY', name: 'New Balance 574 Core Navy', brand: 'newbalance', brandLabel: 'New Balance', collection: 'lifestyle', price: 980000, status: 'ready', sizes: [39, 40, 41, 42, 43, 44], popularity: 88, newestRank: 9 },
  { id: 5, code: 'BGS-DUNK-PND', name: 'Nike Dunk Low Retro Panda', brand: 'nike', brandLabel: 'Nike', collection: 'lifestyle', price: 1650000, status: 'habis', sizes: [], popularity: 91, newestRank: 8 },
  { id: 6, code: 'BGS-FRM-WHT', name: 'Adidas Forum Low White Blue', brand: 'adidas', brandLabel: 'Adidas', collection: 'lifestyle', price: 1100000, status: 'ready', sizes: [39, 40, 41, 42, 43], popularity: 83, newestRank: 7 },
  { id: 7, code: 'BGS-VNT-CLS', name: 'Ventela Classic White Low', brand: 'lokal', brandLabel: 'Ventela', collection: 'lifestyle', price: 420000, status: 'ready', sizes: [39, 40, 41, 42, 43, 44], popularity: 87, newestRank: 6 },
  { id: 8, code: 'BGS-J4-BCAT', name: 'Jordan 4 Retro Black Cat', brand: 'jordan', brandLabel: 'Jordan', collection: 'basketball', price: 2450000, status: 'po', sizes: [41, 42, 43, 44, 45], popularity: 95, newestRank: 5 },
  { id: 9, code: 'BGS-AF1-WHT', name: 'Air Force 1 Low 07 White', brand: 'nike', brandLabel: 'Nike', collection: 'lifestyle', price: 1200000, status: 'ready', sizes: [40, 41, 42, 43], popularity: 86, newestRank: 4 },
  { id: 10, code: 'BGS-VNS-OLD', name: 'Vans Old Skool Black White', brand: 'vans', brandLabel: 'Vans', collection: 'skate', price: 750000, status: 'ready', sizes: [39, 40, 41, 42, 43], popularity: 84, newestRank: 3 },
  { id: 11, code: 'BGS-PGS-41', name: 'Nike Pegasus 41 React', brand: 'nike', brandLabel: 'Nike', collection: 'running', price: 1550000, status: 'po', sizes: [40, 41, 42, 43, 44], popularity: 90, newestRank: 2 },
  { id: 12, code: 'BGS-UB22-BLK', name: 'Adidas Ultra Boost 22 Core Black', brand: 'adidas', brandLabel: 'Adidas', collection: 'running', price: 1800000, status: 'ready', sizes: [40, 41, 42, 43], popularity: 89, newestRank: 1 },
])

const searchQuery = ref('')
const sortMode = ref<SortMode>('newest')
const viewMode = ref<ViewMode>('3col')
const selectedStatus = ref<(typeof statusFilters)[number]['key']>('all')

const selectedBrands = ref<string[]>([])
const selectedSizes = ref<number[]>([])
const selectedCollections = ref<string[]>([])

const priceMin = ref(0)
const priceMax = ref(MAX_PRICE)

const currentPage = ref(1)
const applyButtonLabel = ref('Terapkan Filter')

const wishlistedIds = ref<number[]>([])

let applyFeedbackTimeout: ReturnType<typeof setTimeout> | undefined

const brandCount = computed<Record<string, number>>(() => {
  return products.value.reduce((acc, product) => {
    acc[product.brand] = (acc[product.brand] ?? 0) + 1

    return acc
  }, {} as Record<string, number>)
})

const collectionCount = computed<Record<string, number>>(() => {
  return products.value.reduce((acc, product) => {
    acc[product.collection] = (acc[product.collection] ?? 0) + 1

    return acc
  }, {} as Record<string, number>)
})

const filteredProducts = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()

  return products.value.filter(product => {
    const matchesQuery = query.length === 0
      || product.name.toLowerCase().includes(query)
      || product.brandLabel.toLowerCase().includes(query)
      || product.code.toLowerCase().includes(query)

    const matchesBrand = selectedBrands.value.length === 0
      || selectedBrands.value.includes(product.brand)

    const matchesSize = selectedSizes.value.length === 0
      || selectedSizes.value.some(size => product.sizes.includes(size))

    const matchesCollection = selectedCollections.value.length === 0
      || selectedCollections.value.includes(product.collection)

    const matchesStatus = selectedStatus.value === 'all'
      || product.status === selectedStatus.value

    const matchesPrice = product.price >= priceMin.value && product.price <= priceMax.value

    return matchesQuery && matchesBrand && matchesSize && matchesCollection && matchesStatus && matchesPrice
  })
})

const sortedProducts = computed(() => {
  const items = [...filteredProducts.value]

  switch (sortMode.value) {
    case 'price-asc':
      return items.sort((a, b) => a.price - b.price)
    case 'price-desc':
      return items.sort((a, b) => b.price - a.price)
    case 'popular':
      return items.sort((a, b) => b.popularity - a.popularity)
    case 'az':
      return items.sort((a, b) => a.name.localeCompare(b.name, 'id-ID'))
    default:
      return items.sort((a, b) => b.newestRank - a.newestRank)
  }
})

const totalPages = computed(() => Math.max(1, Math.ceil(sortedProducts.value.length / ITEMS_PER_PAGE)))

const shownCount = computed(() => {
  const limit = currentPage.value * ITEMS_PER_PAGE

  return Math.min(limit, sortedProducts.value.length)
})

const filteredCount = computed(() => sortedProducts.value.length)

const visibleProducts = computed(() => {
  return sortedProducts.value.slice(0, shownCount.value)
})

const pageNumbers = computed(() => Array.from({ length: totalPages.value }, (_, idx) => idx + 1))

const loadProgress = computed(() => {
  if (filteredCount.value === 0) {
    return 0
  }

  return (shownCount.value / filteredCount.value) * 100
})

const gridClass = computed(() => {
  if (viewMode.value === 'list') {
    return 'grid-cols-1'
  }

  if (viewMode.value === '4col') {
    return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-4'
  }

  return 'grid-cols-1 sm:grid-cols-2 xl:grid-cols-3'
})

const activeFilterChips = computed<FilterChip[]>(() => {
  const chips: FilterChip[] = []

  if (searchQuery.value.trim().length > 0) {
    chips.push({ key: 'search', label: `Cari: ${searchQuery.value.trim()}`, type: 'search' })
  }

  selectedBrands.value.forEach(brand => {
    const label = brandFilters.find(item => item.key === brand)?.label ?? brand
    chips.push({ key: `brand-${brand}`, label, type: 'brand', value: brand })
  })

  selectedSizes.value.forEach(size => {
    chips.push({ key: `size-${size}`, label: `Sz. ${size}`, type: 'size', value: size })
  })

  selectedCollections.value.forEach(collection => {
    const label = collectionFilters.find(item => item.key === collection)?.label ?? collection
    chips.push({ key: `collection-${collection}`, label, type: 'collection', value: collection })
  })

  if (selectedStatus.value !== 'all') {
    const label = statusFilters.find(item => item.key === selectedStatus.value)?.label ?? selectedStatus.value
    chips.push({ key: `status-${selectedStatus.value}`, label, type: 'status', value: selectedStatus.value })
  }

  if (priceMin.value > 0 || priceMax.value < MAX_PRICE) {
    chips.push({
      key: 'price',
      label: `${formatCompactPrice(priceMin.value)} - ${formatCompactPrice(priceMax.value)}`,
      type: 'price',
    })
  }

  return chips
})

watch([priceMin, priceMax], () => {
  const safeMin = normalizePrice(priceMin.value)
  const safeMax = normalizePrice(priceMax.value)

  if (safeMin !== priceMin.value) {
    priceMin.value = safeMin
  }

  if (safeMax !== priceMax.value) {
    priceMax.value = safeMax
  }

  if (priceMin.value > priceMax.value) {
    priceMax.value = priceMin.value
  }
})

watch([searchQuery, sortMode, selectedStatus, selectedBrands, selectedSizes, selectedCollections, priceMin, priceMax], () => {
  currentPage.value = 1
}, { deep: true })

watch(sortedProducts, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
})

const setViewMode = (mode: ViewMode) => {
  viewMode.value = mode
}

const toggleBrand = (brand: string) => {
  if (selectedBrands.value.includes(brand)) {
    selectedBrands.value = selectedBrands.value.filter(item => item !== brand)

    return
  }

  selectedBrands.value = [...selectedBrands.value, brand]
}

const toggleCollection = (collection: string) => {
  if (selectedCollections.value.includes(collection)) {
    selectedCollections.value = selectedCollections.value.filter(item => item !== collection)

    return
  }

  selectedCollections.value = [...selectedCollections.value, collection]
}

const toggleSize = (size: number) => {
  if (selectedSizes.value.includes(size)) {
    selectedSizes.value = selectedSizes.value.filter(item => item !== size)

    return
  }

  selectedSizes.value = [...selectedSizes.value, size]
}

const resetBrandFilter = () => {
  selectedBrands.value = []
}

const resetSizeFilter = () => {
  selectedSizes.value = []
}

const resetCollectionFilter = () => {
  selectedCollections.value = []
}

const showApplyFeedback = () => {
  applyButtonLabel.value = 'Diterapkan'

  if (applyFeedbackTimeout !== undefined) {
    clearTimeout(applyFeedbackTimeout)
  }

  applyFeedbackTimeout = setTimeout(() => {
    applyButtonLabel.value = 'Terapkan Filter'
  }, 1200)
}

const goToPage = (page: number) => {
  const target = Math.min(Math.max(page, 1), totalPages.value)
  currentPage.value = target
}

const loadMore = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value += 1
  }
}

const clearAllFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = 'all'
  selectedBrands.value = []
  selectedSizes.value = []
  selectedCollections.value = []
  priceMin.value = 0
  priceMax.value = MAX_PRICE
}

const removeFilterChip = (chip: FilterChip) => {
  if (chip.type === 'search') {
    searchQuery.value = ''

    return
  }

  if (chip.type === 'brand' && typeof chip.value === 'string') {
    selectedBrands.value = selectedBrands.value.filter(item => item !== chip.value)

    return
  }

  if (chip.type === 'size' && typeof chip.value === 'number') {
    selectedSizes.value = selectedSizes.value.filter(item => item !== chip.value)

    return
  }

  if (chip.type === 'collection' && typeof chip.value === 'string') {
    selectedCollections.value = selectedCollections.value.filter(item => item !== chip.value)

    return
  }

  if (chip.type === 'status') {
    selectedStatus.value = 'all'

    return
  }

  if (chip.type === 'price') {
    priceMin.value = 0
    priceMax.value = MAX_PRICE
  }
}

const goToProduct = (productId: number) => {
  router.visit(`/katalog/${productId}`)
}

const toggleWishlist = (id: number) => {
  if (wishlistedIds.value.includes(id)) {
    wishlistedIds.value = wishlistedIds.value.filter(item => item !== id)

    return
  }

  wishlistedIds.value = [...wishlistedIds.value, id]
}

const isWishlisted = (id: number) => wishlistedIds.value.includes(id)

const statusText = (status: ProductStatus) => {
  if (status === 'po') {
    return 'PO'
  }

  if (status === 'ready') {
    return 'Ready'
  }

  return 'Habis'
}

const statusBadgeClass = (status: ProductStatus) => {
  if (status === 'po') {
    return 'border border-matcha/40 bg-matcha/15 text-matcha'
  }

  if (status === 'ready') {
    return 'border border-take/40 bg-take/15 text-take'
  }

  return 'border border-sumi/15 bg-sumi/5 text-hai'
}

const statusButtonClass = (status: (typeof statusFilters)[number]['key']) => {
  if (selectedStatus.value !== status) {
    return 'border-sumi/15 bg-shironeri text-hai hover:border-sumi/30 hover:text-sumi'
  }

  if (status === 'po') {
    return 'border-matcha/50 bg-matcha/15 text-matcha'
  }

  if (status === 'ready') {
    return 'border-take/50 bg-take/15 text-take'
  }

  if (status === 'habis') {
    return 'border-sumi/30 bg-sumi/5 text-hai'
  }

  return 'border-sumi bg-sumi text-washi'
}

const collectionLabel = (collection: string) => {
  return collectionFilters.find(item => item.key === collection)?.label ?? collection
}

const priceSubtext = (status: ProductStatus) => {
  if (status === 'po') {
    return 'Pre-order · 14-21 hari'
  }

  if (status === 'ready') {
    return 'Stok tersedia'
  }

  return 'Stok habis · Notif saya'
}

const formatCompactPrice = (price: number) => {
  const inThousands = Math.round(price / 1000)

  return `Rp ${inThousands.toLocaleString('id-ID')}K`
}

const normalizePrice = (value: number) => {
  if (!Number.isFinite(value)) {
    return 0
  }

  return Math.min(Math.max(Math.floor(value), 0), MAX_PRICE)
}

onUnmounted(() => {
  if (applyFeedbackTimeout !== undefined) {
    clearTimeout(applyFeedbackTimeout)
  }
})
</script>
