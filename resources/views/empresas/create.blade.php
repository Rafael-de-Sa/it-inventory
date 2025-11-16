@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="empresaForm" action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6"
            data-cep-endpoint="{{ route('empresas.cep', ['cep' => '00000000']) }}">
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Empresa</h2>
                <p class="text-xs text-green-200">Preencha os dados abaixo.</p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Nome Fantasia --}}
            <div>
                <label for="nome_fantasia" class="block mb-1 text-sm font-medium text-green-100">Nome Fantasia*</label>
                <input id="nome_fantasia" name="nome_fantasia" type="text" maxlength="100" autocomplete="organization"
                    placeholder="Empresa Exemplo" value="{{ old('nome_fantasia') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'nome_fantasia'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'nome_fantasia'),
                    ])
                    aria-invalid="{{ $errors->has('nome_fantasia') ? 'true' : 'false' }}"
                    aria-describedby="nome_fantasia_help">
                @if ($errors->has('nome_fantasia'))
                    <p id="nome_fantasia_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('nome_fantasia') }}
                    </p>
                @else
                    <p id="nome_fantasia_help" class="mt-1 text-xs text-green-200">Ex.: Empresa Exemplo</p>
                @endif
            </div>

            {{-- Razão Social --}}
            <div>
                <label for="razao_social" class="block mb-1 text-sm font-medium text-green-100">Razão Social*</label>
                <input id="razao_social" name="razao_social" type="text" maxlength="100"
                    placeholder="Empresa Exemplo LTDA" value="{{ old('razao_social') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'razao_social'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'razao_social'),
                    ])
                    aria-invalid="{{ $errors->has('razao_social') ? 'true' : 'false' }}"
                    aria-describedby="razao_social_help">
                @if ($errors->has('razao_social'))
                    <p id="razao_social_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('razao_social') }}
                    </p>
                @else
                    <p id="razao_social_help" class="mt-1 text-xs text-green-200">Ex.: Empresa Exemplo LTDA</p>
                @endif
            </div>

            {{-- CNPJ --}}
            <div>
                <label for="cnpj" class="block mb-1 text-sm font-medium text-green-100">CNPJ*</label>
                <input id="cnpj" name="cnpj" type="text" inputmode="numeric" maxlength="18"
                    placeholder="00.000.000/0000-00"
                    value="{{ old('cnpj') ? \App\Support\Mask::cnpj(old('cnpj')) : $empresa->cnpj_masked ?? '' }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'cnpj'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'cnpj'),
                    ]) aria-invalid="{{ $errors->has('cnpj') ? 'true' : 'false' }}"
                    aria-describedby="cnpj_help">
                @if ($errors->has('cnpj'))
                    <p id="cnpj_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('cnpj') }}
                    </p>
                @else
                    <p id="cnpj_help" class="mt-1 text-xs text-green-200">Formato: 00.000.000/0000-00</p>
                @endif
            </div>

            {{-- Endereço --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200">Endereço</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- CEP --}}
                    <div class="md:col-span-3">
                        <label for="cep" class="block mb-1 text-sm font-medium text-green-100">CEP*</label>
                        <input id="cep" name="cep" type="text" inputmode="numeric" maxlength="9"
                            placeholder="87500-000" autocomplete="postal-code"
                            value="{{ old('cep') ? \App\Support\Mask::cep(old('cep')) : $empresa->cep_masked ?? '' }}"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'cep'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'cep'),
                            ]) aria-invalid="{{ $errors->has('cep') ? 'true' : 'false' }}"
                            aria-describedby="cep_help">
                        @if ($errors->has('cep'))
                            <p id="cep_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('cep') }}
                            </p>
                        @else
                            <p id="cep_help" class="mt-1 text-xs text-green-200">Formato: 00000-000</p>
                        @endif
                    </div>

                    {{-- Rua --}}
                    <div class="md:col-span-9">
                        <label for="rua" class="block mb-1 text-sm font-medium text-green-100">Logradouro*</label>
                        <input id="rua" name="rua" type="text" maxlength="100" autocomplete="address-line1"
                            placeholder="Av. Paraná" value="{{ old('rua') }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'rua'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'rua'),
                            ])
                            aria-invalid="{{ $errors->has('rua') ? 'true' : 'false' }}" aria-describedby="rua_help">
                        @if ($errors->has('rua'))
                            <p id="rua_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('rua') }}
                            </p>
                        @else
                            <p id="rua_help" class="mt-1 text-xs text-green-200">Ex.: Av. Paraná</p>
                        @endif
                    </div>

                    {{-- Número --}}
                    <div class="md:col-span-3">
                        <label for="numero" class="block mb-1 text-sm font-medium text-green-100">Número*</label>
                        <input id="numero" name="numero" type="text" inputmode="numeric" maxlength="8"
                            placeholder="1234" autocomplete="address-line2" value="{{ old('numero') }}"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'numero'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'numero'),
                            ]) aria-invalid="{{ $errors->has('numero') ? 'true' : 'false' }}"
                            aria-describedby="numero_help">
                        @if ($errors->has('numero'))
                            <p id="numero_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('numero') }}
                            </p>
                        @else
                            <p id="numero_help" class="mt-1 text-xs text-green-200">Ex.: 1234</p>
                        @endif
                    </div>

                    {{-- Bairro --}}
                    <div class="md:col-span-4">
                        <label for="bairro" class="block mb-1 text-sm font-medium text-green-100">Bairro*</label>
                        <input id="bairro" name="bairro" type="text" maxlength="50" placeholder="Centro"
                            value="{{ old('bairro') }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'bairro'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'bairro'),
                            ])
                            aria-invalid="{{ $errors->has('bairro') ? 'true' : 'false' }}"
                            aria-describedby="bairro_help">
                        @if ($errors->has('bairro'))
                            <p id="bairro_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('bairro') }}
                            </p>
                        @else
                            <p id="bairro_help" class="mt-1 text-xs text-green-200">Ex.: Centro</p>
                        @endif
                    </div>

                    {{-- Complemento --}}
                    <div class="md:col-span-5">
                        <label for="complemento" class="block mb-1 text-sm font-medium text-green-100">Complemento</label>
                        <input id="complemento" name="complemento" type="text" maxlength="50"
                            placeholder="Ap., sala, bloco..." value="{{ old('complemento') }}"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'complemento'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'complemento'),
                            ])
                            aria-invalid="{{ $errors->has('complemento') ? 'true' : 'false' }}"
                            aria-describedby="complemento_help">
                        @if ($errors->has('complemento'))
                            <p id="complemento_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('complemento') }}
                            </p>
                        @else
                            <p id="complemento_help" class="mt-1 text-xs text-green-200">Opcional</p>
                        @endif
                    </div>

                    {{-- Cidade --}}
                    <div class="md:col-span-8">
                        <label for="cidade" class="block mb-1 text-sm font-medium text-green-100">Cidade*</label>
                        <input id="cidade" name="cidade" type="text" maxlength="30"
                            autocomplete="address-level2" placeholder="Umuarama" value="{{ old('cidade') }}"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'cidade'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'cidade'),
                            ]) aria-invalid="{{ $errors->has('cidade') ? 'true' : 'false' }}"
                            aria-describedby="cidade_help">
                        @if ($errors->has('cidade'))
                            <p id="cidade_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('cidade') }}
                            </p>
                        @else
                            <p id="cidade_help" class="mt-1 text-xs text-green-200">Ex.: Umuarama</p>
                        @endif
                    </div>

                    {{-- Estado (UF) --}}
                    <div class="md:col-span-4">
                        <label for="estado" class="block mb-1 text-sm font-medium text-green-100">Estado*</label>
                        <input id="estado" name="estado" type="text" maxlength="2" placeholder="PR"
                            value="{{ old('estado') }}" oninput="this.value = this.value.toUpperCase()"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none uppercase',
                                'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                    'estado'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'estado'),
                            ]) aria-invalid="{{ $errors->has('estado') ? 'true' : 'false' }}"
                            aria-describedby="estado_help">
                        @if ($errors->has('estado'))
                            <p id="estado_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('estado') }}
                            </p>
                        @else
                            <p id="estado_help" class="mt-1 text-xs text-green-200">Digite a UF (ex.: PR)</p>
                        @endif
                    </div>
                </div>
            </fieldset>

            {{-- E-mail --}}
            <div>
                <label for="email" class="block mb-1 text-sm font-medium text-green-100">E-mail*</label>
                <input id="email" name="email" type="email" maxlength="60" autocomplete="email"
                    placeholder="contato@exemplo.com.br" value="{{ old('email') }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'email'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'email'),
                    ])
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}" aria-describedby="email_help">
                @if ($errors->has('email'))
                    <p id="email_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('email') }}
                    </p>
                @else
                    <p id="email_help" class="mt-1 text-xs text-green-200">Usado para contato oficial</p>
                @endif
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block mb-1 text-sm font-medium text-green-100">Telefone</label>
                <input id="telefone" name="telefone" type="text" placeholder="(44) 99999-9999 ou (44) 3333-3333"
                    value="{{ old('telefone') ? \App\Support\Mask::telefone(old('telefone')) : $empresa->telefone_masked ?? '' }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                            'telefone'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'telefone'),
                    ]) aria-invalid="{{ $errors->has('telefone') ? 'true' : 'false' }}"
                    aria-describedby="telefone_help">
                @if ($errors->has('telefone'))
                    <p id="telefone_help" class="mt-1 text-xs text-red-300 flex items-center gap-1">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('telefone') }}
                    </p>
                @else
                    <p id="telefone_help" class="mt-1 text-xs text-green-200">Ex: (44) 99999-9999 ou (44) 3333-3333</p>
                @endif
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('empresas.index') }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">
                    <i class="fa-solid fa-arrow-left"></i>Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Salvar
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/empresas/empresa-form.js')
@endpush
