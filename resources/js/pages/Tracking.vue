<template>
    <div class="font-body bg-washi text-sumi antialiased">
        <Head title="Tracking Pesanan" />
        <FloatingMenuNav current-page="tracking" />
        <FloatingAdminPanel
            :contacts="contacts"
            title="HUBUNGI FARHAN"
            subtitle="Customer Service & Tracking"
        />

        <!-- TRACKING HEADER -->
        <section
            class="pattern-shippou relative flex min-h-screen flex-col justify-center overflow-hidden px-6 pt-32 pb-24 lg:px-16"
        >
            <div class="mx-auto w-full max-w-7xl">
                <!-- Header -->
                <div class="accent-top mb-16 pb-10">
                    <div class="animate-slide-up mb-4 flex items-center gap-3">
                        <span
                            class="animate-pulse-soft h-2 w-2 rounded-full bg-indigo"
                        ></span>
                        <span class="text-xs tracking-widest text-hai"
                            >ORDER TRACKING SYSTEM</span
                        >
                    </div>
                    <h1
                        class="font-heading animate-slide-up mb-6 text-5xl leading-none font-bold tracking-tight lg:text-7xl"
                        style="animation-delay: 0.1s"
                    >
                        Lacak<br />
                        <span class="text-indigo">Pesanan Mu</span>
                    </h1>
                    <p
                        class="animate-slide-up mb-8 max-w-md leading-relaxed text-hai"
                        style="animation-delay: 0.2s"
                    >
                        Monitor status pesanan real-time. Dari produksi hingga
                        sampai ke tangan Anda.
                    </p>
                </div>

                <!-- Search Box -->
                <div
                    class="animate-slide-up max-w-2xl"
                    style="animation-delay: 0.3s"
                >
                    <div class="mb-4 flex gap-2">
                        <input
                            v-model="searchId"
                            type="text"
                            placeholder="Masukkan nomor order (contoh: BGS-2841)"
                            class="flex-1 rounded-full border border-sumi/10 bg-washi px-6 py-4 text-sm placeholder-hai/50 focus:border-transparent focus:ring-2 focus:ring-indigo focus:outline-none"
                        />
                        <button
                            @click="searchOrder"
                            class="rounded-full bg-indigo px-8 py-4 text-sm font-bold tracking-wider text-washi transition-all hover:opacity-90"
                        >
                            Cari
                        </button>
                    </div>
                    <p class="text-xs text-hai">
                        Gunakan nomor order untuk tracking pesanan Anda
                    </p>
                </div>
            </div>
        </section>

        <!-- ACTIVE ORDERS -->
        <section class="bg-shironeri px-6 py-24 lg:px-16">
            <div class="mx-auto max-w-7xl">
                <h2 class="font-heading mb-12 text-4xl font-bold">
                    Pesanan Aktif
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    <div
                        v-for="(order, idx) in activeOrders"
                        :key="idx"
                        class="card-lift animate-slide-up rounded-3xl border border-sumi/5 bg-washi p-8"
                        :style="{ animationDelay: `${idx * 0.1}s` }"
                    >
                        <!-- Order Header -->
                        <div
                            class="mb-6 flex items-center justify-between border-b border-sumi/10 pb-6"
                        >
                            <div>
                                <p class="mb-1 text-xs tracking-wider text-hai">
                                    NOMOR PESANAN
                                </p>
                                <p class="text-2xl font-bold">{{ order.id }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-xs tracking-wider text-hai">
                                    STATUS
                                </p>
                                <span
                                    :class="`rounded-full px-4 py-2 text-sm font-bold ${order.statusClass}`"
                                    >{{ order.status }}</span
                                >
                            </div>
                            <div>
                                <p class="mb-1 text-xs tracking-wider text-hai">
                                    PRODUK
                                </p>
                                <p class="font-bold">{{ order.product }}</p>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-6">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-xs tracking-wider text-hai">
                                    PROGRESS
                                </p>
                                <p class="text-sm font-bold">
                                    {{ order.progress }}%
                                </p>
                            </div>
                            <div
                                class="h-2 overflow-hidden rounded-full bg-sumi/10"
                            >
                                <div
                                    :class="`h-full rounded-full transition-all duration-500 ${order.progressClass}`"
                                    :style="`width: ${order.progress}%`"
                                ></div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="space-y-3">
                            <div
                                v-for="(step, stepIdx) in order.timeline"
                                :key="stepIdx"
                                class="flex gap-4"
                            >
                                <div class="flex flex-col items-center">
                                    <div
                                        :class="`flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold ${step.completed ? 'bg-indigo text-washi' : 'bg-sumi/10 text-hai'}`"
                                    >
                                        <i
                                            v-if="step.completed"
                                            class="bi bi-check"
                                        ></i>
                                        <span v-else>{{ stepIdx + 1 }}</span>
                                    </div>
                                    <div
                                        v-if="
                                            stepIdx < order.timeline.length - 1
                                        "
                                        class="mt-1 h-8 w-0.5 bg-sumi/10"
                                    ></div>
                                </div>
                                <div class="pt-1">
                                    <p
                                        :class="[
                                            'font-bold',
                                            step.completed
                                                ? 'text-sumi'
                                                : 'text-hai',
                                        ]"
                                    >
                                        {{ step.title }}
                                    </p>
                                    <p class="text-xs text-hai">
                                        {{ step.date }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="mt-6 border-t border-sumi/10 pt-6">
                            <p class="mb-2 text-xs text-hai">
                                Ada pertanyaan? Hubungi Farhan via WhatsApp
                            </p>
                            <a
                                href="https://wa.me/6289876543210"
                                target="_blank"
                                class="inline-flex items-center gap-2 text-sm font-bold text-indigo transition-colors hover:text-indigo/70"
                            >
                                <i class="bi bi-whatsapp"></i>
                                Chat Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="activeOrders.length === 0" class="py-12 text-center">
                    <i
                        class="bi bi-box-seam mb-4 block text-6xl text-hai/20"
                    ></i>
                    <p class="text-lg text-hai">Tidak ada pesanan aktif</p>
                    <p class="text-sm text-hai">
                        Gunakan search untuk mencari pesanan Anda
                    </p>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section class="bg-washi px-6 py-24 lg:px-16">
            <div class="mx-auto max-w-4xl">
                <h2 class="font-heading mb-12 text-center text-4xl font-bold">
                    Pertanyaan Umum
                </h2>

                <div class="space-y-4">
                    <div
                        v-for="(faq, idx) in faqs"
                        :key="idx"
                        class="card-lift cursor-pointer rounded-2xl bg-shironeri p-6 transition-all hover:shadow-lg"
                        @click="faq.open = !faq.open"
                    >
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-bold">{{ faq.question }}</p>
                            <i
                                :class="`bi ${faq.open ? 'bi-chevron-up' : 'bi-chevron-down'} transition-transform`"
                            ></i>
                        </div>
                        <p
                            v-if="faq.open"
                            class="mt-4 text-sm leading-relaxed text-hai"
                        >
                            {{ faq.answer }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <Footer />
    </div>
</template>

<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import Footer from '@/components/sections/Footer.vue';
import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue';
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue';
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui';

// State Management
const searchId = ref('');
const page = usePage();

const contacts = computed<FloatingContact[]>(() => {
    const sharedContacts = page.props.floatingContacts;

    if (!Array.isArray(sharedContacts)) {
        return [];
    }

    return sharedContacts as FloatingContact[];
});

const orders = ref<FloatingOrder[]>([
    {
        id: '#BGS-2841',
        product: 'Nike Air Max 97 Silver',
        size: '42',
        status: 'Produksi',
        statusClass:
            'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700',
        progress: 55,
        progressClass: 'bg-sumi',
    },
    {
        id: '#BGS-2790',
        product: 'Adidas Samba OG White',
        size: '40',
        status: 'Dikirim',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700',
        progress: 85,
        progressClass: 'bg-sumi',
    },
    {
        id: '#BGS-2755',
        product: 'Jordan 1 Retro High Bred',
        size: '43',
        status: 'Selesai',
        statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha',
        progress: 100,
        progressClass: 'bg-matcha',
    },
]);

// Active Orders
const activeOrders = ref([
    {
        id: '#BGS-2841',
        product: 'Nike Air Max 97 Silver',
        size: '42',
        status: 'Produksi',
        statusClass: 'bg-amber-100 text-amber-700',
        progress: 55,
        progressClass: 'bg-sumi',
        timeline: [
            {
                title: 'Pesanan Diterima',
                date: '1 April 2025',
                completed: true,
            },
            {
                title: 'Verifikasi Payment',
                date: '1 April 2025',
                completed: true,
            },
            { title: 'Produksi', date: 'Sedang berlangsung', completed: false },
            { title: 'QC & Packing', date: 'Tunggu update', completed: false },
            { title: 'Dikirim', date: 'Tunggu pengiriman', completed: false },
        ],
    },
    {
        id: '#BGS-2790',
        product: 'Adidas Samba OG White',
        size: '40',
        status: 'Dikirim',
        statusClass: 'bg-blue-100 text-blue-700',
        progress: 85,
        progressClass: 'bg-indigo',
        timeline: [
            {
                title: 'Pesanan Diterima',
                date: '28 Maret 2025',
                completed: true,
            },
            {
                title: 'Verifikasi Payment',
                date: '28 Maret 2025',
                completed: true,
            },
            {
                title: 'Produksi Selesai',
                date: '31 Maret 2025',
                completed: true,
            },
            {
                title: 'Dalam Perjalanan',
                date: 'Sedang berlangsung',
                completed: false,
            },
            {
                title: 'Sampai Tujuan',
                date: 'Tunggu kedatangan',
                completed: false,
            },
        ],
    },
]);

// FAQs
const faqs = ref([
    {
        question: 'Berapa lama waktu produksi?',
        answer: 'Waktu produksi normal adalah 7-14 hari kerja tergantung model dan ketersediaan komponen. Ready stock biasanya langsung diproses pengiriman.',
        open: false,
    },
    {
        question: 'Bagaimana cara lacak pesanan?',
        answer: 'Anda bisa lacak pesanan menggunakan nomor order (contoh: BGS-2841) di halaman ini. Atau hubungi Farhan via WhatsApp untuk info real-time.',
        open: false,
    },
    {
        question: 'Apa yang dilakukan saat status "QC & Packing"?',
        answer: 'Pada tahap ini sepatu dicek kualitasnya, dibersihkan, dikemas dengan rapi dalam box, dan siap untuk pengiriman. Foto QC akan dikirim sebelum pengiriman.',
        open: false,
    },
    {
        question: 'Apakah bisa cancel atau ubah pesanan?',
        answer: 'Untuk pesanan yang belum di-produksi, bisa dicancel dengan refund penuh. Hubungi CS kami secepatnya jika ingin mengubah atau cancel.',
        open: false,
    },
    {
        question: 'Bagaimana jika sepatu rusak saat pengiriman?',
        answer: 'Kami menggunakan packing bubble wrap + box tebal untuk meminimalkan risiko. Jika terjadi kerusakan, dokumentasikan dengan foto dan hubungi Farhan untuk klaim.',
        open: false,
    },
]);

// Methods
const searchOrder = () => {
    // Search functionality can be implemented later
    console.log('Searching for order:', searchId.value);
};
</script>

<style scoped>
* {
    box-sizing: border-box;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f7f5f0;
}

::-webkit-scrollbar-thumb {
    background: #4a4a4a;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #1a1a1a;
}

/* Japanese pattern backgrounds */
.pattern-shippou {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='15' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.15'/%3E%3Ccircle cx='20' cy='20' r='10' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.11'/%3E%3Ccircle cx='20' cy='20' r='5' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.08'/%3E%3C/svg%3E");
}
</style>
