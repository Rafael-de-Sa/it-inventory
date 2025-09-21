@extends('layouts.main_layout')

@section('content')
    @php
        // Rótulos
        $label = 'block mb-1 text-sm font-medium text-green-100';

        // Inputs habilitados (branco)
        $input = "w-full rounded-lg border border-green-700 px-3 py-2
              bg-white text-gray-900 placeholder-gray-500
              focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400";

        // Inputs desabilitados (cinza claro) — força para ganhar de qualquer reset
        $inputDisabled = "w-full rounded-lg border px-3 py-2 appearance-none
                      !bg-gray-400 !text-black !border-gray-300 placeholder-gray-400
                      cursor-not-allowed focus:outline-none focus:ring-0 focus:border-gray-300";
    @endphp
    <div class="w-full flex justify-center bg-green-950 py-7">

        <form action="{{ route('loginSubmit') }}" method="post"
            class="w-3/5 bg-green-900 p-8 rounded-xl shadow-md text-white font-inter">

            @csrf
            <div class="space-y-8">

                <div class="pb-6 border-b border-green-600">
                    <h2 class="text-lg font-semibold">Login</h2>
                    <p class="mt-1 text-sm text-gray-300">
                        Realize login para ter acesso ao sistema.
                    </p>
                </div>

                <div class="pb-6 border-b border-green-600">

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-6">

                        <div class="sm:col-span-6">
                            <label for="email" class="{{ $label }}">Email</label>
                            <input id="email" type="email" name="email" autocomplete="email" required
                                value="{{ old('email') }}" class="{{ $input }}" />

                            @error('email')
                                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                    role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-span-full">
                            <label for="password" class="{{ $label }}">Senha</label>
                            <input id="password" type="password" name="password" autocomplete="current-password" required
                                class="{{ $input }}" />

                            @error('password')
                                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                    role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>

                <div class="mt-6 flex items-center justify-end gap-x-4">
                    <button type="submit"
                        class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500 transition duration-300 shadow-md">
                        Login
                    </button>
                </div>

            </div>
        </form>
    </div>
@endsection
