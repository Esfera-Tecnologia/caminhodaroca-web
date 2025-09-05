<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AdminController;
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

// Rotas de autenticação (públicas)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);

// Rotas de registro (públicas)
Route::prefix('register')->group(function () {
    Route::post('/personal-data', [RegisterController::class, 'personalData']);
    Route::post('/categories', [RegisterController::class, 'categories']);
    Route::post('/finish', [RegisterController::class, 'finish']);
});

// Rotas de localização (públicas)
Route::get('/states', [LocationController::class, 'states']);
Route::get('/cities', [LocationController::class, 'cities']);

// Rotas de categorias (públicas)
Route::get('/categories', [CategoryController::class, 'categories']);
Route::get('/subcategories', [CategoryController::class, 'subcategories']);

// Rotas de propriedades (públicas)
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Propriedades
    Route::post('/properties/{id}/favorite', [PropertyController::class, 'toggleFavorite']);
    
    // Perfil do usuário
    Route::prefix('profile')->group(function () {
        Route::put('/personal-data', [ProfileController::class, 'updatePersonalData']);
        Route::put('/categories', [ProfileController::class, 'updateCategories']);
        Route::post('/photo', [ProfileController::class, 'updatePhoto']);
    });

    // Rotas administrativas (requer privilégios de admin)
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/stats', [AdminController::class, 'userStats']);
        Route::get('/users/{id}', [AdminController::class, 'userDetails']);
    });
});
