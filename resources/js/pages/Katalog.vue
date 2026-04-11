<template>
  <div class="bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="katalog" />
    <FloatingAdminPanel :contacts="contacts" />
    <FloatingOrderPanel :orders="orders" />

    <!-- KATALOG HEADER -->
    <section class="min-h-screen flex flex-col justify-center px-6 lg:px-16 pt-32 pb-24 pattern-seigaiha relative overflow-hidden">
      <div class="max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="mb-16 pb-10 accent-top">
          <div class="flex items-center gap-3 mb-4 animate-slide-up">
            <span class="w-2 h-2 rounded-full bg-matcha animate-pulse-soft"></span>
            <span class="text-xs tracking-widest text-hai">BOGOR SNEAKER KATALOG</span>
          </div>
          <h1 class="text-5xl lg:text-7xl font-heading font-bold leading-none tracking-tight mb-6 animate-slide-up" style="animation-delay: 0.1s">
            Koleksi<br>
            <span class="text-matcha">Lengkap</span>
          </h1>
          <p class="text-hai leading-relaxed max-w-md mb-8 animate-slide-up" style="animation-delay: 0.2s">
            240+ model signature dan limited edition dari brand internasional ternama.
          </p>
        </div>

        <!-- Filters -->
        <div class="mb-8 flex gap-2 overflow-x-auto pb-2 animate-slide-up" style="animation-delay: 0.3s">
          <button
            v-for="filter in ['all', 'nike', 'adidas', 'jordan', 'nb', 'lokal']"
            :key="filter"
            @click="catalogFilter = filter"
            :class="['px-4 py-1.5 rounded-full text-xs tracking-wide whitespace-nowrap transition-all', catalogFilter === filter ? 'bg-sumi text-washi' : 'bg-sumi/5 text-usuzumi hover:bg-sumi/10']"
          >
            {{ filter === 'all' ? 'Semua' : filter.charAt(0).toUpperCase() + filter.slice(1) }}
          </button>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="katalog-item bg-shironeri rounded-3xl overflow-hidden card-lift border border-sumi/5 animate-slide-up"
            :style="{ animationDelay: `${product.id * 0.05}s` }"
          >
            <div class="aspect-square bg-sumi/5 relative img-reveal">
              <div class="absolute inset-0 flex items-center justify-center">
                <i class="bi bi-image text-4xl text-hai/30"></i>
              </div>
              <span :class="['absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium', product.statusClass]">
                {{ product.status }}
              </span>
            </div>
            <div class="p-5">
              <p :class="['font-bold mb-1', product.status === 'Habis' ? 'text-hai' : '']">{{ product.name }}</p>
              <p class="text-xs text-hai mb-3">Sz. {{ product.size }}</p>
              <div class="flex items-center justify-between">
                <p :class="['text-lg font-bold', product.status === 'Habis' ? 'text-hai line-through' : '']">{{ product.price }}</p>
                <button :disabled="product.status === 'Habis'" class="w-10 h-10 rounded-full bg-sumi text-washi flex items-center justify-center hover:bg-usuzumi transition-all disabled:bg-sumi/20 disabled:cursor-not-allowed">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Load More -->
        <div class="text-center mt-12">
          <button class="px-8 py-3 border-2 border-sumi text-sumi rounded-full text-sm tracking-wider hover:bg-sumi hover:text-washi transition-all">
            Muat Lebih Banyak
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

// State Management
const catalogFilter = ref('all')

// Contact Data
const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Rizky - Admin', role: 'PO · Order · Ketersediaan', phone: '6281234567890', initial: 'R', color: 'bg-matcha/20 text-matcha' },
  { id: 2, name: 'Farhan - CS', role: 'Komplain · Tracking · Retur', phone: '6289876543210', initial: 'F', color: 'bg-indigo/20 text-indigo' },
  { id: 3, name: 'Dinda - DIY', role: 'Kustom · Desain · Konsultasi', phone: '6285511223344', initial: 'D', color: 'bg-sakura/30 text-sakura' },
])

// Orders
const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
  { id: '#BGS-2755', product: 'Jordan 1 Retro High Bred', size: '43', status: 'Selesai', statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha', progress: 100, progressClass: 'bg-matcha' },
  { id: '#BGS-2870', product: 'NB 574 Navy', size: '41', status: 'Menunggu', statusClass: 'px-2 py-1 rounded text-[10px] bg-sumi/10 text-hai', progress: 10, progressClass: 'bg-hai/50' },
])

// Products
const products = ref([
  { id: 1, name: 'Nike Air Max 97 Silver', size: '39-44', price: 'Rp 1.850K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'nike' },
  { id: 2, name: 'Adidas Samba OG White', size: '39-43', price: 'Rp 1.290K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'adidas' },
  { id: 3, name: 'Jordan 1 Retro High Bred', size: '40-45', price: 'Rp 2.100K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'jordan' },
  { id: 4, name: 'New Balance 574 Navy', size: '39-44', price: 'Rp 980K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'nb' },
  { id: 5, name: 'Nike Dunk Low Panda', size: '40-45', price: 'Rp 1.650K', status: 'Habis', statusClass: 'bg-hai/50 text-washi', brand: 'nike' },
  { id: 6, name: 'Adidas Forum Low', size: '39-43', price: 'Rp 1.100K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'adidas' },
  { id: 7, name: 'Ventela Classic White', size: '39-44', price: 'Rp 420K', status: 'Ready', statusClass: 'bg-take text-washi', brand: 'lokal' },
  { id: 8, name: 'Jordan 4 Retro Black Cat', size: '41-45', price: 'Rp 2.450K', status: 'PO', statusClass: 'bg-matcha text-washi', brand: 'jordan' },
])

const filteredProducts = computed(() => {
  return products.value.filter(p => catalogFilter.value === 'all' || p.brand === catalogFilter.value)
})
</script>

<style scoped>
* {
  box-sizing: border-box;
}

/* Custom scrollbar */
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

/* Japanese pattern backgrounds */
.pattern-seigaiha {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='40' viewBox='0 0 80 40'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.08' d='M0 40a19.96 19.96 0 0 1 5.9-14.11 20.17 20.17 0 0 1 19.44-5.2A20 20 0 0 1 20.2 40H0zM40 40a20 20 0 0 1-14.11-5.9 19.96 19.96 0 0 1-5.2-19.44A20.17 20.17 0 0 1 40 20.2V40zM40 20.2a20.17 20.17 0 0 1 19.44 5.2A20 20 0 0 1 40 40V20.2zM80 40H59.8a20 20 0 0 1 5.2-19.44 19.96 19.96 0 0 1 14.11-5.9V40z'/%3E%3C/svg%3E");
}
</style>
