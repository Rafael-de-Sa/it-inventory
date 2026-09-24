{{--
    Campos compartilhados entre o cadastro e a edição de funcionário.
    Variáveis: $funcionario (null no cadastro), $opcoesEmpresas, $empresaSelecionadaId, $opcoesSetores.
    O setor é recarregado via JS (funcionario-form.js) quando a empresa muda.
--}}
@php
    $funcionario ??= null;
    $empresaSelecionadaId ??= null;
    $opcoesSetores ??= collect();
@endphp

<x-form.select name="empresa_id" label="Empresa" required placeholder="Selecione..." :options="$opcoesEmpresas"
    :value="$empresaSelecionadaId" data-old="{{ old('empresa_id', $empresaSelecionadaId) }}"
    data-url-base="{{ url('empresas') }}" />

<x-form.select name="setor_id" label="Setor" required :options="$opcoesSetores" :value="$funcionario?->setor_id"
    :placeholder="$opcoesSetores->isEmpty() ? 'Selecione uma empresa primeiro...' : null"
    data-old="{{ old('setor_id', $funcionario?->setor_id) }}" />

<x-form.grid>
    <x-form.input name="nome" label="Nome" required maxlength="30" :value="$funcionario?->nome"
        wrapper-class="md:col-span-6" />
    <x-form.input name="sobrenome" label="Sobrenome" required maxlength="50" :value="$funcionario?->sobrenome"
        wrapper-class="md:col-span-6" />

    <x-form.input name="cpf" label="CPF" required mask="cpf" maxlength="14" placeholder="000.000.000-00"
        :value="$funcionario?->cpf" wrapper-class="md:col-span-6" />
    {{-- "**": obrigatória só para funcionários próprios; o JS desabilita quando "Terceirizado" é marcado. --}}
    <x-form.input name="matricula" label="Matrícula**" maxlength="8" inputmode="numeric" pattern="\d*"
        autocomplete="off" placeholder="Somente números" :value="$funcionario?->matricula"
        help="**Obrigatório quando não é terceirizado" class="disabled:cursor-not-allowed disabled:bg-gray-300"
        wrapper-class="md:col-span-6" />

    <x-form.input name="telefone" label="Telefone" mask="telefone" placeholder="(44) 99999-0000"
        :value="$funcionario?->telefone" wrapper-class="md:col-span-6" />
    <x-form.input type="date" name="admitido_em" label="Data de admissão" required
        :value="$funcionario?->admitido_em?->toDateString()" :max="today()->toDateString()"
        :help="$funcionario ? 'Não pode ser futura nem posterior ao desligamento.' : 'Não pode ser uma data futura.'"
        wrapper-class="md:col-span-6" />
</x-form.grid>

<x-form.checkbox name="terceirizado" label="Terceirizado" :checked="$funcionario?->terceirizado" />
