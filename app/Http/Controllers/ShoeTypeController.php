<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use App\Models\ShoeType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShoeTypeController extends Controller
{
    public function store(Request $request, $shoeModelId): RedirectResponse
    {
        $shoeModel = ShoeModel::findOrFail($shoeModelId);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $shoeModel->types()->create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back()->with('success', 'Jenis sepatu berhasil ditambahkan.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $shoeType = ShoeType::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $shoeType->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return redirect()->back()->with('success', 'Jenis sepatu berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $shoeType = ShoeType::with('variants.model')->findOrFail($id);
        
        // Cleanup physical files for all variants in this type
        foreach ($shoeType->variants as $variant) {
            $modelSlug = $variant->model->slug;
            $variantId = $variant->id;
            \Illuminate\Support\Facades\Storage::disk('public_path')->deleteDirectory("shoes-svg/{$modelSlug}/{$variantId}");
        }

        $shoeType->delete();
        return redirect()->back()->with('success', 'Jenis sepatu berhasil dihapus beserta seluruh varian dan asetnya.');
    }
}
