<template>
  <transition name="modal-fade">
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 lg:p-6 bg-sumi/60 backdrop-blur-md">
      <div class="bg-white rounded-[2.5rem] p-8 lg:p-10 max-w-md w-full shadow-[0_40px_80px_rgba(26,26,26,0.2)] relative overflow-hidden group border border-white/20">
        <!-- Decorative Background -->
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-matcha/5 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-125"></div>
        <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-indigo/5 rounded-full blur-3xl transition-transform duration-1000 group-hover:scale-125"></div>

        <div class="relative z-10">
          <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-matcha/10 rounded-2xl flex items-center justify-center text-matcha">
              <span class="material-symbols-outlined text-2xl">fact_check</span>
            </div>
            <div>
              <h3 class="text-xl font-black text-sumi uppercase tracking-tight">Konfirmasi Pesanan</h3>
              <p class="text-[9px] text-hai font-bold uppercase tracking-widest mt-0.5">Mohon periksa kembali data Anda</p>
            </div>
          </div>

          <!-- Customer Info Summary -->
          <div class="space-y-4 mb-8">
            <div class="p-4 rounded-2xl bg-shironeri border border-sumi/5 space-y-3">
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-hai text-sm mt-0.5">person</span>
                <div class="flex-1">
                  <p class="text-[8px] font-black text-hai uppercase tracking-widest">Nama Pemesan</p>
                  <p class="text-[11px] font-bold text-sumi">{{ customerName }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-hai text-sm mt-0.5">call</span>
                <div class="flex-1">
                  <p class="text-[8px] font-black text-hai uppercase tracking-widest">Nomor WhatsApp</p>
                  <p class="text-[11px] font-bold text-sumi">{{ customerPhone }}</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-hai text-sm mt-0.5">location_on</span>
                <div class="flex-1">
                  <p class="text-[8px] font-black text-hai uppercase tracking-widest">Alamat Pengiriman</p>
                  <p class="text-[11px] font-bold text-sumi leading-relaxed">{{ customerAddress }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="mb-10">
            <div class="flex justify-between items-end mb-4">
              <p class="text-[10px] font-black text-hai uppercase tracking-widest">Total Pembayaran</p>
              <p class="text-2xl font-black text-sumi tracking-tighter">{{ formattedTotal }}</p>
            </div>
            <div class="h-px bg-sumi/5 w-full"></div>
            <p class="text-[8px] text-hai font-medium uppercase tracking-[0.1em] mt-4 text-center">
              Pesanan akan diproses setelah konfirmasi via WhatsApp
            </p>
          </div>

          <div class="flex flex-col gap-3">
            <button 
              @click="$emit('confirm')" 
              :disabled="isSubmitting"
              class="group/btn relative overflow-hidden w-full py-5 bg-sumi text-washi rounded-2xl font-black text-[11px] uppercase tracking-[0.2em] transition-all duration-500 hover:bg-matcha hover:text-sumi hover:-translate-y-1 active:translate-y-0 shadow-xl shadow-sumi/20 disabled:opacity-50"
            >
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:animate-shine duration-1000"></div>
              <span v-if="isSubmitting" class="relative z-10 flex items-center justify-center gap-2">
                <span class="w-4 h-4 border-2 border-washi/20 border-t-washi rounded-full animate-spin"></span>
                Memproses...
              </span>
              <span v-else class="relative z-10">KONFIRMASI & PESAN SEKARANG</span>
            </button>
            <button 
              @click="$emit('close')" 
              :disabled="isSubmitting"
              class="w-full py-3 text-hai font-black text-[10px] uppercase tracking-[0.2em] hover:text-sumi transition-colors disabled:opacity-50"
            >
              KEMBALI KE FORM
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
defineProps<{
  show: boolean;
  customerName: string;
  customerPhone: string;
  customerAddress: string;
  formattedTotal: string;
  isSubmitting?: boolean;
}>();

defineEmits(['close', 'confirm']);
</script>

<style scoped>
.modal-fade-enter-active {
  animation: modal-in 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modal-fade-leave-active {
  animation: modal-out 0.4s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes modal-in {
  from { opacity: 0; transform: scale(0.85) translateY(30px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes modal-out {
  from { opacity: 1; transform: scale(1); }
  to { opacity: 0; transform: scale(0.95) translateY(10px); }
}

@keyframes shine {
  100% {
    transform: translateX(100%);
  }
}
.group-hover\/btn\:animate-shine {
  animation: shine 1.5s infinite;
}
</style>
