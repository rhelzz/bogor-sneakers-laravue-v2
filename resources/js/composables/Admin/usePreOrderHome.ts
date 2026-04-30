import { ref } from 'vue';
import { INITIAL_PREORDER_PAYLOAD } from '@/constants/Admin/preOrderHomeConstants';
import { createAdminPreOrderHomeApiService } from '@/services/Admin/preOrderHome';
import type {
    AdminAssignment,
    PreOrderAssignmentPayload,
    CatalogOption,
} from '@/types/Admin/preOrderHome';
import {
    getStatusClass,
    formatStatus,
    getSlotColor,
    formatDate,
} from '@/utils/Admin/preOrderHomeHelper';

export function usePreOrderHome(
    initialAssignments: AdminAssignment[],
    initialCatalogOptions: CatalogOption[],
    visibleLimit: number,
) {
    const assignments = ref<AdminAssignment[]>(initialAssignments);
    const catalogOptions = ref<CatalogOption[]>(initialCatalogOptions);
    const apiService = createAdminPreOrderHomeApiService();

    const isModalOpen = ref(false);
    const modalMode = ref<'add' | 'edit'>('add');
    const selectedItem = ref<AdminAssignment | null>(null);
    const isLoading = ref(false);
    const formError = ref<Record<string, string[]>>({});
    const imageLoadErrors = ref<Set<number>>(new Set());

    // Form Payload
    const formPayload = ref<PreOrderAssignmentPayload>({
        ...INITIAL_PREORDER_PAYLOAD,
    });

    const resetForm = () => {
        formPayload.value = { ...INITIAL_PREORDER_PAYLOAD };
        formError.value = {};
    };

    const formatForDatetimeLocal = (dateString: string) => {
        if (!dateString) return '';
        const date = new Date(dateString);
        // adjust to local timezone for the datetime-local input
        const offset = date.getTimezoneOffset() * 60000;
        const localISOTime = new Date(date.getTime() - offset)
            .toISOString()
            .slice(0, 16);
        return localISOTime;
    };

    // Helper untuk memperbaiki bug timezone backend (mempertahankan waktu lokal tanpa shift)
    const preserveLocalTimeForBackend = (localDateTimeStr: string) => {
        if (!localDateTimeStr) return '';
        if (localDateTimeStr.length === 16) return `${localDateTimeStr}:00.000Z`;
        if (localDateTimeStr.length === 19) return `${localDateTimeStr}.000Z`;
        // Fallback jika format tidak dikenali
        return new Date(localDateTimeStr).toISOString();
    };

    const openModal = (
        mode: 'add' | 'edit',
        item: AdminAssignment | null = null,
    ) => {
        modalMode.value = mode;
        selectedItem.value = item;
        resetForm();

        if (mode === 'edit' && item) {
            formPayload.value = {
                catalog_id: item.catalog_id,
                status: item.status,
                is_visible: item.is_visible,
                max_slots: item.max_slots,
                filled_slots: item.filled_slots,
                countdown_ends_at: formatForDatetimeLocal(
                    item.countdown_ends_at || '',
                ),
                batch_label: item.batch_label || '',
                urutan: item.urutan,
            };
        }

        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        setTimeout(() => {
            selectedItem.value = null;
            resetForm();
        }, 300);
    };

    const saveAssignment = async () => {
        isLoading.value = true;
        formError.value = {};

        try {
            let response;
            const payloadToSubmit = { ...formPayload.value };
            if (!payloadToSubmit.urutan) delete payloadToSubmit.urutan; // Remove if empty so backend handles it

            // Konversi countdown_ends_at untuk menghindari shift jam di backend
            if (payloadToSubmit.countdown_ends_at) {
                payloadToSubmit.countdown_ends_at = preserveLocalTimeForBackend(
                    payloadToSubmit.countdown_ends_at,
                );
            }

            if (modalMode.value === 'add') {
                response = await apiService.createAssignment(payloadToSubmit);
                if (response.assignment) {
                    assignments.value.push(response.assignment);
                }
            } else if (modalMode.value === 'edit' && selectedItem.value) {
                response = await apiService.updateAssignment(
                    selectedItem.value.id,
                    payloadToSubmit,
                );
                if (response.assignment) {
                    const index = assignments.value.findIndex(
                        (a) => a.id === selectedItem.value!.id,
                    );
                    if (index !== -1) {
                        assignments.value[index] = response.assignment;
                    }
                }
            }

            // Urutkan ulang assignment
            assignments.value.sort(
                (a, b) => a.urutan - b.urutan || a.id - b.id,
            );
            closeModal();
        } catch (error: any) {
            if (error.payload?.errors) {
                formError.value = error.payload.errors;
            } else {
                console.error('Error saving assignment:', error);
                alert(
                    error.payload?.message ||
                        'Terjadi kesalahan saat menyimpan data.',
                );
            }
        } finally {
            isLoading.value = false;
        }
    };

    const deleteAssignment = async (item: AdminAssignment) => {
        if (!confirm('Apakah Anda yakin ingin menghapus pre-order ini?')) {
            return;
        }

        const originalAssignments = [...assignments.value];

        try {
            // Optimistic UI Delete
            assignments.value = assignments.value.filter(
                (a) => a.id !== item.id,
            );
            await apiService.deleteAssignment(item.id);
        } catch (error) {
            console.error('Error deleting assignment:', error);
            // Revert state
            assignments.value = originalAssignments;
            alert('Terjadi kesalahan saat menghapus data.');
        }
    };

    const toggleVisibility = async (item: AdminAssignment) => {
        const originalVisibility = item.is_visible;
        const newVisibility = !originalVisibility;

        // Check limit sebelum optimis UI update
        const currentVisibleCount = assignments.value.filter(
            (a) => a.is_visible,
        ).length;
        if (newVisibility && currentVisibleCount >= visibleLimit) {
            alert(`Maksimal ${visibleLimit} produk pre-order dapat ditampilkan.`);
            return;
        }

        // Optimistic UI Update
        item.is_visible = newVisibility;

        try {
            const payload: Partial<PreOrderAssignmentPayload> = {
                catalog_id: item.catalog_id,
                status: item.status,
                is_visible: newVisibility,
                max_slots: item.max_slots,
                filled_slots: item.filled_slots,
                countdown_ends_at: item.countdown_ends_at
                    ? preserveLocalTimeForBackend(formatForDatetimeLocal(item.countdown_ends_at))
                    : '',
                batch_label: item.batch_label,
            };

            // Hanya kirim urutan jika nilainya valid (>= 1) untuk menghindari error min:1 di validator backend
            if (item.urutan && item.urutan >= 1) {
                payload.urutan = item.urutan;
            }

            const response = await apiService.updateAssignment(
                item.id,
                payload as PreOrderAssignmentPayload,
            );
            if (response.assignment) {
                const index = assignments.value.findIndex(
                    (a) => a.id === item.id,
                );
                if (index !== -1) {
                    assignments.value[index] = response.assignment;
                }
            }
        } catch (error: any) {
            // Revert jika gagal
            item.is_visible = originalVisibility;
            
            let errorMessage = 'Gagal mengubah visibilitas.';
            if (error?.payload?.errors) {
                errorMessage = Object.values(error.payload.errors).flat().join('\n');
            } else if (error?.payload?.message) {
                errorMessage = error.payload.message;
            }
            
            console.error('Error toggling visibility:', error);
            alert(errorMessage);
        }
    };

    const trackImageError = (id: number) => {
        imageLoadErrors.value.add(id);
    };

    return {
        assignments,
        catalogOptions,
        isModalOpen,
        modalMode,
        selectedItem,
        formPayload,
        isLoading,
        formError,
        imageLoadErrors,
        openModal,
        closeModal,
        saveAssignment,
        deleteAssignment,
        toggleVisibility,
        trackImageError,
        // Helpers
        getStatusClass,
        formatStatus,
        getSlotColor,
        formatDate,
    };
}