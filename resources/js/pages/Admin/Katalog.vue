<template>
    <AdminLayout>
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola Katalog"
                description="Data katalog dikelola penuh dari backend Laravel + Inertia."
            >
                <template #actions>
                    <button
                        class="admin-btn admin-btn-primary"
                        @click="openCreateModal"
                    >
                        <i class="bi bi-plus-circle"></i>
                        Tambah Produk
                    </button>
                </template>
            </AdminPageHeader>

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <div class="mb-4 flex items-center justify-between">
                <h3 class="admin-section-title flex items-center gap-2">
                    <i class="bi bi-grid text-sm"></i>
                    Produk ({{ catalogs.length }})
                </h3>
            </div>

            <div v-if="catalogs.length === 0" class="admin-empty-state">
                <i
                    class="bi bi-box-seam mb-2 block text-4xl text-slate-400"
                ></i>
                <p class="mb-1 text-sm font-semibold text-slate-700">
                    Belum ada data katalog
                </p>
                <p class="admin-muted-text">
                    Klik tombol Tambah Produk untuk memulai.
                </p>
            </div>

            <div
                v-else
                class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="catalog in sortedCatalogs"
                    :key="catalog.id"
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white"
                >
                    <div
                        class="relative aspect-4/3 overflow-hidden bg-slate-100"
                    >
                        <img
                            v-if="catalog.images[0]?.image_url"
                            :src="catalog.images[0].image_url"
                            :alt="catalog.name"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-slate-400"
                        >
                            <i class="bi bi-image text-4xl"></i>
                        </div>

                        <span
                            class="absolute top-2.5 left-2.5 rounded-full px-3 py-1 text-[10px] tracking-wider uppercase"
                            :class="statusBadgeClass(catalog.status)"
                        >
                            {{ catalog.status }}
                        </span>

                        <span
                            class="absolute top-2.5 right-2.5 rounded-full border border-slate-300 bg-white px-2.5 py-1 text-[10px] text-slate-700"
                        >
                            #{{ catalog.sort_order }}
                        </span>
                    </div>

                    <div class="space-y-3 p-3.5">
                        <div>
                            <p
                                class="text-[10px] tracking-wider text-hai uppercase"
                            >
                                {{ catalog.brand }} · {{ catalog.collection }}
                            </p>
                            <p class="font-heading text-base font-bold">
                                {{ catalog.name }}
                            </p>
                            <p class="text-xs text-hai">{{ catalog.code }}</p>
                            <p class="mt-1 text-sm font-semibold">
                                {{ formatCurrency(catalog.price) }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-1">
                            <span
                                v-for="size in catalog.sizes"
                                :key="`${catalog.id}-${size}`"
                                class="rounded border border-slate-200 bg-slate-100 px-2 py-1 text-[10px] text-slate-700"
                            >
                                EU {{ size }}
                            </span>
                        </div>

                        <div class="admin-card-muted">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-xs font-bold text-slate-700">
                                    Gambar ({{ catalog.images.length }}/{{
                                        maxImages
                                    }})
                                </p>
                                <label
                                    v-if="catalog.images.length < maxImages"
                                    class="admin-btn admin-btn-primary px-2 py-1 text-[10px]"
                                >
                                    + Upload
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        @change="
                                            uploadSingleImage(catalog, $event)
                                        "
                                    />
                                </label>
                            </div>

                            <div
                                v-if="catalog.images.length === 0"
                                class="rounded-lg border border-dashed border-slate-300 p-3 text-center text-[11px] text-slate-500"
                            >
                                Belum ada gambar.
                            </div>

                            <div v-else class="space-y-2">
                                <div
                                    v-for="image in orderedImages(catalog)"
                                    :key="image.id"
                                    class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white p-2"
                                >
                                    <img
                                        :src="image.image_url"
                                        alt="Katalog image"
                                        class="h-10 w-10 rounded object-cover"
                                    />
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-[10px] font-semibold text-slate-700"
                                        >
                                            Posisi {{ image.position }}
                                        </p>
                                        <p
                                            class="truncate text-[10px] text-slate-500"
                                        >
                                            {{ image.image_path }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button
                                            class="admin-btn admin-btn-soft px-2 py-1 text-[10px]"
                                            :disabled="image.position === 1"
                                            @click="
                                                moveImage(
                                                    catalog,
                                                    image.id,
                                                    'up',
                                                )
                                            "
                                        >
                                            <i class="bi bi-chevron-up"></i>
                                        </button>
                                        <button
                                            class="admin-btn admin-btn-soft px-2 py-1 text-[10px]"
                                            :disabled="
                                                image.position ===
                                                catalog.images.length
                                            "
                                            @click="
                                                moveImage(
                                                    catalog,
                                                    image.id,
                                                    'down',
                                                )
                                            "
                                        >
                                            <i class="bi bi-chevron-down"></i>
                                        </button>
                                        <button
                                            class="admin-btn admin-btn-danger px-2 py-1 text-[10px]"
                                            @click="
                                                deleteImage(catalog, image.id)
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <button
                                class="admin-btn admin-btn-secondary flex-1"
                                @click="startEdit(catalog)"
                            >
                                Edit
                            </button>
                            <a
                                :href="`/katalog/${catalog.route_key}`"
                                target="_blank"
                                class="admin-btn admin-btn-soft"
                            >
                                Detail
                            </a>
                            <button
                                class="admin-btn admin-btn-danger"
                                @click="deleteCatalog(catalog)"
                            >
                                Hapus
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <Transition
                enter-active-class="transition duration-300 ease-in-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-250 ease-in-out"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isFormModalOpen"
                    class="fixed inset-0 z-70 flex items-center justify-center bg-sumi/45 p-4"
                    @click.self="closeFormModal"
                >
                    <Transition
                        enter-active-class="transition duration-300 ease-in-out"
                        enter-from-class="translate-y-3 scale-95 opacity-0"
                        enter-to-class="translate-y-0 scale-100 opacity-100"
                        leave-active-class="transition duration-250 ease-in-out"
                        leave-from-class="translate-y-0 scale-100 opacity-100"
                        leave-to-class="translate-y-3 scale-95 opacity-0"
                    >
                        <div
                            v-if="isFormModalOpen"
                            class="w-full max-w-3xl overflow-hidden rounded-2xl border border-sumi/10 bg-washi shadow-[0_20px_50px_rgba(26,26,26,0.2)]"
                        >
                            <div
                                class="flex items-center justify-between border-b border-sumi/10 px-5 py-3"
                            >
                                <h3
                                    class="font-heading flex items-center gap-2 text-lg font-bold"
                                >
                                    <i
                                        class="bi bi-pencil-square text-matcha"
                                    ></i>
                                    {{
                                        form.id === null
                                            ? 'Tambah Produk Katalog'
                                            : 'Edit Produk Katalog'
                                    }}
                                </h3>
                                <button
                                    class="rounded-md border border-sumi/15 px-2 py-1 text-xs text-hai transition hover:bg-sumi/5 hover:text-sumi"
                                    :disabled="isSubmitting"
                                    @click="closeFormModal"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>

                            <div
                                class="max-h-[calc(90vh-138px)] overflow-y-auto px-5 py-4"
                            >
                                <div class="space-y-3.5">
                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] tracking-wider text-hai"
                                            >NAMA KATALOG</label
                                        >
                                        <input
                                            v-model.trim="form.name"
                                            type="text"
                                            maxlength="160"
                                            class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            placeholder="Contoh: Air Max 97 Silver Bullet"
                                        />
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >KODE</label
                                            >
                                            <input
                                                v-model.trim="form.code"
                                                type="text"
                                                maxlength="80"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm uppercase transition-all outline-none focus:border-matcha"
                                                placeholder="BGS-XYZ-001"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >HARGA</label
                                            >
                                            <input
                                                v-model.number="form.price"
                                                type="number"
                                                min="0"
                                                step="1000"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                                placeholder="1850000"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >BRAND</label
                                            >
                                            <input
                                                v-model.trim="form.brand"
                                                type="text"
                                                maxlength="120"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                                placeholder="Nike"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >KOLEKSI</label
                                            >
                                            <input
                                                v-model.trim="form.collection"
                                                type="text"
                                                maxlength="120"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                                placeholder="Lifestyle"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] tracking-wider text-hai"
                                            >DESKRIPSI</label
                                        >
                                        <textarea
                                            v-model.trim="form.description"
                                            rows="3"
                                            class="w-full resize-none rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            placeholder="Deskripsi produk katalog"
                                        ></textarea>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >STATUS</label
                                            >
                                            <select
                                                v-model="form.status"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            >
                                                <option value="ready">
                                                    Ready
                                                </option>
                                                <option value="po">PO</option>
                                                <option value="habis">
                                                    Habis
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >POPULARITAS (0-100)</label
                                            >
                                            <input
                                                v-model.number="
                                                    form.popularity_score
                                                "
                                                type="number"
                                                min="0"
                                                max="100"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >EST. PO MIN (hari)</label
                                            >
                                            <input
                                                v-model.number="
                                                    form.preorder_min_days
                                                "
                                                type="number"
                                                min="1"
                                                max="365"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha disabled:opacity-50"
                                                :disabled="form.status !== 'po'"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >EST. PO MAX (hari)</label
                                            >
                                            <input
                                                v-model.number="
                                                    form.preorder_max_days
                                                "
                                                type="number"
                                                min="1"
                                                max="365"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha disabled:opacity-50"
                                                :disabled="form.status !== 'po'"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                                    >
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >SORT ORDER</label
                                            >
                                            <input
                                                v-model.number="form.sort_order"
                                                type="number"
                                                min="0"
                                                max="9999"
                                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                class="mb-1 block text-[11px] tracking-wider text-hai"
                                                >AKTIF</label
                                            >
                                            <button
                                                type="button"
                                                @click="
                                                    form.is_active =
                                                        !form.is_active
                                                "
                                                :class="[
                                                    'mt-0.5 flex h-10 w-full items-center rounded-lg px-1 transition-all',
                                                    form.is_active
                                                        ? 'bg-matcha/90'
                                                        : 'bg-sumi/20',
                                                ]"
                                            >
                                                <span
                                                    :class="[
                                                        'h-8 w-8 rounded-md bg-washi transition-all',
                                                        form.is_active
                                                            ? 'translate-x-[calc(100%-4px)]'
                                                            : 'translate-x-0',
                                                    ]"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] tracking-wider text-hai"
                                            >UKURAN (36-50, pisahkan
                                            koma)</label
                                        >
                                        <input
                                            v-model.trim="form.sizes_raw"
                                            type="text"
                                            class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                                            placeholder="36, 37, 38, 39"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1 block text-[11px] tracking-wider text-hai"
                                            >UPLOAD GAMBAR AWAL</label
                                        >
                                        <input
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            multiple
                                            class="block w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-xs"
                                            @change="pickPendingImages"
                                        />
                                        <p class="mt-1 text-[11px] text-hai">
                                            Maks {{ maxImages }} gambar. Sisanya
                                            bisa diupload setelah produk
                                            tersimpan.
                                        </p>
                                        <p
                                            v-if="pendingImageNames.length > 0"
                                            class="mt-1 text-[11px] text-sumi"
                                        >
                                            {{ pendingImageNames.join(', ') }}
                                        </p>
                                    </div>

                                    <div
                                        class="rounded-lg border border-sumi/10 bg-sumi/5 p-3"
                                    >
                                        <p
                                            class="text-xs leading-relaxed text-hai/70"
                                        >
                                            Diskon belum diaktifkan ke tabel
                                            terpisah. Field backend sudah siap
                                            untuk fase berikutnya.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-end gap-2 border-t border-sumi/10 px-5 py-3"
                            >
                                <button
                                    class="rounded-lg bg-sumi/10 px-4 py-2 text-sm font-medium text-sumi transition hover:bg-sumi/20"
                                    :disabled="isSubmitting"
                                    @click="closeFormModal"
                                >
                                    Batal
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-lg bg-matcha px-4 py-2 text-sm font-bold text-washi transition-all hover:bg-matcha/80 disabled:cursor-not-allowed disabled:bg-matcha/45"
                                    :disabled="isSubmitting"
                                    @click="submitForm"
                                >
                                    <i
                                        v-if="!isSubmitting"
                                        class="bi"
                                        :class="
                                            form.id === null
                                                ? 'bi-plus-circle'
                                                : 'bi-save'
                                        "
                                    ></i>
                                    <i
                                        v-else
                                        class="bi bi-hourglass-split animate-spin"
                                    ></i>
                                    {{
                                        isSubmitting
                                            ? 'Menyimpan...'
                                            : form.id === null
                                              ? 'Tambah Produk'
                                              : 'Simpan Perubahan'
                                    }}
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type {
    CatalogAdminItem,
    CatalogImageItem,
    CatalogStatus,
} from '@/types/catalog';

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type CatalogUpsertResponse = {
    message: string;
    catalog: CatalogAdminItem;
};

type CatalogDeleteResponse = {
    message: string;
    id: number;
};

type CatalogImageUploadResponse = {
    message: string;
    image: CatalogImageItem;
    catalog_id: number;
};

type CatalogImageDeleteResponse = {
    message: string;
    id: number;
};

type CatalogImageReorderResponse = {
    message: string;
    images: CatalogImageItem[];
};

const props = defineProps<{
    catalogs: CatalogAdminItem[];
    maxImages: number;
}>();

const catalogs = ref<CatalogAdminItem[]>([...props.catalogs]);
const maxImages = props.maxImages;

const successMessage = ref('');
const errorMessage = ref('');
const isSubmitting = ref(false);
const isFormModalOpen = ref(false);

const pendingImageFiles = ref<File[]>([]);

const form = reactive({
    id: null as number | null,
    name: '',
    code: '',
    brand: '',
    collection: '',
    description: '',
    price: 0,
    status: 'ready' as CatalogStatus,
    preorder_min_days: 14 as number | null,
    preorder_max_days: 21 as number | null,
    popularity_score: 0,
    sort_order: 0,
    is_active: true,
    sizes_raw: '',
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const sortedCatalogs = computed(() => {
    return [...catalogs.value].sort((a, b) => {
        const orderCompare = a.sort_order - b.sort_order;

        if (orderCompare !== 0) {
            return orderCompare;
        }

        return b.id - a.id;
    });
});

const pendingImageNames = computed(() =>
    pendingImageFiles.value.map((item) => item.name),
);

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

const parseSizes = (raw: string) => {
    return Array.from(
        new Set(
            raw
                .split(',')
                .map((part) => Number(part.trim()))
                .filter(
                    (value) =>
                        Number.isInteger(value) && value >= 36 && value <= 50,
                ),
        ),
    ).sort((a, b) => a - b);
};

const statusBadgeClass = (status: CatalogStatus) => {
    if (status === 'habis') {
        return 'bg-sumi text-washi shadow-sm';
    }

    return 'bg-matcha text-washi shadow-sm';
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(value);
};

const orderedImages = (catalog: CatalogAdminItem) => {
    return [...catalog.images].sort((a, b) => a.position - b.position);
};

const flashSuccess = (message: string) => {
    successMessage.value = message;
    errorMessage.value = '';
};

const flashError = (message: string) => {
    errorMessage.value = message;
    successMessage.value = '';
};

const normalizeApiError = (error: unknown, fallback: string) => {
    const apiError = error as { payload?: ApiErrorResponse };

    if (apiError.payload?.errors) {
        const firstError = Object.values(apiError.payload.errors)[0]?.[0];

        if (firstError) {
            return firstError;
        }
    }

    return apiError.payload?.message || fallback;
};

const clearPendingImages = () => {
    pendingImageFiles.value = [];
};

const openCreateModal = () => {
    resetForm();
    isFormModalOpen.value = true;
};

const closeFormModal = () => {
    if (isSubmitting.value) {
        return;
    }

    isFormModalOpen.value = false;
    resetForm();
};

const resetForm = () => {
    form.id = null;
    form.name = '';
    form.code = '';
    form.brand = '';
    form.collection = '';
    form.description = '';
    form.price = 0;
    form.status = 'ready';
    form.preorder_min_days = 14;
    form.preorder_max_days = 21;
    form.popularity_score = 0;
    form.sort_order = catalogs.value.length;
    form.is_active = true;
    form.sizes_raw = '';

    clearPendingImages();
};

const upsertCatalog = (catalog: CatalogAdminItem) => {
    const next = [...catalogs.value];
    const index = next.findIndex((item) => item.id === catalog.id);

    if (index === -1) {
        next.unshift(catalog);
    } else {
        next[index] = catalog;
    }

    catalogs.value = next;
};

const pickPendingImages = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const files = Array.from(input.files ?? []);

    if (files.length === 0) {
        return;
    }

    const allowed = files.filter((file) => {
        const isValidType = ['image/jpeg', 'image/png', 'image/webp'].includes(
            file.type,
        );
        const isValidSize = file.size <= 5 * 1024 * 1024;

        return isValidType && isValidSize;
    });

    pendingImageFiles.value = allowed.slice(0, maxImages);

    input.value = '';
};

const uploadImageFile = async (catalogId: number, file: File) => {
    const formData = new FormData();
    formData.append('image', file);

    const result = await requestJson<CatalogImageUploadResponse>(
        `/admin/katalog/${catalogId}/images`,
        {
            method: 'POST',
            body: formData,
        },
    );

    const index = catalogs.value.findIndex((item) => item.id === catalogId);

    if (index === -1) {
        return;
    }

    const target = { ...catalogs.value[index] };
    target.images = [...target.images, result.image].sort(
        (a, b) => a.position - b.position,
    );

    catalogs.value[index] = target;
    catalogs.value = [...catalogs.value];
};

const submitForm = async () => {
    const parsedSizes = parseSizes(form.sizes_raw);

    if (parsedSizes.length === 0) {
        flashError('Minimal satu ukuran valid (36-50) wajib diisi.');

        return;
    }

    if (form.status === 'po') {
        const minDays = Number(form.preorder_min_days ?? 14);
        const maxDays = Number(form.preorder_max_days ?? 21);

        if (maxDays < minDays) {
            flashError(
                'Estimasi PO maksimum tidak boleh lebih kecil dari minimum.',
            );

            return;
        }
    }

    const payload = {
        name: form.name,
        code: form.code,
        brand: form.brand,
        collection: form.collection,
        description: form.description || null,
        price: Number(form.price),
        status: form.status,
        preorder_min_days: form.status === 'po' ? form.preorder_min_days : null,
        preorder_max_days: form.status === 'po' ? form.preorder_max_days : null,
        popularity_score: Number(form.popularity_score),
        sort_order: Number(form.sort_order),
        is_active: form.is_active,
        sizes: parsedSizes,
    };

    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const response = form.id
            ? await requestJson<CatalogUpsertResponse>(
                  `/admin/katalog/${form.id}`,
                  {
                      method: 'PUT',
                      body: JSON.stringify(payload),
                  },
              )
            : await requestJson<CatalogUpsertResponse>('/admin/katalog', {
                  method: 'POST',
                  body: JSON.stringify(payload),
              });

        upsertCatalog(response.catalog);

        if (pendingImageFiles.value.length > 0) {
            const limit = Math.max(
                0,
                maxImages - response.catalog.images.length,
            );
            const uploadQueue = pendingImageFiles.value.slice(0, limit);

            for (const file of uploadQueue) {
                await uploadImageFile(response.catalog.id, file);
            }
        }

        flashSuccess(response.message);
        isFormModalOpen.value = false;
        resetForm();
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal menyimpan data katalog.'));
    } finally {
        isSubmitting.value = false;
    }
};

const startEdit = (catalog: CatalogAdminItem) => {
    form.id = catalog.id;
    form.name = catalog.name;
    form.code = catalog.code;
    form.brand = catalog.brand;
    form.collection = catalog.collection;
    form.description = catalog.description ?? '';
    form.price = catalog.price;
    form.status = catalog.status;
    form.preorder_min_days = catalog.preorder_min_days;
    form.preorder_max_days = catalog.preorder_max_days;
    form.popularity_score = catalog.popularity_score;
    form.sort_order = catalog.sort_order;
    form.is_active = catalog.is_active;
    form.sizes_raw = catalog.sizes.join(', ');

    clearPendingImages();
    isFormModalOpen.value = true;
};

const deleteCatalog = async (catalog: CatalogAdminItem) => {
    const approved = window.confirm(
        `Hapus produk "${catalog.name}"? Semua gambar akan ikut terhapus.`,
    );

    if (!approved) {
        return;
    }

    try {
        const result = await requestJson<CatalogDeleteResponse>(
            `/admin/katalog/${catalog.id}`,
            {
                method: 'DELETE',
            },
        );

        catalogs.value = catalogs.value.filter((item) => item.id !== result.id);

        if (form.id === result.id) {
            resetForm();
            isFormModalOpen.value = false;
        }

        flashSuccess(result.message);
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal menghapus katalog.'));
    }
};

const uploadSingleImage = async (catalog: CatalogAdminItem, event: Event) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
        return;
    }

    if (catalog.images.length >= maxImages) {
        flashError(`Maksimal ${maxImages} gambar per produk.`);

        input.value = '';

        return;
    }

    try {
        await uploadImageFile(catalog.id, file);
        flashSuccess('Gambar katalog berhasil ditambahkan.');
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal upload gambar katalog.'));
    } finally {
        input.value = '';
    }
};

const deleteImage = async (catalog: CatalogAdminItem, imageId: number) => {
    const approved = window.confirm(
        'Hapus gambar ini? File juga akan dihapus dari storage.',
    );

    if (!approved) {
        return;
    }

    try {
        const result = await requestJson<CatalogImageDeleteResponse>(
            `/admin/katalog/${catalog.id}/images/${imageId}`,
            {
                method: 'DELETE',
            },
        );

        const index = catalogs.value.findIndex(
            (item) => item.id === catalog.id,
        );

        if (index === -1) {
            return;
        }

        const next = { ...catalogs.value[index] };

        next.images = next.images
            .filter((image) => image.id !== result.id)
            .sort((a, b) => a.position - b.position)
            .map((image, orderIndex) => ({
                ...image,
                position: orderIndex + 1,
            }));

        catalogs.value[index] = next;
        catalogs.value = [...catalogs.value];

        flashSuccess(result.message);
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal menghapus gambar katalog.'));
    }
};

const moveImage = async (
    catalog: CatalogAdminItem,
    imageId: number,
    direction: 'up' | 'down',
) => {
    const sorted = orderedImages(catalog);
    const index = sorted.findIndex((item) => item.id === imageId);

    if (index === -1) {
        return;
    }

    const targetIndex = direction === 'up' ? index - 1 : index + 1;

    if (targetIndex < 0 || targetIndex >= sorted.length) {
        return;
    }

    const reordered = [...sorted];
    const [moved] = reordered.splice(index, 1);
    reordered.splice(targetIndex, 0, moved);

    const ids = reordered.map((item) => item.id);

    try {
        const result = await requestJson<CatalogImageReorderResponse>(
            `/admin/katalog/${catalog.id}/images/reorder`,
            {
                method: 'PUT',
                body: JSON.stringify({ image_ids: ids }),
            },
        );

        const catalogIndex = catalogs.value.findIndex(
            (item) => item.id === catalog.id,
        );

        if (catalogIndex === -1) {
            return;
        }

        const nextCatalog = { ...catalogs.value[catalogIndex] };
        nextCatalog.images = result.images.sort(
            (a, b) => a.position - b.position,
        );

        catalogs.value[catalogIndex] = nextCatalog;
        catalogs.value = [...catalogs.value];

        flashSuccess(result.message);
    } catch (error) {
        flashError(normalizeApiError(error, 'Gagal mengubah urutan gambar.'));
    }
};

resetForm();
</script>
