<template>
  <div class="min-h-screen bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="katalog" />
    <FloatingAdminPanel :contacts="contacts" />
    <FloatingOrderPanel :orders="orders" />

    <section class="min-h-screen bg-[radial-gradient(circle_at_1px_1px,rgba(26,26,26,0.06)_1px,transparent_0)] bg-size-[24px_24px] px-4 pb-14 pt-28 sm:px-6 lg:px-10">
      <div class="mx-auto max-w-6xl">
        <Link
          href="/katalog"
          class="inline-flex items-center gap-2 rounded-full border border-sumi/20 bg-shironeri px-4 py-2 text-xs uppercase tracking-widest text-usuzumi transition hover:border-sumi/35 hover:text-sumi"
        >
          <i class="bi bi-arrow-left" />
          Kembali ke katalog
        </Link>

        <div
          v-if="product"
          class="mt-5 overflow-hidden rounded-[28px] border border-sumi/10 bg-shironeri/95 shadow-[0_16px_36px_rgba(26,26,26,0.08)]"
        >
          <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="border-b border-sumi/10 p-5 lg:border-b-0 lg:border-r lg:p-7">
              <div class="relative aspect-square overflow-hidden rounded-3xl border border-sumi/10 bg-sumi/5">
                <div class="absolute inset-0 flex items-center justify-center text-hai/35">
                  <i class="bi bi-image text-6xl" />
                </div>
                <span
                  class="absolute left-4 top-4 rounded-md px-3 py-1 text-[11px] uppercase tracking-[0.08em]"
                  :class="statusBadgeClass(product.status)"
                >
                  {{ statusText(product.status) }}
                </span>
              </div>

              <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-hai sm:grid-cols-4">
                <div class="rounded-xl border border-sumi/10 bg-washi p-3">
                  <p class="mb-1 uppercase tracking-[0.08em]">ID</p>
                  <p class="font-semibold text-sumi">#{{ product.id }}</p>
                </div>
                <div class="rounded-xl border border-sumi/10 bg-washi p-3">
                  <p class="mb-1 uppercase tracking-[0.08em]">Kode</p>
                  <p class="font-semibold text-sumi">{{ product.code }}</p>
                </div>
                <div class="rounded-xl border border-sumi/10 bg-washi p-3">
                  <p class="mb-1 uppercase tracking-[0.08em]">Brand</p>
                  <p class="font-semibold text-sumi">{{ product.brandLabel }}</p>
                </div>
                <div class="rounded-xl border border-sumi/10 bg-washi p-3">
                  <p class="mb-1 uppercase tracking-[0.08em]">Koleksi</p>
                  <p class="font-semibold text-sumi">{{ collectionLabel(product.collection) }}</p>
                </div>
              </div>
            </div>

            <div class="p-5 lg:p-7">
              <p class="text-[11px] uppercase tracking-[0.14em] text-hai">{{ product.brandLabel }} · {{ collectionLabel(product.collection) }}</p>
              <h1 class="mt-2 text-3xl font-bold leading-tight tracking-tight">{{ product.name }}</h1>
              <p class="mt-3 text-2xl font-bold text-sumi">{{ formatCurrency(product.price) }}</p>
              <p class="mt-1 text-xs text-hai">{{ priceSubtext(product.status) }}</p>

              <div class="mt-6">
                <p class="mb-2 text-[11px] uppercase tracking-[0.12em] text-hai">Pilih Ukuran</p>
                <div class="grid grid-cols-4 gap-2 sm:grid-cols-5">
                  <button
                    v-for="size in availableSizes"
                    :key="size"
                    class="rounded-md border px-2 py-2 text-xs transition"
                    :class="sizeButtonClass(size)"
                    @click="selectSize(size)"
                  >
                    {{ size }}
                  </button>
                </div>
              </div>

              <div class="mt-6 rounded-2xl border border-sumi/10 bg-washi p-4">
                <p class="text-xs leading-relaxed text-usuzumi">
                  Produk dipilih melalui URL detail berbasis ID, sehingga user langsung masuk ke halaman detail tanpa modal.
                  URL produk ini: <span class="font-semibold text-sumi">/katalog/{{ product.id }}</span>
                </p>
              </div>

              <div class="mt-6 flex flex-wrap gap-2">
                <button
                  class="rounded-full px-5 py-2.5 text-xs uppercase tracking-widest transition"
                  :class="ctaClass"
                  :disabled="product.status === 'habis'"
                >
                  {{ ctaText }}
                </button>
                <button class="rounded-full border border-sumi/20 bg-transparent px-5 py-2.5 text-xs uppercase tracking-widest text-usuzumi transition hover:border-sumi/40 hover:text-sumi">
                  Simpan Wishlist
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mt-5 rounded-3xl border border-dashed border-sumi/20 bg-shironeri p-10 text-center">
          <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl border border-sumi/15 text-hai">
            <i class="bi bi-search" />
          </div>
          <h2 class="text-xl font-bold">Produk tidak ditemukan</h2>
          <p class="mt-2 text-sm text-hai">ID produk {{ normalizedId }} tidak tersedia di katalog demo.</p>
          <Link
            href="/katalog"
            class="mt-5 inline-flex items-center gap-2 rounded-full bg-sumi px-5 py-2.5 text-xs uppercase tracking-widest text-washi transition hover:opacity-85"
          >
            Kembali ke katalog
          </Link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

type ProductStatus = 'po' | 'ready' | 'habis'

interface ProductDetailItem {
  id: number
  code: string
  name: string
  brandLabel: string
  collection: string
  price: number
  status: ProductStatus
  sizes: number[]
}

const props = defineProps<{
  productId: number | string
}>()

const availableSizes = [38, 39, 40, 41, 42, 43, 44, 45]

const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Rizky - Admin', role: 'PO · Order · Ketersediaan', phone: '6281234567890', initial: 'R', color: 'bg-matcha/20 text-matcha' },
  { id: 2, name: 'Farhan - CS', role: 'Komplain · Tracking · Retur', phone: '6289876543210', initial: 'F', color: 'bg-indigo/20 text-indigo' },
])

const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
])

const products: ProductDetailItem[] = [
  { id: 1, code: 'BGS-NM97-SLV', name: 'Air Max 97 Silver Bullet', brandLabel: 'Nike', collection: 'lifestyle', price: 1850000, status: 'po', sizes: [39, 40, 41, 42, 43] },
  { id: 2, code: 'BGS-SMB-WHT', name: 'Samba OG White Gum', brandLabel: 'Adidas', collection: 'lifestyle', price: 1290000, status: 'ready', sizes: [39, 40, 41, 42] },
  { id: 3, code: 'BGS-J1-BRED', name: 'Jordan 1 Retro High Bred', brandLabel: 'Jordan', collection: 'basketball', price: 2100000, status: 'po', sizes: [40, 41, 42, 43, 44] },
  { id: 4, code: 'BGS-NB574-NVY', name: 'New Balance 574 Core Navy', brandLabel: 'New Balance', collection: 'lifestyle', price: 980000, status: 'ready', sizes: [39, 40, 41, 42, 43, 44] },
  { id: 5, code: 'BGS-DUNK-PND', name: 'Nike Dunk Low Retro Panda', brandLabel: 'Nike', collection: 'lifestyle', price: 1650000, status: 'habis', sizes: [] },
  { id: 6, code: 'BGS-FRM-WHT', name: 'Adidas Forum Low White Blue', brandLabel: 'Adidas', collection: 'lifestyle', price: 1100000, status: 'ready', sizes: [39, 40, 41, 42, 43] },
  { id: 7, code: 'BGS-VNT-CLS', name: 'Ventela Classic White Low', brandLabel: 'Ventela', collection: 'lifestyle', price: 420000, status: 'ready', sizes: [39, 40, 41, 42, 43, 44] },
  { id: 8, code: 'BGS-J4-BCAT', name: 'Jordan 4 Retro Black Cat', brandLabel: 'Jordan', collection: 'basketball', price: 2450000, status: 'po', sizes: [41, 42, 43, 44, 45] },
  { id: 9, code: 'BGS-AF1-WHT', name: 'Air Force 1 Low 07 White', brandLabel: 'Nike', collection: 'lifestyle', price: 1200000, status: 'ready', sizes: [40, 41, 42, 43] },
  { id: 10, code: 'BGS-VNS-OLD', name: 'Vans Old Skool Black White', brandLabel: 'Vans', collection: 'skate', price: 750000, status: 'ready', sizes: [39, 40, 41, 42, 43] },
  { id: 11, code: 'BGS-PGS-41', name: 'Nike Pegasus 41 React', brandLabel: 'Nike', collection: 'running', price: 1550000, status: 'po', sizes: [40, 41, 42, 43, 44] },
  { id: 12, code: 'BGS-UB22-BLK', name: 'Adidas Ultra Boost 22 Core Black', brandLabel: 'Adidas', collection: 'running', price: 1800000, status: 'ready', sizes: [40, 41, 42, 43] },
]

const normalizedId = computed(() => {
  return Number(props.productId)
})

const product = computed(() => {
  return products.find(item => item.id === normalizedId.value)
})

const selectedSize = ref<number | null>(null)

watch(product, value => {
  selectedSize.value = value?.sizes[0] ?? null
}, { immediate: true })

const ctaText = computed(() => {
  if (product.value?.status === 'habis') {
    return 'Notifikasi Saya'
  }

  if (product.value?.status === 'po') {
    return 'Pre-Order Sekarang'
  }

  return 'Pesan Sekarang'
})

const ctaClass = computed(() => {
  if (product.value?.status === 'habis') {
    return 'cursor-not-allowed border border-sumi/20 bg-sumi/5 text-hai'
  }

  return 'bg-sumi text-washi hover:opacity-85'
})

const selectSize = (size: number) => {
  if (!product.value?.sizes.includes(size)) {
    return
  }

  selectedSize.value = size
}

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

const sizeButtonClass = (size: number) => {
  if (!product.value?.sizes.includes(size)) {
    return 'cursor-not-allowed border-sumi/10 bg-sumi/5 text-hai/50'
  }

  if (selectedSize.value === size) {
    return 'border-sumi bg-sumi text-washi'
  }

  return 'border-sumi/15 bg-shironeri text-usuzumi hover:border-matcha hover:text-sumi'
}

const collectionLabel = (collection: string) => {
  const dictionary: Record<string, string> = {
    lifestyle: 'Lifestyle',
    running: 'Running',
    basketball: 'Basketball',
    skate: 'Skateboarding',
  }

  return dictionary[collection] ?? collection
}

const priceSubtext = (status: ProductStatus) => {
  if (status === 'po') {
    return 'Pre-order · estimasi 14-21 hari'
  }

  if (status === 'ready') {
    return 'Stok tersedia · siap kirim'
  }

  return 'Stok habis · aktifkan notifikasi'
}

const formatCurrency = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}
</script>
