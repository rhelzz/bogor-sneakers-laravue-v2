<template>
  <nav
    class="fixed top-6 left-1/2 -translate-x-1/2 z-40 bg-washi/90 backdrop-blur-md rounded-full px-2 py-1.5 shadow-lg border border-sumi/10"
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

import type { FloatingNavPage } from '@/types/floating-ui'

interface NavItem {
  key: FloatingNavPage
  href: string
  label: string
}

defineProps<{
  currentPage: FloatingNavPage
}>()

const navItems: NavItem[] = [
  { key: 'home', href: '/', label: 'Home' },
  { key: 'katalog', href: '/katalog', label: 'Katalog' },
  { key: 'studio-custom', href: '/studio-custom', label: 'Studio Custom' },
  { key: 'tracking', href: '/tracking', label: 'Tracking' },
]
</script>
