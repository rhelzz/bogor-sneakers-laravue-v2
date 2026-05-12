import { router } from '@inertiajs/vue3'
import { reactive } from 'vue'

export interface CartItem {
  id: number
  catalog_id: number
  catalog_size_id: number
  quantity: number
  price_at_time: number
  catalog: {
    id: number
    name: string
    price: number
    brand: string
    images: Array<{
      id: number
      image_url: string
    }>
  }
  size: {
    id: number
    size_eu: number
  }
}

export interface CartData {
  items: CartItem[]
  total: number
  count: number
}

export const cartState = reactive({
  isOpen: false,
  items: [] as any[],
  total: 0,
  count: 0,
  loading: false,

  updateFromProps(cart: CartData) {
    if (!cart) return

    this.items = cart.items.map((item) => ({
      id: item.id,
      catalog_id: item.catalog_id,
      size_id: item.catalog_size_id,
      name: item.catalog.name,
      size: item.size.size_eu,
      qty: item.quantity,
      price: item.catalog.price,
      image: item.catalog.images?.[0]?.image_url || null
    }))
    this.total = cart.total
    this.count = cart.count
  },

  async addItem(catalogId: number, sizeId: number, quantity: number = 1) {
    this.loading = true
    router.post(
      '/cart/add',
      {
        catalog_id: catalogId,
        catalog_size_id: sizeId,
        quantity
      },
      {
        preserveScroll: true,
        onSuccess: () => {
          this.open()
        },
        onFinish: () => {
          this.loading = false
        }
      }
    )
  },

  async updateQuantity(itemId: number, quantity: number) {
    router.patch(
      `/cart/items/${itemId}`,
      {
        quantity
      },
      {
        preserveScroll: true
      }
    )
  },

  async removeItem(itemId: number) {
    router.delete(`/cart/items/${itemId}`, {
      preserveScroll: true
    })
  },

  open() {
    this.isOpen = true
  },
  close() {
    this.isOpen = false
  },
  toggle() {
    this.isOpen = !this.isOpen
  }
})
