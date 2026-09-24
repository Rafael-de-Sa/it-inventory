<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IT Inventory')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-teste-icon2.png') }}">

    {{-- Aplica o tema antes da pintura, sem piscar o claro. Botão e escolha: resources/js/layout/tema.js --}}
    <script>
        (function () {
            var tema = 'sistema';
            try { tema = localStorage.getItem('tema') || 'sistema'; } catch (e) {}
            var escuro = tema === 'escuro' || (tema !== 'claro' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', escuro);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://kit.fontawesome.com/c89f8cd936.js" crossorigin="anonymous"></script>
</head>

<body class="flex min-h-screen flex-col">
    <a href="#conteudo"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-surface focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:shadow-lg">
        Pular para o conteúdo
    </a>

    @include('layouts.top_bar')

    <main id="conteudo" class="w-full flex-1 px-4 py-8 sm:px-6">
        @foreach (['success', 'error'] as $type)
            @if (session($type))
                <x-ui.flash :type="$type" :message="session($type)" />
            @endif
        @endforeach

        @yield('content')
    </main>

    <footer class="border-t border-line bg-surface px-4 py-4 text-center text-xs text-ink-muted">
        &copy; {{ date('Y') }} IT Inventory. Todos os direitos reservados.
    </footer>

    @stack('scripts')
</body>

</html>
