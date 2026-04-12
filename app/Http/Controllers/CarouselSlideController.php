<?php

namespace App\Http\Controllers;

use App\Models\CarouselSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarouselSlideController extends Controller
{
    /**
     * Show the carousel admin page.
     */
    public function index()
    {
        $slides = CarouselSlide::orderBy('order', 'asc')->get();

        return Inertia::render('Admin/CarouselHome', [
            'slides' => $slides,
        ]);
    }

    /**
     * Store a new carousel slide.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png|max:5120', // 5MB
        ], [
            'image.required' => 'Image harus diunggah',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus JPEG atau PNG',
            'image.max' => 'Ukuran gambar maksimal 5MB',
        ]);

        // Get the next order value
        $nextOrder = (CarouselSlide::max('order') ?? -1) + 1;

        // Store image file
        $path = $request->file('image')->store('carousel', 'public');

        // Create carousel slide
        $slide = CarouselSlide::create([
            'image_path' => $path,
            'order' => $nextOrder,
            'is_active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Slide carousel berhasil ditambahkan!',
                'slide' => [
                    'id' => $slide->id,
                    'image_path' => $slide->image_path,
                    'image_url' => $slide->image_url,
                    'is_active' => $slide->is_active,
                    'order' => $slide->order,
                    'created_at' => $slide->created_at,
                    'updated_at' => $slide->updated_at,
                ],
            ], 201);
        }

        return back()->with('message', 'Slide carousel berhasil ditambahkan!');
    }

    /**
     * Update a carousel slide.
     */
    public function update(Request $request, CarouselSlide $carouselSlide)
    {
        // Validate input
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        // Update slide
        $carouselSlide->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Slide carousel berhasil diperbarui!',
                'slide' => [
                    'id' => $carouselSlide->id,
                    'image_path' => $carouselSlide->image_path,
                    'image_url' => $carouselSlide->image_url,
                    'is_active' => $carouselSlide->is_active,
                    'order' => $carouselSlide->order,
                    'created_at' => $carouselSlide->created_at,
                    'updated_at' => $carouselSlide->updated_at,
                ],
            ]);
        }

        return back()->with('message', 'Slide carousel berhasil diperbarui!');
    }

    /**
     * Delete a carousel slide.
     */
    public function destroy(CarouselSlide $carouselSlide)
    {
        $deletedSlideId = $carouselSlide->id;

        // Delete image file
        if ($carouselSlide->image_path && Storage::disk('public')->exists($carouselSlide->image_path)) {
            Storage::disk('public')->delete($carouselSlide->image_path);
        }

        // Delete the slide record
        $carouselSlide->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Slide carousel berhasil dihapus!',
                'id' => $deletedSlideId,
            ]);
        }

        return back()->with('message', 'Slide carousel berhasil dihapus!');
    }

    /**
     * Get carousel slides as JSON (for frontend API).
     */
    public function api()
    {
        $slides = CarouselSlide::active()
            ->whereNotNull('image_path')
            ->where('image_path', '!=', '')
            ->get()
            ->map(function ($slide) {
            return [
                'id' => $slide->id,
                'image_url' => $slide->image_url,
                'is_active' => $slide->is_active,
            ];
        });

        return response()->json($slides);
    }
}
