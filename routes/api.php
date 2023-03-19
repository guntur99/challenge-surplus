<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('category')->group(function () {
    Route::get('/all', [CategoryController::class,'index']);
    Route::post('/store', [CategoryController::class,'store']);
    Route::post('/update', [CategoryController::class,'update']);
    Route::post('/delete', [CategoryController::class,'destroy']);
});

Route::prefix('product')->group(function () {
    Route::get('/all', [ProductController::class,'index']);
    Route::post('/store', [ProductController::class,'store']);
    Route::post('/update', [ProductController::class,'update']);
    Route::post('/delete', [ProductController::class,'destroy']);
});

Route::prefix('image')->group(function () {
    Route::get('/all', [ImageController::class,'index']);
    Route::post('/store', [ImageController::class,'store']);
    Route::post('/update', [ImageController::class,'update']);
    Route::post('/update/multiple', [ImageController::class,'updateMultiple']);
    Route::post('/delete', [ImageController::class,'destroy']);
});
