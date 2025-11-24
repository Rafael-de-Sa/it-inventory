<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EquipamentoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\SetorController;
use App\Http\Controllers\TipoEquipamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ViaCepController;
use App\Models\Movimentacao;
use Illuminate\Support\Facades\Route;

//sem login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginSubmit'])->name('login');
});

//autenticados
Route::middleware('auth')->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('/');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('empresas', EmpresaController::class);

    Route::get('/empresas/cep/{cep}', [ViaCepController::class, 'show'])->name('empresas.cep');

    Route::resource('setores', SetorController::class)
        ->parameters(['setores' => 'setor']);

    Route::resource('tipo-equipamentos', TipoEquipamentoController::class);

    Route::resource('equipamentos', EquipamentoController::class);

    Route::resource('funcionarios', FuncionarioController::class);

    Route::get('empresas/{empresa}/setores', [FuncionarioController::class, 'setoresPorEmpresa'])
        ->name('funcionarios.setoresPorEmpresa');

    Route::resource('usuarios', UsuarioController::class);

    Route::get('/empresas/{empresa}/setores-ativos', [UsuarioController::class, 'setoresAtivos']);

    Route::get('/setores/{setor}/funcionarios-disponiveis', [UsuarioController::class, 'funcionariosPorSetor']);

    Route::resource('movimentacoes', MovimentacaoController::class)
        ->parameters(['movimentacoes' => 'movimentacao'])
        ->except(['destroy', 'update', 'edit']);

    Route::get('/empresas/{empresa}/setores-movimentacao', [MovimentacaoController::class, 'setoresParaMovimentacao'])
        ->name('movimentacoes.setores-para-movimentacao');

    Route::get('/setores/{setor}/funcionarios-movimentacao', [MovimentacaoController::class, 'funcionariosParaMovimentacao'])
        ->name('movimentacoes.funcionarios-para-movimentacao');

    Route::post(
        '/movimentacoes/{movimentacao}/upload-termo-responsabilidade',
        [MovimentacaoController::class, 'uploadTermoResponsabilidade']
    )->name('movimentacoes.upload-termo-responsabilidade');

    Route::get(
        '/movimentacoes/{movimentacao}/termo-responsabilidade',
        [MovimentacaoController::class, 'gerarTermoResponsabilidade']
    )->name('movimentacoes.termo-responsabilidade');

    //devolução
    Route::get('/movimentacoes/devolucao/create', [MovimentacaoController::class, 'createDevolucao'])
        ->name('movimentacoes.devolucao.create');

    Route::post('/movimentacoes/devolucao', [MovimentacaoController::class, 'storeDevolucao'])
        ->name('movimentacoes.devolucao.store');

    Route::get(
        '/movimentacoes/devolucao/funcionarios/{funcionario}/equipamentos-em-uso',
        [MovimentacaoController::class, 'equipamentosEmUsoParaDevolucao']
    )->name('movimentacoes.equipamentos-em-uso');

    Route::get('/movimentacoes/{movimentacao}/termo-devolucao', [MovimentacaoController::class, 'gerarTermoDevolucao'])
        ->name('movimentacoes.termo-devolucao');

    Route::post(
        '/movimentacoes/{movimentacao}/upload-termo-devolucao',
        [MovimentacaoController::class, 'uploadTermoDevolucao']
    )->name('movimentacoes.upload-termo-devolucao');

    Route::get(
        '/movimentacoes/{movimentacao}/termo-responsabilidade/visualizar',
        [MovimentacaoController::class, 'visualizarTermoResponsabilidade']
    )->name('movimentacoes.termo.responsabilidade.visualizar');

    Route::get(
        '/movimentacoes/{movimentacao}/termo-devolucao/visualizar',
        [MovimentacaoController::class, 'visualizarTermoDevolucao']
    )->name('movimentacoes.termo.devolucao.visualizar');
});
