<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use App\Models\ShoeVariant;
use App\Models\ShoeVariantSvg;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

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
            $variantId = $shoeVariant->id;

            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                // Store in public/shoes-svg/{model-slug}/{variant-id}/{filename}
                $path = $file->storeAs(
                    "shoes-svg/{$modelSlug}/{$variantId}",
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
        $variantId = $shoeVariant->id;
        Storage::disk('public_path')->deleteDirectory("shoes-svg/{$modelSlug}/{$variantId}");
        
        $shoeVariant->delete();
        return redirect()->back()->with('success', 'Varian berhasil dihapus.');
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
