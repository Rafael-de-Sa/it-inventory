<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Inventory')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-teste-icon2.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/c89f8cd936.js" crossorigin="anonymous"></script>
</head>

<body class="bg-green-950 text-white font-inter min-h-screen flex flex-col">

    @include('layouts.top_bar')

    <main class="flex-1 p-6 w-full">
        @foreach (['success', 'error'] as $type)
            @if (session($type))
                <x-ui.flash :type="$type" :message="session($type)" />
            @endif
        @endforeach

        @yield('content')
    </main>

    <footer class="bg-green-900 text-center text-sm p-4">
        &copy; {{ date('Y') }} IT Inventory. Todos os direitos reservados.
    </footer>

    @stack('scripts')
</body>

</html>
