@props(['paginator'])

@if($paginator->hasPages())
    <nav class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" aria-label="Navigasi halaman">
        <p class="text-xs font-semibold text-slate-600">
            Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari {{ $paginator->total() }} data
        </p>
        <div class="flex flex-wrap items-center gap-1">
            @if($paginator->onFirstPage())
                <span class="grid h-9 min-w-9 place-items-center rounded-[6px] border border-[#dce5f1] bg-slate-50 px-2 text-xs font-bold text-slate-400">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="grid h-9 min-w-9 place-items-center rounded-[6px] border border-[#dce5f1] bg-white px-2 text-xs font-bold text-slate-700 hover:bg-blue-50">&laquo;</a>
            @endif

            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page === $paginator->currentPage())
                    <span class="grid h-9 min-w-9 place-items-center rounded-[6px] bg-[#2385dd] px-3 text-xs font-bold text-white">{{ $page }}</span>
                @elseif($page <= 3 || $page > $paginator->lastPage() - 2 || abs($page - $paginator->currentPage()) <= 1)
                    <a href="{{ $url }}" class="grid h-9 min-w-9 place-items-center rounded-[6px] border border-[#dce5f1] bg-white px-3 text-xs font-bold text-slate-700 hover:bg-blue-50">{{ $page }}</a>
                @elseif($page === 4 || $page === $paginator->lastPage() - 2)
                    <span class="grid h-9 min-w-9 place-items-center px-1 text-xs font-bold text-slate-400">...</span>
                @endif
            @endforeach

            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="grid h-9 min-w-9 place-items-center rounded-[6px] border border-[#dce5f1] bg-white px-2 text-xs font-bold text-slate-700 hover:bg-blue-50">&raquo;</a>
            @else
                <span class="grid h-9 min-w-9 place-items-center rounded-[6px] border border-[#dce5f1] bg-slate-50 px-2 text-xs font-bold text-slate-400">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
