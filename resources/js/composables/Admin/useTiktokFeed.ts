import { ref, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { refreshTikTokEmbedScript, disableTikTokAutoplay } from '@/utils/Admin/tiktokHelper';
import * as tiktokFeedRoutes from '@/routes/admin/tiktok-feed';

export const useTiktokFeed = () => {
    const viewMode = ref<'grid' | 'list'>('grid');
    const isAddingNewCategory = ref(false);

    const form = useForm({
        url: '',
        category: '',
        category_new: '',
        is_active: true,
    });

    const submit = () => {
        form.post(tiktokFeedRoutes.store.url(), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                isAddingNewCategory.value = false;
                renderTikTokEmbeds();
            },
        });
    };

    const destroy = (id: number) => {
        if (confirm('Apakah Anda yakin ingin menghapus feed ini?')) {
            router.delete(tiktokFeedRoutes.destroy.url({ tiktokFeed: id }), {
                preserveScroll: true,
                onSuccess: () => {
                    renderTikTokEmbeds();
                }
            });
        }
    };

    const renderTikTokEmbeds = async () => {
        await nextTick();
        refreshTikTokEmbedScript();

        setTimeout(() => {
            disableTikTokAutoplay();
        }, 200);
    };

    return {
        viewMode,
        form,
        isAddingNewCategory,
        submit,
        destroy,
        renderTikTokEmbeds,
    };
};
