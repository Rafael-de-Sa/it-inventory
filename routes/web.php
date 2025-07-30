<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

//index route
Route::get('/', [MainController::class, 'index'])->name('/');

//auth routes
Route::get('/login', [AuthController::class, 'login'])->name('/login');
Route::post('loginSubmit', [AuthController::class, 'loginSubmit'])->name('loginSubmit');
Route::get('/logout', [AuthController::class, 'logout'])->name('/logout');

//employers routes
Route::get('/NewEmployer', [MainController::class, 'NewEmployer'])->name('/NewEmployer');
