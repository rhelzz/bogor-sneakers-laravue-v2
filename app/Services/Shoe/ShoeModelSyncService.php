<?php

namespace App\Services\Shoe;

use App\Models\ShoeModel;
use App\Models\ShoeVariant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ShoeModelSyncService
{
    public function syncFromFilesystem(): array
    {
        $root = public_path('shoes-svg');

        if (!File::isDirectory($root)) {
            return ['status' => 'error', 'message' => 'Directory not found'];
        }

        $results = [
            'models_created' => 0,
            'variants_created' => 0,
            'errors' => [],
        ];

        $directories = File::directories($root);

        foreach ($directories as $dir) {
            $modelSlug = basename($dir);
            $modelName = $this->toLabel($modelSlug);

            $shoeModel = ShoeModel::firstOrCreate(
                ['slug' => $modelSlug],
                ['name' => $modelName]
            );

            if ($shoeModel->wasRecentlyCreated) {
                $results['models_created']++;
            }

            $variantDirs = File::directories($dir);

            foreach ($variantDirs as $vDir) {
                $variantName = basename($vDir);
                
                $files = File::files($vDir);
                $svgData = $this->parseSvgFiles($files);

                $fullPreview = 'shoes-svg/' . $modelSlug . '/' . $variantName . '/full.svg';

                $shoeVariant = ShoeVariant::updateOrCreate(
                    [
                        'shoe_model_id' => $shoeModel->id,
                        'name' => $variantName,
                    ],
                    [
                        'full_preview_path' => $fullPreview,
                        'svg_data' => $svgData,
                    ]
                );

                if ($shoeVariant->wasRecentlyCreated) {
                    $results['variants_created']++;
                }
            }
        }

        return $results;
    }

    private function parseSvgFiles(array $files): array
    {
        $data = [
            'base' => null,
            'pattern_base' => null,
            'aksen' => [],
            'pola' => [],
        ];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            $lower = strtolower($filename);

            if ($lower === '_base.svg') {
                $data['base'] = $filename;
            } elseif ($lower === '_pola base.svg') {
                $data['pattern_base'] = $filename;
            } elseif (preg_match('/_aksen\s*(\d+)/i', $filename, $matches)) {
                $data['aksen'][$matches[1]] = $filename;
            } elseif (preg_match('/_pola\s+aksen\s*(\d+)/i', $filename, $matches)) {
                $data['pola'][$matches[1]] = $filename;
            }
        }

        ksort($data['aksen']);
        ksort($data['pola']);

        return $data;
    }

    private function toLabel(string $value): string
    {
        return Str::of($value)->replace(['-', '_'], ' ')->title()->value();
    }
}
