<template>
  <Teleport v-if="isMounted" to="body">
    <!-- Backdrop -->
    <Transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-100 bg-sumi/40 backdrop-blur-sm"
        @click="$emit('close')"
      ></div>
    </Transition>

    <!-- Drawer -->
    <Transition
      enter-active-class="transition-transform duration-500 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-400 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div
        v-if="isOpen"
        class="fixed inset-y-0 right-0 z-101 flex w-full max-w-md flex-col bg-washi shadow-2xl"
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-sumi/10 p-6">
          <div class="flex items-center gap-3">
            <h2 class="font-heading text-xl font-bold tracking-wider">KERANJANG</h2>
            <span class="rounded-full bg-sumi/5 px-3 py-0.5 text-xs font-bold">{{ cartState.count }} ITEM</span>
          </div>
          <button
            @click="$emit('close')"
            class="flex h-10 w-10 items-center justify-center rounded-full transition-all hover:bg-sumi/5"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-6">
          <div v-if="items.length > 0" class="space-y-6">
            <div
              v-for="item in items"
              :key="item.id"
              class="flex gap-4 border-b border-sumi/5 pb-6 last:border-0"
            >
              <!-- Product Image -->
              <div class="h-24 w-24 shrink-0 overflow-hidden rounded-2xl bg-shironeri border border-sumi/5">
                <img
                  v-if="item.image"
                  :src="item.image"
                  :alt="item.name"
                  class="h-full w-full object-cover"
                />
                <div v-else class="flex h-full w-full items-center justify-center">
                  <i class="bi bi-image text-2xl text-hai/20"></i>
                </div>
              </div>

              <!-- Product Info -->
              <div class="flex flex-1 flex-col">
                <div class="flex justify-between">
                  <h3 class="text-sm font-bold">{{ item.name }}</h3>
                  <button 
                    @click="cartState.removeItem(item.id)"
                    class="text-hai hover:text-red-500 transition-colors"
                  >
                    <i class="bi bi-trash text-sm"></i>
                  </button>
                </div>
                <p class="mt-1 text-xs text-hai">Size: {{ item.size }}</p>
                
                <div class="mt-auto flex items-center justify-between">
                  <!-- Quantity Control -->
                  <div class="flex items-center gap-3 rounded-full border border-sumi/10 px-2 py-1">
                    <button 
                      @click="cartState.updateQuantity(item.id, item.qty - 1)"
                      class="flex h-6 w-6 items-center justify-center text-hai hover:text-sumi"
                    >
                      <i class="bi bi-dash"></i>
                    </button>
                    <span class="text-xs font-bold">{{ item.qty }}</span>
                    <button 
                      @click="cartState.updateQuantity(item.id, item.qty + 1)"
                      class="flex h-6 w-6 items-center justify-center text-hai hover:text-sumi"
                    >
                      <i class="bi bi-plus"></i>
                    </button>
                  </div>
                  
                  <!-- Price -->
                  <p class="text-sm font-bold">{{ formatPrice(item.price * item.qty) }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="flex h-full flex-col items-center justify-center text-center">
            <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-sumi/5 text-hai">
              <i class="bi bi-bag-x text-4xl"></i>
            </div>
            <h3 class="font-heading text-lg font-bold">Wah, keranjangmu kosong!</h3>
            <p class="mt-2 text-sm text-hai">Yuk, intip katalog terbaru kami dan temukan sneaker impianmu.</p>
            <button
              @click="$emit('close')"
              class="mt-8 rounded-full bg-sumi px-8 py-3 text-sm tracking-wider text-washi transition-all hover:bg-usuzumi"
            >
              Lihat Katalog
            </button>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="items.length > 0" class="border-t border-sumi/10 bg-shironeri/50 p-6">
          <div class="mb-6 flex items-center justify-between">
            <span class="text-sm tracking-widest text-hai">SUBTOTAL</span>
            <span class="text-xl font-bold text-matcha">{{ formatPrice(subtotal) }}</span>
          </div>
          <button
            @click="goToCheckout"
            class="w-full rounded-full bg-sumi py-4 text-sm font-bold tracking-widest text-washi transition-all hover:bg-usuzumi shadow-lg hover:shadow-xl active:scale-[0.98]"
          >
            LANJUT KE CHECKOUT
          </button>
          <p class="mt-4 text-center text-[10px] tracking-widest text-hai uppercase">
            Pajak & Ongkir dihitung saat checkout
          </p>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { cartState } from '@/stores/cart'
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  isOpen: boolean
  items: any[]
}>()

const emit = defineEmits(['close'])

const isMounted = ref(false)

onMounted(() => {
  isMounted.value = true
})

const subtotal = computed(() => {
  return cartState.total
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(price)
}

const goToCheckout = () => {
  emit('close')
  router.visit('/checkout')
}
</script>

<style scoped>
.z-100 { z-index: 100; }
.z-101 { z-index: 101; }
</style>
