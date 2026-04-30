import { TikTokFeedItem } from '@/types/tiktok';

export const TIKTOK_EMBED_SCRIPT_ID = 'tiktok-admin-embed-script';

export const extractTikTokUsername = (videoUrl: string, fallback?: string | null) => {
    const match = videoUrl.match(/tiktok\.com\/@([^/?]+)/i);

    if (match?.[1]) {
        return match[1];
    }

    const cleanFallback = (fallback ?? '').trim().replace(/^@/, '');

    return cleanFallback !== '' ? cleanFallback : 'bogorsneakers';
};

export const getEmbedAuthor = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);

    return `@${username}`;
};

export const getEmbedProfileUrl = (video: TikTokFeedItem) => {
    const username = extractTikTokUsername(video.url, video.author_name);

    return `https://www.tiktok.com/@${encodeURIComponent(username)}?refer=embed`;
};

export const getEmbedVideoUrl = (video: TikTokFeedItem) => {
    try {
        const url = new URL(video.url);
        url.searchParams.set('refer', 'embed');

        return url.toString();
    } catch {
        return video.url;
    }
};

export const getEmbedCaption = (video: TikTokFeedItem) => {
    return video.title?.trim() || 'Video TikTok';
};

export const disableTikTokAutoplay = () => {
    const tiktokEmbeds = document.querySelectorAll('.tiktok-embed');

    tiktokEmbeds.forEach((embed) => {
        const iframe = embed.querySelector('iframe');

        if (iframe) {
            iframe.addEventListener(
                'load',
                () => {
                    try {
                        const sandboxValue = iframe.getAttribute('sandbox') || '';

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

export const refreshTikTokEmbedScript = () => {
    const existing = document.getElementById(TIKTOK_EMBED_SCRIPT_ID);

    if (existing) {
        existing.remove();
    }

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
