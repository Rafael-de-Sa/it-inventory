<?php

namespace App\Http\Controllers;

use App\Services\PainelInicial;

class MainController extends Controller
{
    /** Tela inicial: boas-vindas e dashboard do perfil (TIC ou DP). */
    public function index()
    {
        return view('index', ['painel' => PainelInicial::paraUsuarioAtual()]);
    }
}
