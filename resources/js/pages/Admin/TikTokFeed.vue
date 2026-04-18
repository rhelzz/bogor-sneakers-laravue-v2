<template>
    <AdminLayout>
        <Head title="Admin TikTok Feed" />
        <div class="admin-page">
            <AdminPageHeader
                title="Kelola TikTok Feed"
                description="Maksimal 4 link TikTok untuk ditampilkan di Home dengan filter kategori."
            />

            <AdminAlert :message="successMessage" variant="success" />
            <AdminAlert :message="errorMessage" variant="error" />

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-1">
                    <div class="admin-card admin-card-sticky">
                        <h3
                            class="admin-section-title mb-4 flex items-center gap-2"
                        >
                            <i class="bi bi-tiktok text-base"></i>
                            {{
                                editId
                                    ? 'Edit Link TikTok'
                                    : 'Tambah Link TikTok'
                            }}
                        </h3>

                        <div class="mb-4">
                            <label class="admin-label">Link Video</label>
                            <input
                                v-model.trim="form.url"
                                type="url"
                                placeholder="https://www.tiktok.com/@username/video/123"
                                class="admin-input"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="admin-label">Kategori</label>
                            <select
                                v-model="form.category"
                                class="admin-select"
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
                            <label class="admin-label">Kategori Baru</label>
                            <input
                                v-model.trim="form.category_new"
                                type="text"
                                maxlength="64"
                                placeholder="Contoh: unboxing"
                                class="admin-input"
                            />
                        </div>

                        <div
                            class="admin-card-muted mb-4 flex items-center justify-between"
                        >
                            <span class="admin-muted-text"
                                >Tampilkan di Home</span
                            >
                            <button
                                type="button"
                                @click="form.is_active = !form.is_active"
                                :class="[
                                    'flex h-7 w-12 items-center rounded-full px-1 transition-all',
                                    form.is_active
                                        ? 'bg-emerald-600'
                                        : 'bg-slate-300',
                                ]"
                            >
                                <span
                                    :class="[
                                        'h-5 w-5 rounded-full bg-white transition-all',
                                        form.is_active
                                            ? 'translate-x-5'
                                            : 'translate-x-0',
                                    ]"
                                ></span>
                            </button>
                        </div>

                        <button
                            @click="submitForm"
                            :disabled="isSubmitting || isMaxReached"
                            class="admin-btn admin-btn-primary admin-btn-block"
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
                            class="admin-btn admin-btn-soft admin-btn-block mt-2"
                        >
                            Batal Edit
                        </button>

                        <div class="admin-card-muted mt-4">
                            <p class="admin-muted-text leading-relaxed">
                                <strong>Catatan:</strong> Slot terpakai
                                {{ feeds.length }}/4. Data preview (judul,
                                thumbnail, author) otomatis diambil dari TikTok.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="admin-section-title flex items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-sm"></i>
                            Video Aktif ({{ feeds.length }})
                        </h3>
                    </div>

                    <div v-if="feeds.length === 0" class="admin-empty-state">
                        <i
                            class="bi bi-collection-play mb-2 block text-4xl text-slate-400"
                        ></i>
                        <p class="mb-1 text-sm font-semibold text-slate-700">
                            Belum ada link TikTok
                        </p>
                        <p class="admin-muted-text">
                            Tambahkan link pertama lewat form di sebelah kiri.
                        </p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div
                            v-for="feed in sortedFeeds"
                            :key="feed.id"
                            class="overflow-hidden rounded-xl border border-slate-200 bg-white"
                        >
                            <div
                                class="relative aspect-9/16 overflow-hidden bg-slate-100"
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
                                        class="bi bi-image text-4xl text-slate-300"
                                    ></i>
                                </div>

                                <div
                                    class="admin-image-overlay absolute inset-x-0 bottom-0 p-3 text-white"
                                >
                                    <p class="line-clamp-2 text-sm font-medium">
                                        {{ feed.title ?? 'Video TikTok' }}
                                    </p>
                                    <p class="mt-1 text-xs text-washi/70">
                                        {{ feed.author_name ?? '@unknown' }}
                                    </p>
                                </div>

                                <span
                                    class="absolute top-3 left-3 rounded-full border border-slate-300 bg-white px-3 py-1 text-[11px] tracking-wide text-slate-700"
                                >
                                    {{ toCategoryLabel(feed.category) }}
                                </span>
                            </div>

                            <div class="space-y-3 p-4">
                                <a
                                    :href="feed.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="line-clamp-1 block text-xs text-slate-500 hover:text-slate-800"
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
                                                ? 'border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
                                                : 'border border-slate-300 bg-slate-100 text-slate-600 hover:bg-slate-200',
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
                                        class="admin-btn admin-btn-secondary"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        @click="removeFeed(feed)"
                                        :disabled="busyRowId === feed.id"
                                        class="admin-btn admin-btn-danger disabled:cursor-not-allowed disabled:opacity-60"
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
import { Head } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

import AdminAlert from '@/components/admin/AdminAlert.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
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
