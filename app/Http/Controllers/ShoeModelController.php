<?php

namespace App\Http\Controllers;

use App\Models\ShoeModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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

    /**
     * Update shoe model.
     * Use Route Model Binding for reliability.
     */
    public function update(Request $request, $shoeModelId): RedirectResponse
    {
        $shoeModel = ShoeModel::with('variants.svgs')->findOrFail($shoeModelId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $oldSlug = $shoeModel->slug;
        $newName = $validated['name'];
        $newSlug = Str::slug($newName);

        // 1. Validasi Keunikan Slug (Mencegah konflik database dan folder)
        if (ShoeModel::where('slug', $newSlug)->where('id', '!=', $shoeModelId)->exists()) {
            return redirect()->back()->withErrors(['name' => "Nama model sudah digunakan oleh model lain (Slug: {$newSlug})."]);
        }

        DB::beginTransaction();
        try {
            // 2. Rename folder fisik jika slug berubah
            if ($oldSlug !== $newSlug) {
                $oldPath = public_path("shoes-svg/{$oldSlug}");
                $newPath = public_path("shoes-svg/{$newSlug}");

                if (is_dir($oldPath)) {
                    // Pastikan folder tujuan tidak ada untuk menghindari error rename
                    if (is_dir($newPath)) {
                        throw new \Exception("Folder tujuan '{$newSlug}' sudah ada secara fisik. Silakan hapus folder tersebut secara manual atau gunakan nama lain.");
                    }

                    // Gunakan File::moveDirectory (rename). Jika gagal di Windows, coba Copy & Delete sebagai fallback.
                    if (!File::moveDirectory($oldPath, $newPath)) {
                        if (File::copyDirectory($oldPath, $newPath)) {
                            File::deleteDirectory($oldPath);
                        } else {
                            throw new \Exception("Gagal mengubah nama folder dari '{$oldSlug}' ke '{$newSlug}'. Pastikan tidak ada file di dalamnya yang sedang dibuka oleh aplikasi lain (seperti Image Viewer, Explorer, atau Code Editor).");
                        }
                    }

                    // 3. Update semua path SVG di database agar tetap sinkron
                    foreach ($shoeModel->variants as $variant) {
                        foreach ($variant->svgs as $svg) {
                            $svg->file_path = str_replace("shoes-svg/{$oldSlug}/", "shoes-svg/{$newSlug}/", $svg->file_path);
                            $svg->save();
                        }
                    }
                }
            }

            // 4. Update data model di database
            $shoeModel->name = $newName;
            $shoeModel->slug = $newSlug;
            $shoeModel->is_active = $validated['is_active'];
            $shoeModel->save();

            DB::commit();
            return redirect()->back()->with('success', 'Model sepatu berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ShoeModel Update Error: ' . $e->getMessage());
            
            // Kembalikan error agar modal di frontend tetap terbuka dan menampilkan pesan
            return redirect()->back()->withErrors(['name' => 'Gagal sistematis edit: ' . $e->getMessage()]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $shoeModel = ShoeModel::findOrFail($id);
        
        // Delete physical directory
        Storage::disk('public_path')->deleteDirectory("shoes-svg/{$shoeModel->slug}");
        
        $shoeModel->delete();
        return redirect()->back()->with('success', 'Model sepatu berhasil dihapus.');
    }
}
