import { computed, reactive, ref } from 'vue';

import {
    PRE_ORDER_DEFAULT_COUNTDOWN_HOURS,
    PRE_ORDER_DEFAULT_FILLED_SLOTS,
    PRE_ORDER_DEFAULT_MAX_SLOTS,
    PRE_ORDER_DEFAULT_STATUS,
} from '@/constants/Admin/preOrderHome';
import { createAdminPreOrderHomeApiService } from '@/services/Admin/preOrderHome/preOrderHomeApi';
import type {
    CatalogOption,
    PreOrderAssignment,
    PreOrderAssignmentPayload,
    PreOrderFormState,
} from '@/types/Admin/preOrderHome';
import {
    formatPreOrderDateTime,
    normalizePreOrderApiError,
    statusBadgeClassForPreOrder,
    toIsoFromLocalPreOrderDatetime,
    toLocalPreOrderDatetimeInput,
    toSafePreOrderUrutan,
} from '@/utils/Admin/preOrderHome/preOrderHomeHelpers';

type UseAdminPreOrderHomeManagerOptions = {
    initialAssignments: PreOrderAssignment[];
    initialCatalogOptions: CatalogOption[];
    initialVisibleCount: number;
    visibleLimit: number;
};

export const useAdminPreOrderHomeManager = ({
    initialAssignments,
    initialCatalogOptions,
    initialVisibleCount,
    visibleLimit,
}: UseAdminPreOrderHomeManagerOptions) => {
    const assignments = ref<PreOrderAssignment[]>([...initialAssignments]);
    const catalogOptions = ref<CatalogOption[]>([...initialCatalogOptions]);
    const visibleCount = ref(Number(initialVisibleCount));
    const successMessage = ref('');
    const errorMessage = ref('');
    const isSubmitting = ref(false);
    const busyRowId = ref<number | null>(null);
    const editWasVisible = ref(false);

    const form = reactive<PreOrderFormState>({
        id: null,
        catalog_id: null,
        status: PRE_ORDER_DEFAULT_STATUS,
        is_visible: false,
        max_slots: PRE_ORDER_DEFAULT_MAX_SLOTS,
        filled_slots: PRE_ORDER_DEFAULT_FILLED_SLOTS,
        countdown_ends_at: '',
        batch_label: null,
        urutan: 1,
    });

    const apiService = createAdminPreOrderHomeApiService();

    const sortedAssignments = computed(() => {
        return [...assignments.value].sort((a, b) => {
            const orderCompare =
                toSafePreOrderUrutan(a.urutan) - toSafePreOrderUrutan(b.urutan);

            if (orderCompare !== 0) {
                return orderCompare;
            }

            return a.id - b.id;
        });
    });

    const selectedCatalog = computed(() => {
        if (form.catalog_id === null) {
            return null;
        }

        return (
            catalogOptions.value.find((item) => item.id === form.catalog_id) ??
            null
        );
    });

    const formProgress = computed(() => {
        const maxSlots = Math.max(Number(form.max_slots) || 0, 1);
        const filledSlots = Math.max(Number(form.filled_slots) || 0, 0);

        return Math.max(
            0,
            Math.min(100, Math.round((filledSlots / maxSlots) * 100)),
        );
    });

    const formRemainingSlots = computed(() => {
        const maxSlots = Math.max(Number(form.max_slots) || 0, 0);
        const filledSlots = Math.max(Number(form.filled_slots) || 0, 0);

        return Math.max(maxSlots - filledSlots, 0);
    });

    const canEnableVisible = computed(() => {
        if (form.is_visible) {
            return true;
        }

        if (editWasVisible.value) {
            return true;
        }

        return visibleCount.value < visibleLimit;
    });

    const usedUrutanValues = computed(() => {
        return Array.from(
            new Set(
                assignments.value
                    .filter((item) => item.id !== form.id)
                    .map((item) => toSafePreOrderUrutan(item.urutan)),
            ),
        );
    });

    const urutanOptions = computed(() => {
        const highestUrutan = assignments.value.reduce(
            (max, item) => Math.max(max, toSafePreOrderUrutan(item.urutan)),
            0,
        );
        const maxUrutan = Math.max(10, highestUrutan + 1);
        const options = [];

        for (let i = 1; i <= maxUrutan; i++) {
            options.push({
                value: i,
                label: `Urutan ${i}`,
                disabled: usedUrutanValues.value.includes(i),
            });
        }

        return options;
    });

    const flashSuccess = (message: string) => {
        successMessage.value = message;
        errorMessage.value = '';
    };

    const flashError = (message: string) => {
        errorMessage.value = message;
        successMessage.value = '';
    };

    const syncCatalogOption = (
        catalogId: number,
        assignmentId: number | null,
        isAssigned: boolean,
    ) => {
        const index = catalogOptions.value.findIndex(
            (item) => item.id === catalogId,
        );

        if (index === -1) {
            return;
        }

        const next = [...catalogOptions.value];
        next[index] = {
            ...next[index],
            is_assigned: isAssigned,
            assignment_id: assignmentId,
        };

        catalogOptions.value = next;
    };

    const upsertAssignment = (assignment: PreOrderAssignment) => {
        const next = [...assignments.value];
        const index = next.findIndex((item) => item.id === assignment.id);
        let previousCatalogId: number | null = null;

        if (index === -1) {
            next.unshift(assignment);
        } else {
            previousCatalogId = next[index].catalog_id;
            next[index] = assignment;
        }

        assignments.value = next;

        if (
            previousCatalogId !== null &&
            previousCatalogId !== assignment.catalog_id
        ) {
            syncCatalogOption(previousCatalogId, null, false);
        }

        syncCatalogOption(assignment.catalog_id, assignment.id, true);
    };

    const applyQuickOffset = (hours: number) => {
        const target = new Date(Date.now() + hours * 60 * 60 * 1000);

        form.countdown_ends_at = toLocalPreOrderDatetimeInput(
            target.toISOString(),
        );
    };

    const resetForm = () => {
        const nextUrutan =
            assignments.value.length === 0
                ? 1
                : assignments.value.reduce(
                      (max, item) =>
                          Math.max(max, toSafePreOrderUrutan(item.urutan)),
                      0,
                  ) + 1;

        form.id = null;
        form.catalog_id = null;
        form.status = PRE_ORDER_DEFAULT_STATUS;
        form.is_visible = false;
        form.max_slots = PRE_ORDER_DEFAULT_MAX_SLOTS;
        form.filled_slots = PRE_ORDER_DEFAULT_FILLED_SLOTS;
        form.countdown_ends_at = toLocalPreOrderDatetimeInput(
            new Date(
                Date.now() + PRE_ORDER_DEFAULT_COUNTDOWN_HOURS * 60 * 60 * 1000,
            ).toISOString(),
        );
        form.batch_label = null;
        form.urutan = nextUrutan;

        editWasVisible.value = false;
    };

    const toAssignmentPayload = (
        assignment: PreOrderAssignment,
        isVisible: boolean,
    ): PreOrderAssignmentPayload => {
        return {
            catalog_id: assignment.catalog_id,
            status: assignment.status,
            is_visible: isVisible,
            max_slots: assignment.max_slots,
            filled_slots: assignment.filled_slots,
            countdown_ends_at: assignment.countdown_ends_at,
            batch_label: assignment.batch_label,
            urutan: Number(assignment.urutan),
        };
    };

    const toggleAssignmentVisible = async (assignment: PreOrderAssignment) => {
        const newVisibleState = !assignment.is_visible;

        if (
            newVisibleState &&
            visibleCount.value >= visibleLimit &&
            !assignment.is_visible
        ) {
            flashError(`Maksimal ${visibleLimit} produk pre-order bisa visible.`);

            return;
        }

        busyRowId.value = assignment.id;

        try {
            const response = await apiService.updateAssignment(
                assignment.id,
                toAssignmentPayload(assignment, newVisibleState),
            );

            upsertAssignment(response.assignment);
            visibleCount.value = Number(response.visible_count);
            flashSuccess(response.message);
        } catch (error) {
            flashError(normalizePreOrderApiError(error, 'Gagal mengubah status visible.'));
        } finally {
            busyRowId.value = null;
        }
    };

    const submitForm = async () => {
        if (form.catalog_id === null) {
            flashError('Produk katalog wajib dipilih.');

            return;
        }

        const maxSlots = Number(form.max_slots);
        const filledSlots = Number(form.filled_slots);

        if (!Number.isInteger(maxSlots) || maxSlots < 1) {
            flashError('Maksimal slot minimal 1.');

            return;
        }

        if (!Number.isInteger(filledSlots) || filledSlots < 0) {
            flashError('Slot terisi minimal 0.');

            return;
        }

        if (filledSlots > maxSlots) {
            flashError('Slot terisi tidak boleh melebihi maksimal slot.');

            return;
        }

        if (form.countdown_ends_at.trim() === '') {
            flashError('Waktu tutup PO wajib diisi.');

            return;
        }

        const countdownIso = toIsoFromLocalPreOrderDatetime(form.countdown_ends_at);

        if (!countdownIso) {
            flashError('Format waktu tutup PO tidak valid.');

            return;
        }

        if (new Date(countdownIso).getTime() <= Date.now()) {
            flashError('Waktu tutup PO harus lebih besar dari waktu saat ini.');

            return;
        }

        if (form.is_visible && !canEnableVisible.value) {
            flashError(`Maksimal ${visibleLimit} produk pre-order bisa visible.`);

            return;
        }

        if (form.urutan < 1) {
            flashError('Urutan wajib dipilih.');

            return;
        }

        const payload: PreOrderAssignmentPayload = {
            catalog_id: form.catalog_id,
            status: form.status,
            is_visible: form.is_visible,
            max_slots: maxSlots,
            filled_slots: filledSlots,
            countdown_ends_at: countdownIso,
            batch_label: form.batch_label || null,
            urutan: Number(form.urutan),
        };

        isSubmitting.value = true;
        errorMessage.value = '';

        try {
            const response = form.id
                ? await apiService.updateAssignment(form.id, payload)
                : await apiService.createAssignment(payload);

            upsertAssignment(response.assignment);
            visibleCount.value = Number(response.visible_count);
            flashSuccess(response.message);
            resetForm();
        } catch (error) {
            flashError(
                normalizePreOrderApiError(
                    error,
                    'Gagal menyimpan data pre-order Home.',
                ),
            );
        } finally {
            isSubmitting.value = false;
        }
    };

    const startEdit = (assignment: PreOrderAssignment) => {
        form.id = assignment.id;
        form.catalog_id = assignment.catalog_id;
        form.status = assignment.status;
        form.is_visible = assignment.is_visible;
        form.max_slots = assignment.max_slots;
        form.filled_slots = assignment.filled_slots;
        form.countdown_ends_at = toLocalPreOrderDatetimeInput(
            assignment.countdown_ends_at,
        );
        form.batch_label = assignment.batch_label;
        form.urutan = toSafePreOrderUrutan(assignment.urutan);

        editWasVisible.value = assignment.is_visible;
        errorMessage.value = '';
        successMessage.value = '';
    };

    const deleteAssignment = async (assignment: PreOrderAssignment) => {
        const approved = window.confirm(
            `Hapus pre-order untuk "${assignment.catalog?.name ?? 'produk ini'}"?`,
        );

        if (!approved) {
            return;
        }

        busyRowId.value = assignment.id;

        try {
            const response = await apiService.deleteAssignment(assignment.id);

            assignments.value = assignments.value.filter(
                (item) => item.id !== response.id,
            );
            syncCatalogOption(assignment.catalog_id, null, false);
            visibleCount.value = Number(response.visible_count);

            if (form.id === response.id) {
                resetForm();
            }

            flashSuccess(response.message);
        } catch (error) {
            flashError(
                normalizePreOrderApiError(error, 'Gagal menghapus pre-order Home.'),
            );
        } finally {
            busyRowId.value = null;
        }
    };

    resetForm();

    return {
        assignments,
        catalogOptions,
        visibleCount,
        visibleLimit,
        successMessage,
        errorMessage,
        isSubmitting,
        busyRowId,
        form,
        sortedAssignments,
        selectedCatalog,
        formProgress,
        formRemainingSlots,
        urutanOptions,
        applyQuickOffset,
        submitForm,
        resetForm,
        toggleAssignmentVisible,
        startEdit,
        deleteAssignment,
        toSafeUrutan: toSafePreOrderUrutan,
        formatDateTime: formatPreOrderDateTime,
        statusBadgeClass: statusBadgeClassForPreOrder,
    };
};
