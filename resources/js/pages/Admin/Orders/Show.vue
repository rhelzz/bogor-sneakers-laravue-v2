<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import orderRoutes from '@/routes/admin/orders';

interface OrderItemDetail {
    id: number;
    product_name: string;
    size: string;
    price: number;
    quantity: number;
    subtotal: number;
}

interface OrderDetail {
    id: number;
    order_number: string;
    customer_name: string;
    customer_phone: string;
    customer_address: string;
    notes: string | null;
    subtotal: number;
    shipping_cost: number;
    total_amount: number;
    status: string;
    status_label: string;
    created_at: string;
    items: OrderItemDetail[];
}

const props = defineProps<{
    order?: OrderDetail;
    statusOptions?: Record<string, string>;
}>();

const dummyOrder: OrderDetail = {
    id: 1,
    order_number: 'BGS-202405-0001',
    customer_name: 'Budi Santoso',
    customer_phone: '081234567890',
    customer_address: 'Jl. Jenderal Sudirman No. 123, Bogor Tengah, Kota Bogor, Jawa Barat 16121',
    notes: 'Tolong dipacking kayu dan bubble wrap tebal ya min.',
    subtotal: 1500000,
    shipping_cost: 0,
    total_amount: 1500000,
    status: 'unverified',
    status_label: 'Menunggu Verifikasi',
    created_at: '15 Mei 2024, 10:00',
    items: [
        {
            id: 101,
            product_name: 'Nike Air Jordan 1 Retro High OG',
            size: '42',
            price: 1500000,
            quantity: 1,
            subtotal: 1500000,
        }
    ]
};

const dummyStatusOptions = {
    'unverified': 'Menunggu Verifikasi',
    'pending': 'Menunggu Pembayaran',
    'processing': 'Sedang Diproses',
    'shipped': 'Dalam Pengiriman',
    'completed': 'Selesai',
    'cancelled': 'Dibatalkan',
};

const orderData = computed(() => props.order || dummyOrder);
const statusOptionsList = computed(() => props.statusOptions || dummyStatusOptions);

const isUpdating = ref(false);

const updateStatus = (newStatus: string) => {
    if (confirm(`Apakah Anda yakin ingin mengubah status pesanan menjadi "${statusOptionsList.value[newStatus]}"?`)) {
        isUpdating.value = true;
        router.patch(orderRoutes.updateStatus.url({ order: orderData.value.id }), {
            status: newStatus
        }, {
            onFinish: () => isUpdating.value = false
        });
    }
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

const openWhatsApp = () => {
    const message = `Halo Kak ${orderData.value.customer_name}, kami dari Bogor Sneakers ingin mengonfirmasi pesanan #${orderData.value.order_number}...`;
    window.open(`https://wa.me/${orderData.value.customer_phone}?text=${encodeURIComponent(message)}`, '_blank');
};
</script>

<template>
    <Head :title="`Detail Pesanan ${orderData.order_number}`" />

    <AdminLayout>
        <template #header> Detail Pesanan </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Header Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <Link 
                        :href="orderRoutes.index.url()"
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition-all hover:bg-slate-50 hover:text-slate-800"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight">{{ orderData.order_number }}</h1>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ orderData.created_at }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button 
                        @click="openWhatsApp"
                        class="flex items-center space-x-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition-all hover:bg-emerald-700 hover:shadow-emerald-200"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span>Hubungi Pelanggan</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column: Details -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Order Items -->
                    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                            <h3 class="font-bold text-slate-800">Daftar Produk</h3>
                        </div>
                        <div class="p-0">
                            <table class="w-full border-collapse text-left text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100 bg-slate-50/30 text-[10px] font-bold tracking-widest text-slate-400 uppercase">
                                        <th class="px-6 py-3">Produk</th>
                                        <th class="px-6 py-3 text-center">Size</th>
                                        <th class="px-6 py-3 text-right">Harga</th>
                                        <th class="px-6 py-3 text-center">Jumlah</th>
                                        <th class="px-6 py-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    <tr v-for="item in orderData.items" :key="item.id" class="transition-colors hover:bg-slate-50">
                                        <td class="px-6 py-4">
                                            <p class="font-bold text-slate-800">{{ item.product_name }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-600">
                                                {{ item.size }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-slate-600">
                                            {{ formatCurrency(item.price) }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-slate-600">
                                            x{{ item.quantity }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-slate-900">
                                            {{ formatCurrency(item.subtotal) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50/30">
                                        <td colspan="4" class="px-6 py-4 text-right font-bold text-slate-500">Subtotal</td>
                                        <td class="px-6 py-4 text-right font-black text-slate-900">{{ formatCurrency(orderData.subtotal) }}</td>
                                    </tr>
                                    <tr class="bg-slate-50/30">
                                        <td colspan="4" class="px-6 py-4 text-right font-bold text-slate-500">Biaya Pengiriman</td>
                                        <td class="px-6 py-4 text-right font-black text-slate-900">{{ formatCurrency(orderData.shipping_cost) }}</td>
                                    </tr>
                                    <tr class="bg-indigo-50/50">
                                        <td colspan="4" class="px-6 py-4 text-right font-bold text-indigo-600 uppercase tracking-wider text-xs">Total Akhir</td>
                                        <td class="px-6 py-4 text-right text-lg font-black text-indigo-600">{{ formatCurrency(orderData.total_amount) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-slate-400">Informasi Pelanggan</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap</p>
                                    <p class="font-bold text-slate-800">{{ orderData.customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">WhatsApp / Telepon</p>
                                    <p class="font-bold text-indigo-600">{{ orderData.customer_phone }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-slate-400">Alamat Pengiriman</h3>
                            <p class="text-sm font-semibold leading-relaxed text-slate-600">{{ orderData.customer_address }}</p>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div v-if="orderData.notes" class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                        <h3 class="mb-2 text-xs font-black uppercase tracking-widest text-amber-600">Catatan Pesanan</h3>
                        <p class="text-sm font-bold text-amber-800 italic">"{{ orderData.notes }}"</p>
                    </div>
                </div>

                <!-- Right Column: Status & Actions -->
                <div class="space-y-6">
                    <!-- Current Status -->
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
                        <h3 class="mb-4 text-xs font-black uppercase tracking-widest text-slate-400">Status Saat Ini</h3>
                        <div 
                            class="inline-flex items-center rounded-2xl border px-6 py-3 text-xs font-black tracking-wider uppercase shadow-md"
                            :class="getStatusBadge(orderData.status)"
                        >
                            <span class="mr-3 h-2 w-2 rounded-full bg-current animate-pulse"></span>
                            {{ orderData.status_label }}
                        </div>
                    </div>

                    <!-- Change Status -->
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-5 text-xs font-black uppercase tracking-widest text-slate-400">Perbarui Status</h3>
                        <div class="grid grid-cols-1 gap-2">
                            <button 
                                v-for="(label, status) in statusOptionsList" 
                                :key="status"
                                @click="updateStatus(status as string)"
                                :disabled="isUpdating || orderData.status === status"
                                class="flex items-center justify-between rounded-xl border p-3.5 text-left text-xs font-bold transition-all duration-300"
                                :class="[
                                    orderData.status === status 
                                        ? 'border-indigo-600 bg-indigo-50 text-indigo-700' 
                                        : 'border-slate-100 bg-slate-50 text-slate-600 hover:border-slate-300 hover:bg-white hover:shadow-sm'
                                ]"
                            >
                                <span>{{ label }}</span>
                                <svg v-if="orderData.status === status" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <p class="mt-4 text-[10px] font-bold text-center text-slate-400">Pembaruan status akan mengirimkan sinyal ke database.</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
