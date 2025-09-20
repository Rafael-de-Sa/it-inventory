@extends('layouts.main_layout')

@section('content')
    @php
        // Estilos base
        $label = 'block mb-1 text-sm font-medium text-green-100';
        $inputBase =
            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none';
        $inputOk = 'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400';
        $inputErr = 'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300';
        $msgErr = 'mt-1 text-xs text-red-300 flex items-center gap-1';
        $msgHelp = 'mt-1 text-xs text-green-200';

        // Helpers
        $hasErr = fn(string $name) => $errors->has($name);
        $cls = fn(string $name) => $inputBase . ' ' . ($hasErr($name) ? $inputErr : $inputOk);
    @endphp

    <div class="w-full flex justify-center">
        <form id="empresaForm" action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Cadastro de Empresa</h2>
                <p class="text-xs text-green-200">Preencha os dados abaixo.</p>
            </header>

            {{-- Resumo de erros (opcional) --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- Nome Fantasia --}}
            <div>
                <label for="nome_fantasia" class="{{ $label }}">Nome Fantasia</label>
                <input id="nome_fantasia" name="nome_fantasia" type="text" maxlength="100" autocomplete="organization"
                    placeholder="EMPRESA LTDA" value="{{ old('nome_fantasia') }}" class="{{ $cls('nome_fantasia') }}"
                    aria-invalid="{{ $hasErr('nome_fantasia') ? 'true' : 'false' }}" aria-describedby="nome_fantasia_help">
                @if ($hasErr('nome_fantasia'))
                    <p id="nome_fantasia_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('nome_fantasia') }}
                    </p>
                @else
                    <p id="nome_fantasia_help" class="{{ $msgHelp }}">Ex.: EMPRESA LTDA</p>
                @endif
            </div>

            {{-- Razão Social --}}
            <div>
                <label for="razao_social" class="{{ $label }}">Razão Social</label>
                <input id="razao_social" name="razao_social" type="text" maxlength="100" placeholder="EMPRESA"
                    value="{{ old('razao_social') }}" class="{{ $cls('razao_social') }}"
                    aria-invalid="{{ $hasErr('razao_social') ? 'true' : 'false' }}" aria-describedby="razao_social_help">
                @if ($hasErr('razao_social'))
                    <p id="razao_social_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('razao_social') }}
                    </p>
                @else
                    <p id="razao_social_help" class="{{ $msgHelp }}">Ex.: EMPRESA</p>
                @endif
            </div>

            {{-- CNPJ (com máscara na digitação) --}}
            <div>
                <label for="cnpj" class="{{ $label }}">CNPJ</label>
                <input id="cnpj" name="cnpj" type="text" inputmode="numeric" maxlength="18"
                    placeholder="00.000.000/0000-00" value="{{ old('cnpj') }}" class="{{ $cls('cnpj') }}"
                    aria-invalid="{{ $hasErr('cnpj') ? 'true' : 'false' }}" aria-describedby="cnpj_help">
                @if ($hasErr('cnpj'))
                    <p id="cnpj_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('cnpj') }}
                    </p>
                @else
                    <p id="cnpj_help" class="{{ $msgHelp }}">Formato: 00.000.000/0000-00</p>
                @endif
            </div>

            {{-- Endereço --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200 uppercase">Endereço</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- CEP (com máscara na digitação) --}}
                    <div class="md:col-span-3">
                        <label for="cep" class="{{ $label }}">CEP</label>
                        <input id="cep" name="cep" type="text" inputmode="numeric" maxlength="9"
                            placeholder="87500-000" autocomplete="postal-code" value="{{ old('cep') }}"
                            class="{{ $cls('cep') }}" aria-invalid="{{ $hasErr('cep') ? 'true' : 'false' }}"
                            aria-describedby="cep_help">
                        @if ($hasErr('cep'))
                            <p id="cep_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('cep') }}
                            </p>
                        @else
                            <p id="cep_help" class="{{ $msgHelp }}">Formato: 00000-000</p>
                        @endif
                    </div>

                    {{-- Rua --}}
                    <div class="md:col-span-9">
                        <label for="rua" class="{{ $label }}">Rua</label>
                        <input id="rua" name="rua" type="text" maxlength="100" autocomplete="address-line1"
                            placeholder="Av. Paraná" value="{{ old('rua') }}" class="{{ $cls('rua') }}"
                            aria-invalid="{{ $hasErr('rua') ? 'true' : 'false' }}" aria-describedby="rua_help">
                        @if ($hasErr('rua'))
                            <p id="rua_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('rua') }}
                            </p>
                        @else
                            <p id="rua_help" class="{{ $msgHelp }}">Ex.: Av. Paraná</p>
                        @endif
                    </div>

                    {{-- Número --}}
                    <div class="md:col-span-3">
                        <label for="numero" class="{{ $label }}">Número</label>
                        <input id="numero" name="numero" type="text" inputmode="numeric" maxlength="8"
                            placeholder="1234" autocomplete="address-line2" value="{{ old('numero') }}"
                            class="{{ $cls('numero') }}" aria-invalid="{{ $hasErr('numero') ? 'true' : 'false' }}"
                            aria-describedby="numero_help">
                        @if ($hasErr('numero'))
                            <p id="numero_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('numero') }}
                            </p>
                        @else
                            <p id="numero_help" class="{{ $msgHelp }}">Ex.: 1234</p>
                        @endif
                    </div>

                    {{-- Bairro --}}
                    <div class="md:col-span-4">
                        <label for="bairro" class="{{ $label }}">Bairro</label>
                        <input id="bairro" name="bairro" type="text" maxlength="50" placeholder="Centro"
                            value="{{ old('bairro') }}" class="{{ $cls('bairro') }}"
                            aria-invalid="{{ $hasErr('bairro') ? 'true' : 'false' }}" aria-describedby="bairro_help">
                        @if ($hasErr('bairro'))
                            <p id="bairro_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('bairro') }}
                            </p>
                        @else
                            <p id="bairro_help" class="{{ $msgHelp }}">Ex.: Centro</p>
                        @endif
                    </div>

                    {{-- Complemento --}}
                    <div class="md:col-span-5">
                        <label for="complemento" class="{{ $label }}">Complemento</label>
                        <input id="complemento" name="complemento" type="text" maxlength="50"
                            placeholder="Ap., sala, bloco..." value="{{ old('complemento') }}"
                            class="{{ $cls('complemento') }}"
                            aria-invalid="{{ $hasErr('complemento') ? 'true' : 'false' }}"
                            aria-describedby="complemento_help">
                        @if ($hasErr('complemento'))
                            <p id="complemento_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('complemento') }}
                            </p>
                        @else
                            <p id="complemento_help" class="{{ $msgHelp }}">Opcional</p>
                        @endif
                    </div>

                    {{-- Cidade --}}
                    <div class="md:col-span-8">
                        <label for="cidade" class="{{ $label }}">Cidade</label>
                        <input id="cidade" name="cidade" type="text" maxlength="30"
                            autocomplete="address-level2" placeholder="Umuarama" value="{{ old('cidade') }}"
                            class="{{ $cls('cidade') }}" aria-invalid="{{ $hasErr('cidade') ? 'true' : 'false' }}"
                            aria-describedby="cidade_help">
                        @if ($hasErr('cidade'))
                            <p id="cidade_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('cidade') }}
                            </p>
                        @else
                            <p id="cidade_help" class="{{ $msgHelp }}">Ex.: Umuarama</p>
                        @endif
                    </div>

                    {{-- Estado (UF) - preenchimento manual (sem datalist) --}}
                    <div class="md:col-span-4">
                        <label for="estado" class="{{ $label }}">Estado</label>
                        <input id="estado" name="estado" type="text" maxlength="2" placeholder="PR"
                            class="{{ $cls('estado') }} uppercase" value="{{ old('estado') }}"
                            oninput="this.value = this.value.toUpperCase()"
                            aria-invalid="{{ $hasErr('estado') ? 'true' : 'false' }}" aria-describedby="estado_help">
                        @if ($hasErr('estado'))
                            <p id="estado_help" class="{{ $msgErr }}">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                                </svg>
                                {{ $errors->first('estado') }}
                            </p>
                        @else
                            <p id="estado_help" class="{{ $msgHelp }}">Digite a UF (ex.: PR)</p>
                        @endif
                    </div>
                </div>
            </fieldset>

            {{-- Site --}}
            <div>
                <label for="site" class="{{ $label }}">Site</label>
                <input id="site" name="site" type="url" maxlength="40" placeholder="https://exemplo.com.br"
                    value="{{ old('site') }}" class="{{ $cls('site') }}"
                    aria-invalid="{{ $hasErr('site') ? 'true' : 'false' }}" aria-describedby="site_help">
                @if ($hasErr('site'))
                    <p id="site_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('site') }}
                    </p>
                @else
                    <p id="site_help" class="{{ $msgHelp }}">Opcional</p>
                @endif
            </div>

            {{-- E-mail --}}
            <div>
                <label for="email" class="{{ $label }}">E-mail</label>
                <input id="email" name="email" type="email" maxlength="60" autocomplete="email"
                    placeholder="contato@exemplo.com.br" value="{{ old('email') }}" class="{{ $cls('email') }}"
                    aria-invalid="{{ $hasErr('email') ? 'true' : 'false' }}" aria-describedby="email_help">
                @if ($hasErr('email'))
                    <p id="email_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('email') }}
                    </p>
                @else
                    <p id="email_help" class="{{ $msgHelp }}">Usado para contato oficial</p>
                @endif
            </div>

            {{-- Telefones --}}
            <div>
                <label for="telefones" class="{{ $label }}">Telefones</label>
                <input id="telefones" name="telefones" type="text" placeholder="(44) 9 9999-9999, (44) 3333-3333"
                    value="{{ old('telefones') }}" class="{{ $cls('telefones') }}"
                    aria-invalid="{{ $hasErr('telefones') ? 'true' : 'false' }}" aria-describedby="telefones_help">
                @if ($hasErr('telefones'))
                    <p id="telefones_help" class="{{ $msgErr }}">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm-.75-5.5h1.5v1.5h-1.5V12.5zm0-6h1.5V11h-1.5V6.5z" />
                        </svg>
                        {{ $errors->first('telefones') }}
                    </p>
                @else
                    <p id="telefones_help" class="{{ $msgHelp }}">Separe múltiplos números por vírgula</p>
                @endif
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ url()->previous() }}"
                    class="px-4 py-2 rounded-lg border border-green-700 hover:bg-green-800/40 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection
