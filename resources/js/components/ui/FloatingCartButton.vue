<template>
  <button
    aria-label="Buka keranjang"
    class="fixed top-6 right-[1.5rem] z-50 group md:right-auto md:left-1/2 md:translate-x-[19rem]"
    @click="cartState.toggle()"
  >
    <span class="relative w-12 h-12 rounded-full bg-washi/90 border border-sumi/10 text-usuzumi flex items-center justify-center transition-all duration-300 group-hover:text-sumi shadow-lg backdrop-blur-md">
      <i class="bi bi-cart2 text-lg"></i>
      <span 
        v-if="cartState.count > 0"
        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-matcha text-[10px] font-bold text-sumi border-2 border-washi"
      >
        {{ cartState.count }}
      </span>
    </span>
    <span class="absolute top-full left-1/2 -translate-x-1/2 mt-2 text-[10px] tracking-widest uppercase text-hai whitespace-nowrap">
      Keranjang
    </span>
  </button>

  <CartDrawer 
    v-if="isMounted"
    :is-open="cartState.isOpen" 
    :items="cartState.items"
    @close="cartState.close()"
  />
</template>

<script lang="ts">
export default {
  name: 'FloatingCartButton',
}
</script>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue'
import { cartState } from '@/stores/cart'
import CartDrawer from './CartDrawer.vue'

const isMounted = ref(false)
const page = usePage()

watch(
  () => page.props.cart,
  (cart: any) => {
    cartState.updateFromProps(cart)
  },
  { immediate: true, deep: true }
)

onMounted(() => {
  isMounted.value = true
})
</script>
