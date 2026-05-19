<template>
  <header
    :class="[
      'w-full transition-all duration-300 border-b backdrop-blur-md',
      isSticky 
        ? 'fixed top-0 left-0 bg-washi/90 border-sumi/10 shadow-md py-2 sm:py-3 animate-slide-down z-50' 
        : 'absolute top-0 left-0 bg-washi/95 border-sumi/5 shadow-xs py-4 sm:py-5 z-40'
    ]"
  >
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-12 sm:h-14 items-center justify-between">
        
        <!-- Brand Logo & Wordmark -->
        <Link
          href="/"
          aria-label="Bogor Sneakers Home"
          class="flex items-center gap-3 shrink-0 group"
        >
          <div class="flex h-9 w-9 items-center justify-center rounded-full border border-sumi/10 bg-shironeri shadow-sm group-hover:border-matcha/50 transition-all duration-300">
            <img
              src="/logo-b-bogor.svg"
              alt="Logo Bogor Sneakers"
              class="h-5 w-5 group-hover:scale-110 transition-transform duration-300"
              loading="eager"
            />
          </div>
          <span class="font-heading font-black tracking-widest text-sm text-sumi group-hover:text-matcha transition-colors uppercase">
            BOGOR SNEAKERS
          </span>
        </Link>

        <!-- Navigation Links - Desktop Only -->
        <nav class="hidden md:flex items-center gap-8">
          <Link
            v-for="item in navItems"
            :key="item.key"
            :href="item.href"
            class="group relative py-1 text-xs font-extrabold tracking-widest uppercase transition-all duration-300"
            :class="currentPage === item.key ? 'text-matcha' : 'text-usuzumi hover:text-sumi'"
          >
            {{ item.label }}
            <span
              :class="[
                'absolute bottom-0 left-0 h-[2px] bg-matcha transition-all duration-300',
                currentPage === item.key ? 'w-full' : 'w-0 group-hover:w-full'
              ]"
            ></span>
          </Link>
        </nav>

        <!-- Right Side Actions (Cart, Order Tracker, WA, Hamburger) -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Order Tracker Dropdown Panel -->
          <div class="relative">
            <button
              @click="isOrderPanelOpen = !isOrderPanelOpen; isMobileMenuOpen = false;"
              class="flex h-9 w-9 items-center justify-center rounded-full border border-sumi/10 hover:bg-sumi/5 transition-all duration-300 relative"
              aria-label="Lacak Pesanan"
            >
              <i class="bi bi-bag text-sm sm:text-base text-usuzumi hover:text-sumi transition-colors"></i>
            </button>
            
            <!-- Dropdown Menu panel -->
            <Transition name="slide-fade">
              <div
                v-if="isOrderPanelOpen"
                class="absolute right-0 mt-3 w-72 sm:w-80 bg-washi rounded-2xl shadow-2xl border border-sumi/10 z-50 overflow-hidden"
              >
                <div class="p-4 border-b border-sumi/10 flex items-center justify-between">
                  <h3 class="font-heading font-black text-xs tracking-wider text-sumi">ORDER TRACKER</h3>
                  <div class="flex items-center gap-1.5 text-[10px] font-bold text-matcha">
                    <span class="w-1.5 h-1.5 rounded-full bg-matcha animate-pulse"></span>
                    Live PO
                  </div>
                </div>

                <div class="max-h-64 overflow-y-auto">
                  <div
                    v-for="order in trackingOrders"
                    :key="order.id"
                    class="p-3.5 border-b border-sumi/5 text-left"
                  >
                    <div class="flex items-center justify-between mb-1.5">
                      <span class="text-xs font-black text-sumi">{{ order.id }}</span>
                      <span :class="`px-2 py-0.5 rounded text-[9px] font-bold ${order.statusClass}`">{{ order.status }}</span>
                    </div>
                    <p class="text-[11px] text-hai font-sans mb-2">{{ order.product }} - Sz. {{ order.size }}</p>
                    <div class="h-1 bg-sumi/10 rounded-full overflow-hidden">
                      <div :class="`h-full rounded-full transition-all duration-500 ${order.progressClass}`" :style="`width: ${order.progress}%`"></div>
                    </div>
                  </div>
                </div>

                <div class="p-3 bg-shironeri border-t border-sumi/10 text-center">
                  <Link
                    href="/tracking"
                    class="inline-block text-[10px] font-heading font-black tracking-widest uppercase text-usuzumi hover:text-sumi transition-colors"
                    @click="isOrderPanelOpen = false"
                  >
                    LIHAT SEMUA ORDER
                  </Link>
                </div>
              </div>
            </Transition>
          </div>

          <!-- Cart Button with Count Badge -->
          <button
            @click="cartState.toggle(); isMobileMenuOpen = false; isOrderPanelOpen = false;"
            class="flex h-9 w-9 items-center justify-center rounded-full border border-sumi/10 hover:bg-sumi/5 transition-all duration-300 relative"
            aria-label="Buka Keranjang"
          >
            <i class="bi bi-cart2 text-sm sm:text-base text-usuzumi hover:text-sumi transition-colors"></i>
            <span
              v-if="cartState.count > 0"
              class="absolute -top-1 -right-1 flex h-4.5 w-4.5 items-center justify-center rounded-full bg-matcha text-[8px] font-black text-sumi border border-washi"
            >
              {{ cartState.count }}
            </span>
          </button>

          <!-- Vertical Separation Line -->
          <span class="h-5 w-[1px] bg-sumi/10 hidden md:block"></span>

          <!-- Dynamic WA Contact Link - Desktop Only -->
          <a
            :href="whatsappUrl"
            target="_blank"
            class="hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sumi text-washi font-heading font-bold text-[9px] tracking-widest hover:bg-matcha hover:text-sumi hover:scale-105 active:scale-95 transition-all duration-300 shadow-xs"
          >
            <i class="bi bi-whatsapp"></i> CONTACT
          </a>

          <!-- Mobile Hamburger Toggle Menu -->
          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen; isOrderPanelOpen = false;"
            class="md:hidden flex h-9 w-9 items-center justify-center rounded-full border border-sumi/10 hover:bg-sumi/5 transition-all duration-300"
            aria-label="Toggle Navigation Menu"
          >
            <i class="bi text-lg" :class="isMobileMenuOpen ? 'bi-x-lg text-matcha' : 'bi-list'"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Overlay (Premium Slide-Down Overlay) -->
    <Transition name="slide-fade">
      <div
        v-if="isMobileMenuOpen"
        class="absolute top-16 sm:top-20 left-0 w-full bg-washi border-b border-sumi/10 shadow-lg z-30 px-6 py-8 md:hidden"
      >
        <div class="flex flex-col gap-6">
          <!-- Dynamic Mobile Nav Links -->
          <Link
            v-for="item in navItems"
            :key="item.key"
            :href="item.href"
            @click="isMobileMenuOpen = false"
            class="flex items-center justify-between py-2 border-b border-sumi/5 text-sm font-black tracking-widest uppercase text-sumi hover:text-matcha transition-colors"
          >
            <span>{{ item.label }}</span>
            <i class="bi bi-chevron-right text-xs opacity-50"></i>
          </Link>

          <!-- Dynamic WA Contact for Mobile Menu -->
          <div class="mt-4 space-y-4">
            <div class="flex items-center gap-2 px-3 py-1 rounded-full border border-sumi/10 bg-sumi/5 w-fit">
              <span class="h-1.5 w-1.5 rounded-full bg-matcha animate-pulse"></span>
              <span class="font-heading text-[8px] font-bold tracking-[0.2em] text-usuzumi uppercase">
                TOKYO - BOGOR CONNECTION
              </span>
            </div>
            
            <a
              :href="whatsappUrl"
              target="_blank"
              class="flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl bg-sumi text-washi font-heading font-black text-xs tracking-widest hover:bg-matcha hover:text-sumi active:scale-95 transition-all duration-300 shadow-sm"
            >
              <i class="bi bi-whatsapp"></i> CHAT WHATSAPP
            </a>
          </div>
        </div>
      </div>
    </Transition>
  </header>

  <!-- Cart Drawer Component Overlay -->
  <CartDrawer 
    v-if="isMounted"
    :is-open="cartState.isOpen" 
    :items="cartState.items"
    @close="cartState.close()"
  />
</template>

<script lang="ts">
export default {
  name: 'FloatingMenuNav',
}
</script>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { FloatingNavPage } from '@/types/floating-ui'
import { cartState } from '@/stores/cart'
import CartDrawer from './CartDrawer.vue'

interface NavItem {
  key: FloatingNavPage
  href: string
  label: string
}

defineProps<{
  currentPage: FloatingNavPage
}>()

// Component States
const isMobileMenuOpen = ref(false)
const isOrderPanelOpen = ref(false)
const isMounted = ref(false)
const isSticky = ref(false)

const navItems: NavItem[] = [
  { key: 'home', href: '/', label: 'Home' },
  { key: 'katalog', href: '/katalog', label: 'Katalog' },
  { key: 'studio-custom', href: '/studio-custom', label: 'Studio Custom' },
  { key: 'tracking', href: '/tracking', label: 'Tracking' },
]

// Mock tracking orders list
const trackingOrders = ref([
  {
    id: '#BGS-2841',
    product: 'Nike Air Max 97 Silver',
    size: '42',
    status: 'Produksi',
    statusClass: 'px-2 py-0.5 rounded text-[10px] bg-amber-100 text-amber-700',
    progress: 55,
    progressClass: 'bg-sumi',
  },
  {
    id: '#BGS-2790',
    product: 'Adidas Samba OG White',
    size: '40',
    status: 'Dikirim',
    statusClass: 'px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-700',
    progress: 85,
    progressClass: 'bg-sumi',
  },
  {
    id: '#BGS-2755',
    product: 'Jordan 1 Retro High Bred',
    size: '43',
    status: 'Selesai',
    statusClass: 'px-2 py-0.5 rounded text-[10px] bg-matcha/20 text-matcha',
    progress: 100,
    progressClass: 'bg-matcha',
  },
  {
    id: '#BGS-2870',
    product: 'NB 574 Navy',
    size: '41',
    status: 'Menunggu',
    statusClass: 'px-2 py-0.5 rounded text-[10px] bg-sumi/10 text-hai',
    progress: 10,
    progressClass: 'bg-hai/50',
  },
])

// Dynamic contacts integration (preserving dynamic data flow)
const page = usePage()

const handleScroll = () => {
  if (window.scrollY > 400) {
    isSticky.value = true
  } else {
    isSticky.value = false
  }
}

watch(
  () => page.props.cart,
  (cart: any) => {
    cartState.updateFromProps(cart)
  },
  { immediate: true, deep: true }
)

onMounted(() => {
  isMounted.value = true
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll() // Trigger initially just in case page starts scrolled
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const firstActiveContact = computed(() => {
  const contacts = (page.props.floatingContacts as any[]) || []
  return contacts.length > 0 ? contacts[0] : null
})

const whatsappUrl = computed(() => {
  if (!firstActiveContact.value) return '#'
  let phone = firstActiveContact.value.phone || ''
  phone = phone.replace(/\D/g, '')
  if (phone.startsWith('0')) {
    phone = '62' + phone.substring(1)
  }
  return `https://wa.me/${phone}?text=Halo%20Admin%20Bogor%20Sneakers%20!%20Saya%20tertarik%20untuk%20bertanya%20mengenai%20sepatu.`;
})
</script>

<style scoped>
/* Mobile overlay & panel transition animations */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease-in-out;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}

/* Premium sticky slide-down animation */
@keyframes slideDown {
  from {
    transform: translateY(-100%);
  }
  to {
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
