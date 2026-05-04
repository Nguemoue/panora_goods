<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Livewire\TrackOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/track-order', TrackOrder::class)->name('track.order');

Route::get("/contact",[ContactController::class,'show'])->name('contact');
Route::post("/contact",[ContactController::class,'store'])->name('contact.send');
Route::view("/metions-legale","legal")->name('legal.mentions');
