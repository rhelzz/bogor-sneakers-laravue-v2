<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import CustomDatePicker from '@/components/admin/ui/CustomDatePicker.vue';
import CustomInput from '@/components/admin/ui/CustomInput.vue';
import CustomSelect from '@/components/admin/ui/CustomSelect.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import orderRoutes from '@/routes/admin/orders';
import { debounce } from 'lodash';

interface OrderItem {
    id: number;
    order_number: string;
    customer_name: string;
    customer_phone: string;
    customer_address: string;
    total_amount: number;
    status: string;
    status_label: string;
    item_count: number;
    created_at: string;
    notes?: string;
}

const props = defineProps<{
    orders: OrderItem[];
    statusOptions: Record<string, string>;
    initialFilters?: Record<string, any>;
}>();

const today = new Date().toISOString().split('T')[0];

// Filters State (Synced with Backend)
const filters = ref({
    search: props.initialFilters?.search || '',
    status: props.initialFilters?.status || 'all',
    date_from: props.initialFilters?.date_from || '',
    date_to: props.initialFilters?.date_to || today,
    notes_filter: props.initialFilters?.notes_filter || 'all',
    region: props.initialFilters?.region || 'all',
    item_count: props.initialFilters?.item_count || 'all',
    sort_by: props.initialFilters?.sort_by || 'created_at',
    sort_order: props.initialFilters?.sort_order || 'desc',
});

// Dropdown Options
const statusFilterOptions = computed(() => ({
    'all': 'Semua Status',
    ...props.statusOptions
}));

const itemCountOptions = {
    'all': 'Semua Jumlah',
    '1': '1 Item',
    '2': '2 Item',
    '3+': '3+ Item'
};

const notesOptions = {
    'all': 'Semua Pesanan',
    'with_notes': 'Ada Catatan Khusus',
    'no_notes': 'Tanpa Catatan'
};

const regionOptions = {
    'all': 'Semua Wilayah',
    'bogor': 'Area Bogor',
    'outside': 'Luar Bogor'
};

// Request Logic
const updateFilters = debounce(() => {
    router.get(orderRoutes.index.url(), filters.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300);

// Watch for changes in filters
watch(() => filters.value, () => {
    updateFilters();
}, { deep: true });

const toggleSort = (column: string) => {
    if (filters.value.sort_by === column) {
        filters.value.sort_order = filters.value.sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        filters.value.sort_by = column;
        filters.value.sort_order = 'asc';
    }
};

const resetFilters = () => {
    filters.value = {
        search: '',
        status: 'all',
        date_from: '',
        date_to: today,
        notes_filter: 'all',
        region: 'all',
        item_count: 'all',
        sort_by: 'created_at',
        sort_order: 'desc',
    };
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(amount);
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'unverified': return 'bg-slate-100 text-slate-700 border-slate-200';
        case 'pending': return 'bg-amber-100 text-amber-700 border-amber-200';
        case 'processing': return 'bg-indigo-100 text-indigo-700 border-indigo-200';
        case 'shipped': return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'completed': return 'bg-emerald-100 text-emerald-700 border-emerald-200';
        case 'cancelled': return 'bg-rose-100 text-rose-700 border-rose-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};
</script>

<template>
    <Head title="Manajemen Pesanan" />

    <AdminLayout>
        <template #header> Manajemen Pesanan </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">Total Pesanan</p>
                    <p class="mt-2 text-3xl font-extrabold text-slate-800">{{ orders.length }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-extrabold text-amber-600">{{ orders.filter(o => o.status === 'unverified').length }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">Perlu Dikirim</p>
                    <p class="mt-2 text-3xl font-extrabold text-indigo-600">{{ orders.filter(o => o.status === 'processing').length }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:shadow-md">
                    <p class="text-xs font-bold tracking-widest text-slate-400 uppercase">Selesai</p>
                    <p class="mt-2 text-3xl font-extrabold text-emerald-600">{{ orders.filter(o => o.status === 'completed').length }}</p>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Row 1 -->
                    <div class="lg:col-span-2">
                        <CustomInput
                            v-model="filters.search"
                            label="Cari Pesanan"
                            placeholder="No. Order, Nama, atau Telepon..."
                        >
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </template>
                        </CustomInput>
                    </div>

                    <div>
                        <CustomSelect
                            v-model="filters.status"
                            :options="statusFilterOptions"
                            label="Filter Status"
                        />
                    </div>

                    <div>
                        <CustomSelect
                            v-model="filters.item_count"
                            :options="itemCountOptions"
                            label="Jumlah Item"
                        />
                    </div>

                    <!-- Row 2 -->
                    <div>
                        <CustomDatePicker
                            v-model="filters.date_from"
                            label="Dari Tanggal"
                            placeholder="Mulai"
                            :max-date="filters.date_to || today"
                        />
                    </div>
                    <div>
                        <CustomDatePicker
                            v-model="filters.date_to"
                            label="Sampai Tanggal"
                            placeholder="Selesai"
                            :max-date="today"
                        />
                    </div>
                    <div>
                        <CustomSelect
                            v-model="filters.notes_filter"
                            :options="notesOptions"
                            label="Status Catatan"
                        />
                    </div>
                    <div>
                        <CustomSelect
                            v-model="filters.region"
                            :options="regionOptions"
                            label="Wilayah Pengiriman"
                        />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-slate-50 pt-6">
                    <p class="text-sm font-semibold text-slate-500">
                        Menampilkan <span class="text-indigo-600">{{ orders.length }}</span> Pesanan
                    </p>
                    <button
                        @click="resetFilters"
                        class="group flex items-center space-x-2 rounded-xl bg-slate-50 px-6 py-2.5 text-xs font-bold text-slate-500 transition-all duration-300 hover:bg-rose-50 hover:text-rose-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-500 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>Reset Semua Filter</span>
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold tracking-wider text-slate-500 uppercase">
                                <th @click="toggleSort('order_number')" class="cursor-pointer px-6 py-4 transition-colors hover:bg-slate-100">
                                    <div class="flex items-center space-x-2">
                                        <span>No. Order</span>
                                        <svg v-if="filters.sort_by === 'order_number'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="filters.sort_order === 'asc' ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </div>
                                </th>
                                <th @click="toggleSort('customer_name')" class="cursor-pointer px-6 py-4 transition-colors hover:bg-slate-100">
                                    <div class="flex items-center space-x-2">
                                        <span>Pelanggan</span>
                                        <svg v-if="filters.sort_by === 'customer_name'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="filters.sort_order === 'asc' ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </div>
                                </th>
                                <th @click="toggleSort('created_at')" class="cursor-pointer px-6 py-4 transition-colors hover:bg-slate-100">
                                    <div class="flex items-center space-x-2">
                                        <span>Waktu Pesan</span>
                                        <svg v-if="filters.sort_by === 'created_at'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="filters.sort_order === 'asc' ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </div>
                                </th>
                                <th @click="toggleSort('total_amount')" class="cursor-pointer px-6 py-4 transition-colors hover:bg-slate-100">
                                    <div class="flex items-center space-x-2">
                                        <span>Total</span>
                                        <svg v-if="filters.sort_by === 'total_amount'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" :class="filters.sort_order === 'asc' ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 relative">
                            <tr v-for="order in orders" :key="order.id" class="group transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <p class="font-black tracking-wider text-indigo-600">{{ order.order_number }}</p>
                                        <div v-if="order.notes" class="group relative">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                            </svg>
                                            <div class="invisible absolute bottom-full left-0 z-50 mb-2 w-48 rounded-lg bg-slate-900 p-2 text-[10px] font-bold text-white opacity-0 transition-all group-hover:visible group-hover:opacity-100">
                                                {{ order.notes }}
                                                <div class="absolute top-full left-2 -mt-1 border-4 border-transparent border-t-slate-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400">{{ order.item_count }} Item</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-800">{{ order.customer_name }}</p>
                                    <p class="max-w-[180px] truncate text-[10px] font-medium text-slate-400" :title="order.customer_address">
                                        {{ order.customer_address }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-slate-600">{{ order.created_at }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-black text-slate-900">{{ formatCurrency(order.total_amount) }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="inline-flex items-center rounded-full border px-3 py-1.5 text-[10px] font-bold tracking-wider uppercase shadow-sm"
                                        :class="getStatusBadge(order.status)"
                                    >
                                        <span class="mr-2 h-1.5 w-1.5 rounded-full bg-current animate-pulse"></span>
                                        {{ order.status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end">
                                        <Link
                                            :href="orderRoutes.show.url({ order: order.id })"
                                            class="flex items-center space-x-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition-all duration-300 hover:bg-indigo-600 hover:shadow-indigo-200"
                                        >
                                            <span>Detail</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="orders.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="font-bold">Tidak ada data pesanan ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
