@if ($message->sender == auth()->user() || $message->sender == $selectedClient)
    <div class="flex {{ $message->sender == auth()->user() ? 'justify-end' : 'justify-start' }}"
        id="message-{{ $message->id }}">
        <div class="flex max-w-[85%] gap-2">
            @unless ($message->sender == auth()->user())
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                        style="width: 32px; height: 32px;">
                        {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                    </div>
                </div>
            @endunless

            <div class="flex-1 flex flex-col {{ $message->sender == auth()->user() ? 'items-end' : 'items-start' }}">
                @if ($message->documents->count() > 0)
                    <div class="grid gap-2 mb-1 w-full">
                        @foreach ($message->documents as $document)
                            <div
                                class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                                @if (str_starts_with($document->file->mime_type, 'image/'))
                                    <iconify-icon icon="heroicons:photo"
                                        class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                @else
                                    <iconify-icon icon="heroicons:document"
                                        class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]">
                                        {{ $document->file->file_name }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ formatBytes($document->file->size) }}
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('file.download', $document->file->id) }}"
                                        class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                        title="{{ __('chat.messages.message.download') }}">
                                        <iconify-icon icon="heroicons:arrow-down-tray"
                                            class="text-[1.1rem] p-0.5"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($message->description)
                    <div
                        class="px-3 py-2 rounded-lg {{ $message->sender == auth()->user() ? 'bg-primary-500 text-white rounded-br-none' : 'bg-neutral-100 dark:bg-neutral-700 text-neutral-800 dark:text-neutral-200 rounded-bl-none' }}">
                        <p class="mb-0 break-all overflow-wrap-anywhere text-sm">{{ $message->description }}</p>
                    </div>
                @endif

                <div class="text-xs text-neutral-500 dark:text-neutral-400 mt-1">
                    {{ $message->created_at->format('h:i A') }}
                </div>
            </div>

            @if ($message->sender == auth()->user())
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                        style="width: 32px; height: 32px;">
                        {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@else
    <div class="flex justify-end" id="message-{{ $message->id }}">
        <div class="flex max-w-[85%] gap-2">
            <div class="flex-1 flex flex-col items-end">
                @if ($message->documents->count() > 0)
                    <div class="grid gap-2 mb-1 w-full">
                        @foreach ($message->documents as $document)
                            <div
                                class="flex items-center gap-2 bg-white dark:bg-neutral-700 rounded-lg p-2 border border-neutral-200 dark:border-neutral-600">
                                @if (str_starts_with($document->file->mime_type, 'image/'))
                                    <iconify-icon icon="heroicons:photo"
                                        class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                @else
                                    <iconify-icon icon="heroicons:document"
                                        class="text-4xl text-primary-600 dark:text-primary-400"></iconify-icon>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="text-sm font-medium text-neutral-800 dark:text-neutral-200 truncate max-w-[45px]">
                                        {{ $document->file->file_name }}</div>
                                    <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ formatBytes($document->file->size) }}
                                    </div>
                                </div>
                                <div class="ml-2">
                                    <a href="{{ route('file.download', $document->file->id) }}"
                                        class="w-9 h-9 text-primary-600 dark:text-primary-400 flex items-center justify-center hover:text-primary-700 dark:hover:text-primary-300 transition-colors"
                                        title="{{ __('chat.messages.message.download') }}">
                                        <iconify-icon icon="heroicons:arrow-down-tray"
                                            class="text-[1.1rem] p-0.5"></iconify-icon>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($message->description)
                    <div class="px-3 py-2 rounded-lg bg-primary-500 text-white rounded-br-none">
                        <p class="mb-0 break-all overflow-wrap-anywhere text-sm">{{ $message->description }}</p>
                    </div>
                @endif

                <div class="flex items-center gap-1 mt-1">
                    <div class="text-xs text-neutral-500 dark:text-neutral-400">
                        {{ $message->created_at->format('h:i A') }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col items-center flex-shrink-0">
                <div class="avatar-lg object-fit-cover rounded-full bg-primary-500 text-white flex items-center justify-center"
                    style="width: 32px; height: 32px;">
                    {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                </div>
            </div>
        </div>
    </div>
@endif
