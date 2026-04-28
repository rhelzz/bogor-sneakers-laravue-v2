<template>
  <AdminLayout>
    <template #header>
      Manajemen Carousel
    </template>

    <div class="space-y-8 font-['Source_Sans_Pro']">

      <!-- Bagian Upload -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-['Montserrat'] font-bold text-xl text-slate-800 tracking-tight">Upload Gambar Baru</h3>
        </div>

        <div class="relative group cursor-pointer bg-white border-2 border-dashed border-slate-300 hover:border-indigo-500 rounded-2xl p-12 transition-colors duration-300 flex flex-col items-center justify-center text-center">
          <div class="w-16 h-16 bg-slate-100 group-hover:bg-indigo-50 text-slate-400 group-hover:text-indigo-600 rounded-full flex items-center justify-center transition-colors duration-300 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
          </div>
          <p class="text-slate-600 font-semibold text-lg">Tarik & Lepas gambar di sini</p>
          <p class="text-slate-400 text-sm mt-1 mb-6">Atau klik untuk memilih file dari komputer</p>
          <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-xl transition-colors duration-200">
            Pilih File
          </button>
          <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
        </div>
      </section>

      <hr class="border-slate-200" />

      <!-- Bagian Preview Gallery -->
      <section>
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-['Montserrat'] font-bold text-xl text-slate-800 tracking-tight">Galeri Carousel</h3>
          <span class="bg-slate-100 text-slate-600 py-1 px-3 rounded-lg text-sm font-bold border border-slate-200">
            Total: {{ mockSlides.length }} Slide
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

          <div
            v-for="slide in mockSlides"
            :key="slide.id"
            class="group relative bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-indigo-300 transition-colors duration-300 flex flex-col h-full"
          >
            <div class="relative h-48 w-full bg-slate-100 overflow-hidden">
              <img
                :src="slide.image_path"
                alt="Carousel Slide"
                class="w-full h-full object-cover"
              />

              <!-- Hover Overlay -->
              <div class="absolute inset-0 bg-slate-900/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center space-x-3 backdrop-blur-sm">
                <button class="p-2 bg-white text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg transition-colors" title="Edit">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                  </svg>
                </button>
                <button class="p-2 bg-rose-500 text-white hover:bg-rose-600 rounded-lg transition-colors" title="Hapus">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>

              <!-- FIX BADGE: style binding langsung, tidak lewat :class -->
              <div class="absolute top-3 left-3 z-10">
                <span
                  class="text-white text-xs font-bold px-2.5 py-1 rounded-md shadow-sm"
                  :style="{ backgroundColor: slide.is_active ? '#10b981' : '#64748b' }"
                >
                  {{ slide.is_active ? 'Aktif' : 'Non-aktif' }}
                </span>
              </div>
            </div>

            <!-- Card Info -->
            <div class="p-4 flex items-center justify-between bg-white border-t border-slate-100 flex-1">
              <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Urutan</span>
                <span class="text-lg font-['Montserrat'] font-extrabold text-slate-800">#{{ slide.order }}</span>
              </div>

              <!-- FIX SWITCH: backgroundColor dan transform pakai :style semua -->
              <button
                type="button"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                :style="{ backgroundColor: slide.is_active ? '#10b981' : '#cbd5e1' }"
                @click="slide.is_active = !slide.is_active"
                role="switch"
                :aria-checked="slide.is_active.toString()"
              >
                <span
                  class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                  :style="{ transform: slide.is_active ? 'translateX(1.25rem)' : 'translateX(0)' }"
                />
              </button>
            </div>

          </div>

        </div>
      </section>

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const mockSlides = ref([
  {
    id: 1,
    image_path: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&q=80&w=800',
    is_active: true,
    order: 1
  },
  {
    id: 2,
    image_path: 'https://images.unsplash.com/photo-1551107696-a4b0c5a0d9a2?auto=format&fit=crop&q=80&w=800',
    is_active: true,
    order: 2
  },
  {
    id: 3,
    image_path: 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?auto=format&fit=crop&q=80&w=800',
    is_active: false,
    order: 3
  },
  {
    id: 4,
    image_path: 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?auto=format&fit=crop&q=80&w=800',
    is_active: true,
    order: 4
  },
])
</script>
