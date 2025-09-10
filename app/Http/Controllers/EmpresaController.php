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

        //dd($request);

        $request->validate(
            [
                'nome_fantasia' => ['required', 'min:3', 'max:100'],
                'razao_social' => ['required', 'min:3', 'max:100'],
                'cnpj' => ['required', 'min:14', 'max:14'],
                'rua' => ['required', 'min:3', 'max:100'],
                'numero' => ['required', 'max:8'],
                'complemento' => ['max:50'],
                'bairro' => ['required', 'min:3', 'max:50'],
                'cidade' => ['required', 'min:3', 'max:30'],
                'estado' => ['required', 'min:2', 'max:2'],
                'cep' => ['required', 'min:8', 'max:8'],
                'site' => ['max:40'],
                'email' => ['required', 'email', 'max:60'],
                'telefones' => [],
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:280']
            ],
            []
        );


        if ($request->file('logo') != null) {
            //Tá salvando o logo
            $path = $request->file('logo')->store('logo');
        };

        echo $request->input('nome_fantasia');
    }
}
