{{--
    Barra superior. Os itens vêm de config/navegacao.php (fonte única para desktop e mobile).
    Comportamento dos dropdowns e do menu mobile: resources/js/layout/nav.js (data-dropdown*, #btn-mobile, #mobile-menu).
--}}
@php
    $menu = config('navegacao');
    $estaAtivo = fn (array $item) => isset($item['itens'])
        ? collect($item['itens'])->contains(fn ($subitem) => request()->routeIs($subitem['ativo']))
        : request()->routeIs($item['ativo']);
@endphp

<header class="bg-green-900/95 shadow-md backdrop-blur">
    <div class="w-full px-3">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/logo-teste.png') }}" alt="" class="h-10 w-10 rounded-full border border-green-600">
                <span class="text-lg font-semibold tracking-wider">IT Inventory</span>
            </a>

            @unless (Route::is('login'))
                {{-- Desktop --}}
                <nav class="hidden items-center gap-1 md:flex" aria-label="Menu principal">
                    @foreach ($menu as $item)
                        @isset($item['itens'])
                            <div class="relative" data-dropdown>
                                <button type="button" data-dropdown-button aria-haspopup="menu" aria-expanded="false"
                                    @class([
                                        'flex cursor-pointer items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition hover:text-green-300',
                                        'text-green-300' => $estaAtivo($item),
                                    ])>
                                    <i class="{{ $item['icone'] }}" aria-hidden="true"></i> {{ $item['rotulo'] }}
                                    <i class="fa-solid fa-chevron-down text-xs" aria-hidden="true"></i>
                                </button>

                                <div class="invisible absolute right-0 z-20 mt-2 w-56 rounded-lg border border-green-800 bg-green-900/95 opacity-0 shadow-xl transition-all pointer-events-none"
                                    data-dropdown-menu role="menu">
                                    <div class="py-2 text-sm">
                                        @foreach ($item['itens'] as $subitem)
                                            <a href="{{ route($subitem['rota']) }}" role="menuitem"
                                                @if (request()->routeIs($subitem['ativo'])) aria-current="page" @endif
                                                @class([
                                                    'block px-4 py-2 hover:bg-green-800/50',
                                                    'text-green-300' => request()->routeIs($subitem['ativo']),
                                                ])>
                                                <i class="{{ $subitem['icone'] }}" aria-hidden="true"></i> {{ $subitem['rotulo'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route($item['rota']) }}" @if ($estaAtivo($item)) aria-current="page" @endif
                                @class([
                                    'rounded-md px-3 py-2 text-sm font-medium transition',
                                    'bg-green-800/40 text-green-300' => $estaAtivo($item),
                                    'hover:text-green-300' => !$estaAtivo($item),
                                ])>
                                <i class="{{ $item['icone'] }}" aria-hidden="true"></i> {{ $item['rotulo'] }}
                            </a>
                        @endisset
                    @endforeach

                    <a href="{{ route('logout') }}" title="Encerrar sessão"
                        class="rounded-md px-3 py-2 text-sm font-medium transition hover:text-green-300">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Sair
                    </a>
                </nav>

                {{-- Mobile: botão do menu --}}
                <button type="button" id="btn-mobile" aria-controls="mobile-menu" aria-expanded="false"
                    class="inline-flex items-center justify-center rounded-md p-2 hover:bg-green-800/40 md:hidden">
                    <span class="sr-only">Abrir menu</span>
                    <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
                </button>
            @endunless
        </div>
    </div>

    @unless (Route::is('login'))
        {{-- Mobile: menu --}}
        <nav id="mobile-menu" class="hidden border-t border-green-800 md:hidden" aria-label="Menu principal">
            <div class="space-y-1 px-4 py-3">
                @foreach ($menu as $item)
                    @isset($item['itens'])
                        <details class="group" @if ($estaAtivo($item)) open @endif>
                            <summary
                                class="flex cursor-pointer items-center justify-between rounded-md px-3 py-2 text-base font-medium hover:text-green-300">
                                <span><i class="{{ $item['icone'] }} mr-2" aria-hidden="true"></i> {{ $item['rotulo'] }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition group-open:rotate-180" aria-hidden="true"></i>
                            </summary>
                            <div class="mt-1 space-y-1 pl-6">
                                @foreach ($item['itens'] as $subitem)
                                    <a href="{{ route($subitem['rota']) }}"
                                        @if (request()->routeIs($subitem['ativo'])) aria-current="page" @endif
                                        @class([
                                            'block rounded-md px-3 py-2 hover:bg-green-800/40',
                                            'text-green-300' => request()->routeIs($subitem['ativo']),
                                        ])>
                                        <i class="{{ $subitem['icone'] }} mr-2" aria-hidden="true"></i> {{ $subitem['rotulo'] }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ route($item['rota']) }}" @if ($estaAtivo($item)) aria-current="page" @endif
                            @class([
                                'block rounded-md px-3 py-2 text-base font-medium',
                                'bg-green-800/40 text-green-300' => $estaAtivo($item),
                                'hover:text-green-300' => !$estaAtivo($item),
                            ])>
                            <i class="{{ $item['icone'] }} mr-2" aria-hidden="true"></i> {{ $item['rotulo'] }}
                        </a>
                    @endisset
                @endforeach

                <a href="{{ route('logout') }}" class="mt-1 block rounded-md px-3 py-2 text-base font-medium hover:text-green-300">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2" aria-hidden="true"></i> Sair
                </a>
            </div>
        </nav>
    @endunless
</header>
