<template>
    <div class="space-y-8 py-2">
        <!-- Section Header -->
        <div class="flex flex-col gap-1 px-1">
            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-sumi opacity-40">Identity Design</h4>
            <div class="h-px w-8 bg-indigo/20"></div>
        </div>

        <!-- Add Text Button -->
        <button
            @click="$emit('add-text')"
            class="group relative w-full overflow-hidden rounded-[2rem] border-2 border-dashed border-indigo/10 bg-shironeri/30 p-8 transition-all duration-500 hover:border-indigo/30 hover:bg-white hover:shadow-2xl hover:shadow-indigo/5"
        >
            <!-- Decorative Background Element -->
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo/5 opacity-0 transition-all duration-700 group-hover:scale-150 group-hover:opacity-100"></div>
            
            <div class="relative z-10 flex flex-col items-center justify-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-indigo/5 transition-all duration-500 group-hover:-rotate-6 group-hover:scale-110 group-hover:shadow-indigo/10 group-hover:ring-indigo/20">
                    <span class="material-symbols-outlined text-3xl text-indigo transition-transform duration-500 group-hover:scale-110">text_fields</span>
                </div>
                
                <div class="text-center px-4">
                    <span class="block font-montserrat text-[12px] font-black uppercase tracking-[0.2em] text-sumi">
                        Tambah Kustom Teks
                    </span>
                    <p class="mt-2 text-[11px] font-medium text-usuzumi/70 tracking-normal">
                        Tambahkan nama, angka, atau pesan kustom ke desain Anda
                    </p>
                </div>
            </div>
        </button>

        <!-- Information Card -->
        <div class="p-5 rounded-3xl bg-indigo/[0.03] border border-indigo/10 flex gap-4">
            <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-xl text-indigo">info</span>
            </div>
            <div class="space-y-1.5">
                <span class="text-[11px] font-black uppercase tracking-widest text-sumi block">Tips Tipografi</span>
                <p class="text-[11px] leading-relaxed text-usuzumi/80 font-medium">
                    Gunakan panel kontrol yang muncul setelah menambahkan teks untuk mengubah font, warna, dan ukuran stroke.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineEmits(['add-text']);
</script>

<style scoped>
/* Clean and focused panel style */
</style>
