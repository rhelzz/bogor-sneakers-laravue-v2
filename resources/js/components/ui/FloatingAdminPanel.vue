<template>
  <button
    v-if="contacts.length > 0"
    aria-label="Hubungi admin"
    class="fixed bottom-6 right-6 z-50 flex items-center gap-3 group"
    @click="contactOpen = !contactOpen"
  >
    <span class="w-12 h-12 rounded-full bg-matcha text-washi flex items-center justify-center transition-all duration-300 group-hover:opacity-90 shadow-lg">
      <i :class="contactOpen ? 'bi bi-x-lg text-lg' : 'bi bi-chat-dots text-lg'"></i>
    </span>
    <span class="text-xs tracking-widest uppercase text-hai hidden md:block">{{ contactOpen ? 'Close' : buttonLabel }}</span>
  </button>

  <div
    v-if="contacts.length > 0"
    :class="[
      'fixed bottom-24 right-6 w-72 bg-washi rounded-2xl shadow-2xl border border-sumi/10 z-40 overflow-hidden transition-all duration-300 origin-bottom-right',
      contactOpen ? 'opacity-100 pointer-events-auto translate-y-0 scale-100' : 'opacity-0 pointer-events-none translate-y-2 scale-[0.98]',
    ]"
  >
    <div class="p-5 border-b border-sumi/10">
      <div class="flex items-center gap-3 mb-2">
        <i class="bi bi-whatsapp text-2xl text-matcha"></i>
        <h3 class="font-heading font-bold tracking-wider">{{ title }}</h3>
      </div>
      <p class="text-xs text-hai">{{ subtitle }}</p>
    </div>

    <a
      v-for="(contact, index) in contacts"
      :key="contact.id"
      :href="`https://wa.me/${contact.phone}`"
      target="_blank"
      :class="[
        'flex items-center gap-4 p-4 hover:bg-sumi/3 transition-all',
        index < contacts.length - 1 ? 'border-b border-sumi/5' : '',
      ]"
    >
      <div :class="`w-11 h-11 rounded-full flex items-center justify-center font-bold ${contact.color}`">{{ contact.initial }}</div>
      <div class="flex-1">
        <p class="text-sm font-medium">{{ contact.name }}</p>
        <p class="text-xs text-hai">{{ contact.role }}</p>
      </div>
      <i class="bi bi-arrow-right text-hai"></i>
    </a>

    <div class="p-4 bg-sumi/3 flex items-center justify-center gap-2 text-xs text-hai">
      <span class="w-2 h-2 rounded-full bg-matcha animate-pulse"></span>
      Aktif Senin-Sabtu 09.00-21.00 WIB
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: 'FloatingAdminPanel',
}
</script>

<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'

import type { FloatingContact } from '@/types/floating-ui'

withDefaults(defineProps<{
  contacts: FloatingContact[]
  title?: string
  subtitle?: string
  buttonLabel?: string
}>(), {
  title: 'HUBUNGI ADMIN',
  subtitle: 'Respon cepat via WhatsApp',
  buttonLabel: 'Admin',
})

const contactOpen = ref(false)

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === 'Escape') {
    contactOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>
