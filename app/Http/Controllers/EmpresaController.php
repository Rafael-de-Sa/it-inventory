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
                'nome_fantasia' => ['required', 'string', 'min:3', 'max:100'],
                'razao_social'  => ['required', 'string', 'min:3', 'max:100'],

                'cnpj'          => ['required', 'string', 'size:14', 'regex:/^\d{14}$/'],
                'cep'           => ['required', 'string', 'size:8',  'regex:/^\d{8}$/'],

                'rua'           => ['required', 'string', 'min:3', 'max:100'],
                'numero'        => ['required', 'string', 'max:8'],
                'complemento'   => ['nullable', 'string', 'max:50'],
                'bairro'        => ['required', 'string', 'min:3', 'max:50'],
                'cidade'        => ['required', 'string', 'min:3', 'max:30'],
                'estado'        => ['required', 'string', 'size:2', 'alpha'],
                'site'          => ['nullable', 'string', 'url', 'max:40'],
                'email'         => ['required', 'string', 'email', 'max:60'],
                'telefones'     => ['nullable'],
                'logo'          => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:5120'],
            ],

            [
                'required' => 'O campo :attribute é obrigatório.',
                'string'   => 'O campo :attribute deve ser um texto.',
                'min'      => 'O campo :attribute deve possuir ao menos :min caracteres.',
                'max'      => 'O campo :attribute deve possuir no máximo :max caracteres.',
                'size'     => 'O campo :attribute deve conter exatamente :size caracteres.',
                'alpha'    => 'O campo :attribute deve conter apenas letras.',
                'email'    => 'Informe um :attribute válido.',
                'url'      => 'Informe uma URL válida em :attribute.',
                'array'    => 'O campo :attribute deve ser uma lista (array).',

                'image'    => 'O arquivo :attribute deve ser uma imagem.',
                'mimes'    => 'A :attribute deve ser do tipo: :values.',
                'logo.max' => 'A logo deve ter no máximo :max KB.',

                'cnpj.regex' => 'O CNPJ deve conter apenas dígitos (somente números), sem pontuação, com 14 caracteres.',
                'cep.regex'  => 'O CEP deve conter apenas dígitos (somente números), sem pontuação, com 8 caracteres.',
            ],

            [
                'nome_fantasia' => 'nome fantasia',
                'razao_social'  => 'razão social',
                'cnpj'          => 'CNPJ',
                'rua'           => 'rua',
                'numero'        => 'número',
                'complemento'   => 'complemento',
                'bairro'        => 'bairro',
                'cidade'        => 'cidade',
                'estado'        => 'UF',
                'cep'           => 'CEP',
                'site'          => 'site',
                'email'         => 'e-mail',
                'telefones'     => 'telefones',
                'logo'          => 'logo',
            ]
        );


        if ($request->file('logo') != null) {
            //Tá salvando o logo
            $path = $request->file('logo')->store('logo');
        };

        echo $request->input('nome_fantasia');
    }
}
