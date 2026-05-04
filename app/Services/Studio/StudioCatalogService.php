<?php

namespace App\Services\Studio;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class StudioCatalogService
{
    /**
     * Scan the public/shoes-svg directory to generate a catalog.
     * 
     * @return array
     */
    public function generateCatalog(): array
    {
        $root = public_path('shoes-svg');

        if (!File::isDirectory($root)) {
            return [];
        }

        return collect(File::directories($root))
            ->sort(static fn (string $a, string $b): int => strnatcasecmp($a, $b))
            ->map(function (string $folderPath): array {
                $folderKey = basename($folderPath);
                $models = $this->scanModelsInFolder($folderPath);

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

    private function scanModelsInFolder(string $folderPath): array
    {
        return collect(File::directories($folderPath))
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

                return [
                    'id' => $modelId,
                    'label' => $modelLabel,
                    'preview_base_file' => $this->findFile($files, '_base.svg', ['_pola ']),
                    'main_file' => $this->findMainFile($files),
                    'pattern_base_file' => $this->findFile($files, '_pola base.svg'),
                    'layers' => $this->extractLayers($files, '/_aksen\s*(\d+)(?:\s*\(\d+\))?\.svg$/i', ['_pola ']),
                    'pattern_layers' => $this->extractLayers($files, '/_pola\s+aksen\s*(\d+)(?:\s*\(\d+\))?(?:\.png)?\.svg$/i'),
                ];
            })
            ->filter(static fn (?array $model): bool => $model !== null)
            ->values()
            ->all();
    }

    private function findFile(Collection $files, string $pattern, array $excludes = []): ?string
    {
        return $files->first(function (string $file) use ($pattern, $excludes): bool {
            $lower = Str::lower($file);
            foreach ($excludes as $exclude) {
                if (str_contains($lower, Str::lower($exclude))) return false;
            }
            return str_contains($lower, Str::lower($pattern));
        });
    }

    private function findMainFile(Collection $files): ?string
    {
        return $files->first(function (string $file): bool {
            $lower = Str::lower($file);
            return !str_contains($lower, '_aksen') && 
                   !str_contains($lower, '_pola ') && 
                   !str_contains($lower, '_base.svg');
        });
    }

    private function extractLayers(Collection $files, string $regex, array $excludes = []): array
    {
        $layerMap = [];
        foreach ($files as $file) {
            $lower = Str::lower($file);
            $skip = false;
            foreach ($excludes as $exclude) {
                if (str_contains($lower, Str::lower($exclude))) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) continue;

            if (preg_match($regex, $file, $matches)) {
                $id = (int) $matches[1];
                if (!isset($layerMap[$id])) $layerMap[$id] = $file;
            }
        }
        ksort($layerMap);

        return collect($layerMap)
            ->map(static fn (string $file, int $id): array => ['id' => $id, 'file' => $file])
            ->values()
            ->all();
    }

    private function toFolderLabel(string $value): string
    {
        return Str::of(str_replace(['-', '_'], ' ', $value))->title()->value();
    }
}
