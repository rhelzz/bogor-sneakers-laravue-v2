<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudioAssetController extends Controller
{
    private const CACHE_KEY = 'studio-custom:asset-catalog:v2';

    public function catalog(Request $request): JsonResponse
    {
        if ($request->boolean('refresh')) {
            Cache::forget(self::CACHE_KEY);
        }

        /**
         * @var array<string, mixed> $catalog
         */
        $catalog = Cache::remember(self::CACHE_KEY, now()->addMinutes(15), function (): array {
            return [
                'generated_at' => now()->toIso8601String(),
                'folders' => $this->scanFolders(),
            ];
        });

        return response()->json($catalog);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function scanFolders(): array
    {
        $root = public_path('shoes-svg');

        if (! File::isDirectory($root)) {
            return [];
        }

        return collect(File::directories($root))
            ->sort(static fn (string $a, string $b): int => strnatcasecmp($a, $b))
            ->map(function (string $folderPath): array {
                $folderKey = basename($folderPath);

                $models = collect(File::directories($folderPath))
                    ->sort(static fn (string $a, string $b): int => strnatcasecmp($a, $b))
                    ->map(function (string $modelPath): ?array {
                        $modelRaw = basename($modelPath);
                        $modelId = $modelRaw;
                        $modelLabel = $modelRaw;

                        if (ctype_digit($modelRaw)) {
                            $modelId = (int) $modelRaw;
                            $modelLabel = sprintf('M%d', $modelId);
                        } elseif (preg_match('/M(\d+)/i', $modelRaw, $idMatches)) {
                            $modelId = (int) $idMatches[1];
                            $modelLabel = $modelRaw;
                        }

                        $files = collect(File::files($modelPath))
                            ->map(static fn (\SplFileInfo $file): string => $file->getFilename())
                            ->sort(static fn (string $a, string $b): int => strnatcasecmp($a, $b))
                            ->values();

                        if ($files->isEmpty()) {
                            return null;
                        }

                        $previewBaseFile = $files
                            ->first(static fn (string $file): bool => str_contains(Str::lower($file), '_base.svg') && ! str_contains(Str::lower($file), '_pola '));

                        $mainFile = $files
                            ->first(static fn (string $file): bool => ! str_contains(Str::lower($file), '_aksen') && ! str_contains(Str::lower($file), '_pola ') && ! str_contains(Str::lower($file), '_base.svg'));

                        $patternBaseFile = $files
                            ->first(static fn (string $file): bool => str_contains(Str::lower($file), '_pola base.svg'));

                        $layers = $this->extractLayerFiles($files->all());
                        $patternLayers = $this->extractPatternLayerFiles($files->all());

                        return [
                            'id' => $modelId,
                            'label' => $modelLabel,
                            'preview_base_file' => $previewBaseFile,
                            'main_file' => $mainFile,
                            'pattern_base_file' => $patternBaseFile,
                            'layers' => $layers,
                            'pattern_layers' => $patternLayers,
                        ];
                    })
                    ->filter(static fn (?array $model): bool => $model !== null)
                    ->values()
                    ->all();

                return [
                    'key' => $folderKey,
                    'label' => $this->toFolderLabel($folderKey),
                    'model_count' => count($models),
                    'models' => $models,
                ];
            })
            ->filter(static fn (array $folder): bool => $folder['models'] !== [])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $files
     * @return array<int, array{id: int, file: string}>
     */
    private function extractLayerFiles(array $files): array
    {
        $layerMap = [];

        foreach ($files as $file) {
            $lower = Str::lower($file);

            if (str_contains($lower, '_pola ')) {
                continue;
            }

            if (! preg_match('/_aksen\s*(\d+)(?:\s*\(\d+\))?\.svg$/i', $file, $matches)) {
                continue;
            }

            $id = (int) $matches[1];

            if (! isset($layerMap[$id])) {
                $layerMap[$id] = $file;
            }
        }

        ksort($layerMap);

        return collect($layerMap)
            ->map(static fn (string $file, int $id): array => [
                'id' => $id,
                'file' => $file,
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $files
     * @return array<int, array{id: int, file: string}>
     */
    private function extractPatternLayerFiles(array $files): array
    {
        $layerMap = [];

        foreach ($files as $file) {
            if (! preg_match('/_pola\s+aksen\s*(\d+)(?:\s*\(\d+\))?(?:\.png)?\.svg$/i', $file, $matches)) {
                continue;
            }

            $id = (int) $matches[1];

            if (! isset($layerMap[$id])) {
                $layerMap[$id] = $file;
            }
        }

        ksort($layerMap);

        return collect($layerMap)
            ->map(static fn (string $file, int $id): array => [
                'id' => $id,
                'file' => $file,
            ])
            ->values()
            ->all();
    }

    private function toFolderLabel(string $value): string
    {
        $normalized = str_replace(['-', '_'], ' ', $value);

        return Str::of($normalized)
            ->title()
            ->value();
    }
}
