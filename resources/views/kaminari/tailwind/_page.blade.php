<a href="{{ $url }}" 
   @if($remote) data-remote="true" @endif
   @if($page === $paginator->currentPage() + 1) rel="next" 
   @elseif($page === $paginator->currentPage() - 1) rel="prev" @endif
   class="inline-flex items-center px-3 md:px-4 py-1.5 text-xs md:text-sm font-medium border border-gray-300 dark:border-gray-600 rounded-md {{ $page === $paginator->currentPage() ? 'bg-blue-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
    {{ $page }}
</a>