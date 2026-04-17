import { computed, reactive, ref } from 'vue';

import {
    CATALOG_DEFAULT_PREORDER_MAX_DAYS,
    CATALOG_DEFAULT_PREORDER_MIN_DAYS,
    CATALOG_DEFAULT_STATUS,
} from '@/constants/Admin/katalog';
import { createAdminCatalogApiService } from '@/services/Admin/katalog/catalogApi';
import type { CatalogFormState, CatalogUpsertPayload } from '@/types/Admin/katalog';
import type { CatalogAdminItem } from '@/types/catalog';
import {
    formatCatalogCurrency,
    isValidCatalogImageFile,
    normalizeCatalogApiError,
    orderedCatalogImages,
    parseCatalogSizes,
    statusBadgeClassForCatalog,
} from '@/utils/Admin/katalog/catalogHelpers';

type UseAdminCatalogManagerOptions = {
    initialCatalogs: CatalogAdminItem[];
    maxImages: number;
};

export const useAdminCatalogManager = ({
    initialCatalogs,
    maxImages,
}: UseAdminCatalogManagerOptions) => {
    const catalogs = ref<CatalogAdminItem[]>([...initialCatalogs]);
    const successMessage = ref('');
    const errorMessage = ref('');
    const isSubmitting = ref(false);
    const isFormModalOpen = ref(false);
    const pendingImageFiles = ref<File[]>([]);

    const form = reactive<CatalogFormState>({
        id: null,
        name: '',
        code: '',
        brand: '',
        collection: '',
        description: '',
        price: 0,
        status: CATALOG_DEFAULT_STATUS,
        preorder_min_days: CATALOG_DEFAULT_PREORDER_MIN_DAYS,
        preorder_max_days: CATALOG_DEFAULT_PREORDER_MAX_DAYS,
        popularity_score: 0,
        sort_order: 0,
        is_active: true,
        sizes_raw: '',
    });

    const apiService = createAdminCatalogApiService();

    const sortedCatalogs = computed(() => {
        return [...catalogs.value].sort((a, b) => {
            const orderCompare = a.sort_order - b.sort_order;

            if (orderCompare !== 0) {
                return orderCompare;
            }

            return b.id - a.id;
        });
    });

    const pendingImageNames = computed(() => {
        return pendingImageFiles.value.map((item) => item.name);
    });

    const flashSuccess = (message: string) => {
        successMessage.value = message;
        errorMessage.value = '';
    };

    const flashError = (message: string) => {
        errorMessage.value = message;
        successMessage.value = '';
    };

    const clearPendingImages = () => {
        pendingImageFiles.value = [];
    };

    const resetForm = () => {
        form.id = null;
        form.name = '';
        form.code = '';
        form.brand = '';
        form.collection = '';
        form.description = '';
        form.price = 0;
        form.status = CATALOG_DEFAULT_STATUS;
        form.preorder_min_days = CATALOG_DEFAULT_PREORDER_MIN_DAYS;
        form.preorder_max_days = CATALOG_DEFAULT_PREORDER_MAX_DAYS;
        form.popularity_score = 0;
        form.sort_order = catalogs.value.length;
        form.is_active = true;
        form.sizes_raw = '';

        clearPendingImages();
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

    const upsertCatalogInState = (catalog: CatalogAdminItem) => {
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
            return isValidCatalogImageFile(file);
        });

        pendingImageFiles.value = allowed.slice(0, maxImages);

        input.value = '';
    };

    const uploadImageFile = async (catalogId: number, file: File) => {
        const result = await apiService.uploadCatalogImage(catalogId, file);
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
        const parsedSizes = parseCatalogSizes(form.sizes_raw);

        if (parsedSizes.length === 0) {
            flashError('Minimal satu ukuran valid (36-50) wajib diisi.');

            return;
        }

        if (form.status === 'po') {
            const minDays = Number(
                form.preorder_min_days ?? CATALOG_DEFAULT_PREORDER_MIN_DAYS,
            );
            const maxDays = Number(
                form.preorder_max_days ?? CATALOG_DEFAULT_PREORDER_MAX_DAYS,
            );

            if (maxDays < minDays) {
                flashError(
                    'Estimasi PO maksimum tidak boleh lebih kecil dari minimum.',
                );

                return;
            }
        }

        const payload: CatalogUpsertPayload = {
            name: form.name,
            code: form.code,
            brand: form.brand,
            collection: form.collection,
            description: form.description || null,
            price: Number(form.price),
            status: form.status,
            preorder_min_days:
                form.status === 'po' ? form.preorder_min_days : null,
            preorder_max_days:
                form.status === 'po' ? form.preorder_max_days : null,
            popularity_score: Number(form.popularity_score),
            sort_order: Number(form.sort_order),
            is_active: form.is_active,
            sizes: parsedSizes,
        };

        isSubmitting.value = true;
        errorMessage.value = '';

        try {
            const response = await apiService.upsertCatalog(form.id, payload);

            upsertCatalogInState(response.catalog);

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
            flashError(
                normalizeCatalogApiError(error, 'Gagal menyimpan data katalog.'),
            );
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
            const result = await apiService.deleteCatalog(catalog.id);
            catalogs.value = catalogs.value.filter((item) => item.id !== result.id);

            if (form.id === result.id) {
                resetForm();
                isFormModalOpen.value = false;
            }

            flashSuccess(result.message);
        } catch (error) {
            flashError(normalizeCatalogApiError(error, 'Gagal menghapus katalog.'));
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
            flashError(
                normalizeCatalogApiError(error, 'Gagal upload gambar katalog.'),
            );
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
            const result = await apiService.deleteCatalogImage(catalog.id, imageId);
            const index = catalogs.value.findIndex((item) => item.id === catalog.id);

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
            flashError(
                normalizeCatalogApiError(error, 'Gagal menghapus gambar katalog.'),
            );
        }
    };

    const moveImage = async (
        catalog: CatalogAdminItem,
        imageId: number,
        direction: 'up' | 'down',
    ) => {
        const sorted = orderedCatalogImages(catalog);
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
            const result = await apiService.reorderCatalogImages(catalog.id, ids);
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
            flashError(
                normalizeCatalogApiError(error, 'Gagal mengubah urutan gambar.'),
            );
        }
    };

    resetForm();

    return {
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
        orderedImages: orderedCatalogImages,
        statusBadgeClass: statusBadgeClassForCatalog,
        formatCurrency: formatCatalogCurrency,
    };
};
