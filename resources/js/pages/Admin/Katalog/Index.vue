<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { katalog } from '@/routes/admin';

interface CatalogItem {
    id: number;
    shoe_model_id: number | null;
    shoe_model_name: string | null;
    name: string;
    collection: string;
    price: number;
    status: 'ready' | 'po' | 'habis';
    card_image_url: string | null;
    is_active: boolean;
}

const props = defineProps<{
    catalogs: CatalogItem[];
}>();

const viewMode = ref<'table' | 'card'>('table');
const searchQuery = ref('');

const filteredCatalogs = computed(() => {
    if (!searchQuery.value) return props.catalogs;
    const query = searchQuery.value.toLowerCase();
    return props.catalogs.filter(item => 
        item.name.toLowerCase().includes(query) || 
        item.collection.toLowerCase().includes(query)
    );
});

const formatCurrency = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(price);
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'ready': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'po': return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'habis': return 'bg-rose-100 text-rose-700 border-rose-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

const getStatusLabel = (status: string) => {
    switch (status) {
        case 'ready': return 'Ready Stok';
        case 'po': return 'Pre-Order';
        case 'habis': return 'Stok Habis';
        default: return status;
    }
};
</script>

<template>
    <Head title="Manajemen Katalog" />

    <AdminLayout>
        <template #header> Manajemen Katalog </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Top Action Bar -->
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center md:justify-between">
                <div class="flex flex-1 items-center space-x-4">
                    <div class="relative w-full max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input 
                            v-model="searchQuery"
                            type="text" 
                            placeholder="Cari nama produk atau koleksi..." 
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm outline-none transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10"
                        />
                    </div>
                    <span class="hidden rounded-lg bg-indigo-50 px-3 py-1 text-sm font-bold text-indigo-700 md:block">
                        Total: {{ catalogs.length }} Produk
                    </span>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <!-- View Toggle -->
                    <div class="flex rounded-xl border border-slate-200 bg-slate-100 p-1">
                        <button
                            @click="viewMode = 'table'"
                            :class="viewMode === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="rounded-lg p-2 transition-all duration-200"
                            title="Mode Tabel"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <button
                            @click="viewMode = 'card'"
                            :class="viewMode === 'card' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                            class="rounded-lg p-2 transition-all duration-200"
                            title="Mode Card"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                    </div>

                    <Link
                        :href="katalog.create.url()"
                        class="flex items-center space-x-2 rounded-xl bg-indigo-600 px-5 py-2.5 font-bold text-white shadow-sm transition-all duration-200 hover:bg-indigo-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Produk</span>
                    </Link>
                </div>
            </div>

            <!-- Table View -->
            <div v-if="viewMode === 'table'" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold tracking-wider text-slate-500 uppercase">
                                <th class="px-6 py-4">Thumbnail</th>
                                <th class="px-6 py-4">Informasi Produk</th>
                                <th class="px-6 py-4">Koleksi</th>
                                <th class="px-6 py-4">Harga</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in filteredCatalogs" :key="item.id" class="group transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="h-16 w-16 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                        <img v-if="item.card_image_url" :src="item.card_image_url" :alt="item.name" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <p class="font-bold text-slate-800">{{ item.name }}</p>
                                        <div v-if="item.shoe_model_name" class="mt-0.5 flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">{{ item.shoe_model_name }}</span>
                                        </div>
                                        <p v-else class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter italic">No Model</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        {{ item.collection }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-900">{{ formatCurrency(item.price) }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span 
                                        class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase shadow-sm"
                                        :class="getStatusBadge(item.status)"
                                    >
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ getStatusLabel(item.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-600" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button class="rounded-lg border border-slate-200 p-2 text-slate-400 transition-all hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredCatalogs.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    Data katalog tidak ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card View -->
            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div v-for="item in filteredCatalogs" :key="item.id" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                    <div class="relative aspect-square w-full overflow-hidden bg-slate-50">
                        <img v-if="item.card_image_url" :src="item.card_image_url" :alt="item.name" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                        <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span 
                                class="inline-flex items-center rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase shadow-md backdrop-blur-sm"
                                :class="getStatusBadge(item.status)"
                            >
                                {{ getStatusLabel(item.status) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="mb-1 flex items-center justify-between">
                            <p class="text-[11px] font-bold tracking-widest text-indigo-600 uppercase">{{ item.collection }}</p>
                            <span v-if="item.shoe_model_name" class="rounded bg-indigo-50 px-1.5 py-0.5 text-[9px] font-extrabold text-indigo-600 uppercase">{{ item.shoe_model_name }}</span>
                        </div>
                        <h4 class="mb-2 line-clamp-1 font-bold text-slate-800">{{ item.name }}</h4>
                        <div class="flex items-center justify-between border-t border-slate-100 pt-4">
                            <p class="font-bold text-slate-900">{{ formatCurrency(item.price) }}</p>
                            <div class="flex space-x-1">
                                <button class="rounded-lg border border-slate-100 bg-slate-50 p-1.5 text-slate-400 transition-all hover:bg-white hover:text-indigo-600" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button class="rounded-lg border border-slate-100 bg-slate-50 p-1.5 text-slate-400 transition-all hover:bg-white hover:text-rose-600" title="Hapus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.font-montserrat {
    font-family: 'Montserrat', sans-serif;
}
.font-source-sans {
    font-family: 'Source Sans Pro', sans-serif;
}
</style>
