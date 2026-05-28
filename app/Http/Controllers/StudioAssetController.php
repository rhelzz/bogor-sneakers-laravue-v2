<?php

namespace App\Http\Controllers;

use App\Services\Studio\StudioCatalogService;
use App\Services\Studio\StudioV2CatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudioAssetController extends Controller
{
    private const CACHE_KEY = 'studio-custom:asset-catalog:v2';

    public function __construct(
        private readonly StudioCatalogService $catalogService,
        private readonly StudioV2CatalogService $v2CatalogService,
    ) {}

    private const V2_CACHE_KEY = 'studio-custom:asset-catalog-v2:v1';

    public function catalog(Request $request): JsonResponse
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::CACHE_KEY);
        }

        $catalog = Cache::remember(self::CACHE_KEY, now()->addMinutes(15), function (): array {
            return [
                'generated_at' => now()->toIso8601String(),
                'folders' => $this->catalogService->generateCatalog(),
            ];
        });

        return response()->json($catalog);
    }

    public function catalogV2(Request $request): JsonResponse
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::V2_CACHE_KEY);
        }

        $catalog = Cache::remember(self::V2_CACHE_KEY, now()->addMinutes(15), function (): array {
            return [
                'generated_at' => now()->toIso8601String(),
                'shoe_types' => $this->v2CatalogService->generateCatalog(),
            ];
        });

        return response()->json($catalog);
    }
}
