<template>
    <AdminLayout>
        <template #header> TikTok Feed </template>

        <div class="space-y-8 font-['Source_Sans_Pro']">
            <!-- Input Section -->
            <section
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >
                <div class="mb-6 flex items-center justify-between">
                    <h3
                        class="font-['Montserrat'] text-lg font-bold tracking-tight text-slate-800"
                    >
                        Tambah Feed Baru
                    </h3>
                </div>

                <!-- Info Box -->
                <div
                    class="mb-6 flex items-center space-x-3 rounded-xl border border-indigo-100 bg-indigo-50 p-4"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 text-indigo-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <p class="text-sm font-semibold text-indigo-700">
                        Maksimal {{ MAX_TIKTOK_FEEDS }} link dapat diunggah.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-bold text-slate-700"
                            >Link TikTok</label
                        >
                        <input
                            v-model="form.url"
                            type="text"
                            placeholder="https://www.tiktok.com/@user/video/..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                            :class="{ 'border-rose-500': form.errors.url }"
                        />
                        <p
                            v-if="form.errors.url"
                            class="mt-1 text-xs font-semibold text-rose-500"
                        >
                            {{ form.errors.url }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="mb-1.5 block text-sm font-bold text-slate-700"
                            >Kategori Feed</label
                        >
                        <div class="space-y-2">
                            <select
                                v-model="form.category"
                                @change="handleCategoryChange"
                                class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                :class="{
                                    'border-rose-500': form.errors.category,
                                }"
                            >
                                <option value="" disabled>
                                    Pilih Kategori...
                                </option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat"
                                    :value="cat"
                                >
                                    {{ cat }}
                                </option>
                                <option value="__new">+ Kategori Baru</option>
                            </select>

                            <input
                                v-if="isAddingNewCategory"
                                v-model="form.category_new"
                                type="text"
                                placeholder="Masukkan kategori baru"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 font-semibold text-slate-800 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                :class="{
                                    'border-rose-500': form.errors.category_new,
                                }"
                            />
                            <p
                                v-if="form.errors.category"
                                class="mt-1 text-xs font-semibold text-rose-500"
                            >
                                {{ form.errors.category }}
                            </p>
                            <p
                                v-if="form.errors.category_new"
                                class="mt-1 text-xs font-semibold text-rose-500"
                            >
                                {{ form.errors.category_new }}
                            </p>
                        </div>
                    </div>
                    <div class="flex justify-end md:col-span-2">
                        <button
                            @click="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-indigo-600 px-8 py-2.5 font-bold text-white shadow-sm transition-colors duration-200 hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                form.processing ? 'Menyimpan...' : 'Simpan Feed'
                            }}
                        </button>
                    </div>
                </div>
            </section>

            <!-- List Section -->
            <section>
                <div class="mb-6 flex items-center justify-between px-1">
                    <h3
                        class="font-['Montserrat'] text-xl font-bold tracking-tight text-slate-800"
                    >
                        Daftar Feed Aktif
                    </h3>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="viewMode = 'grid'"
                            :class="
                                viewMode === 'grid'
                                    ? 'bg-indigo-100 text-indigo-600'
                                    : 'border border-slate-200 bg-white text-slate-400'
                            "
                            class="rounded-lg p-2 transition-colors"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                />
                            </svg>
                        </button>
                        <button
                            @click="viewMode = 'list'"
                            :class="
                                viewMode === 'list'
                                    ? 'bg-indigo-100 text-indigo-600'
                                    : 'border border-slate-200 bg-white text-slate-400'
                            "
                            class="rounded-lg p-2 transition-colors"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Display Grid -->
                <div
                    v-if="viewMode === 'grid'"
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <div
                        v-for="feed in feeds"
                        :key="feed.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:border-indigo-300"
                    >
                        <div
                            class="relative flex flex-1 items-center justify-center bg-slate-50 p-4"
                        >
                            <blockquote
                                class="tiktok-embed m-0 mx-auto w-full"
                                :cite="feed.url"
                                :data-video-id="feed.video_id ?? undefined"
                                style="max-width: 100%; min-width: 280px"
                            >
                                <section>
                                    <a
                                        target="_blank"
                                        :title="getEmbedAuthor(feed)"
                                        :href="getEmbedProfileUrl(feed)"
                                    >
                                        {{ getEmbedAuthor(feed) }}
                                    </a>
                                    {{ getEmbedCaption(feed) }}
                                    <a
                                        target="_blank"
                                        title="Buka video"
                                        :href="getEmbedVideoUrl(feed)"
                                    >
                                        Lihat di TikTok
                                    </a>
                                </section>
                            </blockquote>
                        </div>
                        <div
                            class="flex items-center justify-between border-t border-slate-100 p-4"
                        >
                            <div class="mr-4 truncate">
                                <span
                                    class="mb-1.5 inline-block rounded bg-indigo-600 px-2 py-1 text-[10px] font-bold tracking-wider text-white uppercase shadow-sm"
                                >
                                    {{ feed.category }}
                                </span>
                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                    :title="feed.title ?? undefined"
                                >
                                    {{ feed.title || 'Video TikTok' }}
                                </p>
                                <p
                                    class="truncate text-xs font-semibold text-slate-500"
                                >
                                    {{ feed.author_name || '@user' }}
                                </p>
                            </div>
                            <button
                                @click="destroy(feed.id)"
                                class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500"
                                title="Hapus"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Display List -->
                <div
                    v-else
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="border-b border-slate-200 bg-slate-50 text-xs tracking-wider text-slate-500 uppercase"
                            >
                                <th class="px-6 py-4 font-bold">Video</th>
                                <th class="px-6 py-4 font-bold">Informasi</th>
                                <th class="px-6 py-4 font-bold">Kategori</th>
                                <th class="px-6 py-4 text-right font-bold">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="feed in feeds"
                                :key="feed.id"
                                class="transition-colors hover:bg-slate-50/50"
                            >
                                <td class="w-64 px-6 py-4">
                                    <div
                                        class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                                    >
                                        <blockquote
                                            class="tiktok-embed m-0 mx-auto w-full"
                                            :cite="feed.url"
                                            :data-video-id="
                                                feed.video_id ?? undefined
                                            "
                                            style="
                                                max-width: 100%;
                                                min-width: 200px;
                                            "
                                        >
                                            <section>
                                                <a
                                                    target="_blank"
                                                    :title="
                                                        getEmbedAuthor(feed)
                                                    "
                                                    :href="
                                                        getEmbedProfileUrl(feed)
                                                    "
                                                >
                                                    {{ getEmbedAuthor(feed) }}
                                                </a>
                                                {{ getEmbedCaption(feed) }}
                                                <a
                                                    target="_blank"
                                                    title="Buka video"
                                                    :href="
                                                        getEmbedVideoUrl(feed)
                                                    "
                                                >
                                                    Lihat di TikTok
                                                </a>
                                            </section>
                                        </blockquote>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ feed.title || 'Video TikTok' }}
                                    </p>
                                    <a
                                        :href="feed.url"
                                        target="_blank"
                                        class="block max-w-xs truncate text-xs font-semibold text-indigo-600 hover:underline"
                                        >{{ feed.url }}</a
                                    >
                                    <p
                                        class="mt-1 text-xs font-semibold text-slate-500"
                                    >
                                        {{ feed.author_name || '@user' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="rounded-md border border-slate-200 bg-slate-100 px-2.5 py-1 text-[10px] font-bold tracking-wider text-slate-600 uppercase"
                                    >
                                        {{ feed.category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        @click="destroy(feed.id)"
                                        class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { useTiktokFeed } from '@/composables/Admin/useTiktokFeed';
import { MAX_TIKTOK_FEEDS } from '@/constants/Admin/tiktokConstants';
import AdminLayout from '@/layouts/AdminLayout.vue';
import type { TikTokFeedItem } from '@/types/tiktok';
import {
    getEmbedAuthor,
    getEmbedProfileUrl,
    getEmbedVideoUrl,
    getEmbedCaption,
} from '@/utils/Admin/tiktokHelper';

defineProps<{
    feeds: TikTokFeedItem[];
    categories: string[];
}>();

const {
    viewMode,
    form,
    isAddingNewCategory,
    submit,
    destroy,
    renderTikTokEmbeds,
} = useTiktokFeed();

onMounted(() => {
    renderTikTokEmbeds();
});

watch(
    () => viewMode.value,
    () => {
        renderTikTokEmbeds();
    },
);

const handleCategoryChange = (e: Event) => {
    const target = e.target as HTMLSelectElement;

    if (target.value === '__new') {
        isAddingNewCategory.value = true;
        form.category = '__new';
    } else {
        isAddingNewCategory.value = false;
        form.category_new = '';
    }
};
</script>
