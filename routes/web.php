<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

//index route
Route::get('/', [MainController::class, 'index'])->name('/');

//auth routes
Route::get('/login', [AuthController::class, 'login'])->name('/login');
Route::post('loginSubmit', [AuthController::class, 'loginSubmit'])->name('loginSubmit');
Route::get('/logout', [AuthController::class, 'logout'])->name('/logout');

//employers routes
Route::get('/empresas/create', [EmpresaController::class, 'create'])->name('/empresas.create');
Route::post('/empresas',        [EmpresaController::class, 'store'])->name('empresas.store');
