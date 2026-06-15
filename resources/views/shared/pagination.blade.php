@if($resources->total() > $resources->perPage())
  <div class="px-4 py-4 border-t border-neutral-200 dark:border-neutral-700 flex flex-col sm:flex-row sm:justify-end items-center gap-3">
    {{ $resources->links('vendor.pagination.tailwind') }}
  </div>
@endif