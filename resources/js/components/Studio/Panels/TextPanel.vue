<template>
    <div class="space-y-6">
        <button
            @click="$emit('addText')"
            class="w-full py-8 border-2 border-dashed border-gray-100 rounded-3xl flex flex-col items-center justify-center gap-3 bg-gray-50/50 hover:bg-white hover:border-primary hover:shadow-xl hover:shadow-primary/5 transition-all group"
        >
            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-secondary group-hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-3xl">text_fields</span>
            </div>
            <div class="text-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-black block">Tambah Teks</span>
                <span class="text-[9px] text-secondary font-medium mt-1 block uppercase tracking-tighter opacity-60">Custom Branding</span>
            </div>
        </button>
    </div>
</template>

<script setup lang="ts">
defineEmits(['addText']);
</script>
