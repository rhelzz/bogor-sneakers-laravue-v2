<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import CustomSelect from '@/components/admin/ui/CustomSelect.vue';
import CustomInput from '@/components/admin/ui/CustomInput.vue';
import { orders as orderRoutes } from '@/routes/admin';

interface OrderItem {
    id: number;
    order_number: string;
    customer_name: string;
    customer_phone: string;
    total_amount: number;
    status: string;
    status_label: string;
    item_count: number;
    created_at: string;
}

const props = defineProps<{
    orders: OrderItem[];
    statusOptions: Record<string, string>;
}>();

const searchQuery = ref('');
const statusFilter = ref('all');

const filterOptions = computed(() => {
    return {
        'all': 'Semua Status',
        ...props.statusOptions
    };
});

const filteredOrders = computed(() => {
    return props.orders.filter(order => {
        const matchesSearch = 
            order.order_number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            order.customer_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            order.customer_phone.includes(searchQuery.value);
        
        const matchesStatus = statusFilter.value === 'all' || order.status === statusFilter.value;
        
        return matchesSearch && matchesStatus;
    });
});

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
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-end md:justify-between">
                <div class="relative flex-1 max-w-md">
                    <CustomInput 
                        v-model="searchQuery"
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

                <div class="w-full md:w-64">
                    <CustomSelect 
                        v-model="statusFilter"
                        :options="filterOptions"
                        label="Filter Status"
                    />
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-xs font-bold tracking-wider text-slate-500 uppercase">
                                <th class="px-6 py-4">No. Order</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4">Waktu Pesan</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="order in filteredOrders" :key="order.id" class="group transition-colors hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <p class="font-black tracking-wider text-indigo-600">{{ order.order_number }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ order.item_count }} Item</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-800">{{ order.customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ order.customer_phone }}</p>
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
                            <tr v-if="filteredOrders.length === 0">
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
