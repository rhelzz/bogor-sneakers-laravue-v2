<template>
    <AdminLayout>
        <div class="mx-auto max-w-6xl">
            <!-- Page Header -->
            <div class="accent-left mb-6">
                <h2 class="font-heading mb-1 text-2xl font-bold lg:text-3xl">
                    Kelola Carousel Home
                </h2>
                <p class="text-sm text-hai">
                    Upload dan kelola slide carousel di halaman utama website
                </p>
            </div>

            <!-- Alerts -->
            <div
                v-if="successMessage"
                class="animate-fade-in mb-4 rounded-xl border border-matcha bg-matcha/20 p-3 text-sm text-matcha"
            >
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle"></i>
                    {{ successMessage }}
                </div>
            </div>
            <div
                v-if="errorMessage"
                class="animate-fade-in mb-4 rounded-xl border border-red-500 bg-red-200/20 p-3 text-sm text-red-600"
            >
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ errorMessage }}
                </div>
            </div>

            <!-- Main Grid: Upload Form + Gallery -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Upload Form Card -->
                <div class="lg:col-span-1">
                    <div
                        class="card-lift sticky top-6 rounded-2xl border border-sumi/5 bg-washi p-6"
                    >
                        <h3
                            class="font-heading mb-5 flex items-center gap-2 text-lg font-bold"
                        >
                            <i
                                class="bi bi-cloud-arrow-up text-2xl text-matcha"
                            ></i>
                            Upload Image
                        </h3>

                        <!-- File Drop Zone -->
                        <div
                            class="mb-5 cursor-pointer rounded-xl border-2 border-dashed border-sumi/30 p-6 text-center transition-all hover:border-matcha hover:bg-matcha/5"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleFileDrop"
                            :class="{ 'border-matcha bg-matcha/5': isDragging }"
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
                                    class="bi bi-image mb-2 block text-3xl text-hai/50"
                                ></i>
                                <p class="mb-1 text-xs font-medium">
                                    {{
                                        form.image
                                            ? '✓ ' + form.image.name
                                            : 'Pilih atau Drag Image'
                                    }}
                                </p>
                                <p class="text-[11px] text-hai">
                                    JPEG atau PNG, max 5MB
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            @click="uploadSlide"
                            :disabled="isSubmitting || !form.image"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-matcha px-4 py-2.5 text-sm font-bold text-washi transition-all hover:bg-matcha/80 disabled:cursor-not-allowed disabled:bg-matcha/40"
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
                            class="mt-2.5 w-full rounded-lg bg-sumi/10 px-4 py-2.5 text-sm font-medium text-sumi transition-all hover:bg-sumi/20"
                        >
                            Reset
                        </button>

                        <!-- Info Box -->
                        <div
                            class="mt-5 rounded-lg border border-sumi/10 bg-sumi/5 p-3"
                        >
                            <p class="text-xs leading-relaxed text-hai/70">
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
                        <h3
                            class="font-heading flex items-center gap-2 text-xl font-bold"
                        >
                            <i class="bi bi-gallery text-xl text-matcha"></i>
                            Slides ({{ slides.length }})
                        </h3>
                        <span
                            v-if="activeSlides > 0"
                            class="rounded-full bg-matcha/20 px-3 py-1 text-xs font-medium text-matcha"
                        >
                            {{ activeSlides }} aktif
                        </span>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="slides.length === 0"
                        class="rounded-2xl border border-sumi/5 bg-washi p-8 text-center"
                    >
                        <i
                            class="bi bi-inbox mb-3 block text-5xl text-hai/30"
                        ></i>
                        <p class="mb-1 text-base font-bold text-hai">
                            Belum ada slide carousel
                        </p>
                        <p class="text-sm text-hai/60">
                            Mulai dengan upload image pertama di form sebelahnya
                        </p>
                    </div>

                    <!-- Gallery Grid -->
                    <div v-else class="grid grid-cols-2 gap-4 lg:grid-cols-3">
                        <div
                            v-for="(slide, idx) in slides"
                            :key="slide.id"
                            class="group card-lift relative overflow-hidden rounded-2xl border border-sumi/10 transition-all hover:shadow-xl"
                        >
                            <!-- Image Container -->
                            <div
                                class="relative aspect-video overflow-hidden bg-sumi/5"
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
                                        class="bi bi-image text-2xl text-hai/30"
                                    ></i>
                                </div>

                                <!-- Overlay -->
                                <div
                                    class="absolute inset-0 flex items-end bg-linear-to-b from-transparent via-transparent to-sumi/60 p-4 opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <div class="w-full">
                                        <!-- Order Badge -->
                                        <div
                                            class="flex items-center justify-between"
                                        >
                                            <span
                                                class="text-sm font-bold text-washi"
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
                                                        ? 'bg-matcha/30 text-matcha'
                                                        : 'bg-sumi/30 text-washi/60',
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
                                        'flex h-9 w-9 items-center justify-center rounded-lg font-bold transition-all',
                                        slide.is_active
                                            ? 'bg-matcha/80 text-washi hover:bg-matcha'
                                            : 'bg-sumi/80 text-washi/60 hover:bg-sumi',
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
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500/80 font-bold text-washi transition-all hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-60"
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
import { computed, reactive, ref } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { CarouselSlide } from '@/types/carousel';

// Props
const props = defineProps({
    slides: {
        type: Array as () => CarouselSlide[],
        default: () => [],
    },
});

// State
const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const isSubmitting = ref(false);
const isUpdating = ref<number | null>(null);
const isDeleting = ref<number | null>(null);
const successMessage = ref('');
const errorMessage = ref('');

const slides = ref<CarouselSlide[]>(props.slides);

const form = reactive({
    image: null as File | null,
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type ApiSlideResponse = {
    message: string;
    slide: CarouselSlide;
};

type ApiDeleteResponse = {
    message: string;
    id: number;
};

const activeSlides = computed(() => {
    return slides.value.filter((s) => s.is_active).length;
});

const buildHeaders = (isJsonBody: boolean) => {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    if (isJsonBody) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const requestJson = async <T,>(url: string, init: RequestInit = {}) => {
    const isFormData = init.body instanceof FormData;
    const response = await fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            ...buildHeaders(!isFormData),
            ...(init.headers ?? {}),
        },
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        throw {
            status: response.status,
            payload,
        };
    }

    return payload as T;
};

// Methods
const openFilePicker = () => {
    fileInput.value?.click();
};

const handleFileSelect = (e: Event) => {
    const input = e.target as HTMLInputElement;
    const file = input.files?.[0];

    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            errorMessage.value = 'Ukuran gambar maksimal 5MB.';

            return;
        }

        form.image = file;
        errorMessage.value = '';
    }
};

const handleFileDrop = (e: DragEvent) => {
    isDragging.value = false;
    const file = e.dataTransfer?.files?.[0];

    if (file && (file.type === 'image/jpeg' || file.type === 'image/png')) {
        if (file.size > 5 * 1024 * 1024) {
            errorMessage.value = 'Ukuran gambar maksimal 5MB.';

            return;
        }

        form.image = file;
        errorMessage.value = '';
    }
};

const resetForm = () => {
    form.image = null;

    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const uploadSlide = async () => {
    if (!form.image) {
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    const pickedImage = form.image;
    const tempId = -Date.now();
    const previewUrl = URL.createObjectURL(pickedImage);

    const optimisticSlide: CarouselSlide = {
        id: tempId,
        image_path: previewUrl,
        is_active: true,
        order: slides.value.length,
    };

    slides.value = [optimisticSlide, ...slides.value];

    const formData = new FormData();
    formData.append('image', pickedImage);

    resetForm();

    try {
        const data = await requestJson<ApiSlideResponse>(
            '/admin/carousel-home',
            {
                method: 'POST',
                body: formData,
            },
        );

        slides.value = slides.value.map((slide) => {
            if (slide.id !== tempId) {
                return slide;
            }

            return data.slide;
        });

        successMessage.value = data.message || 'Image berhasil diupload!';
    } catch (error) {
        slides.value = slides.value.filter((slide) => slide.id !== tempId);

        const apiError = error as { payload?: ApiErrorResponse };
        const imageError = apiError.payload?.errors?.image?.[0];
        errorMessage.value =
            imageError ||
            apiError.payload?.message ||
            'Gagal upload image. Cek ukuran file atau format.';
    } finally {
        URL.revokeObjectURL(previewUrl);
        isSubmitting.value = false;
    }
};

const toggleActive = async (id: number) => {
    const slide = slides.value.find((s) => s.id === id);

    if (!slide || id < 0) {
        return;
    }

    const oldState = slide.is_active;
    slide.is_active = !slide.is_active;

    isUpdating.value = id;
    errorMessage.value = '';

    try {
        await requestJson<ApiSlideResponse>(`/admin/carousel-home/${id}`, {
            method: 'PUT',
            body: JSON.stringify({
                is_active: slide.is_active,
            }),
        });

        successMessage.value = 'Status diperbarui!';
    } catch (error) {
        slide.is_active = oldState;
        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value =
            apiError.payload?.message || 'Gagal update status.';
    } finally {
        isUpdating.value = null;
    }
};

const deleteSlide = async (id: number) => {
    if (!confirm('Hapus slide ini? Gambar akan dihapus dari server.')) {
        return;
    }

    if (id < 0) {
        slides.value = slides.value.filter((slide) => slide.id !== id);

        return;
    }

    const index = slides.value.findIndex((slide) => slide.id === id);

    if (index === -1) {
        return;
    }

    const [deletedSlide] = slides.value.splice(index, 1);
    isDeleting.value = id;
    errorMessage.value = '';

    try {
        const data = await requestJson<ApiDeleteResponse>(
            `/admin/carousel-home/${id}`,
            {
                method: 'DELETE',
            },
        );

        successMessage.value = data.message || 'Slide dihapus!';
    } catch (error) {
        slides.value.splice(index, 0, deletedSlide);
        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value = apiError.payload?.message || 'Gagal hapus slide.';
    } finally {
        isDeleting.value = null;
    }
};

const getImageUrl = (imagePath: string) => {
    if (!imagePath) {
        return '';
    }

    if (
        imagePath.startsWith('blob:') ||
        imagePath.startsWith('http://') ||
        imagePath.startsWith('https://') ||
        imagePath.startsWith('/')
    ) {
        return imagePath;
    }

    return `/storage/${imagePath}`;
};
</script>

<style scoped>
.accent-left {
    border-left: 4px solid #7c8c5a;
    padding-left: 1rem;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
