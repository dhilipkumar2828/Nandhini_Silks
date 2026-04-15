<style>
    .custom-pagination {
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        width: 100%;
    }

    .pagination-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-results {
        flex-shrink: 0;
    }

    .results-text {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
        font-family: system-ui, -apple-system, sans-serif;
    }

    .results-text span {
        font-weight: 600;
        color: #940437;
    }

    .pagination-links {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .pagination-numbers {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .pagination-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0 0.625rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        font-family: system-ui, -apple-system, sans-serif;
        line-height: 1;
        gap: 0.5rem;
    }

    .pagination-item i {
        font-size: 0.75rem;
    }

    .pagination-item:not(.disabled):not(.active):hover {
        background-color: #fef2f4;
        border-color: #940437;
        color: #940437;
        transform: translateY(-1px);
    }

    .pagination-item.active {
        background-color: #940437;
        border-color: #940437;
        color: white;
        cursor: default;
        box-shadow: 0 2px 4px rgba(148, 4, 55, 0.2);
    }

    .pagination-item.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background-color: #f9fafb;
    }

    .pagination-item.dots {
        border: none;
        background: transparent;
        cursor: default;
        min-width: auto;
        padding: 0 0.25rem;
    }

    .pagination-item.dots:hover {
        background: transparent;
        transform: none;
        color: #374151;
    }

    /* Responsive Design - 1800px to 320px */
    @media (min-width: 1280px) {
        .pagination-item {
            min-width: 2.5rem;
            height: 2.5rem;
            font-size: 0.9375rem;
        }
    }

    @media (min-width: 1024px) and (max-width: 1279px) {
        .pagination-item {
            min-width: 2.25rem;
            height: 2.25rem;
            font-size: 0.875rem;
        }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        .pagination-container {
            gap: 0.875rem;
        }
        
        .pagination-item {
            min-width: 2.125rem;
            height: 2.125rem;
            font-size: 0.875rem;
            padding: 0 0.5rem;
        }
        
        .pagination-links {
            gap: 0.375rem;
        }
        
        .pagination-numbers {
            gap: 0.125rem;
        }
    }

    @media (min-width: 640px) and (max-width: 767px) {
        .pagination-container {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .pagination-results {
            text-align: center;
        }
        
        .pagination-links {
            justify-content: center;
            gap: 0.5rem;
        }
        
        .pagination-numbers {
            gap: 0.25rem;
        }
        
        .pagination-item {
            min-width: 2rem;
            height: 2rem;
            font-size: 0.8125rem;
            padding: 0 0.5rem;
        }
    }

    @media (min-width: 480px) and (max-width: 639px) {
        .pagination-container {
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .pagination-results {
            text-align: center;
        }
        
        .pagination-links {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.375rem;
        }
        
        .pagination-numbers {
            gap: 0.125rem;
        }
        
        .pagination-item {
            min-width: 1.875rem;
            height: 1.875rem;
            font-size: 0.75rem;
            padding: 0 0.375rem;
        }
        
        .results-text {
            font-size: 0.8125rem;
        }
    }

    @media (max-width: 479px) {
        .pagination-container {
            flex-direction: column;
            align-items: center;
            gap: 0.875rem;
        }
        
        .pagination-results {
            text-align: center;
            width: 100%;
        }
        
        .pagination-links {
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.375rem;
            width: 100%;
        }
        
        .pagination-numbers {
            gap: 0.125rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .pagination-item {
            min-width: 1.75rem;
            height: 1.75rem;
            font-size: 0.6875rem;
            padding: 0 0.3125rem;
        }
        
        .results-text {
            font-size: 0.75rem;
        }
        
        .pagination-item i {
            font-size: 0.625rem;
        }
    }

    /* Touch-friendly for mobile */
    @media (max-width: 768px) {
        .pagination-item {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        
        .pagination-item:active {
            transform: scale(0.95);
        }
    }
</style>

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination">
        <div class="pagination-container">
            {{-- Results info on left --}}
            <div class="pagination-results">
                <p class="results-text">
                    Showing <span>{{ $paginator->firstItem() }}</span> to <span>{{ $paginator->lastItem() }}</span> 
                    of <span>{{ $paginator->total() }}</span> Results
                </p>
            </div>

            {{-- Pagination links --}}
            <div class="pagination-links">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="pagination-item disabled">
                        <i class="fas fa-chevron-left"></i> Prev
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-item">
                        <i class="fas fa-chevron-left"></i> Prev
                    </a>
                @endif

                {{-- Smart Pagination with 2 digits on each side --}}
                <div class="pagination-numbers">
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();
                        $start = max(1, $current - 1);  // was -2, now -1 (1 left)
                        $end = min($last, $current + 1); // was +2, now +1 (1 right)
                        
                        // Show first page + dots
                        if ($start > 1) {
                            echo '<a href="' . $paginator->url(1) . '" class="pagination-item">1</a>';
                            if ($start > 2) {
                                echo '<span class="pagination-item dots">...</span>';
                            }
                        }
                        
                        // Show window: 1 before, current, 1 after
                        for ($i = $start; $i <= $end; $i++) {
                            if ($i == $current) {
                                echo '<span class="pagination-item active">' . $i . '</span>';
                            } else {
                                echo '<a href="' . $paginator->url($i) . '" class="pagination-item">' . $i . '</a>';
                            }
                        }
                        
                        // Show dots + last page
                        if ($end < $last) {
                            if ($end < $last - 1) {
                                echo '<span class="pagination-item dots">...</span>';
                            }
                            echo '<a href="' . $paginator->url($last) . '" class="pagination-item">' . $last . '</a>';
                        }
                    @endphp
                </div>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-item">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="pagination-item disabled">
                        Next <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif