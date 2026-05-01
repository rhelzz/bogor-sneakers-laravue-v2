<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\CarouselSlide;
use App\Models\GallerySlot;
use App\Models\HomePreorder;
use App\Models\TikTokFeed;
use App\Services\TikTok\TikTokFollowerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    private const HOME_PREORDER_LIMIT = 5;

    private const HOME_LATEST_COLLECTION_LIMIT = 8;

    private const GALLERY_DEFAULT_AUTHOR = '@bogorsneakers';

    /**
     * @var array<int, string>
     */
    private const GALLERY_DEFAULT_TITLES = [
        1 => 'Air Max 97 x Jogja Streets',
        2 => 'Samba OG - Bogor Vibe',
        3 => 'Jordan 1 Bred - Cold Day',
        4 => 'NB 574 Navy x Rain',
        5 => 'Vans Old Skool',
        6 => 'Converse Chuck 70',
        7 => 'Asics Gel-Kayano',
        8 => 'Puma RS-X Effekt',
    ];

    public function __construct(
        private readonly TikTokFollowerService $followerService,
    ) {}

    public function index(): Response
    {
        $username = (string) config('services.tiktok.username', 'bogorsneaker');

        return Inertia::render('Home', [
            'carouselSlides' => $this->getCarouselSlides(),
            'tiktokFeed' => $this->getTikTokFeed(),
            'tiktokFollowers' => $this->followerService->getFollowerSnapshot($username),
            'gallerySlots' => $this->getGallerySlots(),
            'preorderItems' => $this->getHomePreorders(),
            'latestCollections' => $this->getLatestCollections(),
        ]);
    }

    /**
     * @return array<int, array{id: int, image_url: string, is_active: bool}>
     */
    private function getCarouselSlides(): array
    {
        return CarouselSlide::query()
            ->active()
            ->whereNotNull('image_path', 'and')
            ->where('image_path', '!=', '')
            ->get(['*'])
            ->map(static fn (CarouselSlide $slide): array => [
                'id' => $slide->id,
                'image_url' => $slide->image_url,
                'is_active' => (bool) $slide->is_active,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, url: string, category: string, title: ?string, author_name: ?string, thumbnail_url: ?string, video_id: ?string}>
     */
    private function getTikTokFeed(): array
    {
        return TikTokFeed::query()
            ->active()
            ->get(['*'])
            ->map(static fn (TikTokFeed $feed): array => [
                'id' => $feed->id,
                'url' => $feed->url,
                'category' => $feed->category,
                'title' => $feed->title,
                'author_name' => $feed->author_name,
                'thumbnail_url' => $feed->thumbnail_url,
                'video_id' => $feed->video_id,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, slot: int, image_path: ?string, image_url: ?string, title: string, author: string}>
     */
    private function getGallerySlots(): array
    {
        return GallerySlot::query()
            ->whereBetween('slot', [1, 8])
            ->ordered()
            ->get()
            ->map(function (GallerySlot $slot): array {
                $title = is_string($slot->title) && trim($slot->title) !== ''
                    ? trim($slot->title)
                    : $this->defaultGalleryTitle((int) $slot->slot);

                $author = is_string($slot->author) && trim($slot->author) !== ''
                    ? $this->normalizeGalleryAuthor($slot->author)
                    : self::GALLERY_DEFAULT_AUTHOR;

                return [
                    'id' => $slot->id,
                    'slot' => $slot->slot,
                    'image_path' => $slot->image_path,
                    'image_url' => $slot->image_url,
                    'title' => $title,
                    'author' => $author,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, catalog_id: int, product: string, sku: string, size: string, batch: string, progress: int, remaining_slots: int, max_slots: int, filled_slots: int, status: string, countdown_ends_at: ?string}>
     */
    private function getHomePreorders(): array
    {
        HomePreorder::expireVisibleItems();

        return HomePreorder::query()
            ->with(['catalog.images', 'catalog.sizes'])
            ->visibleForHome()
            ->whereHas('catalog', function (Builder $query): void {
                $query->active()->where('status', 'po');
            })
            ->ordered()
            ->limit(self::HOME_PREORDER_LIMIT)
            ->get()
            ->map(function (HomePreorder $assignment): array {
                $catalog = $assignment->catalog;

                if (! $catalog instanceof Catalog) {
                    return [];
                }

                $maxSlots = max((int) $assignment->max_slots, 1);
                $filledSlots = min(max((int) $assignment->filled_slots, 0), $maxSlots);
                $remainingSlots = max($maxSlots - $filledSlots, 0);
                $progress = (int) round(($filledSlots / $maxSlots) * 100);

                $batchLabel = trim((string) ($assignment->batch_label ?? ''));

                return [
                    'id' => $assignment->id,
                    'catalog_id' => (int) $catalog->id,
                    'product' => $catalog->name,
                    'size' => $this->formatCatalogSizeRange($catalog),
                    'batch' => $batchLabel !== ''
                        ? $batchLabel
                        : sprintf('#%02d', ((int) $assignment->urutan)),
                    'progress' => $progress,
                    'remaining_slots' => $remainingSlots,
                    'max_slots' => $maxSlots,
                    'filled_slots' => $filledSlots,
                    'status' => $this->toPreorderStatusLabel((string) $assignment->status),
                    'countdown_ends_at' => optional($assignment->countdown_ends_at)?->toISOString(),
                ];
            })
            ->filter(static fn (array $row): bool => $row !== [])
            ->values()
            ->all();
    }

    /**
    * @return array<int, array{id: int, name: string, size: string, price: int, status: string, statusClass: string, image_url: ?string}>
    */    private function getLatestCollections(): array
    {
        return Catalog::query()
            ->active()
            ->with(['images', 'sizes'])
            ->latest('created_at')
            ->limit(self::HOME_LATEST_COLLECTION_LIMIT)
            ->get(['*'])
            ->map(function (Catalog $catalog): array {
                return [
                    'id' => $catalog->id,
                    'name' => $catalog->name,
                    'size' => $this->formatCatalogSizeRange($catalog),
                    'price' => (int) $catalog->price,
                    'status' => $this->toCatalogStatusLabel((string) $catalog->status),
                    'statusClass' => $this->toCatalogStatusClass((string) $catalog->status),
                    'image_url' => $catalog->primary_image_url,
                ];
            })
            ->values()
            ->all();
    }

    private function formatCatalogSizeRange(Catalog $catalog): string
    {
        $sizes = $catalog->sizes
            ->pluck('size_eu')
            ->map(fn (mixed $size): int => (int) $size)
            ->sort()
            ->values()
            ->all();

        if ($sizes === []) {
            return '-';
        }

        $min = $sizes[0];
        $max = $sizes[count($sizes) - 1];

        if ($min === $max) {
            return (string) $min;
        }

        return sprintf('%d-%d', $min, $max);
    }

    private function toCatalogStatusLabel(string $status): string
    {
        return match ($status) {
            'po' => 'PO',
            'habis' => 'Habis',
            default => 'Ready',
        };
    }

    private function toCatalogStatusClass(string $status): string
    {
        return match ($status) {
            'po' => 'bg-matcha text-washi',
            'habis' => 'bg-hai/50 text-washi',
            default => 'bg-take text-washi',
        };
    }

    private function toPreorderStatusLabel(string $status): string
    {
        return match ($status) {
            'buka' => 'BUKA',
            'hampir_habis' => 'HAMPIR HABIS',
            default => 'HABIS',
        };
    }

    private function toKey(string $value): string
    {
        $key = Str::of($value)->trim()->lower()->slug('')->value();

        return $key === '' ? 'lainnya' : $key;
    }

    private function defaultGalleryTitle(int $slot): string
    {
        return self::GALLERY_DEFAULT_TITLES[$slot] ?? sprintf('Galeri Karya Slot %d', $slot);
    }

    private function normalizeGalleryAuthor(string $author): string
    {
        $trimmed = trim($author);

        if ($trimmed === '') {
            return self::GALLERY_DEFAULT_AUTHOR;
        }

        if (str_starts_with($trimmed, '@')) {
            return $trimmed;
        }

        return '@'.$trimmed;
    }
}
