<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/katalog', 'Katalog')->name('katalog');
Route::inertia('/studio-custom', 'StudioCustom')->name('studio-custom');
Route::inertia('/tracking', 'Tracking')->name('tracking');
