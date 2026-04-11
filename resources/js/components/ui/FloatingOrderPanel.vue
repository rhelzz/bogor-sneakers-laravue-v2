<template>
  <button
    aria-label="Lacak pesanan"
    class="fixed bottom-6 right-6 z-50 flex items-center gap-3 group"
    @click="orderOpen = !orderOpen"
  >
    <span class="text-xs tracking-widest uppercase text-hai hidden md:block">{{ orderOpen ? 'Close' : buttonLabel }}</span>
    <span class="w-12 h-12 rounded-full bg-indigo text-washi flex items-center justify-center transition-all duration-300 group-hover:opacity-90 shadow-lg relative">
      <i :class="orderOpen ? 'bi bi-x-lg text-lg' : 'bi bi-box-seam text-lg'"></i>
      <span class="absolute -top-1 -right-1 w-3 h-3 bg-matcha rounded-full animate-pulse"></span>
    </span>
  </button>

  <div
    :class="[
      'fixed bottom-24 right-6 w-80 bg-washi rounded-2xl shadow-2xl border border-sumi/10 z-40 overflow-hidden transition-all duration-300 origin-bottom-right',
      orderOpen ? 'opacity-100 pointer-events-auto translate-y-0 scale-100' : 'opacity-0 pointer-events-none translate-y-2 scale-[0.98]',
    ]"
  >
    <div class="p-5 border-b border-sumi/10 flex items-center justify-between">
      <h3 class="font-heading font-bold tracking-wider">ORDER TRACKER</h3>
      <div class="flex items-center gap-2 text-xs text-matcha">
        <span class="w-2 h-2 rounded-full bg-matcha animate-pulse"></span>
        Live
      </div>
    </div>

    <div v-if="orders.length > 0" class="max-h-64 overflow-y-auto">
      <div
        v-for="order in orders"
        :key="order.id"
        class="p-4 border-b border-sumi/5"
      >
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold">{{ order.id }}</span>
          <span :class="`px-2 py-1 rounded text-[10px] ${order.statusClass}`">{{ order.status }}</span>
        </div>
        <p class="text-xs text-hai mb-3">{{ order.product }} - Sz. {{ order.size }}</p>
        <div class="h-1.5 bg-sumi/10 rounded-full overflow-hidden">
          <div :class="`h-full rounded-full transition-all duration-500 ${order.progressClass}`" :style="`width: ${order.progress}%`"></div>
        </div>
      </div>
    </div>

    <div v-else class="p-6 text-center text-sm text-hai">
      Belum ada order aktif.
    </div>

    <div class="p-4 border-t border-sumi/10 text-center">
      <a
        v-if="isHashLink"
        :href="ctaHref"
        class="text-xs tracking-widest uppercase text-indigo hover:text-indigo/70 transition-colors"
      >
        {{ ctaText }}
      </a>
      <Link
        v-else
        :href="ctaHref"
        class="text-xs tracking-widest uppercase text-indigo hover:text-indigo/70 transition-colors"
      >
        {{ ctaText }}
      </Link>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'FloatingOrderPanel',
}
</script>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed, onMounted, onUnmounted, ref } from 'vue'

import type { FloatingOrder } from '@/types/floating-ui'

const props = withDefaults(defineProps<{
  orders: FloatingOrder[]
  ctaHref?: string
  ctaText?: string
  buttonLabel?: string
}>(), {
  ctaHref: '/tracking',
  ctaText: 'Lihat Semua Order',
  buttonLabel: 'Orders',
})

const orderOpen = ref(false)

const isHashLink = computed(() => {
  return props.ctaHref.startsWith('#')
})

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    orderOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>
