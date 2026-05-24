<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use App\Models\ShoeVariant;
use App\Models\ShoeVariantSvg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

use Illuminate\Support\Str;

class ShoeVariantController extends Controller
{
    public function index($shoeModelId): Response
    {
        $shoeModel = ShoeModel::with(['types'])->findOrFail($shoeModelId);
        
        return Inertia::render('Admin/ShoeVariant/Index', [
            'shoeModel' => $shoeModel,
            'variants' => $shoeModel->variants()->with(['svgs', 'type'])->get(),
            'types' => $shoeModel->types,
        ]);
    }

    public function store(Request $request, $shoeModelId): RedirectResponse
    {
        $shoeModel = ShoeModel::findOrFail($shoeModelId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shoe_type_id' => 'nullable|exists:shoe_types,id',
        ]);

        $shoeModel->variants()->create([
            'name' => $validated['name'],
            'shoe_type_id' => $validated['shoe_type_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Varian berhasil dibuat.');
    }

    public function update(Request $request, $shoeVariantId): RedirectResponse
    {
        $shoeVariant = ShoeVariant::with(['model', 'svgs'])->findOrFail($shoeVariantId);
        $oldNameSlug = \Illuminate\Support\Str::slug($shoeVariant->name);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'shoe_type_id' => 'nullable|exists:shoe_types,id',
        ]);

        $newNameSlug = \Illuminate\Support\Str::slug($validated['name']);
        $modelSlug = $shoeVariant->model->slug;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($oldNameSlug !== $newNameSlug) {
                $pathByOldSlug = public_path("shoes-svg/{$modelSlug}/{$oldNameSlug}");
                $pathById = public_path("shoes-svg/{$modelSlug}/{$shoeVariant->id}");
                $newPath = public_path("shoes-svg/{$modelSlug}/{$newNameSlug}");

                // Find which directory exists (could be by slug or by ID if it was created before refactor)
                $sourcePath = null;
                $oldPart = null;

                if (is_dir($pathByOldSlug)) {
                    $sourcePath = $pathByOldSlug;
                    $oldPart = $oldNameSlug;
                } elseif (is_dir($pathById)) {
                    $sourcePath = $pathById;
                    $oldPart = $shoeVariant->id;
                }

                if ($sourcePath && $sourcePath !== $newPath) {
                    if (!\Illuminate\Support\Facades\File::moveDirectory($sourcePath, $newPath)) {
                        throw new \Exception("Gagal mengubah nama direktori varian ke {$newNameSlug}");
                    }

                    // Update SVG paths in database for this variant
                    foreach ($shoeVariant->svgs as $svg) {
                        $newFilePath = str_replace("shoes-svg/{$modelSlug}/{$oldPart}/", "shoes-svg/{$modelSlug}/{$newNameSlug}/", $svg->file_path);
                        $svg->update(['file_path' => $newFilePath]);
                    }
                }
            }

            $shoeVariant->update([
                'name' => $validated['name'],
                'shoe_type_id' => $validated['shoe_type_id'] ?? null,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'Varian berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('ShoeVariant update failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['name' => 'Gagal memperbarui varian: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk upload SVGs to a variant.
     */
    public function uploadSvgs(Request $request, $shoeVariantId): RedirectResponse
    {
        $shoeVariant = ShoeVariant::findOrFail($shoeVariantId);
        $request->validate([
            'files.*' => 'required|file|mimes:svg,xml', // Some SVGs might be detected as xml
        ]);

        if ($request->hasFile('files')) {
            $modelSlug = $shoeVariant->model->slug;
            $variantSlug = Str::slug($shoeVariant->name);

            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                // Store in public/shoes-svg/{model-slug}/{variant-name}/{filename}
                $path = $file->storeAs(
                    "shoes-svg/{$modelSlug}/{$variantSlug}",
                    $fileName,
                    'public_path'
                );

                $shoeVariant->svgs()->create([
                    'file_name' => $fileName,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Aset SVG berhasil diupload secara bulk.');
    }

    public function destroy($shoeVariantId): RedirectResponse
    {
        $shoeVariant = ShoeVariant::with('model')->findOrFail($shoeVariantId);
        
        // Delete physical directory
        $modelSlug = $shoeVariant->model->slug;
        $variantSlug = Str::slug($shoeVariant->name);
        Storage::disk('public_path')->deleteDirectory("shoes-svg/{$modelSlug}/{$variantSlug}");
        
        $shoeVariant->delete();
        return redirect()->back()->with('success', 'Varian berhasil dihapus.');
    }

    public function updateStudioConfig(Request $request, ShoeVariant $shoeVariant): RedirectResponse
    {
        $validated = $request->validate([
            'studio_config.preview_zone.x'      => 'required|numeric',
            'studio_config.preview_zone.y'      => 'required|numeric',
            'studio_config.preview_zone.width'  => 'required|numeric|min:1',
            'studio_config.preview_zone.height' => 'required|numeric|min:1',
        ]);

        $existingConfig = $shoeVariant->studio_config ?? [];
        $merged = array_merge($existingConfig, ['preview_zone' => $validated['studio_config']['preview_zone']]);

        $shoeVariant->update(['studio_config' => $merged]);

        Cache::forget('studio-custom:asset-catalog:v2');

        return redirect()->back()->with('success', 'Studio config berhasil disimpan.');
    }

    /**
     * Move all variants of a model from one type (or null) to another type.
     */
    public function moveAllToType(Request $request, $shoeModelId): RedirectResponse
    {
        $request->validate([
            'from_type_id' => 'nullable|exists:shoe_types,id',
            'to_type_id' => 'required|exists:shoe_types,id',
        ]);

        ShoeVariant::where('shoe_model_id', $shoeModelId)
            ->where('shoe_type_id', $request->from_type_id)
            ->update(['shoe_type_id' => $request->to_type_id]);

        return redirect()->back()->with('success', 'Semua varian berhasil dipindahkan.');
    }
}
