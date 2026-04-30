<template>
  <AdminLayout>
    <template #header>
      TikTok Feed
    </template>

    <div class="space-y-8 font-['Source_Sans_Pro']">
      <!-- Input Section -->
      <section class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-6">
          <h3 class="font-['Montserrat'] font-bold text-lg text-slate-800 tracking-tight">Tambah Feed Baru</h3>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center space-x-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm font-semibold text-indigo-700">Maksimal {{ MAX_TIKTOK_FEEDS }} link dapat diunggah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">Link TikTok</label>
            <input
              v-model="form.url"
              type="text"
              placeholder="https://www.tiktok.com/@user/video/..."
              class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold"
              :class="{ 'border-rose-500': form.errors.url }"
            />
            <p v-if="form.errors.url" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.url }}</p>
          </div>
          <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5">Kategori Feed</label>
            <div class="space-y-2">
              <select 
                v-model="form.category" 
                @change="handleCategoryChange"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold appearance-none cursor-pointer"
                :class="{ 'border-rose-500': form.errors.category }"
              >
                <option value="" disabled>Pilih Kategori...</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                <option value="__new">+ Kategori Baru</option>
              </select>

              <input
                v-if="isAddingNewCategory"
                v-model="form.category_new"
                type="text"
                placeholder="Masukkan kategori baru"
                class="w-full bg-slate-50 border border-slate-200 text-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors font-semibold"
                :class="{ 'border-rose-500': form.errors.category_new }"
              />
              <p v-if="form.errors.category" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.category }}</p>
              <p v-if="form.errors.category_new" class="mt-1 text-xs text-rose-500 font-semibold">{{ form.errors.category_new }}</p>
            </div>
          </div>
          <div class="md:col-span-2 flex justify-end">
            <button 
              @click="submit"
              :disabled="form.processing"
              class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold py-2.5 px-8 rounded-xl transition-colors duration-200 shadow-sm"
            >
              {{ form.processing ? 'Menyimpan...' : 'Simpan Feed' }}
            </button>
          </div>
        </div>
      </section>

      <!-- List Section -->
      <section>
        <div class="flex items-center justify-between mb-6 px-1">
          <h3 class="font-['Montserrat'] font-bold text-xl text-slate-800 tracking-tight">Daftar Feed Aktif</h3>
          <div class="flex items-center space-x-2">
            <button
              @click="viewMode = 'grid'"
              :class="viewMode === 'grid' ? 'bg-indigo-100 text-indigo-600' : 'bg-white text-slate-400 border border-slate-200'"
              class="p-2 rounded-lg transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
            </button>
            <button
              @click="viewMode = 'list'"
              :class="viewMode === 'list' ? 'bg-indigo-100 text-indigo-600' : 'bg-white text-slate-400 border border-slate-200'"
              class="p-2 rounded-lg transition-colors"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Display Grid -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="feed in feeds" :key="feed.id" class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:border-indigo-300 transition-all duration-300 flex flex-col">
            <div class="relative bg-slate-50 flex-1 flex items-center justify-center p-4">
              <blockquote
                  class="tiktok-embed mx-auto w-full m-0"
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
            <div class="p-4 flex items-center justify-between border-t border-slate-100">
              <div class="truncate mr-4">
                <span class="px-2 py-1 mb-1.5 inline-block bg-indigo-600 text-white text-[10px] font-bold rounded shadow-sm uppercase tracking-wider">
                  {{ feed.category }}
                </span>
                <p class="text-sm font-bold text-slate-800 truncate" :title="feed.title">{{ feed.title || 'Video TikTok' }}</p>
                <p class="text-xs font-semibold text-slate-500 truncate">{{ feed.author_name || '@user' }}</p>
              </div>
              <button @click="destroy(feed.id)" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Display List -->
        <div v-else class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs tracking-wider">
                <th class="px-6 py-4 font-bold">Video</th>
                <th class="px-6 py-4 font-bold">Informasi</th>
                <th class="px-6 py-4 font-bold">Kategori</th>
                <th class="px-6 py-4 font-bold text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="feed in feeds" :key="feed.id" class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 w-64">
                  <div class="bg-slate-50 rounded-xl overflow-hidden border border-slate-200">
                    <blockquote
                        class="tiktok-embed mx-auto w-full m-0"
                        :cite="feed.url"
                        :data-video-id="feed.video_id ?? undefined"
                        style="max-width: 100%; min-width: 200px"
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
                </td>
                <td class="px-6 py-4">
                  <p class="text-sm font-bold text-slate-800">{{ feed.title || 'Video TikTok' }}</p>
                  <a :href="feed.url" target="_blank" class="text-xs font-semibold text-indigo-600 hover:underline truncate block max-w-xs">{{ feed.url }}</a>
                  <p class="text-xs font-semibold text-slate-500 mt-1">{{ feed.author_name || '@user' }}</p>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-md border border-slate-200 uppercase tracking-wider">
                    {{ feed.category }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <button @click="destroy(feed.id)" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useTiktokFeed } from '@/composables/Admin/useTiktokFeed';
import { TikTokFeedItem } from '@/types/tiktok';
import { getEmbedAuthor, getEmbedProfileUrl, getEmbedVideoUrl, getEmbedCaption } from '@/utils/Admin/tiktokHelper';

const props = defineProps<{
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

watch(() => viewMode.value, () => {
    renderTikTokEmbeds();
});

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