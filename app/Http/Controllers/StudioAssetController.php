<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use App\Models\ShoeVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudioAssetController extends Controller
{
    private const CACHE_KEY = 'studio-custom:asset-catalog:v3';

    public function catalog(Request $request): JsonResponse
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::CACHE_KEY);
        }

        $catalog = Cache::remember(self::CACHE_KEY, now()->addMinutes(60), function (): array {
            return [
                'generated_at' => now()->toIso8601String(),
                'folders' => $this->getDatabaseCatalog(),
            ];
        });

        return response()->json($catalog);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDatabaseCatalog(): array
    {
        return ShoeModel::with(['variants' => function ($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->get()
            ->map(function (ShoeModel $model) {
                return [
                    'key' => $model->slug,
                    'label' => $model->name,
                    'model_count' => $model->variants->count(),
                    'models' => $model->variants->map(function (ShoeVariant $variant) {
                        $svg = $variant->svg_data;
                        
                        // Map database structure to existing frontend expectation
                        return [
                            'id' => $variant->id,
                            'label' => $this->formatVariantLabel($variant->name),
                            'preview_base_file' => $svg['base'] ?? null,
                            'main_file' => basename($variant->full_preview_path),
                            'pattern_base_file' => $svg['pattern_base'] ?? null,
                            'layers' => $this->formatLayers($svg['aksen'] ?? []),
                            'pattern_layers' => $this->formatLayers($svg['pola'] ?? []),
                        ];
                    })->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function formatVariantLabel(string $name): string
    {
        if (ctype_digit($name)) {
            return 'M' . $name;
        }
        return $name;
    }

    private function formatLayers(array $aksen): array
    {
        $layers = [];
        foreach ($aksen as $id => $file) {
            $layers[] = [
                'id' => (int) $id,
                'file' => $file,
            ];
        }
        return $layers;
    }
}
