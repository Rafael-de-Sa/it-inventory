{{--
    Paginação padrão ($paginator->links()), com os tokens de cor do projeto para acompanhar o tema claro/escuro.
    Baseada em vendor/laravel/framework/src/Illuminate/Pagination/resources/views/tailwind.blade.php.
--}}
@php
    $botao = 'inline-flex items-center border border-line-strong bg-surface text-sm font-medium leading-5 transition-colors';
    $link = $botao . ' text-ink-muted hover:bg-surface-muted hover:text-ink';
    $desabilitado = $botao . ' cursor-not-allowed text-ink-subtle opacity-60';
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        <div class="flex items-center justify-between gap-2 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="{{ $desabilitado }} rounded-md px-4 py-2">{!! __('pagination.previous') !!}</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }} rounded-md px-4 py-2">{!! __('pagination.previous') !!}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }} rounded-md px-4 py-2">{!! __('pagination.next') !!}</a>
            @else
                <span class="{{ $desabilitado }} rounded-md px-4 py-2">{!! __('pagination.next') !!}</span>
            @endif
        </div>

        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between sm:gap-2">
            <p class="text-sm leading-5 text-ink-muted">
                {!! __('Showing') !!}
                @if ($paginator->firstItem())
                    <span class="font-medium text-ink">{{ $paginator->firstItem() }}</span>
                    {!! __('to') !!}
                    <span class="font-medium text-ink">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {!! __('of') !!}
                <span class="font-medium text-ink">{{ $paginator->total() }}</span>
                {!! __('results') !!}
            </p>

            <span class="inline-flex rounded-md shadow-xs rtl:flex-row-reverse">
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                        <span class="{{ $desabilitado }} rounded-l-md px-2 py-2" aria-hidden="true">
                            <i class="fa-solid fa-chevron-left h-5 w-5 text-center text-xs leading-5"></i>
                        </span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }} rounded-l-md px-2 py-2" aria-label="{{ __('pagination.previous') }}">
                        <i class="fa-solid fa-chevron-left h-5 w-5 text-center text-xs leading-5" aria-hidden="true"></i>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span aria-disabled="true">
                            <span class="{{ $botao }} -ml-px cursor-default px-4 py-2 text-ink-subtle">{{ $element }}</span>
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page">
                                    <span class="{{ $botao }} relative z-10 -ml-px cursor-default border-brand-700 bg-brand-700 px-4 py-2 text-white">{{ $page }}</span>
                                </span>
                            @else
                                <a href="{{ $url }}" class="{{ $link }} -ml-px px-4 py-2" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }} -ml-px rounded-r-md px-2 py-2" aria-label="{{ __('pagination.next') }}">
                        <i class="fa-solid fa-chevron-right h-5 w-5 text-center text-xs leading-5" aria-hidden="true"></i>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                        <span class="{{ $desabilitado }} -ml-px rounded-r-md px-2 py-2" aria-hidden="true">
                            <i class="fa-solid fa-chevron-right h-5 w-5 text-center text-xs leading-5"></i>
                        </span>
                    </span>
                @endif
            </span>
        </div>
    </nav>
@endif
