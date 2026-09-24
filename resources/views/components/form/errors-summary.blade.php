@if ($errors->any())
    <div {{ $attributes->class('rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200') }}>
        <strong>Ops!</strong> Encontramos {{ $errors->count() }} campo(s) para revisar.
    </div>
@endif
