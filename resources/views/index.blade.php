<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Inventory')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Vite --}}
</head>

<body class="bg-green-950 text-white font-inter min-h-screen flex flex-col">

    {{-- Header futurista --}}
    <header class="bg-green-900 shadow-md p-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="{{ asset('./assets/logo-teste.png') }}" alt="Logo"
                class="h-14 w-14 rounded-full border border-green-600 hover:cursor-pointer transition duration-300">
            <h1 class="text-xl font-semibold tracking-wider">IT Inventory</h1>
        </div>
        <nav class="space-x-4">
            <a href="#" class="hover:text-green-300 transition">Dashboard</a>
            <a href="#" class="hover:text-green-300 transition">Equipamentos</a>
            <a href="#" class="hover:text-green-300 transition">Sair</a>
        </nav>
    </header>

    {{-- Conteúdo principal --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

    {{-- Rodapé --}}
    <footer class="bg-green-900 text-center text-sm p-4">
        &copy; {{ date('Y') }} IT Inventory. Todos os direitos reservados.
    </footer>

</body>

</html>
