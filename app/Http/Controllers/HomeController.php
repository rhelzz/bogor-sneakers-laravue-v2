<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use App\Models\GallerySlot;
use App\Models\TikTokFeed;
use App\Services\TikTok\TikTokFollowerService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
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
