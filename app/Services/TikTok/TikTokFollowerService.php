<?php

namespace App\Services\TikTok;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TikTokFollowerService
{
    /**
     * @return array{username: string, followers: ?int, formatted_followers: string, source: string, last_updated: ?string, is_stale: bool}
     */
    public function getFollowerSnapshot(string $username): array
    {
        $normalizedUsername = ltrim(strtolower(trim($username)), '@');
        $cacheKey = "tiktok.followers.{$normalizedUsername}";
        $ttl = max((int) config('services.tiktok.follower_cache_ttl', 900), 60);

        $cached = Cache::get($cacheKey);

        if (is_array($cached) && $this->isFresh($cached, $ttl)) {
            $cached['is_stale'] = false;

            return $cached;
        }

        $fresh = $this->fetchFromOfficialApi($normalizedUsername)
            ?? $this->fetchFromRapidApi($normalizedUsername);

        if ($fresh !== null) {
            $snapshot = [
                'username' => '@' . $normalizedUsername,
                'followers' => $fresh['followers'],
                'formatted_followers' => $this->formatCount($fresh['followers']),
                'source' => $fresh['source'],
                'last_updated' => now()->toIso8601String(),
                'is_stale' => false,
            ];

            Cache::put($cacheKey, $snapshot, now()->addSeconds($ttl));

            return $snapshot;
        }

        if (is_array($cached)) {
            $cached['is_stale'] = true;
            $cached['source'] = 'cache';

            return $cached;
        }

        return [
            'username' => '@' . $normalizedUsername,
            'followers' => null,
            'formatted_followers' => '-',
            'source' => 'unavailable',
            'last_updated' => null,
            'is_stale' => true,
        ];
    }

    /**
     * @return array{followers: int, source: string}|null
     */
    private function fetchFromOfficialApi(string $username): ?array
    {
        $apiBaseUrl = rtrim((string) config('services.tiktok.api_base_url', 'https://open.tiktokapis.com'), '/');
        $accessToken = $this->resolveOfficialAccessToken($apiBaseUrl);

        if ($accessToken === '') {
            return null;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->withToken($accessToken)
            ->get($apiBaseUrl . '/v2/user/info/', [
                'fields' => 'username,follower_count',
            ]);

        if (! $response->ok()) {
            return null;
        }

        $user = data_get($response->json(), 'data.user');

        if (! is_array($user)) {
            return null;
        }

        $apiUsername = ltrim(strtolower((string) ($user['username'] ?? '')), '@');

        if ($apiUsername !== '' && $apiUsername !== $username) {
            return null;
        }

        $followers = $this->extractFollowerCount($user);

        if ($followers === null) {
            return null;
        }

        return [
            'followers' => $followers,
            'source' => 'official',
        ];
    }

    private function resolveOfficialAccessToken(string $apiBaseUrl): string
    {
        $directAccessToken = trim((string) config('services.tiktok.access_token', ''));

        if ($directAccessToken !== '') {
            return $directAccessToken;
        }

        $cachedToken = Cache::get('tiktok.official.access_token');

        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $clientKey = trim((string) config('services.tiktok.client_key', ''));
        $clientSecret = trim((string) config('services.tiktok.client_secret', ''));

        if ($clientKey === '' || $clientSecret === '') {
            return '';
        }

        $response = Http::timeout(10)
            ->asForm()
            ->post($apiBaseUrl . '/v2/oauth/token/', [
                'client_key' => $clientKey,
                'client_secret' => $clientSecret,
                'grant_type' => 'client_credentials',
            ]);

        if (! $response->ok()) {
            return '';
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return '';
        }

        $accessToken = trim((string) ($payload['access_token'] ?? ''));

        if ($accessToken === '') {
            return '';
        }

        $expiresIn = (int) ($payload['expires_in'] ?? 3600);
        $safeTtl = max($expiresIn - 120, 60);

        Cache::put('tiktok.official.access_token', $accessToken, now()->addSeconds($safeTtl));

        return $accessToken;
    }

    /**
     * @return array{followers: int, source: string}|null
     */
    private function fetchFromRapidApi(string $username): ?array
    {
        $rapidApiKey = (string) config('services.tiktok.rapidapi_key', '');
        $rapidApiUrl = (string) config('services.tiktok.rapidapi_url', '');

        if ($rapidApiKey === '' || $rapidApiUrl === '') {
            return null;
        }

        $rapidApiHost = (string) config('services.tiktok.rapidapi_host', parse_url($rapidApiUrl, PHP_URL_HOST) ?: '');

        $response = Http::timeout(10)
            ->acceptJson()
            ->withHeaders([
                'X-RapidAPI-Key' => $rapidApiKey,
                'X-RapidAPI-Host' => $rapidApiHost,
            ])
            ->get($rapidApiUrl, [
                'username' => $username,
                'unique_id' => $username,
            ]);

        if (! $response->ok()) {
            return null;
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return null;
        }

        $followers = $this->extractFollowerCount($payload);

        if ($followers === null) {
            return null;
        }

        return [
            'followers' => $followers,
            'source' => 'rapidapi',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function isFresh(array $payload, int $ttl): bool
    {
        $lastUpdated = $payload['last_updated'] ?? null;

        if (! is_string($lastUpdated) || $lastUpdated === '') {
            return false;
        }

        try {
            $timestamp = Carbon::parse($lastUpdated);
        } catch (\Throwable) {
            return false;
        }

        return $timestamp->greaterThanOrEqualTo(now()->subSeconds($ttl));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function extractFollowerCount(array $payload): ?int
    {
        $paths = [
            'follower_count',
            'followers',
            'data.follower_count',
            'data.followers',
            'data.user.follower_count',
            'data.user.followers',
            'data.stats.follower_count',
            'data.stats.followers',
            'data.stats.followerCount',
            'data.userInfo.stats.followerCount',
            'stats.follower_count',
            'stats.followers',
            'stats.followerCount',
            'user.follower_count',
            'user.followers',
            'user.stats.follower_count',
            'user.stats.followers',
            'user.stats.followerCount',
        ];

        foreach ($paths as $path) {
            $value = data_get($payload, $path);
            $parsed = $this->parseCount($value);

            if ($parsed !== null) {
                return $parsed;
            }
        }

        return null;
    }

    private function parseCount(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int) round($value);
        }

        if (! is_string($value)) {
            return null;
        }

        $cleaned = strtolower(trim(str_replace([',', ' '], '', $value)));

        if ($cleaned === '') {
            return null;
        }

        if (is_numeric($cleaned)) {
            return (int) round((float) $cleaned);
        }

        if (preg_match('/^(\d+(?:\.\d+)?)([kmb])$/', $cleaned, $matches) !== 1) {
            return null;
        }

        $base = (float) $matches[1];
        $suffix = $matches[2];

        $multiplier = match ($suffix) {
            'k' => 1_000,
            'm' => 1_000_000,
            'b' => 1_000_000_000,
            default => 1,
        };

        return (int) round($base * $multiplier);
    }

    private function formatCount(int $count): string
    {
        if ($count >= 1_000_000_000) {
            return number_format($count / 1_000_000_000, 1) . 'B';
        }

        if ($count >= 1_000_000) {
            return number_format($count / 1_000_000, 1) . 'M';
        }

        if ($count >= 1_000) {
            return number_format($count / 1_000, 1) . 'K';
        }

        return (string) $count;
    }
}
