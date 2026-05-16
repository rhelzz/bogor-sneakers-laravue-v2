<template>
  <transition name="modal-fade">
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
      <div class="bg-white rounded-3xl p-8 max-w-sm w-full shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 text-center">
          <!-- Icon -->
          <div class="mx-auto w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>

          <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight mb-2">Konfirmasi Perubahan</h3>
          <p class="text-sm text-slate-500 leading-relaxed mb-8">
            Apakah Anda yakin ingin mengubah status pesanan menjadi <span class="font-bold text-indigo-600">"{{ statusLabel }}"</span>?
          </p>

          <div class="flex flex-col gap-3">
            <button 
              @click="$emit('confirm')" 
              :disabled="isSubmitting"
              class="w-full py-4 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all hover:bg-indigo-600 active:scale-95 disabled:opacity-50"
            >
              <span v-if="isSubmitting" class="flex items-center justify-center gap-2">
                <span class="w-3 h-3 border-2 border-white/20 border-t-white rounded-full animate-spin"></span>
                Memproses...
              </span>
              <span v-else>Ya, Perbarui Status</span>
            </button>
            <button 
              @click="$emit('close')" 
              :disabled="isSubmitting"
              class="w-full py-3 text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em] hover:text-slate-600 transition-colors disabled:opacity-50"
            >
              Batalkan
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
  statusLabel: string;
  isSubmitting?: boolean;
}>();

defineEmits(['close', 'confirm']);
</script>

<style scoped>
.modal-fade-enter-active {
  animation: modal-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modal-fade-leave-active {
  animation: modal-out 0.3s cubic-bezier(0.4, 0, 1, 1);
}

@keyframes modal-in {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes modal-out {
  from { opacity: 1; transform: scale(1); }
  to { opacity: 0; transform: scale(0.95) translateY(10px); }
}
</style>
