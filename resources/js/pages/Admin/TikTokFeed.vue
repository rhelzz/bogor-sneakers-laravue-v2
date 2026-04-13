<template>
    <AdminLayout>
        <div class="mx-auto max-w-6xl">
            <div class="accent-left mb-6">
                <h2 class="font-heading mb-1 text-2xl font-bold lg:text-3xl">
                    Kelola TikTok Feed
                </h2>
                <p class="text-sm text-hai">
                    Maksimal 4 link TikTok untuk ditampilkan di Home dengan
                    filter kategori.
                </p>
            </div>

            <div
                v-if="successMessage"
                class="animate-fade-in mb-4 rounded-xl border border-matcha bg-matcha/20 p-3 text-sm text-matcha"
            >
                <div class="flex items-center gap-2">
                    <i class="bi bi-check-circle"></i>
                    {{ successMessage }}
                </div>
            </div>

            <div
                v-if="errorMessage"
                class="animate-fade-in mb-4 rounded-xl border border-red-500 bg-red-200/20 p-3 text-sm text-red-600"
            >
                <div class="flex items-center gap-2">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ errorMessage }}
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div
                        class="card-lift sticky top-6 rounded-2xl border border-sumi/5 bg-washi p-6"
                    >
                        <h3
                            class="font-heading mb-5 flex items-center gap-2 text-lg font-bold"
                        >
                            <i class="bi bi-tiktok text-xl text-matcha"></i>
                            {{
                                editId
                                    ? 'Edit Link TikTok'
                                    : 'Tambah Link TikTok'
                            }}
                        </h3>

                        <div class="mb-4">
                            <label
                                class="mb-2 block text-xs tracking-wider text-hai"
                                >LINK VIDEO</label
                            >
                            <input
                                v-model.trim="form.url"
                                type="url"
                                placeholder="https://www.tiktok.com/@username/video/123"
                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                            />
                        </div>

                        <div class="mb-4">
                            <label
                                class="mb-2 block text-xs tracking-wider text-hai"
                                >KATEGORI</label
                            >
                            <select
                                v-model="form.category"
                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                            >
                                <option value="">Pilih kategori</option>
                                <option
                                    v-for="category in categories"
                                    :key="`cat-${category}`"
                                    :value="category"
                                >
                                    {{ toCategoryLabel(category) }}
                                </option>
                                <option value="__new">
                                    + Tambah kategori baru
                                </option>
                            </select>
                        </div>

                        <div v-if="form.category === '__new'" class="mb-4">
                            <label
                                class="mb-2 block text-xs tracking-wider text-hai"
                                >KATEGORI BARU</label
                            >
                            <input
                                v-model.trim="form.category_new"
                                type="text"
                                maxlength="64"
                                placeholder="Contoh: unboxing"
                                class="w-full rounded-lg border border-sumi/20 bg-shironeri px-3 py-2.5 text-sm transition-all outline-none focus:border-matcha"
                            />
                        </div>

                        <div
                            class="mb-4 flex items-center justify-between rounded-lg bg-sumi/5 p-2.5"
                        >
                            <span class="text-xs text-hai"
                                >Tampilkan di Home</span
                            >
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                :class="[
                                    'flex h-8 w-14 items-center rounded-full px-1 transition-all',
                                    form.is_active ? 'bg-matcha' : 'bg-sumi/20',
                                ]"
                            >
                                <span
                                    :class="[
                                        'h-6 w-6 rounded-full bg-washi transition-all',
                                        form.is_active
                                            ? 'translate-x-6'
                                            : 'translate-x-0',
                                    ]"
                                ></span>
                            </button>
                        </div>

                        <button
                            @click="submitForm"
                            :disabled="isSubmitting || isMaxReached"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-matcha px-4 py-2.5 text-sm font-bold text-washi transition-all hover:bg-matcha/80 disabled:cursor-not-allowed disabled:bg-matcha/40"
                        >
                            <i v-if="!isSubmitting" class="bi bi-save"></i>
                            <i
                                v-else
                                class="bi bi-hourglass-split animate-spin"
                            ></i>
                            {{
                                isSubmitting
                                    ? 'Menyimpan...'
                                    : editId
                                      ? 'Simpan Perubahan'
                                      : 'Tambah Link'
                            }}
                        </button>

                        <button
                            v-if="editId"
                            @click="resetForm"
                            class="mt-2.5 w-full rounded-lg bg-sumi/10 px-4 py-2.5 text-sm font-medium text-sumi transition-all hover:bg-sumi/20"
                        >
                            Batal Edit
                        </button>

                        <div
                            class="mt-5 rounded-lg border border-sumi/10 bg-sumi/5 p-3"
                        >
                            <p class="text-xs leading-relaxed text-hai/70">
                                <strong>Catatan:</strong> Slot terpakai
                                {{ feeds.length }}/4. Data preview (judul,
                                thumbnail, author) otomatis diambil dari TikTok.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3
                            class="font-heading flex items-center gap-2 text-xl font-bold"
                        >
                            <i
                                class="bi bi-grid-3x3-gap text-xl text-matcha"
                            ></i>
                            Video Aktif ({{ feeds.length }})
                        </h3>
                    </div>

                    <div
                        v-if="feeds.length === 0"
                        class="rounded-2xl border border-sumi/5 bg-washi p-8 text-center"
                    >
                        <i
                            class="bi bi-collection-play mb-3 block text-5xl text-hai/30"
                        ></i>
                        <p class="mb-1 text-base font-bold text-hai">
                            Belum ada link TikTok
                        </p>
                        <p class="text-sm text-hai/60">
                            Tambahkan link pertama lewat form di sebelah kiri.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div
                            v-for="feed in sortedFeeds"
                            :key="feed.id"
                            class="card-lift overflow-hidden rounded-2xl border border-sumi/10 bg-washi"
                        >
                            <div
                                class="relative aspect-9/16 overflow-hidden bg-sumi/10"
                            >
                                <img
                                    v-if="feed.thumbnail_url"
                                    :src="feed.thumbnail_url"
                                    :alt="feed.title ?? 'TikTok thumbnail'"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center"
                                >
                                    <i
                                        class="bi bi-image text-4xl text-hai/30"
                                    ></i>
                                </div>

                                <div
                                    class="absolute inset-x-0 bottom-0 bg-linear-to-t from-kuro/80 via-kuro/20 to-transparent p-4 text-washi"
                                >
                                    <p class="line-clamp-2 text-sm font-medium">
                                        {{ feed.title ?? 'Video TikTok' }}
                                    </p>
                                    <p class="mt-1 text-xs text-washi/70">
                                        {{ feed.author_name ?? '@unknown' }}
                                    </p>
                                </div>

                                <span
                                    class="absolute top-3 left-3 rounded-full bg-sumi/70 px-3 py-1 text-[11px] tracking-wide text-washi"
                                >
                                    {{ toCategoryLabel(feed.category) }}
                                </span>
                            </div>

                            <div class="space-y-3 p-4">
                                <a
                                    :href="feed.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="line-clamp-1 block text-xs text-hai hover:text-sumi"
                                >
                                    {{ feed.url }}
                                </a>

                                <div class="flex gap-2">
                                    <button
                                        @click="toggleActive(feed)"
                                        :disabled="busyRowId === feed.id"
                                        :class="[
                                            'flex-1 rounded-lg px-3 py-2 text-xs font-bold transition-all disabled:cursor-not-allowed disabled:opacity-60',
                                            feed.is_active
                                                ? 'bg-matcha/20 text-matcha hover:bg-matcha/30'
                                                : 'bg-sumi/10 text-hai hover:bg-sumi/20',
                                        ]"
                                    >
                                        {{
                                            feed.is_active
                                                ? 'Aktif'
                                                : 'Nonaktif'
                                        }}
                                    </button>

                                    <button
                                        @click="startEdit(feed)"
                                        class="rounded-lg bg-indigo/15 px-3 py-2 text-xs font-bold text-indigo transition-all hover:bg-indigo/25"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        @click="removeFeed(feed)"
                                        :disabled="busyRowId === feed.id"
                                        class="rounded-lg bg-red-500/15 px-3 py-2 text-xs font-bold text-red-600 transition-all hover:bg-red-500/25 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';

import AdminLayout from '@/layouts/AdminLayout.vue';
import type { TikTokFeedItem } from '@/types/tiktok';

const props = defineProps({
    feeds: {
        type: Array as () => TikTokFeedItem[],
        default: () => [],
    },
    categories: {
        type: Array as () => string[],
        default: () => [],
    },
});

type ApiErrorResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type ApiStoreResponse = {
    message: string;
    feed: TikTokFeedItem;
};

type ApiDeleteResponse = {
    message: string;
    id: number;
};

const feeds = ref<TikTokFeedItem[]>(props.feeds);
const categories = ref<string[]>(props.categories);

const editId = ref<number | null>(null);
const busyRowId = ref<number | null>(null);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const form = reactive({
    url: '',
    category: '',
    category_new: '',
    is_active: true,
    sort_order: 0,
});

const csrfToken =
    document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content') ?? '';

const sortedFeeds = computed(() => {
    return [...feeds.value].sort((a, b) => {
        const left = a.sort_order ?? 0;
        const right = b.sort_order ?? 0;

        return left - right;
    });
});

const isMaxReached = computed(() => {
    return !editId.value && feeds.value.length >= 4;
});

const buildHeaders = (isJsonBody: boolean) => {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    if (isJsonBody) {
        headers['Content-Type'] = 'application/json';
    }

    return headers;
};

const requestJson = async <T,>(url: string, init: RequestInit = {}) => {
    const isFormData = init.body instanceof FormData;

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...init,
        headers: {
            ...buildHeaders(!isFormData),
            ...(init.headers ?? {}),
        },
    });

    const payload = await response.json().catch(() => null);

    if (!response.ok) {
        throw {
            status: response.status,
            payload,
        };
    }

    return payload as T;
};

const toCategoryLabel = (category: string) => {
    if (!category) {
        return '-';
    }

    return category
        .split(/[\s_-]+/)
        .filter(Boolean)
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const refreshCategories = () => {
    categories.value = Array.from(
        new Set(feeds.value.map((feed) => feed.category).filter(Boolean)),
    );
};

const resetForm = () => {
    editId.value = null;
    form.url = '';
    form.category = '';
    form.category_new = '';
    form.is_active = true;
    form.sort_order = feeds.value.length;
};

const resolveFormCategory = () => {
    if (form.category === '__new') {
        return {
            category: '__new',
            category_new: form.category_new.trim(),
        };
    }

    return {
        category: form.category.trim(),
        category_new: '',
    };
};

const submitForm = async () => {
    if (isMaxReached.value) {
        errorMessage.value = 'Maksimal 4 link TikTok pada feed home.';

        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    const categoryPayload = resolveFormCategory();
    const payload = {
        url: form.url,
        category: categoryPayload.category,
        category_new: categoryPayload.category_new,
        is_active: form.is_active,
        sort_order: form.sort_order,
    };

    try {
        if (editId.value) {
            const data = await requestJson<ApiStoreResponse>(
                `/admin/tiktok-feed/${editId.value}`,
                {
                    method: 'PUT',
                    body: JSON.stringify(payload),
                },
            );

            feeds.value = feeds.value.map((feed) => {
                if (feed.id !== editId.value) {
                    return feed;
                }

                return data.feed;
            });

            successMessage.value = data.message;
        } else {
            const data = await requestJson<ApiStoreResponse>(
                '/admin/tiktok-feed',
                {
                    method: 'POST',
                    body: JSON.stringify(payload),
                },
            );

            feeds.value = [...feeds.value, data.feed];
            successMessage.value = data.message;
        }

        refreshCategories();
        resetForm();
    } catch (error) {
        const apiError = error as { payload?: ApiErrorResponse };
        const firstError = Object.values(
            apiError.payload?.errors ?? {},
        )?.[0]?.[0];

        errorMessage.value =
            firstError ||
            apiError.payload?.message ||
            'Gagal menyimpan link TikTok.';
    } finally {
        isSubmitting.value = false;
    }
};

const startEdit = (feed: TikTokFeedItem) => {
    editId.value = feed.id;
    form.url = feed.url;
    form.is_active = !!feed.is_active;
    form.sort_order = feed.sort_order ?? 0;

    const hasCategory = categories.value.includes(feed.category);

    if (hasCategory) {
        form.category = feed.category;
        form.category_new = '';
    } else {
        form.category = '__new';
        form.category_new = feed.category;
    }
};

const toggleActive = async (feed: TikTokFeedItem) => {
    busyRowId.value = feed.id;
    errorMessage.value = '';

    const nextState = !feed.is_active;
    const oldState = feed.is_active;
    feed.is_active = nextState;

    try {
        const data = await requestJson<ApiStoreResponse>(
            `/admin/tiktok-feed/${feed.id}`,
            {
                method: 'PUT',
                body: JSON.stringify({
                    is_active: nextState,
                    category: feed.category,
                }),
            },
        );

        feeds.value = feeds.value.map((item) => {
            if (item.id !== feed.id) {
                return item;
            }

            return data.feed;
        });

        successMessage.value = data.message;
    } catch (error) {
        feed.is_active = oldState;

        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value =
            apiError.payload?.message || 'Gagal memperbarui status video.';
    } finally {
        busyRowId.value = null;
    }
};

const removeFeed = async (feed: TikTokFeedItem) => {
    if (!confirm('Hapus link TikTok ini dari feed Home?')) {
        return;
    }

    busyRowId.value = feed.id;
    errorMessage.value = '';

    const oldFeeds = [...feeds.value];
    feeds.value = feeds.value.filter((item) => item.id !== feed.id);

    try {
        const data = await requestJson<ApiDeleteResponse>(
            `/admin/tiktok-feed/${feed.id}`,
            {
                method: 'DELETE',
            },
        );

        successMessage.value = data.message;

        if (editId.value === feed.id) {
            resetForm();
        }

        refreshCategories();
    } catch (error) {
        feeds.value = oldFeeds;

        const apiError = error as { payload?: ApiErrorResponse };
        errorMessage.value =
            apiError.payload?.message || 'Gagal menghapus data.';
    } finally {
        busyRowId.value = null;
    }
};

if (feeds.value.length > 0) {
    form.sort_order = feeds.value.length;
}
</script>

<style scoped>
.accent-left {
    border-left: 4px solid #7c8c5a;
    padding-left: 1rem;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
