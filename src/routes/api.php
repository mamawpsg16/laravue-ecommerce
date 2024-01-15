<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\App\AuthenticationController;

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

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', [AuthenticationController::class, 'tokenVerification'])->middleware('user.authenticated');
    Route::post('/logout',[AuthenticationController::class,'logout']); 

    Route::apiResources([
        'product' => ProductController::class,
        'category' => CategoryController::class,
    ]);

    Route::get('get-categories', [CategoryController::class, 'categories']);

});