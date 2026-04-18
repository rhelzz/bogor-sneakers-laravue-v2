<?php

use App\Http\Controllers\CarouselSlideController;
use App\Http\Controllers\GalleryKaryaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PreOrderHomeController;
use App\Http\Controllers\StudioAssetController;
use App\Http\Controllers\TikTokFeedController;
use App\Http\Controllers\WhatsAppAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/katalog/{catalog}', [KatalogController::class, 'show'])
    ->where('catalog', '^[a-z0-9-]+-[0-9a-hjkmnp-tv-z]{26}$')
    ->name('katalog.show');
Route::inertia('/studio-custom', 'StudioCustom')->name('studio-custom');
Route::get('/api/studio-custom/catalog', [StudioAssetController::class, 'catalog'])->name('studio-custom.catalog');
Route::inertia('/tracking', 'Tracking')->name('tracking');

// Admin Carousel Routes
Route::prefix('admin')->group(function () {
    Route::get('/carousel-home', [CarouselSlideController::class, 'index'])->name('admin.carousel-home');
    Route::post('/carousel-home', [CarouselSlideController::class, 'store'])->name('admin.carousel-home.store');
    Route::put('/carousel-home/{carouselSlide}', [CarouselSlideController::class, 'update'])->name('admin.carousel-home.update');
    Route::delete('/carousel-home/{carouselSlide}', [CarouselSlideController::class, 'destroy'])->name('admin.carousel-home.destroy');

    Route::get('/galeri-karya', [GalleryKaryaController::class, 'index'])->name('admin.galeri-karya');
    Route::post('/galeri-karya/{gallerySlot}/image', [GalleryKaryaController::class, 'replaceImage'])->name('admin.galeri-karya.replace-image');
    Route::put('/galeri-karya/{gallerySlot}', [GalleryKaryaController::class, 'updateMetadata'])->name('admin.galeri-karya.update');

    Route::get('/tiktok-feed', [TikTokFeedController::class, 'index'])->name('admin.tiktok-feed');
    Route::post('/tiktok-feed', [TikTokFeedController::class, 'store'])->name('admin.tiktok-feed.store');
    Route::put('/tiktok-feed/{tiktokFeed}', [TikTokFeedController::class, 'update'])->name('admin.tiktok-feed.update');
    Route::delete('/tiktok-feed/{tiktokFeed}', [TikTokFeedController::class, 'destroy'])->name('admin.tiktok-feed.destroy');

    Route::get('/katalog', [KatalogController::class, 'adminIndex'])->name('admin.katalog');
    Route::post('/katalog', [KatalogController::class, 'store'])->name('admin.katalog.store');
    Route::put('/katalog/{catalog}', [KatalogController::class, 'update'])->name('admin.katalog.update');
    Route::delete('/katalog/{catalog}', [KatalogController::class, 'destroy'])->name('admin.katalog.destroy');
    Route::post('/katalog/{catalog}/images', [KatalogController::class, 'uploadImage'])->name('admin.katalog.images.store');
    Route::delete('/katalog/{catalog}/images/{catalogImage}', [KatalogController::class, 'deleteImage'])->name('admin.katalog.images.destroy');
    Route::put('/katalog/{catalog}/images/reorder', [KatalogController::class, 'reorderImages'])->name('admin.katalog.images.reorder');

    Route::get('/pre-order-home', [PreOrderHomeController::class, 'index'])->name('admin.pre-order-home');
    Route::post('/pre-order-home', [PreOrderHomeController::class, 'store'])->name('admin.pre-order-home.store');
    Route::put('/pre-order-home/{homePreorder}', [PreOrderHomeController::class, 'update'])->name('admin.pre-order-home.update');
    Route::delete('/pre-order-home/{homePreorder}', [PreOrderHomeController::class, 'destroy'])->name('admin.pre-order-home.destroy');

    Route::get('/whatsapp-admins', [WhatsAppAdminController::class, 'index'])->name('admin.whatsapp-admins');
    Route::post('/whatsapp-admins', [WhatsAppAdminController::class, 'store'])->name('admin.whatsapp-admins.store');
    Route::put('/whatsapp-admins/{whatsappAdmin}', [WhatsAppAdminController::class, 'update'])->name('admin.whatsapp-admins.update');
    Route::delete('/whatsapp-admins/{whatsappAdmin}', [WhatsAppAdminController::class, 'destroy'])->name('admin.whatsapp-admins.destroy');
});
