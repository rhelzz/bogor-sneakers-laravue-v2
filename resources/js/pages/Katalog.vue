<template>
  <div class="bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="katalog" />
    <FloatingAdminPanel :contacts="contacts" />
    <FloatingOrderPanel :orders="orders" />

    <section class="catalog-page pattern-seigaiha">
      <div class="catalog-shell">
        <header class="cat-header">
          <div class="cat-header__left">
            <p class="cat-header__eyebrow">STORE ID BGS-001 · BOGOR, IDN</p>
            <h1 class="cat-header__title">Katalog</h1>
            <p class="cat-header__sub">
              Menampilkan
              <strong>{{ shownCount }}</strong>
              dari
              <strong>{{ filteredCount }}</strong>
              produk
            </p>
          </div>

          <div class="cat-search-wrap">
            <input
              v-model="searchQuery"
              class="cat-search"
              type="text"
              placeholder="Cari nama / brand / kode..."
              autocomplete="off"
              spellcheck="false"
            >
            <span class="cat-search-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </span>
          </div>

          <div class="cat-header__right">
            <select v-model="sortMode" class="cat-sort">
              <option value="newest">Terbaru</option>
              <option value="price-asc">Harga: Rendah ke Tinggi</option>
              <option value="price-desc">Harga: Tinggi ke Rendah</option>
              <option value="popular">Terpopuler</option>
              <option value="az">A ke Z</option>
            </select>
            <div class="view-toggle">
              <button
                class="view-btn"
                :class="{ active: viewMode === '3col' }"
                title="3 kolom"
                @click="setViewMode('3col')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="3" width="5" height="18" rx="1" />
                  <rect x="10" y="3" width="5" height="18" rx="1" />
                  <rect x="17" y="3" width="5" height="18" rx="1" />
                </svg>
              </button>
              <button
                class="view-btn"
                :class="{ active: viewMode === '4col' }"
                title="4 kolom"
                @click="setViewMode('4col')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="3" width="4" height="18" rx="1" />
                  <rect x="7.5" y="3" width="4" height="18" rx="1" />
                  <rect x="13" y="3" width="4" height="18" rx="1" />
                  <rect x="18.5" y="3" width="4" height="18" rx="1" />
                </svg>
              </button>
              <button
                class="view-btn"
                :class="{ active: viewMode === 'list' }"
                title="List"
                @click="setViewMode('list')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
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

        <div class="catalog-layout">
          <aside class="sidebar">
            <div class="sidebar__section">
              <div class="sidebar__label">
                Brand
                <button class="sidebar__label-reset" @click="resetBrandFilter">Reset</button>
              </div>
              <div class="filter-list">
                <button
                  v-for="brand in brandFilters"
                  :key="brand.key"
                  class="filter-item"
                  :class="{ selected: selectedBrands.includes(brand.key) }"
                  @click="toggleBrand(brand.key)"
                >
                  <span class="filter-item__left">
                    <span class="filter-checkbox" />
                    <span class="filter-item__name">{{ brand.label }}</span>
                  </span>
                  <span class="filter-item__count">{{ brandCount[brand.key] ?? 0 }}</span>
                </button>
              </div>
            </div>

            <div class="sidebar__section">
              <div class="sidebar__label">
                Ukuran (EU)
                <button class="sidebar__label-reset" @click="resetSizeFilter">Reset</button>
              </div>
              <div class="size-grid">
                <button
                  v-for="size in availableSizes"
                  :key="size"
                  class="size-btn"
                  :class="{ active: selectedSizes.includes(size) }"
                  @click="toggleSize(size)"
                >
                  {{ size }}
                </button>
              </div>
            </div>

            <div class="sidebar__section">
              <div class="sidebar__label">Harga</div>
              <div class="price-inputs">
                <input
                  v-model.number="priceMin"
                  class="price-input"
                  type="number"
                  min="0"
                  :max="MAX_PRICE"
                  placeholder="Min"
                >
                <span class="price-sep">-</span>
                <input
                  v-model.number="priceMax"
                  class="price-input"
                  type="number"
                  min="0"
                  :max="MAX_PRICE"
                  placeholder="Maks"
                >
              </div>
              <input
                v-model.number="priceMax"
                class="price-slider"
                type="range"
                min="0"
                :max="MAX_PRICE"
                step="50000"
              >
            </div>

            <div class="sidebar__section">
              <div class="sidebar__label">Status Stok</div>
              <div class="status-pills">
                <button
                  v-for="status in statusFilters"
                  :key="status.key"
                  class="status-pill"
                  :class="[
                    status.className,
                    { active: selectedStatus === status.key },
                  ]"
                  @click="selectedStatus = status.key"
                >
                  {{ status.label }}
                </button>
              </div>
            </div>

            <div class="sidebar__section">
              <div class="sidebar__label">
                Koleksi
                <button class="sidebar__label-reset" @click="resetCollectionFilter">Reset</button>
              </div>
              <div class="filter-list">
                <button
                  v-for="collection in collectionFilters"
                  :key="collection.key"
                  class="filter-item"
                  :class="{ selected: selectedCollections.includes(collection.key) }"
                  @click="toggleCollection(collection.key)"
                >
                  <span class="filter-item__left">
                    <span class="filter-checkbox" />
                    <span class="filter-item__name">{{ collection.label }}</span>
                  </span>
                  <span class="filter-item__count">{{ collectionCount[collection.key] ?? 0 }}</span>
                </button>
              </div>
            </div>

            <button class="sidebar__apply" @click="showApplyFeedback">
              {{ applyButtonLabel }}
            </button>
          </aside>

          <main class="catalog-main">
            <div class="active-filters">
              <span class="active-filters__label">Filter:</span>
              <span
                v-for="chip in activeFilterChips"
                :key="chip.key"
                class="active-chip"
              >
                {{ chip.label }}
                <button class="active-chip__remove" :aria-label="`Hapus ${chip.label}`" @click="removeFilterChip(chip)">
                  <i class="bi bi-x" />
                </button>
              </span>
              <button class="active-filters__clear" :disabled="activeFilterChips.length === 0" @click="clearAllFilters">
                Hapus semua
              </button>
            </div>

            <div class="results-meta">
              <p class="results-meta__count">
                Menampilkan
                <strong>{{ shownCount }}</strong>
                produk · halaman
                <strong>{{ currentPage }}</strong>
                dari
                <strong>{{ totalPages }}</strong>
              </p>
            </div>

            <div class="product-grid" :class="productGridClass">
              <article
                v-for="product in visibleProducts"
                :key="product.id"
                class="prod-card card-lift"
                :class="{ 'prod-card--disabled': product.status === 'habis' }"
                @click="openModal(product)"
              >
                <div class="prod-card__thumb img-reveal">
                  <span class="prod-card__status" :class="statusBadgeClass(product.status)">
                    {{ statusText(product.status) }}
                  </span>
                  <button
                    class="prod-card__wish"
                    :class="{ liked: isWishlisted(product.id) }"
                    :aria-label="`Wishlist ${product.name}`"
                    @click.stop="toggleWishlist(product.id)"
                  >
                    <i class="bi bi-heart" />
                  </button>
                </div>

                <div class="prod-card__body">
                  <p class="prod-card__brand">{{ product.brandLabel }} · {{ collectionLabel(product.collection) }}</p>
                  <p class="prod-card__name">{{ product.name }}</p>

                  <div class="prod-card__sizes">
                    <span v-for="size in product.sizes.slice(0, 6)" :key="`${product.id}-${size}`" class="prod-card__size-tag">
                      {{ size }}
                    </span>
                  </div>

                  <div class="prod-card__footer">
                    <div>
                      <p class="prod-card__price">{{ formatCompactPrice(product.price) }}</p>
                      <p class="prod-card__price-sub">{{ priceSubtext(product.status) }}</p>
                    </div>
                    <button class="prod-card__cta" @click.stop="openModal(product)">
                      {{ product.status === 'habis' ? 'Notif' : 'Order' }}
                    </button>
                  </div>
                </div>
              </article>

              <div v-if="visibleProducts.length === 0" class="empty-state">
                <div class="empty-state__icon">
                  <i class="bi bi-search" />
                </div>
                <p class="empty-state__title">Produk tidak ditemukan</p>
                <p class="empty-state__sub">Coba ubah filter atau kata kunci pencarian</p>
              </div>
            </div>

            <div v-if="filteredCount > 0" class="catalog-footer">
              <p class="load-more-label">Menampilkan {{ shownCount }} dari {{ filteredCount }} produk</p>
              <div class="load-more-bar">
                <div class="load-more-bar__fill" :style="{ width: `${loadProgress}%` }" />
              </div>
              <button class="load-more-btn" :disabled="shownCount >= filteredCount" @click="loadMore">
                Muat lebih banyak
              </button>

              <div class="pagination">
                <button class="page-btn page-btn--arrow" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">‹</button>
                <button
                  v-for="page in pageNumbers"
                  :key="page"
                  class="page-btn"
                  :class="{ active: currentPage === page }"
                  @click="goToPage(page)"
                >
                  {{ page }}
                </button>
                <button class="page-btn page-btn--arrow" :disabled="currentPage === totalPages" @click="goToPage(currentPage + 1)">›</button>
              </div>
            </div>
          </main>
        </div>
      </div>
    </section>

    <div class="modal-overlay" :class="{ open: isModalOpen }" @click="closeModalOnOverlay">
      <div v-if="selectedProduct" class="modal">
        <div class="modal__top">
          <div class="modal__thumb" />
          <div class="modal__info">
            <p class="modal__brand">{{ selectedProduct.brandLabel }}</p>
            <h2 class="modal__name">{{ selectedProduct.name }}</h2>
            <p class="modal__price">{{ formatCurrency(selectedProduct.price) }}</p>

            <p class="modal__section-label">Pilih Ukuran</p>
            <div class="modal__size-grid">
              <button
                v-for="size in availableSizes"
                :key="`modal-${size}`"
                class="modal__size-btn"
                :class="{
                  active: modalSelectedSize === size,
                  unavail: !selectedProduct.sizes.includes(size),
                }"
                @click="selectModalSize(size)"
              >
                {{ size }}
              </button>
            </div>

            <div class="modal__actions">
              <button class="modal__btn-main">
                {{ modalCtaText }}
              </button>
              <button
                class="modal__btn-wish"
                :class="{ liked: isWishlisted(selectedProduct.id) }"
                @click="toggleWishlist(selectedProduct.id)"
              >
                <i class="bi bi-heart" />
              </button>
            </div>
          </div>
        </div>
        <div class="modal__footer">
          <span class="modal__meta">{{ modalMetaText }}</span>
          <button class="modal__close" @click="closeModal">
            <i class="bi bi-x" />
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'

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
  { key: 'all', label: 'Semua', className: '' },
  { key: 'ready', label: 'Ready', className: 'status-pill--ready' },
  { key: 'po', label: 'PO', className: 'status-pill--po' },
  { key: 'habis', label: 'Habis', className: 'status-pill--habis' },
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

const selectedProduct = ref<CatalogProduct | null>(null)
const isModalOpen = ref(false)
const modalSelectedSize = ref<number | null>(null)

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

const productGridClass = computed(() => {
  if (viewMode.value === '4col') {
    return 'view--4col'
  }

  if (viewMode.value === 'list') {
    return 'view--list'
  }

  return ''
})

const modalCtaText = computed(() => {
  if (selectedProduct.value?.status === 'habis') {
    return 'Notifikasi Saya'
  }

  if (selectedProduct.value?.status === 'po') {
    return 'Pre-Order Sekarang'
  }

  return 'Pesan Sekarang'
})

const modalMetaText = computed(() => {
  if (selectedProduct.value?.status === 'habis') {
    return 'Stok habis · aktifkan notifikasi saat tersedia'
  }

  if (selectedProduct.value?.status === 'po') {
    return 'Pre-order · estimasi 14 sampai 21 hari kerja'
  }

  return 'Pilih ukuran lalu pesan via WA atau DM'
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

watch(isModalOpen, value => {
  if (typeof document !== 'undefined') {
    document.body.style.overflow = value ? 'hidden' : ''
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

const openModal = (product: CatalogProduct) => {
  selectedProduct.value = product
  modalSelectedSize.value = product.sizes[0] ?? null
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const closeModalOnOverlay = (event: MouseEvent) => {
  if (event.target === event.currentTarget) {
    closeModal()
  }
}

const selectModalSize = (size: number) => {
  if (!selectedProduct.value?.sizes.includes(size)) {
    return
  }

  modalSelectedSize.value = size
}

const handleEscape = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && isModalOpen.value) {
    closeModal()
  }
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
    return 'prod-card__status--po'
  }

  if (status === 'ready') {
    return 'prod-card__status--ready'
  }

  return 'prod-card__status--habis'
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

const formatCurrency = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}

const normalizePrice = (value: number) => {
  if (!Number.isFinite(value)) {
    return 0
  }

  return Math.min(Math.max(Math.floor(value), 0), MAX_PRICE)
}

onMounted(() => {
  if (typeof document !== 'undefined') {
    document.addEventListener('keydown', handleEscape)
  }
})

onUnmounted(() => {
  if (applyFeedbackTimeout !== undefined) {
    clearTimeout(applyFeedbackTimeout)
  }

  if (typeof document !== 'undefined') {
    document.removeEventListener('keydown', handleEscape)
    document.body.style.overflow = ''
  }
})
</script>

<style scoped>
* {
  box-sizing: border-box;
}

::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f7f5f0;
}

::-webkit-scrollbar-thumb {
  background: #4a4a4a;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #1a1a1a;
}

.catalog-page {
  min-height: 100vh;
  padding: 7rem 1rem 3rem;
}

.catalog-shell {
  max-width: 1280px;
  margin: 0 auto;
  border: 1px solid rgba(26, 26, 26, 0.1);
  border-radius: 2rem;
  background: rgba(250, 250, 248, 0.92);
  backdrop-filter: blur(8px);
  overflow: hidden;
  box-shadow: 0 16px 36px rgba(26, 26, 26, 0.08);
}

.cat-header {
  border-bottom: 1px solid rgba(26, 26, 26, 0.1);
  background: rgba(247, 245, 240, 0.85);
  padding: 1.5rem;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.cat-header__eyebrow {
  font-size: 0.65rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: #8a8a8a;
  margin-bottom: 0.4rem;
}

.cat-header__title {
  font-size: clamp(1.7rem, 2.8vw, 2.25rem);
  font-weight: 700;
  letter-spacing: -0.01em;
  line-height: 1;
}

.cat-header__sub {
  margin-top: 0.45rem;
  font-size: 0.75rem;
  color: #8a8a8a;
  letter-spacing: 0.03em;
}

.cat-header__sub strong {
  color: #1a1a1a;
}

.cat-search-wrap {
  flex: 1;
  max-width: 24rem;
  position: relative;
}

.cat-search {
  width: 100%;
  background: #fafaf8;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.75rem;
  padding: 0.72rem 2.4rem 0.72rem 0.9rem;
  font-size: 0.78rem;
  color: #1a1a1a;
  letter-spacing: 0.04em;
  outline: none;
  transition: border-color 0.15s;
}

.cat-search::placeholder {
  color: #8a8a8a;
}

.cat-search:focus {
  border-color: #7c8c5a;
}

.cat-search-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #8a8a8a;
  pointer-events: none;
}

.cat-header__right {
  display: flex;
  align-items: center;
  gap: 0.65rem;
}

.cat-sort {
  background: #fafaf8;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.75rem;
  padding: 0.62rem 1.8rem 0.62rem 0.75rem;
  font-size: 0.72rem;
  color: #1a1a1a;
  letter-spacing: 0.05em;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%234a4a4a' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.55rem center;
  outline: none;
}

.view-toggle {
  display: flex;
  background: #fafaf8;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.75rem;
  overflow: hidden;
}

.view-btn {
  width: 2.1rem;
  height: 2.1rem;
  border: none;
  background: transparent;
  color: #8a8a8a;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s;
}

.view-btn:hover {
  color: #1a1a1a;
}

.view-btn.active {
  background: #1a1a1a;
  color: #f7f5f0;
}

.catalog-layout {
  display: grid;
  grid-template-columns: 16rem minmax(0, 1fr);
}

.sidebar {
  border-right: 1px solid rgba(26, 26, 26, 0.1);
  background: rgba(247, 245, 240, 0.8);
  padding: 1.2rem;
  max-height: calc(100vh - 11.5rem);
  overflow-y: auto;
  position: sticky;
  top: 6rem;
}

.sidebar__section {
  margin-bottom: 1.2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(26, 26, 26, 0.1);
}

.sidebar__section:last-child {
  margin-bottom: 0;
  border-bottom: none;
}

.sidebar__label {
  font-size: 0.66rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: #8a8a8a;
  margin-bottom: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.sidebar__label-reset {
  border: none;
  background: none;
  color: #8a8a8a;
  font-size: 0.63rem;
  letter-spacing: 0.08em;
  text-decoration: underline;
  cursor: pointer;
}

.sidebar__label-reset:hover {
  color: #1a1a1a;
}

.filter-list {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.filter-item {
  border: none;
  background: transparent;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 0.55rem;
  padding: 0.45rem 0.55rem;
  cursor: pointer;
  transition: background 0.12s;
}

.filter-item:hover {
  background: rgba(26, 26, 26, 0.05);
}

.filter-item.selected {
  background: rgba(124, 140, 90, 0.12);
}

.filter-item__left {
  display: flex;
  align-items: center;
  gap: 0.55rem;
}

.filter-checkbox {
  width: 0.8rem;
  height: 0.8rem;
  border: 1.5px solid rgba(26, 26, 26, 0.3);
  border-radius: 0.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.filter-item.selected .filter-checkbox {
  border-color: #7c8c5a;
  background: #7c8c5a;
}

.filter-item.selected .filter-checkbox::after {
  content: '';
  width: 0.42rem;
  height: 0.25rem;
  border-left: 1.2px solid #f7f5f0;
  border-bottom: 1.2px solid #f7f5f0;
  transform: rotate(-45deg) translateY(-1px);
}

.filter-item__name {
  font-size: 0.72rem;
  color: #1a1a1a;
  letter-spacing: 0.03em;
}

.filter-item__count {
  font-size: 0.66rem;
  color: #8a8a8a;
}

.size-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.3rem;
}

.size-btn {
  border: 1px solid rgba(26, 26, 26, 0.14);
  background: #fafaf8;
  border-radius: 0.45rem;
  padding: 0.4rem 0.2rem;
  font-size: 0.7rem;
  color: #4a4a4a;
  cursor: pointer;
  transition: all 0.12s;
}

.size-btn:hover {
  border-color: #7c8c5a;
  color: #1a1a1a;
}

.size-btn.active {
  background: #1a1a1a;
  border-color: #1a1a1a;
  color: #f7f5f0;
}

.price-inputs {
  display: flex;
  gap: 0.45rem;
  align-items: center;
}

.price-input {
  flex: 1;
  min-width: 0;
  background: #fafaf8;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.45rem;
  padding: 0.45rem 0.5rem;
  font-size: 0.68rem;
  color: #1a1a1a;
  outline: none;
}

.price-input:focus {
  border-color: #7c8c5a;
}

.price-sep {
  font-size: 0.7rem;
  color: #8a8a8a;
}

.price-slider {
  width: 100%;
  margin-top: 0.65rem;
  accent-color: #7c8c5a;
  cursor: pointer;
}

.status-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
}

.status-pill {
  border: 1px solid rgba(26, 26, 26, 0.14);
  background: #fafaf8;
  border-radius: 999px;
  padding: 0.28rem 0.65rem;
  font-size: 0.62rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #8a8a8a;
  cursor: pointer;
  transition: all 0.15s;
}

.status-pill:hover {
  border-color: #1a1a1a;
  color: #1a1a1a;
}

.status-pill.active {
  border-color: #1a1a1a;
  background: #1a1a1a;
  color: #f7f5f0;
}

.status-pill--po.active {
  border-color: #7c8c5a;
  color: #7c8c5a;
  background: rgba(124, 140, 90, 0.14);
}

.status-pill--ready.active {
  border-color: #3d5a4c;
  color: #3d5a4c;
  background: rgba(61, 90, 76, 0.14);
}

.status-pill--habis.active {
  border-color: rgba(26, 26, 26, 0.3);
  color: #8a8a8a;
  background: rgba(26, 26, 26, 0.06);
}

.sidebar__apply {
  width: 100%;
  border: none;
  border-radius: 999px;
  background: #1a1a1a;
  color: #f7f5f0;
  font-size: 0.72rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.65rem;
  cursor: pointer;
  transition: opacity 0.15s;
}

.sidebar__apply:hover {
  opacity: 0.87;
}

.catalog-main {
  padding: 1.2rem;
}

.active-filters {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.active-filters__label {
  font-size: 0.64rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #8a8a8a;
}

.active-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 999px;
  padding: 0.22rem 0.35rem 0.22rem 0.6rem;
  font-size: 0.64rem;
  color: #1a1a1a;
  background: #fafaf8;
}

.active-chip__remove {
  width: 1rem;
  height: 1rem;
  border: none;
  border-radius: 50%;
  background: rgba(26, 26, 26, 0.08);
  color: #4a4a4a;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.active-chip__remove:hover {
  background: rgba(26, 26, 26, 0.2);
  color: #f7f5f0;
}

.active-filters__clear {
  margin-left: auto;
  border: none;
  background: none;
  font-size: 0.64rem;
  color: #8a8a8a;
  text-decoration: underline;
  cursor: pointer;
}

.active-filters__clear:disabled {
  cursor: not-allowed;
  opacity: 0.5;
}

.results-meta {
  margin-bottom: 1rem;
  padding-bottom: 0.8rem;
  border-bottom: 1px solid rgba(26, 26, 26, 0.1);
}

.results-meta__count {
  font-size: 0.72rem;
  color: #8a8a8a;
  letter-spacing: 0.04em;
}

.results-meta__count strong {
  color: #1a1a1a;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.8rem;
}

.product-grid.view--4col {
  grid-template-columns: repeat(4, minmax(0, 1fr));
}

.product-grid.view--list {
  grid-template-columns: 1fr;
  gap: 0;
}

.prod-card {
  border: 1px solid rgba(26, 26, 26, 0.12);
  border-radius: 0.95rem;
  background: #fafaf8;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  cursor: pointer;
}

.prod-card--disabled {
  opacity: 0.6;
}

.view--list .prod-card {
  border-radius: 0;
  border-left: none;
  border-right: none;
  border-top: none;
  flex-direction: row;
}

.view--list .prod-card:first-child {
  border-top: 1px solid rgba(26, 26, 26, 0.12);
}

.prod-card__thumb {
  aspect-ratio: 1 / 1;
  background: rgba(26, 26, 26, 0.06);
  position: relative;
}

.prod-card__thumb::before,
.prod-card__thumb::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 100%;
  height: 1px;
  background: rgba(26, 26, 26, 0.14);
}

.prod-card__thumb::before {
  transform: rotate(20deg);
}

.prod-card__thumb::after {
  transform: rotate(-20deg);
}

.view--list .prod-card__thumb {
  width: 5.6rem;
  margin: 0.65rem;
  border-radius: 0.7rem;
  flex-shrink: 0;
}

.prod-card__status {
  position: absolute;
  top: 0.55rem;
  left: 0.55rem;
  border-radius: 0.3rem;
  padding: 0.18rem 0.42rem;
  font-size: 0.58rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.prod-card__status--po {
  border: 1px solid rgba(124, 140, 90, 0.4);
  background: rgba(124, 140, 90, 0.14);
  color: #7c8c5a;
}

.prod-card__status--ready {
  border: 1px solid rgba(61, 90, 76, 0.4);
  background: rgba(61, 90, 76, 0.14);
  color: #3d5a4c;
}

.prod-card__status--habis {
  border: 1px solid rgba(26, 26, 26, 0.14);
  background: rgba(26, 26, 26, 0.06);
  color: #8a8a8a;
}

.prod-card__wish {
  position: absolute;
  top: 0.55rem;
  right: 0.55rem;
  width: 1.7rem;
  height: 1.7rem;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 50%;
  background: rgba(26, 26, 26, 0.35);
  color: rgba(247, 245, 240, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.prod-card__wish.liked {
  color: #d4a5a5;
  border-color: rgba(212, 165, 165, 0.55);
}

.prod-card__body {
  padding: 0.8rem;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  min-width: 0;
  flex: 1;
}

.view--list .prod-card__body {
  flex-direction: row;
  align-items: center;
  gap: 0;
  padding: 0.7rem 0.8rem 0.7rem 0;
}

.prod-card__brand {
  font-size: 0.58rem;
  letter-spacing: 0.11em;
  text-transform: uppercase;
  color: #8a8a8a;
}

.prod-card__name {
  font-size: 0.82rem;
  font-weight: 700;
  line-height: 1.28;
  color: #1a1a1a;
}

.view--list .prod-card__name {
  flex: 1;
  margin: 0 0.8rem;
}

.view--list .prod-card__brand {
  display: none;
}

.prod-card__sizes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.2rem;
}

.prod-card__size-tag {
  background: rgba(26, 26, 26, 0.06);
  border-radius: 0.25rem;
  padding: 0.12rem 0.3rem;
  font-size: 0.58rem;
  color: #4a4a4a;
}

.view--list .prod-card__sizes {
  display: none;
}

.prod-card__footer {
  margin-top: auto;
  padding-top: 0.55rem;
  border-top: 1px solid rgba(26, 26, 26, 0.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.4rem;
}

.view--list .prod-card__footer {
  border-top: none;
  margin-top: 0;
  padding-top: 0;
}

.prod-card__price {
  font-size: 0.95rem;
  font-weight: 700;
}

.prod-card__price-sub {
  font-size: 0.58rem;
  color: #8a8a8a;
}

.prod-card__cta {
  border: none;
  border-radius: 999px;
  background: #1a1a1a;
  color: #f7f5f0;
  font-size: 0.63rem;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  padding: 0.45rem 0.75rem;
  white-space: nowrap;
  cursor: pointer;
}

.prod-card__cta:hover {
  opacity: 0.85;
}

.empty-state {
  grid-column: 1 / -1;
  border: 1px dashed rgba(26, 26, 26, 0.2);
  border-radius: 1rem;
  padding: 3rem 1rem;
  text-align: center;
}

.empty-state__icon {
  width: 3.2rem;
  height: 3.2rem;
  border-radius: 0.8rem;
  border: 1px solid rgba(26, 26, 26, 0.15);
  color: #8a8a8a;
  margin: 0 auto 0.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-state__title {
  font-weight: 700;
  margin-bottom: 0.35rem;
}

.empty-state__sub {
  font-size: 0.75rem;
  color: #8a8a8a;
}

.catalog-footer {
  margin-top: 1.4rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.8rem;
}

.load-more-label {
  font-size: 0.64rem;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: #8a8a8a;
}

.load-more-bar {
  width: min(100%, 24rem);
  height: 3px;
  border-radius: 999px;
  background: rgba(26, 26, 26, 0.1);
  overflow: hidden;
}

.load-more-bar__fill {
  height: 100%;
  background: #7c8c5a;
  transition: width 0.3s;
}

.load-more-btn {
  border: 1px solid rgba(26, 26, 26, 0.18);
  border-radius: 999px;
  background: transparent;
  color: #1a1a1a;
  font-size: 0.7rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  padding: 0.62rem 1.5rem;
  cursor: pointer;
}

.load-more-btn:hover {
  background: rgba(26, 26, 26, 0.06);
}

.load-more-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.page-btn {
  width: 1.95rem;
  height: 1.95rem;
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.45rem;
  background: transparent;
  font-size: 0.7rem;
  color: #8a8a8a;
  cursor: pointer;
}

.page-btn:hover {
  background: rgba(26, 26, 26, 0.08);
  color: #1a1a1a;
}

.page-btn.active {
  background: #1a1a1a;
  color: #f7f5f0;
  border-color: #1a1a1a;
}

.page-btn:disabled {
  opacity: 0.35;
  cursor: not-allowed;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(13, 13, 13, 0.56);
  backdrop-filter: blur(3px);
  z-index: 990;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
}

.modal-overlay.open {
  opacity: 1;
  pointer-events: auto;
}

.modal {
  width: min(92vw, 35rem);
  background: #fafaf8;
  border-radius: 1.15rem;
  border: 1px solid rgba(26, 26, 26, 0.14);
  overflow: hidden;
  transform: translateY(10px) scale(0.96);
  transition: transform 0.2s;
}

.modal-overlay.open .modal {
  transform: translateY(0) scale(1);
}

.modal__top {
  display: flex;
}

.modal__thumb {
  width: 13rem;
  flex-shrink: 0;
  position: relative;
  background: rgba(26, 26, 26, 0.08);
}

.modal__thumb::before,
.modal__thumb::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 100%;
  height: 1px;
  background: rgba(26, 26, 26, 0.2);
}

.modal__thumb::before {
  transform: rotate(20deg);
}

.modal__thumb::after {
  transform: rotate(-20deg);
}

.modal__info {
  flex: 1;
  padding: 1.1rem;
}

.modal__brand {
  font-size: 0.65rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #8a8a8a;
  margin-bottom: 0.4rem;
}

.modal__name {
  font-size: 1rem;
  line-height: 1.3;
  margin-bottom: 0.45rem;
}

.modal__price {
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
}

.modal__section-label {
  font-size: 0.62rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: #8a8a8a;
  margin-bottom: 0.4rem;
}

.modal__size-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 0.3rem;
  margin-bottom: 0.75rem;
}

.modal__size-btn {
  border: 1px solid rgba(26, 26, 26, 0.14);
  border-radius: 0.4rem;
  background: transparent;
  font-size: 0.68rem;
  color: #4a4a4a;
  padding: 0.35rem 0;
  cursor: pointer;
}

.modal__size-btn.active {
  background: #1a1a1a;
  border-color: #1a1a1a;
  color: #f7f5f0;
}

.modal__size-btn.unavail {
  opacity: 0.3;
  pointer-events: none;
}

.modal__actions {
  display: flex;
  gap: 0.5rem;
}

.modal__btn-main {
  flex: 1;
  border: none;
  border-radius: 999px;
  background: #1a1a1a;
  color: #f7f5f0;
  font-size: 0.68rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  padding: 0.7rem 0;
  cursor: pointer;
}

.modal__btn-wish {
  width: 2.45rem;
  height: 2.45rem;
  border-radius: 50%;
  border: 1px solid rgba(26, 26, 26, 0.14);
  background: transparent;
  color: #8a8a8a;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.modal__btn-wish.liked {
  color: #d4a5a5;
  border-color: rgba(212, 165, 165, 0.5);
}

.modal__footer {
  border-top: 1px solid rgba(26, 26, 26, 0.1);
  padding: 0.75rem 1.1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.8rem;
}

.modal__meta {
  font-size: 0.62rem;
  color: #8a8a8a;
  letter-spacing: 0.05em;
}

.modal__close {
  border: none;
  background: none;
  font-size: 0.64rem;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: #8a8a8a;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  cursor: pointer;
}

.modal__close:hover {
  color: #1a1a1a;
}

.pattern-seigaiha {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='40' viewBox='0 0 80 40'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.08' d='M0 40a19.96 19.96 0 0 1 5.9-14.11 20.17 20.17 0 0 1 19.44-5.2A20 20 0 0 1 20.2 40H0zM40 40a20 20 0 0 1-14.11-5.9 19.96 19.96 0 0 1-5.2-19.44A20.17 20.17 0 0 1 40 20.2V40zM40 20.2a20.17 20.17 0 0 1 19.44 5.2A20 20 0 0 1 40 40V20.2zM80 40H59.8a20 20 0 0 1 5.2-19.44 19.96 19.96 0 0 1 14.11-5.9V40z'/%3E%3C/svg%3E");
}

@media (max-width: 1200px) {
  .product-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .product-grid.view--4col {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 1024px) {
  .catalog-layout {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: static;
    max-height: none;
    border-right: none;
    border-bottom: 1px solid rgba(26, 26, 26, 0.1);
  }

  .catalog-main {
    padding-top: 1rem;
  }
}

@media (max-width: 768px) {
  .catalog-page {
    padding-top: 6.2rem;
    padding-left: 0.6rem;
    padding-right: 0.6rem;
  }

  .cat-header {
    padding: 1rem;
  }

  .cat-search-wrap {
    order: 3;
    min-width: 100%;
    max-width: none;
  }

  .cat-header__right {
    width: 100%;
    justify-content: space-between;
  }

  .cat-sort {
    flex: 1;
  }

  .catalog-main,
  .sidebar {
    padding: 0.9rem;
  }

  .product-grid,
  .product-grid.view--4col {
    grid-template-columns: 1fr;
  }

  .view--list .prod-card {
    border-radius: 0.7rem;
    border: 1px solid rgba(26, 26, 26, 0.12);
    margin-bottom: 0.6rem;
  }

  .view--list .prod-card:first-child {
    border-top: 1px solid rgba(26, 26, 26, 0.12);
  }

  .modal {
    width: 92vw;
  }

  .modal__top {
    flex-direction: column;
  }

  .modal__thumb {
    width: 100%;
    min-height: 10rem;
  }
}
</style>
