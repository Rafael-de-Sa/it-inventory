@extends('layouts.main_layout')

@section('content')
    <div class="w-full flex justify-center bg-green-950 py-7">

        <form action="{{ route('loginSubmit') }}" method="post"
            class="w-3/5 bg-green-900 p-8 rounded-xl shadow-md text-white font-inter" novalidate>

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
                            <label for="email" class="block text-sm font-medium text-white">Email</label>
                            <input id="email" type="email" name="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="mt-2 block w-full rounded-md bg-green-950 border border-green-600 px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-300" />

                            @error('email')
                                <div class="col-span-full bg-green-950 border-l-4 border-red-500 text-red-300 px-4 py-2 rounded-md mt-6 text-sm"
                                    role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!--TODO: Colocar required no formulário -->

                        <div class="col-span-full">
                            <label for="password" class="block text-sm font-medium text-white">Senha</label>
                            <input id="password" type="password" name="password" autocomplete="current-password" required
                                class="mt-2 block w-full rounded-md bg-green-950 border border-green-600 px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-300" />

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
