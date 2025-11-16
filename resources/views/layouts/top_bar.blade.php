{{-- resources/views/layouts/partials/header.blade.php --}}
<header class="bg-green-900/95 backdrop-blur shadow-md">
    <div class="w-full px-3"> {{-- << antes: mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 --}}
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo-teste.png') }}" alt="Logo"
                        class="h-10 w-10 rounded-full border border-green-600">
                    <span class="text-lg font-semibold tracking-wider">IT Inventory</span>
                </a>
            </div>

            @if (!Route::is('/login'))
                {{-- Desktop nav --}}
                <nav class="hidden md:flex items-center gap-1">
                    {{-- Dashboard --}}
                    <a href="{{ route('/') }}" @class([
                        'px-3 py-2 rounded-md text-sm font-medium transition',
                        'text-green-300 bg-green-800/40' => request()->routeIs('/'),
                        'hover:text-green-300' => !request()->routeIs('/'),
                    ])>
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>

                    {{-- Cadastros (dropdown) --}}
                    <div class="relative" data-dropdown>
                        <button type="button"
                            class="px-3 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 hover:text-green-300"
                            data-dropdown-button aria-haspopup="menu" aria-expanded="false">
                            <i class="fa-solid fa-folder-tree"></i> Cadastros
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>

                        <div class="invisible opacity-0 pointer-events-none absolute right-0 mt-2 w-56 rounded-lg border border-green-800 bg-green-900/95 shadow-xl transition-all"
                            data-dropdown-menu>
                            <div class="py-2 text-sm">
                                <a href="{{ route('empresas.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('empresas.*'),
                                ])>
                                    <i class="fa-regular fa-building"></i> Empresas
                                </a>
                                <a href="{{ route('setores.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('setores.*'),
                                ])>
                                    <i class="fa-solid fa-diagram-project"></i> Setores
                                </a>
                                <a href="{{ route('tipo-equipamentos.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('tipo-equipamentos.*'),
                                ])>
                                    <i class="fa-solid fa-sitemap"></i> Tipo de Equipamentos
                                </a>
                                <a href="{{ route('equipamentos.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('equipamentos.*'),
                                ])>
                                    <i class="fa-solid fa-computer"></i> Equipamentos
                                </a>
                                <a href="{{ route('funcionarios.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('funcionarios.*'),
                                ])>
                                    <i class="fa-solid fa-user-tie"></i> Funcionários
                                </a>
                                {{-- usuários --}}
                                <a href="{{ route('usuarios.index') }}" @class([
                                    'block px-4 py-2 hover:bg-green-800/50',
                                    'text-green-300' => request()->routeIs('usuarios.*'),
                                ])>
                                    <i class="fa-solid fa-users-gear"></i> Usuários
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Operações (dropdown) --}}
                    <div class="relative" data-dropdown>
                        <button type="button"
                            class="px-3 py-2 rounded-md text-sm font-medium transition flex items-center gap-2 hover:text-green-300"
                            data-dropdown-button aria-haspopup="menu" aria-expanded="false">
                            <i class="fa-solid fa-arrows-rotate"></i> Operações
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </button>

                        <div class="invisible opacity-0 pointer-events-none absolute right-0 mt-2 w-56 rounded-lg border border-green-800 bg-green-900/95 shadow-xl transition-all"
                            data-dropdown-menu>
                            <div class="py-2 text-sm">
                                @if (Route::has('movimentacoes.index'))
                                    <a href="{{ route('movimentacoes.index') }}" @class([
                                        'block px-4 py-2 hover:bg-green-800/50',
                                        'text-green-300' => request()->routeIs('movimentacoes.*'),
                                    ])>
                                        <i class="fa-solid fa-right-left"></i> Movimentações
                                    </a>
                                @else
                                    <span class="block px-4 py-2 text-green-200/60 cursor-not-allowed">
                                        <i class="fa-solid fa-right-left"></i> Movimentações
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Sair --}}
                    <a href="{{ route('/logout') }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition hover:text-green-300"
                        title="Encerrar sessão">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
                    </a>
                </nav>

                {{-- Mobile: botão hambúrguer --}}
                <button class="md:hidden inline-flex items-center justify-center rounded-md p-2 hover:bg-green-800/40"
                    type="button" aria-controls="mobile-menu" aria-expanded="false" id="btn-mobile">
                    <span class="sr-only">Abrir menu</span>
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
        </div>

        @endif
    </div>

    {{-- Mobile menu --}}
    <div class="md:hidden hidden border-t border-green-800" id="mobile-menu">
        <div class="space-y-1 px-4 py-3">
            <a href="{{ route('/') }}" @class([
                'block rounded-md px-3 py-2 text-base font-medium',
                'text-green-300 bg-green-800/40' => request()->routeIs('/'),
                'hover:text-green-300' => !request()->routeIs('/'),
            ])>
                <i class="fa-solid fa-gauge"></i> Dashboard
            </a>

            {{-- MOBILE MENU (substitua só o bloco <details> de Cadastros e Operações) --}}
            <details class="group">
                <summary
                    class="flex cursor-pointer items-center justify-between rounded-md px-3 py-2 text-base font-medium hover:text-green-300">
                    <span><i class="fa-solid fa-folder-tree mr-2"></i> Cadastros</span>
                    <i class="fa-solid fa-chevron-down text-xs transition group-open:rotate-180"></i>
                </summary>
                <div class="mt-1 space-y-1 pl-6">
                    <a href="{{ route('empresas.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('empresas.*')) text-green-300 @endif">
                        <i class="fa-regular fa-building mr-2"></i> Empresas
                    </a>
                    <a href="{{ route('setores.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('setores.*')) text-green-300 @endif">
                        <i class="fa-solid fa-diagram-project mr-2"></i> Setores
                    </a>
                    <a href="{{ route('tipo-equipamentos.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('tipo-equipamentos.*')) text-green-300 @endif">
                        <i class="fa-solid fa-sitemap mr-2"></i> Tipo de Equipamentos
                    </a>
                    <a href="{{ route('equipamentos.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('equipamentos.*')) text-green-300 @endif">
                        <i class="fa-solid fa-computer mr-2"></i> Equipamentos
                    </a>
                    <a href="{{ route('funcionarios.index') }}"
                        class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('funcionarios.*')) text-green-300 @endif">
                        <i class="fa-solid fa-user-tie mr-2"></i> Funcionários
                    </a>
                    @if (Route::has('usuarios.index'))
                        <a href="{{ route('usuarios.index') }}"
                            class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('usuarios.*')) text-green-300 @endif">
                            <i class="fa-solid fa-users-gear mr-2"></i> Usuários
                        </a>
                    @endif
                </div>
            </details>

            <details class="group">
                <summary
                    class="flex cursor-pointer items-center justify-between rounded-md px-3 py-2 text-base font-medium hover:text-green-300">
                    <span><i class="fa-solid fa-arrows-rotate mr-2"></i> Operações</span>
                    <i class="fa-solid fa-chevron-down text-xs transition group-open:rotate-180"></i>
                </summary>
                <div class="mt-1 space-y-1 pl-6">
                    @if (Route::has('movimentacoes.index'))
                        <a href="{{ route('movimentacoes.index') }}"
                            class="block rounded-md px-3 py-2 hover:bg-green-800/40 @if (request()->routeIs('movimentacoes.*')) text-green-300 @endif">
                            <i class="fa-solid fa-right-left mr-2"></i> Movimentações
                        </a>
                    @else
                        <span class="block rounded-md px-3 py-2 text-green-200/60">
                            <i class="fa-solid fa-right-left mr-2"></i> Movimentações
                        </span>
                    @endif
                </div>
            </details>


            <a href="{{ route('/logout') }}"
                class="mt-1 block rounded-md px-3 py-2 text-base font-medium hover:text-green-300">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Sair
            </a>
        </div>
    </div>
</header>

@vite(['resources/css/app.css', 'resources/js/app.js'])
