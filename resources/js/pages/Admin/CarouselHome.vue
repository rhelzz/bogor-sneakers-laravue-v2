<template>
    <AdminLayout>
        <template #header> Manajemen Carousel </template>

        <div class="space-y-8 font-['Source_Sans_Pro']">
            <!-- Bagian Upload -->
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <h3
                        class="font-['Montserrat'] text-xl font-bold tracking-tight text-slate-800"
                    >
                        Upload Gambar Baru
                    </h3>
                </div>

                <!-- Error Messages -->
                <div
                    v-if="showUploadError && uploadError.length > 0"
                    class="mb-4 rounded-lg border border-rose-200 bg-rose-50 p-4"
                >
                    <div class="flex items-start space-x-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mt-0.5 h-5 w-5 shrink-0 text-rose-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-rose-800">
                                Error Upload:
                            </p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                <li
                                    v-for="error in uploadError"
                                    :key="error"
                                    class="text-sm text-rose-700"
                                >
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Preview Image jika sudah dipilih -->
                <div
                    v-if="previewUrl"
                    class="mb-4 flex items-center space-x-4 rounded-lg border border-indigo-200 bg-indigo-50 p-4"
                >
                    <img
                        :src="previewUrl"
                        alt="Preview"
                        class="h-24 w-24 rounded object-cover"
                    />
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-indigo-900">
                            File dipilih dan siap diunggah
                        </p>
                        <p
                            v-if="selectedFile"
                            class="mt-1 text-xs text-indigo-700"
                        >
                            {{ selectedFile.name }}
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="uploadImage('/admin/carousel-home')"
                            :disabled="isUploading || !selectedFile"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-slate-400"
                        >
                            {{ isUploading ? 'Mengunggah...' : 'Upload' }}
                        </button>
                        <button
                            @click="clearSelectedFile"
                            :disabled="isUploading"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-300 disabled:cursor-not-allowed disabled:bg-slate-400"
                        >
                            Batal
                        </button>
                    </div>
                </div>

                <!-- Upload Dropzone -->
                <div
                    v-if="!previewUrl"
                    @drop.prevent="handleDropZone"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    class="group relative flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed bg-white p-12 text-center transition-colors duration-300"
                    :class="
                        isDragging
                            ? 'border-indigo-500 bg-indigo-50'
                            : 'border-slate-300 hover:border-indigo-500 hover:bg-slate-50'
                    "
                >
                    <div
                        class="mb-4 flex h-16 w-16 items-center justify-center rounded-full transition-colors duration-300"
                        :class="
                            isDragging
                                ? 'bg-indigo-100 text-indigo-600'
                                : 'bg-slate-100 text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600'
                        "
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                            />
                        </svg>
                    </div>
                    <p class="text-lg font-semibold text-slate-600">
                        Tarik & Lepas gambar di sini
                    </p>
                    <p class="mt-1 mb-6 text-sm text-slate-400">
                        Atau klik untuk memilih file dari komputer
                    </p>
                    <button
                        type="button"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 font-semibold text-white transition-colors duration-200 hover:bg-indigo-700"
                    >
                        Pilih File
                    </button>
                    <input
                        ref="fileInput"
                        type="file"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                        accept="image/jpeg,image/png"
                        @change="handleFileInput"
                    />
                </div>
            </section>

            <hr class="border-slate-200" />

            <!-- Bagian Preview Gallery -->
            <section>
                <div class="mb-6 flex items-center justify-between">
                    <h3
                        class="font-['Montserrat'] text-xl font-bold tracking-tight text-slate-800"
                    >
                        Galeri Carousel
                    </h3>
                    <span
                        class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-1 text-sm font-bold text-slate-600"
                    >
                        Total: {{ totalSlides }} Slide
                    </span>
                </div>

                <!-- Warning untuk storage symlink atau image loading issues -->
                <div
                    v-if="imageLoadErrors.size > 0"
                    class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4"
                >
                    <div class="flex items-start space-x-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4v2m0 0a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-amber-800">
                                ⚠️ {{ imageLoadErrors.size }} gambar gagal
                                dimuat
                            </p>
                            <p class="mt-1 text-xs text-amber-700">
                                Solusi cepat:
                            </p>
                            <ul class="mt-2 list-inside list-disc space-y-1">
                                <li class="text-xs text-amber-700">
                                    1. Buka terminal → Jalankan:
                                    <code class="rounded bg-amber-100 px-1"
                                        >php artisan storage:link</code
                                    >
                                </li>
                                <li class="text-xs text-amber-700">
                                    2. Refresh browser → Tekan:
                                    <code class="rounded bg-amber-100 px-1"
                                        >Ctrl+Shift+Delete</code
                                    >
                                    lalu
                                    <code class="rounded bg-amber-100 px-1"
                                        >F5</code
                                    >
                                </li>
                                <li class="text-xs text-amber-700">
                                    3. Jika masih error, cek file permissions:
                                    <code class="rounded bg-amber-100 px-1"
                                        >chmod 755 storage/app/public</code
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-if="sortedSlides.length === 0"
                    class="rounded-lg border border-slate-200 bg-slate-50 py-12 text-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="mx-auto mb-4 h-16 w-16 text-slate-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <p class="font-semibold text-slate-500">
                        Belum ada gambar carousel
                    </p>
                    <p class="mt-1 text-sm text-slate-400">
                        Mulai dengan upload gambar di atas
                    </p>
                </div>

                <!-- Gallery Grid -->
                <div
                    v-else
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                >
                    <div
                        v-for="slide in sortedSlides"
                        :key="slide.id"
                        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition-colors duration-300 hover:border-indigo-300"
                    >
                        <div
                            class="relative flex h-48 w-full items-center justify-center overflow-hidden bg-slate-100"
                        >
                            <img
                                :src="slide.image_url"
                                alt="Carousel Slide"
                                class="h-full w-full object-cover"
                                @error="
                                    (e) => {
                                        console.error(
                                            '❌ Image failed to load:',
                                            slide.image_url,
                                        );
                                        console.error('Slide data:', slide);
                                        trackImageError(slide.id);
                                        (
                                            e.target as HTMLImageElement
                                        ).style.display = 'none';
                                    }
                                "
                            />

                            <!-- Fallback UI ketika image gagal load -->
                            <div
                                v-if="!slide.image_url"
                                class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 text-center"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mb-2 h-12 w-12 text-slate-300"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <p class="text-xs text-slate-400">
                                    Gambar tidak tersedia
                                </p>
                            </div>

                            <!-- Hover Overlay -->
                            <div
                                class="absolute inset-0 flex items-center justify-center space-x-3 bg-slate-900/80 opacity-0 backdrop-blur-sm transition-opacity duration-300 group-hover:opacity-100"
                            >
                                <button
                                    @click="handleDeleteClick(slide)"
                                    class="rounded-lg bg-rose-500 p-2 text-white transition-colors hover:bg-rose-600"
                                    title="Hapus"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>

                            <!-- Status Badge -->
                            <div class="absolute top-3 left-3 z-10">
                                <span
                                    class="rounded-md px-2.5 py-1 text-xs font-bold text-white shadow-sm"
                                    :style="{
                                        backgroundColor: getStatusBadgeColor(
                                            slide.is_active,
                                        ),
                                    }"
                                >
                                    {{ getStatusBadgeLabel(slide.is_active) }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Info -->
                        <div
                            class="flex flex-1 items-center justify-between border-t border-slate-100 bg-white p-4"
                        >
                            <div class="flex flex-col">
                                <span
                                    class="mb-1 text-xs font-bold tracking-wider text-slate-400 uppercase"
                                    >Urutan</span
                                >
                                <span
                                    class="font-['Montserrat'] text-lg font-extrabold text-slate-800"
                                    >{{ formatOrder(slide.order) }}</span
                                >
                            </div>

                            <!-- Toggle Status Button -->
                            <button
                                type="button"
                                @click="
                                    toggleSlideStatus(
                                        slide,
                                        `/admin/carousel-home/${slide.id}`,
                                    )
                                "
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :style="{
                                    backgroundColor: getStatusBadgeColor(
                                        slide.is_active,
                                    ),
                                }"
                                role="switch"
                                :aria-checked="slide.is_active"
                            >
                                <span
                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow ring-0 transition-transform duration-200 ease-in-out"
                                    :style="{
                                        transform: slide.is_active
                                            ? 'translateX(1.25rem)'
                                            : 'translateX(0)',
                                    }"
                                />
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Confirmation Modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div
                    v-if="showConfirmDelete"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
                    @click.self="showConfirmDelete = false"
                >
                    <div
                        class="animate-in mx-4 max-w-sm rounded-lg bg-white shadow-xl"
                    >
                        <div class="p-6">
                            <h3 class="mb-2 text-lg font-bold text-slate-900">
                                Konfirmasi Hapus
                            </h3>
                            <p class="mb-6 text-slate-600">
                                Apakah Anda yakin ingin menghapus slide ini?
                                Gambar akan dihapus secara permanen.
                            </p>
                            <div class="flex justify-end space-x-3">
                                <button
                                    @click="showConfirmDelete = false"
                                    class="rounded-lg bg-slate-200 px-4 py-2 font-semibold text-slate-700 transition-colors hover:bg-slate-300"
                                >
                                    Batal
                                </button>
                                <button
                                    @click="confirmDelete"
                                    :disabled="isDeleting"
                                    class="rounded-lg bg-rose-600 px-4 py-2 font-semibold text-white transition-colors hover:bg-rose-700 disabled:bg-slate-400"
                                >
                                    {{ isDeleting ? 'Menghapus...' : 'Hapus' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import type { ICarouselSlide } from '@/composables/Admin/useCarouselHome';
import { useCarouselHome } from '@/composables/Admin/useCarouselHome';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    debugImagePath,
    debugStorageSymlink,
} from '@/utils/Admin/checkStoragePath';

// Props dari Controller
interface Props {
    slides: ICarouselSlide[];
}

const props = withDefaults(defineProps<Props>(), {
    slides: () => [],
});

// Composable
const {
    selectedFile,
    previewUrl,
    isUploading,
    showUploadError,
    uploadError,
    totalSlides,
    sortedSlides,
    imageLoadErrors,
    handleFileSelect,
    clearSelectedFile,
    uploadImage,
    toggleSlideStatus,
    deleteSlide,
    formatOrder,
    getStatusBadgeColor,
    getStatusBadgeLabel,
    refreshSlides,
    trackImageLoadError,
} = useCarouselHome(props.slides);

// Local state
const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const showConfirmDelete = ref(false);
const isDeleting = ref(false);
const slideToDelete = ref<ICarouselSlide | null>(null);

// Methods untuk Upload

/**
 * Handle file input change
 */
const handleFileInput = (event: Event) => {
    const input = event.target as HTMLInputElement;

    if (input.files && input.files.length > 0) {
        handleFileSelect(input.files[0]);
    }
};

/**
 * Track failed image loads
 */
const trackImageError = (slideId: number) => {
    trackImageLoadError(slideId, 'Image failed to load');
};

/**
 * Handle drop zone untuk drag & drop
 */
const handleDropZone = (event: DragEvent) => {
    isDragging.value = false;

    if (event.dataTransfer && event.dataTransfer.files.length > 0) {
        const file = event.dataTransfer.files[0];
        handleFileSelect(file);
    }
};

// Methods untuk Delete

/**
 * Tampilkan confirmation dialog sebelum delete
 */
const handleDeleteClick = (slide: ICarouselSlide) => {
    slideToDelete.value = slide;
    showConfirmDelete.value = true;
};

/**
 * Konfirmasi dan jalankan delete
 */
const confirmDelete = async () => {
    if (!slideToDelete.value) {
        return;
    }

    isDeleting.value = true;

    try {
        await deleteSlide(
            slideToDelete.value,
            `/admin/carousel-home/${slideToDelete.value.id}`,
        );
    } finally {
        isDeleting.value = false;
        showConfirmDelete.value = false;
        slideToDelete.value = null;
    }
};

// Lifecycle
onMounted(() => {
    // Update slides jika ada perubahan dari prop
    refreshSlides(props.slides);

    // Debug: Check storage symlink
    console.log('🚀 Carousel Home loaded');
    debugStorageSymlink();
});

// Watch untuk debug setiap kali slides berubah
watch(
    sortedSlides,
    (newSlides) => {
        if (newSlides.length > 0) {
            console.log('📸 Slides updated:', newSlides.length);
            // Debug first slide
            debugImagePath(newSlides[0]);
        }
    },
    { immediate: true },
);
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
