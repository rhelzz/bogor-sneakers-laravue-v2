<?php

namespace App\Http\Controllers;

use App\Models\TikTokFeed;
use App\Services\TikTok\TikTokOEmbedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Throwable;

class TikTokFeedController extends Controller
{
    public function __construct(
        private readonly TikTokOEmbedService $oEmbedService,
    ) {}

    /**
     * Show the TikTok feed admin page.
     */
    public function index()
    {
        $feeds = TikTokFeed::orderBy('sort_order', 'asc')->get(['*']);

        return Inertia::render('Admin/TikTokFeed', [
            'feeds' => $feeds,
            'categories' => $feeds
                ->pluck('category')
                ->filter()
                ->map(static fn (string $category) => trim($category))
                ->unique()
                ->values(),
        ]);
    }

    /**
     * Store a new TikTok feed item.
     */
    public function store(Request $request): RedirectResponse
    {
        if (TikTokFeed::count('*') >= 4) {
            throw ValidationException::withMessages([
                'url' => 'Maksimal 4 link TikTok pada feed home.',
            ]);
        }

        $validated = $this->validatePayload($request, false);
        $metadata = $this->resolveMetadata($validated['url']);

        $nextOrder = (TikTokFeed::max('sort_order') ?? -1) + 1;
        $category = $this->resolveCategory($validated);

        TikTokFeed::create([
            'url' => $metadata['url'],
            'category' => $category,
            'title' => $metadata['title'],
            'author_name' => $metadata['author_name'],
            'thumbnail_url' => $metadata['thumbnail_url'],
            'video_id' => $metadata['video_id'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
            'sort_order' => $validated['sort_order'] ?? $nextOrder,
        ]);

        return back()->with('success', 'Link TikTok berhasil ditambahkan.');
    }

    /**
     * Update TikTok feed item.
     */
    public function update(Request $request, TikTokFeed $tiktokFeed): RedirectResponse
    {
        $validated = $this->validatePayload($request, true);

        $category = $this->resolveCategory($validated, $tiktokFeed->category);
        $nextUrl = isset($validated['url']) ? $validated['url'] : $tiktokFeed->url;
        $isUrlChanged = $nextUrl !== $tiktokFeed->url;

        $metadata = null;

        if ($isUrlChanged) {
            $metadata = $this->resolveMetadata($nextUrl);
        }

        $tiktokFeed->update([
            'url' => $metadata['url'] ?? $tiktokFeed->url,
            'category' => $category,
            'title' => $metadata['title'] ?? $tiktokFeed->title,
            'author_name' => $metadata['author_name'] ?? $tiktokFeed->author_name,
            'thumbnail_url' => $metadata['thumbnail_url'] ?? $tiktokFeed->thumbnail_url,
            'video_id' => $metadata['video_id'] ?? $tiktokFeed->video_id,
            'is_active' => (bool) ($validated['is_active'] ?? $tiktokFeed->is_active),
            'sort_order' => $validated['sort_order'] ?? $tiktokFeed->sort_order,
        ]);

        return back()->with('success', 'Link TikTok berhasil diperbarui.');
    }

    /**
     * Delete TikTok feed item.
     */
    public function destroy(TikTokFeed $tiktokFeed): RedirectResponse
    {
        $deletedId = $tiktokFeed->id;

        TikTokFeed::destroy($deletedId);

        return back()->with('success', 'Link TikTok berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, bool $isUpdate): array
    {
        $requiredRule = $isUpdate ? 'sometimes' : 'required';

        return $request->validate([
            'url' => [$requiredRule, 'string', 'max:500', function (string $attribute, mixed $value, callable $fail): void {
                if (! is_string($value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
                    $fail('URL TikTok tidak valid.');

                    return;
                }

                $host = strtolower((string) parse_url($value, PHP_URL_HOST));

                if (! ($host === 'tiktok.com' || str_ends_with($host, '.tiktok.com'))) {
                    $fail('URL harus berasal dari TikTok.');
                }
            }],
            'category' => ['nullable', 'string', 'max:64'],
            'category_new' => ['nullable', 'string', 'max:64'],
            'is_active' => [$isUpdate ? 'sometimes' : 'nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'url.required' => 'Link TikTok wajib diisi.',
            'url.max' => 'Panjang URL maksimal 500 karakter.',
            'category.max' => 'Kategori maksimal 64 karakter.',
            'category_new.max' => 'Kategori baru maksimal 64 karakter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveCategory(array $validated, ?string $fallback = null): string
    {
        $selectedCategory = trim((string) ($validated['category'] ?? ''));
        $newCategory = trim((string) ($validated['category_new'] ?? ''));

        if ($selectedCategory === '__new') {
            $selectedCategory = '';
        }

        $category = $newCategory !== '' ? $newCategory : $selectedCategory;

        if ($category === '' && $fallback !== null && trim($fallback) !== '') {
            return trim($fallback);
        }

        if ($category === '') {
            throw ValidationException::withMessages([
                'category' => 'Pilih kategori atau isi kategori baru.',
            ]);
        }

        return Str::of($category)->trim()->lower()->value();
    }

    /**
     * @return array{url: string, title: ?string, author_name: ?string, thumbnail_url: ?string, video_id: ?string}
     */
    private function resolveMetadata(string $url): array
    {
        try {
            return $this->oEmbedService->fetchMetadata($url);
        } catch (Throwable) {
            return $this->oEmbedService->fallbackMetadata($url);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeFeed(TikTokFeed $feed): array
    {
        return [
            'id' => $feed->id,
            'url' => $feed->url,
            'category' => $feed->category,
            'title' => $feed->title,
            'author_name' => $feed->author_name,
            'thumbnail_url' => $feed->thumbnail_url,
            'video_id' => $feed->video_id,
            'is_active' => $feed->is_active,
            'sort_order' => $feed->sort_order,
            'created_at' => $feed->created_at,
            'updated_at' => $feed->updated_at,
        ];
    }
}
