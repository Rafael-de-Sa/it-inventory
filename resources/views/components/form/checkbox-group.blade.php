{{--
    Grupo de checkboxes para escolha múltipla. Envia um array (name[]).
    <x-form.checkbox-group name="computador[portas_video]" label="Portas de vídeo"
        :options="['HDMI' => 'HDMI', 'VGA' => 'VGA']" :value="$computador?->portas_video" />
--}}
@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => [],
    'help' => null,
    'required' => false,
    'wrapperClass' => null,
])

@php
    $errorKey = rtrim(str_replace(['][', '[', ']'], ['.', '.', ''], $name), '.');
    $id = str_replace('.', '_', $errorKey);
    $selecionados = array_map('strval', (array) (session()->hasOldInput() ? old($errorKey, []) : ($value ?? [])));
    $invalid = $errors->has($errorKey) || $errors->has($errorKey . '.*');
@endphp

<x-form.field :label="$label" :error-key="$errors->has($errorKey) ? $errorKey : $errorKey . '.*'" :help="$help"
    :required="$required" :class="$wrapperClass">
    <div id="{{ $id }}" role="group" @if ($label) aria-label="{{ $label }}" @endif
        @class([
            'flex min-h-10 flex-wrap items-center gap-x-5 gap-y-2 rounded-lg border bg-surface px-3 py-2',
            'border-line-strong' => !$invalid,
            'border-red-500' => $invalid,
        ])>
        @foreach ($options as $valorOpcao => $rotulo)
            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-ink">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $valorOpcao }}"
                    @checked(in_array((string) $valorOpcao, $selecionados, true))
                    {{ $attributes->class('h-4 w-4 cursor-pointer disabled:cursor-not-allowed') }}>
                {{ $rotulo }}
            </label>
        @endforeach
    </div>
</x-form.field>
