@if($paginator->hasPages())
<div class="compact-pager">
    <a class="pager-btn {{ $paginator->onFirstPage() ? 'disabled' : '' }}"
       href="{{ $paginator->onFirstPage() ? '#' : $paginator->previousPageUrl() }}">
        <i class="fas fa-chevron-left"></i>
    </a>
    <span class="pager-meta">{{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
    <a class="pager-btn {{ $paginator->hasMorePages() ? '' : 'disabled' }}"
       href="{{ $paginator->hasMorePages() ? $paginator->nextPageUrl() : '#' }}">
        <i class="fas fa-chevron-right"></i>
    </a>
</div>
@endif
