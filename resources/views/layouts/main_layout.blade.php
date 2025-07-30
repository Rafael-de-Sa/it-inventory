<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Inventory')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-green-950 text-white font-inter min-h-screen flex flex-col">

    @include('top_bar')

    <main class="flex-1 p-6 w-full">
        @yield('content')
    </main>

    <footer class="bg-green-900 text-center text-sm p-4">
        &copy; {{ date('Y') }} IT Inventory. Todos os direitos reservados.
    </footer>

</body>

</html>
