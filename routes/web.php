<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccessProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyImageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\SenhaController;
use Illuminate\Support\Facades\Artisan;


Route::get('/phpini', function (){
    phpinfo();
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::resource('access-profiles', AccessProfileController::class);
});

// Usuários
Route::resource('users', UserController::class)->middleware('auth');

Route::get('/definir-senha', [SetPasswordController::class, 'showSetPasswordForm'])
    ->name('password.set')
    ->middleware('signed');

Route::post('/definir-senha', [SetPasswordController::class, 'storePassword'])->name('password.set.store'); 

Route::get('/definir-senha/{token}', [SenhaController::class, 'formNovaSenha'])->name('password.set.token');
Route::post('/definir-senha/{token}', [SenhaController::class, 'storeNovaSenha'])->name('definir-senha.store');



// Categorias
Route::resource('categories', CategoryController::class)->middleware('auth');


// Subcategorias
Route::resource('subcategories', SubcategoryController::class)->middleware('auth');
Route::post('/ajax/categories', [CategoryController::class, 'storeAjax'])->name('categories.ajax.store');


Route::resource('products', ProductController::class)->middleware('auth');


// Propriedades
// routes/web.php

Route::post('/imagens/remover', [PropertyImageController::class, 'remover'])->name('imagens.remover');


Route::resource('properties', PropertyController::class)->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
