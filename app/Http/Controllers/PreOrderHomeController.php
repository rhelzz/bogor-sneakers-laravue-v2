<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\HomePreorder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class PreOrderHomeController extends Controller
{
    private const VISIBLE_LIMIT = 5;

    public function index(): Response
    {
        HomePreorder::expireVisibleItems();

        $assignments = HomePreorder::query()
            ->with(['catalog.images', 'catalog.sizes'])
            ->ordered()
            ->get();

        $catalogOptions = Catalog::query()
            ->active()
            ->where('status', 'po')
            ->with(['images', 'sizes', 'homePreorder'])
            ->ordered()
            ->get();

        return Inertia::render('Admin/PreOrderHome', [
            'assignments' => $assignments
                ->map(fn (HomePreorder $assignment): array => $this->serializeAdminAssignment($assignment))
                ->values()
                ->all(),
            'catalogOptions' => $catalogOptions
                ->map(function (Catalog $catalog): array {
                    return $this->serializeCatalogOption(
                        $catalog,
                        $catalog->homePreorder instanceof HomePreorder,
                        $catalog->homePreorder?->id,
                    );
                })
                ->values()
                ->all(),
            'visibleCount' => HomePreorder::query()->visibleForHome()->count(),
            'visibleLimit' => self::VISIBLE_LIMIT,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatePayload($request);

        $assignment = DB::transaction(function () use ($validated): HomePreorder {
            $urutan = isset($validated['urutan'])
                ? (int) $validated['urutan']
                : (int) ((HomePreorder::max('urutan') ?? -1) + 1);

            return HomePreorder::create([
                'catalog_id' => (int) $validated['catalog_id'],
                'status' => (string) $validated['status'],
                'is_visible' => (bool) ($validated['is_visible'] ?? false),
                'max_slots' => (int) $validated['max_slots'],
                'filled_slots' => (int) $validated['filled_slots'],
                'countdown_ends_at' => Carbon::parse((string) $validated['countdown_ends_at'])->utc(),
                'batch_label' => $this->normalizeNullableText($validated['batch_label'] ?? null),
                'urutan' => $urutan,
            ]);
        });

        $assignment->load(['catalog.images', 'catalog.sizes']);

        return response()->json([
            'message' => 'Pre-order Home berhasil ditambahkan.',
            'assignment' => $this->serializeAdminAssignment($assignment),
            'visible_count' => HomePreorder::query()->visibleForHome()->count(),
        ], 201);
    }

    public function update(Request $request, HomePreorder $homePreorder): JsonResponse
    {
        $validated = $this->validatePayload($request, $homePreorder);

        $updated = DB::transaction(function () use ($homePreorder, $validated): HomePreorder {
            $homePreorder->update([
                'catalog_id' => (int) $validated['catalog_id'],
                'status' => (string) $validated['status'],
                'is_visible' => (bool) ($validated['is_visible'] ?? false),
                'max_slots' => (int) $validated['max_slots'],
                'filled_slots' => (int) $validated['filled_slots'],
                'countdown_ends_at' => Carbon::parse((string) $validated['countdown_ends_at'])->utc(),
                'batch_label' => $this->normalizeNullableText($validated['batch_label'] ?? null),
                'urutan' => isset($validated['urutan'])
                    ? (int) $validated['urutan']
                    : (int) $homePreorder->urutan,
            ]);

            return $homePreorder->fresh(['catalog.images', 'catalog.sizes']);
        });

        return response()->json([
            'message' => 'Pre-order Home berhasil diperbarui.',
            'assignment' => $this->serializeAdminAssignment($updated),
            'visible_count' => HomePreorder::query()->visibleForHome()->count(),
        ]);
    }

    public function destroy(HomePreorder $homePreorder): JsonResponse
    {
        $deletedId = $homePreorder->id;

        HomePreorder::destroy($deletedId);

        return response()->json([
            'message' => 'Pre-order Home berhasil dihapus.',
            'id' => $deletedId,
            'visible_count' => HomePreorder::query()->visibleForHome()->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request, ?HomePreorder $homePreorder = null): array
    {
        $catalogUniqueRule = Rule::unique('home_preorders', 'catalog_id');

        if ($homePreorder instanceof HomePreorder) {
            $catalogUniqueRule = $catalogUniqueRule->ignore($homePreorder->id);
        }

        $validator = Validator::make($request->all(), [
            'catalog_id' => ['required', 'integer', Rule::exists('catalogs', 'id'), $catalogUniqueRule],
            'status' => ['required', Rule::in(['buka', 'hampir_habis', 'habis'])],
            'is_visible' => ['nullable', 'boolean'],
            'max_slots' => ['required', 'integer', 'min:1', 'max:9999'],
            'filled_slots' => ['required', 'integer', 'min:0', 'max:9999'],
            'countdown_ends_at' => ['required', 'date'],
            'batch_label' => ['nullable', 'string', 'max:40'],
            'urutan' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ], [
            'catalog_id.required' => 'Produk katalog wajib dipilih.',
            'catalog_id.unique' => 'Produk ini sudah dipakai pada Pre-order Home.',
            'status.required' => 'Status pre-order wajib dipilih.',
            'max_slots.required' => 'Maksimal slot wajib diisi.',
            'max_slots.min' => 'Maksimal slot minimal 1.',
            'filled_slots.required' => 'Slot terisi wajib diisi.',
            'filled_slots.min' => 'Slot terisi minimal 0.',
            'countdown_ends_at.required' => 'Waktu tutup PO wajib diisi.',
            'countdown_ends_at.date' => 'Format waktu tutup PO tidak valid.',
        ]);

        $validator->after(function ($validator) use ($request, $homePreorder): void {
            $catalog = Catalog::query()
                ->whereKey((int) $request->input('catalog_id'))
                ->first();

            if (! $catalog instanceof Catalog || $catalog->status !== 'po' || ! $catalog->is_active) {
                $validator->errors()->add('catalog_id', 'Produk harus berstatus PO dan aktif.');
            }

            $maxSlots = (int) $request->input('max_slots', 0);
            $filledSlots = (int) $request->input('filled_slots', 0);

            if ($filledSlots > $maxSlots) {
                $validator->errors()->add('filled_slots', 'Slot terisi tidak boleh melebihi maksimal slot.');
            }

            if (! $validator->errors()->has('countdown_ends_at')) {
                try {
                    $endsAt = Carbon::parse((string) $request->input('countdown_ends_at'));

                    if ($endsAt->lessThanOrEqualTo(now())) {
                        $validator->errors()->add('countdown_ends_at', 'Waktu tutup PO harus lebih besar dari waktu saat ini.');
                    }
                } catch (Throwable) {
                    $validator->errors()->add('countdown_ends_at', 'Format waktu tutup PO tidak valid.');
                }
            }

            $visibleInput = filter_var($request->input('is_visible', false), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            $shouldBeVisible = $visibleInput === true;

            if (! $shouldBeVisible) {
                return;
            }

            HomePreorder::expireVisibleItems();

            $visibleCount = HomePreorder::query()
                ->visibleForHome()
                ->when(
                    $homePreorder instanceof HomePreorder,
                    fn (Builder $query): Builder => $query->whereKeyNot($homePreorder->id),
                )
                ->count();

            if ($visibleCount >= self::VISIBLE_LIMIT) {
                $validator->errors()->add('is_visible', sprintf('Maksimal %d produk pre-order dapat ditampilkan.', self::VISIBLE_LIMIT));
            }
        });

        return $validator->validate();
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAdminAssignment(HomePreorder $assignment): array
    {
        $catalog = $assignment->catalog;

        $maxSlots = max((int) $assignment->max_slots, 1);
        $filledSlots = min(max((int) $assignment->filled_slots, 0), $maxSlots);
        $remainingSlots = max($maxSlots - $filledSlots, 0);
        $progress = (int) round(($filledSlots / $maxSlots) * 100);

        return [
            'id' => $assignment->id,
            'catalog_id' => (int) $assignment->catalog_id,
            'status' => $assignment->status,
            'status_label' => $this->toStatusLabel((string) $assignment->status),
            'is_visible' => (bool) $assignment->is_visible,
            'max_slots' => $maxSlots,
            'filled_slots' => $filledSlots,
            'remaining_slots' => $remainingSlots,
            'progress' => $progress,
            'countdown_ends_at' => optional($assignment->countdown_ends_at)?->toISOString(),
            'batch_label' => $this->resolveBatchLabel($assignment),
            'urutan' => (int) $assignment->urutan,
            'catalog' => $catalog instanceof Catalog
                ? [
                    'id' => $catalog->id,
                    'name' => $catalog->name,
                    'collection' => $catalog->collection,
                    'size_range' => $this->formatSizeRange($catalog),
                    'image_url' => $catalog->primary_image_url,
                    'route_key' => $catalog->route_key,
                ]
                : null,
            'created_at' => optional($assignment->created_at)?->toISOString(),
            'updated_at' => optional($assignment->updated_at)?->toISOString(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeCatalogOption(Catalog $catalog, bool $isAssigned, ?int $assignmentId): array
    {
        return [
            'id' => $catalog->id,
            'name' => $catalog->name,
            'collection' => $catalog->collection,
            'size_range' => $this->formatSizeRange($catalog),
            'image_url' => $catalog->primary_image_url,
            'is_assigned' => $isAssigned,
            'assignment_id' => $assignmentId,
        ];
    }

    private function formatSizeRange(Catalog $catalog): string
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

    private function toStatusLabel(string $status): string
    {
        return match ($status) {
            'buka' => 'BUKA',
            'hampir_habis' => 'HAMPIR HABIS',
            default => 'HABIS',
        };
    }

    private function resolveBatchLabel(HomePreorder $assignment): string
    {
        $label = trim((string) ($assignment->batch_label ?? ''));

        if ($label !== '') {
            return $label;
        }

        return sprintf('#%02d', ((int) $assignment->urutan));
    }

    private function normalizeNullableText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim(Str::of($value)->value());

        return $trimmed === '' ? null : $trimmed;
    }
}
