<template>
  <AdminLayout>
    <template #header>
      Galeri Karya
    </template>

    <div class="space-y-8 font-['Source_Sans_Pro']">
      <!-- Header Actions -->
      <div class="flex items-center justify-between">
        <div>
          <h3 class="font-['Montserrat'] font-bold text-xl text-slate-800 tracking-tight">Koleksi Galeri</h3>
          <p class="text-sm font-semibold text-slate-500 mt-1">Kelola tampilan visual utama galeri Anda.</p>
        </div>
        <button 
          @click="openCreateModal"
          :disabled="isMaxSlots"
          :class="isMaxSlots ? 'bg-slate-400 cursor-not-allowed text-white font-bold py-2.5 px-6 rounded-xl shadow-sm flex items-center space-x-2' : 'bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-xl transition-all duration-200 shadow-sm flex items-center space-x-2'"
        >
          <svg v-if="!isMaxSlots" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
          </svg>
          <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
          <span>{{ isMaxSlots ? 'Sudah Maksimal' : 'Tambah Karya' }}</span>
        </button>
      </div>

      <!-- Grid Display -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="slot in slots" 
          :key="slot.id" 
          class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:border-indigo-300 transition-all duration-300"
        >
          <div class="relative aspect-square bg-slate-100 overflow-hidden">
            <img 
              v-if="slot.image_url" 
              :src="slot.image_url" 
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
            />
            <div v-else class="w-full h-full flex flex-col items-center justify-center text-slate-300 space-y-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
              </svg>
              <span class="text-xs font-bold uppercase tracking-widest">Kosong</span>
            </div>
            
            <!-- Overlay Actions -->
            <div v-if="slot.image_url" class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-3 backdrop-blur-[2px]">
              <button @click="openEditModal(slot)" class="p-2.5 bg-white text-slate-800 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors" title="Ubah Karya">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button @click="destroy(slot.id)" class="p-2.5 bg-rose-500 text-white rounded-xl hover:bg-rose-600 transition-colors" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>

            <!-- Slot Number Badge -->
            <div class="absolute top-3 left-3 px-2 py-1 bg-white/90 backdrop-blur-sm text-[10px] font-extrabold text-slate-800 rounded-lg shadow-sm border border-slate-100">
              SLOT {{ slot.slot }}
            </div>
          </div>
          <div class="p-4 border-t border-slate-50">
            <p class="text-sm font-bold text-slate-800 truncate mb-0.5">{{ slot.title }}</p>
            <p class="text-xs font-semibold text-slate-400 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              {{ slot.author }}
            </p>
          </div>
        </div>
      </div>

      <!-- Modal Management -->
      <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="closeModal"></div>
          
          <!-- Modal Content -->
          <Transition
            enter-active-class="transition duration-300 ease-out delay-75"
            enter-from-class="opacity-0 translate-y-8 scale-95"
            enter-to-class="opacity-100 translate-y-0 scale-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 scale-100"
            leave-to-class="opacity-0 translate-y-8 scale-95"
          >
            <div v-if="isModalOpen" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col">
              <div class="flex items-center justify-between px-6 py-5 border-b border-slate-50 bg-slate-50/50">
                <h3 class="font-['Montserrat'] font-bold text-lg text-slate-800">{{ editingSlotId ? 'Ubah Karya' : 'Tambah Karya Baru' }}</h3>
                <button @click="closeModal" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div class="p-6 space-y-5">
                <!-- Info Box -->
                <div v-if="!editingSlotId" class="p-4 bg-amber-50 border border-amber-100 rounded-xl flex items-center space-x-3">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <p class="text-sm font-semibold text-amber-700">Karya akan ditambahkan ke slot kosong pertama.</p>
                </div>

                <!-- Drag & Drop Area -->
                <div class="relative overflow-hidden group border-2 border-dashed border-slate-200 rounded-2xl p-10 flex flex-col items-center justify-center text-center hover:border-indigo-500 hover:bg-indigo-50/30 transition-all cursor-pointer min-h-[200px]">
                  <img v-if="previewUrl" :src="previewUrl" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-30 transition-opacity z-0" />
                  <div class="relative z-10 p-3 bg-slate-50/90 backdrop-blur-sm group-hover:bg-white rounded-xl mb-3 shadow-sm transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
                    </svg>
                  </div>
                  <p class="relative z-10 text-sm font-bold text-slate-800">Unggah Gambar</p>
                  <p class="relative z-10 text-xs font-semibold text-slate-600 mt-1">PNG atau JPG, maksimal 5MB</p>
                  <input type="file" @change="e => handleFileSelect((e.target as HTMLInputElement).files?.[0] as File)" accept="image/jpeg,image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" />
                </div>
                <p v-if="form.errors.image" class="text-xs text-rose-500 font-semibold">{{ form.errors.image }}</p>

                <div class="space-y-4">
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Nama Judul</label>
                    <input 
                      v-model="form.title"
                      type="text" 
                      placeholder="Contoh: Air Max 97 Bogor Edition" 
                      class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold"
                      :class="{ 'border-rose-500': form.errors.title }"
                    />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.title }}</p>
                  </div>
                  <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Nama Author</label>
                    <input 
                      v-model="form.author"
                      type="text" 
                      placeholder="Contoh: @bogorsneakers" 
                      class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold"
                      :class="{ 'border-rose-500': form.errors.author }"
                    />
                    <p v-if="form.errors.author" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.author }}</p>
                  </div>
                </div>
              </div>

              <div class="px-6 py-5 border-t border-slate-50 bg-slate-50/50 flex items-center justify-end space-x-3">
                <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors">Batal</button>
                <button 
                  @click="() => submit(slots)" 
                  :disabled="form.processing"
                  class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-bold rounded-xl transition-all shadow-sm"
                >
                  {{ form.processing ? 'Menyimpan...' : (editingSlotId ? 'Simpan Perubahan' : 'Simpan Karya') }}
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useGaleriKarya } from '@/composables/Admin/useGaleriKarya';
import type { GallerySlot } from '@/types/gallery';

const props = defineProps<{
    slots: GallerySlot[];
}>();

const {
    isModalOpen,
    editingSlotId,
    form,
    previewUrl,
    openCreateModal,
    openEditModal,
    closeModal,
    handleFileSelect,
    submit,
    destroy,
} = useGaleriKarya();

const activeImagesCount = computed(() => 
    props.slots.filter(s => s.image_url !== null).length
);

const isMaxSlots = computed(() => activeImagesCount.value >= 8);
</script>
