<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\CatalogImage;
use App\Models\ShoeModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class KatalogController extends Controller
{
    private const MAX_IMAGES = 6;

    public function index(): Response
    {
        $products = Catalog::query()
            ->active()
            ->with(['images', 'sizes'])
            ->ordered()
            ->get(['*']);

        return Inertia::render('Katalog', [
            'products' => $products
                ->map(fn (Catalog $catalog): array => $this->serializePublicCatalog($catalog))
                ->values()
                ->all(),
            'maxPrice' => max(3000000, (int) ($products->max('price') ?? 0)),
        ]);
    }

    public function show(string $catalog): Response|RedirectResponse
    {
        $parsed = $this->parseRouteKey($catalog);

        if ($parsed === null) {
            abort(404);
        }

        $product = Catalog::query()
            ->active()
            ->with(['images', 'sizes'])
            ->where('public_id', $parsed['public_id'])
            ->firstOrFail();

        if ($parsed['slug'] !== $product->slug) {
            return redirect()->route('katalog.show', ['catalog' => $product->route_key], 301);
        }

        return Inertia::render('ProductDetail', [
            'product' => $this->serializeDetailCatalog($product),
        ]);
    }

    public function adminIndex(): Response
    {
        $catalogs = Catalog::query()
            ->with(['images', 'sizes', 'shoeModel'])
            ->ordered()
            ->get(['*']);

        return Inertia::render('Admin/Katalog/Index', [
            'catalogs' => $catalogs
                ->map(fn (Catalog $catalog): array => $this->serializeAdminCatalog($catalog))
                ->values()
                ->all(),
            'maxImages' => self::MAX_IMAGES,
            'shoeModels' => ShoeModel::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Katalog/Create', [
            'shoeModels' => ShoeModel::where('is_active', true)->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateCatalogPayload($request);

        /** @var array<int, UploadedFile> $images */
        $images = $request->file('images', []);

        $catalog = DB::transaction(function () use ($validated, $images): Catalog {
            $sortOrder = (int) ($validated['sort_order'] ?? ((Catalog::max('sort_order') ?? -1) + 1));

            $catalog = Catalog::create([
                'shoe_model_id' => isset($validated['shoe_model_id']) ? (int) $validated['shoe_model_id'] : null,
                'name' => trim((string) $validated['name']),
                'collection' => trim((string) $validated['collection']),
                'description' => $this->normalizeNullableText($validated['description'] ?? null),
                'price' => (int) $validated['price'],
                'status' => (string) $validated['status'],
                'preorder_min_days' => $this->resolvePreorderMinDays($validated),
                'preorder_max_days' => $this->resolvePreorderMaxDays($validated),
                'discount_type' => $this->normalizeNullableText($validated['discount_type'] ?? null),
                'discount_value' => isset($validated['discount_value']) ? (int) $validated['discount_value'] : null,
                'discount_starts_at' => $validated['discount_starts_at'] ?? null,
                'discount_ends_at' => $validated['discount_ends_at'] ?? null,
                'popularity_score' => (int) ($validated['popularity_score'] ?? 0),
                'is_active' => (bool) ($validated['is_active'] ?? true),
                'sort_order' => $sortOrder,
            ]);

            $this->syncSizes($catalog, $validated['sizes'] ?? []);
            $this->appendImages($catalog, $images);

            return $catalog->fresh(['images', 'sizes']);
        });

        return response()->json([
            'message' => 'Produk katalog berhasil ditambahkan.',
            'catalog' => $this->serializeAdminCatalog($catalog),
        ], 201);
    }

    public function update(Request $request, Catalog $catalog): JsonResponse
    {
        $validated = $this->validateCatalogPayload($request, $catalog);

        $updated = DB::transaction(function () use ($catalog, $validated): Catalog {
            $catalog->update([
                'shoe_model_id' => isset($validated['shoe_model_id']) ? (int) $validated['shoe_model_id'] : null,
                'name' => trim((string) $validated['name']),
                'collection' => trim((string) $validated['collection']),
                'description' => $this->normalizeNullableText($validated['description'] ?? null),
                'price' => (int) $validated['price'],
                'status' => (string) $validated['status'],
                'preorder_min_days' => $this->resolvePreorderMinDays($validated),
                'preorder_max_days' => $this->resolvePreorderMaxDays($validated),
                'discount_type' => $this->normalizeNullableText($validated['discount_type'] ?? null),
                'discount_value' => isset($validated['discount_value']) ? (int) $validated['discount_value'] : null,
                'discount_starts_at' => $validated['discount_starts_at'] ?? null,
                'discount_ends_at' => $validated['discount_ends_at'] ?? null,
                'popularity_score' => (int) ($validated['popularity_score'] ?? 0),
                'is_active' => (bool) ($validated['is_active'] ?? true),
                'sort_order' => (int) ($validated['sort_order'] ?? $catalog->sort_order),
            ]);

            $this->syncSizes($catalog, $validated['sizes'] ?? []);

            return $catalog->fresh(['images', 'sizes']);
        });

        return response()->json([
            'message' => 'Produk katalog berhasil diperbarui.',
            'catalog' => $this->serializeAdminCatalog($updated),
        ]);
    }

    public function destroy(Catalog $catalog): JsonResponse
    {
        $deletedId = $catalog->id;

        DB::transaction(function () use ($catalog): void {
            $catalog->loadMissing('images');

            $this->deleteStoredImage($catalog->card_image_path);

            $catalog->images->each(function (CatalogImage $image): void {
                $this->deleteStoredImage($image->image_path);
            });

            $catalog->delete();
        });

        return response()->json([
            'message' => 'Produk katalog berhasil dihapus.',
            'id' => $deletedId,
        ]);
    }

    public function uploadImage(Request $request, Catalog $catalog): JsonResponse
    {
        $validated = $request->validate([
            'kind' => ['nullable', 'string', Rule::in(['card', 'preview'])],
            'image' => 'required|image|mimes:jpeg,png,webp|max:5120',
        ], [
            'kind.in' => 'Jenis gambar harus card atau preview.',
            'image.required' => 'Gambar wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $kind = (string) ($validated['kind'] ?? 'preview');

        /** @var UploadedFile $file */
        $file = $request->file('image');

        if ($kind === 'card') {
            $updated = DB::transaction(function () use ($catalog, $file): Catalog {
                $previousPath = $catalog->card_image_path;
                $path = $this->storeOptimizedImage($file);

                $catalog->update([
                    'card_image_path' => $path,
                ]);

                $isPreviousPathUsedByPreview = is_string($previousPath)
                    && $previousPath !== ''
                    && $catalog->images()->where('image_path', $previousPath)->exists();

                if ($previousPath !== $path && ! $isPreviousPathUsedByPreview) {
                    $this->deleteStoredImage($previousPath);
                }

                return $catalog->fresh(['images', 'sizes']);
            });

            return response()->json([
                'message' => 'Gambar card katalog berhasil diperbarui.',
                'kind' => 'card',
                'catalog_id' => $catalog->id,
                'card_image_path' => $updated->card_image_path,
                'card_image_url' => $updated->card_image_url,
            ]);
        }

        $catalog->loadMissing('images');

        if ($catalog->images->count() >= self::MAX_IMAGES) {
            throw ValidationException::withMessages([
                'image' => sprintf('Maksimal %d gambar per produk.', self::MAX_IMAGES),
            ]);
        }

        $image = DB::transaction(function () use ($catalog, $file): CatalogImage {
            $nextPosition = ((int) $catalog->images()->max('position')) + 1;
            $path = $this->storeOptimizedImage($file);

            return $catalog->images()->create([
                'image_path' => $path,
                'position' => $nextPosition,
            ]);
        });

        return response()->json([
            'message' => 'Gambar katalog berhasil ditambahkan.',
            'kind' => 'preview',
            'image' => $this->serializeImage($image->fresh()),
            'catalog_id' => $catalog->id,
        ], 201);
    }

    public function deleteImage(Catalog $catalog, CatalogImage $catalogImage): JsonResponse
    {
        if ($catalogImage->catalog_id !== $catalog->id) {
            abort(404);
        }

        DB::transaction(function () use ($catalog, $catalogImage): void {
            $deletedImagePath = $catalogImage->image_path;

            $catalogImage->delete();

            $isUsedByCard = is_string($catalog->card_image_path)
                && $catalog->card_image_path === $deletedImagePath;
            $isUsedByPreview = $catalog->images()
                ->where('image_path', $deletedImagePath)
                ->exists();

            if (! $isUsedByCard && ! $isUsedByPreview) {
                $this->deleteStoredImage($deletedImagePath);
            }

            $this->reindexCatalogImages($catalog);
        });

        return response()->json([
            'message' => 'Gambar katalog berhasil dihapus.',
            'id' => $catalogImage->id,
        ]);
    }

    public function reorderImages(Request $request, Catalog $catalog): JsonResponse
    {
        $validated = $request->validate([
            'image_ids' => 'required|array|min:1|max:6',
            'image_ids.*' => 'required|integer',
        ], [
            'image_ids.required' => 'Urutan gambar wajib dikirim.',
            'image_ids.array' => 'Format urutan gambar tidak valid.',
            'image_ids.max' => sprintf('Maksimal %d gambar per produk.', self::MAX_IMAGES),
        ]);

        /** @var array<int, int> $ids */
        $ids = array_values(array_unique(array_map('intval', $validated['image_ids'])));

        $imageIds = $catalog->images()->orderBy('position')->pluck('id')->all();

        sort($ids);
        $sortedCurrent = $imageIds;
        sort($sortedCurrent);

        if ($ids !== $sortedCurrent) {
            throw ValidationException::withMessages([
                'image_ids' => 'Daftar gambar tidak sesuai dengan produk yang dipilih.',
            ]);
        }

        $orderedIds = $validated['image_ids'];

        DB::transaction(function () use ($catalog, $orderedIds): void {
            foreach (array_values($orderedIds) as $index => $imageId) {
                $catalog->images()
                    ->whereKey((int) $imageId)
                    ->update(['position' => $index + 1]);
            }
        });

        $images = $catalog->images()->orderBy('position')->get()->map(fn (CatalogImage $image): array => $this->serializeImage($image));

        return response()->json([
            'message' => 'Urutan gambar berhasil diperbarui.',
            'images' => $images,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateCatalogPayload(Request $request, ?Catalog $catalog = null): array
    {
        return $request->validate([
            'shoe_model_id' => ['nullable', 'integer', 'exists:shoe_models,id'],
            'name' => ['required', 'string', 'max:160'],
            'collection' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'status' => ['required', Rule::in(['ready', 'po', 'habis'])],
            'preorder_min_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'preorder_max_days' => ['nullable', 'integer', 'min:1', 'max:365', 'gte:preorder_min_days'],
            'discount_type' => ['nullable', 'string', 'max:24'],
            'discount_value' => ['nullable', 'integer', 'min:0', 'max:1000000000'],
            'discount_starts_at' => ['nullable', 'date'],
            'discount_ends_at' => ['nullable', 'date', 'after_or_equal:discount_starts_at'],
            'popularity_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*' => ['required', 'integer', 'min:1', 'max:255'],
            'images' => ['nullable', 'array', 'max:6'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'shoe_model_id.exists' => 'Model sepatu tidak valid.',
            'name.required' => 'Nama katalog wajib diisi.',
            'collection.required' => 'Koleksi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka.',
            'status.required' => 'Status katalog wajib dipilih.',
            'sizes.required' => 'Minimal satu ukuran wajib diisi.',
            'sizes.array' => 'Format ukuran tidak valid.',
            'sizes.min' => 'Minimal satu ukuran wajib diisi.',
            'sizes.*.integer' => 'Ukuran harus berupa angka.',
            'images.max' => sprintf('Maksimal %d gambar per produk.', self::MAX_IMAGES),
            'images.*.mimes' => 'Format gambar harus JPEG, PNG, atau WEBP.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 5MB.',
        ]);
    }

    /**
     * @param  array<int, int|string>  $sizes
     */
    private function syncSizes(Catalog $catalog, array $sizes): void
    {
        $unique = collect($sizes)
            ->map(fn (int|string $size): int => (int) $size)
            ->filter(fn (int $size): bool => $size > 0)
            ->unique()
            ->sort()
            ->values();

        $catalog->sizes()->delete();

        $payload = $unique
            ->map(fn (int $size): array => ['size_eu' => $size])
            ->all();

        if ($payload !== []) {
            $catalog->sizes()->createMany($payload);
        }
    }

    /**
     * @param  array<int, UploadedFile>  $images
     */
    private function appendImages(Catalog $catalog, array $images): void
    {
        if ($images === []) {
            return;
        }

        $existing = (int) $catalog->images()->count();

        foreach (array_values($images) as $index => $image) {
            if (($existing + $index) >= self::MAX_IMAGES) {
                break;
            }

            $path = $this->storeOptimizedImage($image);

            $catalog->images()->create([
                'image_path' => $path,
                'position' => $existing + $index + 1,
            ]);
        }
    }

    private function reindexCatalogImages(Catalog $catalog): void
    {
        $images = $catalog->images()->orderBy('position')->orderBy('id')->get();

        foreach ($images as $index => $image) {
            $expectedPosition = $index + 1;

            if ((int) $image->position !== $expectedPosition) {
                $image->update(['position' => $expectedPosition]);
            }
        }
    }

    private function deleteStoredImage(?string $path): void
    {
        if (is_string($path) && $path !== '' && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storeOptimizedImage(UploadedFile $file): string
    {
        $optimized = $this->optimizeImageBinary($file);

        if ($optimized === null) {
            return $file->store('katalog', 'public');
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

        $raw = @file_get_contents($file->getRealPath());

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $source = @imagecreatefromstring($raw);

        if ($source === false) {
            return null;
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);

        if ($sourceWidth < 1 || $sourceHeight < 1) {
            imagedestroy($source);

            return null;
        }

        $maxWidth = 2200;
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight;

        if ($sourceWidth > $maxWidth) {
            $scale = $maxWidth / $sourceWidth;
            $targetWidth = $maxWidth;
            $targetHeight = max((int) round($sourceHeight * $scale), 1);
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($canvas === false) {
            imagedestroy($source);

            return null;
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $folder = 'katalog';
        $fileName = (string) Str::uuid();
        $binary = null;
        $path = null;

        if (function_exists('imagewebp')) {
            ob_start();
            imagewebp($canvas, null, 84);
            $binary = ob_get_clean();
            $path = sprintf('%s/%s.webp', $folder, $fileName);
        } else {
            $mime = strtolower((string) $file->getMimeType());

            if ($mime === 'image/png' && function_exists('imagepng')) {
                ob_start();
                imagepng($canvas, null, 6);
                $binary = ob_get_clean();
                $path = sprintf('%s/%s.png', $folder, $fileName);
            } elseif (function_exists('imagejpeg')) {
                ob_start();
                imagejpeg($canvas, null, 84);
                $binary = ob_get_clean();
                $path = sprintf('%s/%s.jpg', $folder, $fileName);
            }
        }

        imagedestroy($canvas);
        imagedestroy($source);

        if (! is_string($binary) || $binary === '' || ! is_string($path)) {
            return null;
        }

        return [
            'path' => $path,
            'binary' => $binary,
        ];
    }

    /**
     * @return array{slug: string, public_id: string}|null
     */
    private function parseRouteKey(string $value): ?array
    {
        $trimmed = trim($value);

        if ($trimmed === '') {
            return null;
        }

        if (! preg_match('/^(.*)-([0-9a-hjkmnp-tv-z]{26})$/i', $trimmed, $matches)) {
            return null;
        }

        $slug = trim((string) ($matches[1] ?? ''), '-');
        $publicId = strtolower((string) ($matches[2] ?? ''));

        if ($publicId === '') {
            return null;
        }

        return [
            'slug' => $slug === '' ? 'produk' : $slug,
            'public_id' => $publicId,
        ];
    }

    private function resolvePreorderMinDays(array $validated): ?int
    {
        if (($validated['status'] ?? '') !== 'po') {
            return null;
        }

        if (isset($validated['preorder_min_days'])) {
            return (int) $validated['preorder_min_days'];
        }

        return 14;
    }

    private function resolvePreorderMaxDays(array $validated): ?int
    {
        if (($validated['status'] ?? '') !== 'po') {
            return null;
        }

        if (isset($validated['preorder_max_days'])) {
            return (int) $validated['preorder_max_days'];
        }

        return 21;
    }

    private function normalizeNullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    private function toKey(string $value): string
    {
        $key = Str::of($value)->trim()->lower()->slug('')->value();

        return $key === '' ? 'lainnya' : $key;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializePublicCatalog(Catalog $catalog): array
    {
        $collectionLabel = trim($catalog->collection);

        return [
            'id' => $catalog->id,
            'route_key' => $catalog->route_key,
            'public_id' => $catalog->public_id,
            'slug' => $catalog->slug,
            'name' => $catalog->name,
            'collection' => $this->toKey($collectionLabel),
            'collectionLabel' => $collectionLabel,
            'description' => $catalog->description,
            'price' => (int) $catalog->price,
            'status' => $catalog->status,
            'sizes' => $catalog->sizes->pluck('size_eu')->map(fn (mixed $size): int => (int) $size)->values()->all(),
            'popularity' => (int) $catalog->popularity_score,
            'card_image_url' => $catalog->card_image_url,
            'image_url' => $catalog->primary_image_url,
            'preorder_min_days' => $catalog->preorder_min_days,
            'preorder_max_days' => $catalog->preorder_max_days,
            'created_at' => optional($catalog->created_at)?->toISOString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeDetailCatalog(Catalog $catalog): array
    {
        $base = $this->serializePublicCatalog($catalog);

        $images = $catalog->images
            ->sortBy('position')
            ->values()
            ->map(fn (CatalogImage $image): array => $this->serializeImage($image))
            ->all();

        if ($images === [] && $catalog->card_image_url !== null) {
            $images[] = [
                'id' => 0,
                'image_path' => (string) ($catalog->card_image_path ?? ''),
                'image_url' => $catalog->card_image_url,
                'position' => 1,
            ];
        }

        $base['images'] = $images;

        return $base;
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAdminCatalog(Catalog $catalog): array
    {
        return [
            'id' => $catalog->id,
            'shoe_model_id' => $catalog->shoe_model_id,
            'shoe_model_name' => $catalog->shoeModel?->name,
            'public_id' => $catalog->public_id,
            'route_key' => $catalog->route_key,
            'slug' => $catalog->slug,
            'name' => $catalog->name,
            'collection' => $catalog->collection,
            'card_image_path' => $catalog->card_image_path,
            'card_image_url' => $catalog->card_image_url,
            'description' => $catalog->description,
            'price' => (int) $catalog->price,
            'status' => $catalog->status,
            'preorder_min_days' => $catalog->preorder_min_days,
            'preorder_max_days' => $catalog->preorder_max_days,
            'discount_type' => $catalog->discount_type,
            'discount_value' => $catalog->discount_value,
            'discount_starts_at' => optional($catalog->discount_starts_at)?->toISOString(),
            'discount_ends_at' => optional($catalog->discount_ends_at)?->toISOString(),
            'popularity_score' => (int) $catalog->popularity_score,
            'is_active' => (bool) $catalog->is_active,
            'sort_order' => (int) $catalog->sort_order,
            'sizes' => $catalog->sizes->pluck('size_eu')->map(fn (mixed $size): int => (int) $size)->values()->all(),
            'images' => $catalog->images
                ->sortBy('position')
                ->values()
                ->map(fn (CatalogImage $image): array => $this->serializeImage($image))
                ->all(),
            'created_at' => optional($catalog->created_at)?->toISOString(),
            'updated_at' => optional($catalog->updated_at)?->toISOString(),
        ];
    }

    /**
     * @return array{id: int, image_path: string, image_url: string, position: int}
     */
    private function serializeImage(CatalogImage $image): array
    {
        return [
            'id' => $image->id,
            'image_path' => $image->image_path,
            'image_url' => $image->image_url,
            'position' => (int) $image->position,
        ];
    }
}
