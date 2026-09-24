{{-- Com empréstimo em aberto o status fica travado em "Em uso"; sem empréstimo, "Em uso" não é oferecido. --}}
@if ($emprestimoEmAberto)
    <input type="hidden" name="status" value="em_uso">
    <x-form.select name="status" id="status_travado" label="Status" disabled
        :options="['em_uso' => \App\Models\Equipamento::STATUS['em_uso']]" value="em_uso"
        :help="'Em uso pela movimentação #' . $emprestimoEmAberto->movimentacao_id . '. Para alterar, registre a devolução.'"
        wrapper-class="md:col-span-6" />
@else
    <x-form.select name="status" label="Status" required
        :options="\Illuminate\Support\Arr::only(\App\Models\Equipamento::STATUS, \App\Models\Equipamento::STATUS_EDICAO)"
        :value="$equipamento->status" wrapper-class="md:col-span-6" />
@endif
