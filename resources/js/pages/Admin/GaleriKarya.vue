<template>
    <AdminLayout>
        <Head title="Admin Galeri Karya" />
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola Galeri Karya"
                description="Galeri homepage selalu 8 slot tetap. Admin dapat mengganti gambar, judul, dan author tiap slot."
            />

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <div class="admin-card-muted mb-5">
                <p class="admin-muted-text">
                    Format upload: JPEG/PNG, maksimal 5MB. Setelah memilih file,
                    preview slot langsung muncul lalu otomatis diunggah ke
                    server. Judul dan author bisa diubah lalu disimpan per slot.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="slot in slots"
                    :key="slot.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white"
                >
                    <div
                        :class="`relative overflow-hidden bg-slate-100 ${getAspectClass(slot.slot)}`"
                    >
                        <img
                            v-if="getDisplayImage(slot)"
                            :src="getDisplayImage(slot)"
                            :alt="`Galeri karya slot ${slot.slot}`"
                            class="h-full w-full object-contain"
                            loading="lazy"
                            decoding="async"
                        />
                        <div
                            v-else
                            class="flex h-full w-full flex-col items-center justify-center gap-2 text-slate-400"
                        >
                            <i class="bi bi-image text-4xl"></i>
                            <p class="text-xs">Belum ada gambar</p>
                        </div>

                        <div class="absolute top-3 left-3">
                            <span
                                class="rounded-full border border-slate-300 bg-white px-2.5 py-1 text-[11px] font-bold tracking-wide text-slate-700"
                            >
                                SLOT {{ slot.slot }}
                            </span>
                        </div>

                        <div
                            v-if="uploadingSlot === slot.slot"
                            class="admin-image-overlay absolute inset-0 flex items-center justify-center text-white"
                        >
                            <div
                                class="flex items-center gap-2 text-xs font-bold"
                            >
                                <i class="bi bi-arrow-repeat animate-spin"></i>
                                Uploading...
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5 p-3">
                        <label
                            :for="fileInputId(slot.slot)"
                            class="admin-btn admin-btn-primary admin-btn-block"
                            :class="{
                                'cursor-not-allowed opacity-50':
                                    uploadingSlot === slot.slot,
                            }"
                        >
                            {{
                                slot.image_url
                                    ? 'Ganti Gambar Slot'
                                    : 'Upload Gambar Slot'
                            }}
                        </label>
                        <input
                            :id="fileInputId(slot.slot)"
                            type="file"
                            accept="image/jpeg,image/png"
                            class="hidden"
                            :disabled="uploadingSlot === slot.slot"
                            @change="handleFilePick(slot, $event)"
                        />

                        <div class="space-y-1">
                            <label class="admin-label">Judul</label>
                            <input
                                v-model="metadataForms[slot.id].title"
                                type="text"
                                maxlength="120"
                                class="admin-input"
                                :disabled="savingMetadataSlot === slot.slot"
                            />
                        </div>

                        <div class="space-y-1">
                            <label class="admin-label">Author</label>
                            <input
                                v-model="metadataForms[slot.id].author"
                                type="text"
                                maxlength="80"
                                class="admin-input"
                                :disabled="savingMetadataSlot === slot.slot"
                            />
                        </div>

                        <button
                            class="admin-btn admin-btn-secondary admin-btn-block"
                            :disabled="
                                savingMetadataSlot === slot.slot ||
                                uploadingSlot === slot.slot
                            "
                            @click="saveMetadata(slot)"
                        >
                            {{
                                savingMetadataSlot === slot.slot
                                    ? 'Menyimpan...'
                                    : 'Simpan Judul & Author'
                            }}
                        </button>

                        <p class="text-center text-xs text-hai/70">
                            Update terakhir:
                            {{ formatUpdatedAt(slot.updated_at) }}
                        </p>
                    </div>
                </article>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { useAdminGaleriKaryaManager } from '@/composables/Admin/galeriKarya/useAdminGaleriKaryaManager';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { GallerySlot } from '@/types/gallery';

const props = defineProps<{
    slots?: GallerySlot[];
}>();

const {
    slots,
    uploadingSlot,
    savingMetadataSlot,
    successMessage,
    errorMessage,
    metadataForms,
    handleFilePick,
    saveMetadata,
    getAspectClass,
    fileInputId,
    getDisplayImage,
    formatUpdatedAt,
} = useAdminGaleriKaryaManager({
    initialSlots: props.slots ?? [],
});
</script>
