<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TipoEquipamentoController;
use App\Http\Controllers\ViaCepController;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'index'])->name('/');

//auth routes
Route::get('/login', [AuthController::class, 'login'])->name('/login');
Route::post('loginSubmit', [AuthController::class, 'loginSubmit'])->name('loginSubmit');
Route::get('/logout', [AuthController::class, 'logout'])->name('/logout');

Route::get('/empresas/cep/{cep}', [ViaCepController::class, 'show'])->name('empresas.cep');

Route::resource('empresas', EmpresaController::class);
Route::resource('setores', SetorController::class)
    ->parameters(['setores' => 'setor']);
Route::resource('tipo-equipamentos', TipoEquipamentoController::class);
Route::resource('equipamentos', EquipamentoController::class);
Route::resource('funcionarios', FuncionarioController::class);
Route::get('empresas/{empresa}/setores', [FuncionarioController::class, 'setoresPorEmpresa'])
    ->name('funcionarios.setoresPorEmpresa');

Route::resource('movimentacoes', MovimentacaoController::class)
    ->parameters(['movimentacoes' => 'movimentacao']);
