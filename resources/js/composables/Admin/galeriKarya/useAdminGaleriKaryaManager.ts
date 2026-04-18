import { onUnmounted, ref } from 'vue';

import { createAdminGaleriKaryaApiService } from '@/services/Admin/galeriKarya/galeriKaryaApi';
import type { GaleriKaryaMetadataForm } from '@/types/Admin/galeriKarya';
import type { GallerySlot } from '@/types/gallery';
import {
    formatGaleriKaryaUpdatedAt,
    getDefaultGaleriKaryaTitle,
    getGaleriKaryaAspectClass,
    getGaleriKaryaFileInputId,
    normalizeGaleriKaryaApiError,
    normalizeGaleriKaryaAuthorInput,
    validateGaleriKaryaFile,
} from '@/utils/Admin/galeriKarya/galeriKaryaHelpers';

type UseAdminGaleriKaryaManagerOptions = {
    initialSlots: GallerySlot[];
};

export const useAdminGaleriKaryaManager = ({
    initialSlots,
}: UseAdminGaleriKaryaManagerOptions) => {
    const slots = ref<GallerySlot[]>(
        [...initialSlots].sort((a, b) => a.slot - b.slot),
    );
    const uploadingSlot = ref<number | null>(null);
    const savingMetadataSlot = ref<number | null>(null);
    const successMessage = ref('');
    const errorMessage = ref('');
    const localPreviews = ref<Record<number, string>>({});
    const metadataForms = ref<Record<number, GaleriKaryaMetadataForm>>({});

    const apiService = createAdminGaleriKaryaApiService();

    const syncMetadataFormForSlot = (slot: GallerySlot) => {
        metadataForms.value[slot.id] = {
            title:
                (slot.title ?? '').trim() ||
                getDefaultGaleriKaryaTitle(slot.slot),
            author: normalizeGaleriKaryaAuthorInput(slot.author),
        };
    };

    const initializeMetadataForms = () => {
        slots.value.forEach((slot) => {
            syncMetadataFormForSlot(slot);
        });
    };

    initializeMetadataForms();

    const getDisplayImage = (slot: GallerySlot) => {
        return localPreviews.value[slot.slot] ?? slot.image_url ?? '';
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

        try {
            const data = await apiService.replaceSlotImage(slot.id, file);

            slots.value = slots.value.map((item) => {
                if (item.id !== slot.id) {
                    return item;
                }

                return data.slot;
            });

            syncMetadataFormForSlot(data.slot);
            successMessage.value =
                data.message || 'Gambar slot berhasil diganti.';

            URL.revokeObjectURL(optimisticPreview);
            delete localPreviews.value[slot.slot];
        } catch (error) {
            errorMessage.value = normalizeGaleriKaryaApiError(
                error,
                'Gagal upload gambar slot.',
                ['image'],
            );

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
        const author = normalizeGaleriKaryaAuthorInput(form.author);

        if (title === '') {
            errorMessage.value = 'Judul wajib diisi.';

            return;
        }

        savingMetadataSlot.value = slot.slot;
        errorMessage.value = '';
        successMessage.value = '';

        try {
            const data = await apiService.updateSlotMetadata(slot.id, {
                title,
                author,
            });

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
            errorMessage.value = normalizeGaleriKaryaApiError(
                error,
                'Gagal menyimpan judul dan author.',
                ['title', 'author'],
            );
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

        const validationError = validateGaleriKaryaFile(file);

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

    return {
        slots,
        uploadingSlot,
        savingMetadataSlot,
        successMessage,
        errorMessage,
        metadataForms,
        handleFilePick,
        saveMetadata,
        getAspectClass: getGaleriKaryaAspectClass,
        fileInputId: getGaleriKaryaFileInputId,
        getDisplayImage,
        formatUpdatedAt: formatGaleriKaryaUpdatedAt,
    };
};
