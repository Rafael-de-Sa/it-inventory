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
                    ? trim(
                            ($movimentacao->funcionario->nome ?? '') .
                                ' ' .
                                ($movimentacao->funcionario->sobrenome ?? ''),
                        ) .
                        ($movimentacao->funcionario->matricula
                            ? ' (' . $movimentacao->funcionario->matricula . ')'
                            : '') .
                        ($movimentacao->funcionario->terceirizado ? ' - Terceirizado' : '')
                    : '—';

                $descricaoSetor = $movimentacao->setor->nome ?? '—';
                $descricaoEmpresa = $movimentacao->setor->empresa->razao_social ?? '—';

                $classesStatus = match ($movimentacao->status) {
                    'pendente' => 'bg-yellow-900/60 text-yellow-100 border-yellow-700',
                    'finalizada', 'encerrada' => 'bg-green-900/60 text-green-100 border-green-700',
                    'cancelada' => 'bg-red-900/60 text-red-100 border-red-700',
                    default => 'bg-slate-900/60 text-slate-100 border-slate-700',
                };

                // tipo de termo conforme vínculo do funcionário
                $descricaoTipoTermo = $movimentacao->funcionario?->terceirizado
                    ? 'Termo de devolução – Terceirizado'
                    : 'Termo de devolução – Funcionário próprio';

                $classesTipoTermo = $movimentacao->funcionario?->terceirizado
                    ? 'bg-sky-900/60 text-sky-100 border-sky-700'
                    : 'bg-indigo-900/60 text-indigo-100 border-indigo-700';

                // mapa para mostrar o motivo de forma amigável
                $motivosDevolucaoLabels = [
                    'manutencao' => 'Manutenção',
                    'defeito' => 'Defeito',
                    'quebra' => 'Quebra',
                    'devolucao' => 'Devolução',
                ];
            @endphp

            {{-- Dados principais --}}
            <section class="grid gap-4 md:grid-cols-4">
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

                {{-- Status --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Status</label>
                    <div
                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium {{ $classesStatus }}">
                        {{ ucfirst($movimentacao->status) }}
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Tipo de termo</label>
                    <div
                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium bg-sky-900/60 text-sky-100 border-sky-700">
                        Devolução
                    </div>
                </div>

            </section>

            {{-- Relacionamentos principais --}}
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

                {{-- Observações da movimentação --}}
                <div>
                    <label class="block mb-1 text-sm font-medium text-green-100">Observações da movimentação</label>
                    <textarea rows="3"
                        class="w-full rounded-lg border border-green-700 px-3 py-2 bg-gray-300 text-black
                               cursor-default transition-none focus:outline-none focus:ring-0 focus:border-green-700"
                        disabled readonly>{{ $movimentacao->observacao ?? 'Sem observações registradas.' }}</textarea>
                </div>
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
                                <th class="px-3 py-2 text-left font-semibold">Patrimônio</th>
                                <th class="px-3 py-2 text-left font-semibold">Tipo</th>
                                <th class="px-3 py-2 text-left font-semibold">Descrição</th>
                                <th class="px-3 py-2 text-left font-semibold">Número de série</th>
                                <th class="px-3 py-2 text-left font-semibold">Motivo da devolução</th>
                                <th class="px-3 py-2 text-left font-semibold">Observação da devolução</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-green-800/60">
                            @forelse ($movimentacao->equipamentos as $equipamento)
                                @php
                                    /** @var \App\Models\Equipamento $equipamento */
                                    $pivot = $equipamento->pivot;
                                    $motivoBruto = $pivot->motivo_devolucao ?? null;
                                    $motivoFormatado = $motivoBruto
                                        ? $motivosDevolucaoLabels[$motivoBruto] ?? ucfirst($motivoBruto)
                                        : '—';
                                @endphp
                                <tr class="hover:bg-green-900/50">
                                    <td class="px-3 py-2">
                                        {{ $equipamento->patrimonio ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $equipamento->tipoEquipamento->nome ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $equipamento->descricao ?? ($equipamento->nome ?? '—') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $equipamento->numero_serie ?? '—' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $motivoFormatado }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $pivot->observacao ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-sm text-green-200">
                                        Nenhum equipamento vinculado a esta movimentação.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-semibold tracking-wide">Termo de devolução</h3>

                <div class="rounded-2xl border border-green-800 bg-green-900/40 p-6 space-y-6">

                    @if (empty($movimentacao->caminho_termo_devolucao))
                        {{-- CENÁRIO 1: ainda NÃO existe termo de devolução enviado --}}
                        <div class="grid gap-6 md:grid-cols-2 md:items-start">
                            {{-- Coluna esquerda: upload --}}
                            <div class="space-y-2">
                                <p class="text-sm font-medium text-green-100">Upload do termo assinado (PDF)</p>
                                <p class="text-xs text-green-200">
                                    Envie o termo de devolução assinado para manter o registro associado a esta
                                    movimentação.
                                </p>

                                <form id="form-upload-termo-devolucao" method="POST"
                                    action="{{ route('movimentacoes.upload-termo-devolucao', $movimentacao) }}"
                                    enctype="multipart/form-data" class="space-y-3">
                                    @csrf

                                    <input type="file" name="arquivo_termo" accept="application/pdf"
                                        class="block w-full text-sm text-green-50
                                               file:mr-3 file:rounded-lg file:border-0
                                               file:bg-green-700 file:px-4 file:py-2
                                               file:text-sm file:font-medium file:text-white
                                               hover:file:bg-green-600">

                                    @error('arquivo_termo')
                                        <p class="text-xs text-red-300">{{ $message }}</p>
                                    @enderror
                                </form>
                            </div>

                            {{-- Coluna direita: ações do termo --}}
                            <div class="flex flex-col items-stretch gap-3 md:items-end">
                                {{-- Gerar termo (PDF) --}}
                                <a href="{{ route('movimentacoes.termo-devolucao', $movimentacao) }}" target="_blank"
                                    class="inline-flex items-center gap-2 rounded-lg border border-green-700 bg-green-800/60
                                           px-4 py-2 text-sm font-medium text-green-50 hover:bg-green-700/50">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <span>Gerar termo de devolução</span>
                                </a>

                                {{-- Enviar termo (submit do form de cima) --}}
                                <button type="submit" form="form-upload-termo-devolucao"
                                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2
                                           text-sm font-medium text-white hover:bg-green-500">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span>Upload termo de devolução</span>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- CENÁRIO 2: já existe termo de devolução enviado --}}
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-green-100">
                                    Termo de devolução armazenado
                                </p>
                                <p class="text-xs text-green-200">
                                    Já existe um termo de devolução enviado para esta movimentação.
                                    Não é permitido enviar um novo arquivo.
                                </p>
                            </div>

                            <div class="flex flex-col items-stretch gap-2 md:flex-row md:items-center md:gap-3">
                                <a href="{{ route('movimentacoes.termo.devolucao.visualizar', $movimentacao) }}?v={{ $movimentacao->atualizado_em?->timestamp ?? now()->timestamp }}"
                                    target="_blank"
                                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-700
                                           bg-green-800/80 px-4 py-2 text-sm font-medium text-green-50 hover:bg-green-700/60">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Visualizar termo de devolução</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Rodapé do card: Voltar --}}
                    <div
                        class="flex flex-col gap-3 border-t border-green-800 pt-4 md:flex-row md:items-center md:justify-between">

                        <a href="{{ route('movimentacoes.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-green-700
                                   bg-green-900/50 px-4 py-2 text-sm font-medium text-green-50 hover:bg-green-800/60">
                            <i class="fa-solid fa-arrow-left-long"></i>
                            <span>Voltar</span>
                        </a>
                    </div>
                </div>
            </section>

        </div>
    </div>
@endsection
