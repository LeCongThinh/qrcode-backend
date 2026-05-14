<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);
// Tạo sản phẩm mới
Route::post('/products', [ProductController::class, 'store']);
// Chi tiết sản phẩm theo slug
Route::get('/products/{slug}', [ProductController::class, 'show']);
