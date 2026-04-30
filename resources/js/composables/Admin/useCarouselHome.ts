/**
 * Carousel Home Composable
 * State management dan logic untuk halaman Carousel Home admin
 */

import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { CAROUSEL_MESSAGES } from '@/constants/Admin/carouselConstants';
import { validateImage, formatOrderNumber, getStatusColor, getStatusLabel } from '@/utils/Admin/carouselHelper';

export interface ICarouselSlide {
  id: number;
  image_path: string;
  image_url: string;
  is_active: boolean;
  order: number;
  created_at: string;
  updated_at: string;
}

export function useCarouselHome(initialSlides: ICarouselSlide[] = []) {
  // State
  const slides = ref<ICarouselSlide[]>(initialSlides);
  const selectedFile = ref<File | null>(null);
  const previewUrl = ref<string>('');
  const isUploading = ref(false);
  const showUploadError = ref(false);
  const uploadError = ref<string[]>([]);
  const imageLoadErrors = ref<Map<number, string>>(new Map()); // Track detailed image load errors

  // Form untuk upload
  const uploadForm = useForm({
    image: null as File | null,
  });

  // Form untuk update status
  const updateForm = useForm({
    is_active: false,
  });

  // Computed
  const totalSlides = computed(() => slides.value.length);

  const activeSlides = computed(() =>
    slides.value.filter((slide) => slide.is_active).length
  );

  const inactiveSlides = computed(() =>
    slides.value.filter((slide) => !slide.is_active).length
  );

  const sortedSlides = computed(() =>
    [...slides.value].sort((a, b) => a.order - b.order)
  );

  // Methods untuk Upload

  /**
   * Handle file selection dari input
   */
  const handleFileSelect = (file: File) => {
    // Validasi file
    const validation = validateImage(file);

    if (!validation.valid) {
      uploadError.value = validation.errors;
      showUploadError.value = true;
      selectedFile.value = null;
      previewUrl.value = '';
      return;
    }

    // Clear error jika validasi sukses
    uploadError.value = [];
    showUploadError.value = false;

    // Set selected file
    selectedFile.value = file;

    // Generate preview INSTANT dengan URL.createObjectURL
    previewUrl.value = URL.createObjectURL(file);
  };

  /**
   * Hapus file yang dipilih
   */
  const clearSelectedFile = () => {
    // Revoke object URL untuk cleanup memory
    if (previewUrl.value) {
      URL.revokeObjectURL(previewUrl.value);
    }
    selectedFile.value = null;
    previewUrl.value = '';
    uploadError.value = [];
    showUploadError.value = false;
  };

  /**
   * Upload gambar ke backend dengan real-time response
   */
  const uploadImage = async (route: string) => {
    if (!selectedFile.value) {
      uploadError.value = ['Pilih gambar terlebih dahulu'];
      showUploadError.value = true;
      return;
    }

    isUploading.value = true;

    try {
      // Prepare FormData
      const formData = new FormData();
      formData.append('image', selectedFile.value);

      // Use fetch dengan Inertia header CSRF token
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

      const response = await fetch(route, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'X-Requested-With': 'XMLHttpRequest', // Trigger isXmlHttpRequest() di Laravel
        },
      });

      if (!response.ok) {
        const errorData = await response.json();
        uploadError.value = Object.values(errorData.errors || {}).flat() as string[];
        showUploadError.value = true;
        console.error('Upload validation error:', errorData);
        isUploading.value = false;
        return;
      }

      // Parse JSON response dari controller
      const data = await response.json();
      const slideData = data.slide;

      // Validate response has required fields
      if (!slideData?.id || !slideData?.image_path || !slideData?.image_url) {
        uploadError.value = ['⚠️ Server response tidak lengkap'];
        showUploadError.value = true;
        console.error('Invalid response from server:', slideData);
        isUploading.value = false;
        return;
      }

      const newSlide: ICarouselSlide = {
        id: slideData.id,
        image_path: slideData.image_path,
        image_url: slideData.image_url,
        is_active: slideData.is_active ?? true,
        order: slideData.order || totalSlides.value + 1,
        created_at: slideData.created_at || new Date().toISOString(),
        updated_at: slideData.updated_at || new Date().toISOString(),
      };

      // Add slide to array IMMEDIATELY (real-time) ✨
      slides.value.push(newSlide);
      imageLoadErrors.value.delete(newSlide.id);

      console.log('✅ Upload berhasil! Slide ditambahkan real-time:', newSlide);

      // Clear form dan error
      clearSelectedFile();
      uploadError.value = [];
      showUploadError.value = false;
    } catch (error) {
      console.error('Upload error:', error);
      uploadError.value = ['Gagal mengunggah gambar: ' + (error instanceof Error ? error.message : 'Unknown error')];
      showUploadError.value = true;
    } finally {
      isUploading.value = false;
    }
  };

  // Methods untuk Update/Delete

  /**
   * Toggle status is_active slide
   */
  const toggleSlideStatus = async (slide: ICarouselSlide, route: string) => {
    const newStatus = !slide.is_active;

    try {
      updateForm.is_active = newStatus;
      updateForm.put(route, {
        onSuccess: () => {
          // Update local state
          const index = slides.value.findIndex((s) => s.id === slide.id);
          if (index !== -1) {
            slides.value[index].is_active = newStatus;
          }
        },
        onError: (errors) => {
          console.error('Update error:', errors);
        },
      });
    } catch (error) {
      console.error('Toggle status error:', error);
    }
  };

  /**
   * Hapus slide
   */
  const deleteSlide = async (slide: ICarouselSlide, route: string) => {
    try {
      updateForm.delete(route, {
        onSuccess: () => {
          // Hapus dari local state
          const index = slides.value.findIndex((s) => s.id === slide.id);
          if (index !== -1) {
            slides.value.splice(index, 1);
          }
        },
        onError: (errors) => {
          console.error('Delete error:', errors);
        },
      });
    } catch (error) {
      console.error('Delete slide error:', error);
    }
  };

  /**
   * Hapus semua slide (batch delete)
   */
  const deleteAllSlides = (slides: ICarouselSlide[], deleteRoute: string) => {
    slides.forEach((slide) => deleteSlide(slide, deleteRoute));
  };

  // Helper methods untuk display

  /**
   * Format nomor urutan
   */
  const formatOrder = (order: number): string => {
    return formatOrderNumber(order);
  };

  /**
   * Get warna status badge
   */
  const getStatusBadgeColor = (isActive: boolean): string => {
    return getStatusColor(isActive);
  };

  /**
   * Get label status
   */
  const getStatusBadgeLabel = (isActive: boolean): string => {
    return getStatusLabel(isActive);
  };

  /**
   * Refresh slides dari server (jika diperlukan)
   */
  const refreshSlides = (newSlides: ICarouselSlide[]) => {
    slides.value = newSlides;
  };

  /**
   * Track image load error dengan detail
   */
  const trackImageLoadError = (slideId: number, errorMessage: string) => {
    imageLoadErrors.value.set(slideId, errorMessage);
    console.error(`❌ Image load error for slide ${slideId}:`, errorMessage);
  };

  /**
   * Get image load error details
   */
  const getImageLoadError = (slideId: number): string | undefined => {
    return imageLoadErrors.value.get(slideId);
  };

  /**
   * Clear semua image load errors
   */
  const clearImageLoadErrors = () => {
    imageLoadErrors.value.clear();
  };

  return {
    // State
    slides,
    selectedFile,
    previewUrl,
    isUploading,
    showUploadError,
    uploadError,
    uploadForm,
    updateForm,
    imageLoadErrors,

    // Computed
    totalSlides,
    activeSlides,
    inactiveSlides,
    sortedSlides,

    // Upload methods
    handleFileSelect,
    clearSelectedFile,
    uploadImage,

    // Update/Delete methods
    toggleSlideStatus,
    deleteSlide,
    deleteAllSlides,

    // Display methods
    formatOrder,
    getStatusBadgeColor,
    getStatusBadgeLabel,
    refreshSlides,

    // Image error tracking
    trackImageLoadError,
    getImageLoadError,
    clearImageLoadErrors,
  };
}
