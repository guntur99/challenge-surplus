<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
