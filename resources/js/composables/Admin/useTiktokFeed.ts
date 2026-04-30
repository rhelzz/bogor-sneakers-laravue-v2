import { ref, nextTick } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { refreshTikTokEmbedScript, disableTikTokAutoplay } from '@/utils/Admin/tiktokHelper';

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
        form.post(route('admin.tiktok-feed.store'), {
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
            router.delete(route('admin.tiktok-feed.destroy', id), {
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
