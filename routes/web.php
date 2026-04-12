<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CarouselSlideController;
use App\Http\Controllers\GalleryKaryaController;
use App\Http\Controllers\TikTokFeedController;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/katalog', 'Katalog')->name('katalog');
Route::get('/katalog/{productId}', function (int $productId) {
    return Inertia::render('ProductDetail', [
        'productId' => $productId,
    ]);
})->whereNumber('productId')->name('katalog.show');
Route::inertia('/studio-custom', 'StudioCustom')->name('studio-custom');
Route::inertia('/tracking', 'Tracking')->name('tracking');

// Admin Carousel Routes
Route::prefix('admin')->group(function () {
    Route::get('/carousel-home', [CarouselSlideController::class, 'index'])->name('admin.carousel-home');
    Route::post('/carousel-home', [CarouselSlideController::class, 'store'])->name('admin.carousel-home.store');
    Route::put('/carousel-home/{carouselSlide}', [CarouselSlideController::class, 'update'])->name('admin.carousel-home.update');
    Route::delete('/carousel-home/{carouselSlide}', [CarouselSlideController::class, 'destroy'])->name('admin.carousel-home.destroy');

    Route::get('/galeri-karya', [GalleryKaryaController::class, 'index'])->name('admin.galeri-karya');
    Route::post('/galeri-karya/{gallerySlot}/image', [GalleryKaryaController::class, 'replaceImage'])->name('admin.galeri-karya.replace-image');

    Route::get('/tiktok-feed', [TikTokFeedController::class, 'index'])->name('admin.tiktok-feed');
    Route::post('/tiktok-feed', [TikTokFeedController::class, 'store'])->name('admin.tiktok-feed.store');
    Route::put('/tiktok-feed/{tiktokFeed}', [TikTokFeedController::class, 'update'])->name('admin.tiktok-feed.update');
    Route::delete('/tiktok-feed/{tiktokFeed}', [TikTokFeedController::class, 'destroy'])->name('admin.tiktok-feed.destroy');
});

// Public API Routes
Route::get('/api/carousel-slides', [CarouselSlideController::class, 'api'])->name('api.carousel-slides');
Route::get('/api/galeri-karya', [GalleryKaryaController::class, 'api'])->name('api.galeri-karya');
Route::get('/api/tiktok-feed', [TikTokFeedController::class, 'apiFeed'])->name('api.tiktok-feed');
Route::get('/api/tiktok-followers', [TikTokFeedController::class, 'apiFollowers'])->name('api.tiktok-followers');
