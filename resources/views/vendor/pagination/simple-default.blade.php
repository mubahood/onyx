@if ($paginator->hasPages())
    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="disabled"><i class="fas fa-chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" title="Previous"><i class="fas fa-chevron-left"></i></a>
    @endif

    {{-- Page windows --}}
    @php
        $current  = $paginator->currentPage();
        $last     = $paginator->lastPage();
        $window   = 2; // pages each side of current
        $pages    = collect(range(1, $last));
        $show     = $pages->filter(fn($p) =>
            $p === 1 || $p === $last ||
            abs($p - $current) <= $window
        );
    @endphp

    @php $prev = null; @endphp
    @foreach($show as $page)
        @if($prev !== null && $page - $prev > 1)
            <span class="disabled" style="border:none;padding:0 2px;color:var(--mt);">…</span>
        @endif
        @if($page === $current)
            <span class="active">{{ $page }}</span>
        @else
            <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
        @endif
        @php $prev = $page; @endphp
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" title="Next"><i class="fas fa-chevron-right"></i></a>
    @else
        <span class="disabled"><i class="fas fa-chevron-right"></i></span>
    @endif
@endif
