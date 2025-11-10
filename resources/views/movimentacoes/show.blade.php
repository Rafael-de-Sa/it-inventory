@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <div class="w-full max-w-5xl bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-8">

            @php
                $setor = $movimentacao->setor;
                $empresa = optional($setor)->empresa;
                $funcionario = $movimentacao->funcionario;

                $statusMovimentacao = $movimentacao->status ?? '';

                $classesBadgeStatus = 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold border ';
                switch ($statusMovimentacao) {
                    case 'pendente':
                        $classesBadgeStatus .= 'bg-yellow-500/20 text-yellow-200 border-yellow-500/60';
                        break;
                    case 'concluida':
                        $classesBadgeStatus .= 'bg-blue-500/20 text-blue-200 border-blue-500/60';
                        break;
                    case 'encerrada':
                        $classesBadgeStatus .= 'bg-green-500/20 text-green-200 border-green-500/60';
                        break;
                    case 'cancelada':
                        $classesBadgeStatus .= 'bg-red-500/20 text-red-200 border-red-500/60';
                        break;
                    default:
                        $classesBadgeStatus .= 'bg-gray-500/20 text-gray-200 border-gray-500/60';
                        break;
                }
            @endphp

            {{-- Cabeçalho: ID + Data --}}
            <section class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-3">
                    <label class="block mb-1 text-sm font-medium text-green-100">ID da Movimentação</label>
                    <input type="text" value="{{ $movimentacao->id }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-5">
                    <label class="block mb-1 text-sm font-medium text-green-100">Data da Movimentação</label>
                    <input type="text" value="{{ $movimentacao->criado_em?->format('d/m/Y H:i') ?? '-' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div class="md:col-span-4">
                    <label class="block mb-1 text-sm font-medium text-green-100">Status</label>
                    <div class="h-[38px] flex items-center">
                        <span class="{{ $classesBadgeStatus }}">
                            {{ $statusMovimentacao !== '' ? ucfirst($statusMovimentacao) : '—' }}
                        </span>
                    </div>
                </div>
            </section>

            {{-- Funcionário / Setor / Empresa --}}
            <section class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Funcionário</label>
                    <input type="text"
                        value="@if ($funcionario) {{ trim(($funcionario->nome ?? '') . ' ' . ($funcionario->sobrenome ?? '')) }}@if ($funcionario->matricula) ({{ $funcionario->matricula }}) @endif
@else
- @endif"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Setor</label>
                    <input type="text" value="{{ $setor?->nome ?? '-' }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Empresa</label>
                    <input type="text" value="{{ $empresa?->rotulo_empresa ?? ($empresa?->razao_social ?? '-') }}"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>
                </div>

                @if ($movimentacao->observacao)
                    <div>
                        <label class="block mb-1 text-sm font-medium text-green-100">Observação</label>
                        <textarea rows="3"
                            class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300/40 text-green-50 text-sm resize-y focus:outline-none focus:ring-0 focus:border-green-700"
                            disabled readonly>{{ $movimentacao->observacao }}</textarea>
                    </div>
                @endif
            </section>

            {{-- Equipamentos --}}
            <section class="space-y-3">
                <h3 class="text-sm font-semibold text-green-100 tracking-wide uppercase text-center">
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

            {{-- Termo de Responsabilidade + Ações --}}
            <section class="space-y-4">
                <h3 class="text-sm font-semibold text-green-100 tracking-wide uppercase">
                    Termo de Responsabilidade
                </h3>

                <div class="rounded-2xl border border-green-800 bg-green-900/30 p-4 space-y-4">
                    {{-- Linha: Voltar + Gerar termo --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('movimentacoes.index') }}"
                                class="inline-flex items-center gap-2 rounded-lg border border-green-700 px-4 py-2 text-sm hover:bg-green-800/40">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Voltar</span>
                            </a>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Gerar termo (PDF em nova guia) --}}
                            <a href="{{ route('movimentacoes.termo-responsabilidade', $movimentacao->id) }}"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Gerar termo de responsabilidade</span>
                            </a>

                            {{-- Visualizar termo enviado, se existir --}}
                            @if (!empty($movimentacao->url_termo_responsabilidade))
                                <a href="{{ $movimentacao->url_termo_responsabilidade }}" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Visualizar termo enviado</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Upload do termo assinado --}}
                    <form method="POST"
                        action="{{ route('movimentacoes.upload-termo-responsabilidade', $movimentacao->id) }}"
                        enctype="multipart/form-data" class="space-y-3">
                        @csrf

                        <div>
                            <label for="arquivo_termo" class="block mb-1 text-sm font-medium text-green-100">
                                Upload do termo assinado (PDF)
                            </label>
                            <input type="file" id="arquivo_termo" name="arquivo_termo" accept="application/pdf"
                                class="block w-full text-sm text-green-100
                                       file:mr-3 file:rounded-md file:border-0
                                       file:bg-green-800 file:px-3 file:py-1.5
                                       file:text-xs file:font-semibold file:text-green-50
                                       hover:file:bg-green-700">
                            @error('arquivo_termo')
                                <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/40 px-4 py-2 text-sm hover:bg-green-700/40 cursor-pointer">
                                <i class="fa-solid fa-upload"></i>
                                <span>Upload termo de responsabilidade</span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </div>
    </div>
@endsection
