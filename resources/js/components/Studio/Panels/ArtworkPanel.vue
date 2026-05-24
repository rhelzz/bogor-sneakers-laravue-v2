<template>
    <div class="space-y-8 py-2">
        <!-- Section Header (Optional, but adds hierarchy) -->
        <div class="flex flex-col gap-1 px-1">
            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-sumi opacity-40">Media Assets</h4>
            <div class="h-px w-8 bg-indigo/20"></div>
        </div>

        <!-- Upload Zone -->
        <button
            @click="triggerUpload"
            class="group relative w-full overflow-hidden rounded-[2rem] border-2 border-dashed border-indigo/10 bg-shironeri/30 p-8 transition-all duration-500 hover:border-indigo/30 hover:bg-white hover:shadow-2xl hover:shadow-indigo/5"
        >
            <!-- Decorative Background Element -->
            <div class="absolute -right-4 -top-4 h-24 w-24 rounded-full bg-indigo/5 opacity-0 transition-all duration-700 group-hover:scale-150 group-hover:opacity-100"></div>
            
            <div class="relative z-10 flex flex-col items-center justify-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-indigo/5 transition-all duration-500 group-hover:rotate-6 group-hover:scale-110 group-hover:shadow-indigo/10 group-hover:ring-indigo/20">
                    <span class="material-symbols-outlined text-3xl text-indigo transition-transform duration-500 group-hover:scale-90">add_photo_alternate</span>
                </div>
                
                <div class="text-center">
                    <span class="block font-montserrat text-[12px] font-black uppercase tracking-[0.2em] text-sumi">
                        Upload Media
                    </span>
                    <p class="mt-2 text-[11px] font-medium text-usuzumi/70">
                        PNG • JPG • SVG (Max 5MB)
                    </p>
                </div>
            </div>
        </button>

        <!-- Media Library -->
        <div class="space-y-4">
            <div v-if="uploadedMedia.length > 0" class="flex items-center justify-between px-1">
                <span class="text-[10px] font-black uppercase tracking-[0.15em] text-sumi/40">Recent Uploads</span>
                <span class="text-[10px] font-bold text-indigo/60 uppercase tracking-widest">{{ uploadedMedia.length }} Items</span>
            </div>

            <transition-group 
                tag="div" 
                name="media-list"
                class="grid grid-cols-2 gap-4"
                v-if="uploadedMedia.length > 0"
            >
                <div
                    v-for="media in uploadedMedia"
                    :key="media.id"
                    class="group relative aspect-square cursor-pointer overflow-hidden rounded-2xl bg-white ring-1 ring-indigo/5 transition-all duration-500 hover:ring-indigo/20 hover:shadow-2xl"
                    @click="$emit('add-media', media.id)"
                >
                    <!-- Image Container -->
                    <div class="flex h-full w-full items-center justify-center p-2 bg-shironeri/30 group-hover:bg-white transition-colors duration-500">
                        <img 
                            :src="media.src" 
                            class="h-full w-full object-contain transition-all duration-700 group-hover:scale-110"
                        >
                    </div>

                    <!-- Delete Button (Top Right) — always visible on mobile, hover-only on desktop -->
                    <button
                        @click.stop="removeUploadedMedia(media.id)"
                        class="absolute right-2 top-2 z-30 flex h-6 w-6 items-center justify-center rounded-full bg-white/80 text-sumi backdrop-blur-md transition-all duration-300 hover:bg-red-500 hover:text-white md:opacity-0 md:group-hover:opacity-100 shadow-sm"
                    >
                        <span class="material-symbols-outlined text-[14px]">close</span>
                    </button>

                    <!-- Hover Overlay (Add Action) — desktop only -->
                    <div class="hidden md:flex absolute inset-0 z-20 flex-col items-center justify-center bg-sumi/80 opacity-0 backdrop-blur-sm transition-all duration-500 group-hover:opacity-100">
                        <div class="flex flex-col items-center gap-2 translate-y-2 transition-transform duration-500 group-hover:translate-y-0">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-sumi shadow-xl">
                                <span class="material-symbols-outlined text-lg">add</span>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-widest text-white/90">Add to Canvas</span>
                        </div>
                    </div>

                    <!-- Mobile tap indicator -->
                    <div class="md:hidden absolute bottom-1.5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-1 bg-sumi/70 backdrop-blur-sm rounded-full px-2 py-0.5">
                        <span class="material-symbols-outlined text-[10px] text-white">add</span>
                        <span class="text-[7px] font-black uppercase tracking-wider text-white/90">Tambah</span>
                    </div>
                </div>
            </transition-group>

            <!-- Empty State for Media Library -->
            <div 
                v-else 
                class="flex flex-col items-center justify-center py-12 px-6 border border-indigo/5 rounded-[2rem] bg-shironeri/20 border-dashed"
            >
                <div class="w-12 h-12 rounded-full bg-indigo/5 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-indigo/30">cloud_off</span>
                </div>
                <p class="text-[9px] font-bold text-usuzumi/60 uppercase tracking-[0.2em] text-center">No assets found</p>
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

const emit = defineEmits(['add-media']);

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
                src: event.target?.result as string
            });
            showToast(`"${file.name}" ditambahkan`);
        };
        reader.readAsDataURL(file);
    }
};

const removeUploadedMedia = (id: string) => {
    const media = uploadedMedia.value.find(m => m.id === id);
    uploadedMedia.value = uploadedMedia.value.filter(m => m.id !== id);
    showToast(`Media ${media?.name || ''} dihapus`);
};
</script>

<style scoped>
.media-list-enter-active,
.media-list-leave-active {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.media-list-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
}
.media-list-leave-to {
    opacity: 0;
    transform: scale(0.9);
}
.media-list-move {
    transition: transform 0.5s ease;
}
</style>
