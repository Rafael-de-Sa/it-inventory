@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form id="empresaEditForm" action="{{ route('empresas.update', $empresa->id) }}" method="POST"
            enctype="multipart/form-data"
            class="w-full max-w-3xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6"
            data-cep-endpoint="{{ route('empresas.cep', ['cep' => '00000000']) }}">
            @csrf
            @method('PUT')

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Empresa — {{ $empresa->razao_social }}</h2>
                <p class="text-xs text-green-200">
                    Criada em: {{ $empresa->criado_em?->format('d/m/Y H:i') }} ·
                    Atualizada em: {{ $empresa->atualizado_em?->format('d/m/Y H:i') }}
                </p>
            </header>

            {{-- Resumo de erros --}}
            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
                </div>
            @endif

            {{-- ID + Ativa --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                {{-- ID (somente leitura) --}}
                <div class="md:col-span-3">
                    <label for="empresa_id" class="block mb-1 text-sm font-medium text-green-100">ID</label>
                    <input id="empresa_id" type="text" value="{{ $empresa->id }}" @class([
                        'w-full rounded-lg border px-3 py-2 cursor-default',
                        'bg-gray-300 text-black border-green-700',
                    ]) disabled
                        readonly>
                </div>

                {{-- Ativa (checkbox real) --}}
                <div class="md:col-span-3">
                    <label for="ativo" class="block mb-1 text-sm font-medium text-green-100">Ativa</label>
                    <div class="h-[42px] flex items-center gap-3">
                        <input type="hidden" name="ativo" value="0">
                        <input id="ativo" name="ativo" type="checkbox" value="1" @checked(old('ativo', $empresa->ativo))
                            @class([
                                'h-5 w-5 rounded focus:ring-0 focus:outline-none',
                                'border-green-700 text-green-700',
                            ])>
                        @if (old('ativo', $empresa->ativo))
                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'border border-green-600/60 bg-green-600/15 text-green-100 ring-1 ring-inset ring-green-400/10',
                            ])>
                                <i class="fa-solid fa-check-circle text-[10px]"></i> Ativa
                            </span>
                        @else
                            <span @class([
                                'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'border border-gray-500/60 bg-gray-500/15 text-gray-200/80 ring-1 ring-inset ring-gray-400/10',
                            ])>
                                <i class="fa-solid fa-circle-xmark text-[10px]"></i> Inativa
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Nome Fantasia --}}
            <div>
                <label for="nome_fantasia" class="block mb-1 text-sm font-medium text-green-100">Nome Fantasia*</label>
                <input id="nome_fantasia" name="nome_fantasia" type="text" maxlength="100"
                    value="{{ old('nome_fantasia', $empresa->nome_fantasia) }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('nome_fantasia'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'nome_fantasia'),
                    ])>
                @if ($errors->has('nome_fantasia'))
                    <p class="mt-1 text-xs text-red-300">{{ $errors->first('nome_fantasia') }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Ex.: Empresa Exemplo</p>
                @endif
            </div>

            {{-- Razão Social --}}
            <div>
                <label for="razao_social" class="block mb-1 text-sm font-medium text-green-100">Razão Social*</label>
                <input id="razao_social" name="razao_social" type="text" maxlength="100"
                    value="{{ old('razao_social', $empresa->razao_social) }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('razao_social'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'razao_social'),
                    ])>
                @if ($errors->has('razao_social'))
                    <p class="mt-1 text-xs text-red-300">{{ $errors->first('razao_social') }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Ex.: Empresa Exemplo LTDA</p>
                @endif
            </div>

            {{-- CNPJ --}}
            <div>
                <label for="cnpj" class="block mb-1 text-sm font-medium text-green-100">CNPJ*</label>
                <input id="cnpj" name="cnpj" type="text" inputmode="numeric" maxlength="18"
                    value="{{ old('cnpj', $empresa->cnpj_masked ?? $empresa->cnpj) }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('cnpj'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'cnpj'),
                    ])>
                @if ($errors->has('cnpj'))
                    <p class="mt-1 text-xs text-red-300">{{ $errors->first('cnpj') }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Formato: 00.000.000/0000-00</p>
                @endif
            </div>

            {{-- Endereço --}}
            <fieldset class="rounded-xl border border-green-800 bg-green-900/60 p-4 md:p-5 space-y-4">
                <legend class="px-2 text-sm font-semibold tracking-wide text-green-200 uppercase">Endereço</legend>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    {{-- CEP --}}
                    <div class="md:col-span-3">
                        <label for="cep" class="block mb-1 text-sm font-medium text-green-100">CEP*</label>
                        <input id="cep" name="cep" type="text" inputmode="numeric" maxlength="9"
                            value="{{ old('cep', $empresa->cep_masked ?? $empresa->cep) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('cep'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'cep'),
                            ])>
                        @if ($errors->has('cep'))
                            <p id="cep_help" class="mt-1 text-xs text-red-300">{{ $errors->first('cep') }}</p>
                        @else
                            <p id="cep_help" class="mt-1 text-xs text-green-200">Formato: 00000-000</p>
                        @endif
                    </div>

                    {{-- Rua --}}
                    <div class="md:col-span-9">
                        <label for="rua" class="block mb-1 text-sm font-medium text-green-100">Logradouro*</label>
                        <input id="rua" name="rua" type="text" maxlength="100"
                            value="{{ old('rua', $empresa->rua) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('rua'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'rua'),
                            ])>
                        @if ($errors->has('rua'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('rua') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Ex.: Av. Paraná</p>
                        @endif
                    </div>

                    {{-- Número --}}
                    <div class="md:col-span-3">
                        <label for="numero" class="block mb-1 text-sm font-medium text-green-100">Número*</label>
                        <input id="numero" name="numero" type="text" inputmode="numeric" maxlength="8"
                            value="{{ old('numero', $empresa->numero) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('numero'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'numero'),
                            ])>
                        @if ($errors->has('numero'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('numero') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Ex.: 1234</p>
                        @endif
                    </div>

                    {{-- Bairro --}}
                    <div class="md:col-span-4">
                        <label for="bairro" class="block mb-1 text-sm font-medium text-green-100">Bairro*</label>
                        <input id="bairro" name="bairro" type="text" maxlength="50"
                            value="{{ old('bairro', $empresa->bairro) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('bairro'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'bairro'),
                            ])>
                        @if ($errors->has('bairro'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('bairro') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Ex.: Centro</p>
                        @endif
                    </div>

                    {{-- Complemento --}}
                    <div class="md:col-span-5">
                        <label for="complemento" class="block mb-1 text-sm font-medium text-green-100">Complemento</label>
                        <input id="complemento" name="complemento" type="text" maxlength="50"
                            value="{{ old('complemento', $empresa->complemento) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('complemento'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'complemento'),
                            ])>
                        @if ($errors->has('complemento'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('complemento') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Opcional</p>
                        @endif
                    </div>

                    {{-- Cidade --}}
                    <div class="md:col-span-8">
                        <label for="cidade" class="block mb-1 text-sm font-medium text-green-100">Cidade*</label>
                        <input id="cidade" name="cidade" type="text" maxlength="30"
                            value="{{ old('cidade', $empresa->cidade) }}" @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('cidade'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'cidade'),
                            ])>
                        @if ($errors->has('cidade'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('cidade') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Ex.: Umuarama</p>
                        @endif
                    </div>

                    {{-- Estado (UF) --}}
                    <div class="md:col-span-4">
                        <label for="estado" class="block mb-1 text-sm font-medium text-green-100">Estado*</label>
                        <input id="estado" name="estado" type="text" maxlength="2"
                            value="{{ old('estado', $empresa->estado) }}" oninput="this.value = this.value.toUpperCase()"
                            @class([
                                'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 uppercase focus:outline-none',
                                'border-red-500 ring-1 ring-red-400' => $errors->has('estado'),
                                'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                    'estado'),
                            ])>
                        @if ($errors->has('estado'))
                            <p class="mt-1 text-xs text-red-300">{{ $errors->first('estado') }}</p>
                        @else
                            <p class="mt-1 text-xs text-green-200">Digite a UF (ex.: PR)</p>
                        @endif
                    </div>
                </div>
            </fieldset>

            {{-- E-mail --}}
            <div>
                <label for="email" class="block mb-1 text-sm font-medium text-green-100">E-mail*</label>
                <input id="email" name="email" type="email" maxlength="60"
                    value="{{ old('email', $empresa->email) }}" @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('email'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'email'),
                    ])>
                @if ($errors->has('email'))
                    <p class="mt-1 text-xs text-red-300">{{ $errors->first('email') }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Usado para contato oficial</p>
                @endif
            </div>

            {{-- Telefone --}}
            <div>
                <label for="telefone" class="block mb-1 text-sm font-medium text-green-100">Telefone</label>
                <input id="telefone" name="telefone" type="text"
                    value="{{ old('telefone', $empresa->telefone_masked ?? $empresa->telefone) }}"
                    @class([
                        'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 focus:outline-none',
                        'border-red-500 ring-1 ring-red-400' => $errors->has('telefone'),
                        'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                            'telefone'),
                    ])>
                @if ($errors->has('telefone'))
                    <p class="mt-1 text-xs text-red-300">{{ $errors->first('telefone') }}</p>
                @else
                    <p class="mt-1 text-xs text-green-200">Ex.: (44) 99999-9999 ou (44) 3333-3333</p>
                @endif
            </div>

            {{-- Ações --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('empresas.index') }}" @class([
                    'px-4 py-2 rounded-lg border inline-flex items-center gap-2',
                    'border-green-700 hover:bg-green-800/40',
                ]) title="Cancelar"
                    aria-label="Cancelar">
                    <i class="fa-solid fa-arrow-left"></i><span>Cancelar</span>
                </a>

                <button type="submit" @class([
                    'px-5 py-2 rounded-lg font-medium inline-flex items-center gap-2',
                    'bg-green-700 hover:bg-green-600 text-white',
                ])>
                    <i class="fa-solid fa-floppy-disk"></i><span>Salvar</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/empresas/empresa-form.js')
@endpush
