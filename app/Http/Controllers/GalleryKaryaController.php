<?php

namespace App\Http\Controllers;

use App\Models\GallerySlot;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GalleryKaryaController extends Controller
{
    private const MAX_SLOT = 8;

    private const DEFAULT_AUTHOR = '@bogorsneakers';

    /**
     * @var array<int, string>
     */
    private const DEFAULT_TITLES = [
        1 => 'Air Max 97 x Jogja Streets',
        2 => 'Samba OG - Bogor Vibe',
        3 => 'Jordan 1 Bred - Cold Day',
        4 => 'NB 574 Navy x Rain',
        5 => 'Vans Old Skool',
        6 => 'Converse Chuck 70',
        7 => 'Asics Gel-Kayano',
        8 => 'Puma RS-X Effekt',
    ];

    /**
     * Show the gallery karya admin page.
     */
    public function index()
    {
        $slots = $this->ensureEightSlots();

        return Inertia::render('Admin/GaleriKarya', [
            'slots' => $slots->map(fn (GallerySlot $slot) => $this->serializeSlot($slot)),
        ]);
    }

    /**
     * Replace image for a fixed gallery slot.
     */
    public function replaceImage(Request $request, GallerySlot $gallerySlot)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:5120',
        ], [
            'image.required' => 'Image wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG atau PNG.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $oldPath = $gallerySlot->image_path;
        $newPath = $this->storeOptimizedImage($request->file('image'));

        $gallerySlot->update([
            'image_path' => $newPath,
        ]);

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        return response()->json([
            'message' => 'Gambar slot berhasil diperbarui.',
            'slot' => $this->serializeSlot($gallerySlot->fresh()),
        ]);
    }

    /**
     * Update title and author for a fixed gallery slot.
     */
    public function updateMetadata(Request $request, GallerySlot $gallerySlot)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'author' => 'required|string|max:80',
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.max' => 'Judul maksimal 120 karakter.',
            'author.required' => 'Author wajib diisi.',
            'author.max' => 'Author maksimal 80 karakter.',
        ]);

        $title = trim((string) $validated['title']);
        $author = $this->normalizeAuthor((string) $validated['author']);

        if ($title === '') {
            $title = $this->defaultTitleForSlot((int) $gallerySlot->slot);
        }

        $gallerySlot->update([
            'title' => $title,
            'author' => $author,
        ]);

        return response()->json([
            'message' => 'Judul dan author berhasil diperbarui.',
            'slot' => $this->serializeSlot($gallerySlot->fresh()),
        ]);
    }

    /**
     * Public API for homepage gallery.
     */
    public function api()
    {
        $slots = $this->ensureEightSlots();

        return response()->json(
            $slots->map(fn (GallerySlot $slot) => $this->serializeSlot($slot))
        );
    }

    /**
     * Ensure database always has exactly 8 fixed slots.
     */
    private function ensureEightSlots(): Collection
    {
        for ($slotNumber = 1; $slotNumber <= self::MAX_SLOT; $slotNumber++) {
            $slot = GallerySlot::firstOrCreate(
                ['slot' => $slotNumber],
                [
                    'image_path' => null,
                    'title' => $this->defaultTitleForSlot($slotNumber),
                    'author' => self::DEFAULT_AUTHOR,
                ],
            );

            $updates = [];

            if (! is_string($slot->title) || trim($slot->title) === '') {
                $updates['title'] = $this->defaultTitleForSlot($slotNumber);
            }

            if (! is_string($slot->author) || trim($slot->author) === '') {
                $updates['author'] = self::DEFAULT_AUTHOR;
            }

            if ($updates !== []) {
                $slot->fill($updates);
                $slot->save();
            }
        }

        return GallerySlot::query()
            ->whereBetween('slot', [1, self::MAX_SLOT])
            ->ordered()
            ->get();
    }

    /**
     * Try to optimize image bytes before storing on disk.
     */
    private function storeOptimizedImage(?UploadedFile $file): string
    {
        if (! $file instanceof UploadedFile) {
            abort(422, 'Image tidak valid.');
        }

        $optimized = $this->optimizeImageBinary($file);

        if ($optimized === null) {
            return $file->store('galeri-karya', 'public');
        }

        Storage::disk('public')->put($optimized['path'], $optimized['binary']);

        return $optimized['path'];
    }

    /**
     * @return array{path: string, binary: string}|null
     */
    private function optimizeImageBinary(UploadedFile $file): ?array
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $rawBinary = @file_get_contents($file->getRealPath());

        if (! is_string($rawBinary) || $rawBinary === '') {
            return null;
        }

        $sourceImage = @imagecreatefromstring($rawBinary);

        if ($sourceImage === false) {
            return null;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        if ($sourceWidth < 1 || $sourceHeight < 1) {
            imagedestroy($sourceImage);

            return null;
        }

        $maxWidth = 1800;
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;

        if ($sourceWidth > $maxWidth) {
            $scale = $maxWidth / $sourceWidth;
            $targetWidth = $maxWidth;
            $targetHeight = max((int) round($sourceHeight * $scale), 1);
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($canvas === false) {
            imagedestroy($sourceImage);

            return null;
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled(
            $canvas,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $fileName = (string) Str::uuid();
        $storageFolder = 'galeri-karya';
        $binary = null;
        $path = null;

        if (function_exists('imagewebp')) {
            ob_start();
            imagewebp($canvas, null, 82);
            $binary = ob_get_clean();
            $path = sprintf('%s/%s.webp', $storageFolder, $fileName);
        } else {
            $mime = strtolower((string) $file->getMimeType());

            if ($mime === 'image/png' && function_exists('imagepng')) {
                ob_start();
                imagepng($canvas, null, 6);
                $binary = ob_get_clean();
                $path = sprintf('%s/%s.png', $storageFolder, $fileName);
            } elseif (function_exists('imagejpeg')) {
                ob_start();
                imagejpeg($canvas, null, 82);
                $binary = ob_get_clean();
                $path = sprintf('%s/%s.jpg', $storageFolder, $fileName);
            }
        }

        imagedestroy($canvas);
        imagedestroy($sourceImage);

        if (! is_string($binary) || $binary === '' || ! is_string($path)) {
            return null;
        }

        return [
            'path' => $path,
            'binary' => $binary,
        ];
    }

    /**
     * @return array{id: int, slot: int, image_path: ?string, image_url: ?string, title: string, author: string, updated_at: mixed}
     */
    private function serializeSlot(GallerySlot $slot): array
    {
        $title = is_string($slot->title) && trim($slot->title) !== ''
            ? trim($slot->title)
            : $this->defaultTitleForSlot((int) $slot->slot);

        $author = is_string($slot->author) && trim($slot->author) !== ''
            ? $this->normalizeAuthor($slot->author)
            : self::DEFAULT_AUTHOR;

        return [
            'id' => $slot->id,
            'slot' => $slot->slot,
            'image_path' => $slot->image_path,
            'image_url' => $slot->image_url,
            'title' => $title,
            'author' => $author,
            'updated_at' => $slot->updated_at,
        ];
    }

    private function defaultTitleForSlot(int $slot): string
    {
        return self::DEFAULT_TITLES[$slot] ?? sprintf('Galeri Karya Slot %d', $slot);
    }

    private function normalizeAuthor(string $author): string
    {
        $trimmed = trim($author);

        if ($trimmed === '') {
            return self::DEFAULT_AUTHOR;
        }

        if (str_starts_with($trimmed, '@')) {
            return $trimmed;
        }

        return '@'.$trimmed;
    }
}
