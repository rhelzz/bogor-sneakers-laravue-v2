<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import CustomSelect from '@/components/admin/ui/CustomSelect.vue';
import { orders as orderRoutes } from '@/routes/admin';

interface OrderItem {
    id: number;
    product_name: string;
    size: string;
    price: number;
    quantity: number;
    subtotal: number;
    image_url: string | null;
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
    items: OrderItem[];
}

const props = defineProps<{
    order: OrderDetail;
    statusOptions: Record<string, string>;
}>();

const form = useForm({
    status: props.order.status
});

const isUpdating = ref(false);

const updateStatus = () => {
    isUpdating.value = true;
    form.patch(orderRoutes.updateStatus.url({ order: props.order.id }), {
        preserveScroll: true,
        onSuccess: () => {
            isUpdating.value = false;
        },
        onError: () => {
            isUpdating.value = false;
        }
    });
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
    <Head :title="`Detail Pesanan ${order.order_number}`" />

    <AdminLayout>
        <template #header> Detail Pesanan </template>

        <div class="space-y-6 font-['Source_Sans_Pro']">
            <!-- Back Button & Quick Actions -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <Link 
                    :href="orderRoutes.url()"
                    class="flex items-center space-x-2 text-sm font-bold text-slate-400 transition-colors hover:text-indigo-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </Link>

                <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm">
                    <span 
                        class="inline-flex items-center rounded-xl border px-4 py-2 text-xs font-bold tracking-wider uppercase shadow-sm"
                        :class="getStatusBadge(order.status)"
                    >
                        {{ order.status_label }}
                    </span>
                    <div class="h-6 w-px bg-slate-100"></div>
                    <a 
                        :href="`https://wa.me/${order.customer_phone.replace(/^0/, '62')}`" 
                        target="_blank"
                        class="flex items-center space-x-2 rounded-xl bg-emerald-500 px-4 py-2 text-xs font-bold text-white transition-all hover:bg-emerald-600 hover:shadow-lg hover:shadow-emerald-200"
                    >
                        <i class="bi bi-whatsapp"></i>
                        <span>Hubungi WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column: Order Items & Customer Info -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Items Card -->
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:shadow-md">
                        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                            <h3 class="text-sm font-bold tracking-widest text-slate-800 uppercase">Daftar Produk</h3>
                        </div>
                        <div class="divide-y divide-slate-100 px-6">
                            <div v-for="item in order.items" :key="item.id" class="group flex items-center gap-4 py-6">
                                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50">
                                    <img v-if="item.image_url" :src="item.image_url" :alt="item.product_name" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                    <div v-else class="flex h-full w-full items-center justify-center text-slate-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-800">{{ item.product_name }}</h4>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Size {{ item.size }}</p>
                                    <p class="mt-1 text-sm font-medium text-slate-500">{{ item.quantity }} x {{ formatCurrency(item.price) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-black text-slate-900">{{ formatCurrency(item.subtotal) }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Summary In Card -->
                        <div class="border-t border-slate-100 bg-slate-50/30 p-6">
                            <div class="space-y-2">
                                <div class="flex justify-between text-sm font-medium text-slate-500">
                                    <span>Subtotal</span>
                                    <span>{{ formatCurrency(order.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm font-medium text-slate-500">
                                    <span>Ongkos Kirim</span>
                                    <span>{{ formatCurrency(order.shipping_cost) }}</span>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 pt-4">
                                    <span class="text-lg font-bold text-slate-800">Total Pembayaran</span>
                                    <span class="text-xl font-black text-indigo-600">{{ formatCurrency(order.total_amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Detail Card -->
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-md">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-sm font-bold tracking-widest text-slate-400 uppercase">Informasi Pengiriman</h3>
                            <div class="h-10 w-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Nama Lengkap</p>
                                    <p class="mt-1 font-bold text-slate-800">{{ order.customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Nomor Telepon</p>
                                    <p class="mt-1 font-bold text-slate-800">{{ order.customer_phone }}</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">Alamat Lengkap</p>
                                <p class="mt-1 text-sm font-medium leading-relaxed text-slate-600">{{ order.customer_address }}</p>
                            </div>
                        </div>
                        <div v-if="order.notes" class="mt-8 rounded-2xl bg-amber-50 p-4 border border-amber-100">
                            <p class="text-[10px] font-bold tracking-widest text-amber-600 uppercase mb-1">Catatan Pembeli</p>
                            <p class="text-sm font-medium text-amber-800 italic">"{{ order.notes }}"</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Status Update -->
                <div class="space-y-6">
                    <div class="sticky top-28 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-md">
                        <div class="mb-6">
                            <h3 class="text-sm font-bold tracking-widest text-slate-800 uppercase">Kelola Status</h3>
                            <p class="mt-1 text-xs font-medium text-slate-500">Update tahapan pesanan di sini.</p>
                        </div>

                        <div class="space-y-6">
                            <CustomSelect 
                                v-model="form.status"
                                :options="statusOptions"
                                label="Status Pesanan"
                                @change="updateStatus"
                            />

                            <div class="rounded-2xl bg-indigo-50 p-5 border border-indigo-100">
                                <p class="text-[10px] font-bold tracking-widest text-indigo-600 uppercase mb-2">Metadata Pesanan</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-[11px] font-bold">
                                        <span class="text-slate-400">ID Pesanan:</span>
                                        <span class="text-slate-700">#{{ order.id }}</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-bold">
                                        <span class="text-slate-400">Waktu:</span>
                                        <span class="text-slate-700">{{ order.created_at }}</span>
                                    </div>
                                </div>
                            </div>

                            <button 
                                v-if="form.isDirty"
                                @click="updateStatus"
                                :disabled="isUpdating"
                                class="w-full rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition-all duration-300 hover:bg-indigo-700 disabled:opacity-50"
                            >
                                <span v-if="isUpdating">Menyimpan...</span>
                                <span v-else>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Print Info -->
                    <div class="rounded-3xl border border-slate-200 border-dashed bg-white p-6 text-center">
                        <button class="flex w-full items-center justify-center gap-2 text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Label Pengiriman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
