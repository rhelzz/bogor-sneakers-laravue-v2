<template>
    <AdminLayout>
        <div class="mx-auto max-w-6xl">
            <div class="accent-left mb-6">
                <h2 class="font-heading mb-1 text-2xl font-bold lg:text-3xl">
                    Kelola Galeri Karya
                </h2>
                <p class="text-sm text-hai">
                    Galeri homepage selalu 8 slot tetap. Admin dapat mengganti
                    gambar, judul, dan author tiap slot.
                </p>
            </div>

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

            <div class="mb-6 rounded-2xl border border-sumi/10 bg-washi p-4">
                <p class="text-xs text-hai">
                    Format upload: JPEG/PNG, maksimal 5MB. Setelah memilih file,
                    preview slot langsung muncul lalu otomatis diunggah ke
                    server. Judul dan author bisa diubah lalu disimpan per slot.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                <article
                    v-for="slot in slots"
                    :key="slot.id"
                    class="card-lift overflow-hidden rounded-2xl border border-sumi/10 bg-washi"
                >
                    <div
                        :class="`relative overflow-hidden bg-sumi/5 ${getAspectClass(slot.slot)}`"
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
                            class="flex h-full w-full flex-col items-center justify-center gap-2 text-hai/50"
                        >
                            <i class="bi bi-image text-4xl"></i>
                            <p class="text-xs">Belum ada gambar</p>
                        </div>

                        <div class="absolute top-3 left-3">
                            <span
                                class="rounded-full bg-sumi px-2.5 py-1 text-[11px] font-bold tracking-wide text-washi"
                            >
                                SLOT {{ slot.slot }}
                            </span>
                        </div>

                        <div
                            v-if="uploadingSlot === slot.slot"
                            class="absolute inset-0 flex items-center justify-center bg-sumi/50 text-washi"
                        >
                            <div
                                class="flex items-center gap-2 text-sm font-bold"
                            >
                                <i class="bi bi-arrow-repeat animate-spin"></i>
                                Uploading...
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2.5 p-3">
                        <label
                            :for="fileInputId(slot.slot)"
                            class="block w-full cursor-pointer rounded-lg bg-matcha px-3 py-2.5 text-center text-xs font-bold text-washi transition-all hover:bg-matcha/80"
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
                            <label class="block text-xs font-bold text-sumi/70"
                                >Judul</label
                            >
                            <input
                                v-model="metadataForms[slot.id].title"
                                type="text"
                                maxlength="120"
                                class="w-full rounded-lg border border-sumi/20 bg-white px-3 py-2 text-xs text-sumi focus:border-matcha focus:outline-none"
                                :disabled="savingMetadataSlot === slot.slot"
                            />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-bold text-sumi/70"
                                >Author</label
                            >
                            <input
                                v-model="metadataForms[slot.id].author"
                                type="text"
                                maxlength="80"
                                class="w-full rounded-lg border border-sumi/20 bg-white px-3 py-2 text-xs text-sumi focus:border-matcha focus:outline-none"
                                :disabled="savingMetadataSlot === slot.slot"
                            />
                        </div>

                        <button
                            class="w-full rounded-lg bg-sumi px-4 py-2 text-xs font-bold text-washi transition-all hover:bg-sumi/85 disabled:cursor-not-allowed disabled:opacity-50"
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
import { onUnmounted, ref } from 'vue';

import AdminLayout from '@/layouts/AdminLayout.vue';
import type { GallerySlot } from '@/types/gallery';

const props = defineProps({
    slots: {
        type: Array as () => GallerySlot[],
        default: () => [],
    },
});

type ApiSlotResponse = {
    message: string;
    slot: GallerySlot;
};

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type SlotMetaForm = {
    title: string;
    author: string;
};

const slots = ref<GallerySlot[]>(
    [...props.slots].sort((a, b) => a.slot - b.slot),
);
const uploadingSlot = ref<number | null>(null);
const savingMetadataSlot = ref<number | null>(null);
const successMessage = ref('');
const errorMessage = ref('');
const localPreviews = ref<Record<number, string>>({});
const metadataForms = ref<Record<number, SlotMetaForm>>({});

const aspectClassMap: Record<number, string> = {
    1: 'aspect-[3/4]',
    2: 'aspect-square',
    3: 'aspect-[4/5]',
    4: 'aspect-[3/4]',
    5: 'aspect-square',
    6: 'aspect-[4/3]',
    7: 'aspect-[3/4]',
    8: 'aspect-square',
};

const titleDefaults: Record<number, string> = {
    1: 'Air Max 97 x Jogja Streets',
    2: 'Samba OG - Bogor Vibe',
    3: 'Jordan 1 Bred - Cold Day',
    4: 'NB 574 Navy x Rain',
    5: 'Vans Old Skool',
    6: 'Converse Chuck 70',
    7: 'Asics Gel-Kayano',
    8: 'Puma RS-X Effekt',
};

const defaultAuthor = '@bogorsneakers';

const defaultTitleForSlot = (slot: number) => {
    return titleDefaults[slot] ?? `Galeri Karya Slot ${slot}`;
};

const normalizeAuthorInput = (author?: string | null) => {
    const trimmed = (author ?? '').trim();

    if (trimmed === '') {
        return defaultAuthor;
    }

    if (trimmed.startsWith('@')) {
        return trimmed;
    }

    return `@${trimmed}`;
};

const syncMetadataFormForSlot = (slot: GallerySlot) => {
    metadataForms.value[slot.id] = {
        title: (slot.title ?? '').trim() || defaultTitleForSlot(slot.slot),
        author: normalizeAuthorInput(slot.author),
    };
};

const initializeMetadataForms = () => {
    slots.value.forEach((slot) => {
        syncMetadataFormForSlot(slot);
    });
};

initializeMetadataForms();

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

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

const getAspectClass = (slot: number) => {
    return aspectClassMap[slot] ?? 'aspect-square';
};

const fileInputId = (slot: number) => `gallery-slot-input-${slot}`;

const getDisplayImage = (slot: GallerySlot) => {
    return localPreviews.value[slot.slot] ?? slot.image_url ?? '';
};

const formatUpdatedAt = (value?: string) => {
    if (!value) {
        return 'Belum pernah diupdate';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return 'Belum pernah diupdate';
    }

    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};

const validateFile = (file: File) => {
    if (!(file.type === 'image/jpeg' || file.type === 'image/png')) {
        return 'Format file harus JPEG atau PNG.';
    }

    if (file.size > 5 * 1024 * 1024) {
        return 'Ukuran gambar maksimal 5MB.';
    }

    return '';
};

const replaceSlotImage = async (slot: GallerySlot, file: File) => {
    const oldPreview = localPreviews.value[slot.slot];
    const optimisticPreview = URL.createObjectURL(file);

    if (oldPreview) {
        URL.revokeObjectURL(oldPreview);
    }

    localPreviews.value[slot.slot] = optimisticPreview;
    uploadingSlot.value = slot.slot;
    errorMessage.value = '';
    successMessage.value = '';

    const formData = new FormData();
    formData.append('image', file);

    try {
        const data = await requestJson<ApiSlotResponse>(
            `/admin/galeri-karya/${slot.id}/image`,
            {
                method: 'POST',
                body: formData,
            },
        );

        slots.value = slots.value.map((item) => {
            if (item.id !== slot.id) {
                return item;
            }

            return data.slot;
        });

        syncMetadataFormForSlot(data.slot);

        successMessage.value = data.message || 'Gambar slot berhasil diganti.';

        URL.revokeObjectURL(optimisticPreview);
        delete localPreviews.value[slot.slot];
    } catch (error) {
        const apiError = error as { payload?: ApiErrorResponse };
        const imageError = apiError.payload?.errors?.image?.[0];

        errorMessage.value =
            imageError ||
            apiError.payload?.message ||
            'Gagal upload gambar slot.';

        URL.revokeObjectURL(optimisticPreview);
        delete localPreviews.value[slot.slot];
    } finally {
        uploadingSlot.value = null;
    }
};

const saveMetadata = async (slot: GallerySlot) => {
    const form = metadataForms.value[slot.id];

    if (!form) {
        syncMetadataFormForSlot(slot);

        return;
    }

    const title = form.title.trim();
    const author = normalizeAuthorInput(form.author);

    if (title === '') {
        errorMessage.value = 'Judul wajib diisi.';

        return;
    }

    savingMetadataSlot.value = slot.slot;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const data = await requestJson<ApiSlotResponse>(
            `/admin/galeri-karya/${slot.id}`,
            {
                method: 'PUT',
                body: JSON.stringify({
                    title,
                    author,
                }),
            },
        );

        slots.value = slots.value.map((item) => {
            if (item.id !== slot.id) {
                return item;
            }

            return data.slot;
        });

        syncMetadataFormForSlot(data.slot);
        successMessage.value =
            data.message || 'Judul dan author berhasil disimpan.';
    } catch (error) {
        const apiError = error as { payload?: ApiErrorResponse };
        const titleError = apiError.payload?.errors?.title?.[0];
        const authorError = apiError.payload?.errors?.author?.[0];

        errorMessage.value =
            titleError ||
            authorError ||
            apiError.payload?.message ||
            'Gagal menyimpan judul dan author.';
    } finally {
        savingMetadataSlot.value = null;
    }
};

const handleFilePick = async (slot: GallerySlot, event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        return;
    }

    const validationError = validateFile(file);

    if (validationError) {
        errorMessage.value = validationError;
        input.value = '';

        return;
    }

    await replaceSlotImage(slot, file);
    input.value = '';
};

onUnmounted(() => {
    Object.values(localPreviews.value).forEach((previewUrl) => {
        URL.revokeObjectURL(previewUrl);
    });
});
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
