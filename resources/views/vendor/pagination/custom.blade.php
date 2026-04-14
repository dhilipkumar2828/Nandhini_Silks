@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        <div class="pagination-results">
            <p>
                Showing <span>{{ $paginator->firstItem() }}</span> to <span>{{ $paginator->lastItem() }}</span> of <span>{{ $paginator->total() }}</span> Results
            </p>
        </div>

        <div class="pagination-links">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="pagination-item disabled">
                    <i class="fas fa-chevron-left mr-2"></i> Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-item">
                    <i class="fas fa-chevron-left mr-2"></i> Prev
                </a>
            @endif

            <div class="hidden sm:flex items-center gap-1 mx-2">
                {{-- Manual small window logic --}}
                @foreach ($elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            {{-- Only show current page and ONE neighbor --}}
                            @if (abs($page - $paginator->currentPage()) <= 1)
                                @if ($page == $paginator->currentPage())
                                    <span class="pagination-item active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="pagination-item">{{ $page }}</a>
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-item">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </a>
            @else
                <span class="pagination-item disabled">
                    Next <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @endif
        </div>
    </nav>
@endif

