@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:border-[#a91b43] hover:text-[#a91b43] transition-all">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 text-slate-400 text-xs">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#a91b43] text-white text-xs font-bold shadow-md shadow-pink-200">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 text-xs font-bold hover:border-[#a91b43] hover:text-[#a91b43] transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:border-[#a91b43] hover:text-[#a91b43] transition-all">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </a>
            @else
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </span>
            @endif
        </div>

        <div class="hidden sm:block">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                {!! __('Showing') !!}
                <span class="font-black text-slate-600">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-black text-slate-600">{{ $paginator->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-black text-slate-600">{{ $paginator->total() }}</span>
                {!! __('Results') !!}
            </p>
        </div>
    </nav>
@endif
