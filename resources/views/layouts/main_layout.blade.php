<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Inventory')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/c89f8cd936.js" crossorigin="anonymous"></script>
</head>

<body class="bg-green-950 text-white font-inter min-h-screen flex flex-col">

    @include('layouts.top_bar')

    <main class="flex-1 p-6 w-full">
        @if (session('success'))
            <div id="flash-success"
                class="mx-auto mt-4 w-full max-w-3xl rounded-lg border border-green-700 bg-green-900/40 px-4 py-3 text-green-100 flex items-start gap-2 mb-6">
                <svg class="h-5 w-5 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 106.293 10.707l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <strong class="font-semibold">Sucesso!</strong>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="ml-2 hover:opacity-75"
                    onclick="document.getElementById('flash-success')?.remove()">✕</button>
            </div>
            <script>
                setTimeout(() => document.getElementById('flash-success')?.remove(), 5000);
            </script>
        @endif

        @if (session('error'))
            <div id="flash-error"
                class="mx-auto mt-4 w-full max-w-3xl rounded-lg border border-red-600 bg-red-900/30 px-4 py-3 text-red-100 flex items-start gap-2 mb-6">
                <svg class="h-5 w-5 mt-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 5h2v7H9V5zm0 8h2v2H9v-2z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <strong class="font-semibold">Erro!</strong>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" class="ml-2 hover:opacity-75"
                    onclick="document.getElementById('flash-error')?.remove()">✕</button>
            </div>
            <script>
                setTimeout(() => document.getElementById('flash-error')?.remove(), 5000);
            </script>
        @endif

        @yield('content')
    </main>

    <footer class="bg-green-900 text-center text-sm p-4">
        &copy; {{ date('Y') }} IT Inventory. Todos os direitos reservados.
    </footer>

    @stack('scripts')
</body>

</html>
