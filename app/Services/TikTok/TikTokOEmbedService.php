<?php

namespace App\Services\TikTok;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class TikTokOEmbedService
{
    /**
     * Fetch TikTok video metadata through oEmbed endpoint.
     *
     * @return array{url: string, title: ?string, author_name: ?string, thumbnail_url: ?string, video_id: ?string}
     */
    public function fetchMetadata(string $url): array
    {
        $normalizedUrl = $this->normalizeUrl($url);

        if (! $this->isTikTokUrl($normalizedUrl)) {
            throw new RuntimeException('URL TikTok tidak valid.');
        }

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get('https://www.tiktok.com/oembed', [
                    'url' => $normalizedUrl,
                ]);

            if (! $response->ok()) {
                return $this->fallbackMetadata($normalizedUrl);
            }

            $payload = $response->json();

            if (! is_array($payload)) {
                return $this->fallbackMetadata($normalizedUrl);
            }

            return [
                'url' => $normalizedUrl,
                'title' => $this->nullableString($payload['title'] ?? null),
                'author_name' => $this->nullableString($payload['author_name'] ?? null),
                'thumbnail_url' => $this->nullableString($payload['thumbnail_url'] ?? null),
                'video_id' => $this->extractVideoId($normalizedUrl, $payload),
            ];
        } catch (Throwable) {
            return $this->fallbackMetadata($normalizedUrl);
        }
    }

    /**
     * Build minimum usable metadata when oEmbed is unavailable.
     *
     * @return array{url: string, title: ?string, author_name: ?string, thumbnail_url: ?string, video_id: ?string}
     */
    public function fallbackMetadata(string $url): array
    {
        $normalizedUrl = $this->normalizeUrl($url);

        if (! $this->isTikTokUrl($normalizedUrl)) {
            throw new RuntimeException('URL TikTok tidak valid.');
        }

        return [
            'url' => $normalizedUrl,
            'title' => null,
            'author_name' => $this->extractAuthorFromUrl($normalizedUrl),
            'thumbnail_url' => null,
            'video_id' => $this->extractVideoId($normalizedUrl, []),
        ];
    }

    public function isTikTokUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if ($host === '') {
            return false;
        }

        return $host === 'tiktok.com' || str_ends_with($host, '.tiktok.com');
    }

    private function normalizeUrl(string $url): string
    {
        $cleaned = trim($url);

        if (! str_starts_with(strtolower($cleaned), 'http://') && ! str_starts_with(strtolower($cleaned), 'https://')) {
            $cleaned = 'https://' . $cleaned;
        }

        return $cleaned;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function extractAuthorFromUrl(string $url): ?string
    {
        if (preg_match('/tiktok\.com\/@([^\/?]+)/i', $url, $matches) !== 1) {
            return null;
        }

        $username = trim((string) ($matches[1] ?? ''));

        return $username === '' ? null : '@' . $username;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractVideoId(string $url, array $payload): ?string
    {
        $embedProductId = $this->nullableString($payload['embed_product_id'] ?? null);

        if ($embedProductId !== null) {
            return $embedProductId;
        }

        $html = $this->nullableString($payload['html'] ?? null);

        if ($html !== null && preg_match('/data-video-id="(\d+)"/', $html, $matches) === 1) {
            return $matches[1];
        }

        if (preg_match('/\/video\/(\d+)/', $url, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }
}
