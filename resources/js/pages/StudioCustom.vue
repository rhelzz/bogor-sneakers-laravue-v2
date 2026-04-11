<template>
  <div class="bg-washi text-sumi font-body antialiased">
    <FloatingMenuNav current-page="studio-custom" />
    <FloatingAdminPanel
      :contacts="contacts"
      title="HUBUNGI DINDA"
      subtitle="DIY & Customization Specialist"
    />
    <FloatingOrderPanel :orders="orders" />

    <!-- STUDIO CUSTOM HEADER -->
    <section class="min-h-screen flex flex-col justify-center px-6 lg:px-16 pt-32 pb-24 pattern-kumiko relative overflow-hidden">
      <div class="max-w-7xl mx-auto w-full">
        <!-- Header -->
        <div class="mb-16 pb-10 accent-top">
          <div class="flex items-center gap-3 mb-4 animate-slide-up">
            <span class="w-2 h-2 rounded-full bg-sakura animate-pulse-soft"></span>
            <span class="text-xs tracking-widest text-hai">BOGOR SNEAKER STUDIO</span>
          </div>
          <h1 class="text-5xl lg:text-7xl font-heading font-bold leading-none tracking-tight mb-6 animate-slide-up" style="animation-delay: 0.1s">
            Custom<br>
            <span class="text-sakura">Design</span>
          </h1>
          <p class="text-hai leading-relaxed max-w-md mb-8 animate-slide-up" style="animation-delay: 0.2s">
            Ubah sepatu idaman Anda dengan customization profesional. Dari design simpel hingga custom hand-painted.
          </p>
          <a
            href="https://wa.me/6285511223344"
            target="_blank"
            class="inline-flex items-center gap-3 animate-slide-up"
            style="animation-delay: 0.3s"
          >
            <span class="px-8 py-3 bg-sakura text-washi rounded-full text-sm tracking-wider hover:opacity-90 transition-all">
              Konsultasi Desain
            </span>
          </a>
        </div>
      </div>
    </section>

    <!-- SERVICES SECTION -->
    <section class="py-24 px-6 lg:px-16 bg-shironeri">
      <div class="max-w-7xl mx-auto">
        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(service, idx) in services"
            :key="idx"
            class="bg-washi rounded-3xl p-8 card-lift border border-sumi/5 animate-slide-up"
            :style="{ animationDelay: `${idx * 0.1}s` }"
          >
            <div :class="`w-16 h-16 rounded-2xl flex items-center justify-center mb-6 ${service.bgColor}`">
              <i :class="`${service.icon} text-2xl ${service.textColor}`"></i>
            </div>
            <h3 class="font-heading font-bold text-lg mb-3">{{ service.title }}</h3>
            <p class="text-sm text-hai leading-relaxed mb-4">{{ service.description }}</p>
            <p :class="`text-lg font-bold ${service.priceColor}`">{{ service.price }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PORTFOLIO SECTION -->
    <section class="py-24 px-6 lg:px-16 bg-washi">
      <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16 pb-10 accent-top">
          <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-4">Galeri<br>Custom Sneakers</h2>
          <p class="text-hai max-w-md mx-auto">Karya-karya custom terbaik dari Bogor Sneaker Studio</p>
        </div>

        <!-- Gallery Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div
            v-for="(item, idx) in galleryItems"
            :key="idx"
            :class="`bg-sumi/5 rounded-2xl overflow-hidden card-lift border border-sumi/5 group cursor-pointer ${item.aspectClass}`"
          >
            <div class="w-full h-full flex items-center justify-center relative overflow-hidden">
              <i class="bi bi-image text-4xl text-hai/30 group-hover:scale-110 transition-transform"></i>
              <div class="absolute inset-0 bg-sumi/0 group-hover:bg-sumi/10 transition-all"></div>
            </div>
            <div class="absolute inset-0 flex flex-col justify-end p-4 bg-linear-to-t from-kuro via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
              <p class="text-sm font-medium text-washi">{{ item.title }}</p>
              <p class="text-xs text-washi/60">{{ item.artist }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-24 px-6 lg:px-16 bg-sakura text-washi relative overflow-hidden">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl lg:text-5xl font-heading font-bold mb-6">Siap Custom Sepat Impian?</h2>
        <p class="text-lg text-washi/80 mb-8 max-w-2xl mx-auto">
          Hubungi Dinda sekarang untuk berkonsultasi tentang design custom Anda. Kami siap mewujudkan imajinasi Anda.
        </p>
        <a
          href="https://wa.me/6285511223344"
          target="_blank"
          class="inline-block px-10 py-4 bg-washi text-sakura rounded-full text-base font-bold tracking-wider hover:scale-105 transition-transform"
        >
          <i class="bi bi-whatsapp mr-2"></i>
          Chat via WhatsApp
        </a>
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
const contacts = ref<FloatingContact[]>([
  { id: 1, name: 'Dinda - DIY Master', role: 'Kustom, Desain, Konsultasi', phone: '6285511223344', initial: 'D', color: 'bg-sakura/30 text-sakura' },
])

const orders = ref<FloatingOrder[]>([
  { id: '#BGS-2841', product: 'Nike Air Max 97 Silver', size: '42', status: 'Produksi', statusClass: 'px-2 py-1 rounded text-[10px] bg-amber-100 text-amber-700', progress: 55, progressClass: 'bg-sumi' },
  { id: '#BGS-2790', product: 'Adidas Samba OG White', size: '40', status: 'Dikirim', statusClass: 'px-2 py-1 rounded text-[10px] bg-blue-100 text-blue-700', progress: 85, progressClass: 'bg-sumi' },
  { id: '#BGS-2755', product: 'Jordan 1 Retro High Bred', size: '43', status: 'Selesai', statusClass: 'px-2 py-1 rounded text-[10px] bg-matcha/20 text-matcha', progress: 100, progressClass: 'bg-matcha' },
])

// Services Data
const services = ref([
  { 
    title: 'Basic Customization',
    description: 'Custom warna, logo sederhana, atau patch tambahan pada sepatu pilihan.',
    price: 'Mulai Rp 200K',
    icon: 'bi bi-palette',
    bgColor: 'bg-indigo/10',
    textColor: 'text-indigo',
    priceColor: 'text-indigo'
  },
  { 
    title: 'Hand-Painted Design',
    description: 'Desain custom hand-painted profesional sesuai brand atau karakter kesukaan.',
    price: 'Mulai Rp 500K',
    icon: 'bi bi-brush',
    bgColor: 'bg-sakura/20',
    textColor: 'text-sakura',
    priceColor: 'text-sakura'
  },
  { 
    title: 'Premium Custom Pack',
    description: 'Paket lengkap: desain custom, patch leather, perubahan warna, dan branding.',
    price: 'Mulai Rp 1.2Jt',
    icon: 'bi bi-star',
    bgColor: 'bg-matcha/10',
    textColor: 'text-matcha',
    priceColor: 'text-matcha'
  },
])

// Gallery Items
const galleryItems = ref([
  { title: 'Cherry Blossom Air Max', artist: 'Custom Sakura Edition', aspectClass: 'aspect-square' },
  { title: 'Wave Painted Dunk', artist: 'Seigaiha Inspired', aspectClass: 'aspect-[3/4]' },
  { title: 'Neon Street Vibe', artist: 'Urban Custom', aspectClass: 'aspect-[4/3]' },
  { title: 'Minimalist Bred', artist: 'Clean Lines', aspectClass: 'aspect-[3/4]' },
  { title: 'Dragon Design Jordan', artist: 'Asian Fusion', aspectClass: 'aspect-[4/5]' },
  { title: 'Retro Color Swap', artist: 'Vintage Vibes', aspectClass: 'aspect-square' },
])
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
.pattern-kumiko {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 60 60'%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.8' opacity='0.12' d='M0 0h60v60H0z M15 0v60M30 0v60M45 0v60 M0 15h60M0 30h60M0 45h60' /%3E%3Cpath fill='none' stroke='%231A1A1A' stroke-width='0.5' opacity='0.06' d='M10 10 L50 50 M50 10 L10 50' /%3E%3C/svg%3E");
}
</style>
