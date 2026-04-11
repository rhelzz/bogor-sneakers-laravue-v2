<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/katalog', 'Katalog')->name('katalog');
Route::get('/katalog/{productId}', function (int $productId) {
    return Inertia::render('ProductDetail', [
        'productId' => $productId,
    ]);
})->whereNumber('productId')->name('katalog.show');
Route::inertia('/studio-custom', 'StudioCustom')->name('studio-custom');
Route::inertia('/tracking', 'Tracking')->name('tracking');
