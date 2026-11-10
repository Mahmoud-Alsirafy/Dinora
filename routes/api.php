<?php

use App\Http\Controllers\Api\Var\ApiVariants;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Cate\Categories;
use App\Http\Controllers\Api\Prod\ApiProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// !! Routes For All Users
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// !! Routes For All Users

// ?? Product Routes
Route::get('/categories', [Categories::class, 'index']);
Route::get('/products/{id}', [ApiProducts::class, 'get']);
Route::get('/product/{slug}', [ApiProducts::class, 'show']);
Route::get('/variant/{sku}', [ApiVariants::class, 'show']);

// !!Routes For Authanticated Users
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});