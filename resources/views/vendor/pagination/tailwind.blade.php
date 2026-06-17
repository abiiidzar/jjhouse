@if ($paginator->hasPages())
    <div class="flex justify-center mt-20">

        <div class="flex items-center gap-6 text-xs uppercase tracking-widest">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="opacity-30">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="hover:opacity-50 transition">
                    Prev
                </a>
            @endif


            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        @if ($page == $paginator->currentPage())
                            <span class="border-b border-black pb-1">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="hover:opacity-50">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach
                @endif
            @endforeach


            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="hover:opacity-50 transition">
                    Next
                </a>
            @else
                <span class="opacity-30">Next</span>
            @endif

        </div>

    </div>
@endif
