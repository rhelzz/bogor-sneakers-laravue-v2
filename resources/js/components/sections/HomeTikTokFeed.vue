<template>
    <section
        id="tiktok"
        class="pattern-dot-wave bg-sumi px-6 py-16 sm:py-24 text-washi lg:px-16"
    >
        <div class="mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-12 flex flex-col md:flex-row md:items-end md:justify-between gap-6 border-b border-washi/10 pb-8">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-tiktok text-matcha text-lg animate-pulse-soft"></i>
                        <span class="text-[10px] font-bold tracking-widest text-washi/50 uppercase">@bogorsneaker ON TIKTOK</span>
                    </div>
                    <h2 class="font-heading text-4xl font-black tracking-tight text-washi uppercase">
                        TIKTOK<br />
                        <span class="text-matcha">FEED</span>
                    </h2>
                </div>
                
                <!-- Follower Counter -->
                <div class="flex items-center gap-4 rounded-2xl border border-washi/10 bg-washi/[0.02] px-6 py-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-washi/10 text-washi">
                        <i class="bi bi-people text-lg"></i>
                    </div>
                    <div class="text-left">
                        <span class="text-[10px] font-bold tracking-widest text-washi/40 uppercase block">TOTAL FOLLOWERS</span>
                        <span class="text-2xl font-black text-matcha font-sans">{{ tiktokFollowers?.formatted_followers ?? '-' }}</span>
                        <span class="text-[9px] text-washi/30 block mt-0.5">
                            {{ tiktokFollowers?.is_stale ? 'Data Cache' : 'Refresh tiap 15m' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Category Bar - Swipe Track on Mobile -->
            <div class="mb-10 -mx-6 px-6 overflow-x-auto flex flex-nowrap items-center gap-2.5 no-scrollbar scroll-smooth">
                <span class="shrink-0 text-[10px] font-bold tracking-widest text-washi/40 uppercase mr-1">KATEGORI:</span>
                <button
                    v-for="category in tiktokCategories"
                    :key="category"
                    @click="tiktokCategoryFilter = category"
                    :class="[
                        'shrink-0 rounded-full px-5 py-2.5 text-xs font-bold tracking-widest uppercase transition-all duration-300 border',
                        tiktokCategoryFilter === category
                            ? 'bg-washi text-sumi border-washi scale-[1.02] shadow-md'
                            : 'bg-washi/5 text-washi/60 border-washi/10 hover:border-washi/20 hover:bg-washi/10',
                    ]"
                >
                    {{
                        category === 'all'
                            ? 'Semua'
                            : toCategoryLabel(category)
                    }}
                </button>
            </div>

            <!-- TikTok Grid -->
            <div
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
                id="tiktokGrid"
            >
                <div
                    v-if="filteredTiktokVideos.length === 0"
                    class="col-span-full rounded-3xl border border-washi/10 bg-washi/[0.02] p-16 text-center flex flex-col items-center justify-center gap-3"
                >
                    <i class="bi bi-collection-play text-4xl text-washi/20"></i>
                    <p class="text-sm text-washi/50">
                        Belum ada video TikTok pada kategori ini.
                    </p>
                </div>

                <div
                    v-for="video in filteredTiktokVideos"
                    :key="video.id"
                    class="tiktok-card group rounded-3xl border border-washi/10 bg-washi/[0.02] p-4 transition-all duration-300 hover:border-matcha/40 hover:-translate-y-1.5 hover:bg-washi/[0.04]"
                >
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <span
                            class="rounded-full bg-matcha/10 border border-matcha/20 px-3 py-1.5 text-[9px] font-bold tracking-widest text-matcha uppercase"
                        >
                            {{ toCategoryLabel(video.category) }}
                        </span>
                        
                        <a
                            :href="video.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-1.5 rounded-full bg-washi/10 px-3 py-1.5 text-[9px] font-bold tracking-widest text-washi uppercase border border-washi/5 hover:bg-washi hover:text-sumi hover:scale-[1.05] active:scale-[0.95] transition-all duration-300"
                        >
                            <i class="bi bi-box-arrow-up-right"></i>
                            Buka TikTok
                        </a>
                    </div>

                    <blockquote
                        class="tiktok-embed mx-auto w-full overflow-hidden rounded-2xl shadow-lg border border-washi/5"
                        :cite="video.url"
                        :data-video-id="video.video_id ?? undefined"
                        style="max-width: 500px; min-width: 280px"
                    >
                        <section class="p-4 bg-sumi text-xs text-washi/40">
                            <a
                                target="_blank"
                                :title="getEmbedAuthor(video)"
                                :href="getEmbedProfileUrl(video)"
                                class="text-matcha font-bold"
                            >
                                {{ getEmbedAuthor(video) }}
                            </a>
                            <p class="my-2 text-washi/80">{{ getEmbedCaption(video) }}</p>
                            <a
                                target="_blank"
                                title="Buka video"
                                :href="getEmbedVideoUrl(video)"
                                class="text-[10px] text-washi/40 underline block mt-2"
                            >
                                Lihat di TikTok
                            </a>
                        </section>
                    </blockquote>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, nextTick, onMounted, ref, watch } from 'vue';

import type { TikTokFeedItem, TikTokFollowerSnapshot } from '@/types/tiktok';

const props = defineProps<{
    tiktokFeed: TikTokFeedItem[];
    tiktokFollowers: TikTokFollowerSnapshot | null;
}>();

const tiktokCategoryFilter = ref('all');

const tiktokCategories = computed(() => {
    const categories = Array.from(
        new Set(
            props.tiktokFeed
                .map((video) => video.category)
                .filter((category): category is string => category !== ''),
        ),
    );
    return ['all', ...categories];
});

const filteredTiktokVideos = computed(() => {
    if (tiktokCategoryFilter.value === 'all') return props.tiktokFeed;
    return props.tiktokFeed.filter(
        (video) => video.category === tiktokCategoryFilter.value,
    );
});

const TIKTOK_EMBED_SCRIPT_ID = 'tiktok-embed-script';

const extractTikTokUsername = (videoUrl: string, fallback?: string | null) => {
    const match = videoUrl.match(/tiktok\.com\/@([^/?]+)/i);
    if (match?.[1]) return match[1];
    const cleanFallback = (fallback ?? '').trim().replace(/^@/, '');
    return cleanFallback !== '' ? cleanFallback : 'bogorsneaker';
};

const getEmbedAuthor = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);
    return `@${username}`;
};

const getEmbedProfileUrl = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);
    return `https://www.tiktok.com/@${encodeURIComponent(username)}?refer=embed`;
};

const getEmbedVideoUrl = (video: TikTokFeedItem) => {
    try {
        const url = new URL(video.url);
        url.searchParams.set('refer', 'embed');
        return url.toString();
    } catch {
        return video.url;
    }
};

const getEmbedCaption = (video: TikTokFeedItem) => {
    return video.title?.trim() || 'Lihat video terbaru dari Bogor Sneakers.';
};

const disableTikTokAutoplay = () => {
    const tiktokEmbeds = document.querySelectorAll('.tiktok-embed');
    tiktokEmbeds.forEach((embed) => {
        const iframe = embed.querySelector('iframe');
        if (iframe) {
            iframe.addEventListener(
                'load',
                () => {
                    try {
                        const sandboxValue =
                            iframe.getAttribute('sandbox') || '';
                        if (!sandboxValue.includes('allow-scripts')) {
                            iframe.setAttribute(
                                'sandbox',
                                sandboxValue +
                                    ' allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-presentation',
                            );
                        }
                    } catch (e) {
                        console.warn('Could not disable autoplay:', e);
                    }
                },
                { once: true },
            );
        }
    });
};

const refreshTikTokEmbedScript = () => {
    const existing = document.getElementById(TIKTOK_EMBED_SCRIPT_ID);
    if (existing) existing.remove();
    const script = document.createElement('script');
    script.id = TIKTOK_EMBED_SCRIPT_ID;
    script.async = true;
    script.src = 'https://www.tiktok.com/embed.js';
    script.onload = () => {
        setTimeout(() => {
            disableTikTokAutoplay();
        }, 100);
    };
    document.body.appendChild(script);
};

const renderTikTokEmbeds = async () => {
    await nextTick();
    if (filteredTiktokVideos.value.length === 0) return;
    refreshTikTokEmbedScript();
    setTimeout(() => {
        disableTikTokAutoplay();
    }, 200);
};

const toCategoryLabel = (category: string) => {
    if (!category) return '-';
    return category
        .split(/[\s_-]+/)
        .filter(Boolean)
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

onMounted(async () => {
    await renderTikTokEmbeds();
});

watch(
    filteredTiktokVideos,
    () => {
        void renderTikTokEmbeds();
    },
    { deep: true },
);
</script>

<style scoped>
.pattern-dot-wave {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cdefs%3E%3CradialGradient id='dotGrad'%3E%3Cstop offset='0%25' stop-color='%23f7f5f0' stop-opacity='0.32'/%3E%3Cstop offset='100%25' stop-color='%23f7f5f0' stop-opacity='0'/3E%3C/radialGradient%3E%3C/defs%3E%3Ccircle cx='20' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='50' cy='30' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='80' cy='20' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='30' cy='60' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='70' cy='70' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='20' cy='90' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3Ccircle cx='80' cy='80' r='2' fill='url(%23dotGrad)' opacity='0.3'/%3E%3C/svg%3E");
    background-size: 100px 100px;
}

.tiktok-card {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
