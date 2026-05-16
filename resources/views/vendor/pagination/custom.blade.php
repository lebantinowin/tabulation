@if ($paginator->hasPages())
<nav style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border, #e2e8f0); color: #ccc; font-size: 0.85rem; cursor: not-allowed;">
            <i class="fas fa-chevron-left"></i>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border, #e2e8f0); color: var(--color-text, #333); font-size: 0.85rem; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--color-btn,#8B4513)';this.style.color='white';this.style.borderColor='var(--color-btn,#8B4513)'" onmouseout="this.style.background='';this.style.color='var(--color-text,#333)';this.style.borderColor='var(--color-border,#e2e8f0)'">
            <i class="fas fa-chevron-left"></i>
        </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; color: var(--color-muted, #888); font-size: 0.85rem;">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; background: var(--color-btn, #8B4513); color: white; font-size: 0.85rem; font-weight: 600; border: 1px solid var(--color-btn, #8B4513);">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border, #e2e8f0); color: var(--color-text, #333); font-size: 0.85rem; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--color-btn,#8B4513)';this.style.color='white';this.style.borderColor='var(--color-btn,#8B4513)'" onmouseout="this.style.background='';this.style.color='var(--color-text,#333)';this.style.borderColor='var(--color-border,#e2e8f0)'">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border, #e2e8f0); color: var(--color-text, #333); font-size: 0.85rem; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='var(--color-btn,#8B4513)';this.style.color='white';this.style.borderColor='var(--color-btn,#8B4513)'" onmouseout="this.style.background='';this.style.color='var(--color-text,#333)';this.style.borderColor='var(--color-border,#e2e8f0)'">
            <i class="fas fa-chevron-right"></i>
        </a>
    @else
        <span style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border-radius: 8px; border: 1px solid var(--color-border, #e2e8f0); color: #ccc; font-size: 0.85rem; cursor: not-allowed;">
            <i class="fas fa-chevron-right"></i>
        </span>
    @endif

</nav>
@endif
