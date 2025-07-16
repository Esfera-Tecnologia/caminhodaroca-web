<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccessProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\SenhaController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/forgot-password ', [PasswordResetLinkController::class, 'create'])->name('password.request');


Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::resource('access-profiles', AccessProfileController::class);
});

// Usuários
Route::resource('users', UserController::class)->middleware('auth');

Route::get('/definir-senha', [SetPasswordController::class, 'showSetPasswordForm'])
    ->name('password.set')
    ->middleware('signed');

Route::post('/definir-senha', [SetPasswordController::class, 'storePassword'])->name('password.store'); 

Route::get('/definir-senha/{token}', [SenhaController::class, 'formNovaSenha'])->name('password.set');
Route::post('/definir-senha', [SenhaController::class, 'storeNovaSenha'])->name('definir-senha.store');



// Categorias
Route::resource('categories', CategoryController::class)->middleware('auth');


// Subcategorias
Route::resource('subcategories', SubcategoryController::class)->middleware('auth');
Route::post('/ajax/categories', [CategoryController::class, 'storeAjax'])->name('categories.ajax.store');


// Propriedades
Route::resource('properties', PropertyController::class)->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
