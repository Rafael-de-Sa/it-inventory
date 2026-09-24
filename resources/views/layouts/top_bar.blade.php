{{--
    Barra superior. Os itens vêm de config/navegacao.php (fonte única para desktop e mobile).
    Comportamento dos dropdowns e do menu mobile: resources/js/layout/nav.js (data-dropdown*, #btn-mobile, #mobile-menu).
--}}
@php
    // Só os itens que o perfil do usuário pode acessar; submenus vazios somem.
    $podeVer = fn (array $item) => !isset($item['can']) || Gate::allows($item['can']);
    $menu = collect(config('navegacao'))
        ->map(fn (array $item) => isset($item['itens'])
            ? [...$item, 'itens' => array_values(array_filter($item['itens'], $podeVer))]
            : $item)
        ->filter(fn (array $item) => $podeVer($item) && (!isset($item['itens']) || $item['itens'] !== []))
        ->all();
    $estaAtivo = fn (array $item) => isset($item['itens'])
        ? collect($item['itens'])->contains(fn ($subitem) => request()->routeIs($subitem['ativo']))
        : request()->routeIs($item['ativo']);

    $linkTopo = 'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors';
    $linkTopoAtivo = 'bg-brand-50 text-brand-800';
    $linkTopoInativo = 'text-ink-muted hover:bg-surface-muted hover:text-ink';
@endphp

<header class="sticky top-0 z-30 border-b border-line bg-surface/90 backdrop-blur">
    <div class="w-full px-4 sm:px-6">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('assets/logo-teste.png') }}" alt="" class="h-9 w-9 rounded-full ring-1 ring-line">
                <span class="text-base font-semibold tracking-tight text-ink">IT Inventory</span>
            </a>

            @unless (Route::is('login'))
                {{-- Desktop --}}
                <nav class="hidden items-center gap-1 md:flex" aria-label="Menu principal">
                    @foreach ($menu as $item)
                        @isset($item['itens'])
                            <div class="relative" data-dropdown>
                                <button type="button" data-dropdown-button aria-haspopup="menu" aria-expanded="false"
                                    @class([$linkTopo, 'cursor-pointer', $estaAtivo($item) ? $linkTopoAtivo : $linkTopoInativo])>
                                    <i class="{{ $item['icone'] }} text-xs" aria-hidden="true"></i> {{ $item['rotulo'] }}
                                    <i class="fa-solid fa-chevron-down text-[10px] opacity-70" aria-hidden="true"></i>
                                </button>

                                <div class="invisible absolute right-0 z-20 mt-2 w-60 rounded-lg border border-line bg-surface p-1 opacity-0 shadow-lg transition-all pointer-events-none"
                                    data-dropdown-menu role="menu">
                                    @foreach ($item['itens'] as $subitem)
                                        @php $subitemAtivo = request()->routeIs($subitem['ativo']); @endphp
                                        <a href="{{ route($subitem['rota']) }}" role="menuitem"
                                            @if ($subitemAtivo) aria-current="page" @endif
                                            @class([
                                                'flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors',
                                                'bg-brand-50 font-medium text-brand-800' => $subitemAtivo,
                                                'text-ink hover:bg-surface-muted' => !$subitemAtivo,
                                            ])>
                                            <i class="{{ $subitem['icone'] }} w-4 text-center text-xs {{ $subitemAtivo ? '' : 'text-ink-subtle' }}" aria-hidden="true"></i>
                                            {{ $subitem['rotulo'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route($item['rota']) }}" @if ($estaAtivo($item)) aria-current="page" @endif
                                @class([$linkTopo, $estaAtivo($item) ? $linkTopoAtivo : $linkTopoInativo])>
                                <i class="{{ $item['icone'] }} text-xs" aria-hidden="true"></i> {{ $item['rotulo'] }}
                            </a>
                        @endisset
                    @endforeach

                    <span class="mx-2 h-6 w-px bg-line" aria-hidden="true"></span>

                    <a href="{{ route('logout') }}" title="Encerrar sessão" @class([$linkTopo, $linkTopoInativo])>
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs" aria-hidden="true"></i> Sair
                    </a>
                </nav>

                {{-- Mobile: botão do menu --}}
                <button type="button" id="btn-mobile" aria-controls="mobile-menu" aria-expanded="false"
                    class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-md text-ink-muted hover:bg-surface-muted hover:text-ink md:hidden">
                    <span class="sr-only">Abrir menu</span>
                    <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
                </button>
            @endunless
        </div>
    </div>

    @unless (Route::is('login'))
        {{-- Mobile: menu --}}
        <nav id="mobile-menu" class="hidden border-t border-line bg-surface md:hidden" aria-label="Menu principal">
            <div class="space-y-1 px-4 py-3">
                @foreach ($menu as $item)
                    @isset($item['itens'])
                        <details class="group" @if ($estaAtivo($item)) open @endif>
                            <summary
                                class="flex cursor-pointer list-none items-center justify-between rounded-md px-3 py-2 text-sm font-medium text-ink hover:bg-surface-muted">
                                <span><i class="{{ $item['icone'] }} mr-2 w-4 text-center text-ink-subtle" aria-hidden="true"></i> {{ $item['rotulo'] }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-ink-subtle transition group-open:rotate-180" aria-hidden="true"></i>
                            </summary>
                            <div class="mt-1 space-y-1 pl-6">
                                @foreach ($item['itens'] as $subitem)
                                    @php $subitemAtivo = request()->routeIs($subitem['ativo']); @endphp
                                    <a href="{{ route($subitem['rota']) }}" @if ($subitemAtivo) aria-current="page" @endif
                                        @class([
                                            'block rounded-md px-3 py-2 text-sm',
                                            'bg-brand-50 font-medium text-brand-800' => $subitemAtivo,
                                            'text-ink-muted hover:bg-surface-muted hover:text-ink' => !$subitemAtivo,
                                        ])>
                                        <i class="{{ $subitem['icone'] }} mr-2 w-4 text-center" aria-hidden="true"></i> {{ $subitem['rotulo'] }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @else
                        <a href="{{ route($item['rota']) }}" @if ($estaAtivo($item)) aria-current="page" @endif
                            @class([
                                'block rounded-md px-3 py-2 text-sm font-medium',
                                'bg-brand-50 text-brand-800' => $estaAtivo($item),
                                'text-ink hover:bg-surface-muted' => !$estaAtivo($item),
                            ])>
                            <i class="{{ $item['icone'] }} mr-2 w-4 text-center" aria-hidden="true"></i> {{ $item['rotulo'] }}
                        </a>
                    @endisset
                @endforeach

                <a href="{{ route('logout') }}"
                    class="mt-1 block rounded-md border-t border-line px-3 py-2 pt-3 text-sm font-medium text-ink-muted hover:text-ink">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4 text-center" aria-hidden="true"></i> Sair
                </a>
            </div>
        </nav>
    @endunless
</header>
