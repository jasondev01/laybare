<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UsersController;

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

// categories
Route::get('/categories', [CategoriesController::class, 'index']);
Route::post('/categories', [CategoriesController::class, 'store']);
Route::get('/categories/{id}', [CategoriesController::class, 'show']);
Route::put('/categories/{id}/update', [CategoriesController::class, 'update']);
Route::delete('/categories/{id}', [CategoriesController::class, 'softDelete']);
Route::get('/categories/soft-deleted/archived', [CategoriesController::class, 'softDeleteShow']);
Route::patch('/categories/{id}', [CategoriesController::class, 'restore']);

// products
Route::get('/products', [ProductsController::class, 'index']);
Route::post('/products', [ProductsController::class, 'store']);
Route::get('/products/{id}', [ProductsController::class, 'show']);
Route::put('/products/{id}/update', [ProductsController::class, 'update']);
Route::delete('/products/{id}', [ProductsController::class, 'softDelete']);
Route::get('/products/soft-deleted/archived', [ProductsController::class, 'softDeleteShow']);
Route::patch('/products/{id}', [ProductsController::class, 'restore']);

// users
Route::get('/users', [UsersController::class, 'index']);
Route::post('/users', [UsersController::class, 'store']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::patch('/users/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'softDelete']);
Route::patch('/users/{id}/restore', [UsersController::class, 'restore']);