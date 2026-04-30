import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { DEFAULT_FORM_DATA } from '@/constants/Admin/whatsappConstants';
import * as whatsappAdminsRoutes from '@/routes/admin/whatsapp-admins';

export function useWhatsappAdmin() {
    // --- State View ---
    const viewMode = ref<'table' | 'card'>('table');

    // --- State Modal ---
    const isModalOpen = ref(false);
    const modalMode = ref<'add' | 'edit'>('add');
    const editingId = ref<number | null>(null);

    // --- Processing States for individual items ---
    const processingIds = ref<Set<number>>(new Set());

    const form = useForm({
        ...DEFAULT_FORM_DATA,
    });

    const openModal = (mode: 'add' | 'edit', item: any = null) => {
        modalMode.value = mode;

        if (item) {
            editingId.value = item.id;
            form.name = item.name;
            form.role = item.role;
            form.phone = item.phone;
            form.initial = item.initial;
            form.color = item.color_key;
            form.is_active = item.is_active;
            form.sort_order = item.sort_order;
        } else {
            editingId.value = null;
            form.reset();
        }

        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        form.clearErrors();
    };

    const submitForm = () => {
        if (modalMode.value === 'add') {
            form.post(whatsappAdminsRoutes.store.url(), {
                onSuccess: () => {
                    closeModal();
                },
            });
        } else {
            form.put(whatsappAdminsRoutes.update.url({ whatsappAdmin: editingId.value as number }), {
                onSuccess: () => {
                    closeModal();
                },
            });
        }
    };

    const deleteAdmin = (id: number) => {
        if (confirm('Apakah Anda yakin ingin menghapus kontak ini?')) {
            router.delete(whatsappAdminsRoutes.destroy.url({ whatsappAdmin: id }), {
                onStart: () => processingIds.value.add(id),
                onFinish: () => processingIds.value.delete(id),
            });
        }
    };

    const toggleStatus = (admin: any) => {
        router.put(
            whatsappAdminsRoutes.update.url({ whatsappAdmin: admin.id }),
            {
                name: admin.name,
                role: admin.role,
                phone: admin.phone,
                initial: admin.initial,
                color: admin.color_key,
                is_active: !admin.is_active,
                sort_order: admin.sort_order,
            },
            {
                preserveScroll: true,
                onStart: () => processingIds.value.add(admin.id),
                onFinish: () => processingIds.value.delete(admin.id),
            }
        );
    };

    const isProcessing = (id: number) => processingIds.value.has(id);

    return {
        viewMode,
        isModalOpen,
        modalMode,
        form,
        processingIds,
        openModal,
        closeModal,
        submitForm,
        deleteAdmin,
        toggleStatus,
        isProcessing,
    };
}
