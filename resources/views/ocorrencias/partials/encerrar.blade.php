{{--
    Modal "Encerrar ocorrência": liberação pela TI, solução e custos. Abre sozinho quando volta com erro de validação.
    Variáveis: $ocorrencia, $fornecedores (sugestões).
--}}
<x-ui.modal id="modal-encerrar" title="Encerrar ocorrência" :open="$errors->any()" class="max-w-lg">
    <form method="POST" action="{{ route('ocorrencias.encerrar', $ocorrencia) }}" class="space-y-4">
        @csrf

        <p class="text-sm text-ink-muted">
            @if ($ocorrencia->alterou_status)
                O equipamento volta para <strong class="text-ink">Disponível</strong> com a TI.
                @if ($ocorrencia->devolucao_movimentacao_id)
                    Depois, se quiser, ele pode voltar para o mesmo funcionário pela própria ocorrência.
                @endif
            @else
                O status do equipamento não muda.
            @endif
        </p>

        <x-form.input name="liberado_em" type="date" label="Liberado pela TI em" required
            :value="today()->format('Y-m-d')" :min="$ocorrencia->reportado_em->format('Y-m-d')" :max="today()->format('Y-m-d')" />

        <x-form.textarea name="solucao" label="Solução" required rows="3" :value="$ocorrencia->solucao"
            placeholder="Ex.: troca do display, limpeza do app, reinstalação…" />

        <x-form.input name="custo_manutencao" label="Custo da manutenção (R$)" inputmode="numeric" placeholder="0,00"
                data-mascara="moeda"
                :value="filled($ocorrencia->custo_manutencao) ? number_format((float) $ocorrencia->custo_manutencao, 2, ',', '.') : null" />
        <x-form.input name="valor_cobrado" label="Valor cobrado do colaborador (R$)" inputmode="numeric" placeholder="0,00"
                data-mascara="moeda"
                :value="filled($ocorrencia->valor_cobrado) ? number_format((float) $ocorrencia->valor_cobrado, 2, ',', '.') : null" />

        <x-form.input name="fornecedor" label="Fornecedor / assistência técnica" maxlength="80" list="fornecedores_anteriores"
            :value="$ocorrencia->fornecedor" />
        <datalist id="fornecedores_anteriores">
            @foreach ($fornecedores as $fornecedor)
                <option value="{{ $fornecedor }}"></option>
            @endforeach
        </datalist>

        <div class="flex justify-end gap-2 border-t border-line pt-4">
            <x-ui.button type="button" onclick="this.closest('dialog').close()">Cancelar</x-ui.button>
            <x-ui.button variant="primary" icon="fa-solid fa-circle-check">Encerrar</x-ui.button>
        </div>
    </form>
</x-ui.modal>
