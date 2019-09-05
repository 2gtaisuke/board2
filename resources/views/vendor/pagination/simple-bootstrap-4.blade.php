@if ($paginator->hasPages())
    <ul class="pagination justify-content-center" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled mx-2" aria-disabled="true">
                <span class="page-link rounded-pill">@lang('pagination.previous')</span>
            </li>
        @else
            <li class="page-item mx-2">
                <a class="page-link rounded-pill" href="{{ $paginator->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>
            </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item mx-2">
                <a class="page-link rounded-pill" href="{{ $paginator->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>
            </li>
        @else
            <li class="page-item disabled mx-2" aria-disabled="true">
                <span class="page-link rounded-pill">@lang('pagination.next')</span>
            </li>
        @endif
    </ul>
@endif
