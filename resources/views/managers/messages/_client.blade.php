<a href="{{ route('managers.messages.index', ['client_id' => $client->id, 'search' => request('search')]) }}" 
   class="block w-full px-4 py-3 hover:bg-neutral-50 dark:hover:bg-neutral-600 active:bg-neutral-100 dark:active:bg-neutral-700 transition-colors"
   data-turbo-frame="mobile_chat">
  <div class="flex items-center gap-3">
    <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center flex-shrink-0" style="width: 40px; height: 40px;">
      {{ strtoupper(substr($client->name, 0, 1)) }}
    </div>
    <div class="flex-1 min-w-0">
      <div class="flex items-center justify-between">
        <div class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate">{{ $client->name }}</div>
        <div class="text-xs text-neutral-500 dark:text-neutral-400 whitespace-nowrap ml-2">
          {{ $client->created_at->diffForHumans() }}
        </div>
      </div>
      <p class="text-xs text-neutral-500 dark:text-neutral-400 mt-0.5 truncate">
        {{ __('chat.client_list.click_to_chat') }}
      </p>
    </div>
    <div class="text-neutral-400 dark:text-neutral-500">
      <iconify-icon icon="heroicons:chevron-right" class="text-lg"></iconify-icon>
    </div>
  </div>
</a>