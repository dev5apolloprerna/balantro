<div class="{{ $paginationClass ?? '' }}">
    <nav class="isolate inline-flex flex-wrap justify-center sm:justify-end gap-2 md:gap-1 rounded-md" aria-label="Pagination">
        @unless ($paginator->onFirstPage())
            @include('pagination._first_page', ['url' => $paginator->url(1), 'remote' => $remote ?? false])
        @endunless

        @unless ($paginator->onFirstPage())
            @include('pagination._prev_page', ['url' => $paginator->previousPageUrl(), 'remote' => $remote ?? false])
        @endunless

        @foreach ($elements as $element)
            @if (is_string($element))
                @include('pagination._gap')
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        @include('pagination._page', ['page' => $page, 'url' => $url, 'remote' => $remote ?? false])
                    @else
                        @include('pagination._page', ['page' => $page, 'url' => $url, 'remote' => $remote ?? false])
                    @endif
                @endforeach
            @endif
        @endforeach

        @unless ($paginator->currentPage() == $paginator->lastPage())
            @include('pagination._next_page', ['url' => $paginator->nextPageUrl(), 'remote' => $remote ?? false])
        @endunless

        @unless ($paginator->currentPage() == $paginator->lastPage())
            @include('pagination._last_page', ['url' => $paginator->url($paginator->lastPage()), 'remote' => $remote ?? false])
        @endunless
    </nav>
</div>