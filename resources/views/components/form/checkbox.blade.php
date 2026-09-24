{{--
    Checkbox com rótulo ao lado. Envia `value` quando marcado; nada quando desmarcado
    (o FormRequest trata ausência como false via $this->boolean()).
    <x-form.checkbox name="terceirizado" label="Terceirizado" :checked="$model->terceirizado" />
--}}
@props([
    'name',
    'label',
    'checked' => false,
    'value' => '1',
    'id' => null,
])

@php
    $id ??= $name;
    // Após erro de validação, old() existe para campos enviados; checkbox desmarcado não é enviado.
    $isChecked = session()->hasOldInput() ? (bool) old($name) : (bool) $checked;
@endphp

<div>
    <label for="{{ $id }}" class="inline-flex cursor-pointer items-center gap-2">
        <input id="{{ $id }}" name="{{ $name }}" type="checkbox" value="{{ $value }}" @checked($isChecked)
            {{ $attributes->class('h-5 w-5 rounded border border-green-700 disabled:cursor-not-allowed disabled:opacity-60') }}>
        <span class="text-sm">{{ $label }}</span>
    </label>

    @error($name)
        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
    @enderror
</div>
