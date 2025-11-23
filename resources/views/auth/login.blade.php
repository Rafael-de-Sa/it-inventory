@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center">
        <form action="{{ route('login') }}" method="POST"
            class="w-full max-w-lg bg-green-900/40 border border-green-800 rounded-2xl shadow-lg p-6 md:p-8 space-y-6">
            @csrf

            <header class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-wide">Acessar o sistema</h2>
                <p class="text-xs text-green-200">Informe suas credenciais para continuar.</p>
            </header>

            @if ($errors->any())
                <div class="rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <strong>Ops!</strong> Verifique os campos destacados e tente novamente.
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="email" class="mb-1 block text-sm text-green-100">E-mail</label>
                    <input id="email" type="email" name="email" autocomplete="email" required
                        value="{{ old('email') }}" @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'email'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'email'),
                        ]) />
                    @error('email')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm text-green-100">Senha</label>
                    <input id="password" type="password" name="password" autocomplete="current-password" required
                        @class([
                            'w-full rounded-lg border px-3 py-2 bg-white text-gray-900 placeholder-gray-500 focus:outline-none',
                            'border-red-500 ring-1 ring-red-400 focus:ring-red-400 focus:border-red-400 placeholder-red-300' => $errors->has(
                                'password'),
                            'border-green-700 focus:ring-2 focus:ring-green-400 focus:border-green-400' => !$errors->has(
                                'password'),
                        ]) />
                    @error('password')
                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-600 transition font-medium flex items-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Entrar
                </button>
            </div>
        </form>
    </div>
@endsection
