<?php

namespace App\Services\Studio;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudioV2CatalogService
{
    /**
     * Scan all *-studio-v2 directories and return catalog based on actual files on disk.
     * Each model's accent_slots and pola_slots are the exact slot numbers detected,
     * so gaps (e.g. m8 slipon has no aksen12) are automatically reflected.
     */
    public function generateCatalog(): array
    {
        $root = public_path('shoes-svg');

        if (! File::isDirectory($root)) {
            return [];
        }

        return collect(File::directories($root))
            ->filter(fn (string $path): bool => str_ends_with(basename($path), '-studio-v2'))
            ->sort()
            ->map(fn (string $path): array => $this->scanShoeType($path))
            ->filter(fn (array $type): bool => ! empty($type['models']))
            ->values()
            ->all();
    }

    private function scanShoeType(string $dirPath): array
    {
        $dirName = basename($dirPath);

        $filenames = collect(File::files($dirPath))
            ->map(fn (\SplFileInfo $f): string => $f->getFilename());

        $label = Str::of($dirName)
            ->replaceLast('-studio-v2', '')
            ->replace(['-', '_'], ' ')
            ->title()
            ->value();

        return [
            'id' => $dirName,
            'label' => $label,
            'base_path' => "/shoes-svg/{$dirName}",
            'models' => $this->extractModels($filenames),
        ];
    }

    /**
     * Find all model numbers via m{N}_base.svg, then for each model
     * collect exact accent slot numbers and pola slot numbers from disk.
     */
    private function extractModels(Collection $filenames): array
    {
        $modelNumbers = $filenames
            ->filter(fn (string $f): bool => (bool) preg_match('/^m(\d+)_base\.svg$/i', $f))
            ->map(function (string $f): int {
                preg_match('/^m(\d+)_base\.svg$/i', $f, $matches);

                return (int) $matches[1];
            })
            ->sort()
            ->values();

        return $modelNumbers
            ->mapWithKeys(function (int $model) use ($filenames): array {
                $accentSlots = $this->extractSlotNumbers(
                    $filenames,
                    "/^m{$model}_aksen(\\d+)\\.svg\$/i"
                );

                $polaSlots = $this->extractSlotNumbers(
                    $filenames,
                    "/^m{$model}_pola_aksen(\\d+)\\.svg\$/i"
                );

                return [$model => [
                    'accent_slots' => $accentSlots,
                    'pola_slots' => $polaSlots,
                ]];
            })
            ->all();
    }

    private function extractSlotNumbers(Collection $filenames, string $pattern): array
    {
        return $filenames
            ->filter(fn (string $f): bool => (bool) preg_match($pattern, $f))
            ->map(function (string $f) use ($pattern): int {
                preg_match($pattern, $f, $matches);

                return (int) $matches[1];
            })
            ->sort()
            ->values()
            ->all();
    }
}
