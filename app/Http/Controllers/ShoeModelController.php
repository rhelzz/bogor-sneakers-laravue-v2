<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use App\Services\Shoe\ShoeModelSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShoeModelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ShoeModel/Index', [
            'shoeModels' => ShoeModel::withCount('variants')->get(),
        ]);
    }

    public function show(ShoeModel $shoeModel): Response
    {
        return Inertia::render('Admin/ShoeModel/Show', [
            'shoeModel' => $shoeModel->load('variants'),
        ]);
    }

    public function update(Request $request, ShoeModel $shoeModel): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'layer_labels' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        $shoeModel->update($validated);

        return back()->with('success', 'Model berhasil diperbarui');
    }

    public function sync(ShoeModelSyncService $syncService): RedirectResponse
    {
        $results = $syncService->syncFromFilesystem();

        return back()->with('success', "Sinkronisasi selesai. {$results['models_created']} model baru dan {$results['variants_created']} varian baru ditambahkan.");
    }
}
