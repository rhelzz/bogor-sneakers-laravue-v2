<template>
  <button
    id="menuToggle"
    aria-label="Toggle navigation"
    class="fixed top-6 left-6 z-50 flex items-center gap-3 group"
    @click="menuOpen = !menuOpen"
  >
    <span class="w-12 h-12 rounded-full bg-sumi text-washi flex items-center justify-center transition-all duration-300 group-hover:bg-usuzumi shadow-lg">
      <i :class="menuOpen ? 'bi bi-x-lg text-xl' : 'bi bi-list text-xl'"></i>
    </span>
    <span class="text-xs tracking-widest uppercase text-hai hidden md:block">{{ menuOpen ? 'Close' : 'Menu' }}</span>
  </button>

  <nav
    :class="[
      'fixed top-6 left-1/2 -translate-x-1/2 z-40 bg-washi/90 backdrop-blur-md rounded-full px-2 py-1.5 shadow-lg border border-sumi/10 transition-all duration-300',
      { 'opacity-0 pointer-events-none -translate-y-5': !menuOpen },
    ]"
  >
    <div class="flex items-center gap-1">
      <Link
        v-for="item in navItems"
        :key="item.key"
        :href="item.href"
        :class="[
          'nav-link px-5 py-2 rounded-full text-xs tracking-widest uppercase transition-all duration-300',
          currentPage === item.key ? 'bg-sumi text-washi' : 'text-usuzumi hover:bg-sumi/5',
        ]"
      >
        {{ item.label }}
      </Link>
    </div>
  </nav>
</template>

<script lang="ts">
export default {
  name: 'FloatingMenuNav',
}
</script>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { onMounted, onUnmounted, ref } from 'vue'

import type { FloatingNavPage } from '@/types/floating-ui'

interface NavItem {
  key: FloatingNavPage
  href: string
  label: string
}

defineProps<{
  currentPage: FloatingNavPage
}>()

const menuOpen = ref(true)

const navItems: NavItem[] = [
  { key: 'home', href: '/', label: 'Home' },
  { key: 'katalog', href: '/katalog', label: 'Katalog' },
  { key: 'studio-custom', href: '/studio-custom', label: 'Studio Custom' },
  { key: 'tracking', href: '/tracking', label: 'Tracking' },
]

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    menuOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>
