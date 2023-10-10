<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
  return redirect('/login');
});

Route::get('/email/verify', function () {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Auth::routes(['verify' => true]);

Route::middleware(['auth', 'verified'])->group(function () {
  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

  Route::get('/welcome', function () {
    return redirect('/home');
  })->name('welcome');

  Route::middleware(['can:consultar-usuarios'])->group(function () {
    Route::controller(App\Http\Controllers\UsersController::class)->group(function () {
      Route::get('/users', 'index')->name('admin.user.list');
      Route::get('/users/edit/{id?}', 'edit')->name('admin.user.edit');
      Route::get('/users/create', 'create')->name('admin.user.create');
      Route::post('/users/store', 'store')->name('admin.user.store');
      Route::get('/users/deactivate/{id?}', 'destroy')->name('admin.user.deactivate');
    });
  });

  Route::middleware(['can:consultar-productos'])->group(function () {
    Route::controller(App\Http\Controllers\ProductosController::class)->group(function () {
      Route::get('/productos', 'index')->name('admin.productos.list');
      Route::get('/productos/edit/{id?}', 'edit')->name('admin.productos.edit');
      Route::get('/productos/create', 'create')->name('admin.productos.create');
      Route::post('/productos/store', 'store')->name('admin.productos.store');
      Route::get('/productos/deactivate/{id?}', 'destroy')->name('admin.productos.deactivate');
    });
  });
});
