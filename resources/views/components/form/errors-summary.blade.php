@if ($errors->any())
    <div {{ $attributes->class('rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800') }}>
        <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
    </div>
@endif
