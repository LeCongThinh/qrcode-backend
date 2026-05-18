<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Đăng nhập
Route::post('/login', [AuthController::class, 'login']);
// Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Danh sách sản phẩm
Route::get('/products', [ProductController::class, 'index']);
// Chi tiết sản phẩm theo slug
Route::get('/products/{slug}', [ProductController::class, 'show']);

// Bảo vệ api tạo sản phẩm bằng middleware auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Tạo sản phẩm mới
    Route::post('/products', [ProductController::class, 'store']);
});
