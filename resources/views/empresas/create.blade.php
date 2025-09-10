@extends('layouts.main_layout')

@section('content')
    @php
        // Rótulos
        $label = 'block mb-1 text-sm font-medium text-green-100';

        // Inputs habilitados (branco)
        $input = "w-full rounded-lg border border-green-700 px-3 py-2
              bg-white text-gray-900 placeholder-gray-500
              focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400";

        // Inputs desabilitados (cinza claro) — força para ganhar de qualquer reset
        $inputDisabled = "w-full rounded-lg border px-3 py-2 appearance-none
                      !bg-gray-400 !text-black !border-gray-300 placeholder-gray-400
                      cursor-not-allowed focus:outline-none focus:ring-0 focus:border-gray-300";
    @endphp

    <div class="w-full flex justify-center">

        <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Empresa</h2>
                <p class="text-xs text-green-200">Preencha os dados abaixo.</p>
            </header>

            {{-- Nome Fantasia --}}
            <div>
                <label for="nome_fantasia" class="{{ $label }}">Nome Fantasia</label>
                <input id="nome_fantasia" name="nome_fantasia" type="text" placeholder="EMPRESA LTDA"
                    class="{{ $input }}">
            </div>


            @error('nome_fantasia')
                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                    role="alert">
                    {{ $message }}
                </div>
            @enderror

            {{-- Razão Social --}}
            <div>
                <label for="razao_social" class="{{ $label }}">Razão Social</label>
                <input id="razao_social" name="razao_social" type="text" class="{{ $input }}"
                    placeholder="EMPRESA">
            </div>

            @error('razao_social')
                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                    role="alert">
                    {{ $message }}
                </div>
            @enderror

            {{-- CNPJ --}}
            <div>
                <label for="cnpj" class="{{ $label }}">CNPJ</label>
                <input id="cnpj" name="cnpj" type="text" placeholder="00.000.000/0000-00"
                    class="{{ $input }}">
            </div>

            @error('cnpj')
                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                    role="alert">
                    {{ $message }}
                </div>
            @enderror

            {{-- Endereço (caixinha) --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200 uppercase">Endereço</legend>

                <p class="text-xs text-green-200">
                    Por ora, apenas <span class="font-semibold">CEP</span>, <span class="font-semibold">Número</span> e
                    <span class="font-semibold">Complemento</span> estão habilitados.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- CEP + Rua --}}
                    <div class="md:col-span-3">
                        <label for="cep" class="{{ $label }}">CEP</label>
                        <input id="cep" name="cep" type="text" placeholder="87500-000"
                            class="{{ $input }}">
                        @error('cep')
                            <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>



                    <div class="md:col-span-9">
                        <label for="rua" class="{{ $label }}">Rua</label>
                        <input id="rua" name="rua" type="text"
                            class="{{ $input }}"placeholder="Av. Paraná">
                        @error('rua')
                            <div class="col-span-9 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    {{--
                    TODO: ajustar input: rua, bairro, estado, cidade
                        -> disabled como atributo e
                        -> $inputDisabled na classe
                    --}}

                    {{-- Número + Bairro + Complemento --}}
                    <div class="md:col-span-3">
                        <label for="numero" class="{{ $label }}">Número</label>
                        <input id="numero" name="numero" type="text" class="{{ $input }}" placeholder="1234">
                        @error('numero')
                            <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="md:col-span-4">
                        <label for="bairro" class="{{ $label }}">Bairro</label>
                        <input id="bairro" name="bairro" type="text" class="{{ $input }}"
                            placeholder="Centro">
                        @error('bairro')
                            <div class="col-span-4 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="md:col-span-5">
                        <label for="complemento" class="{{ $label }}">Complemento</label>
                        <input id="complemento" name="complemento" type="text" placeholder="Ap., sala, bloco..."
                            class="{{ $input }}">
                        @error('complemento')
                            <div class="col-span-5 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Cidade + Estado --}}
                    <div class="md:col-span-8">
                        <label for="cidade" class="{{ $label }}">Cidade</label>
                        <input id="cidade" name="cidade" type="text" class="{{ $input }}"
                            placeholder="Umuarama">
                        @error('cidade')
                            <div class="col-span-8 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="md:col-span-4">
                        <label for="estado" class="{{ $label }}">Estado</label>
                        <input id="estado" name="estado" type="text" class="{{ $input }}"
                            placeholder="PR">
                        @error('estado')
                            <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                </div>
            </fieldset>

            {{-- Site --}}
            <div>
                <label for="site" class="{{ $label }}">Site</label>
                <input id="site" name="site" type="url" placeholder="https://exemplo.com.br"
                    class="{{ $input }}">
                @error('site')
                    <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                        role="alert">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- E-mail --}}
            <div>
                <label for="email" class="{{ $label }}">E-mail</label>
                <input id="email" name="email" type="email" placeholder="contato@exemplo.com.br"
                    class="{{ $input }}">
                @error('email')
                    <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                        role="alert">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Telefones --}}
            <div>
                <label for="telefones" class="{{ $label }}">Telefones</label>
                <input id="telefones" name="telefones" type="text" placeholder="(44) 9 9999-9999, (44) 3333-3333"
                    class="{{ $input }}">
                @error('telefones')
                    <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                        role="alert">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Logo --}}
            <div>
                <label for="logo" class="{{ $label }}">Logo</label>
                <input id="logo" name="logo" type="file" accept="image/*"
                    class="block w-full text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-green-700 file:text-white
                          file:px-4 file:py-2 file:hover:bg-green-600 file:cursor-pointer file:font-medium
                          rounded-lg bg-white text-gray-900 border border-green-700 px-3 py-2">
                @error('logo')
                    <div class="col-span-3 bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                        role="alert">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ url()->previous() }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">Cancelar</a>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">Salvar</button>
            </div>
        </form>
    </div>
@endsection
