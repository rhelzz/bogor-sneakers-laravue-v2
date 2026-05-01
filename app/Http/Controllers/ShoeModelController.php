<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ShoeModelController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ShoeModel/Index', [
            'models' => ShoeModel::withCount('variants')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        ShoeModel::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Model sepatu berhasil dibuat.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $shoeModel = ShoeModel::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $shoeModel->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Model sepatu berhasil diperbarui.');
    }

    public function destroy($id): RedirectResponse
    {
        $shoeModel = ShoeModel::findOrFail($id);
        $shoeModel->delete();
        return redirect()->back()->with('success', 'Model sepatu berhasil dihapus.');
    }
}
