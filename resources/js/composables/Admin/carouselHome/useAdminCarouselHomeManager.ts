import { computed, reactive, ref } from 'vue';

import { CAROUSEL_MAX_IMAGE_FILE_SIZE_BYTES } from '@/constants/Admin/carouselHome';
import { createAdminCarouselHomeApiService } from '@/services/Admin/carouselHome/carouselHomeApi';
import type { CarouselUploadFormState } from '@/types/Admin/carouselHome';
import type { CarouselSlide } from '@/types/carousel';
import {
    getCarouselImageUrl,
    isAllowedCarouselImageSize,
    isAllowedCarouselImageType,
    normalizeCarouselApiError,
    normalizeCarouselImageUploadError,
} from '@/utils/Admin/carouselHome/carouselHomeHelpers';

type UseAdminCarouselHomeManagerOptions = {
    initialSlides: CarouselSlide[];
};

export const useAdminCarouselHomeManager = ({
    initialSlides,
}: UseAdminCarouselHomeManagerOptions) => {
    const fileInput = ref<HTMLInputElement | null>(null);
    const isDragging = ref(false);
    const isSubmitting = ref(false);
    const isUpdating = ref<number | null>(null);
    const isDeleting = ref<number | null>(null);
    const successMessage = ref('');
    const errorMessage = ref('');

    const slides = ref<CarouselSlide[]>([...initialSlides]);

    const form = reactive<CarouselUploadFormState>({
        image: null,
    });

    const apiService = createAdminCarouselHomeApiService();

    const activeSlides = computed(() => {
        return slides.value.filter((slide) => slide.is_active).length;
    });

    const openFilePicker = () => {
        fileInput.value?.click();
    };

    const resetForm = () => {
        form.image = null;

        if (fileInput.value) {
            fileInput.value.value = '';
        }
    };

    const setFileToForm = (file: File) => {
        form.image = file;
        errorMessage.value = '';
    };

    const handleFileSelect = (event: Event) => {
        const input = event.target as HTMLInputElement;
        const file = input.files?.[0];

        if (!file) {
            return;
        }

        if (!isAllowedCarouselImageSize(file)) {
            errorMessage.value = `Ukuran gambar maksimal ${CAROUSEL_MAX_IMAGE_FILE_SIZE_BYTES / 1024 / 1024}MB.`;

            return;
        }

        setFileToForm(file);
    };

    const handleFileDrop = (event: DragEvent) => {
        isDragging.value = false;
        const file = event.dataTransfer?.files?.[0];

        if (!file || !isAllowedCarouselImageType(file)) {
            return;
        }

        if (!isAllowedCarouselImageSize(file)) {
            errorMessage.value = `Ukuran gambar maksimal ${CAROUSEL_MAX_IMAGE_FILE_SIZE_BYTES / 1024 / 1024}MB.`;

            return;
        }

        setFileToForm(file);
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

        resetForm();

        try {
            const data = await apiService.uploadSlide(pickedImage);

            slides.value = slides.value.map((slide) => {
                if (slide.id !== tempId) {
                    return slide;
                }

                return data.slide;
            });

            successMessage.value = data.message || 'Image berhasil diupload!';
        } catch (error) {
            slides.value = slides.value.filter((slide) => slide.id !== tempId);
            errorMessage.value = normalizeCarouselImageUploadError(
                error,
                'Gagal upload image. Cek ukuran file atau format.',
            );
        } finally {
            URL.revokeObjectURL(previewUrl);
            isSubmitting.value = false;
        }
    };

    const toggleActive = async (id: number) => {
        const slide = slides.value.find((item) => item.id === id);

        if (!slide || id < 0) {
            return;
        }

        const oldState = slide.is_active;
        slide.is_active = !slide.is_active;

        isUpdating.value = id;
        errorMessage.value = '';

        try {
            await apiService.updateSlideStatus(id, slide.is_active);
            successMessage.value = 'Status diperbarui!';
        } catch (error) {
            slide.is_active = oldState;
            errorMessage.value = normalizeCarouselApiError(
                error,
                'Gagal update status.',
            );
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
            const data = await apiService.deleteSlide(id);
            successMessage.value = data.message || 'Slide dihapus!';
        } catch (error) {
            slides.value.splice(index, 0, deletedSlide);
            errorMessage.value = normalizeCarouselApiError(
                error,
                'Gagal hapus slide.',
            );
        } finally {
            isDeleting.value = null;
        }
    };

    return {
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
        getImageUrl: getCarouselImageUrl,
    };
};
