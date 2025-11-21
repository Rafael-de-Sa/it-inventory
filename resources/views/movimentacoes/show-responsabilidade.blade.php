@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-5xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-8">

            {{-- Cabeçalho --}}
            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">
                    Movimentação — #{{ $movimentacao->id }}
                </h2>
                <p class="text-xs text-green-200">
                    Data da movimentação:
                    {{ $movimentacao->criado_em?->format('d/m/Y H:i') ?? '—' }}
                </p>
            </header>

            @php
                $descricaoFuncionario = $movimentacao->funcionario
                    ? $movimentacao->funcionario->nome .
                        ' ' .
                        $movimentacao->funcionario->sobrenome .
                        ' (' .
                        $movimentacao->funcionario->matricula .
                        ')'
                    : '—';

                $descricaoSetor = $movimentacao->setor->nome ?? '—';
                $descricaoEmpresa = $movimentacao->setor->empresa->razao_social ?? '—';

                $classesStatus = match ($movimentacao->status) {
                    'pendente' => 'bg-yellow-900/60 text-yellow-100 border-yellow-700',
                    'finalizada' => 'bg-green-900/60 text-green-100 border-green-700',
                    'cancelada' => 'bg-red-900/60 text-red-100 border-red-700',
                    default => 'bg-slate-900/60 text-slate-100 border-slate-700',
                };
            @endphp

            {{-- Dados principais --}}
            <section class="grid gap-4 md:grid-cols-3">
                {{-- ID --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">ID da Movimentação</label>
                    <input type="text" value="{{ $movimentacao->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Data --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Data da Movimentação</label>
                    <input type="text" value="{{ $movimentacao->criado_em?->format('d/m/Y H:i') ?? '—' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Status como “badge” --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Status</label>
                    <div
                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $classesStatus }}">
                        {{ ucfirst($movimentacao->status) }}
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                {{-- Funcionário --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Funcionário</label>
                    <input type="text" value="{{ $descricaoFuncionario }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Setor --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Setor</label>
                    <input type="text" value="{{ $descricaoSetor }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                {{-- Empresa --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Empresa</label>
                    <input type="text" value="{{ $descricaoEmpresa }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                      cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>
            </section>

            {{-- Observação --}}
            <section class="space-y-2">
                <label class="block mb-1 text-sm font-medium text-green-100">Observação</label>
                <textarea rows="3"
                    class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black text-sm
                           cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                    disabled readonly>{{ $movimentacao->observacao ?? 'Sem observações registradas.' }}</textarea>
            </section>

            {{-- Equipamentos --}}
            <section class="space-y-3">
                <h3 class="text-sm font-semibold text-green-100 tracking-wide text-center">
                    Equipamentos desta movimentação
                </h3>

                <div class="overflow-x-auto rounded-2xl border border-green-800 bg-green-900/20">
                    <table class="min-w-full text-sm table-auto">
                        <thead class="bg-green-900/60 text-green-100">
                            <tr>
                                <th class="px-4 py-2 text-center">ID</th>
                                <th class="px-4 py-2 text-center">Descrição</th>
                                <th class="px-4 py-2 text-center">Patrimônio</th>
                                <th class="px-4 py-2 text-center">Nº Série</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-green-950/10">
                            @forelse ($movimentacao->equipamentos as $equipamento)
                                @php
                                    $dadosPivot = $equipamento->pivot; // MovimentacaoEquipamento
                                    $estaDevolvido = filled($dadosPivot->devolvido_em);
                                @endphp
                                <tr class="border-b border-green-800/30">
                                    <td class="px-4 py-2 text-center">{{ $equipamento->id }}</td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $equipamento->descricao ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $equipamento->patrimonio ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        {{ $equipamento->numero_serie ?? '-' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if ($estaDevolvido)
                                            <span
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-500/20 text-green-200 border border-green-500/60">
                                                Devolvido
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/60">
                                                Em uso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if (!$estaDevolvido)
                                            {{-- Formulário para marcar devolução deste equipamento
                                                 Ajustar a rota quando criar o fluxo de devolução --}}
                                            <form method="POST" {{-- action="{{ route('movimentacoes.equipamentos.devolver', [$movimentacao->id, $equipamento->id]) }}" --}} class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-3 py-1.5 text-xs hover:bg-green-700/40 cursor-pointer">
                                                    <i class="fa-solid fa-rotate-left text-xs"></i>
                                                    <span>Devolver</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-green-200">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-sm text-green-100/80">
                                        Nenhum equipamento vinculado a esta movimentação.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>


            {{-- TERMO DE RESPONSABILIDADE --}}
            <section class="space-y-4">
                <h3 class="text-lg font-semibold tracking-wide">Termo de responsabilidade</h3>

                <div class="rounded-2xl border border-green-800 bg-green-900/40 p-6 space-y-6">

                    <div class="grid gap-6 md:grid-cols-2 md:items-start">
                        {{-- Coluna esquerda: upload --}}
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-green-100">Upload do termo assinado (PDF)</p>
                            <p class="text-xs text-green-200">
                                Envie o termo assinado para concluir a movimentação.
                            </p>

                            <form id="form-upload-termo" method="POST" action="{{-- route('movimentacoes.termo.upload', $movimentacao) --}}"
                                enctype="multipart/form-data" class="space-y-3">
                                @csrf

                                <input type="file" name="arquivo_termo" accept="application/pdf"
                                    class="block w-full text-sm text-green-50
                                           file:mr-3 file:rounded-lg file:border-0
                                           file:bg-green-700 file:px-4 file:py-2
                                           file:text-sm file:font-medium file:text-white
                                           hover:file:bg-green-600">
                            </form>
                        </div>

                        {{-- Coluna direita: ações do termo --}}
                        <div class="flex flex-col items-stretch gap-3 md:items-end">
                            {{-- Gerar termo --}}
                            <a href="{{ route('movimentacoes.termo-responsabilidade', $movimentacao) }}" target="_blank"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm font-medium text-green-50 hover:bg-green-700/50">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Gerar termo de responsabilidade</span>
                            </a>

                            {{-- Enviar termo (submit do form de cima) --}}
                            <button type="submit" form="form-upload-termo"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span>Upload termo de responsabilidade</span>
                            </button>
                        </div>
                    </div>

                    {{-- Rodapé do card: Voltar --}}
                    <div
                        class="flex flex-col gap-3 border-t border-green-800 pt-4 md:flex-row md:items-center md:justify-between">
                        {{-- Voltar --}}
                        <a href="{{ route('movimentacoes.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-700 bg-green-900/50 px-4 py-2 text-sm font-medium text-green-50 hover:bg-green-800/60">
                            <i class="fa-solid fa-arrow-left-long"></i>
                            <span>Voltar</span>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
