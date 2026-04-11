<template>
  <div class="bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="tracking" />
    <FloatingAdminPanel
      :contacts="contacts"
      title="HUBUNGI FARHAN"
      subtitle="Customer Service & Tracking"
    />
    <FloatingOrderPanel :orders="orders" />

    <!-- TRACKING HEADER -->
    <section class="min-h-screen flex flex-col justify-center px-6 lg:px-16 pt-32 pb-24 pattern-shippou relative overflow-hidden">
      <div class="max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="mb-16 pb-10 accent-top">
          <div class="flex items-center gap-3 mb-4 animate-slide-up">
            <span class="w-2 h-2 rounded-full bg-indigo animate-pulse-soft"></span>
            <span class="text-xs tracking-widest text-hai">ORDER TRACKING SYSTEM</span>
          </div>
          <h1 class="text-5xl lg:text-7xl font-heading font-bold leading-none tracking-tight mb-6 animate-slide-up" style="animation-delay: 0.1s">
            Lacak<br>
            <span class="text-indigo">Pesanan Mu</span>
          </h1>
          <p class="text-hai leading-relaxed max-w-md mb-8 animate-slide-up" style="animation-delay: 0.2s">
            Monitor status pesanan real-time. Dari produksi hingga sampai ke tangan Anda.
          </p>
        </div>

        <!-- Search Box -->
        <div class="max-w-2xl animate-slide-up" style="animation-delay: 0.3s">
          <div class="flex gap-2 mb-4">
            <input 
              v-model="searchId" 
              type="text" 
              placeholder="Masukkan nomor order (contoh: BGS-2841)"
              class="flex-1 px-6 py-4 bg-washi border border-sumi/10 rounded-full text-sm placeholder-hai/50 focus:outline-none focus:ring-2 focus:ring-indigo focus:border-transparent"
            />
            <button @click="searchOrder" class="px-8 py-4 bg-indigo text-washi rounded-full text-sm font-bold tracking-wider hover:opacity-90 transition-all">
              Cari
            </button>
          </div>
          <p class="text-xs text-hai">Gunakan nomor order untuk tracking pesanan Anda</p>
        </div>
      </div>
    </section>

    <!-- ACTIVE ORDERS -->
    <section class="py-24 px-6 lg:px-16 bg-shironeri">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-heading font-bold mb-12">Pesanan Aktif</h2>

        <div class="grid grid-cols-1 gap-6">
          <div
            v-for="(order, idx) in activeOrders"
            :key="idx"
            class="bg-washi rounded-3xl p-8 border border-sumi/5 card-lift animate-slide-up"
            :style="{ animationDelay: `${idx * 0.1}s` }"
          >
            <!-- Order Header -->
            <div class="flex items-center justify-between mb-6 pb-6 border-b border-sumi/10">
              <div>
                <p class="text-xs text-hai tracking-wider mb-1">NOMOR PESANAN</p>
                <p class="text-2xl font-bold">{{ order.id }}</p>
              </div>
              <div>
                <p class="text-xs text-hai tracking-wider mb-1">STATUS</p>
                <span :class="`px-4 py-2 rounded-full text-sm font-bold ${order.statusClass}`">{{ order.status }}</span>
              </div>
              <div>
                <p class="text-xs text-hai tracking-wider mb-1">PRODUK</p>
                <p class="font-bold">{{ order.product }}</p>
              </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-hai tracking-wider">PROGRESS</p>
                <p class="text-sm font-bold">{{ order.progress }}%</p>
              </div>
              <div class="h-2 bg-sumi/10 rounded-full overflow-hidden">
                <div :class="`h-full rounded-full transition-all duration-500 ${order.progressClass}`" :style="`width: ${order.progress}%`"></div>
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
                  <div :class="`w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ${step.completed ? 'bg-indigo text-washi' : 'bg-sumi/10 text-hai'}`">
                    <i v-if="step.completed" class="bi bi-check"></i>
                    <span v-else>{{ stepIdx + 1 }}</span>
                  </div>
                  <div v-if="stepIdx < order.timeline.length - 1" class="w-0.5 h-8 bg-sumi/10 mt-1"></div>
                </div>
                <div class="pt-1">
                  <p :class="['font-bold', step.completed ? 'text-sumi' : 'text-hai']">{{ step.title }}</p>
                  <p class="text-xs text-hai">{{ step.date }}</p>
                </div>
              </div>
            </div>

            <!-- Contact -->
            <div class="mt-6 pt-6 border-t border-sumi/10">
              <p class="text-xs text-hai mb-2">Ada pertanyaan? Hubungi Farhan via WhatsApp</p>
              <a href="https://wa.me/6289876543210" target="_blank" class="inline-flex items-center gap-2 text-indigo hover:text-indigo/70 transition-colors text-sm font-bold">
                <i class="bi bi-whatsapp"></i>
                Chat Sekarang
              </a>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="activeOrders.length === 0" class="text-center py-12">
          <i class="bi bi-box-seam text-6xl text-hai/20 block mb-4"></i>
          <p class="text-hai text-lg">Tidak ada pesanan aktif</p>
          <p class="text-hai text-sm">Gunakan search untuk mencari pesanan Anda</p>
        </div>
      </div>
    </section>

    <!-- FAQ SECTION -->
    <section class="py-24 px-6 lg:px-16 bg-washi">
      <div class="max-w-4xl mx-auto">
        <h2 class="text-4xl font-heading font-bold mb-12 text-center">Pertanyaan Umum</h2>

        <div class="space-y-4">
          <div
            v-for="(faq, idx) in faqs"
            :key="idx"
            class="bg-shironeri rounded-2xl p-6 cursor-pointer hover:shadow-lg transition-all card-lift"
            @click="faq.open = !faq.open"
          >
            <div class="flex items-center justify-between">
              <p class="font-bold text-lg">{{ faq.question }}</p>
              <i :class="`bi ${faq.open ? 'bi-chevron-up' : 'bi-chevron-down'} transition-transform`"></i>
            </div>
            <p v-if="faq.open" class="text-hai text-sm mt-4 leading-relaxed">{{ faq.answer }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

import FloatingAdminPanel from '@/components/ui/FloatingAdminPanel.vue'
import FloatingMenuNav from '@/components/ui/FloatingMenuNav.vue'
import FloatingOrderPanel from '@/components/ui/FloatingOrderPanel.vue'
import type { FloatingContact, FloatingOrder } from '@/types/floating-ui'

// State Management
const searchId = ref('')

const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Farhan - Customer Service', role: 'Komplain, Tracking, Retur', phone: '6289876543210', initial: 'F', color: 'bg-indigo/20 text-indigo' },
])

const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
  { id: '#BGS-2755', product: 'Jordan 1 Retro High Bred', size: '43', status: 'Selesai', statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha', progress: 100, progressClass: 'bg-matcha' },
])

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
      { title: 'Pesanan Diterima', date: '1 April 2025', completed: true },
      { title: 'Verifikasi Payment', date: '1 April 2025', completed: true },
      { title: 'Produksi', date: 'Sedang berlangsung', completed: false },
      { title: 'QC & Packing', date: 'Tunggu update', completed: false },
      { title: 'Dikirim', date: 'Tunggu pengiriman', completed: false },
    ]
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
      { title: 'Pesanan Diterima', date: '28 Maret 2025', completed: true },
      { title: 'Verifikasi Payment', date: '28 Maret 2025', completed: true },
      { title: 'Produksi Selesai', date: '31 Maret 2025', completed: true },
      { title: 'Dalam Perjalanan', date: 'Sedang berlangsung', completed: false },
      { title: 'Sampai Tujuan', date: 'Tunggu kedatangan', completed: false },
    ]
  },
])

// FAQs
const faqs = ref([
  {
    question: 'Berapa lama waktu produksi?',
    answer: 'Waktu produksi normal adalah 7-14 hari kerja tergantung model dan ketersediaan komponen. Ready stock biasanya langsung diproses pengiriman.',
    open: false
  },
  {
    question: 'Bagaimana cara lacak pesanan?',
    answer: 'Anda bisa lacak pesanan menggunakan nomor order (contoh: BGS-2841) di halaman ini. Atau hubungi Farhan via WhatsApp untuk info real-time.',
    open: false
  },
  {
    question: 'Apa yang dilakukan saat status "QC & Packing"?',
    answer: 'Pada tahap ini sepatu dicek kualitasnya, dibersihkan, dikemas dengan rapi dalam box, dan siap untuk pengiriman. Foto QC akan dikirim sebelum pengiriman.',
    open: false
  },
  {
    question: 'Apakah bisa cancel atau ubah pesanan?',
    answer: 'Untuk pesanan yang belum di-produksi, bisa dicancel dengan refund penuh. Hubungi CS kami secepatnya jika ingin mengubah atau cancel.',
    open: false
  },
  {
    question: 'Bagaimana jika sepatu rusak saat pengiriman?',
    answer: 'Kami menggunakan packing bubble wrap + box tebal untuk meminimalkan risiko. Jika terjadi kerusakan, dokumentasikan dengan foto dan hubungi Farhan untuk klaim.',
    open: false
  },
])

// Methods
const searchOrder = () => {
  // Search functionality can be implemented later
  console.log('Searching for order:', searchId.value)
}
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
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='15' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.08'/%3E%3Ccircle cx='20' cy='20' r='10' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.06'/%3E%3Ccircle cx='20' cy='20' r='5' fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.04'/%3E%3C/svg%3E");
}
</style>
