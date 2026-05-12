<template>
  <nav
    class="fixed top-6 left-1/2 z-40 max-w-[calc(100vw-1.5rem)] -translate-x-1/2 rounded-full border border-sumi/10 bg-washi/90 px-2 py-1.5 shadow-lg backdrop-blur-md"
  >
    <div class="flex items-center gap-1 overflow-x-auto">
      <Link
        href="/"
        aria-label="Bogor Sneakers Home"
        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-sumi/10 bg-washi shadow-sm transition-all duration-300 hover:border-sumi/20 hover:bg-sumi"
      >
        <img
          src="/logo-b-bogor.svg"
          alt="Logo Bogor Sneakers"
          class="h-5 w-5"
          loading="eager"
          decoding="async"
        />
      </Link>
      <Link
        v-for="item in navItems"
        :key="item.key"
        :href="item.href"
        :class="[
          'nav-link shrink-0 rounded-full px-4 py-2 text-xs tracking-widest uppercase transition-all duration-300',
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
import { onMounted, ref } from 'vue'
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

const isMounted = ref(false)

onMounted(() => {
  isMounted.value = true
})

const navItems: NavItem[] = [
  { key: 'home', href: '/', label: 'Home' },
  { key: 'katalog', href: '/katalog', label: 'Katalog' },
  { key: 'studio-custom', href: '/studio-custom', label: 'Studio Custom' },
  { key: 'tracking', href: '/tracking', label: 'Tracking' },
]
</script>
