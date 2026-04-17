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
import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { useAdminCatalogManager } from '@/composables/Admin/katalog/useAdminCatalogManager';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { CatalogAdminItem } from '@/types/catalog';

const props = defineProps<{
    catalogs: CatalogAdminItem[];
    maxImages: number;
}>();

const {
    catalogs,
    maxImages,
    sortedCatalogs,
    successMessage,
    errorMessage,
    isSubmitting,
    isFormModalOpen,
    form,
    pendingImageNames,
    openCreateModal,
    closeFormModal,
    pickPendingImages,
    submitForm,
    startEdit,
    deleteCatalog,
    uploadSingleImage,
    deleteImage,
    moveImage,
    orderedImages,
    statusBadgeClass,
    formatCurrency,
} = useAdminCatalogManager({
    initialCatalogs: props.catalogs,
    maxImages: props.maxImages,
});
</script>
