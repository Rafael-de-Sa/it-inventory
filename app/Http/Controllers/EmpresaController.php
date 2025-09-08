<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function create()
    {
        return  view('empresas.create');
    }

    public function store(Request $request)
    {
        // 1) Para validar rapidamente o envio, descomente:
        dd($request->all(), $request->file('logo'));

        // 2) Se quiser já “simular” um save e voltar com sucesso:
        $dados = $request->only([
            'nome_fantasia',
            'razao_social',
            'cnpj',
            'cep',
            'rua',
            'numero',
            'bairro',
            'complemento',
            'cidade',
            'estado',
            'site',
            'email',
            'telefones'
        ]);

        // upload opcional (já testando o input file)
        //$logoPath = $request->file('logo')?->store('logos', 'public'); // requer `php artisan storage:link`

        // aqui você só confirma que chegou:
        return back()->with('ok', 'Formulário recebido com sucesso!')
            ->with('debug', ['dados' => $dados]);
    }
}
