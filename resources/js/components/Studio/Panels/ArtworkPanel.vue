<template>
    <div class="space-y-6">
        <button
            @click="triggerUpload"
            class="w-full py-8 border-2 border-dashed border-gray-100 rounded-3xl flex flex-col items-center justify-center gap-3 bg-gray-50/50 hover:bg-white hover:border-primary hover:shadow-xl hover:shadow-primary/5 transition-all group"
        >
            <div class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-secondary group-hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
            </div>
            <div class="text-center">
                <span class="text-[10px] font-black uppercase tracking-widest text-black block">Upload Media</span>
                <span class="text-[9px] text-secondary font-medium mt-1 block uppercase tracking-tighter opacity-60">PNG / JPG / SVG</span>
            </div>
        </button>

        <div v-if="uploadedMedia.length > 0" class="grid grid-cols-2 gap-4">
            <div
                v-for="media in uploadedMedia"
                :key="media.id"
                class="group relative aspect-square rounded-2xl bg-white border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all cursor-pointer"
                @click="$emit('addMedia', media.id)"
            >
                <button
                    @click.stop="removeUploadedMedia(media.id)"
                    class="absolute top-2 right-2 w-6 h-6 rounded-full bg-white/90 text-red-500 flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-50 z-20"
                    title="Hapus dari daftar"
                >
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
                <img :src="media.src" class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform">
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 flex items-center justify-center backdrop-blur-[2px] transition-all">
                    <div class="bg-white text-black px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest scale-75 group-hover:scale-100 transition-transform">Tambah</div>
                </div>
            </div>
        </div>
        
        <input ref="uploadInputRef" type="file" class="hidden" accept="image/*" multiple @change="onUploadInputChange" />
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useStudioStore } from '../../../composables/useStudioStore';

const { uploadedMedia, showToast } = useStudioStore();
const uploadInputRef = ref<HTMLInputElement | null>(null);

const emit = defineEmits(['addMedia']);

const triggerUpload = () => {
    uploadInputRef.value?.click();
};

const onUploadInputChange = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (!files) return;

    for (const file of Array.from(files)) {
        const reader = new FileReader();
        reader.onload = (event) => {
            uploadedMedia.value.push({
                id: Math.random().toString(36).slice(2),
                name: file.name,
                src: event.target?.result
            });
        };
        reader.readAsDataURL(file);
    }
};

const removeUploadedMedia = (id: string) => {
    uploadedMedia.value = uploadedMedia.value.filter(m => m.id !== id);
    showToast('Media dihapus dari daftar');
};
</script>
