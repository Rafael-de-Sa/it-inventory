<header class="bg-green-900 shadow-md p-4 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <a href="{{ route('/') }}" class="flex items-center gap-2">
            <img src="{{ asset('assets/logo-teste.png') }}" alt="Logo"
                class="h-14 w-14 rounded-full border border-green-600 hover:cursor-pointer transition duration-300">
            <h1 class="text-xl font-semibold tracking-wider">IT Inventory</h1>
        </a>
    </div>
    <nav class="space-x-4">
        <a href="#" class="hover:text-green-300 transition">Dashboard</a>
        <a href="#" class="hover:text-green-300 transition">Equipamentos</a>
        <a href="#" class="hover:text-green-300 transition">Sair</a>
    </nav>
</header>
