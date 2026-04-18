<template>
    <AdminLayout>
        <Head title="Admin Carousel Home" />
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola Carousel Home"
                description="Upload dan kelola slide carousel di halaman utama website."
            />

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <!-- Main Grid: Upload Form + Gallery -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Upload Form Card -->
                <div class="lg:col-span-1">
                    <div class="admin-card admin-card-sticky">
                        <h3
                            class="admin-section-title mb-4 flex items-center gap-2"
                        >
                            <i class="bi bi-cloud-arrow-up text-base"></i>
                            Upload Image
                        </h3>

                        <!-- File Drop Zone -->
                        <div
                            class="mb-4 cursor-pointer rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center transition-all hover:border-slate-400"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleFileDrop"
                            :class="{
                                'border-slate-500 bg-slate-100': isDragging,
                            }"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/jpeg,image/png"
                                @change="handleFileSelect"
                                class="hidden"
                            />
                            <div @click="openFilePicker">
                                <i
                                    class="bi bi-image mb-2 block text-2xl text-slate-400"
                                ></i>
                                <p
                                    class="mb-1 text-xs font-semibold text-slate-700"
                                >
                                    {{
                                        form.image
                                            ? '✓ ' + form.image.name
                                            : 'Pilih atau Drag Image'
                                    }}
                                </p>
                                <p class="text-[11px] text-slate-500">
                                    JPEG atau PNG, max 5MB
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            @click="uploadSlide"
                            :disabled="isSubmitting || !form.image"
                            class="admin-btn admin-btn-primary admin-btn-block"
                        >
                            <i v-if="!isSubmitting" class="bi bi-upload"></i>
                            <i
                                v-else
                                class="bi bi-hourglass-split animate-spin"
                            ></i>
                            {{ isSubmitting ? 'Uploading...' : 'Upload Image' }}
                        </button>

                        <!-- Reset Button -->
                        <button
                            @click="resetForm"
                            class="admin-btn admin-btn-soft admin-btn-block mt-2"
                        >
                            Reset
                        </button>

                        <!-- Info Box -->
                        <div class="admin-card-muted mt-4">
                            <p class="admin-muted-text leading-relaxed">
                                <strong>Tips:</strong> Upload hanya gambar saja.
                                Dimensi ideal 16:9 atau landscape untuk hasil
                                optimal.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Gallery Section -->
                <div class="lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="admin-section-title flex items-center gap-2">
                            <i class="bi bi-gallery text-sm"></i>
                            Slides ({{ slides.length }})
                        </h3>
                        <span
                            v-if="activeSlides > 0"
                            class="admin-chip admin-chip-primary"
                        >
                            {{ activeSlides }} aktif
                        </span>
                    </div>

                    <!-- Empty State -->
                    <div v-if="slides.length === 0" class="admin-empty-state">
                        <i
                            class="bi bi-inbox mb-2 block text-4xl text-slate-400"
                        ></i>
                        <p class="mb-1 text-sm font-semibold text-slate-700">
                            Belum ada slide carousel
                        </p>
                        <p class="admin-muted-text">
                            Mulai dengan upload image pertama di form sebelahnya
                        </p>
                    </div>

                    <!-- Gallery Grid -->
                    <div
                        v-else
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <div
                            v-for="(slide, idx) in slides"
                            :key="slide.id"
                            class="group relative overflow-hidden rounded-xl border border-slate-200 bg-white transition-shadow hover:shadow-md"
                        >
                            <!-- Image Container -->
                            <div
                                class="relative aspect-video overflow-hidden bg-slate-100"
                            >
                                <img
                                    v-if="getImageUrl(slide.image_path)"
                                    :src="getImageUrl(slide.image_path)"
                                    :alt="`Carousel slide ${slide.id}`"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <i
                                        class="bi bi-image text-2xl text-slate-300"
                                    ></i>
                                </div>

                                <!-- Overlay -->
                                <div
                                    class="admin-image-overlay absolute inset-0 flex items-end p-3 opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <div class="w-full">
                                        <!-- Order Badge -->
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="text-xs font-bold text-white"
                                            >
                                                <i
                                                    class="bi bi-sort-numeric-up mr-1"
                                                ></i>
                                                #{{ idx + 1 }}
                                            </span>
                                            <span
                                                :class="[
                                                    'rounded px-2 py-1 text-[10px] font-bold',
                                                    slide.is_active
                                                        ? 'bg-emerald-100 text-emerald-700'
                                                        : 'bg-slate-100 text-slate-600',
                                                ]"
                                            >
                                                {{
                                                    slide.is_active
                                                        ? 'ACTIVE'
                                                        : 'INACTIVE'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div
                                class="absolute top-2 right-2 flex gap-2 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <!-- Toggle Button -->
                                <button
                                    @click="toggleActive(slide.id)"
                                    :disabled="isUpdating === slide.id"
                                    :class="[
                                        'flex h-8 w-8 items-center justify-center rounded-lg border text-xs font-bold transition-all',
                                        slide.is_active
                                            ? 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                            : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-100',
                                        isUpdating === slide.id &&
                                            'cursor-not-allowed opacity-60',
                                    ]"
                                >
                                    <i
                                        :class="
                                            slide.is_active
                                                ? 'bi bi-eye'
                                                : 'bi bi-eye-slash'
                                        "
                                    ></i>
                                </button>

                                <!-- Delete Button -->
                                <button
                                    @click="deleteSlide(slide.id)"
                                    :disabled="isDeleting === slide.id"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-300 bg-rose-50 text-xs font-bold text-rose-700 transition-all hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-60"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { useAdminCarouselHomeManager } from '@/composables/Admin/carouselHome/useAdminCarouselHomeManager';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { CarouselSlide } from '@/types/carousel';

const props = defineProps<{
    slides?: CarouselSlide[];
}>();

const {
    fileInput,
    isDragging,
    isSubmitting,
    isUpdating,
    isDeleting,
    successMessage,
    errorMessage,
    slides,
    form,
    activeSlides,
    openFilePicker,
    handleFileSelect,
    handleFileDrop,
    resetForm,
    uploadSlide,
    toggleActive,
    deleteSlide,
    getImageUrl,
} = useAdminCarouselHomeManager({
    initialSlides: props.slides ?? [],
});
</script>
