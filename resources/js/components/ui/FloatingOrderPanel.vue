<template>
  <button
    aria-label="Lacak pesanan"
    class="fixed top-6 right-6 z-50 group md:right-auto md:left-1/2 md:translate-x-62"
    @click="orderOpen = !orderOpen"
  >
    <span class="w-12 h-12 rounded-full bg-washi/90 border border-sumi/10 text-usuzumi flex items-center justify-center transition-all duration-300 group-hover:text-sumi shadow-lg backdrop-blur-md">
      <i class="bi bi-bag text-lg"></i>
    </span>
    <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 text-[10px] tracking-widest uppercase text-hai whitespace-nowrap">
      {{ buttonLabel }}
    </span>
  </button>

  <div
    :class="[
      'fixed top-24 right-6 w-[min(20rem,calc(100vw-3rem))] bg-washi rounded-2xl shadow-2xl border border-sumi/10 z-40 overflow-hidden transition-all duration-300 origin-top-right md:right-auto md:left-1/2 md:-translate-x-6',
      orderOpen ? 'opacity-100 pointer-events-auto translate-y-0 scale-100' : 'opacity-0 pointer-events-none -translate-y-2 scale-[0.98]',
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
        class="text-xs tracking-widest uppercase text-usuzumi hover:text-sumi transition-colors"
      >
        {{ ctaText }}
      </a>
      <Link
        v-else
        :href="ctaHref"
        class="text-xs tracking-widest uppercase text-usuzumi hover:text-sumi transition-colors"
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
  buttonLabel: 'Order',
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
