<template>
  <AdminLayout>
    <template #header>
      Manajemen Pre-order
    </template>

    <div class="space-y-6 font-hind">

      <!-- Top Action Bar -->
      <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center space-x-3">
          <h3 class="font-montserrat font-bold text-lg text-slate-800 tracking-tight">Daftar Pre-order Aktif</h3>
          <span class="bg-indigo-100 text-indigo-700 py-1 px-3 rounded-lg text-sm font-bold">
            Total: {{ mockPreorders.length }} Item
          </span>
        </div>
        <button
          @click="openModal('add')"
          class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl transition-colors duration-200 flex items-center space-x-2"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah Pre-order</span>
        </button>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                <th class="px-6 py-4 font-bold">Urutan</th>
                <th class="px-6 py-4 font-bold">Produk / Katalog</th>
                <th class="px-6 py-4 font-bold">Batch Label</th>
                <th class="px-6 py-4 font-bold text-center">Slot</th>
                <th class="px-6 py-4 font-bold text-center">Batas Waktu</th>
                <th class="px-6 py-4 font-bold text-center">Status</th>
                <th class="px-6 py-4 font-bold text-center">Visibilitas</th>
                <th class="px-6 py-4 font-bold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="item in mockPreorders"
                :key="item.id"
                class="hover:bg-slate-50/50 transition-colors duration-150"
              >
                <!-- Urutan -->
                <td class="px-6 py-4">
                  <span class="font-montserrat font-bold text-slate-800">#{{ item.sort_order }}</span>
                </td>

                <!-- Produk -->
                <td class="px-6 py-4">
                  <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-lg bg-slate-200 overflow-hidden flex-shrink-0 border border-slate-200">
                      <img :src="item.catalog.image" alt="Product" class="h-full w-full object-cover" />
                    </div>
                    <div>
                      <p class="text-sm font-bold text-slate-800">{{ item.catalog.name }}</p>
                      <p class="text-xs font-semibold text-slate-500">{{ item.catalog.sku }}</p>
                    </div>
                  </div>
                </td>

                <!-- Batch -->
                <td class="px-6 py-4">
                  <span class="text-sm font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                    {{ item.batch_label || '-' }}
                  </span>
                </td>

                <!-- Slot -->
                <td class="px-6 py-4 text-center">
                  <div class="flex flex-col items-center justify-center">
                    <span class="text-sm font-bold text-slate-800">{{ item.filled_slots }} / {{ item.max_slots }}</span>
                    <div class="w-full h-1.5 bg-slate-200 rounded-full mt-1.5 max-w-[60px] overflow-hidden">
                      <div
                        class="h-full rounded-full transition-all duration-500"
                        :class="getSlotColor(item.filled_slots, item.max_slots)"
                        :style="{ width: `${(item.filled_slots / item.max_slots) * 100}%` }"
                      ></div>
                    </div>
                  </div>
                </td>

                <!-- Countdown -->
                <td class="px-6 py-4 text-center">
                  <span class="text-sm font-semibold text-slate-600">{{ formatDate(item.countdown_ends_at) }}</span>
                </td>

                <!-- Status Badge -->
                <td class="px-6 py-4 text-center">
                  <span
                    class="px-3 py-1 text-xs font-bold rounded-md"
                    :class="getStatusClass(item.status)"
                  >
                    {{ formatStatus(item.status) }}
                  </span>
                </td>

                <!-- FIX 1: Switch di tabel pakai :style -->
                <td class="px-6 py-4 text-center">
                  <button
                    type="button"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :style="{ backgroundColor: item.is_visible ? '#10b981' : '#cbd5e1' }"
                    @click="item.is_visible = !item.is_visible"
                    role="switch"
                    :aria-checked="item.is_visible.toString()"
                  >
                    <span
                      class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                      :style="{ transform: item.is_visible ? 'translateX(1.25rem)' : 'translateX(0)' }"
                    />
                  </button>
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end space-x-2">
                    <button
                      @click="openModal('edit', item)"
                      class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                      title="Edit"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                    <button
                      class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                      title="Hapus"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-if="mockPreorders.length === 0">
                <td colspan="8" class="px-6 py-12 text-center text-slate-500 font-semibold">
                  Belum ada data pre-order.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- Modal Form (Add / Edit) -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center font-hind p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-[#15161a]/60 backdrop-blur-sm" @click="closeModal"></div>

        <!-- Modal Content -->
        <Transition
          enter-active-class="transition duration-300 ease-out delay-75"
          enter-from-class="opacity-0 translate-y-8 scale-95"
          enter-to-class="opacity-100 translate-y-0 scale-100"
          leave-active-class="transition duration-200 ease-in"
          leave-from-class="opacity-100 translate-y-0 scale-100"
          leave-to-class="opacity-0 translate-y-8 scale-95"
        >
          <div v-if="isModalOpen" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">

            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50 shrink-0">
              <h3 class="font-montserrat font-bold text-xl text-slate-800">
                {{ modalMode === 'add' ? 'Tambah Pre-order' : 'Edit Pre-order' }}
              </h3>
              <button @click="closeModal" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-colors focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 overflow-y-auto flex-1 space-y-5">

              <!-- Form Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Catalog ID / Produk -->
                <div class="col-span-1 md:col-span-2">
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Katalog Produk</label>
                  <select class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold appearance-none cursor-pointer">
                    <option value="" disabled selected>Pilih Produk...</option>
                    <option value="1">Nike Air Max 97 - SK123</option>
                    <option value="2">Adidas Dunk Low - AD456</option>
                    <option value="3">New Balance 550 - NB789</option>
                  </select>
                </div>

                <!-- Batch Label -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Batch Label</label>
                  <input type="text" placeholder="Contoh: Batch 1" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold" />
                </div>

                <!-- Urutan (Sort Order) -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Urutan Tampil</label>
                  <input type="number" placeholder="0" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold" />
                </div>

                <!-- Status -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Status</label>
                  <select class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold appearance-none cursor-pointer">
                    <option value="buka">Buka (Open)</option>
                    <option value="hampir_habis">Hampir Habis</option>
                    <option value="habis">Habis (Closed)</option>
                  </select>
                </div>

                <!-- Batas Waktu -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Batas Waktu (Countdown)</label>
                  <input type="datetime-local" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold" />
                </div>

                <!-- Maksimal Slot -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Maksimal Slot</label>
                  <input type="number" min="1" placeholder="Misal: 50" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold" />
                </div>

                <!-- Slot Terisi -->
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-1.5">Slot Terisi</label>
                  <input type="number" min="0" placeholder="Misal: 10" class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold" />
                </div>

                <!-- FIX 2: Switch di modal pakai :style + state modalVisibility -->
                <div class="col-span-1 md:col-span-2 flex items-center space-x-3 mt-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                  <button
                    type="button"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :style="{ backgroundColor: modalVisibility ? '#10b981' : '#cbd5e1' }"
                    @click="modalVisibility = !modalVisibility"
                    role="switch"
                    :aria-checked="modalVisibility.toString()"
                  >
                    <span
                      class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                      :style="{ transform: modalVisibility ? 'translateX(1.25rem)' : 'translateX(0)' }"
                    />
                  </button>
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800">Tampilkan di Halaman Utama</span>
                    <span class="text-xs font-semibold text-slate-500">Jika aktif, item ini akan muncul di beranda pelanggan.</span>
                  </div>
                </div>

              </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end space-x-3 shrink-0">
              <button @click="closeModal" class="px-5 py-2.5 rounded-xl font-bold text-slate-600 hover:bg-slate-200 hover:text-slate-800 transition-colors focus:outline-none">
                Batal
              </button>
              <button class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors focus:outline-none shadow-sm">
                Simpan Data
              </button>
            </div>

          </div>
        </Transition>
      </div>
    </Transition>

  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

// --- State Modal ---
const isModalOpen = ref(false)
const modalMode = ref<'add' | 'edit'>('add')
const selectedItem = ref(null)
const modalVisibility = ref(true) // State untuk switch di dalam modal

const openModal = (mode: 'add' | 'edit', item: any = null) => {
  modalMode.value = mode
  selectedItem.value = item
  // Saat edit, sync state switch modal dengan data item
  modalVisibility.value = item ? item.is_visible : true
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  setTimeout(() => {
    selectedItem.value = null
  }, 300)
}

// --- Helper Functions ---
const formatStatus = (status: string) => {
  switch(status) {
    case 'buka': return 'Buka'
    case 'hampir_habis': return 'Hampir Habis'
    case 'habis': return 'Habis'
    default: return status
  }
}

const getStatusClass = (status: string) => {
  switch(status) {
    case 'buka': return 'bg-emerald-100 text-emerald-700'
    case 'hampir_habis': return 'bg-amber-100 text-amber-700'
    case 'habis': return 'bg-rose-100 text-rose-700'
    default: return 'bg-slate-100 text-slate-700'
  }
}

const getSlotColor = (filled: number, max: number) => {
  const percentage = (filled / max) * 100
  if (percentage >= 100) return 'bg-rose-500'
  if (percentage >= 80) return 'bg-amber-500'
  return 'bg-emerald-500'
}

const formatDate = (dateStr: string | null) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

// --- Mock Data ---
const mockPreorders = ref([
  {
    id: 1,
    catalog_id: 101,
    catalog: {
      name: 'Nike Air Max 97',
      sku: 'NK-AM97-01',
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=150&q=80'
    },
    status: 'buka',
    is_visible: true,
    max_slots: 50,
    filled_slots: 12,
    countdown_ends_at: '2026-05-15T23:59:00',
    batch_label: 'Batch 1',
    sort_order: 1
  },
  {
    id: 2,
    catalog_id: 102,
    catalog: {
      name: 'Adidas Dunk Low Panda',
      sku: 'AD-DL-PD',
      image: 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&w=150&q=80'
    },
    status: 'hampir_habis',
    is_visible: true,
    max_slots: 30,
    filled_slots: 28,
    countdown_ends_at: '2026-05-01T23:59:00',
    batch_label: 'Batch 2',
    sort_order: 2
  },
  {
    id: 3,
    catalog_id: 103,
    catalog: {
      name: 'New Balance 550',
      sku: 'NB-550-WH',
      image: 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&w=150&q=80'
    },
    status: 'habis',
    is_visible: false,
    max_slots: 20,
    filled_slots: 20,
    countdown_ends_at: '2026-04-20T12:00:00',
    batch_label: 'Batch 1',
    sort_order: 3
  }
])
</script>
