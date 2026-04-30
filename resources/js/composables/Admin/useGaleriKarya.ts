import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import type { GallerySlot } from '@/types/gallery';

export function useGaleriKarya() {
    const isModalOpen = ref(false);
    const editingSlotId = ref<number | null>(null);
    const previewUrl = ref<string>('');

    // Form
    const form = useForm({
        image: null as File | null,
        title: '',
        author: '',
    });

    const openCreateModal = () => {
        editingSlotId.value = null;
        form.reset();
        form.clearErrors();
        previewUrl.value = '';
        isModalOpen.value = true;
    };

    const openEditModal = (slot: GallerySlot) => {
        editingSlotId.value = slot.id;
        form.title = slot.title || '';
        form.author = slot.author || '';
        form.image = null;
        previewUrl.value = slot.image_url || '';
        form.clearErrors();
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        form.reset();
        form.clearErrors();
        previewUrl.value = '';
    };

    const handleFileSelect = (file: File) => {
        if (!file) return;
        form.image = file;
        previewUrl.value = URL.createObjectURL(file);
    };

    const submit = (slots: GallerySlot[]) => {
        // Edit mode
        if (editingSlotId.value) {
            const slotId = editingSlotId.value;
            
            // First, update metadata
            form.put(`/admin/galeri-karya/${slotId}`, {
                preserveScroll: true,
                onSuccess: () => {
                    // If a new image was selected, upload it
                    if (form.image) {
                        form.post(`/admin/galeri-karya/${slotId}/image`, {
                            preserveScroll: true,
                            onSuccess: () => {
                                closeModal();
                            }
                        });
                    } else {
                        closeModal();
                    }
                }
            });
        } else {
            // Create mode: Find the first empty slot
            const emptySlot = slots.find(s => s.image_url === null);
            if (!emptySlot) {
                alert('Semua slot sudah terisi penuh.');
                return;
            }

            const slotId = emptySlot.id;
            
            if (!form.image) {
                form.setError('image', 'Gambar wajib diunggah untuk karya baru.');
                return;
            }

            // Post image first
            form.post(`/admin/galeri-karya/${slotId}/image`, {
                preserveScroll: true,
                onSuccess: () => {
                    // Then update metadata
                    form.put(`/admin/galeri-karya/${slotId}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            closeModal();
                        }
                    });
                }
            });
        }
    };

    const destroy = (slotId: number) => {
        if (confirm('Apakah Anda yakin ingin menghapus karya ini?')) {
            router.delete(`/admin/galeri-karya/${slotId}`, {
                preserveScroll: true,
            });
        }
    };

    return {
        isModalOpen,
        editingSlotId,
        form,
        previewUrl,
        openCreateModal,
        openEditModal,
        closeModal,
        handleFileSelect,
        submit,
        destroy,
    };
}
